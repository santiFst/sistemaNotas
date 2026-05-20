<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

/*
|--------------------------------------------------------------------------
| OBTENER ID DE LA EVALUACIÓN
|--------------------------------------------------------------------------
*/

$id_evaluacion = $_GET['id_evaluacion'];

/*
|--------------------------------------------------------------------------
| CONSULTAR EVALUACIÓN ACTUAL
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM evaluacion
    WHERE id_evaluacion = $id_evaluacion
";

$resultado = pg_query($conexion, $sql);

$evaluacion = pg_fetch_assoc($resultado);

/*
|--------------------------------------------------------------------------
| CONSULTAR CURSOS
|--------------------------------------------------------------------------
*/

$sql_cursos = "
    SELECT *
    FROM curso
    ORDER BY nombre_curso
";

$cursos = pg_query($conexion, $sql_cursos);

/*
|--------------------------------------------------------------------------
| ACTUALIZAR EVALUACIÓN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_curso = $_POST['id_curso'];
    $descripcion = $_POST['descripcion'];
    $porcentaje = $_POST['porcentaje'];
    $posicion = $_POST['posicion'];

    $sql_update = "
        UPDATE evaluacion
        SET
            id_curso = $id_curso,
            descripcion = '$descripcion',
            porcentaje = $porcentaje,
            posicion = $posicion
        WHERE id_evaluacion = $id_evaluacion
    ";

    $resultado_update = pg_query($conexion, $sql_update);

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
        Editar Evaluación
    </title>

</head>

<body>

    <h1>Editar Evaluación</h1>

    <form method="POST">

        <label>ID Evaluación:</label>

        <br>

        <input
            type="number"
            value="<?php echo $evaluacion['id_evaluacion']; ?>"
            disabled>

        <br><br>

        <label>Curso:</label>

        <br>

        <select name="id_curso" required>

            <?php while ($curso = pg_fetch_assoc($cursos)) { ?>

                <option
                    value="<?php echo $curso['id_curso']; ?>"

                    <?php
                    if (
                        $curso['id_curso']
                        ==
                        $evaluacion['id_curso']
                    ) {
                        echo "selected";
                    }
                    ?>>

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
            value="<?php echo $evaluacion['descripcion']; ?>"
            required>

        <br><br>

        <label>Porcentaje:</label>

        <br>

        <input
            type="number"
            step="0.01"
            name="porcentaje"
            value="<?php echo $evaluacion['porcentaje']; ?>"
            required>

        <br><br>

        <label>Posición:</label>

        <br>

        <input
            type="number"
            name="posicion"
            value="<?php echo $evaluacion['posicion']; ?>"
            required>

        <br><br>

        <button type="submit">

            Actualizar

        </button>

    </form>

</body>

</html>