<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
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
</body>
</html>
