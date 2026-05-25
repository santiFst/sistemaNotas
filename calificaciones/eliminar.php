<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$id  = (int)$_GET['id_calificacion'];
$res = pg_query($conexion, "DELETE FROM calificacion WHERE id_calificacion = $id");

if (!$res) {
    layout_header('Error', 'calificaciones', 1);
    echo '<div class="alert alert-danger">' . htmlspecialchars(pg_last_error($conexion)) . '</div>';
    echo '<a href="listar.php" class="btn btn-secondary">← Volver</a>';
    layout_footer();
    exit();
}
header("Location: listar.php"); exit();
