<?php
// list_entries.php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require 'includes/db_config.php';
require 'includes/nav.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action']; // Añadir esta línea para capturar la acción correctamente
    if ($action == 'update_material') {
        $material_id = $_POST['material_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $weight = $conn->real_escape_string($_POST['weight']);
        $quantity = $conn->real_escape_string($_POST['quantity']);
        $lot = $conn->real_escape_string($_POST['lot']);
        $fec_cad = $conn->real_escape_string($_POST['fec_cad']);
        $conn->query("UPDATE raw_materials SET name='$name', weight='$weight', quantity='$quantity', lot='$lot', fec_cad='$fec_cad' WHERE id='$material_id'");
        echo json_encode(['status' => 'success', 'message' => 'Material actualizado exitosamente']);
    }
    exit();
}

$entries = $conn->query("SELECT * FROM entries");
?>

<!DOCTYPE html>
<html>
<?php include 'includes/header.php'; ?>
<body>
<div class="container mt-4 no-print">
    <h1 class="mt-5">Listado de Entradas</h1>
    <!-- Barra de herramientas -->
    <div class="toolbar mb-3">
        <button class="btn btn-secondary" onclick="downloadCSV()">Descargar CSV</button>
        <button class="btn btn-secondary" onclick="downloadPDF()">Descargar PDF</button>
        <button class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <!-- Filtros -->
    <div id="filters" class="filters mb-3">
        <div class="form-group">
            <label for="startDate">Fecha inicio:</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="form-group">
            <label for="endDate">Fecha fin:</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div class="form-group">
            <label for="supplierFilter">Proveedor:</label>
            <input type="text" id="supplierFilter" class="form-control" placeholder="Ingrese nombre del proveedor">
        </div>
        <div class="form-group">
            <label for="refFilter">Referencia de entrada:</label>
            <input type="text" id="refFilter" class="form-control" placeholder="Ingrese referencia de entrada">
        </div>
        <div class="form-group">
            <label for="loteFilter">Lote de ingrediente:</label>
            <input type="text" id="loteFilter" class="form-control" placeholder="Ingrese lote de ingrediente">
        </div>
        <div class="form-group">
            <label for="cadDateFilter">Fecha de caducidad:</label>
            <input type="date" id="cadDateFilter" class="form-control">
        </div>
        <button class="btn btn-primary align-self-end" onclick="applyFilters()">Aplicar filtros</button>
    </div>
</div>

<div class="container mt-5 table-container" id="printableArea">
    <h3 class="mt-3">Entradas</h3>
    <table id="entriesTable" class="table table-bordered centered">
        <thead>
            <tr>
                <th class="bg-primary text-white">Proveedor</th>
                <th class="bg-primary text-white">Referencia</th>
                <th class="bg-primary text-white">Fecha de Entrada</th>
                <th class="bg-primary text-white">Materiales</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($entry = $entries->fetch_assoc()): ?>
                <tr>
                    <td class="align-middle"><?= $entry['supplier'] ?></td>
                    <td class="align-middle"><?= $entry['ref'] ?></td>
                    <td class="align-middle"><?= $entry['entry_date'] ?></td>
                    <td class="align-middle">
                        <button class="btn btn-info" onclick="toggleMaterials(<?= $entry['id'] ?>)">Ver Materiales</button>
                    </td>
                </tr>
                <tr id="materiales-<?= $entry['id'] ?>" style="display: none;">
                    <td colspan="4">
                        <ul class="list-group">
                            <?php
                            $entry_id = $entry['id'];
                            $materials = $conn->query("SELECT * FROM raw_materials WHERE entry_id='$entry_id'");
                            while ($material = $materials->fetch_assoc()):
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span id="material-<?= $material['id'] ?>">
                                        <?= $material['name'] ?> - <?= $material['weight'] ?>kg - <?= $material['quantity'] ?> unidades - Lote: <?= $material['lot'] ?> - Fecha de Cad: <?= $material['fec_cad'] ?>
                                    </span>
                                    <button class="btn btn-warning btn-sm" onclick="editMaterial(<?= $material['id'] ?>)">Editar</button>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function toggleMaterials(entryId) {
    const materialesRow = document.getElementById('materiales-' + entryId);
    if (materialesRow.style.display === 'none') {
        materialesRow.style.display = '';
    } else {
        materialesRow.style.display = 'none';
    }
}

