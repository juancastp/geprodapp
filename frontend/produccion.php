<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $production_lot = $conn->real_escape_string($_POST['production_lot']);
    $production_date = date('Y-m-d');
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $ingredients = $_POST['ingredients'];
    $username = $_SESSION['username'];

    try {
        $conn->begin_transaction();
        // Insertar producción
        $conn->query("INSERT INTO production (product_name, production_lot, production_date, username) VALUES ('$product_name', '$production_lot', '$production_date', '$username')");
        $production_id = $conn->insert_id;

        // Insertar detalles de producción
        foreach ($ingredients as $ingredient) {
            if (!empty($ingredient['name']) && !empty($ingredient['lot'])) {
                $ingredient_name = $conn->real_escape_string($ingredient['name']);
                $ingredient_lot = $conn->real_escape_string($ingredient['lot']);
                $conn->query("INSERT INTO production_details (production_id, ingredient_name, ingredient_lot) VALUES ('$production_id', '$ingredient_name', '$ingredient_lot')");
            }
        }
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Producción registrada exitosamente']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la producción: ' . $e->getMessage()]);
    }
    exit();
}

$recipes = $conn->query("SELECT * FROM recipes");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Producción</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="images/cupcake.ico">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .container {
            margin-top: 30px;
        }
    </style>
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
            <li class="nav-item"><a class="nav-link" href="informes.php">Informes</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="mb-4">Registro de Producción</h1>
    <form id="production-form">
        <div class="form-group">
            <label for="product_name">Receta:</label>
            <select class="form-control" id="product_name" name="product_name" required onchange="loadIngredients(this.value)">
                <option value="">Seleccione una receta</option>
                <?php while ($recipe = $recipes->fetch_assoc()): ?>
                    <option value="<?= $recipe['id'] ?>"><?= $recipe['recipe_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div id="ingredients">
            <!-- Los ingredientes se cargarán aquí dinámicamente -->
        </div>
        <div class="form-group mt-3">
            <label for="production_lot">Lote de Producción:</label>
            <input type="text" class="form-control" id="production_lot" name="production_lot" required>
        </div>
        <div class="form-group mt-3">
            <label for="quantity">Cantidad:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Producción</button>
    </form>
</div>

<script>
    function addNewIngredientField(index) {
        const ingredientsDiv = document.getElementById('ingredients');
        const newDiv = document.createElement('div');
        newDiv.classList.add('form-group', 'd-flex', 'mt-2');
        newDiv.innerHTML = `<input type="text" class="form-control" name="ingredients[${index}][name]" placeholder="Ingrediente" readonly>
                            <input type="text" class="form-control ml-2" name="ingredients[${index}][lot]" placeholder="Lote">
                            <button type="button" class="btn btn-danger ml-2" onclick="removeIngredientField(this)">
                                <i class="bi bi-dash-square-fill"></i>
                            </button>`;
        ingredientsDiv.appendChild(newDiv);
    }

    function removeIngredientField(button) {
        button.parentElement.remove();
    }

    function loadIngredients(recipeId) {
        if (!recipeId) {
            document.getElementById('ingredients').innerHTML = '';
            return;
        }
        fetch(`fetch_ingredients.php?recipe_id=${recipeId}`)
            .then(response => response.json())
            .then(data => {
                const ingredientsDiv = document.getElementById('ingredients');
                ingredientsDiv.innerHTML = '';
                data.forEach((ingredient, index) => {
                    addNewIngredientField(index);
                    const ingredientInputs = ingredientsDiv.querySelectorAll(`input[name="ingredients[${index}][name]"]`);
                    ingredientInputs[0].value = ingredient.ingredient_name;
                });
            });
    }

    document.getElementById('production-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('produccion.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  alert(data.message);
                  location.reload();
              } else {
                  alert('Error al registrar la producción: ' + data.message);
              }
          }).catch(error => {
              console.error('Error:', error);
              alert('Error de conexión o servidor no disponible');
          });
    });
</script>
</body>
</html>
