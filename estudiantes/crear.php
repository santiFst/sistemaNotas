<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {

        $file = $_FILES['csv_file']['tmp_name'];

        if (($handle = fopen($file, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                if (count($data) >= 3) {

                    $cod_estudiante = pg_escape_string($conexion, trim($data[0]));

                    $nombres = pg_escape_string($conexion, trim($data[1]));

                    $apellidos = pg_escape_string($conexion, trim($data[2]));

                    $sql = "INSERT INTO estudiante (cod_estudiante, nombres, apellidos) VALUES ('$cod_estudiante', '$nombres', '$apellidos')";

                    pg_query($conexion, $sql);

                }

            }

            fclose($handle);

        }

    }

    header("Location: listar.php");

    exit();

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <link rel="stylesheet" href="../assets/css/styles.css">

    <meta charset="UTF-8">

    <title>Cargar Estudiantes</title>

</head>

<body>

    <h1>Cargar Estudiantes desde CSV</h1>

    <form method="POST" enctype="multipart/form-data">

        <label>Archivo CSV:</label>

        <br>

        <input type="file" name="csv_file" accept=".csv" required>

        <br><br>

        <button type="submit">Cargar Estudiantes</button>

    </form>

</body>

</html>
















<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cod_estudiante = $_POST['cod_estudiante'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];

    $sql = "
        INSERT INTO estudiante (
            cod_estudiante,
            nombres,
            apellidos,
            correo
        )
        VALUES (
            '$cod_estudiante',
            '$nombres',
            '$apellidos',
            '$correo'
        )
    ";

    pg_query($conexion, $sql);

    header("Location: listar.php");

    exit();
}
