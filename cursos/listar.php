<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

//$sql = "SELECT * FROM curso ORDER BY id_curso";
$sql = "SELECT c.id_curso, c.nombre_curso, c.cod_docente, d.nombres, d.apellidos
        FROM curso c
        JOIN docente d ON c.cod_docente = d.cod_docente
        ORDER BY c.id_curso";
$resultado = pg_query($conexion, $sql);

layout_header('Cursos', 'cursos', 1);
?>

<div class="page-header">
    <div>
        <h1>Cursos</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › Cursos</div>
    </div>
    <a href="crear.php" class="btn btn-orange">+ Nuevo Curso</a>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <strong style="font-family:var(--font-main);font-size:.9rem;color:var(--moodle-blue)">Lista de Cursos</strong>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Curso</th>
                <th>Docente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
            <tr>
                <td><span class="badge badge-blue"><?= htmlspecialchars($fila['id_curso']) ?></span></td>
                <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                <td><?= htmlspecialchars($fila['nombres'] . ' ' . $fila['apellidos']) ?></td>
                <td>
                    <div class="action-links">
                        <a href="editar.php?id_curso=<?= $fila['id_curso'] ?>" class="action-link action-edit">Editar</a>
                        <a href="eliminar.php?id_curso=<?= $fila['id_curso'] ?>" class="action-link action-delete"
                           onclick="return confirm('¿Eliminar este curso?')">Eliminar</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php layout_footer(); ?>
