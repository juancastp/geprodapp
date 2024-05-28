<?php
session_start();
include '../config/config.php';

// Manejo de inactividad de sesión
$inactive = 900; // 15 minutos

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactive) {
    session_unset();
    session_destroy();
    header("Location: ../views/index.php");
    exit;
}

$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->execute(['usuario' => $usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($contrasena, $user['contrasena'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time(); // Inicializar el tiempo de actividad
        header("Location: ../views/index.php");
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>
