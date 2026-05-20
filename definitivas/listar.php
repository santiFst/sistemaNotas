<?php

session_start();

if (!isset($_SESSION['docente'])) {

    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . "/../config/conexion.php");

$sql = "
    SELECT *
    FROM vista_definitivas
    ORDER BY
        nombre_curso,
        apellidos
";

$resultado = pg_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../assets/css/styles.css">

    <meta charset="UTF-8">

    <title>
        Definitivas
    </title>

</head>

<body>

    <h1>Definitivas</h1>

    <table border="1" cellpadding="10">

        <tr>

            <th>Código</th>

            <th>Estudiante</th>

            <th>Curso</th>

            <th>Definitiva</th>

        </tr>

        <?php while ($fila = pg_fetch_assoc($resultado)) { ?>

            <tr>

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
                    <?php echo $fila['definitiva']; ?>
                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>