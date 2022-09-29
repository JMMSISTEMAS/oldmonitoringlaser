<?php

class Usuario{
    public string $nombre = '';
    public string $pass = '';
    public bool $root = false;
    public iterable $permisos = [];

    public function __construct(string $nombre, string $pass, bool $root, iterable $permisos) {
        $this->nombre = $nombre;
        $this->pass = $pass;
        $this->root = $root;
        $this->permisos = $permisos;
    }
}

?>