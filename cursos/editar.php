<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_curso = $_GET['id_curso'];

$sql = "
    SELECT *
    FROM curso
    WHERE id_curso = $id_curso
";

$resultado = pg_query($conexion, $sql);

$curso = pg_fetch_assoc($resultado);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre_curso = $_POST['nombre_curso'];

    $sql_update = "
        UPDATE curso
        SET nombre_curso = '$nombre_curso'
        WHERE id_curso = $id_curso
    ";

    pg_query($conexion, $sql_update);

    header("Location: listar.php");

    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Editar Curso</title>
</head>

<body>

    <h1>Editar Curso</h1>

    <form method="POST">

        <label>ID Curso:</label>
        <br>

        <input
            type="number"
            value="<?php echo $curso['id_curso']; ?>"
            disabled>

        <br><br>

        <label>Nombre Curso:</label>
        <br>

        <input
            type="text"
            name="nombre_curso"
            value="<?php echo $curso['nombre_curso']; ?>"
            required>

        <br><br>

        <button type="submit">
            Actualizar
        </button>

    </form>

</body>

</html>