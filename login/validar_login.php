<?php

session_start();

require_once(__DIR__ . "/../config/conexion.php");

$cod_docente = $_POST['cod_docente'];
$clave = $_POST['clave'];

$sql = "
    SELECT *
    FROM docente
    WHERE cod_docente = $cod_docente
";

$resultado = pg_query($conexion, $sql);

if (pg_num_rows($resultado) > 0) {

    $docente = pg_fetch_assoc($resultado);

    if (
        password_verify(
            $clave,
            $docente['clave']
        )
    ) {

        $_SESSION['docente'] =
            $docente['cod_docente'];

        header("Location: ../dashboard.php");

        exit();

    } else {

        echo "Contraseña incorrecta";
    }

} else {

    echo "Docente no encontrado";
}

?>