<?php

$conexion = pg_connect("
    host=localhost
    dbname=sistema_notas
    user=postgres
    password=1234
");

if (!$conexion) {

    echo "Falló conexión";
    exit();
}

?>