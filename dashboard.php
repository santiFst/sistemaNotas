<?php
session_start();
if (!isset($_SESSION['docente'])) {
    header("Location: login/login.php");
    exit();
}
require_once(__DIR__ . '/assets/layout.php');
layout_header('Dashboard', 'dashboard', 0);
?>

<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <div class="breadcrumb">Sistema de Gestión de Notas</div>
    </div>
</div>

<div class="menu-grid">
    <a href="estudiantes/listar.php" class="menu-card">
        <div class="menu-card-icon">👩‍🎓</div>
        <div class="menu-card-title">Estudiantes</div>
        <div class="menu-card-desc">Gestionar el listado de estudiantes registrados</div>
    </a>
    <a href="cursos/listar.php" class="menu-card">
        <div class="menu-card-icon">📚</div>
        <div class="menu-card-title">Cursos</div>
        <div class="menu-card-desc">Administrar cursos y asignaturas</div>
    </a>
    <a href="evaluaciones/listar.php" class="menu-card">
        <div class="menu-card-icon">📝</div>
        <div class="menu-card-title">Evaluaciones</div>
        <div class="menu-card-desc">Configurar evaluaciones y porcentajes</div>
    </a>
    <a href="inscripciones/listar.php" class="menu-card">
        <div class="menu-card-icon">📋</div>
        <div class="menu-card-title">Inscripciones</div>
        <div class="menu-card-desc">Gestionar inscripciones de estudiantes a cursos</div>
    </a>
    <a href="calificaciones/listar.php" class="menu-card">
        <div class="menu-card-icon">🔢</div>
        <div class="menu-card-title">Calificaciones</div>
        <div class="menu-card-desc">Registrar y editar notas por evaluación</div>
    </a>
    <a href="definitivas/listar.php" class="menu-card">
        <div class="menu-card-icon">🏆</div>
        <div class="menu-card-title">Definitivas</div>
        <div class="menu-card-desc">Consultar las notas finales de los estudiantes</div>
    </a>
</div>

<?php layout_footer(); ?>
