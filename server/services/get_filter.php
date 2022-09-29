<?php
$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/fns/fns.php');

session_start();
if(empty($_SESSION["usuario"])) {
    header("Location: ../../index.php ");
    exit(1);
}

$inicio = $_GET['inicio'];
$fin = $_GET['fin'];
$zona = $_GET['zona'];
$grupo = $_GET['grupo'];
$connectionInfo = array("UID" => 'root', "PWD" => 'rendimientoapp2022$', "Database"=>'RENDIMIENTO_APP', "ReturnDatesAsStrings"=>true);
$conn = sqlsrv_connect("SAGEX3WEB\APPSQL", $connectionInfo);
if(!$conn) {
    echo "Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}

//Obtenemos para la zona los ids de las maquina
$query = "SELECT * from maquina where zona_id=$zona";
$maquinas = array_query($conn, $query);
$maquinas_ids = array_map(fn($x)=>$x['maquina_id'], $maquinas);
$or_clause = array_map(fn($x)=>"maquina_turno.maquina_id = $x", $maquinas_ids);
$or_clause = join(' OR ', $or_clause);
$query = "SELECT * from maquina_turno left join maquina on maquina.maquina_id = maquina_turno.maquina_id where (".$or_clause.") and dia>='{$inicio}T00:00:00' and dia<='{$fin}T00:00:00'";
$datos = array_query($conn, $query);

//Tenemos que calcular el turno según el grupo 
$query2 = "SELECT dia,turno from grupo_turno where grupo_id=$grupo";
$datos2 = array_query($conn, $query2);

$datos3 = [];
foreach($datos as $dato){
    $dia = $dato['dia'];
    $turno1 = $dato['turno'];
    foreach($datos2 as $dato2){
        if($dia===$dato2['dia']){
            $turno2 = $dato2['turno'];
            break;
        }
    }
    if($turno1===$turno2){
        array_push($datos3, $dato);
    }
    $turno2 = 0;
}
borlog($datos3);

echo json_encode($datos3);
?>