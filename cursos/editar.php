<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$id_curso = (int)$_GET['id_curso'];
$resultado = pg_query($conexion, "SELECT * FROM curso WHERE id_curso = $id_curso");
$curso = pg_fetch_assoc($resultado);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_curso = pg_escape_string($conexion, $_POST['nombre_curso']);
    pg_query($conexion, "UPDATE curso SET nombre_curso='$nombre_curso' WHERE id_curso=$id_curso");
    header("Location: listar.php"); exit();
}

layout_header('Editar Curso', 'cursos', 1);
?>
<div class="page-header">
    <div>
        <h1>Editar Curso</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Cursos</a> › Editar</div>
    </div>
</div>
<div class="form-card">
    <div class="form-card-header"><h1>Datos del Curso</h1></div>
    <div class="form-card-body">
        <form method="POST">
            <div class="form-group">
                <label>ID Curso</label>
                <input type="number" value="<?= htmlspecialchars($curso['id_curso']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Nombre del Curso <span class="req">*</span></label>
                <input type="text" name="nombre_curso" value="<?= htmlspecialchars($curso['nombre_curso']) ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php layout_footer(); ?>
