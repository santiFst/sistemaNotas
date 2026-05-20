<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Sistema de Gestion de Notas</title>
</head>

<body>

<div class="login">
    <h1>Sistema de Gestión de Notas</h1>
        <p>Por favor, ingrese sus credenciales para acceder al sistema</p>


    <form action="validar_login.php" method="POST">

        <br>
        <p>Código docente:</p>
        <br>

        <input
            type="number"
            name="cod_docente"
            required>

        <br><br>

        <p>Contraseña:</p>
        <br>

        <input
            type="password"
            name="clave"
            required>

        <br><br>

        <button type="submit">
            Ingresar
        </button>
        <br><br>
    </form>

    </label>
</div>
</body>

</html>