<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config/config.php'; // Incluye la configuración de la base de datos

include '../includes/header.php';

// Obtener usuarios para el listado
$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll();
?>

<div class="container">
    <h2 class="mt-5">Gestión de Usuarios</h2>
    <form action="../controllers/userController.php" method="POST">
        <div class="form-group">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
        </div>
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
    </form>

    <h3 class="mt-5">Listado de Usuarios</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                    <td>
                        <a href="../controllers/userController.php?action=edit&id=<?= $usuario['id'] ?>" class="btn btn-warning">Editar</a>
                        <a href="../controllers/userController.php?action=delete&id=<?= $usuario['id'] ?>" class="btn btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
