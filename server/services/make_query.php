<?php
$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/fns/fns.php');

$zona_nombre = $_GET['zona'];
$f = function($x){
    return $x->nombre;
};
$nombres_zonas = array_map($f, $g_zonas);
$zona_index = array_search($zona_nombre, $nombres_zonas);
$zona = $g_zonas[$zona_index];
$bd = $zona->bd_maquinas;
$conn = $bd->get_conn();

if(!$conn) {
    echo "Conexión no se pudo establecer.<br />";
    die(print_r(sqlsrv_errors(), true));
}

// Creando la consulta
$fecha_inicio = $_GET['inicio'];
$fecha_fin = $_GET['fin'];
$sql = "SELECT TimestampFrom,TimestampTo,OperatingState,Terminal FROM ViewMdaMessages WHERE TimestampFrom>='{$fecha_inicio}' AND TimestampFrom<='{$fecha_fin}'";

// Ejecuntando consulta
//echo $sql;
echo json_query($conn, $sql);
sqlsrv_close($conn);
?>