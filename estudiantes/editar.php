<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$cod_estudiante = $_GET['cod_estudiante'];
$resultado = pg_query($conexion, "SELECT * FROM estudiante WHERE cod_estudiante = $cod_estudiante");
$estudiante = pg_fetch_assoc($resultado);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombres   = pg_escape_string($conexion, $_POST['nombres']);
    $apellidos = pg_escape_string($conexion, $_POST['apellidos']);
    pg_query($conexion, "UPDATE estudiante SET nombres='$nombres', apellidos='$apellidos' WHERE cod_estudiante=$cod_estudiante");
    header("Location: listar.php"); exit();
}

layout_header('Editar Estudiante', 'estudiantes', 1);
?>
<div class="page-header">
    <div>
        <h1>Editar Estudiante</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Estudiantes</a> › Editar</div>
    </div>
</div>
<div class="form-card">
    <div class="form-card-header"><h1>Datos del Estudiante</h1></div>
    <div class="form-card-body">
        <form method="POST">
            <div class="form-group">
                <label>Código</label>
                <input type="number" value="<?= htmlspecialchars($estudiante['cod_estudiante']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Nombres <span class="req">*</span></label>
                <input type="text" name="nombres" value="<?= htmlspecialchars($estudiante['nombres']) ?>" required>
            </div>
            <div class="form-group">
                <label>Apellidos <span class="req">*</span></label>
                <input type="text" name="apellidos" value="<?= htmlspecialchars($estudiante['apellidos']) ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php layout_footer(); ?>
