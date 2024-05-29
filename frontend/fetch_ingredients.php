<?php
if (isset($_GET['recipe_id'])) {
    $recipe_id = $_GET['recipe_id'];
    $conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');
    $result = $conn->query("SELECT ingredient_name FROM recipe_ingredients WHERE recipe_id='$recipe_id'");
    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }
    echo json_encode($ingredients);
}
?>
