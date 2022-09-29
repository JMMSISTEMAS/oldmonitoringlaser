<?php

$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/global/global_data.php');
require_once($root.'server/fns/fns.php');
require_once($root.'vendor/autoload.php');

$user = -1;
session_start();
if(empty($_SESSION["usuario"])) {
    header("Location: ../index.php ");
    exit();
}
else{
	$user = $_SESSION['usuario'];
    $is_root = $user->root;
    if(!$is_root){
        header("Location: ../rendimiento.php ");
        exit();
    }
}

$loader = new \Twig\Loader\FilesystemLoader($root.'templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

//$conn_options = "host=laserapp.lasergran.com port=5432 dbname=lasergran user=consulta_app password=Nesting2017$";
//$conn = pg_connect($conn_options);

echo $twig->render('gestionar_bd.html',[
    'data' => 5
]);

//pg_close($conn);

?>