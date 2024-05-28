<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config/config.php';
include '../includes/header.php';
?>

<div class="container">
    <h2 class="mt-5">Entradas</h2>
    <form action="../controllers/entradasController.php" method="POST" class="form-horizontal">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="proveedor">Proveedor:</label>
                    <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ref_entrada">Ref de Entrada:</label>
                    <input type="text" class="form-control" id="ref_entrada" name="ref_entrada" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="peso">Peso:</label>
                    <input type="text" class="form-control" id="peso" name="peso" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lote">Lote:</label>
                    <input type="text" class="form-control" id="lote" name="lote" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="articulo">Artículo:</label>
            <input type="text" class="form-control" id="articulo" name="articulo" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Entrada</button>
    </form>

    <!-- Filtros -->
    <div class="filters mt-5">
        <div class="form-group">
            <label for="filter_proveedor">Proveedor:</label>
            <input type="text" class="form-control" id="filter_proveedor">
        </div>
        <div class="form-group">
            <label for="filter_ref_entrada">Ref de Entrada:</label>
            <input type="text" class="form-control" id="filter_ref_entrada">
        </div>
        <div class="form-group">
            <label for="filter_lote">Lote:</label>
            <input type="text" class="form-control" id="filter_lote">
        </div>
        <div class="form-group">
            <label for="filter_fecha_inicio">Fecha Inicio:</label>
            <input type="date" class="form-control" id="filter_fecha_inicio">
        </div>
        <div class="form-group">
            <label for="filter_fecha_fin">Fecha Fin:</label>
            <input type="date" class="form-control" id="filter_fecha_fin">
        </div>
        <button class="btn btn-primary" onclick="applyFilters()">Aplicar filtros</button>
    </div>

    <!-- Listado de entradas -->
    <h3 class="mt-5">Listado de Entradas</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Proveedor</th>
                <th>Ref de Entrada</th>
                <th>Fecha</th>
                <th>Cantidad</th>
                <th>Peso</th>
                <th>Lote</th>
                <th>Artículo</th>
            </tr>
        </thead>
        <tbody id="entradas-list">
            <!-- Aquí se llenarán las entradas -->
        </tbody>
    </table>
</div>

<script>
function applyFilters() {
    // Implementación de los filtros
}
</script>

<?php include '../includes/footer.php'; ?>
