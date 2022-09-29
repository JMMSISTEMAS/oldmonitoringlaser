<?php

class Permiso{
    public string $nombre_zona = '';
    public int $permiso = 0;

    public function __construct(string $nombre_zona, int $permiso) {
        $this->nombre_zona = $nombre_zona;
        $this->permiso = $permiso;
    }
}

?>