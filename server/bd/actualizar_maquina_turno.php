<?php 
$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
require_once($root.'server/fns/fns.php');

function actualizar_maquina_turno($inicio, $fin){
    $rango_dias = get_dates_range($inicio, $fin);

    //Obtenemos todas las zonas
    $conn = conn_rendimiento();
    $query = "SELECT zona_id from zona";
    $zona_ids = array_query($conn, $query);
    $zona_ids = array_map(fn ($x)=>$x['zona_id'], $zona_ids);
    $zona_ids = [1];//de momento lo dejamos en Granada solo
    $turnos = [1, 2, 3];
    $datos_actualizacion = [];

    foreach($zona_ids as $zona_id){
        //establecemos la conexión con dicha zona
        $conn2 = conn_maquinas($zona_id);
        //obtenemos todas las máquinas de la zona
        $query = "SELECT maquina_id,nombre from maquina where zona_id = {$zona_id}";
        $maquinas = array_query($conn, $query);
        foreach($maquinas as $maquina){
            $mn = $maquina['nombre'];
            foreach($rango_dias as $dia){
                foreach($turnos as $turno){
                    //Obtener para un día las horas que estuvo trabajando la maquina en un turno
                    $dia_formateado = $dia.'T00:00:00';
                    $query = "SELECT hora_inicio,hora_fin from grupo_turno where dia='{$dia_formateado}' and turno={$turno}";
                    $horas = array_query($conn, $query);
                    if($horas){
                        $horas = $horas[0];
                        $hora_inicio_dateTime = $horas['hora_inicio'];
                        $hora_fin_dateTime = $horas['hora_fin'];
                        $hora_inicio = $hora_inicio_dateTime->format('Y-m-d H:i:s');
                        $hora_fin = $hora_fin_dateTime->format('Y-m-d H:i:s'); 
                        
                        //Obtenemos los tiempos de cada estado
                        $query = "SELECT TimestampFrom,TimestampTo,OperatingState from ViewMdaMessages where TimestampFrom >= '{$hora_inicio}' and TimestampTo < '{$hora_fin}' and Terminal = '{$mn}'";
                        $out = array_query($conn2, $query);

                        //Ahora obtenemos el número de chapas
                        $query = "SELECT count(HpProgName) from MaschinenMeldung where ZeitstempelMaschine>='{$hora_inicio}' and ZeitstempelMaschine < '{$hora_fin}' and Zustand = 4 and AngemeldeterBediener = '{$mn}'";
                        $n_chapas = array_query($conn2, $query)[0][''];                      
                        
                        if($out){
                            //Insertamos, de ser necesario los casos límite
                            //Insertamos el corte que puede haber venido del turno anterior            
                            $query2 = "SELECT TimestampFrom,TimestampTo,OperatingState from ViewMdaMessages where TimestampFrom <= '{$hora_inicio}' and TimestampTo >= '{$hora_inicio}' and Terminal = '{$mn}'";
                            $out2 = array_query($conn2, $query2);
                            //borprint('-----------------');
                            //borprint($out2);
                            $out2 = array_filter($out2, fn($x)=>$x['OperatingState']!==34);
                            //borprint($out2);
                            if($out2){
                                array_unshift($out, $out2[0]);
                            }
                            //borprint('-----------------');
                            //Insertamos el corte que puede haberse ido al turno siguiente
                            $query3 = "SELECT TimestampFrom,TimestampTo,OperatingState from ViewMdaMessages where TimestampFrom <= '{$hora_fin}' and TimestampTo >= '{$hora_fin}' and Terminal = '{$mn}'";
                            $out3 = array_query($conn2, $query3);
                            //borprint($out3);
                            $out3 = array_filter($out3, fn($x)=>$x['OperatingState']!==34);
                            //borprint($out3);
                            if($out3){
                                array_push($out, $out3[0]);
                            }

                            $datos_pre = array_map(function($x){ 
                                return [
                                    'h_inicio' => $x['TimestampFrom'],
                                    'h_fin' =>  $x['TimestampTo'],
                                    'estado' => $x['OperatingState']
                                ];
                            }, $out);

                            //Gestionamos si en el primer y último corte tenemos  que quitar tiempo
                            if($datos_pre[0]['h_inicio']<$hora_inicio_dateTime){
                                $datos_pre[0]['h_inicio'] = $hora_inicio_dateTime;
                            }
                            $nrows = count($datos_pre);
                            if($datos_pre[$nrows-1]['h_fin']>$hora_fin_dateTime){
                                $datos_pre[$nrows-1]['h_fin'] = $hora_fin_dateTime;
                            }
                            //borprint($datos_pre);

                            $datos = array_map(function($x){ 
                                $inicio =  $x['h_inicio']->getTimeStamp();
                                $fin = $x['h_fin']->getTimeStamp();
                                return [
                                    'h_inicio' => $x['h_inicio'],
                                    'h_fin' =>  $x['h_fin'],
                                    'inicio' => $inicio,
                                    'fin' => $fin,
                                    'diff' => $fin-$inicio,
                                    'estado' => $x['estado']
                                ];
                            }, $datos_pre);
                            
                        
                            $datos_cortando = array_values(array_filter($datos, fn($x)=>$x['estado']===1));
                            $t_cortando = array_reduce($datos_cortando, fn($a,$b)=>$a+$b['diff'], 0);
                            
                            $datos_pausa = array_values(array_filter($datos, fn($x)=>$x['estado']===18));
                            $t_pausa = array_reduce($datos_pausa, fn($a,$b)=>$a+$b['diff'], 0);
                            
                            $datos_error = array_values(array_filter($datos, fn($x)=>$x['estado']===6));
                            $t_error = array_reduce($datos_error, fn($a,$b)=>$a+$b['diff'], 0);
                            
                            $datos_mantenimiento = array_values(array_filter($datos, fn($x)=>$x['estado']===34));
                            $t_mantenimiento = array_reduce($datos_mantenimiento, fn($a,$b)=>$a+$b['diff'], 0);

                            $t_encendida = $t_cortando+$t_pausa+$t_error+$t_mantenimiento;
                            
                            $metricas_resumen = [$t_encendida, $t_cortando, $t_pausa, $t_error, $t_mantenimiento];
                            //tcorte = 1 ==> todos = 0
                            if($metricas_resumen[1] == 0){
                                $metricas_resumen = [0, 0, 0, 0, 0];
                            }
                            $metricas_resumen_norm = array_map(fn($x)=>segundos_to_time($x), $metricas_resumen);

                            $datos_resumen = [
                                'Dia' => $dia,
                                'Turno' => $turno,
                                'Maquina' => $maquina['maquina_id'],
                                'Encendida' => $metricas_resumen_norm[0],
                                'Cortando' => $metricas_resumen_norm[1],
                                'Pausa' => $metricas_resumen_norm[2],
                                'Error' => $metricas_resumen_norm[3],
                                'Mantenimiento' => $metricas_resumen_norm[4],
                                'Chapas' => $n_chapas
                            ];
                            array_push($datos_actualizacion, $datos_resumen);
                        }
                    }
                }
            }
        }
    }

    if(count($datos_actualizacion)>0){
        $inicio_t = $inicio.'T00:00:00';
        $fin_t = $fin.'T00:00:00';
        //Ahora que tenemos los datos borramos de la BD si hay datos de esos días
        $query = "DELETE from maquina_turno where dia>='{$inicio_t}' and dia<='{$fin_t}'";
        sqlsrv_query($conn, $query);
        
        //Una vez que ya sabemos que no tenemos datos en esos días insertamos los nuevos
        foreach($datos_actualizacion as $dato){
            $m_id = $dato['Maquina'];
            $turno = $dato['Turno'];
            $tc = $dato['Cortando'];
            $tp = $dato['Pausa'];
            $te = $dato['Error'];
            $tm = '00:00:00';
            $nc = $dato['Chapas'];
            $dia = $dato['Dia'].'T00:00:00';
            $query = "INSERT into maquina_turno (maquina_id, turno, cortando, pausa, error, mantenimiento, chapas, dia) values($m_id, $turno, '$tc', '$tp', '$te', '$tm', $nc, '$dia')";
            sqlsrv_query($conn, $query);
        }
    }
    sqlsrv_close($conn);
}

$root = $_SERVER['DOCUMENT_ROOT'].'\/rendimiento\/';
session_start();
if(empty($_SESSION["usuario"])) {
    header("Location: ../../index.php ");
    exit(1);
}
else{
	$user = $_SESSION['usuario'];
    //obtenemos los datos del usuario
    $conn = conn_rendimiento();
    $query = "SELECT root from usuario where nombre = '$user'";
    $is_root = (array_query($conn, $query))[0]['root'];
    if($is_root !== 1){
        header("Location: ../../rendimiento.php ");
        exit();
    }
    actualizar_maquina_turno('2022-08-01', '2022-08-29');
}
?>