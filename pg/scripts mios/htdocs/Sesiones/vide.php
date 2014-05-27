<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
  <?php
$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

if(isset($_POST['entorno'])){
$entorno = $_POST['entorno'];}
if(!isset($_POST['host'])){
}else{
$host = $_POST['host'];
$fecha = $_POST['fecha'];
}
echo "<form name=\"principal\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">\n\n";
echo "<br>";
echo "<label><strong>Entorno</strong></label>";
echo "<select name=\"entorno\" class='select' onChange=\"this.form.submit()\">\n";
if (isset($entorno)){
	echo "<option value=\"".$entorno."\" selected>".$entorno."</option>\n";
}else{

echo "<option value=\"\" selected> Seleccion Entorno</option>\n";
echo "<option value=\"Preproduccion\" >Preproduccion</option>\n";
echo "<option value=\"Produccion\" >Produccion</option>\n";
}
echo "<form name=\"principal\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">\n\n";
echo "<br>";
echo "<label><strong>Aplicacion</strong></label>";
echo "<select name=\"Aplicacion\" class='select' onChange=\"this.form.submit()\">\n";
echo "<option value=\"\"> Seleccion Aplicacion</option>\n";
echo "<select>\n\n";

$consulta6 = "select DISTINCT(HOST) from filesystems";
$stid6 = oci_parse($con, $consulta6);
oci_execute($stid6);

while(($row6 = oci_fetch_object ($stid6)) != false){
if($host == $row6->HOST){
echo "<option value=\"".$row6->HOST."\" selected>".$row6->HOST."</option>\n";
}else
{
echo "<option value=\"".$row6->HOST."\">".$row6->HOST."</option>\n";
}
}
echo "<select>\n\n";
echo "<label><strong>Fechas Disponibles</strong></label>";
echo "<select class='select' name=\"fecha\" onChange=\"submit()\">\n";

if(!empty($host)){
	
$consulta7 = "select DISTINCT(TRUNC(FECHA)) as FECHA, HOST from filesystems where HOST like '".$host."' ORDER BY 1 DESC";
$stid7 = oci_parse($con, $consulta7);
oci_execute($stid7);

if(oci_num_rows($stid7) == 0){
	echo "<option value=\"\">FECHA</option>\n";
while(($row7 = oci_fetch_object ($stid7)) != false){
echo "<option value=\"".$row7->FECHA."\">".$row7->FECHA."</option>\n";
}
}

else{
echo "<option value=\"\"> No hay fechas disponibles</option>";
}
}else{
echo "<option value=\"\">Seleccione un Host</option>";
}
echo "</select>\n\n";
?>
</body>
</html>