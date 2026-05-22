<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Sistema de Gestion de Notas</title>
</head>

<body>
<body>

<div class="login-container">

    <div class="login-card">

        <h1>Sistema de Gestión de Notas</h1>

        <p>Por favor, ingrese sus credenciales para acceder al sistema</p>

        <form action="validar_login.php" method="POST">

            <label>Código docente</label>

            <input
                type="number"
                name="cod_docente"
                placeholder="Ingrese su código"
                required>

            <label>Contraseña</label>

            <input
                type="password"
                name="clave"
                placeholder="Ingrese su contraseña"
                required>

            <button type="submit">
                Ingresar
            </button>

        </form>

    </div>

</div>

</body>

</html>