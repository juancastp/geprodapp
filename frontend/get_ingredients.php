<?php
$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recipe_name'])) {
    $recipe_name = $conn->real_escape_string($_POST['recipe_name']);
    $sql = "SELECT ingredients.ingredient_name FROM ingredients
            JOIN recipes ON ingredients.recipe_id = recipes.id
            WHERE recipes.recipe_name = '$recipe_name'";
    $result = $conn->query($sql);

    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }
    echo json_encode($ingredients);
}
?>
