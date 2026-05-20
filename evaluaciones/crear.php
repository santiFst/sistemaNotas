<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

/*
|--------------------------------------------------------------------------
| CONSULTAR CURSOS
|--------------------------------------------------------------------------
| Se cargan los cursos para mostrarlos en el <select>
*/

$sql_cursos = "
    SELECT *
    FROM curso
    ORDER BY nombre_curso
";

$cursos = pg_query($conexion, $sql_cursos);

/*
|--------------------------------------------------------------------------
| GUARDAR EVALUACIÓN
|--------------------------------------------------------------------------
| Se ejecuta únicamente cuando el formulario es enviado
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_curso = $_POST['id_curso'];
    $descripcion = $_POST['descripcion'];
    $porcentaje = $_POST['porcentaje'];
    $posicion = $_POST['posicion'];

    $sql = "
        INSERT INTO evaluacion (
            id_curso,
            descripcion,
            porcentaje,
            posicion
        )
        VALUES (
            $id_curso,
            '$descripcion',
            $porcentaje,
            $posicion
        )
    ";

    $resultado = pg_query($conexion, $sql);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR ERRORES SQL
    |--------------------------------------------------------------------------
    */

    if (!$resultado) {

    $error = pg_last_error($conexion);

    if (str_contains($error, 'La suma de porcentajes supera el 100')) {

        echo "
            <h3>
                Error:
                La suma de porcentajes del curso no puede superar el 100%.
            </h3>
        ";

    } else {

        echo $error;
    }

    exit();
}

    header("Location: listar.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">

    <meta charset="UTF-8">

    <title>
        Crear Evaluación
    </title>

</head>

<body>

    <h1>Crear Evaluación</h1>

    <form method="POST">

        <label>Curso:</label>

        <br>

        <select name="id_curso" required>

            <?php while ($curso = pg_fetch_assoc($cursos)) { ?>

                <option
                    value="<?php echo $curso['id_curso']; ?>">

                    <?php echo $curso['nombre_curso']; ?>

                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Descripción:</label>

        <br>

        <input
            type="text"
            name="descripcion"
            required>

        <br><br>

        <label>Porcentaje:</label>

        <br>

        <input
            type="number"
            step="0.01"
            name="porcentaje"
            required>

        <br><br>

        <label>Posición:</label>

        <br>

        <input
            type="number"
            name="posicion"
            required>

        <br><br>

        <button type="submit">

            Guardar

        </button>

    </form>

</body>

</html>