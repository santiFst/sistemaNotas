<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: login/login.php");

    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sistema de Notas</title>
</head>

<body>

    <h1>Bienvenido al Sistema</h1>

    <a href="login/logout.php">
        Cerrar Sesión
    </a>

</body>

</html>