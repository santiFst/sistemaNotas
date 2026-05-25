<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$cursos = pg_query($conexion, "SELECT * FROM curso ORDER BY nombre_curso");
$error  = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso    = (int)$_POST['id_curso'];
    $descripcion = pg_escape_string($conexion, $_POST['descripcion']);
    $porcentaje  = (float)$_POST['porcentaje'];
    $posicion    = (int)$_POST['posicion'];

    $resultado = pg_query($conexion, "INSERT INTO evaluacion (id_curso, descripcion, porcentaje, posicion) VALUES ($id_curso, '$descripcion', $porcentaje, $posicion)");

    if (!$resultado) {
        $err = pg_last_error($conexion);
        $error = strpos($err, 'La suma de porcentajes supera el 100') !== false
            ? 'La suma de porcentajes del curso no puede superar el 100%.'
            : $err;
    } else {
        header("Location: listar.php"); exit();
    }
}

layout_header('Crear Evaluación', 'evaluaciones', 1);
?>
<div class="page-header">
    <div>
        <h1>Nueva Evaluación</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Evaluaciones</a> › Nueva</div>
    </div>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="form-card">
    <div class="form-card-header"><h1>Datos de la Evaluación</h1></div>
    <div class="form-card-body">
        <form method="POST">
            <div class="form-group">
                <label>Curso <span class="req">*</span></label>
                <select name="id_curso" required>
                    <?php while ($c = pg_fetch_assoc($cursos)): ?>
                    <option value="<?= $c['id_curso'] ?>"><?= htmlspecialchars($c['nombre_curso']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Descripción <span class="req">*</span></label>
                <input type="text" name="descripcion" required>
            </div>
            <div class="form-group">
                <label>Porcentaje (%) <span class="req">*</span></label>
                <input type="number" step="0.01" name="porcentaje" min="0" max="100" required>
            </div>
            <div class="form-group">
                <label>Posición <span class="req">*</span></label>
                <input type="number" name="posicion" min="1" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-orange">Guardar Evaluación</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php layout_footer(); ?>
