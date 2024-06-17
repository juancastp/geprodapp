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

<?php
include 'includes/footer.php';
?>
</body>
</html>
