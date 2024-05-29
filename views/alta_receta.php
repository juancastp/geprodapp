<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config/config.php'; // Incluye la configuración de la base de datos

// Obtener todas las recetas con sus ingredientes
$stmt = $pdo->query("SELECT recetas.id as receta_id, recetas.nombre_producto_final, ingredientes_recetas.id as ingrediente_id, ingredientes_recetas.nombre_ingrediente 
                     FROM recetas 
                     LEFT JOIN ingredientes_recetas ON recetas.id = ingredientes_recetas.receta_id 
                     ORDER BY recetas.id");
$recetas_ingredientes = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

// Obtener todas las recetas para el filtro
$stmt = $pdo->query("SELECT id, nombre_producto_final FROM recetas");
$recetas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h2 class="mt-5">Alta de Recetas</h2>
    <form action="../controllers/recetaController.php" method="POST">
        <div class="form-group">
            <label for="nombre_producto_final">Nombre del Producto Final:</label>
            <input type="text" class="form-control" id="nombre_producto_final" name="nombre_producto_final" required>
        </div>
        <div id="ingredientes">
            <div class="form-group d-flex align-items-center">
                <label for="nombre_ingrediente">Ingrediente:</label>
                <input type="text" class="form-control" id="nombre_ingrediente" name="nombre_ingrediente[]">
                <button type="button" class="btn btn-success ml-2" onclick="addIngredient(this)">+</button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>

    <h3 class="mt-5">Filtros</h3>
    <form id="filter-form">
        <div class="form-group">
            <label for="filter_receta">Recetas:</label>
            <select class="form-control" id="filter_receta" name="filter_receta">
                <option value="">Todas las recetas</option>
                <?php foreach ($recetas as $receta): ?>
                    <option value="<?= $receta['id'] ?>"><?= $receta['nombre_producto_final'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <h3 class="mt-5">Listado de Recetas</h3>
    <div class="row">
        <?php foreach ($recetas_ingredientes as $receta_id => $receta): ?>
            <div class="col-md-6 mb-4 receta-item" data-receta-id="<?= $receta_id ?>">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Receta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?= $receta[0]['nombre_producto_final'] ?></strong></td>
                            <td>
                                <a href="../controllers/recetaController.php?action=delete&receta_id=<?= $receta_id ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                        <?php foreach ($receta as $ingrediente): ?>
                            <tr>
                                <td>- <?= $ingrediente['nombre_ingrediente'] ?></td>
                                <td>
                                    <a href="../controllers/recetaController.php?action=edit&ingrediente_id=<?= $ingrediente['ingrediente_id'] ?>" class="btn btn-warning">Editar</a>
                                    <a href="../controllers/recetaController.php?action=delete&ingrediente_id=<?= $ingrediente['ingrediente_id'] ?>" class="btn btn-danger">Eliminar</a>
                                    <?php if ($ingrediente === end($receta)): ?>
                                        <button type="button" class="btn btn-success" onclick="addExistingIngredient(<?= $receta_id ?>)">+</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="receta_<?= $receta_id ?>"></tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function addIngredient(button) {
    const div = document.createElement('div');
    div.className = 'form-group d-flex align-items-center';
    div.innerHTML = `
        <input type="text" class="form-control" name="nombre_ingrediente[]">
        <button type="button" class="btn btn-success ml-2" onclick="addIngredient(this)">+</button>
    `;
    const parentDiv = button.parentNode;
    parentDiv.parentNode.insertBefore(div, parentDiv.nextSibling);
    button.remove();
}

function addExistingIngredient(recetaId) {
    const tr = document.getElementById('receta_' + recetaId);
    const td = document.createElement('td');
    td.colSpan = 2;
    td.innerHTML = `
        <form action="../controllers/recetaController.php" method="POST" class="d-flex align-items-center">
            <input type="hidden" name="receta_id" value="${recetaId}">
            <input type="text" class="form-control" name="nombre_ingrediente">
            <button type="submit" class="btn btn-primary ml-2">Guardar</button>
        </form>
    `;
    tr.appendChild(td);
}

document.getElementById('filter_receta').addEventListener('change', function() {
    const selectedReceta = this.value;
    document.querySelectorAll('.receta-item').forEach(function(item) {
        if (selectedReceta === '' || item.dataset.recetaId === selectedReceta) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
