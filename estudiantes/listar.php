<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$sql = "SELECT * FROM estudiante ORDER BY apellidos, nombres";
$resultado = pg_query($conexion, $sql);

// Mensaje de éxito tras redirección
$ok = $_GET['ok'] ?? null;

layout_header('Estudiantes', 'estudiantes', 1);
?>

<div class="page-header">
    <div>
        <h1>Estudiantes</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › Estudiantes</div>
    </div>
    <!-- Botón con dos acciones -->
    <div style="display:flex;gap:10px">
        <a href="crear.php" class="btn btn-orange">+ Nuevo Estudiante</a>
        <a href="crear.php#panel-csv" class="btn btn-secondary">📄 Importar CSV</a>
    </div>
</div>

<?php if ($ok === 'creado'): ?>
<div class="alert alert-success">✓ Estudiante registrado correctamente.</div>
<?php elseif ($ok === 'csv'): ?>
<div class="alert alert-success">✓ <?= (int)($_GET['n'] ?? 0) ?> estudiante(s) importados desde CSV.</div>
<?php endif; ?>

<div class="table-container">
    <div class="table-toolbar">
        <strong style="font-family:var(--font-main);font-size:.9rem;color:var(--moodle-blue)">Lista de Estudiantes</strong>
        <!-- Buscador rápido en tabla -->
        <input type="text" id="buscarEstudiante" placeholder="Buscar…"
               oninput="filtrarTabla(this.value)"
               style="width:200px;padding:6px 10px;font-size:.84rem">
    </div>
    <table id="tablaEstudiantes">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
            <tr>
                <td><span class="badge badge-blue"><?= htmlspecialchars($fila['cod_estudiante']) ?></span></td>
                <td><?= htmlspecialchars($fila['nombres']) ?></td>
                <td><?= htmlspecialchars($fila['apellidos']) ?></td>
                <td>
                    <div class="action-links">
                        <a href="editar.php?cod_estudiante=<?= $fila['cod_estudiante'] ?>" class="action-link action-edit">Editar</a>
                        <a href="eliminar.php?cod_estudiante=<?= $fila['cod_estudiante'] ?>" class="action-link action-delete"
                           onclick="return confirm('¿Eliminar a <?= htmlspecialchars(addslashes($fila['nombres'] . ' ' . $fila['apellidos'])) ?>?')">Eliminar</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function filtrarTabla(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#tablaEstudiantes tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

<?php layout_footer(); ?>
