<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$cod_estudiante = $_GET['cod_estudiante'];

$sql = "
    DELETE FROM estudiante
    WHERE cod_estudiante = $cod_estudiante
";

pg_query($conexion, $sql);

header("Location: listar.php");

exit();

?>