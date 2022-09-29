<?php

class Bd{
    public string $nombre = '';
    public string $bd_servidor = '';
    public string $bd_nombre = '';
    public string $bd_usuario = '';
    public string $bd_pass = '';

    public function __construct(string $nombre, string $bd_servidor, string $bd_nombre, string $bd_usuario, string $bd_pass) {
        $this->nombre = $nombre;
        $this->bd_servidor = $bd_servidor;
        $this->bd_nombre = $bd_nombre;
        $this->bd_usuario = $bd_usuario;
        $this->bd_pass = $bd_pass;
    }

    public function get_conn(){
        $connectionInfo = array("UID" => $this->bd_usuario, "PWD" => $this->bd_pass, "Database"=>$this->bd_nombre);
        $conn = sqlsrv_connect($this->bd_servidor, $connectionInfo);
        return $conn;
    }
}

?>