function downloadCSV() {
    let csv = 'Proveedor,Referencia,Fecha de Entrada,Materiales\n';
    const rows = document.querySelectorAll('#entriesTable tbody tr');
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 4) {
            const supplier = columns[0].innerText;
            const ref = columns[1].innerText;
            const entryDate = columns[2].innerText;
            let materiales = '';
            const materialesRow = row.nextElementSibling;
            if (materialesRow && materialesRow.querySelector('ul')) {
                const listItems = materialesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    materiales += item.innerText;
                    if (index < listItems.length - 1) materiales += ' | ';
                });
            }
            csv += `"${supplier}","${ref}","${entryDate}","${materiales}"\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'entradas.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    html2canvas(document.querySelector("#printableArea")).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth() - 20; // Ajuste de margen
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        doc.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight); // Ajuste de margen
        doc.save('entradas.pdf');
    });
}

function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const supplierFilter = document.getElementById('supplierFilter').value.toLowerCase();
    const refFilter = document.getElementById('refFilter').value.toLowerCase();
    const loteFilter = document.getElementById('loteFilter').value.toLowerCase();
    const cadDateFilter = document.getElementById('cadDateFilter').value;

    const rows = document.querySelectorAll('#entriesTable tbody tr');
    let entries = [];
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 4) {
            const supplier = columns[0].innerText.toLowerCase();
            const ref = columns[1].innerText.toLowerCase();
            const entryDate = columns[2].innerText;
            let materiales = '';
            const materialesRow = row.nextElementSibling;
            if (materialesRow && materialesRow.querySelector('ul')) {
                const listItems = materialesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    materiales += item.innerText.toLowerCase();
                    if (index < listItems.length - 1) materiales += ' | ';
                });
            }
            entries.push({ row, supplier, ref, entryDate, materiales, materialesRow });
        }
    });

    entries = entries.filter(e => {
        let dateCondition = true;
        let supplierCondition = true;
        let refCondition = true;
        let loteCondition = true;
        let cadDateCondition = true;

        if (startDate && endDate) {
            dateCondition = new Date(e.entryDate) >= new Date(startDate) && new Date(e.entryDate) <= new Date(endDate);
        }
        if (supplierFilter) {
            supplierCondition = e.supplier.includes(supplierFilter);
        }
        if (refFilter) {
            refCondition = e.ref.includes(refFilter);
        }
        if (loteFilter) {
            loteCondition = e.materiales.includes(loteFilter);
        }
        if (cadDateFilter) {
            cadDateCondition = e.materiales.includes(cadDateFilter);
        }

        return dateCondition && supplierCondition && refCondition && loteCondition && cadDateCondition;
    });

    rows.forEach(row => {
        row.style.display = 'none';
    });

    entries.forEach(e => {
        e.row.style.display = '';
        e.materialesRow.style.display = 'none';
    });
}

function editMaterial(materialId) {
    const materialSpan = document.getElementById('material-' + materialId);
    const parts = materialSpan.innerText.split(' - ');
    const name = parts[0].trim();
    const weight = parts[1].replace('kg', '').trim();
    const quantity = parts[2].replace('unidades', '').trim();
    const lot = parts[3].replace('Lote:', '').trim();
    const fec_cad = parts[4].replace('Fecha de Cad:', '').trim();

    const newHtml = `<input type="text" value="${name}" id="edit-name-${materialId}">
                     <input type="number" value="${weight}" id="edit-weight-${materialId}">
                     <input type="number" value="${quantity}" id="edit-quantity-${materialId}">
                     <input type="text" value="${lot}" id="edit-lot-${materialId}">
                     <input type="date" value="${fec_cad}" id="edit-fec-cad-${materialId}">
                     <button class="btn btn-success btn-sm" onclick="saveMaterial(${materialId})">Guardar</button>`;

    materialSpan.innerHTML = newHtml;
}

function saveMaterial(materialId) {
    const name = document.getElementById('edit-name-' + materialId).value;
    const weight = document.getElementById('edit-weight-' + materialId).value;
    const quantity = document.getElementById('edit-quantity-' + materialId).value;
    const lot = document.getElementById('edit-lot-' + materialId).value;
    const fec_cad = document.getElementById('edit-fec-cad-' + materialId).value;

    const formData = new FormData();
    formData.append('action', 'update_material');
    formData.append('material_id', materialId);
    formData.append('name', name);
    formData.append('weight', weight);
    formData.append('quantity', quantity);
    formData.append('lot', lot);
    formData.append('fec_cad', fec_cad);

    fetch('list_entries.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
              location.reload();
          } else {
              alert('Error al actualizar el material');
          }
      });
}
</script>

</body>
</html>
