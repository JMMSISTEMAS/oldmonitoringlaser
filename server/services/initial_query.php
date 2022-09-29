<?php
include_once '../fns.php';
include 'connection_data.php';

$connectionInfo = array("UID" => $g_usuario, "PWD" => $g_pass, "Database"=>$g_nombre_db);  
$conn = sqlsrv_connect($g_servidores[0], $connectionInfo);

// Comprobando conexión
if(!$conn) {
    echo "Conexión no se pudo establecer.<br />";
    die(print_r(sqlsrv_errors(), true));
}

// Ejecutando consulta
$sql = 'SELECT DISTINCT Terminal FROM ViewMdaMessages';
echo json_query($conn, $sql);
sqlsrv_close($conn)
?>