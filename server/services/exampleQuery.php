<?php

	/*
		SERVIDOR GRANADA --> SVR-TTBOOST01\TRUMPFSQL2
		SERVIDOR MADRID --> SLASERGRAN13\TRUMPFSQL2
		SERVIDOR LEVANTE --> SLASERGRAN23\TRUMPFSQL2
		SERVIDOR NORESTE --> SLASERGRAN33\TRUMPFSQL2
	*/

	$serverName = "SVR-TTBOOST01\TRUMPFSQL2";  
	//$serverName = "SLASERGRAN13\TRUMPFSQL2";
	//$serverName = "SLASERGRAN23\TRUMPFSQL2";  
	//$serverName = "SLASERGRAN33\TRUMPFSQL2";    
	$uid = "Topsreader";     
	$pwd = "Toretore-1000";    
	$databaseName = "T1000_V01_V001";  
	$connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database"=>$databaseName);  
	$conn = sqlsrv_connect($serverName, $connectionInfo);  
	
	
	// Check connection
	if( $conn ) {
		echo "Conexión establecida.<br />";
   	}else{
		echo "Conexión no se pudo establecer.<br />";
		die( print_r( sqlsrv_errors(), true));
	}

	$sql = "SELECT * FROM ViewMdaMessages WHERE ID = 1";
	$result = sqlsrv_query($conn, $sql);
	
	if($result === false) {
    	die( print_r(sqlsrv_errors(), true) );
	}

	$res = [];
	while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC) ) {
    	$res[] = $row;
	}

	echo json_encode( [ 'data' => $res ] );
	
?>
