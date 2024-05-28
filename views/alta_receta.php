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
            <div class="form-group d-flex">
                <label for="nombre_ingrediente" class="mr-2">Ingrediente:</label>
                <input type="text" class="form-control" id="nombre_ingrediente" name="nombre_ingrediente[]">
                <button type="button" class="btn btn-success ml-2" onclick="addIngredient()">+</button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>

    <h3 class="mt-5">Listado de Recetas</h3>
    <div class="row">
        <?php foreach ($recetas_ingredientes as $receta_id => $receta): ?>
            <div class="col-md-6 mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Receta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?= htmlspecialchars($receta[0]['nombre_producto_final']) ?></strong></td>
                            <td>
                                <a href="../controllers/recetaController.php?action=delete&receta_id=<?= $receta_id ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                        <?php foreach ($receta as $ingrediente): ?>
                            <tr>
                                <td>- <?= htmlspecialchars($ingrediente['nombre_ingrediente']) ?></td>
                                <td>
                                    <a href="../controllers/recetaController.php?action=edit&ingrediente_id=<?= $ingrediente['ingrediente_id'] ?>" class="btn btn-warning">Editar</a>
                                    <a href="../controllers/recetaController.php?action=delete&ingrediente_id=<?= $ingrediente['ingrediente_id'] ?>" class="btn btn-danger">Eliminar</a>
                                    <?php if ($ingrediente === end($receta)): ?>
                                        <button type="button" class="btn btn-success" onclick="addExistingIngredient(<?= $receta_id ?>)">+</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function addIngredient() {
    const div = document.createElement('div');
    div.className = 'form-group d-flex';
    div.innerHTML = `
        <label class="mr-2">Ingrediente:</label>
        <input type="text" class="form-control" name="nombre_ingrediente[]">
        <button type="button" class="btn btn-success ml-2" onclick="addIngredient()">+</button>
    `;
    document.getElementById('ingredientes').appendChild(div);
}

function addExistingIngredient(recetaId) {
    const div = document.createElement('div');
    div.className = 'form-group';
    div.innerHTML = `
        <form action="../controllers/recetaController.php" method="POST">
            <input type="hidden" name="receta_id" value="${recetaId}">
            <input type="text" class="form-control" name="nombre_ingrediente">
            <button type="submit" class="btn btn-primary ml-2">Guardar</button>
        </form>
    `;
    document.getElementById('receta_' + recetaId).appendChild(div);
}
</script>

<?php include '../includes/footer.php'; ?>
