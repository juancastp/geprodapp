<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nombre_producto_final'])) {
        $nombre_producto_final = $_POST['nombre_producto_final'];
        $ingredientes = $_POST['nombre_ingrediente'];

        $stmt = $pdo->prepare("INSERT INTO recetas (nombre_producto_final) VALUES (:nombre_producto_final)");
        $stmt->execute(['nombre_producto_final' => $nombre_producto_final]);
        $receta_id = $pdo->lastInsertId();

        foreach ($ingredientes as $ingrediente) {
            if (!empty($ingrediente)) {
                $stmt = $pdo->prepare("INSERT INTO ingredientes_recetas (receta_id, nombre_ingrediente) VALUES (:receta_id, :nombre_ingrediente)");
                $stmt->execute(['receta_id' => $receta_id, 'nombre_ingrediente' => $ingrediente]);
            }
        }
    } elseif (isset($_POST['receta_id']) && isset($_POST['nombre_ingrediente'])) {
        $receta_id = $_POST['receta_id'];
        $nombre_ingrediente = $_POST['nombre_ingrediente'];

        if (!empty($nombre_ingrediente)) {
            $stmt = $pdo->prepare("INSERT INTO ingredientes_recetas (receta_id, nombre_ingrediente) VALUES (:receta_id, :nombre_ingrediente)");
            $stmt->execute(['receta_id' => $receta_id, 'nombre_ingrediente' => $nombre_ingrediente]);
        }
    }
    header("Location: ../views/alta_receta.php");
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'getIngredients' && isset($_GET['receta_id'])) {
        $receta_id = $_GET['receta_id'];
        $stmt = $pdo->prepare("SELECT * FROM ingredientes_recetas WHERE receta_id = :receta_id");
        $stmt->execute(['receta_id' => $receta_id]);
        $ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ingredientes);
        exit;
    }

    if ($action == 'delete') {
        if (isset($_GET['receta_id'])) {
            $receta_id = $_GET['receta_id'];

            $stmt = $pdo->prepare("DELETE FROM recetas WHERE id = :receta_id");
            $stmt->execute(['receta_id' => $receta_id]);

            $stmt = $pdo->prepare("DELETE FROM ingredientes_recetas WHERE receta_id = :receta_id");
            $stmt->execute(['receta_id' => $receta_id]);
        } elseif (isset($_GET['ingrediente_id'])) {
            $ingrediente_id = $_GET['ingrediente_id'];

            $stmt = $pdo->prepare("DELETE FROM ingredientes_recetas WHERE id = :ingrediente_id");
            $stmt->execute(['ingrediente_id' => $ingrediente_id]);
        }
    } elseif ($action == 'edit' && isset($_GET['ingrediente_id'])) {
        $ingrediente_id = $_GET['ingrediente_id'];
        // Aquí puedes agregar la lógica para editar un ingrediente específico
    }

    if ($action == 'getIngredients' && isset($_GET['receta_id'])) {
        $receta_id = $_GET['receta_id'];
        $stmt = $pdo->prepare("SELECT * FROM ingredientes_recetas WHERE receta_id = :receta_id");
        $stmt->execute(['receta_id' => $receta_id]);
        $ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Verificación temporal
        if (!$ingredientes) {
            echo json_encode(['error' => 'No ingredients found']);
        } else {
            echo json_encode($ingredientes);
        }
        exit;
    }
     

    header("Location: ../views/alta_receta.php");
}
?>
