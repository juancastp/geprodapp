<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include '../includes/header.php';
?>

<div class="container">
    <h2 class="mt-5">Panel de Control</h2>
    <div class="row mt-3">
        <div class="col-md-3">
            <a href="alta_receta.php" class="btn btn-primary btn-block">Alta Receta</a>
        </div>
        <div class="col-md-3">
            <a href="entradas.php" class="btn btn-primary btn-block">Entradas</a>
        </div>
        <div class="col-md-3">
            <a href="produccion.php" class="btn btn-primary btn-block">Producción</a>
        </div>
        <div class="col-md-3">
            <a href="add_user.php" class="btn btn-primary btn-block">Usuarios</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
