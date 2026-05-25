<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$id_inscripcion = (int)$_GET['id_inscripcion'];
$resultado      = pg_query($conexion, "SELECT * FROM inscripcion WHERE id_inscripcion = $id_inscripcion");
$inscripcion    = pg_fetch_assoc($resultado);
$estudiantes    = pg_query($conexion, "SELECT * FROM estudiante ORDER BY apellidos");
$cursos         = pg_query($conexion, "SELECT * FROM curso ORDER BY nombre_curso");
$error          = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_estudiante = (int)$_POST['cod_estudiante'];
    $id_curso       = (int)$_POST['id_curso'];

    $res = pg_query($conexion, "UPDATE inscripcion SET cod_estudiante=$cod_estudiante, id_curso=$id_curso WHERE id_inscripcion=$id_inscripcion");

    if (!$res) {
        $error = pg_last_error($conexion);
    } else {
        header("Location: listar.php"); exit();
    }
}

layout_header('Editar Inscripción', 'inscripciones', 1);
?>
<div class="page-header">
    <div>
        <h1>Editar Inscripción</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Inscripciones</a> › Editar</div>
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
                    <option value="<?= $e['cod_estudiante'] ?>" <?= $e['cod_estudiante'] == $inscripcion['cod_estudiante'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['apellidos'] . ', ' . $e['nombres']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Curso <span class="req">*</span></label>
                <select name="id_curso" required>
                    <?php while ($c = pg_fetch_assoc($cursos)): ?>
                    <option value="<?= $c['id_curso'] ?>" <?= $c['id_curso'] == $inscripcion['id_curso'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombre_curso']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php layout_footer(); ?>
