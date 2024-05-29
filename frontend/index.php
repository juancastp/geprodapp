<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Index</title>
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
                <li class="nav-item"><a class="nav-link" href="alta_receta.php">Alta Recetas</a></li>
                <li class="nav-item"><a class="nav-link" href="entradas.php">Entradas</a></li>
                <li class="nav-item"><a class="nav-link" href="produccion.php">Producción</a></li>
                <li class="nav-item"><a class="nav-link" href="add_user.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="informes.php">Informes</a></li>
            </ul>
        </div>
    </nav>
</body>
</html>
