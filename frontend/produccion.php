<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');

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
<head>
    <title>Registro de Producción</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="images/cupcake.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <li class="nav-item"><a class="nav-link" href="informes.php">Listado de producción</a></li>
        </ul>
    </div>
</nav>

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
