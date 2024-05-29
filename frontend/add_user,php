<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');
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
<head>
    <title>Usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" width="30" height="30" alt="">
            Gluttire
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="alta_receta.php">Alta Recetas</a></li>
                <li class="nav-item"><a class="nav-link" href="entradas.php">Entradas</a></li>
                <li class="nav-item"><a class="nav-link" href="produccion.php">Producción</a></li>
                <li class="nav-item"><a class="nav-link" href="add_user.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="informes.php">Informes</a></li>
            </ul>
        </div>
    </nav>

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
</body>
</html>
