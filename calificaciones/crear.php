<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

// Cargar todos los cursos para el paso 1
$cursosResult = pg_query($conexion,
    "SELECT c.id_curso, c.nombre_curso,
            COUNT(DISTINCT i.id_inscripcion) AS num_estudiantes,
            COUNT(DISTINCT ev.id_evaluacion)  AS num_evaluaciones
     FROM curso c
     LEFT JOIN inscripcion i  ON i.id_curso  = c.id_curso
     LEFT JOIN evaluacion  ev ON ev.id_curso = c.id_curso
     WHERE c.cod_docente = {$_SESSION['docente']}
     GROUP BY c.id_curso, c.nombre_curso
     ORDER BY c.nombre_curso");
$cursos = [];
while ($row = pg_fetch_assoc($cursosResult)) $cursos[] = $row;

// Si el docente no tiene cursos propios, mostrar todos
if (empty($cursos)) {
    $cursosResult2 = pg_query($conexion,
        "SELECT c.id_curso, c.nombre_curso,
                COUNT(DISTINCT i.id_inscripcion) AS num_estudiantes,
                COUNT(DISTINCT ev.id_evaluacion)  AS num_evaluaciones
         FROM curso c
         LEFT JOIN inscripcion i  ON i.id_curso  = c.id_curso
         LEFT JOIN evaluacion  ev ON ev.id_curso = c.id_curso
         GROUP BY c.id_curso, c.nombre_curso
         ORDER BY c.nombre_curso");
    while ($row = pg_fetch_assoc($cursosResult2)) $cursos[] = $row;
}

$error        = null;
// Guardamos los valores del POST para repoblar el form en JS si hay error
$post_inscripcion = 0;
$post_evaluacion  = 0;
$post_valor       = '';
$post_curso_id    = 0;

// POST — guardar calificacion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_inscripcion   = (int)$_POST['id_inscripcion'];
    $id_evaluacion    = (int)$_POST['id_evaluacion'];
    $valor            = (float)str_replace(',', '.', $_POST['valor']);
    $post_inscripcion = $id_inscripcion;
    $post_evaluacion  = $id_evaluacion;
    $post_valor       = $valor;

    // 1. Validar que inscripcion y evaluacion pertenecen al mismo curso
    $check = pg_query($conexion,
        "SELECT i.id_curso FROM inscripcion i
         INNER JOIN evaluacion e ON i.id_curso = e.id_curso
         WHERE i.id_inscripcion = $id_inscripcion AND e.id_evaluacion = $id_evaluacion");
    $checkRow = pg_fetch_assoc($check);

    if (!$checkRow) {
        $error = 'La evaluacion no pertenece al mismo curso de la inscripcion.';

    } else {
        $post_curso_id = (int)$checkRow['id_curso'];

        // 2. Verificar si ya existe esa calificacion (ANTES del INSERT)
        $existe = pg_query($conexion,
            "SELECT id_calificacion FROM calificacion
             WHERE id_inscripcion = $id_inscripcion
               AND id_evaluacion  = $id_evaluacion");
        $existeRow = pg_fetch_assoc($existe);

        if ($existeRow) {
            // Obtener nombre del estudiante y evaluacion para mensaje claro
            $info = pg_fetch_assoc(pg_query($conexion,
                "SELECT e.nombres, e.apellidos, ev.descripcion
                 FROM inscripcion i
                 INNER JOIN estudiante e  ON i.cod_estudiante = e.cod_estudiante
                 INNER JOIN evaluacion ev ON ev.id_evaluacion = $id_evaluacion
                 WHERE i.id_inscripcion = $id_inscripcion"));
            $nombre_est = $info ? htmlspecialchars($info['nombres'].' '.$info['apellidos']) : 'ese estudiante';
            $desc_eval  = $info ? htmlspecialchars($info['descripcion']) : 'esa evaluacion';
            $error = "Ya existe una calificacion registrada para <strong>$nombre_est</strong> en la evaluacion <strong>$desc_eval</strong>. Use la opcion Editar si desea modificarla.";

        } else {
            // 3. Insertar — ya no deberia fallar por unicidad
            $res = pg_query($conexion,
                "INSERT INTO calificacion (id_inscripcion, id_evaluacion, valor)
                 VALUES ($id_inscripcion, $id_evaluacion, $valor)");

            if (!$res) {
                // Capturar error de PG y traducirlo
                $pgErr = pg_last_error($conexion);
                if (strpos($pgErr, 'duplicate key') !== false || strpos($pgErr, 'llave duplicada') !== false) {
                    $error = 'Este estudiante ya tiene registrada esa calificacion. Use la opcion Editar para modificarla.';
                } elseif (strpos($pgErr, 'valor') !== false || strpos($pgErr, 'check') !== false) {
                    $error = 'El valor de la nota debe estar entre 0.0 y 5.0.';
                } else {
                    $error = 'Error al guardar la calificacion. Intente nuevamente.';
                }
            } else {
                header("Location: listar.php?ok=calificacion"); exit();
            }
        }
    }
}

layout_header('Nueva Calificacion', 'calificaciones', 1);
?>

<!-- TomSelect CSS + JS desde cdnjs -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/css/tom-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/js/tom-select.complete.min.js"></script>

<div class="page-header">
    <div>
        <h1>Nueva Calificacion</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> &rsaquo; <a href="listar.php">Calificaciones</a> &rsaquo; Nueva</div>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger" style="margin-bottom:16px">
    &#x26A0; <?= $error /* ya tiene htmlspecialchars aplicado o es texto fijo */ ?>
    <?php if ($post_curso_id): ?>
    &mdash; <a href="listar.php" style="color:inherit;font-weight:700">Ver listado</a>
    &nbsp;|&nbsp; <a href="editar.php?buscar=<?= $post_inscripcion ?>_<?= $post_evaluacion ?>" style="color:inherit;font-weight:700">Ir a editar</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Indicador de pasos -->
<div class="step-indicator">
    <div class="step-item current" id="step-lbl-1"><div class="step-num">1</div> Seleccionar Curso</div>
    <div class="step-item"        id="step-lbl-2"><div class="step-num">2</div> Seleccionar Evaluacion</div>
    <div class="step-item"        id="step-lbl-3"><div class="step-num">3</div> Estudiante y Nota</div>
</div>

<div class="form-card" style="max-width:680px">
    <div class="form-card-header"><h1 id="formStepTitle">Paso 1 &mdash; Selecciona el Curso</h1></div>
    <div class="form-card-body">

        <form method="POST" id="formCalif">
            <input type="hidden" name="id_inscripcion" id="hidInscripcion">
            <input type="hidden" name="id_evaluacion"  id="hidEvaluacion">
            <input type="hidden" name="valor"          id="hidValor">

            <!-- PASO 1: Curso -->
            <div class="form-step active" id="paso1">
                <?php if (empty($cursos)): ?>
                <div class="alert alert-info">No hay cursos disponibles. Cree un curso primero.</div>
                <?php else: ?>
                <div class="curso-grid" id="cursoGrid">
                    <?php foreach ($cursos as $c): ?>
                    <div class="curso-option"
                         data-id="<?= $c['id_curso'] ?>"
                         data-nombre="<?= htmlspecialchars($c['nombre_curso']) ?>"
                         onclick="seleccionarCurso(this)">
                        <div class="curso-name"><?= htmlspecialchars($c['nombre_curso']) ?></div>
                        <div class="curso-meta">
                            <?= $c['num_estudiantes'] ?> estudiante(s) &bull;
                            <?= $c['num_evaluaciones'] ?> evaluacion(es)
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top:16px;display:flex;gap:10px;align-items:center">
                    <button type="button" class="btn btn-orange" id="btnSiguiente1" disabled onclick="irPaso(2)">
                        Siguiente &rarr;
                    </button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    <span id="spinner1" style="display:none"><span class="spinner"></span> Cargando&hellip;</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- PASO 2: Evaluacion -->
            <div class="form-step" id="paso2">
                <div class="form-group">
                    <label>Evaluacion del curso <strong id="labelCurso2"></strong><span class="req">*</span></label>
                    <div id="listaEvaluaciones"></div>
                </div>
                <div style="margin-top:16px;display:flex;gap:10px">
                    <button type="button" class="btn btn-secondary" onclick="irPaso(1)">&larr; Atras</button>
                    <button type="button" class="btn btn-orange" id="btnSiguiente2" disabled onclick="irPaso(3)">
                        Siguiente &rarr;
                    </button>
                </div>
            </div>

            <!-- PASO 3: Estudiante + Nota -->
            <div class="form-step" id="paso3">
                <div class="form-group">
                    <label>Buscar Estudiante en <strong id="labelCurso3"></strong><span class="req">*</span></label>
                    <select id="selectEstudiante" placeholder="Escriba codigo, nombre o apellido&hellip;"></select>
                    <div id="avisoYaCalificado" class="alert alert-danger"
                         style="margin-top:8px;display:none"></div>
                </div>

                <div class="form-group">
                    <label>Nota (0.0 &ndash; 5.0) <span class="req">*</span></label>
                    <input type="number" id="inputValor" step="0.1" min="0" max="5"
                           placeholder="Ej: 4.5"
                           style="max-width:160px"
                           oninput="validarNota(this)">
                    <div id="notaPreview" style="margin-top:6px;display:none">
                        <span class="badge" id="notaBadge" style="font-size:.9rem;padding:4px 12px"></span>
                    </div>
                </div>

                <div style="margin-top:16px;display:flex;gap:10px;align-items:center">
                    <button type="button" class="btn btn-secondary" onclick="irPaso(2)">&larr; Atras</button>
                    <button type="button" class="btn btn-orange" id="btnGuardar" onclick="guardarCalif()">
                        &#x2713; Guardar Calificacion
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
// ── Estado global ─────────────────────────────────────────────────────────────
let cursoId      = null;
let cursoNombre  = '';
let evaluacionId = null;
let tomSelectInst= null;
let pasoActual   = 1;
// Calificaciones ya existentes en este curso (cargadas junto con los datos del curso)
// Estructura: { "id_inscripcion_id_evaluacion": true }
let yaCalificados = {};

// ── Navegacion entre pasos ────────────────────────────────────────────────────
function irPaso(n) {
    if (n === 2) renderEvaluaciones();
    if (n === 3) renderEstudiantes();

    document.getElementById('paso' + pasoActual).classList.remove('active');
    document.getElementById('paso' + n).classList.add('active');

    for (let i = 1; i <= 3; i++) {
        const lbl = document.getElementById('step-lbl-' + i);
        lbl.classList.remove('current', 'done');
        if (i < n) lbl.classList.add('done');
        else if (i === n) lbl.classList.add('current');
    }

    const titulos = {
        1: 'Paso 1 \u2014 Selecciona el Curso',
        2: 'Paso 2 \u2014 Selecciona la Evaluacion',
        3: 'Paso 3 \u2014 Estudiante y Nota'
    };
    document.getElementById('formStepTitle').textContent = titulos[n];
    pasoActual = n;
}

// ── Paso 1: seleccion de curso ────────────────────────────────────────────────
function seleccionarCurso(el) {
    document.querySelectorAll('.curso-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    cursoId     = el.dataset.id;
    cursoNombre = el.dataset.nombre;
    document.getElementById('btnSiguiente1').disabled = false;
    cargarDatosCurso(cursoId);
}

// ── Cargar evaluaciones e inscritos + calificaciones existentes via AJAX ──────
let datosCurso = null;

async function cargarDatosCurso(id) {
    datosCurso    = null;
    yaCalificados = {};
    document.getElementById('spinner1').style.display = 'inline-block';
    try {
        const resp = await fetch(`api_curso.php?id_curso=${id}`);
        datosCurso = await resp.json();

        // Construir mapa de calificaciones ya existentes
        if (datosCurso.calificaciones_existentes) {
            datosCurso.calificaciones_existentes.forEach(c => {
                yaCalificados[c.id_inscripcion + '_' + c.id_evaluacion] = true;
            });
        }
    } catch(e) {
        console.error('Error cargando datos del curso', e);
    } finally {
        document.getElementById('spinner1').style.display = 'none';
    }
}

// ── Paso 2: renderizar evaluaciones ──────────────────────────────────────────
function renderEvaluaciones() {
    document.getElementById('labelCurso2').textContent = cursoNombre;
    const cont = document.getElementById('listaEvaluaciones');
    evaluacionId = null;
    document.getElementById('hidEvaluacion').value = '';
    document.getElementById('btnSiguiente2').disabled = true;

    if (!datosCurso || !datosCurso.evaluaciones || datosCurso.evaluaciones.length === 0) {
        cont.innerHTML = '<div class="alert alert-info">Este curso no tiene evaluaciones configuradas.</div>';
        return;
    }

    cont.innerHTML = datosCurso.evaluaciones.map(ev => `
        <div class="curso-option" data-id="${ev.id_evaluacion}" onclick="seleccionarEvaluacion(this)"
             style="display:flex;justify-content:space-between;align-items:center">
            <div>
                <div class="curso-name">${ev.descripcion}</div>
                <div class="curso-meta">Posicion ${ev.posicion}</div>
            </div>
            <span class="badge badge-orange" style="font-size:.88rem">${ev.porcentaje}%</span>
        </div>
    `).join('');
}

function seleccionarEvaluacion(el) {
    document.querySelectorAll('#listaEvaluaciones .curso-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    evaluacionId = el.dataset.id;
    document.getElementById('hidEvaluacion').value = evaluacionId;
    document.getElementById('btnSiguiente2').disabled = false;
    // Si ya habia estudiante seleccionado, re-verificar
    verificarDuplicado();
}

// ── Paso 3: TomSelect de estudiantes ─────────────────────────────────────────
function renderEstudiantes() {
    document.getElementById('labelCurso3').textContent = cursoNombre;
    if (tomSelectInst) { tomSelectInst.destroy(); tomSelectInst = null; }
    document.getElementById('selectEstudiante').innerHTML = '';
    document.getElementById('avisoYaCalificado').style.display = 'none';
    document.getElementById('hidInscripcion').value = '';

    const estudiantes = (datosCurso && datosCurso.estudiantes) ? datosCurso.estudiantes : [];

    tomSelectInst = new TomSelect('#selectEstudiante', {
        options: estudiantes.map(e => ({
            value: String(e.id_inscripcion),
            codigo: e.cod_estudiante,
            nombre: e.nombres,
            apellido: e.apellidos,
            search: `${e.cod_estudiante} ${e.nombres} ${e.apellidos}`
        })),
        valueField: 'value',
        labelField: 'search',
        searchField: ['codigo', 'nombre', 'apellido'],
        placeholder: 'Escriba codigo, nombre o apellido...',
        maxOptions: 50,
        render: {
            option: function(data) {
                // Marcar visualmente si ya tiene esa calificacion
                const key = data.value + '_' + evaluacionId;
                const ya  = yaCalificados[key] ? ' &nbsp;<span class="badge badge-orange" style="font-size:.72rem">Ya calificado</span>' : '';
                return `<div>
                    <span class="opt-code">${data.codigo}</span>
                    <span class="opt-apellido">${data.apellido},</span>
                    <span class="opt-nombre">${data.nombre}</span>
                    ${ya}
                </div>`;
            },
            item: function(data) {
                return `<span>${data.codigo} \u2014 ${data.apellido}, ${data.nombre}</span>`;
            }
        },
        onChange: function(val) {
            document.getElementById('hidInscripcion').value = val;
            verificarDuplicado();
        }
    });

    if (estudiantes.length === 0) {
        tomSelectInst.disable();
    }
}

// ── Verificacion en tiempo real de duplicado ──────────────────────────────────
function verificarDuplicado() {
    const inscripcion = document.getElementById('hidInscripcion').value;
    const aviso       = document.getElementById('avisoYaCalificado');
    const btnGuardar  = document.getElementById('btnGuardar');

    if (!inscripcion || !evaluacionId) {
        aviso.style.display = 'none';
        if (btnGuardar) btnGuardar.disabled = false;
        return;
    }

    const key = inscripcion + '_' + evaluacionId;
    if (yaCalificados[key]) {
        aviso.style.display = 'block';
        aviso.innerHTML = '&#x26A0; Este estudiante ya tiene calificacion registrada para esta evaluacion. '
            + '<a href="listar.php" style="color:inherit;font-weight:700">Ir al listado para editar</a>.';
        if (btnGuardar) btnGuardar.disabled = true;
    } else {
        aviso.style.display = 'none';
        if (btnGuardar) btnGuardar.disabled = false;
    }
}

// ── Nota con preview ──────────────────────────────────────────────────────────
function validarNota(input) {
    const val   = parseFloat(input.value);
    const prev  = document.getElementById('notaPreview');
    const badge = document.getElementById('notaBadge');
    if (isNaN(val) || val < 0 || val > 5) { prev.style.display = 'none'; return; }
    prev.style.display = 'block';
    badge.textContent  = val.toFixed(1) + ' \u2014 ' + (val >= 3.0 ? '\u2713 Aprobado' : '\u2717 Reprobado');
    badge.className    = 'badge ' + (val >= 3.0 ? 'badge-green' : 'badge-orange');
    document.getElementById('hidValor').value = val;
}

// ── Guardar con validacion final ──────────────────────────────────────────────
function guardarCalif() {
    const inscripcion = document.getElementById('hidInscripcion').value;
    const evaluacion  = document.getElementById('hidEvaluacion').value;
    const valor       = document.getElementById('hidValor').value;

    if (!inscripcion)  { alert('Seleccione un estudiante.'); return; }
    if (!evaluacion)   { alert('Seleccione una evaluacion.'); return; }
    if (valor === '' || isNaN(parseFloat(valor))) {
        alert('Ingrese una nota valida entre 0.0 y 5.0.'); return;
    }

    // Ultima verificacion de duplicado antes de enviar
    const key = inscripcion + '_' + evaluacion;
    if (yaCalificados[key]) {
        alert('Este estudiante ya tiene calificacion para esa evaluacion. Use Editar en el listado.');
        return;
    }

    document.getElementById('formCalif').submit();
}
</script>

<?php layout_footer(); ?>
