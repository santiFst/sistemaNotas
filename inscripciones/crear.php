<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$estudiantes = pg_query($conexion, "SELECT * FROM estudiante ORDER BY apellidos");
$cursos      = pg_query($conexion, "SELECT * FROM curso ORDER BY nombre_curso");
$error       = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_estudiante = (int)$_POST['cod_estudiante'];
    $id_curso       = (int)$_POST['id_curso'];
    $anio           = (int)$_POST['anio'];
    $periodo        = (int)$_POST['periodo'];

    $resultado = pg_query($conexion, "INSERT INTO inscripcion (cod_estudiante, id_curso, anio, periodo) VALUES ($cod_estudiante, $id_curso, $anio, $periodo)");

    if (!$resultado) {
        $err   = pg_last_error($conexion);
        $error = strpos($err, 'llave duplicada') !== false
            ? 'Esta inscripción ya existe para ese estudiante, curso, año y período.'
            : $err;
    } else {
        header("Location: listar.php"); exit();
    }
}

layout_header('Nueva Inscripción', 'inscripciones', 1);
?>
<div class="page-header">
    <div>
        <h1>Nueva Inscripción</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Inscripciones</a> › Nueva</div>
    </div>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="form-card">
    <div class="form-card-header"><h1>Datos de la Inscripción</h1></div>
    <div class="form-card-body">
        <form method="POST">
            <div class="form-group">
                <label>Estudiante <span class="req">*</span></label>
                <select name="cod_estudiante" required>
                    <?php while ($e = pg_fetch_assoc($estudiantes)): ?>
                    <option value="<?= $e['cod_estudiante'] ?>"><?= htmlspecialchars($e['apellidos'] . ', ' . $e['nombres']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Curso <span class="req">*</span></label>
                <select name="id_curso" required>
                    <?php while ($c = pg_fetch_assoc($cursos)): ?>
                    <option value="<?= $c['id_curso'] ?>"><?= htmlspecialchars($c['nombre_curso']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Año <span class="req">*</span></label>
                <input type="number" name="anio" min="2000" max="2100" value="<?= date('Y') ?>" required>
            </div>
            <div class="form-group">
                <label>Período <span class="req">*</span></label>
                <select name="periodo" required>
                    <option value="1">Período I</option>
                    <option value="2">Período II</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-orange">Guardar Inscripción</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php layout_footer(); ?>
