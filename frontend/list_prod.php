<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'includes/db_config.php'; 

// Consulta para obtener todos los registros de producción
$sql = "SELECT production.*, recipes.recipe_name FROM production
        JOIN recipes ON production.product_name = recipes.recipe_name
        ORDER BY production.production_date DESC";
$result = $conn->query($sql);

// Obtener los detalles de los ingredientes de producción
$sql_ingredients = "SELECT production_details.*, ingredients.ingredient_name FROM production_details
                    JOIN ingredients ON production_details.ingredient_name = ingredients.ingredient_name";
$ingredients_result = $conn->query($sql_ingredients);

// Crear un mapa de producción a ingredientes
$production_ingredients = [];
while ($row = $ingredients_result->fetch_assoc()) {
    $production_id = $row['production_id'];
    if (!isset($production_ingredients[$production_id])) {
        $production_ingredients[$production_id] = [];
    }
    $production_ingredients[$production_id][] = $row;
}

echo '<script>console.log("Mapa de ingredientes de producción:", ' . json_encode($production_ingredients) . ');</script>';
?>

<!DOCTYPE html>
<html>
<?php 
include 'includes/header.php'; 
?>
<body>
<?php 
include 'includes/nav.php'; 
?>

<div class="container mt-4 no-print">
    <h1 class="mt-5">Listado de Producción</h1>

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
</div>

<div class="container mt-5 table-container centered" id="printableArea">
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
                <tr id="ingredientes-<?= $producto['id'] ?>">
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
<?php
include 'includes/footer.php';
?>

</body>
</html>
