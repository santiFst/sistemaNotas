<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$id_calificacion = (int)$_GET['id_calificacion'];

// Datos actuales de la calificación, con curso
$res = pg_query($conexion,
    "SELECT cal.*, i.id_curso, i.cod_estudiante,
            e.nombres, e.apellidos, e.cod_estudiante AS cod_e,
            cu.nombre_curso, ev.descripcion AS desc_eval, ev.porcentaje
     FROM calificacion cal
     INNER JOIN inscripcion i  ON cal.id_inscripcion = i.id_inscripcion
     INNER JOIN estudiante  e  ON i.cod_estudiante   = e.cod_estudiante
     INNER JOIN curso       cu ON i.id_curso         = cu.id_curso
     INNER JOIN evaluacion  ev ON cal.id_evaluacion  = ev.id_evaluacion
     WHERE cal.id_calificacion = $id_calificacion");

if (!$res || pg_num_rows($res) === 0) {
    header("Location: listar.php"); exit();
}
$cal = pg_fetch_assoc($res);

// Evaluaciones del mismo curso (para cambiar eval si se desea)
$evRes = pg_query($conexion,
    "SELECT id_evaluacion, descripcion, porcentaje, posicion
     FROM evaluacion WHERE id_curso = {$cal['id_curso']} ORDER BY posicion");
$evaluaciones = [];
while ($row = pg_fetch_assoc($evRes)) $evaluaciones[] = $row;

// Estudiantes inscritos en el mismo curso (para TomSelect)
$estRes = pg_query($conexion,
    "SELECT i.id_inscripcion, e.cod_estudiante, e.nombres, e.apellidos
     FROM inscripcion i
     INNER JOIN estudiante e ON i.cod_estudiante = e.cod_estudiante
     WHERE i.id_curso = {$cal['id_curso']}
     ORDER BY e.apellidos, e.nombres");
$estudiantes = [];
while ($row = pg_fetch_assoc($estRes)) $estudiantes[] = $row;

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_inscripcion = (int)$_POST['id_inscripcion'];
    $id_evaluacion  = (int)$_POST['id_evaluacion'];
    $valor          = (float)str_replace(',', '.', $_POST['valor']);

    $check = pg_query($conexion,
        "SELECT COUNT(*) FROM inscripcion i
         INNER JOIN evaluacion ev ON i.id_curso = ev.id_curso
         WHERE i.id_inscripcion = $id_inscripcion AND ev.id_evaluacion = $id_evaluacion");
    $row = pg_fetch_row($check);

    if ($row[0] == 0) {
        $error = 'La evaluación no pertenece al mismo curso de la inscripción.';
    } else {
        $upd = pg_query($conexion,
            "UPDATE calificacion
             SET id_inscripcion=$id_inscripcion, id_evaluacion=$id_evaluacion, valor=$valor
             WHERE id_calificacion=$id_calificacion");
        if (!$upd) { $error = pg_last_error($conexion); }
        else { header("Location: listar.php"); exit(); }
    }
}

layout_header('Editar Calificación', 'calificaciones', 1);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/css/tom-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/js/tom-select.complete.min.js"></script>

<div class="page-header">
    <div>
        <h1>Editar Calificación</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Calificaciones</a> › Editar</div>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Info del contexto -->
<div class="alert alert-info" style="margin-bottom:20px">
    📚 Curso: <strong><?= htmlspecialchars($cal['nombre_curso']) ?></strong>
    &nbsp;&bull;&nbsp; Calificación #<?= $id_calificacion ?>
</div>

<div class="form-card" style="max-width:560px">
    <div class="form-card-header"><h1>Modificar Calificación</h1></div>
    <div class="form-card-body">
        <form method="POST">
            <!-- id_inscripcion se gestiona por TomSelect -->
            <input type="hidden" name="id_inscripcion" id="hidInscripcion" value="<?= $cal['id_inscripcion'] ?>">

            <div class="form-group">
                <label>Estudiante <span class="req">*</span></label>
                <select id="selectEstudiante"></select>
            </div>

            <div class="form-group">
                <label>Evaluación <span class="req">*</span></label>
                <select name="id_evaluacion" required>
                    <?php foreach ($evaluaciones as $ev): ?>
                    <option value="<?= $ev['id_evaluacion'] ?>"
                        <?= $ev['id_evaluacion'] == $cal['id_evaluacion'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ev['descripcion']) ?>
                        (<?= $ev['porcentaje'] ?>% — Pos. <?= $ev['posicion'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nota (0.0 – 5.0) <span class="req">*</span></label>
                <input type="number" name="valor" id="inputValor"
                       step="0.1" min="0" max="5"
                       value="<?= $cal['valor'] ?>"
                       style="max-width:160px"
                       oninput="validarNota(this)" required>
                <div id="notaPreview" style="margin-top:6px">
                    <?php
                        $v = (float)$cal['valor'];
                        $bc = $v >= 3.0 ? 'badge-green' : 'badge-orange';
                        $txt = $v >= 3.0 ? '✓ Aprobado' : '✗ Reprobado';
                    ?>
                    <span class="badge <?= $bc ?>" id="notaBadge" style="font-size:.9rem;padding:4px 12px">
                        <?= number_format($v, 1) ?> — <?= $txt ?>
                    </span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Calificación</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
const estudiantes = <?= json_encode($estudiantes, JSON_UNESCAPED_UNICODE) ?>;
const inscActual  = <?= $cal['id_inscripcion'] ?>;

// TomSelect con valor preseleccionado
new TomSelect('#selectEstudiante', {
    options: estudiantes.map(e => ({
        value: String(e.id_inscripcion),
        codigo: e.cod_estudiante,
        nombre: e.nombres,
        apellido: e.apellidos,
        search: `${e.cod_estudiante} ${e.nombres} ${e.apellidos}`
    })),
    items: [String(inscActual)],
    valueField: 'value',
    labelField: 'search',
    searchField: ['codigo', 'nombre', 'apellido'],
    placeholder: 'Escriba código, nombre o apellido…',
    render: {
        option: function(data) {
            return `<div>
                <span class="opt-code">${data.codigo}</span>
                <span class="opt-apellido">${data.apellido},</span>
                <span class="opt-nombre">${data.nombre}</span>
            </div>`;
        },
        item: function(data) {
            return `<span>${data.codigo} — ${data.apellido}, ${data.nombre}</span>`;
        }
    },
    onChange: function(val) {
        document.getElementById('hidInscripcion').value = val;
    }
});

function validarNota(input) {
    const val  = parseFloat(input.value);
    const badge = document.getElementById('notaBadge');
    const prev  = document.getElementById('notaPreview');
    if (isNaN(val) || val < 0 || val > 5) { prev.style.display = 'none'; return; }
    prev.style.display = 'block';
    badge.textContent  = val.toFixed(1) + ' — ' + (val >= 3.0 ? '✓ Aprobado' : '✗ Reprobado');
    badge.className    = 'badge ' + (val >= 3.0 ? 'badge-green' : 'badge-orange');
    badge.style.cssText= 'font-size:.9rem;padding:4px 12px';
}
</script>

<?php layout_footer(); ?>
