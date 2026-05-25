<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
$cod = (int)$_GET['cod_estudiante'];
pg_query($conexion, "DELETE FROM estudiante WHERE cod_estudiante = $cod");
header("Location: listar.php"); exit();
