<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    $conn = new mysqli('localhost', 'saglu', 'W/qxFZpcDh4NIitn', 'geprodapp');
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($conn->real_escape_string($_POST['password']));
    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
    if ($result->num_rows === 1) {
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="images/cupcake.ico">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="images/logo.png" alt="Gluttire">
            <h1>Iniciar Sesión</h1>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
