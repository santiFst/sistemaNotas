<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$sql = "SELECT c.id_calificacion, e.nombres, e.apellidos, cu.nombre_curso,
               ev.descripcion, ev.porcentaje, c.valor
        FROM calificacion c
        INNER JOIN inscripcion i  ON c.id_inscripcion = i.id_inscripcion
        INNER JOIN estudiante e   ON i.cod_estudiante  = e.cod_estudiante
        INNER JOIN curso cu       ON i.id_curso        = cu.id_curso
        INNER JOIN evaluacion ev  ON c.id_evaluacion   = ev.id_evaluacion
        ORDER BY cu.nombre_curso, e.apellidos, ev.posicion";
$resultado = pg_query($conexion, $sql);

$ok = $_GET['ok'] ?? null;

layout_header('Calificaciones', 'calificaciones', 1);
?>

<?php if ($ok === 'calificacion'): ?>
<div class="alert alert-success">&#x2713; Calificacion registrada correctamente.</div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Calificaciones</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> &rsaquo; Calificaciones</div>
    </div>
    <a href="crear.php" class="btn btn-orange">+ Nueva Calificacion</a>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <strong style="font-family:var(--font-main);font-size:.9rem;color:var(--moodle-blue)">Lista de Calificaciones</strong>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Curso</th>
                <th>Evaluacion</th>
                <th>Porcentaje</th>
                <th>Valor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
            <?php
                $valor = (float)$fila['valor'];
                $badgeClass = $valor >= 3.0 ? 'badge-green' : 'badge-orange';
            ?>
            <tr>
                <td><span class="badge badge-blue"><?= htmlspecialchars($fila['id_calificacion']) ?></span></td>
                <td><?= htmlspecialchars($fila['nombres'] . ' ' . $fila['apellidos']) ?></td>
                <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                <td><span class="badge badge-orange"><?= htmlspecialchars($fila['porcentaje']) ?>%</span></td>
                <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($fila['valor']) ?></span></td>
                <td>
                    <div class="action-links">
                        <a href="editar.php?id_calificacion=<?= $fila['id_calificacion'] ?>" class="action-link action-edit">Editar</a>
                        <a href="eliminar.php?id_calificacion=<?= $fila['id_calificacion'] ?>" class="action-link action-delete"
                           onclick="return confirm('Eliminar esta calificacion?')">Eliminar</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php layout_footer(); ?>
