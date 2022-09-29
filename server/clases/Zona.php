<?php

$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/clases/Bd.php');

class Zona{
    public string $nombre = '';
    public Bd $bd_maquinas;

    public function __construct(string $nombre, Bd $bd_maquinas) {
        $this->nombre = $nombre;
        $this->bd_maquinas = $bd_maquinas;
    }
};

?>