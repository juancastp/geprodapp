<?php
$host = 'localhost';
$db = 'gluttiere';
$user = 'saglu';
$pass = 'W/qxFZpcDh4NIitn';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
