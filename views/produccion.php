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
