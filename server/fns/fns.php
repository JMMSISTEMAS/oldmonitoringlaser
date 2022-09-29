<?php
$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/global/global_data.php');

function actualizar_maquina_turno_inside($dia, $maquina, $turno){
    $root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
    require($root.'server/global/global_data.php');
    // Gestionamos que conexión realizar según la maquina (esto en el futuro hacerlo leyendo de la BD)
    $datos_maquinas = [
        ['L76-G01', $g_bds[0]], //0
        ['L76-G02', $g_bds[0]], //1
        ['L52', $g_bds[1]], //2
        ['L76', $g_bds[1]], //3
        ['Tube7000', $g_bds[1]], //4
        ['L52', $g_bds[2]], //5
        ['L68', $g_bds[2]], //6
        ['TruLaser5040-L76', $g_bds[3]]
    ];
    $datos_maquina = $datos_maquinas[$maquina];
    $conn = $datos_maquina[1]->get_conn();

    // Realizamos la consulta
    // Primero debemos obtener la hora del primer corte y el último del turno leyendo de la tabla
    // Vamos a simular que es por la mañana porque todavía no tenemos la tabla
    $inicio = $dia.' 07:25:33';
    $fin = $dia.' 12:35:25';
    //borlog([$inicio, $fin]);

    // Ahora leemos todos los registros de la maquina en cuestion en ese intervalo
    $nombre_maquina = $datos_maquina[0];
    $query = "SELECT TimestampFrom,TimestampTo,OperatingState FROM ViewMdaMessages WHERE Terminal = '{$nombre_maquina}' AND TimestampFrom>='{$inicio}' AND TimestampFrom<='{$fin}'";
    $result = sqlsrv_query($conn, $query);
    if($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $rows = [];
    while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
        $rows[] = $row;
    }

    $ts_cortado = array_filter($rows, function($x){
        return $x['OperatingState']==1;
    });
    //borlog($ts_cortado);
    $ts_pausa = array_filter($rows, function($x){
        return $x['OperatingState']==18;
    });
    $ts_error = array_filter($rows, function($x){
        return $x['OperatingState']==6;
    });
    $ts_matenimiento = array_filter($rows, function($x){
        return $x['OperatingState']==34;
    });

    function diff($x){
        $t_inicio = $x['TimestampFrom']->getTimestamp();
        $t_fin = $x['TimestampTo']->getTimestamp();
        return $t_fin-$t_inicio;
    }

    $diff_cortado = array_map('diff', array_values($ts_cortado));
    $diff_pausa = array_map('diff', array_values($ts_pausa));
    $diff_error = array_map('diff', array_values($ts_error));
    $diff_mantenimiento = array_map('diff', array_values($ts_matenimiento));

    function f_sum($a, $b){
        return $a+$b;
    }

    $total_cortado = array_reduce($diff_cortado, 'f_sum');
    $total_pausa = array_reduce($diff_pausa, 'f_sum');
    $total_error = array_reduce($diff_error, 'f_sum');
    $total_mantenimiento = array_reduce($diff_mantenimiento, 'f_sum');
    $total_parado = $total_pausa+$total_error+$total_mantenimiento;

    $t_total_cortado = gmdate("H:i:s", $total_cortado);
    $t_total_pausa = gmdate("H:i:s", $total_pausa);
    $t_total_error = gmdate("H:i:s", $total_error);
    $t_total_mantenimiento = gmdate("H:i:s", $total_mantenimiento);
    $t_total_parado = gmdate("H:i:s", $total_parado);

    borlog($t_total_cortado);

    sqlsrv_close($conn);
}


function borlog($msg){
    echo json_encode($msg);
    exit();
}

function borprint($str){
    echo json_encode($str).'<br>';
}

/*
    Dada una hora en el formato h:m:s determina si es del turno de mañana, tarde o noche
    $turno = 0 --> manaña
    $turno = 1 --> tarde
    $turno = 2 --> noche
    $turno = -1 --> ha habido un error
*/
function turno_hora($hora){
    $hora_h = explode(':', $hora)[0];
    $hora_h = intval($hora_h);
    $turno = -1;
    if($hora_h>=7 && $hora_h<15){
        $turno = 0;
    }
    else if($hora_h>=15 && $hora_h<23){
        $turno = 1;
    }
    else if($hora_h<7 || $hora_h == 23){
        $turno = 2;
    }
    if($turno==-1){
        borlog($hora_h);
    }
    return $turno;
}

// Dado un día y un turno devuelve el datetime de inicio y de fin de dicho turno
function turno_datetime($dia, $turno){
    $h1 = '';
    $h2 = '';
    $d1 = $dia;
    $d2 = $dia;
    if($turno === 1){
        $h1 = 'T07:00:00';
        $h2 = 'T14:59:59';
    }
    else if($turno === 2){
        $h1 = 'T15:00:00';
        $h2 = 'T22:59:59';
    }
    else if($turno === 3){
        $h1 = 'T23:00:00';
        $h2 = 'T06:59:59';
        $d2 = new DateTime($d2);
        $d2->modify('+1 day');
        $d2 = $d2->format('Y-m-d');
    }
    return [$d1.$h1, $d2.$h2];
}

