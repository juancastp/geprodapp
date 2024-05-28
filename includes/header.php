<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
            }
        }
    </style>
    <title>Production Management</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">