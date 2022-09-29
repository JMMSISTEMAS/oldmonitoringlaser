<?php
    $connectionInfo = array("UID" => 'root', "PWD" => 'rendimientoapp2022$', "Database"=>'RENDIMIENTO_APP');
    $conn = sqlsrv_connect("SAGEX3WEB\APPSQL", $connectionInfo);
    if(!$conn) {
        echo "Connection could not be established.<br />";
        die( print_r( sqlsrv_errors(), true));
    }

    $nombre = 'Granada';
    $pass = '#20lasergran22$';
    $enc_pass = password_hash($pass, PASSWORD_DEFAULT);

    $query = "INSERT INTO usuario (nombre, pass) VALUES ('{$nombre}', '{$enc_pass}')";
    $stmt = sqlsrv_query($conn, $query);
    if($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($conn);
?>