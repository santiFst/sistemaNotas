<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_curso = $_GET['id_curso'];

$sql = "
    DELETE FROM curso
    WHERE id_curso = $id_curso
";

pg_query($conexion, $sql);

header("Location: listar.php");

exit();

?>