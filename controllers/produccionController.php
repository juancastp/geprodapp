<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receta_id = $_POST['receta_id'];
    $lote_produccion = $_POST['lote_produccion'];
    $cantidad = $_POST['cantidad'];
    $fecha_produccion = date('Y-m-d');
    $lotes_ingredientes = $_POST['lote_ingrediente'];

    try {
        // Insertar producción
        $stmt = $pdo->prepare("INSERT INTO produccion (receta_id, lote_produccion, fecha_produccion, cantidad) VALUES (:receta_id, :lote_produccion, :fecha_produccion, :cantidad)");
        $stmt->execute([
            'receta_id' => $receta_id,
            'lote_produccion' => $lote_produccion,
            'fecha_produccion' => $fecha_produccion,
            'cantidad' => $cantidad
        ]);
        
        // Obtener el id de la producción recién insertada
        $produccion_id = $pdo->lastInsertId();

        // Insertar lotes de ingredientes usados
        foreach ($lotes_ingredientes as $ingrediente_id => $lote) {
            if (!empty($lote)) {
                $stmt = $pdo->prepare("INSERT INTO lotes_ingredientes_usados (produccion_id, ingrediente_id, lote) VALUES (:produccion_id, :ingrediente_id, :lote)");
                $stmt->execute([
                    'produccion_id' => $produccion_id,
                    'ingrediente_id' => $ingrediente_id,
                    'lote' => $lote
                ]);
            }
        }

        header("Location: ../views/produccion.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
