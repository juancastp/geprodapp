<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $proveedor = $_POST['proveedor'];
    $referencia_entrada = $_POST['referencia_entrada'];
    $articulo = $_POST['articulo'];
    $cantidad = $_POST['cantidad'];
    $peso = $_POST['peso'];
    $lote = $_POST['lote'];
    $fecha_entrada = $_POST['fecha_entrada'];

    $stmt = $pdo->prepare("INSERT INTO entradas (proveedor, referencia_entrada, articulo, cantidad, peso, lote, fecha_entrada) VALUES (:proveedor, :referencia_entrada, :articulo, :cantidad, :peso, :lote, :fecha_entrada)");
    $stmt->execute([
        'proveedor' => $proveedor,
        'referencia_entrada' => $referencia_entrada,
        'articulo' => $articulo,
        'cantidad' => $cantidad,
        'peso' => $peso,
        'lote' => $lote,
        'fecha_entrada' => $fecha_entrada
    ]);
    header("Location: ../views/entradas.php");
}
?>
