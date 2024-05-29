<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_completo = $_POST['nombre_completo'];
    $usuario = $_POST['usuario'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, usuario, contrasena) VALUES (:nombre_completo, :usuario, :contrasena)");
    $stmt->execute([
        'nombre_completo' => $nombre_completo,
        'usuario' => $usuario,
        'contrasena' => $contrasena
    ]);
    header("Location: ../views/add_user.php");
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: ../views/add_user.php");
    } elseif ($action == 'edit') {
        // Aquí iría la lógica para editar un usuario, se puede agregar un formulario similar al de agregar usuario.
    }
}
?>
