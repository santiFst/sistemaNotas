<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
$id = (int)$_GET['id_curso'];
pg_query($conexion, "DELETE FROM curso WHERE id_curso = $id");
header("Location: listar.php"); exit();
