<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$sql = "SELECT i.id_inscripcion, e.cod_estudiante, e.nombres, e.apellidos, c.nombre_curso
        FROM inscripcion i
        INNER JOIN estudiante e ON i.cod_estudiante = e.cod_estudiante
        INNER JOIN curso c ON i.id_curso = c.id_curso
        ORDER BY c.nombre_curso, e.apellidos";
$resultado = pg_query($conexion, $sql);

layout_header('Inscripciones', 'inscripciones', 1);
?>

<div class="page-header">
    <div>
        <h1>Inscripciones</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › Inscripciones</div>
    </div>
    <a href="crear.php" class="btn btn-orange">+ Nueva Inscripción</a>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <strong style="font-family:var(--font-main);font-size:.9rem;color:var(--moodle-blue)">Lista de Inscripciones</strong>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Estudiante</th>
                <th>Curso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
            <tr>
                <td><span class="badge badge-blue"><?= htmlspecialchars($fila['id_inscripcion']) ?></span></td>
                <td><?= htmlspecialchars($fila['cod_estudiante']) ?></td>
                <td><?= htmlspecialchars($fila['nombres'] . ' ' . $fila['apellidos']) ?></td>
                <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                <td>
                    <div class="action-links">
                        <a href="editar.php?id_inscripcion=<?= $fila['id_inscripcion'] ?>" class="action-link action-edit">Editar</a>
                        <a href="eliminar.php?id_inscripcion=<?= $fila['id_inscripcion'] ?>" class="action-link action-delete"
                           onclick="return confirm('¿Eliminar esta inscripción?')">Eliminar</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php layout_footer(); ?>
