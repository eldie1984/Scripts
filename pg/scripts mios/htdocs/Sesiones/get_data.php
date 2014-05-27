<?php

		$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
        $consulta = "select cpu_usr, memoria from host_stats where fecha in (select max(fecha) from host_stats) ";
	$stid = oci_parse($con, $consulta);
	oci_execute($stid);	
	$row = oci_fetch_object($stid);
    $data[]=$row;
        echo $data;
    
?>
