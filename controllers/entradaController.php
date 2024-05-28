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

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $proveedor = $_POST['proveedor'];
    $referencia_entrada = $_POST['referencia_entrada'];
    $articulo = $_POST['articulo'];
    $cantidad = $_POST['cantidad'];
    $peso = $_POST['peso'];
    $lote = $_POST['lote'];
    $fecha_entrada = $_POST['fecha_entrada'];
    $usuario_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO entradas (proveedor, referencia_entrada, articulo, cantidad, peso, lote, fecha_entrada, usuario_id) VALUES (:proveedor, :referencia_entrada, :articulo, :cantidad, :peso, :lote, :fecha_entrada, :usuario_id)");
    $stmt->execute([
        'proveedor' => $proveedor,
        'referencia_entrada' => $referencia_entrada,
        'articulo' => $articulo,
        'cantidad' => $cantidad,
        'peso' => $peso,
        'lote' => $lote,
        'fecha_entrada' => $fecha_entrada,
        'usuario_id' => $usuario_id
    ]);
    header("Location: ../views/entradas.php");
}
?>
