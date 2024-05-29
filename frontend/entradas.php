<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');

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
            $conn->query("INSERT INTO raw_materials (entry_id, name, weight, quantity, lot) VALUES ('$entry_id', '$name', '$weight', '$quantity', '$lot')");
        }
        echo json_encode(['status' => 'success', 'message' => 'Entrada añadida exitosamente']);
    } elseif ($action == 'delete_entry') {
        $entry_id = $_POST['entry_id'];
        $conn->query("DELETE FROM raw_materials WHERE entry_id='$entry_id'");
        $conn->query("DELETE FROM entries WHERE id='$entry_id'");
        echo json_encode(['status' => 'success', 'message' => 'Entrada eliminada exitosamente']);
    }
    exit();
}

$entries = $conn->query("SELECT * FROM entries");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Entradas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="images/cupcake.ico">
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
                <li class="nav-item"><a class="nav-link" href="informes.php">Listado Produccion</a></li>
            </ul>
        </div>
    </nav>

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
                            <input type="number" class="form-control" name="materials[0][weight]" placeholder="Peso (Kg)" required>
                        </div>
                        <div class="form-group col-md-2">
                            <input type="number" class="form-control" name="materials[0][quantity]" placeholder="Cantidad" required>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="text" class="form-control" name="materials[0][lot]" placeholder="Lote" required>
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

        <h2 class="mt-5">Listado de Entradas</h2>
        <div id="entry-list" class="row">
            <?php while ($entry = $entries->fetch_assoc()): ?>
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <strong><?php echo $entry['supplier']; ?> - <?php echo $entry['ref']; ?> - <?php echo $entry['entry_date']; ?></strong>
                            <button class="btn btn-danger btn-sm float-right" onclick="deleteEntry(<?php echo $entry['id']; ?>)">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php
                            $entry_id = $entry['id'];
                            $materials = $conn->query("SELECT * FROM raw_materials WHERE entry_id='$entry_id'");
                            while ($material = $materials->fetch_assoc()):
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $material['name']; ?> - <?php echo $material['weight']; ?>kg - <?php echo $material['quantity']; ?> unidades - Lote: <?php echo $material['lot']; ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
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
                                        <input type="number" class="form-control" name="materials[${count}][weight]" placeholder="Peso" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="number" class="form-control" name="materials[${count}][quantity]" placeholder="Cantidad" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input type="text" class="form-control" name="materials[${count}][lot]" placeholder="Lote" required>
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

        function deleteEntry(entryId) {
            if (confirm('¿Está seguro de que desea eliminar esta entrada?')) {
                const formData = new FormData();
                formData.append('action', 'delete_entry');
                formData.append('entry_id', entryId);
                fetch('entradas.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al eliminar la entrada');
                      }
                  });
            }
        }
    </script>
</body>
</html>
