<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_evaluacion = $_GET['id_evaluacion'];

$sql = "
    DELETE FROM evaluacion
    WHERE id_evaluacion = $id_evaluacion
";

$resultado = pg_query($conexion, $sql);

/*
|--------------------------------------------------------------------------
| VALIDAR ERROR
|--------------------------------------------------------------------------
*/

if (!$resultado) {

    echo pg_last_error($conexion);
    exit();
}

header("Location: listar.php");

exit();

?>