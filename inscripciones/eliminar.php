<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_inscripcion = $_GET['id_inscripcion'];

$sql = "
    DELETE FROM inscripcion
    WHERE id_inscripcion = $id_inscripcion
";

$resultado = pg_query($conexion, $sql);

if (!$resultado) {

    echo pg_last_error($conexion);
    exit();
}

header("Location: listar.php");

exit();

?>