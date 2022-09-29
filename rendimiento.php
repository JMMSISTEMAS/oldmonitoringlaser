<?php
$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/global/global_data.php');
require_once($root.'server/fns/fns.php');

$nombre_usuario = '';
session_start();
if(empty($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}
else{
	$nombre_usuario = $_SESSION['usuario'];
}

//Obtenemos todos los datos del usuario que ha hecho login
$conn = conn_rendimiento();
//$conn2 = conn_maquinas(2);
$query = "SELECT * FROM usuario WHERE nombre='{$nombre_usuario}'";
$resultado = array_query($conn, $query)[0];
if($resultado['nombre'] !== $nombre_usuario){
    echo 'Error de credenciales';
    exit(1);
}
$datos_usuario = [
    'nombre' => $resultado['nombre'],
    'is_root' => $resultado['root']
];
$id_usuario = $resultado['usuario_id'];

//Obtenemos las zonas a las que tiene acceso y el nivel de permisos
//Si es root damos permiso de tipo 2 a todas las zonas
if($datos_usuario['is_root'] === 1){
    $query = "SELECT * FROM zona";
    $resultado = array_query($conn, $query);
    $permisos = array_map(function($x){
        return [
            'zona_id' => $x['zona_id'],
            'tipo' => 2,
            'zona_nombre' => $x['nombre']
        ];
    }, $resultado);
}
//En cualquier otro caso cogemos los permisos específicos de dicho usuario
else{
    $query = "SELECT * FROM usuario_permiso WHERE usuario_id='{$id_usuario}'";
    $resultado = array_query($conn, $query);
    $permisos = array_map(function($x){
        $zona_id = $x['zona_id'];
        $query2 = "SELECT nombre FROM zona WHERE zona_id='{$zona_id}'";
        $resultado2 = array_query(conn_rendimiento(), $query2);
        return [
            'zona_id' => $zona_id,
            'tipo' => $x['tipo'],
            'zona_nombre' => $resultado2[0]['nombre'] 
        ];
    }, $resultado);
}

//Ahora obtenemos los grupos asociados a su primera zona
$zona_inicial = $permisos[0]['zona_id'];
$query = "SELECT * FROM grupo WHERE zona_id={$zona_inicial}";
$grupos = array_query($conn, $query);

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

echo $twig->render('rendimiento.html',[
	'usuario' => $datos_usuario, 
    'permisos' => $permisos, 
    'grupos' => $grupos
]);
?>
