<?php include '../includes/header.php'; ?>
<div class="container">
    <h2 class="mt-5">Login</h2>
    <form action="../controllers/loginController.php" method="POST">
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
