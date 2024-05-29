<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');

// Consulta para obtener todos los registros de producción
$sql = "SELECT production.*, recipes.recipe_name FROM production
        JOIN recipes ON production.product_name = recipes.recipe_name
        ORDER BY production.production_date DESC";
$result = $conn->query($sql);

// Verificar si la consulta de producción tiene resultados
if ($result === false) {
    die('Error en la consulta de producción: ' . $conn->error);
}

// Obtener los detalles de los ingredientes de producción
$sql_ingredients = "SELECT production_details.*, ingredients.ingredient_name FROM production_details
                    JOIN ingredients ON production_details.ingredient_name = ingredients.ingredient_name";
$ingredients_result = $conn->query($sql_ingredients);

// Verificar si la consulta de ingredientes tiene resultados
if ($ingredients_result === false) {
    die('Error en la consulta de ingredientes: ' . $conn->error);
}

// Crear un mapa de producción a ingredientes
$production_ingredients = [];
while ($row = $ingredients_result->fetch_assoc()) {
    $production_id = $row['production_id'];
    if (!isset($production_ingredients[$production_id])) {
        $production_ingredients[$production_id] = [];
    }
    $production_ingredients[$production_id][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Informe de Producción</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="images/logo.png" width="30" height="30" alt="">
        Gluttire
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="alta_receta.php">Alta Recetas</a></li>
            <li class="nav-item"><a class="nav-link" href="entradas.php">Entradas</a></li>
            <li class="nav-item"><a class="nav-link" href="produccion.php">Producción</a></li>
            <li class="nav-item"><a class="nav-link" href="add_user.php">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="informes.php">Informes</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mt-5">Listado de Producción</h2>

    <!-- Barra de herramientas -->
    <div class="toolbar mb-3">
        <button class="btn btn-secondary" onclick="downloadCSV()">Descargar CSV</button>
        <button class="btn btn-secondary" onclick="downloadPDF()">Descargar PDF</button>
        <button class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <!-- Filtros -->
    <div class="filters mb-3">
        <div class="form-group">
            <label for="startDate">Fecha inicio:</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="form-group">
            <label for="endDate">Fecha fin:</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div class="form-group">
            <label for="loteFilter">Lote de ingrediente:</label>
            <input type="text" id="loteFilter" class="form-control" placeholder="Ingrese lote de ingrediente">
        </div>
        <div class="form-group">
            <label for="orderBy">Ordenar por fecha:</label>
            <select id="orderBy" class="form-control">
                <option value="desc">Mayor a menor</option>
                <option value="asc">Menor a mayor</option>
            </select>
        </div>
        <button class="btn btn-primary" onclick="applyFilters()">Aplicar filtros</button>
    </div>

    <h3 class="mt-3">Productos Finales</h3>
    <?php
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
            <?php while ($producto = $result->fetch_assoc()): ?>
                <?php if ($producto['production_date'] !== $fecha_actual): ?>
                    <?php $fecha_actual = $producto['production_date']; ?>
                    <tr>
                        <td colspan="5" class="text-center bg-success text-white"><?= $fecha_actual ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="align-middle"><?= $producto['production_date'] ?></td>
                    <td class="align-middle"><?= $producto['quantity'] ?></td>
                    <td class="align-middle"><strong><?= $producto['recipe_name'] ?></strong></td>
                    <td class="align-middle"><?= $producto['production_lot'] ?></td>
                    <td class="align-middle">
                        <button class="btn btn-info" onclick="toggleIngredients(<?= $producto['id'] ?>)">Ver Ingredientes</button>
                    </td>
                </tr>
                <tr id="ingredientes-<?= $producto['id'] ?>" style="display: none;">
                    <td colspan="5">
                        <ul class="list-group">
                            <?php if (isset($production_ingredients[$producto['id']])): ?>
                                <?php foreach ($production_ingredients[$producto['id']] as $ingrediente): ?>
                                    <li class="list-group-item">
                                        <strong><?= $ingrediente['ingredient_name'] ?>:</strong> <?= $ingrediente['ingredient_lot'] ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No hay ingredientes registrados</li>
                            <?php endif; ?>
                        </ul>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
const production_ingredients = <?= json_encode($production_ingredients) ?>;
console.log('production_ingredients:', production_ingredients);

function toggleIngredients(productId) {
    const ingredientesRow = document.getElementById('ingredientes-' + productId);
    if (ingredientesRow.style.display === 'none') {
        const ingredientes = production_ingredients[productId];
        if (ingredientes && ingredientes.length > 0) {
            let list = '';
            ingredientes.forEach(ingrediente => {
                list += `<li class="list-group-item"><strong>${ingrediente.ingredient_name}:</strong> ${ingrediente.ingredient_lot}</li>`;
            });
            ingredientesRow.querySelector('.list-group').innerHTML = list;
        } else {
            ingredientesRow.querySelector('.list-group').innerHTML = '<li class="list-group-item">No hay ingredientes registrados</li>';
        }
        ingredientesRow.style.display = '';
    } else {
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

</body>
</html>
