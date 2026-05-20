<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql = "
    SELECT
        c.id_calificacion,
        e.nombres,
        e.apellidos,
        cu.nombre_curso,
        ev.descripcion,
        ev.porcentaje,
        c.valor
    FROM calificacion c

    INNER JOIN inscripcion i
        ON c.id_inscripcion = i.id_inscripcion

    INNER JOIN estudiante e
        ON i.cod_estudiante = e.cod_estudiante

    INNER JOIN curso cu
        ON i.id_curso = cu.id_curso

    INNER JOIN evaluacion ev
        ON c.id_evaluacion = ev.id_evaluacion

    ORDER BY
        cu.nombre_curso,
        e.apellidos,
        ev.posicion
";

$resultado = pg_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">

    <meta charset="UTF-8">

    <title>
        Calificaciones
    </title>

</head>

<body>

    <h1>Lista de Calificaciones</h1>

    <a href="crear.php">
        Nueva Calificación
    </a>

    <br><br>

    <table class="calificaciones-tabla"border="1" cellpadding="10">

        <tr>

            <th>ID</th>

            <th>Estudiante</th>

            <th>Curso</th>

            <th>Evaluación</th>

            <th>Porcentaje</th>

            <th>Valor</th>

            <th>Acciones</th>

        </tr>

        <?php while ($fila = pg_fetch_assoc($resultado)) { ?>

            <tr>

                <td>
                    <?php echo $fila['id_calificacion']; ?>
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
                    <?php echo $fila['descripcion']; ?>
                </td>

                <td>
                    <?php echo $fila['porcentaje']; ?>%
                </td>

                <td>
                    <?php echo $fila['valor']; ?>
                </td>

                <td>

                    <a href="editar.php?id_calificacion=<?php echo $fila['id_calificacion']; ?>">
                        Editar
                    </a>

                    |

                    <a href="eliminar.php?id_calificacion=<?php echo $fila['id_calificacion']; ?>">
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>