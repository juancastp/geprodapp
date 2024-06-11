<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'includes/db_config.php'; 



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add_entry') {
        $supplier = $conn->real_escape_string($_POST['supplier']);
        $entry_date = $conn->real_escape_string($_POST['entry_date']);
        $ref = $conn->real_escape_string($_POST['ref']);
        $username = $_SESSION['username'];
        $conn->query("INSERT INTO entries (supplier, entry_date, ref, username) VALUES ('$supplier', '$entry_date', '$ref', '$username')");
        $entry_id = $conn->insert_id;
        foreach ($_POST['materials'] as $material) {
            $name = $conn->real_escape_string($material['name']);
            $weight = $conn->real_escape_string($material['weight']);
            $quantity = $conn->real_escape_string($material['quantity']);
            $lot = $conn->real_escape_string($material['lot']);
            $fec_cad = $conn->real_escape_string($material['fec_cad']);
            $conn->query("INSERT INTO raw_materials (entry_id, name, weight, quantity, lot, fec_cad) VALUES ('$entry_id', '$name', '$weight', '$quantity', '$lot', '$fec_cad')");
        }
        echo json_encode(['status' => 'success', 'message' => 'Entrada añadida exitosamente']);
        exit();
    }
}
?>

<!DOCTYPE html>
<?php
require 'includes/header.php';
?>
<body>
<?php
require 'includes/nav.php';
?>
<div class="container mt-4">
    <h1 class="mb-4">Registro de Entradas</h1>
    <form id="entry-form">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="supplier">Proveedor:</label>
                <input type="text" class="form-control" id="supplier" name="supplier" required>
            </div>
            <div class="form-group col-md-4">
                <label for="entry_date">Fecha de entrada:</label>
                <input type="date" class="form-control" id="entry_date" name="entry_date" required>
            </div>
            <div class="form-group col-md-4">
                <label for="ref">Referencia de entrada:</label>
                <input type="text" class="form-control" id="ref" name="ref" required>
            </div>
        </div>
        <div id="materials">
            <div class="form-group">
                <label>Artículo 1:</label>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" name="materials[0][name]" placeholder="Material" required>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="number" step="any" class="form-control" name="materials[0][weight]" placeholder="Peso (Kg)" required>
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" step="any" class="form-control" name="materials[0][quantity]" placeholder="Cantidad" required>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" name="materials[0][lot]" placeholder="Lote" required>
                    </div>
                    <div class="form-group col-md-2">
                        <input id="primerart" type="date" class="form-control" name="materials[0][fec_cad]" placeholder="Fecha de cad." required>
                    </div>
                    <div class="form-group col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-success" onclick="addMaterialField()">
                            <i class="bi bi-plus-square-fill"></i>
                        </button>
                    </div>
                    <div class="form-group col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger" onclick="removeMaterialField(this)">
                            <i class="bi bi-dash-square-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<script>
function addMaterialField() {
    const materialsDiv = document.getElementById('materials');
    const count = materialsDiv.getElementsByClassName('form-group').length;
    const newDiv = document.createElement('div');
    newDiv.classList.add('form-group');
    newDiv.innerHTML = `<label>Artículo ${count + 1}:</label>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <input type="text" class="form-control" name="materials[${count}][name]" placeholder="Material" required>
                            </div>
                            <div class="form-group col-md-2">
                                <input type="number" step="any" class="form-control" name="materials[${count}][weight]" placeholder="Peso" required>
                            </div>
                            <div class="form-group col-md-1">
                                <input type="number" step="any" class="form-control" name="materials[${count}][quantity]" placeholder="Cantidad" required>
                            </div>
                            <div class="form-group col-md-2">
                                <input type="text" class="form-control" name="materials[${count}][lot]" placeholder="Lote" required>
                            </div>
                            <div class="form-group col-md-2">
                                <input id="demasart" type="date" class="form-control" name="materials[${count}][fec_cad]" placeholder="Fecha de cad." required>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-success" onclick="addMaterialField()">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" onclick="removeMaterialField(this)">
                                    <i class="bi bi-dash-square-fill"></i>
                                </button>
                            </div>
                        </div>`;
    materialsDiv.appendChild(newDiv);
    updateAddButtons();
}

function removeMaterialField(button) {
    console.log('Botón de eliminar presionado');
    const formRow = button.closest('.form-row');
    console.log('Elemento formRow encontrado:', formRow);
    if (formRow) {
        formRow.parentElement.remove();
        console.log('formRow eliminado');
    } else {
        console.log('formRow no encontrado');
    }
    updateAddButtons();
}

function updateAddButtons() {
    const materialsDiv = document.getElementById('materials');
    const addButtons = materialsDiv.querySelectorAll('button.btn-success');
    addButtons.forEach((button, index) => {
        button.style.display = (index === addButtons.length - 1) ? 'block' : 'none';
    });
}

document.getElementById('entry-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add_entry');
    fetch('entradas.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
              location.reload();
          } else {
              alert('Error al añadir la entrada');
          }
      });
});
</script>
</body>
</html>
