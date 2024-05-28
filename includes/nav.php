<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
        <img src="images/logo.png" alt="Gluttiere" width="30" height="30" class="d-inline-block align-top">
        Gluttiere
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="alta_receta.php">Alta Receta</a></li>
            <li class="nav-item"><a class="nav-link" href="entradas.php">Entradas</a></li>
            <li class="nav-item"><a class="nav-link" href="produccion.php">Producción</a></li>
            <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="controllers/logout.php">Logout</a></li>
        </ul>
    </div>
</nav>
<?php endif; ?>
