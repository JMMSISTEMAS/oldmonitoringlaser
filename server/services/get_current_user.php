<?php
    include '../global/global_data.php';
    session_start();
    $user = $g_usuarios[$_SESSION['usuario']];
    echo json_encode($user);
?>