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
    <link rel="stylesheet" href="assets/css/styles.css">

    <meta charset="UTF-8">

    <title>
        Sistema de Notas
    </title>

</head>

<body>

<div class="contenedor">

    <h1>
        Sistema de Gestión de Notas
    </h1>

    <p>
        Bienvenido al sistema
    </p>

    <hr>

    <h3>Menú Principal</h3>

    <ul>

        <li>
            <a href="estudiantes/listar.php">
                Estudiantes
            </a>
        </li>

        <li>
            <a href="cursos/listar.php">
                Cursos
            </a>
        </li>

        <li>
            <a href="evaluaciones/listar.php">
                Evaluaciones
            </a>
        </li>

        <li>
            <a href="inscripciones/listar.php">
                Inscripciones
            </a>
        </li>

        <li>
            <a href="calificaciones/listar.php">
                Calificaciones
            </a>
        </li>

        <li>
            <a href="definitivas/listar.php">
                Definitivas
            </a>
        </li>

    </ul>

    <hr>

    <a href="login/logout.php">
        Cerrar sesión
    </a>

</div>

</body>

</html>