<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$sql = "SELECT e.id_evaluacion, e.descripcion, e.porcentaje, e.posicion, c.nombre_curso
        FROM evaluacion e INNER JOIN curso c ON e.id_curso = c.id_curso
        ORDER BY c.nombre_curso, e.posicion";
$resultado = pg_query($conexion, $sql);

layout_header('Evaluaciones', 'evaluaciones', 1);
?>

<div class="page-header">
    <div>
        <h1>Evaluaciones</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › Evaluaciones</div>
    </div>
    <a href="crear.php" class="btn btn-orange">+ Nueva Evaluación</a>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <strong style="font-family:var(--font-main);font-size:.9rem;color:var(--moodle-blue)">Lista de Evaluaciones</strong>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Curso</th>
                <th>Descripción</th>
                <th>Porcentaje</th>
                <th>Posición</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
            <tr>
                <td><span class="badge badge-blue"><?= htmlspecialchars($fila['id_evaluacion']) ?></span></td>
                <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                <td><span class="badge badge-orange"><?= htmlspecialchars($fila['porcentaje']) ?>%</span></td>
                <td><?= htmlspecialchars($fila['posicion']) ?></td>
                <td>
                    <div class="action-links">
                        <a href="editar.php?id_evaluacion=<?= $fila['id_evaluacion'] ?>" class="action-link action-edit">Editar</a>
                        <a href="eliminar.php?id_evaluacion=<?= $fila['id_evaluacion'] ?>" class="action-link action-delete"
                           onclick="return confirm('¿Eliminar esta evaluación?')">Eliminar</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php layout_footer(); ?>
