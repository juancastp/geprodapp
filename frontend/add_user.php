<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'includes/db_config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($conn->real_escape_string($_POST['password']));
    $conn->query("INSERT INTO users (full_name, username, password) VALUES ('$full_name', '$username', '$password')");
}
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<?php
include 'includes/header.php';
?>
<body>
    <?php
    include 'includes/nav.php';
    ?>
    <div class="container">
        <h1>Usuarios</h1>
        <form method="post">
            <div class="form-group">
                <label for="full_name">Nombre completo:</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>

        <h2>Listado de Usuarios</h2>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Nombre Completo</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td>
                            <button class="btn btn-warning">Editar</button>
                            <button class="btn btn-danger">Eliminar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    include 'includes/footer.php';
    ?>
</body>
</html>
