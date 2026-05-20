<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql_estudiantes = "
    SELECT *
    FROM estudiante
    ORDER BY apellidos
";

$estudiantes = pg_query(
    $conexion,
    $sql_estudiantes
);

$sql_cursos = "
    SELECT *
    FROM curso
    ORDER BY nombre_curso
";

$cursos = pg_query(
    $conexion,
    $sql_cursos
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cod_estudiante = $_POST['cod_estudiante'];

    $id_curso = $_POST['id_curso'];

    $anio = $_POST['anio'];

    $periodo = $_POST['periodo'];

    $sql = "
        INSERT INTO inscripcion (
            cod_estudiante,
            id_curso,
            anio,
            periodo
        )
        VALUES (
            $cod_estudiante,
            $id_curso,
            $anio,
            $periodo
        )
    ";

    $resultado = pg_query(
        $conexion,
        $sql
    );

    if (!$resultado) {

    $error = pg_last_error($conexion);

    if (
        strpos(
            $error,
            'llave duplicada'
        ) !== false
    ) {

        echo "
            Esta inscripción ya existe
            para ese estudiante,
            curso, año y período.
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
        Crear Inscripción
    </title>

</head>

<body>

    <h1>Nueva Inscripción</h1>

    <form method="POST">

        <label>Estudiante:</label>

        <br>

        <select
            name="cod_estudiante"
            required>

            <?php while ($estudiante = pg_fetch_assoc($estudiantes)) { ?>

                <option
                    value="<?php echo $estudiante['cod_estudiante']; ?>">

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

        <select
            name="id_curso"
            required>

            <?php while ($curso = pg_fetch_assoc($cursos)) { ?>

                <option
                    value="<?php echo $curso['id_curso']; ?>">

                    <?php
                    echo $curso['nombre_curso'];
                    ?>

                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Año:</label>

        <br>

        <input
            type="number"
            name="anio"
            min="2000"
            max="2100"
            required>

        <br><br>

        <label>Período:</label>

        <br>

        <select
            name="periodo"
            required>

            <option value="1">

                Período I

            </option>

            <option value="2">

                Período II

            </option>

        </select>

        <br><br>

        <button type="submit">

            Guardar

        </button>

    </form>

</body>

</html>