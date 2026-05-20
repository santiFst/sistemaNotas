<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql = "
    SELECT
        i.id_inscripcion,
        e.cod_estudiante,
        e.nombres,
        e.apellidos,
        c.nombre_curso
    FROM inscripcion i
    INNER JOIN estudiante e
        ON i.cod_estudiante = e.cod_estudiante
    INNER JOIN curso c
        ON i.id_curso = c.id_curso
    ORDER BY c.nombre_curso, e.apellidos
";

$resultado = pg_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Inscripciones</title>
</head>

<body>

    <h1>Lista de Inscripciones</h1>

    <a href="crear.php">
        Nueva Inscripción
    </a>

    <br><br>

    <table class="inscripciones-tabla" border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Estudiante</th>
            <th>Curso</th>
            <th>Acciones</th>
        </tr>

        <?php while ($fila = pg_fetch_assoc($resultado)) { ?>

            <tr>

                <td>
                    <?php echo $fila['id_inscripcion']; ?>
                </td>

                <td>
                    <?php echo $fila['cod_estudiante']; ?>
                </td>

                <td>
                    <?php
                    echo
                    $fila['nombres']
                    . " "
                    . $fila['apellidos'];
                    ?>
                </td>

                <td>
                    <?php echo $fila['nombre_curso']; ?>
                </td>

                <td>

                    <a href="editar.php?id_inscripcion=<?php echo $fila['id_inscripcion']; ?>">
                        Editar
                    </a>

                    |

                    <a href="eliminar.php?id_inscripcion=<?php echo $fila['id_inscripcion']; ?>">
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>