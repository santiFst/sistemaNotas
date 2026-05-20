<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_curso = $_POST['id_curso'];
    $nombre_curso = $_POST['nombre_curso'];

    $cod_docente = $_SESSION['docente'];

    $sql = "
        INSERT INTO curso (
            id_curso,
            nombre_curso,
            cod_docente
        )
        VALUES (
            $id_curso,
            '$nombre_curso',
            $cod_docente
        )
    ";

    pg_query($conexion, $sql);

    header("Location: listar.php");

    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Crear Curso</title>
</head>

<body>

    <h1>Crear Curso</h1>

    <form method="POST">

        <label>ID Curso:</label>
        <br>

        <input
            type="number"
            name="id_curso"
            required>

        <br><br>

        <label>Nombre Curso:</label>
        <br>

        <input
            type="text"
            name="nombre_curso"
            required>

        <br><br>

        <button type="submit">
            Guardar
        </button>

    </form>

</body>

</html>