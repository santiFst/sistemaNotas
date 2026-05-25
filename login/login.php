<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Sistema de Notas</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">
        <div class="login-header">
            <div class="login-logo">
                SN
                <span>Sistema de Notas</span>
            </div>
        </div>
        <div class="login-body">
            <h2>Bienvenido</h2>
            <p>Ingrese sus credenciales para acceder al sistema</p>

            <form action="validar_login.php" method="POST">
                <div class="form-group">
                    <label for="cod_docente">Código de Docente <span class="req">*</span></label>
                    <input type="number" id="cod_docente" name="cod_docente" placeholder="Ej: 1001" required>
                </div>
                <div class="form-group">
                    <label for="clave">Contraseña <span class="req">*</span></label>
                    <input type="password" id="clave" name="clave" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-orange">Ingresar al sistema</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
