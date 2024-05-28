<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config/config.php'; // Incluye la configuración de la base de datos

include '../includes/header.php';
?>

<style>
@media print {
    .filters, .toolbar {
        display: none !important;
    }
}
</style>

<div class="container">
    <h2 class="mt-5">Listado de Producción</h2>

    <!-- Barra de herramientas -->
    <div class="toolbar mb-3">
        <button class="btn btn-secondary" onclick="downloadCSV()">Descargar CSV</button>
        <button class="btn btn-secondary" onclick="downloadPDF()">Descargar PDF</button>
        <button class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <!-- Filtros -->
    <div class="filters mb-3 row">
        <div class="form-group col-md-3">
            <label for="startDate">Fecha inicio:</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="form-group col-md-3">
            <label for="endDate">Fecha fin:</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div class="form-group col-md-3">
            <label for="loteFilter">Lote de ingrediente:</label>
            <input type="text" id="loteFilter" class="form-control" placeholder="Ingrese lote de ingrediente">
        </div>
        <div class="form-group col-md-3">
            <label for="orderBy">Ordenar por fecha:</label>
            <select id="orderBy" class="form-control">
                <option value="desc">Mayor a menor</option>
                <option value="asc">Menor a mayor</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <button class="btn btn-primary" onclick="applyFilters()">Aplicar filtros</button>
        </div>
    </div>

    <h3 class="mt-3">Productos Finales</h3>
    <?php
    // Obtener todos los productos finales producidos, ordenados por fecha de producción
    $stmt = $pdo->query("SELECT produccion.*, recetas.nombre_producto_final FROM produccion JOIN recetas ON produccion.receta_id = recetas.id ORDER BY fecha_produccion DESC");
    $productos_finales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener lotes de ingredientes usados agrupados por producción
    $stmt = $pdo->query("SELECT lotes_ingredientes_usados.*, ingredientes_recetas.nombre_ingrediente 
                         FROM lotes_ingredientes_usados 
                         JOIN ingredientes_recetas ON lotes_ingredientes_usados.ingrediente_id = ingredientes_recetas.id");
    $lotes_ingredientes = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

    // Crear un mapa de producción a ingredientes
    $produccion_ingredientes = [];
    foreach ($lotes_ingredientes as $lote) {
        $produccion_id = $lote[0]['produccion_id'];
        if (!isset($produccion_ingredientes[$produccion_id])) {
            $produccion_ingredientes[$produccion_id] = [];
        }
        $produccion_ingredientes[$produccion_id][] = $lote[0];
    }

    // Debugging information
    echo "<script>console.log('produccion_ingredientes:', " . json_encode($produccion_ingredientes) . ");</script>";

    $fecha_actual = null;
    ?>
    <table id="produccionTable" class="table table-bordered">
        <thead>
            <tr>
                <th class="bg-primary text-white">Fecha</th>
                <th class="bg-primary text-white">Cantidad</th>
                <th class="bg-primary text-white">Producto</th>
                <th class="bg-primary text-white">Lote</th>
                <th class="bg-primary text-white">Ingredientes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos_finales as $producto): ?>
                <?php if ($producto['fecha_produccion'] !== $fecha_actual): ?>
                    <?php $fecha_actual = $producto['fecha_produccion']; ?>
                    <tr>
                        <td colspan="5" class="text-center bg-success text-white"><?= $fecha_actual ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="align-middle"><?= $producto['fecha_produccion'] ?></td>
                    <td class="align-middle"><?= $producto['cantidad'] ?></td>
                    <td class="align-middle"><strong><?= $producto['nombre_producto_final'] ?></strong></td>
                    <td class="align-middle"><?= $producto['lote_produccion'] ?></td>
                    <td class="align-middle">
                        <button class="btn btn-info" onclick="toggleIngredients(<?= $producto['id'] ?>)">Ver Ingredientes</button>
                    </td>
                </tr>
                <tr id="ingredientes-<?= $producto['id'] ?>" style="display: none;">
                    <td colspan="5">
                        <ul class="list-group">
                            <?php if (isset($produccion_ingredientes[$producto['id']])): ?>
                                <?php foreach ($produccion_ingredientes[$producto['id']] as $ingrediente): ?>
                                    <li class="list-group-item">
                                        <strong><?= $ingrediente['nombre_ingrediente'] ?>:</strong> <?= $ingrediente['lote'] ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No hay ingredientes registrados</li>
                            <?php endif; ?>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
