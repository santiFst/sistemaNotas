<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql = "
    SELECT *
    FROM estudiante
    ORDER BY cod_estudiante
";

$resultado = pg_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta charset="UTF-8">
    <title>Estudiantes</title>
</head>

<body>

    <h1><center>Lista de Estudiantes</center></h1>

    <a href="crear.php">
        Crear estudiante
    </a>

    <br><br>

    <table class="estudiantes-tabla" border="1" cellpadding="10">

        <tr>
            <th>Código</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Acciones</th>
        </tr>

        <?php while ($fila = pg_fetch_assoc($resultado)) { ?>

            <tr>

                <td>
                    <?php echo $fila['cod_estudiante']; ?>
                </td>

                <td>
                    <?php echo $fila['nombres']; ?>
                </td>

                <td>
                    <?php echo $fila['apellidos']; ?>
                </td>

                <td>

                    <a href="editar.php?cod_estudiante=<?php echo $fila['cod_estudiante']; ?>">
                        Editar
                    </a>

                    |

                    <a href="eliminar.php?cod_estudiante=<?php echo $fila['cod_estudiante']; ?>">
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>