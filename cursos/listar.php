<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql = "
    SELECT *
    FROM curso
    ORDER BY id_curso
";

$resultado = pg_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Cursos</title>
</head>

<body>

    <h1>Lista de Cursos</h1>

    <a href="crear.php">
        Crear Curso
    </a>

    <br><br>

    <table class="cursos-tabla" border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Nombre del curso</th>
            <th>Docente</th>
            <th>Acciones</th>
        </tr>

        <?php while ($fila = pg_fetch_assoc($resultado)) { ?>

            <tr>

                <td>
                    <?php echo $fila['id_curso']; ?>
                </td>

                <td>
                    <?php echo $fila['nombre_curso']; ?>
                </td>

                <td>
                    <?php echo $fila['cod_docente']; ?>
                </td>

                <td>

                    <a href="editar.php?id_curso=<?php echo $fila['id_curso']; ?>">
                        Editar
                    </a>

                    |

                    <a href="eliminar.php?id_curso=<?php echo $fila['id_curso']; ?>">
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>