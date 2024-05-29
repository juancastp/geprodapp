<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');

// Manejar errores de conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add_recipe') {
        $recipe_name = $conn->real_escape_string($_POST['recipe_name']);
        $conn->query("INSERT INTO recipes (recipe_name) VALUES ('$recipe_name')");
        $recipe_id = $conn->insert_id;
        foreach ($_POST['ingredients'] as $ingredient) {
            if (!empty($ingredient)) {
                $ingredient = $conn->real_escape_string($ingredient);
                $conn->query("INSERT INTO recipe_ingredients (recipe_id, ingredient_name) VALUES ('$recipe_id', '$ingredient')");
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Receta añadida exitosamente']);
    } elseif ($action == 'delete_recipe') {
        $recipe_id = $_POST['recipe_id'];
        $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id='$recipe_id'");
        $conn->query("DELETE FROM recipes WHERE id='$recipe_id'");
        echo json_encode(['status' => 'success', 'message' => 'Receta eliminada exitosamente']);
    } elseif ($action == 'delete_ingredient') {
        $ingredient_id = $_POST['ingredient_id'];
        $conn->query("DELETE FROM recipe_ingredients WHERE id='$ingredient_id'");
        echo json_encode(['status' => 'success', 'message' => 'Ingrediente eliminado exitosamente']);
    } elseif ($action == 'update_ingredient') {
        $ingredient_id = $_POST['ingredient_id'];
        $ingredient_name = $conn->real_escape_string($_POST['ingredient_name']);
        $conn->query("UPDATE recipe_ingredients SET ingredient_name='$ingredient_name' WHERE id='$ingredient_id'");
        echo json_encode(['status' => 'success', 'message' => 'Ingrediente actualizado exitosamente']);
    } elseif ($action == 'add_ingredient') {
        $recipe_id = $_POST['recipe_id'];
        $ingredient_name = $conn->real_escape_string($_POST['ingredient_name']);
        $conn->query("INSERT INTO recipe_ingredients (recipe_id, ingredient_name) VALUES ('$recipe_id', '$ingredient_name')");
        echo json_encode(['status' => 'success', 'message' => 'Ingrediente añadido exitosamente']);
    }
    exit();
}

$recipes = $conn->query("SELECT * FROM recipes");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alta Recetas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="images/cupcake.ico">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" width="50" height="50" alt="">
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

    <div class="container mt-4">
        <h1 class="mb-4">Alta de Recetas</h1>
        <form id="recipe-form">
            <div class="form-group">
                <label for="recipe_name">Nombre de la receta:</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="recipe_name" name="recipe_name" required>
                </div>
            </div>
            <div id="ingredients">
                <div class="form-group d-flex">
                    <input type="text" class="form-control" id="ingredient_1" name="ingredients[]" placeholder="Ingrediente 1">
                    <button type="button" class="btn btn-success ml-2" onclick="addNewIngredientField()">
                        <i class="bi bi-plus-square-fill"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Receta</button>
        </form>

        <h2 class="mt-5">Listado de Recetas</h2>
        <div id="recipe-list" class="row">
            <?php while ($recipe = $recipes->fetch_assoc()): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong><?php echo $recipe['recipe_name']; ?></strong>
                            <div>
                                <button class="btn btn-success btn-sm" onclick="addIngredient(<?php echo $recipe['id']; ?>)">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecipe(<?php echo $recipe['id']; ?>)">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush" id="ingredients-list-<?php echo $recipe['id']; ?>">
                            <?php
                            $recipe_id = $recipe['id'];
                            $ingredients = $conn->query("SELECT * FROM recipe_ingredients WHERE recipe_id='$recipe_id'");
                            while ($ingredient = $ingredients->fetch_assoc()):
                            ?>
                                <li class="list-group-item">
                                    <input type="text" class="form-control d-inline w-75" id="ingredient-input-<?php echo $ingredient['id']; ?>" value="<?php echo $ingredient['ingredient_name']; ?>" disabled>
                                    <div class="float-right">
                                        <button class="btn btn-warning btn-sm" onclick="editIngredient(<?php echo $ingredient['id']; ?>)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm hidden" id="save-btn-<?php echo $ingredient['id']; ?>" onclick="saveIngredient(<?php echo $ingredient['id']; ?>)">
                                            <i class="bi bi-floppy2-fill"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteIngredient(<?php echo $ingredient['id']; ?>)">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function addNewIngredientField() {
            const ingredientsDiv = document.getElementById('ingredients');
            const count = ingredientsDiv.getElementsByClassName('form-control').length + 1;
            const newDiv = document.createElement('div');
            newDiv.classList.add('form-group', 'd-flex', 'mt-2');
            newDiv.innerHTML = `<input type="text" class="form-control" id="ingredient_${count}" name="ingredients[]" placeholder="Ingrediente ${count}">
                                <button type="button" class="btn btn-success ml-2" onclick="addNewIngredientField()">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>`;
            ingredientsDiv.appendChild(newDiv);
            const buttons = ingredientsDiv.querySelectorAll('button');
            buttons.forEach((button, index) => {
                if (index < buttons.length - 1) {
                    button.style.display = 'none';
                } else {
                    button.style.display = 'block';
                }
            });
        }

        function addIngredient(recipeId) {
            const ingredientName = prompt("Ingrese el nombre del nuevo ingrediente:");
            if (ingredientName) {
                const formData = new FormData();
                formData.append('action', 'add_ingredient');
                formData.append('recipe_id', recipeId);
                formData.append('ingredient_name', ingredientName);
                fetch('alta_receta.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al añadir el ingrediente');
                      }
                  });
            }
        }

        document.getElementById('recipe-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_recipe');
            fetch('alta_receta.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      alert(data.message);
                      location.reload();
                  } else {
                      alert('Error al añadir la receta');
                  }
              });
        });

        function deleteRecipe(recipeId) {
            if (confirm('¿Está seguro de que desea eliminar esta receta?')) {
                const formData = new FormData();
                formData.append('action', 'delete_recipe');
                formData.append('recipe_id', recipeId);
                fetch('alta_receta.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al eliminar la receta');
                      }
                  });
            }
        }

        function deleteIngredient(ingredientId) {
            if (confirm('¿Está seguro de que desea eliminar este ingrediente?')) {
                const formData = new FormData();
                formData.append('action', 'delete_ingredient');
                formData.append('ingredient_id', ingredientId);
                fetch('alta_receta.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al eliminar el ingrediente');
                      }
                  });
            }
        }

        function editIngredient(ingredientId) {
            document.getElementById('ingredient-input-' + ingredientId).disabled = false;
            document.getElementById('save-btn-' + ingredientId).classList.remove('hidden');
        }

        function saveIngredient(ingredientId) {
            const ingredientName = document.getElementById('ingredient-input-' + ingredientId).value;
            const formData = new FormData();
            formData.append('action', 'update_ingredient');
            formData.append('ingredient_id', ingredientId);
            formData.append('ingredient_name', ingredientName);
            fetch('alta_receta.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      alert(data.message);
                      document.getElementById('ingredient-input-' + ingredientId).disabled = true;
                      document.getElementById('save-btn-' + ingredientId).classList.add('hidden');
                  } else {
                      alert('Error al actualizar el ingrediente');
                  }
              });
        }
    </script>
</body>
</html>
