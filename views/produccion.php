<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config/config.php'; // Incluye la configuración de la base de datos

// Obtener recetas para el desplegable
$stmt = $pdo->query("SELECT * FROM recetas");
$recetas = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="container">
    <h2 class="mt-5">Producción</h2>
    <form action="../controllers/produccionController.php" method="POST">
        <div class="form-group">
            <label for="receta_id">Receta:</label>
            <select class="form-control" id="receta_id" name="receta_id" onchange="fetchIngredients(this.value)" required>
                <option value="">Seleccione una receta</option>
                <?php foreach ($recetas as $receta): ?>
                    <option value="<?= $receta['id'] ?>"><?= $receta['nombre_producto_final'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="ingredientes"></div>
        <div class="form-group">
            <label for="lote_produccion">Lote de Producción:</label>
            <input type="text" class="form-control" id="lote_produccion" name="lote_produccion" required>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Producción</button>
    </form>

    <h3 class="mt-5">Listado de Productos Finales</h3>
    <?php
    // Obtener todos los productos finales producidos, ordenados por fecha de producción
    $stmt = $pdo->query("SELECT produccion.*, recetas.nombre_producto_final FROM produccion JOIN recetas ON produccion.receta_id = recetas.id ORDER BY fecha_produccion DESC");
    $productos_finales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener lotes de ingredientes usados agrupados por producción
    $stmt = $pdo->query("SELECT lotes_ingredientes_usados.*, ingredientes_recetas.nombre_ingrediente 
                         FROM lotes_ingredientes_usados 
                         JOIN ingredientes_recetas ON lotes_ingredientes_usados.ingrediente_id = ingredientes_recetas.id");
    $lotes_ingredientes = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    
    $fecha_actual = null;
    ?>
    <table class="table table-bordered">
        <tbody>
            <?php foreach ($productos_finales as $producto): ?>
                <?php if ($producto['fecha_produccion'] !== $fecha_actual): ?>
                    <?php $fecha_actual = $producto['fecha_produccion']; ?>
                    <tr>
                        <td colspan="4" class="text-center bg-success text-white"><?= $fecha_actual ?></td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Cantidad</th>
                        <th class="bg-primary text-white">Producto</th>
                        <th class="bg-primary text-white">Lote</th>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td rowspan="<?= isset($lotes_ingredientes[$producto['id']]) ? count($lotes_ingredientes[$producto['id']]) + 1 : 1 ?>" class="align-middle"><?= $producto['cantidad'] ?></td>
                    <td><strong><?= $producto['nombre_producto_final'] ?></strong></td>
                    <td><?= $producto['lote_produccion'] ?></td>
                </tr>
                <?php if (isset($lotes_ingredientes[$producto['id']])): ?>
                    <?php foreach ($lotes_ingredientes[$producto['id']] as $ingrediente): ?>
                        <tr>
                            <td>- <?= $ingrediente['nombre_ingrediente'] ?></td>
                            <td><?= $ingrediente['lote'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function fetchIngredients(recetaId) {
    if (recetaId) {
        fetch('../controllers/recetaController.php?action=getIngredients&receta_id=' + recetaId)
        .then(response => response.json())
        .then(data => {
            const ingredientesDiv = document.getElementById('ingredientes');
            ingredientesDiv.innerHTML = '';
            data.forEach(ingrediente => {
                const div = document.createElement('div');
                div.className = 'form-group';
                div.innerHTML = `
                    <label for="lote_ingrediente_${ingrediente.id}">Lote de ${ingrediente.nombre_ingrediente}:</label>
                    <input type="text" class="form-control" id="lote_ingrediente_${ingrediente.id}" name="lote_ingrediente[${ingrediente.id}]" required>
                `;
                ingredientesDiv.appendChild(div);
            });
        })
        .catch(error => console.error('Error fetching ingredients:', error));
    } else {
        document.getElementById('ingredientes').innerHTML = '';
    }
}
</script>


<?php include '../includes/footer.php'; ?>
