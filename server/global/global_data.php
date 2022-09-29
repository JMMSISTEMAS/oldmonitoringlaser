<?php
$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/clases/Bd.php');
require_once($root.'server/clases/Zona.php');
require_once($root.'server/clases/Usuario.php');
require_once($root.'server/clases/Permiso.php');

$g_bds = [
    new Bd('Granada_maquinas', 'SVR-TTBOOST01\TRUMPFSQL2', 'T1000_V01_V001', 'Topsreader', 'Toretore-1000'),
    new Bd('Madrid_maquinas', 'SLASERGRAN13\TRUMPFSQL2', 'T1000_V01_V001', 'Topsreader', 'Toretore-1000'),
    new Bd('Levante_maquinas', 'SLASERGRAN23\TRUMPFSQL2', 'T1000_V01_V001', 'Topsreader', 'Toretore-1000'),
    new Bd('Noreste_maquinas', 'SLASERGRAN33\TRUMPFSQL2', 'T1000_V01_V001', 'Topsreader', 'Toretore-1000'),
];

$g_zonas = [
    new Zona('Granada', $g_bds[0]), 
    new Zona('Madrid', $g_bds[1]), 
    new Zona('Levante', $g_bds[2]), 
    new Zona('Noreste', $g_bds[3])
];

$g_usuarios = [
    'Jefe' => new Usuario('Jefe', 'a', true, []),
    'Jefe_Granada' => new Usuario('Jefe_Granada', 'a', false, [new Permiso('Granada', 2)]),
    'Jefe_Madrid' => new Usuario('Jefe_Madrid', 'a', false, [new Permiso('Madrid', 2)]),
    'Jefe_Levante' => new Usuario('Jefe_Levante', 'a', false, [new Permiso('Levante', 2)]),
    'Jefe_Noreste' => new Usuario('Jefe_Noreste', 'a', false, [new Permiso('Noreste', 2)]),
    'Granada' => new Usuario('Granada', 'a', false, [new Permiso('Granada', 1)]),
    'Madrid' => new Usuario('Madrid', 'a', false, [new Permiso('Madrid', 1)]),
    'Levante' => new Usuario('Levante', 'a', false, [new Permiso('Levante', 1)]),
    'Noreste' => new Usuario('Noreste', 'a', false, [new Permiso('Noreste', 1)]),
]

?>