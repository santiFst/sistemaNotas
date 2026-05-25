<?php

class Conexion {
    private $host = "localhost";
    private $dbname = "sistema_notas";
    private $user = "postgres";
    private $password = "1234";

    private $conexion = null;

    public function conectar() {
        if ($this->conexion === null) {

            $cadenaConexion = "
                host={$this->host}
                dbname={$this->dbname}
                user={$this->user}
                password={$this->password}
            ";

            $this->conexion = pg_connect($cadenaConexion);

            if (!$this->conexion) {
                die("Error al conectar con PostgreSQL");
            }
        }

        return $this->conexion;
    }

    public function cerrar() {
        if ($this->conexion) {
            pg_close($this->conexion);
            $this->conexion = null;
        }
    }
}

$db = new Conexion();
$conexion = $db->conectar();

?>