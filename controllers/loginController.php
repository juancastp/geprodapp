<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->execute(['usuario' => $usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($contrasena, $user['contrasena'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ../views/index.php");
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>
