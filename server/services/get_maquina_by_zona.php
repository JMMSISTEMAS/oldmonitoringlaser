<?php
include '../fns.php';
include 'connection_data.php';

$connectionInfo = array("UID" => $g_usuario, "PWD" => $g_pass, "Database"=>$g_nombre_db);
$zona_index = $_GET['zona'];
$servidor = $g_servidores[$zona_index];
$conn = sqlsrv_connect($servidor, $connectionInfo);

// Comprobando conexión
if(!$conn) {
    echo "Conexión no se pudo establecer.<br />";
    die( print_r( sqlsrv_errors(), true));
   }

// Ejecuntando consulta
$sql = 'SELECT DISTINCT Terminal FROM ViewMdaMessages';
echo json_query($conn, $sql);
sqlsrv_close($conn)
?>