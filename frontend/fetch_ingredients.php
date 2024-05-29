<?php
$servername = "localhost";
$username = "saglu";
$password = "W/qxFZpcDh4NIitn";
$dbname = "geprodapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['recipe_id'])) {
    $recipe_id = $_POST['recipe_id'];
    $sql = "SELECT ingredient_name FROM ingredients WHERE recipe_id = $recipe_id";
    $result = $conn->query($sql);

    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }

    echo json_encode($ingredients);
}

$conn->close();
?>
