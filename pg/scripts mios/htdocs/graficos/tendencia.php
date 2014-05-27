<?php

$host =  $_GET['host'];	
$fecha =  $_GET['fecha'];
$indice =  $_GET['indice'];

$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$consulta = "select (db_host)as HOST, (db_fecha)as FECHA, db_sid, (db_tamano) as TOTAL from db_stats where db_fecha <= '".$fecha."' AND db_host LIKE '".$host."' AND db_sid LIKE 'RLT0PDC' order by db_fecha asc";

$stid = oci_parse($con, $consulta);
oci_execute($stid);	
//Variables para la tendencia
$Xten = 0;
$Yten = 0;
$EXten = 0;
$Xexp2 = 0;
$Xexp3 = 0;
$Xexp4 = 0;
$Xexp5 = 0;
$Xexp6 = 0;
$XY = 0;
$Xexp2Y = 0;
$Xexp3Y = 0;


while(($row = oci_fetch_object ($stid)) != false) {
$ocupado = ($row->TOTAL)/1024/1024/1024;	
$N = $N + 1;
echo "lala";
if($N%10==0){
$Xten = $N;
echo "lala2";
echo "N: ".$N;
$EXten = $EXten + $Xten;
$Yten = $Yten + $ocupado;
$XY = $XY + ($N * $ocupado);
$Xexp2 = $Xexp2 + pow($Xten,2);
$Xexp3 = $Xexp3 + pow($Xten,3);
$Xexp4 = $Xexp4 + pow($Xten,4);
$Xexp5 = $Xexp5 + pow($Xten,5);
$Xexp6 = $Xexp6 + pow($Xten,6);
$Xexp2Y = $Xexp2Y + (pow($N,2)*$ocupado);
$Xexp3Y = $Xexp3Y + (pow($N,3)*$ocupado);
}
}


function GausJordan($M, $NroFilas, $NroColumnas)
{
	for($i=0; $i< $NroFilas; $i++)
	{
	$Temp=$M;
	if($M[$i][$i]!=0)
	{
		for($j=0; $j<$NroColumnas; $j++)
			$M[$i][$j]=((1/$Temp[$i][$i])*$Temp[$i][$j]);
			for($k=0; $k< $NroFilas; $k++)
			{
				$Temp=$M;
				if($k!=$i)
				for($j=0; $j< $NroColumnas; $j++)
				$M[$k][$j]=((-$Temp[$k][$i])*$Temp[$i][$j])+$Temp[$k][$j];
			}
	}
}
	return $M;
}

$Matriz= array(
array(6,15,55,152.6),
array(15,55,225,585.6),
array(55,225,979,2488.8),
);



$Result = GausJordan($Matriz,3,4);

echo $Result[0][0]."|".$Result[0][1]."|".$Result[0][2]."|".$Result[0][3]."<br>";
echo $Result[1][0]."|".$Result[1][1]."|".$Result[1][2]."|".$Result[1][3]."<br>";
echo $Result[2][0]."|".$Result[2][1]."|".$Result[2][2]."|".$Result[2][3]."<br>";

?>