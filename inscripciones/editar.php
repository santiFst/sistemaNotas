<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$id_inscripcion = $_GET['id_inscripcion'];

$sql = "
    SELECT *
    FROM inscripcion
    WHERE id_inscripcion = $id_inscripcion
";

$resultado = pg_query($conexion, $sql);

$inscripcion = pg_fetch_assoc($resultado);

$sql_estudiantes = "
    SELECT *
    FROM estudiante
    ORDER BY apellidos
";

$estudiantes = pg_query($conexion, $sql_estudiantes);

$sql_cursos = "
    SELECT *
    FROM curso
    ORDER BY nombre_curso
";

$cursos = pg_query($conexion, $sql_cursos);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cod_estudiante = $_POST['cod_estudiante'];
    $id_curso = $_POST['id_curso'];

    $sql_update = "
        UPDATE inscripcion
        SET
            cod_estudiante = $cod_estudiante,
            id_curso = $id_curso
        WHERE id_inscripcion = $id_inscripcion
    ";

    $resultado_update = pg_query($conexion, $sql_update);

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
        Editar Inscripción
    </title>

</head>

<body>
    <link rel="stylesheet" href="../assets/css/styles.css">

    <h1>Editar Inscripción</h1>

    <form method="POST">

        <label>Estudiante:</label>

        <br>

        <select name="cod_estudiante" required>

            <?php while ($estudiante = pg_fetch_assoc($estudiantes)) { ?>

                <option
                    value="<?php echo $estudiante['cod_estudiante']; ?>"

                    <?php
                    if (
                        $estudiante['cod_estudiante']
                        ==
                        $inscripcion['cod_estudiante']
                    ) {
                        echo "selected";
                    }
                    ?>>

                    <?php
                    echo
                    $estudiante['apellidos']
                    . " "
                    . $estudiante['nombres'];
                    ?>

                </option>

            <?php } ?>

        </select>

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
                        $inscripcion['id_curso']
                    ) {
                        echo "selected";
                    }
                    ?>>

                    <?php echo $curso['nombre_curso']; ?>

                </option>

            <?php } ?>

        </select>

        <br><br>

        <button type="submit">

            Actualizar

        </button>

    </form>

</body>

</html>