//Devuelve una conexión a la bd de la app de rendimiento
function conn_rendimiento(){
    $connectionInfo = array("UID" => 'root', "PWD" => 'rendimientoapp2022$', "Database"=>'RENDIMIENTO_APP');
    $conn = sqlsrv_connect("SAGEX3WEB\APPSQL", $connectionInfo);
    if(!$conn) {
        echo "Connection could not be established.<br />";
        die( print_r( sqlsrv_errors(), true));
    }
    return $conn;
}

//Devuelve una conexión a la bd de las maquinas de cada uno de las zonas
function conn_maquinas($zona_id){
    $index = $zona_id - 1;
    $servidores = [
        'SVR-TTBOOST01\TRUMPFSQL2',
        'SLASERGRAN13\TRUMPFSQL2',
        'SLASERGRAN23\TRUMPFSQL2',
        'SLASERGRAN33\TRUMPFSQL2'
    ];
    $connectionInfo = array("UID" => 'Topsreader', "PWD" => 'Toretore-1000', "Database"=>'T1000_V01_V001');
    $conn = sqlsrv_connect($servidores[$index], $connectionInfo);
    if(!$conn){
        borlog(print_r( sqlsrv_errors(), true));
        die(print_r( sqlsrv_errors(), true));
    }
    return $conn;
}

// Dada una conexión SQL Server y una consulta devuelve el array asociativo de los resultados de la consulta
function array_query($conn, $query){
    $stmt = sqlsrv_query($conn, $query);
    $rows = [];
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $rows[] = $row;
    }
    return $rows;
}

// Dada una conexión SQL Server y una consulta devuelve el JSON de los resultados de la consulta
function json_query($conn, $query) {
    return json_encode(['data'=>array_query($conn, $query)]);
}

//Dada un consulta a postgree devuelve su representación JSON
function json_pg_query($conn, $query){
    $result = pg_query($conn, $query);
    $rows = array();
    while ($row = pg_fetch_row($result)){
      $rows[] = $row;
    }
    echo json_encode($rows);
}

// Dada una fecha de incio y otra de fin devuelve un array con todas las fechas en el rango
function get_dates_range($inicio, $fin){
    $fechas = new DatePeriod(
        new DateTime($inicio),
        new DateInterval('P1D'),
        (new DateTime($fin))->modify('+1 day')
    );
    $fechas_str = [];
    foreach ($fechas as $key => $value) {
        $fechas_str[] = $value->format('Y-m-d');
    }
    return $fechas_str;
}

function segundos_to_time($segundos){
    return gmdate("H:i:s", $segundos);
}

//Dada una zona devuelve las máquinas de dicha zona
/*function get_maqs_by_zone($zone){
    $nombre_zonas = array_keys($g_zonas);
    $zone_index = array_search($zone, $nombre_zonas);
    $bd = $g_zonas[$zona_index][1];
    $connectionInfo = array("UID" => $bd[3], "PWD" => $bd[4], "Database"=>$bd[2]);
    $conn = sqlsrv_connect($bd[1], $connectionInfo);  
    sqlsrv_close($conn);
    return 1;
    
    /*$connectionInfo = array("UID" => $g_usuario, "PWD" => $g_pass, "Database"=>$g_nombre_db);
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
    sqlsrv_close($conn);
}*/


//Dado un grupo y un día devuelve su primer y último corte
//Necesario para derivar cual ha sido su turno
function horas_corte($conn, $grupo, $dia){
    $incio_dia = $dia.' 07:00:00';
    $dia_datetime = new DateTime($dia);
    $dia_siguiente = $dia_datetime->modify('+1 day');
    $dia_siguiente_str = $dia_siguiente->format('Y-m-d');
    $fin_dia = $dia_siguiente_str.' 06:59:59';

    $query = "SELECT * FROM public.gestion_cut WHERE user_id={$grupo} AND updated_at >= '{$incio_dia}' AND updated_at <= '{$fin_dia}'";
    //echo $query;
    $result = pg_query($conn, $query);

    function parsing_dates($x){
        $x = explode(' ', $x)[1];
        $x = explode('.', $x)[0];
        return $x;
    }

    $hora_turno = Array(0, 0, 0);
    $rows = array();
    while ($row = pg_fetch_row($result)){
        $rows[] = $row;
        $hora =  parsing_dates($row[4]);
        $turno = turno_hora($hora);
        $hora_turno[$turno] += 1;
    }
    $turno_sugerido = array_keys($hora_turno, max($hora_turno))[0];
    //echo json_encode($rows);

    //Volvemos hacer el filtrado pero ahora quedándonos solo con el turno sugerido
    $rows2 = array();
    foreach ($rows as $row) {
        //borlog($row);
        $hora =  parsing_dates($row[4]);
        $turno = turno_hora($hora);
        //borlog($turno_sugerido);
        if($turno == $turno_sugerido){
            $rows2[] = $row;
        }
    }
    $n_rows = count($rows2);

    if($n_rows>0){
        $hora_inicio = $rows2[0][4];
        $hora_fin = $rows2[$n_rows-1][4];
        $horas = array_map("parsing_dates", Array($hora_inicio, $hora_fin));
        return Array($turno_sugerido, $hora_inicio, $hora_fin);
    }
    else{
        return Array(-1,0,0);
    }
    
}

?>