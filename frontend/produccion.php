<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'includes/db_config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = $_POST['recipe_id'];
    $production_lot = $_POST['production_lot'];
    $quantity = $_POST['quantity'];
    $username = $_SESSION['username'];
    $production_date = date('Y-m-d');

    $sql = "INSERT INTO production (product_name, production_lot, production_date, quantity, username) 
            VALUES ((SELECT recipe_name FROM recipes WHERE id = $recipe_id), '$production_lot', '$production_date', $quantity, '$username')";

    if ($conn->query($sql) === TRUE) {
        $production_id = $conn->insert_id;
        $ingredients = $_POST['ingredients'];

        foreach ($ingredients as $ingredient) {
            $ingredient_name = $ingredient['name'];
            $ingredient_lot = $ingredient['lot'];
            $sql_detail = "INSERT INTO production_details (production_id, ingredient_name, ingredient_lot) 
                           VALUES ($production_id, '$ingredient_name', '$ingredient_lot')";
            $conn->query($sql_detail);
        }

        echo "<script>alert('Registro de producción guardado exitosamente');</script>";
    } else {
        echo "<script>alert('Error al registrar la producción: " . $conn->error . "');</script>";
    }
}

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

<div class="container mt-5">
<h1 class="mb-4">Registro de producción</h1>
    <form method="POST">
        <div class="form-group">
            <label for="recipe">Receta:</label>
            <select id="recipe" name="recipe_id" class="form-control" required>
                <option value="">Seleccione una receta</option>
                <?php
                $recipes = $conn->query("SELECT * FROM recipes");
                while ($recipe = $recipes->fetch_assoc()) {
                    echo "<option value='{$recipe['id']}'>{$recipe['recipe_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div id="ingredients-container"></div>
        <div class="form-group">
            <label for="production_lot">Lote de Producción:</label>
            <input type="text" id="production_lot" name="production_lot" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="quantity">Cantidad:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Producción</button>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#recipe').change(function() {
        const recipeId = $(this).val();
        if (recipeId) {
            $.ajax({
                url: 'fetch_ingredients.php',
                type: 'POST',
                data: { recipe_id: recipeId },
                dataType: 'json',
                success: function(data) {
                    console.log("Ingredientes recibidos:", data);
                    let html = '<h4>Ingredientes</h4>';
                    data.forEach(ingredient => {
                        html += `<div class="form-group">
                                    <label>${ingredient.ingredient_name}</label>
                                    <input type="hidden" name="ingredients[${ingredient.ingredient_name}][name]" value="${ingredient.ingredient_name}">
                                    <input type="text" name="ingredients[${ingredient.ingredient_name}][lot]" class="form-control" placeholder="Lote de ${ingredient.ingredient_name}" required>
                                 </div>`;
                    });
                    $('#ingredients-container').html(html);
                },
                error: function(error) {
                    console.log("Error al obtener los ingredientes:", error);
                }
            });
        } else {
            $('#ingredients-container').html('');
        }
    });
});
</script>
</body>
</html>
