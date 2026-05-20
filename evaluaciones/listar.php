<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql = "
    SELECT
        e.id_evaluacion,
        e.descripcion,
        e.porcentaje,
        e.posicion,
        c.nombre_curso
    FROM evaluacion e
    INNER JOIN curso c
        ON e.id_curso = c.id_curso
    ORDER BY c.nombre_curso, e.posicion
";

$resultado = pg_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Evaluaciones</title>
</head>

<body>

    <h1>Lista de Evaluaciones</h1>

    <a href="crear.php">
        Crear Evaluación
    </a>

    <br><br>

    <table class="evaluaciones-tabla" border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Curso</th>
            <th>Descripción</th>
            <th>Porcentaje</th>
            <th>Posición</th>
            <th>Acciones</th>
        </tr>

        <?php while ($fila = pg_fetch_assoc($resultado)) { ?>

            <tr>

                <td>
                    <?php echo $fila['id_evaluacion']; ?>
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
                    <?php echo $fila['posicion']; ?>
                </td>

                <td>

                    <a href="editar.php?id_evaluacion=<?php echo $fila['id_evaluacion']; ?>">
                        Editar
                    </a>

                    |

                    <a href="eliminar.php?id_evaluacion=<?php echo $fila['id_evaluacion']; ?>">
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>