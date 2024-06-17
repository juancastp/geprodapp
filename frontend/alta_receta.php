<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'includes/db_config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add_recipe') {
        $recipe_name = $conn->real_escape_string($_POST['recipe_name']);
        $conn->query("INSERT INTO recipes (recipe_name) VALUES ('$recipe_name')");
        $recipe_id = $conn->insert_id;
        foreach ($_POST['ingredients'] as $ingredient) {
            if (!empty($ingredient)) {
                $ingredient = $conn->real_escape_string($ingredient);
                $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name) VALUES ('$recipe_id', '$ingredient')");
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Receta añadida exitosamente']);
    } elseif ($action == 'delete_recipe') {
        $recipe_id = $_POST['recipe_id'];
        $conn->query("DELETE FROM ingredients WHERE recipe_id='$recipe_id'");
        $conn->query("DELETE FROM recipes WHERE id='$recipe_id'");
        echo json_encode(['status' => 'success', 'message' => 'Receta eliminada exitosamente']);
    } elseif ($action == 'delete_ingredient') {
        $ingredient_id = $_POST['ingredient_id'];
        $conn->query("DELETE FROM ingredients WHERE id='$ingredient_id'");
        echo json_encode(['status' => 'success', 'message' => 'Ingrediente eliminado exitosamente']);
    } elseif ($action == 'update_ingredient') {
        $ingredient_id = $_POST['ingredient_id'];
        $ingredient_name = $conn->real_escape_string($_POST['ingredient_name']);
        $conn->query("UPDATE ingredients SET ingredient_name='$ingredient_name' WHERE id='$ingredient_id'");
        echo json_encode(['status' => 'success', 'message' => 'Ingrediente actualizado exitosamente']);
    } elseif ($action == 'add_ingredient') {
        $recipe_id = $_POST['recipe_id'];
        $ingredient_name = $conn->real_escape_string($_POST['ingredient_name']);
        $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name) VALUES ('$recipe_id', '$ingredient_name')");
        echo json_encode(['status' => 'success', 'message' => 'Ingrediente añadido exitosamente']);
    }
    exit();
}

$recipes = $conn->query("SELECT * FROM recipes");
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
                    <b><i class="bi bi-plus-square"></i></b>
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
                                    <b><i class="bi bi-plus-square"></i></b>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecipe(<?php echo $recipe['id']; ?>)">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush" id="ingredients-list-<?php echo $recipe['id']; ?>">
                            <?php
                            $recipe_id = $recipe['id'];
                            $ingredients = $conn->query("SELECT * FROM ingredients WHERE recipe_id='$recipe_id'");
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
    <?php
    include 'includes/footer.php';
    ?>
    
</body>
</html>
