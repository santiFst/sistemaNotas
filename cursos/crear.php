<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso     = (int)$_POST['id_curso'];
    $nombre_curso = pg_escape_string($conexion, $_POST['nombre_curso']);
    $cod_docente  = $_SESSION['docente'];
    pg_query($conexion, "INSERT INTO curso (id_curso, nombre_curso, cod_docente) VALUES ($id_curso, '$nombre_curso', $cod_docente)");
    header("Location: listar.php"); exit();
}

layout_header('Crear Curso', 'cursos', 1);
?>
<div class="page-header">
    <div>
        <h1>Nuevo Curso</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Cursos</a> › Nuevo</div>
    </div>
</div>
<div class="form-card">
    <div class="form-card-header"><h1>Datos del Curso</h1></div>
    <div class="form-card-body">
        <form method="POST">
            <div class="form-group">
                <label>ID Curso <span class="req">*</span></label>
                <input type="number" name="id_curso" required>
            </div>
            <div class="form-group">
                <label>Nombre del Curso <span class="req">*</span></label>
                <input type="text" name="nombre_curso" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-orange">Guardar Curso</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php layout_footer(); ?>
