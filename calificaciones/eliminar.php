<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_calificacion = $_GET['id_calificacion'];

$sql = "
    DELETE FROM calificacion
    WHERE id_calificacion = $id_calificacion
";

$resultado = pg_query($conexion, $sql);

if (!$resultado) {

    echo pg_last_error($conexion);
    exit();
}

header("Location: listar.php");

exit();

?>