<?php 

//Inputs: Un día y un grupo
//Outputs: el turno en el que trabajo dicho grupo en dicho día y la hora del primer y último registro

$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/fns/fns.php');

$user = -1;
session_start();

// Comprobamos que seamos admin
if(empty($_SESSION["usuario"])) {
    header("Location: ../../index.php ");
    exit();
}
else{
	$user = $_SESSION['usuario'];
    $is_root = $user->root;
    if(!$is_root){
        header("Location: ../../rendimiento.php ");
        exit();
    }
}

// Comprobamos que nos haya llegado un dia y un grupo
$dia = -1;
$grupo = -1;
if(!isset($_GET['dia']) || !isset($_GET['grupo'])){
    echo 'No se ha establecido dia y/o grupo';
    exit();
}
else{
    $dia = $_GET['dia'];
    $grupo = $_GET['grupo'];
}

$conn_options = "host=laserapp.lasergran.com port=5432 dbname=lasergran user=consulta_app password=Nesting2017$";
$conn = pg_connect($conn_options);
$horas = horas_corte($conn, $grupo, $dia);
echo json_encode($horas);

//Ahora quedaría rellenar la tabla con un INSERT INTO  ...

?>