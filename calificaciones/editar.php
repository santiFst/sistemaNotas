<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_calificacion = $_GET['id_calificacion'];

$sql = "
    SELECT *
    FROM calificacion
    WHERE id_calificacion = $id_calificacion
";

$resultado = pg_query($conexion, $sql);

$calificacion = pg_fetch_assoc($resultado);

$sql_inscripciones = "
    SELECT
        i.id_inscripcion,
        e.nombres,
        e.apellidos,
        c.nombre_curso
    FROM inscripcion i

    INNER JOIN estudiante e
        ON i.cod_estudiante = e.cod_estudiante

    INNER JOIN curso c
        ON i.id_curso = c.id_curso

    ORDER BY
        c.nombre_curso,
        e.apellidos
";

$inscripciones = pg_query($conexion, $sql_inscripciones);

$sql_evaluaciones = "
    SELECT
        ev.id_evaluacion,
        ev.descripcion,
        ev.porcentaje,
        c.nombre_curso
    FROM evaluacion ev

    INNER JOIN curso c
        ON ev.id_curso = c.id_curso

    ORDER BY
        c.nombre_curso,
        ev.posicion
";

$evaluaciones = pg_query($conexion, $sql_evaluaciones);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_inscripcion = $_POST['id_inscripcion'];

    $id_evaluacion = $_POST['id_evaluacion'];

    $valor = $_POST['valor'];

    $sql_validacion = "
        SELECT COUNT(*)
        FROM inscripcion i

        INNER JOIN evaluacion e
            ON i.id_curso = e.id_curso

        WHERE
            i.id_inscripcion = $id_inscripcion
            AND
            e.id_evaluacion = $id_evaluacion
    ";

    $resultado_validacion = pg_query(
        $conexion,
        $sql_validacion
    );

    $fila_validacion = pg_fetch_row(
        $resultado_validacion
    );

    if ($fila_validacion[0] == 0) {

        echo "
            La evaluación no pertenece
            al mismo curso de la inscripción.
        ";

        exit();
    }

    $sql_update = "
        UPDATE calificacion
        SET
            id_inscripcion = $id_inscripcion,
            id_evaluacion = $id_evaluacion,
            valor = $valor
        WHERE id_calificacion = $id_calificacion
    ";

    $resultado_update = pg_query(
        $conexion,
        $sql_update
    );

    if (!$resultado_update) {

        echo pg_last_error($conexion);
        exit();
    }

    header("Location: listar.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Editar Calificación
    </title>

</head>

<body>

    <h1>Editar Calificación</h1>

    <form method="POST">

        <label>Inscripción:</label>

        <br>

        <select name="id_inscripcion" required>

            <?php while ($inscripcion = pg_fetch_assoc($inscripciones)) { ?>

                <option
                    value="<?php echo $inscripcion['id_inscripcion']; ?>"

                    <?php
                    if (
                        $inscripcion['id_inscripcion']
                        ==
                        $calificacion['id_inscripcion']
                    ) {
                        echo "selected";
                    }
                    ?>>

                    <?php

                    echo
                    $inscripcion['nombre_curso']
                    . " - "
                    . $inscripcion['apellidos']
                    . " "
                    . $inscripcion['nombres'];

                    ?>

                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Evaluación:</label>

        <br>

        <select name="id_evaluacion" required>

            <?php while ($evaluacion = pg_fetch_assoc($evaluaciones)) { ?>

                <option
                    value="<?php echo $evaluacion['id_evaluacion']; ?>"

                    <?php
                    if (
                        $evaluacion['id_evaluacion']
                        ==
                        $calificacion['id_evaluacion']
                    ) {
                        echo "selected";
                    }
                    ?>>

                    <?php

                    echo
                    $evaluacion['nombre_curso']
                    . " - "
                    . $evaluacion['descripcion']
                    . " ("
                    . $evaluacion['porcentaje']
                    . "%)";

                    ?>

                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Valor:</label>

        <br>

        <input
            type="number"
            step="0.01"
            min="0"
            max="5"
            name="valor"
            value="<?php echo $calificacion['valor']; ?>"
            required>

        <br><br>

        <button type="submit">

            Actualizar

        </button>

    </form>

</body>

</html>