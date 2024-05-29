<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include '../includes/header.php';
?>

<div class="container">
    <h2 class="mt-5">Entradas de Materia Prima</h2>
    <form action="../controllers/entradaController.php" method="POST">
        <div class="form-group">
            <label for="proveedor">Proveedor:</label>
            <input type="text" class="form-control" id="proveedor" name="proveedor" required>
        </div>
        <div class="form-group">
            <label for="referencia_entrada">Referencia de Entrada:</label>
            <input type="text" class="form-control" id="referencia_entrada" name="referencia_entrada" required>
        </div>
        <div class="form-group">
            <label for="articulo">Artículo:</label>
            <input type="text" class="form-control" id="articulo" name="articulo" required>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>
        <div class="form-group">
            <label for="peso">Peso:</label>
            <input type="number" step="0.01" class="form-control" id="peso" name="peso" required>
        </div>
        <div class="form-group">
            <label for="lote">Lote:</label>
            <input type="text" class="form-control" id="lote" name="lote" required>
        </div>
        <div class="form-group">
            <label for="fecha_entrada">Fecha de Entrada:</label>
            <input type="date" class="form-control" id="fecha_entrada" name="fecha_entrada" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Entrada</button>
    </form>

    <h3 class="mt-5">Buscar Entradas</h3>
    <form action="" method="GET">
        <div class="form-group">
            <label for="buscar_referencia">Referencia de Entrada:</label>
            <input type="text" class="form-control" id="buscar_referencia" name="buscar_referencia">
        </div>
        <div class="form-group">
            <label for="buscar_proveedor">Proveedor:</label>
            <input type="text" class="form-control" id="buscar_proveedor" name="buscar_proveedor">
        </div>
        <div class="form-group">
            <label for="buscar_lote">Lote:</label>
            <input type="text" class="form-control" id="buscar_lote" name="buscar_lote">
        </div>
        <div class="form-group">
            <label for="buscar_fecha_inicio">Fecha Inicio:</label>
            <input type="date" class="form-control" id="buscar_fecha_inicio" name="buscar_fecha_inicio">
        </div>
        <div class="form-group">
            <label for="buscar_fecha_fin">Fecha Fin:</label>
            <input type="date" class="form-control" id="buscar_fecha_fin" name="buscar_fecha_fin">
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
