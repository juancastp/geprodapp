<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Gluttiere</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <?php if ($loggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../views/alta_receta.php">Alta Receta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../views/entradas.php">Entradas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../views/produccion.php">Producción</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../views/add_user.php">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../views/listproduccion.php">Informes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../controllers/logout.php">Logout</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>