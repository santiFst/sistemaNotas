<?php
session_start();
require_once(__DIR__ . "/../config/conexion.php");

$cod_docente = (int)$_POST['cod_docente'];
$clave       = $_POST['clave'];

$resultado = pg_query($conexion, "SELECT * FROM docente WHERE cod_docente = $cod_docente");

$error = null;
if (pg_num_rows($resultado) > 0) {
    $docente = pg_fetch_assoc($resultado);
    if (password_verify($clave, $docente['clave'])) {
        $_SESSION['docente']         = $docente['cod_docente'];
        // Guardar nombre en sesión para el topbar
        // Se intenta con columnas comunes; adaptar según el esquema real
        $nombre = '';
        if (!empty($docente['nombres']) && !empty($docente['apellidos'])) {
            $nombre = trim($docente['nombres'] . ' ' . $docente['apellidos']);
        } elseif (!empty($docente['nombre'])) {
            $nombre = $docente['nombre'];
        } else {
            $nombre = 'Docente #' . $docente['cod_docente'];
        }
        $_SESSION['nombre_docente'] = $nombre;
        header("Location: ../dashboard.php");
        exit();
    } else {
        $error = 'Contraseña incorrecta. Verifique sus datos.';
    }
} else {
    $error = 'Docente no encontrado. Verifique el código ingresado.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de acceso — Sistema de Notas</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="login-header">
            <div class="login-logo">SN<span>Sistema de Notas</span></div>
        </div>
        <div class="login-body">
            <h2>Acceso denegado</h2>
            <p>No fue posible iniciar sesión.</p>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <a href="login.php" class="btn btn-orange" style="width:100%;justify-content:center;display:flex">← Intentar de nuevo</a>
        </div>
    </div>
</div>
</body>
</html>
