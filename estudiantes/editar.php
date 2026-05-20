<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$cod_estudiante = $_GET['cod_estudiante'];

$sql = "
    SELECT *
    FROM estudiante
    WHERE cod_estudiante = $cod_estudiante
";

$resultado = pg_query($conexion, $sql);

$estudiante = pg_fetch_assoc($resultado);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];

    $sql_update = "
        UPDATE estudiante
        SET
            nombres = '$nombres',
            apellidos = '$apellidos'
        WHERE cod_estudiante = $cod_estudiante
    ";

    pg_query($conexion, $sql_update);

    header("Location: listar.php");

    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Editar Estudiante</title>
</head>

<body>

    <h1>Editar Estudiante</h1>

    <form method="POST">

        <label>Código:</label>
        <br>

        <input
            type="number"
            value="<?php echo $estudiante['cod_estudiante']; ?>"
            disabled>

        <br><br>

        <label>Nombres:</label>
        <br>

        <input
            type="text"
            name="nombres"
            value="<?php echo $estudiante['nombres']; ?>"
            required>

        <br><br>

        <label>Apellidos:</label>
        <br>

        <input
            type="text"
            name="apellidos"
            value="<?php echo $estudiante['apellidos']; ?>"
            required>

        <br><br>

        <button type="submit">
            Actualizar
        </button>

    </form>

</body>

</html>