<?php

include_once '../fns/fns.php';
include_once '../global/global_data.php';

$nombre = $_POST["usuario"];
$pass = $_POST["pass"];

$conn = conn_rendimiento();

// Comprobamos que el usuario exista
$query = "SELECT * FROM usuario WHERE nombre='{$nombre}'";
$stmt = sqlsrv_query($conn, $query);
$rows = [];
while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
    $rows[] = [
        'nombre' => $row['nombre'],
        'pass' => $row['pass']
    ];
}

if(count($rows) !== 1){
    sqlsrv_close($conn);
    header("Location: ../../index.php?pass=false");
}

//comprobamos que la contraseña sea correcta
$usuario = $rows[0];
$pass_bd = $usuario['pass'];

if (password_verify($pass, $pass_bd)) {
    session_start();
    $_SESSION["usuario"] = $usuario['nombre'];
    sqlsrv_close($conn);
    header("Location: ../../rendimiento.php");
} else {
    sqlsrv_close($conn);
    header("Location: ../../index.php?pass=false");
}
?>