const produccion_ingredientes = <?= json_encode($produccion_ingredientes) ?>;
console.log('produccion_ingredientes:', produccion_ingredientes);

function toggleIngredients(productId) {
    console.log("toggleIngredients called with productId:", productId);
    const ingredientesRow = document.getElementById('ingredientes-' + productId);
    if (ingredientesRow.style.display === 'none') {
        console.log("Showing ingredients for productId:", productId);
        console.log("ingredientesRow:", ingredientesRow);
        console.log("produccion_ingredientes:", produccion_ingredientes);
        const ingredientes = produccion_ingredientes[productId];
        console.log("ingredientes for productId:", ingredientes);
        if (ingredientes && ingredientes.length > 0) {
            let list = '';
            ingredientes.forEach(ingrediente => {
                list += `<li class="list-group-item"><strong>${ingrediente.nombre_ingrediente}:</strong> ${ingrediente.lote}</li>`;
            });
            ingredientesRow.querySelector('.list-group').innerHTML = list;
        } else {
            console.log("No ingredientes found for productId:", productId);
            ingredientesRow.querySelector('.list-group').innerHTML = '<li class="list-group-item">No hay ingredientes registrados</li>';
        }
        ingredientesRow.style.display = '';
    } else {
        console.log("Hiding ingredients for productId:", productId);
        ingredientesRow.style.display = 'none';
    }
}

function downloadCSV() {
    let csv = 'Fecha,Cantidad,Producto,Lote,Ingredientes\n';
    const rows = document.querySelectorAll('#produccionTable tbody tr');
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 5) {
            const date = columns[0].innerText;
            const quantity = columns[1].innerText;
            const product = columns[2].innerText;
            const lote = columns[3].innerText;
            let ingredientes = '';
            const ingredientesRow = row.nextElementSibling;
            if (ingredientesRow && ingredientesRow.querySelector('ul')) {
                const listItems = ingredientesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    ingredientes += item.innerText;
                    if (index < listItems.length - 1) ingredientes += ' | ';
                });
            }
            csv += `"${date}","${quantity}","${product}","${lote}","${ingredientes}"\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'produccion.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    html2canvas(document.querySelector("#produccionTable")).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        doc.save('produccion.pdf');
    });
}

function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const loteFilter = document.getElementById('loteFilter').value.toLowerCase();
    const orderBy = document.getElementById('orderBy').value;

    const rows = document.querySelectorAll('#produccionTable tbody tr');
    let products = [];
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 5) {
            const date = columns[0].innerText;
            const quantity = columns[1].innerText;
            const product = columns[2].innerText;
            const lote = columns[3].innerText;
            let ingredientes = '';
            const ingredientesRow = row.nextElementSibling;
            if (ingredientesRow && ingredientesRow.querySelector('ul')) {
                const listItems = ingredientesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    ingredientes += item.innerText.toLowerCase();
                    if (index < listItems.length - 1) ingredientes += ' | ';
                });
            }
            products.push({ row, date, quantity, product, lote, ingredientes, ingredientesRow });
        }
    });

    products = products.filter(p => {
        let dateCondition = true;
        let loteCondition = true;

        if (startDate && endDate) {
            dateCondition = new Date(p.date) >= new Date(startDate) && new Date(p.date) <= new Date(endDate);
        }
        if (loteFilter) {
            loteCondition = p.ingredientes.includes(loteFilter);
        }

        return dateCondition && loteCondition;
    });

    if (orderBy === 'asc') {
        products.sort((a, b) => new Date(a.date) - new Date(b.date));
    } else {
        products.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    rows.forEach(row => {
        row.style.display = 'none';
    });

    products.forEach(p => {
        p.row.style.display = '';
        p.ingredientesRow.style.display = 'none';
    });
}
</script>

<?php include '../includes/footer.php'; ?>
