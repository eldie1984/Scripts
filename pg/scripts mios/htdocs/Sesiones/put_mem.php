

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		
	</head>
	<body>

<?php

echo "ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '. ';";
echo "<br>";
echo "alter session set nls_date_format='yyyy/mm/dd';";


$HOST=$_POST["host"];

//SI EL ARCHIVO SE ENVIÃ" Y ADEMÃS SE SUBIO CORRECTAMENTE
if (isset($_FILES["archivo"]) && is_uploaded_file($_FILES['archivo']['tmp_name'])) {
$fp = fopen ($_FILES['archivo']['tmp_name'], "r" ); 
$j=0;
while (( $data = fgetcsv ( $fp , 1000 , ";" )) !== FALSE ) { // Mientras hay líneas que leer...
if ( $j > 1) { 
$i = 0; 
if ( $data[5] == 0 or strlen($data[5])== 0 or $data[4] == 0 ) {
	$CPU_USR=0;
	$TPU=0;
}
else
{ $CPU_USR= round((($data[4]/$data[5])*100),2);
$TPU=$data[5];}

if ( $data[6] == 0 or strlen($data[6]) == 0 or $data[7]==0) {
	$MEMORIA=0;
}
else
{ $MEMORIA= round((($data[7]/$data[6])*100),2);}
 
echo "<br>";


$consulta = "INSERT
INTO HOST_STATS
  (
    FECHA,
    HOST,
    CPU_USR,
    MEMORIA,
    CPU_SYS,
    TPU,
    MEM_ASSIGNED
  )
  VALUES
  ('".$data[2]."',
  '".$HOST."',
  ".$CPU_USR.",
  ".$MEMORIA.",
  0,
  ".$TPU.",
  ".$data[6].")
  ;";
  echo $consulta;
;
}
$j++;
 } 
fclose ( $fp ); 
echo "commit;";

} else 
 echo "Error de subida";
?>	

	</body>
</html>