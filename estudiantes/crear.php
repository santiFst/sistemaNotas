<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$error_manual = null;
$error_csv    = null;
$tab_activo   = 'manual'; // default al tab más útil

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Registro manual ──────────────────────────────────────────────────────
    if (isset($_POST['modo']) && $_POST['modo'] === 'manual') {
        $tab_activo   = 'manual';
        $cod          = pg_escape_string($conexion, trim($_POST['cod_estudiante']));
        $nombres      = pg_escape_string($conexion, trim($_POST['nombres']));
        $apellidos    = pg_escape_string($conexion, trim($_POST['apellidos']));

        if ($cod === '' || $nombres === '' || $apellidos === '') {
            $error_manual = 'Todos los campos son obligatorios.';
        } else {
            $res = pg_query($conexion,
                "INSERT INTO estudiante (cod_estudiante, nombres, apellidos)
                 VALUES ('$cod', '$nombres', '$apellidos')");
            if (!$res) {
                $err = pg_last_error($conexion);
                $error_manual = strpos($err, 'llave duplicada') !== false || strpos($err, 'duplicate key') !== false
                    ? "Ya existe un estudiante con el código $cod."
                    : $err;
            } else {
                header("Location: listar.php?ok=creado"); exit();
            }
        }
    }

    // ── Carga masiva CSV ─────────────────────────────────────────────────────
    if (isset($_POST['modo']) && $_POST['modo'] === 'csv') {
        $tab_activo = 'csv';
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
            $file    = $_FILES['csv_file']['tmp_name'];
            $count   = 0;
            $errores = [];
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) >= 3) {
                        $cod  = pg_escape_string($conexion, trim($data[0]));
                        $nom  = pg_escape_string($conexion, trim($data[1]));
                        $ape  = pg_escape_string($conexion, trim($data[2]));
                        $r    = pg_query($conexion,
                            "INSERT INTO estudiante (cod_estudiante, nombres, apellidos)
                             VALUES ('$cod', '$nom', '$ape')");
                        if ($r) $count++;
                        else $errores[] = "Fila $cod: " . pg_last_error($conexion);
                    }
                }
                fclose($handle);
            }
            if (empty($errores)) {
                header("Location: listar.php?ok=csv&n=$count"); exit();
            } else {
                $error_csv = "$count importado(s). Errores: " . implode('; ', array_slice($errores, 0, 3));
            }
        } else {
            $error_csv = 'Por favor selecciona un archivo CSV válido.';
        }
    }
}

layout_header('Crear Estudiante', 'estudiantes', 1);
?>

<div class="page-header">
    <div>
        <h1>Agregar Estudiantes</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › <a href="listar.php">Estudiantes</a> › Agregar</div>
    </div>
</div>

<div class="form-card" style="max-width:580px">
    <div class="form-card-header"><h1>Registro de Estudiantes</h1></div>
    <div class="form-card-body">

        <!-- Tabs -->
        <div class="tab-header">
            <button class="tab-btn <?= $tab_activo === 'manual' ? 'active' : '' ?>"
                    onclick="cambiarTab('manual', this)">
                ✏️ Registro Manual
            </button>
            <button class="tab-btn <?= $tab_activo === 'csv' ? 'active' : '' ?>"
                    onclick="cambiarTab('csv', this)">
                📄 Importar CSV
            </button>
        </div>

        <!-- ── PANEL: Registro manual ─────────────────────────────────── -->
        <div class="tab-panel <?= $tab_activo === 'manual' ? 'active' : '' ?>" id="panel-manual">
            <?php if ($error_manual): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_manual) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="modo" value="manual">
                <div class="form-group">
                    <label>Código del Estudiante <span class="req">*</span></label>
                    <input type="text"
                        name="cod_estudiante"
                        value="<?= htmlspecialchars($_POST['cod_estudiante'] ?? '') ?>"
                        placeholder="Ej: 160005001"
                        pattern="[0-9]+"
                        inputmode="numeric"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                    required>
                    <small style="color:var(--moodle-gray-text);font-size:.78rem">
                        Debe coincidir con el código institucional único del estudiante.
                    </small>
                </div>
                <div class="form-group">
                    <label>Nombres <span class="req">*</span></label>
                    <input type="text" name="nombres"
                           value="<?= htmlspecialchars($_POST['nombres'] ?? '') ?>"
                           placeholder="Ej: David Francisco" required>
                </div>
                <div class="form-group">
                    <label>Apellidos <span class="req">*</span></label>
                    <input type="text" name="apellidos"
                           value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>"
                           placeholder="Ej: Alonso Rodríguez" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-orange">Guardar Estudiante</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>

        <!-- ── PANEL: Importar CSV ────────────────────────────────────── -->
        <div class="tab-panel <?= $tab_activo === 'csv' ? 'active' : '' ?>" id="panel-csv">
            <?php if ($error_csv): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_csv) ?></div>
            <?php endif; ?>
            <div class="alert alert-info">
                📄 El archivo CSV debe tener <strong>3 columnas en orden</strong>:
                <code>Código, Nombres, Apellidos</code> — sin fila de encabezado.<br>
                <small>Ejemplo: <code>160005001,David Francisco,Alonso Rodríguez</code></small>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="modo" value="csv">
                <div class="form-group">
                    <label>Archivo CSV <span class="req">*</span></label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-orange">Importar Estudiantes</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function cambiarTab(nombre, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + nombre).classList.add('active');
}
</script>

<?php layout_footer(); ?>
