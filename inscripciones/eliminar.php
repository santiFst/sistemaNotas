<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$id  = (int)$_GET['id_inscripcion'];
$res = pg_query($conexion, "DELETE FROM inscripcion WHERE id_inscripcion = $id");

if (!$res) {
    layout_header('Error', 'inscripciones', 1);
    echo '<div class="alert alert-danger">' . htmlspecialchars(pg_last_error($conexion)) . '</div>';
    echo '<a href="listar.php" class="btn btn-secondary">← Volver</a>';
    layout_footer();
    exit();
}
header("Location: listar.php"); exit();
