<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
#content #divimpresion table tr td table tr td p strong {
	font-family: FrutigerLight;
	font-size: 16px;
}
#content #divimpresion table tr td table tr td table tr td strong em strong {
	font-family: Arial, Helvetica, sans-serif;
}
#content #divimpresion table tr td table tr td table tr td strong em strong {
	font-size: 14px;
	color: #F00;
}
#content #divimpresion table tr td table tr td table tr td strong {
	font-family: "Courier New", Courier, monospace;
	font-size: 24px;
}
#content #divimpresion table tr td table tr td table tr td strong em strong {
	font-style: italic;
}
#content #divimpresion table tr td table tr td table tr td strong em strong {
	font-weight: bold;
}
#content #divimpresion table tr td table tr td table tr td strong em strong {
	color: #8A0000;
}
#content #divimpresion table tr td table tr td table tr td strong em strong {
	font-size: 18px;
}
</style>

?>
<?php
//TRAIGO LA VARIABLE APLICACION
$app =  $_GET['app']; 
$fecha =  $_GET['fecha']; 
?>
<title>Capacity <?php echo $_GET['app']."-".substr($fecha, 3, 2)."/20".substr($fecha, 6); ?></title>
<head>
<link href="../style.css" rel="stylesheet" type="text/css" />

<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/data.js"></script>
<script src="../../js/modules/drilldown.js"></script>

<script language="javascript">
	// Funcion para agregar un recomendacion
      function AgregarRecommendation() {
		 TextoLinea=prompt('Add recommendation:','');
         document.getElementById("recommendation").innerHTML +="<br><li>"+ TextoLinea + "</li>";  // Funcion para agregar comentario
     }
     function AgregarLineaDeTexto() {
		 TextoLinea=prompt('Add comment:','');
         document.getElementById("texto_chat").innerHTML +="<br><li>"+ TextoLinea + "</li>";  // Agrego nueva linea antes
     }
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
function imprimirSelec(nombre)
{
 var ficha = document.getElementById(nombre);//almacenamos en variable los datos del div a imprimir
 var ventimp = window.open('', 'Impresion');//aqui se genera una pagina temporal 
 
 ventimp.document.write( ficha.innerHTML );//aqui cargamos el contenido del div seleccionado
 ventimp.document.close();//cerramos el documento
 ventimp.print( );//enviamos los datos a la impresora
 ventimp.close();//cerramos ventana temporal
}
</script> 


<?php 
include_once "../conexion.php";

//PASO MES A LETRAS
if(substr($fecha, 3, 2)== 01){
$strfecha = "January";
}else{
if(substr($fecha, 3, 2)== 02){
$strfecha = "February";
}else{
	if(substr($fecha, 3, 2)== 03){
$strfecha = "March";
}else{
	if(substr($fecha, 3, 2)== 04){
$strfecha = "April";
}else{
	if(substr($fecha, 3, 2)== 05){
$strfecha = "May";
}else{
	if(substr($fecha, 3, 2)== 06){
$strfecha = "June";
}else{
	if(substr($fecha, 3, 2)== 07){
$strfecha = "July";
}else{
	if(substr($fecha, 3, 2)== 08){
$strfecha = "August";
}else{
	if(substr($fecha, 3, 2)== 09){
$strfecha = "September";
}else{
	if(substr($fecha, 3, 2)== 10){
$strfecha = "October";
}else{
	if(substr($fecha, 3, 2)== 11){
$strfecha = "November";
}else{
	if(substr($fecha, 3, 2)== 12){
$strfecha = "December";
}
}
}
}
}
}
}
}
}
}
}
}

$consulta = "SELECT distinct(HOST), APP, TIPE, userbk FROM userbk WHERE app = '".$app."'";
$stid = oci_parse($con, $consulta);
oci_execute($stid);	
//incremento para indice CONSUMOS DE TPU Y MEMORIA
$indice = 1;
//incremento para indice ANALISIS DE BASE DE DATOS
$indice2 = 1;
//Cantidad de fechas disponibles
$cantidad = 0;
?>
<body>
<div id="content">
<DIV ID="divimpresion">
  <table width="800" height="31" border="0" align="center">
    <tr>
      <td align="left"><table border="1" cellspacing="0" cellpadding="0" width="800">
        <tr>
          <td width="141" align="center" valign="middle"><p><strong>&nbsp;</strong><strong>DP/DSIN/PDA</strong></p></td>
          <td width="520" align="center" valign="middle"><p><strong>&nbsp;</strong><strong><img src="../img/dfk_clip_image001.png" alt="" width="211" height="34" /></strong></p></td>
          <td width="131" align="center" valign="middle"><p><strong>Documentation</strong></p></td>
        </tr>
        <tr>
          <td width="141" align="center" valign="middle"><p><strong> </strong><strong>usage interne </strong></p></td>
          <td width="520" align="center" valign="middle"><table width="520" border="0">
            <tr>
              <td align="center" bgcolor="#CCCCCC"><strong><em><strong><?php echo $app; ?></strong></em></strong></td>
            </tr>
            <tr>
              <td align="center"><strong>Report of Study Performance</strong></td>
            </tr>
          </table></td>
          <td width="131" align="center" valign="middle"><p><strong>GENERAL</strong></p></td>
        </tr>
      </table>
        <table border="1" cellspacing="0" cellpadding="0" width="800">
          <tr>
          <td width="666" align="center" valign="top"><p>&nbsp;</p>
          <h1>Performance Study – <?php echo $strfecha."/"."20".substr($fecha, 6);?></h1>
            <p>&nbsp;</p></td>
        </tr>
    </table>
        <table width="800" border="0">
          <tr>
            <td><A href="#objetive"style="text-decoration:none; color: #000;"><p>&nbsp;</p>1.     Objective <br /></A>
              <A href="#reach"style="text-decoration:none; color: #000;">2.     Reach <br /></A>
              <A href="#technicalarchitecture"style="text-decoration:none; color: #000;">3.     Technical architecture <br /></A>
              <A href="#capacityplanning"style="text-decoration:none; color: #000;">4.     Capacity  Planning <br /></A>
                     <A href="#Consumptions"style="text-decoration:none; color: #000;">4.1.        <strong>Consumptions of TPU and Memory</strong></A><br /><?php  while(($row = 	oci_fetch_object($stid)) != false){
				  $indice3 = $indice;
				echo "              4.1.".$indice.".       ".$row->TIPE." - ".$row->HOST."<br>\n";
				echo "                     4.1.".$indice.".1.       Consumption TPU<br>\n";
				echo "                     4.1.".$indice.".2.       Consumption MEM<br>\n";
				$indice = $indice + 1;
				}?>
                     <A href="#AnalysisofStorage"style="text-decoration:none; color: #000;">4.2       <strong>Analysis of Storage</strong><br /></A>
             <?php
			 $indice = 1;
              $stid = oci_parse($con, $consulta);
			  oci_execute($stid);
			  while(($row = 	oci_fetch_object($stid)) != false){
			  echo "              4.2.".$indice.".       Analysis de Storage File System  Unix ".$row->HOST." - General<br>\n";
			  $indice = $indice + 1;
			  }
?>
                     <A href="#AnalysisofOracleDataBase"style="text-decoration:none; color: #000;">4.3       <strong>Analysis of Oracle Data Base</strong><br /></A>
              <?php
			  $indice = 1;
			  $indice3 = 1;
			  $stid = oci_parse($con, $consulta);
			  oci_execute($stid);
			  while(($row = 	oci_fetch_object($stid)) != false){
			  $host = $row->HOST;
			  //Consulto todas las base para el host en tabla tablespaces
			 $consulta2="select distinct(nombredb) from tablespaces where host 			like '".$host."' order by nombredb asc";
			 $stid2 = oci_parse($con, $consulta2);
			 oci_execute($stid2);	
			 while(($row2 = 	oci_fetch_object($stid2)) != false){
$consulta3= "select count(*) as cantidad from db_stats where db_fecha <= '".$fecha."' AND db_fecha = last_day(db_fecha) AND db_sid = '".$row2->NOMBREDB."' order by db_fecha desc";
$stid3 = oci_parse($con, $consulta3);
oci_execute($stid3);
while(($row3 = 	oci_fetch_object($stid3)) != false){
if($row3->CANTIDAD > $cantidad){
$cantidad = $row3->CANTIDAD;
}
}

			  echo "              4.3.".$indice.".       Distribution of Tablespaces on Data Base - ".$row2->NOMBREDB."<br>\n";
			  $indice = $indice + 1;
			  echo "              4.3.".$indice.".       Historial growth of Data Base - ".$row2->NOMBREDB."<br>\n";
			  $indice = $indice + 1;
			  echo "              4.3.".$indice.".       Proyected growth of Data Base - ".$row2->NOMBREDB."<br>\n";
			  $indice = $indice + 1;
			 }
			  }
			  
			  ?>
              <A href="#Incidents"style="text-decoration:none; color: #000;">5.         Historical of Incidents <br /></A>
              <A href="#Changes"style="text-decoration:none; color: #000;">6.         Historical of Changes <br /></A>
              <A href="#Conclusions"style="text-decoration:none; color: #000;">7.         Conclusions <br /></A>
             <A href="#Recommendations"style="text-decoration:none; color: #000;"> 8.         Recommendations</A></td>
          </tr>
        </table>
        <A name="objetive"><p><h3>1. Objective</h3></A>
        <p>The objective of this document is to present the results of the Analysis realised with the information of <?php echo $strfecha."/"."20".(substr($fecha, 6) - 1);?> to <?php echo $strfecha."/"."20".substr($fecha, 6);?>  on the servers of the application <?php echo $app; ?>
        <A name="reach"><h3>2. Reach</h3></A>
        <ul>
          <li>The present Study of Performance includes an analysis of the hardware resources.</li>
          <li>The behavior of the servants is analyzed precise:</li><br />
          <?php
          $stid = oci_parse($con, $consulta);
		  oci_execute($stid);
		  while(($row = oci_fetch_object($stid)) != false){
		  echo $row->HOST." - ".$row->APP."<br>\n";
		  }
		  ?>
          </li>
          <p></p>
          <li>An analysis of changes and incidents is realised.</li>
          <li>The Data acquired on metric is based on the sources:</li>
  <p>          Portal <a href="http://unix.inetpsa.com/">http://unix.inetpsa.com/</a> (Documentación  Técnica).<br />
             Portal <a href="http://pacific.inetpsa.com/">http://pacific.inetpsa.com/</a> (Métricas  Unix &amp; Hw-).<br />
             Information of Statistics of Bases de Datos Oracle.</p></ul>
        <A name="technicalarchitecture"><h3>3. Technical architecture</h3></A>
        <p> Next the architecture of hardware of the involved virtual servant will appear:</p>
</p>
        </p>
        <table width="781" border="0" bgcolor="#948A54">
          <tr>
            <td height="40" valign="middle"><h2><?php echo $app; ?></h2></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr bgcolor="#C4BD97">
            <td height="40" align="center"><strong>Virtual Server</strong></td>
            <td align="center"><strong>Detail</strong></td>
            <td align="center"><strong><span id="Sp1.s2_o"><span id="p1.t2_1" value="1010/noun:common">Physique</span></span> Server</strong></td>
            <td align="center"><strong>Class</strong></td>
            <td align="center"><strong>Operative System</strong></td>
            <td align="center"><strong>TPU Min</strong></td>
            <td align="center"><strong>TPU Max</strong></td>
            <td align="center"><strong>RAM GB</strong></td>
            <td align="center"><strong>Swap GB</strong></td>
          </tr>
          
          <?php 
		  
		    $consulta3 = "SELECT * FROM userbk WHERE app = '".$app."'";
			$stid3 = oci_parse($con, $consulta3);
			oci_execute($stid3);	
			while(($row3 = oci_fetch_object($stid3)) != false){
				if($row3->TPU == ""){
				$TPU = "--";
				}
				else{
				$TPU = $row3->TPU;
				}
			echo "<tr bgcolor=\"#FFFFFF\">
			<td align=\"center\">".$row3->HOST."</td>
            <td align=\"center\">".$row3->TIPE."</td>
            <td align=\"center\">".$row3->SERVER."</td>
            <td align=\"center\">".$row3->CLASE."</td>
            <td align=\"center\">".$row3->OS."</td>
            <td align=\"center\">".$row3->TPUMIN."</td>
            <td align=\"center\">".$TPU."</td>
            <td align=\"center\">".$row3->RAM."</td>
            <td align=\"center\">".$row3->SWAP."</td>
			</tr>";
			}
		  ?>
            
          
        </table>
<A name="capacityplaning"><h3>4. Capacity Planning</h3><h3></A>       <A name="Consumptions"><h3>4.1.     Consumptions of TPU and Memory</h3></A>

<?php  
$stid = oci_parse($con, $consulta);
oci_execute($stid);

while(($row = 	oci_fetch_object($stid)) != false){
				echo "<h3>              4.1.".$indice3.".       ".$row->TIPE." - ".$row->HOST."</h3><br>\n";
				
				
				$ano = substr($fecha, 6);
				$mes =substr($fecha, 3, 2);
				
				$host2 =  $row->HOST;
				echo "<iframe name=\"window\" src=\"tpu2.php?host=".$host2."&fecha=".$fecha."&indice3=".$indice3."\"width=\"800\" frameborder=\"0\" scrolling=\"no\" id=\"iframe\" onload='javascript:resizeIframe(this);'></iframe>";
				$indice3 = $indice3 + 1;
				}?>        
        <h3><A name="AnalysisofStorage">4.2. Analysis of Storage</A></h3></p>
        <?php
		$stid = oci_parse($con, $consulta);
		oci_execute($stid);
		$indice = 1;
		while(($row = 	oci_fetch_object($stid)) != false){
			$host = $row->HOST;
			$userbk = $row->USERBK;
			echo "<h3>4.2.".$indice.". Analysis of Storage File System Unix  ".$host." – General</h3>";
        echo "</p><p>";
		echo "</p><p>";
			echo "Available free space  on Files System existing on the virtual server ".$host;
			echo "</p><p>";
			echo "</p><p>";
			echo "<table width=\"360\" border=\"0\" align=\"center\">
        <tr>
          <td width=\"13\" bgcolor=\"#FCD5B4\">&nbsp;</td>
          <td width=\"158\" align=\"left\">Operative System.</td>
          <td width=\"11\" align=\"left\" bgcolor=\"#B8CCE4\">&nbsp;</td>
          <td width=\"160\" align=\"left\">Software.</td>
        </tr>
        <tr>
          <td bgcolor=\"#CCFFCC\">&nbsp;</td>
          <td align=\"left\">Data and Archives.</td>
          <td align=\"left\" bgcolor=\"#B1A0C7\">&nbsp;</td>
          <td align=\"left\">Backup.</td>
        </tr>
</table>";
			echo "<table width=\"681\" height=\"199\" border=\"0\">
        <tr>
          <td align=\"left\"><p>The   File Systems (·) dedicated Application they   contain</p>
            <p>. Software.<br>
              . Application.<br>
              . Datafiles (Bd Oracle).<br>
            . Backup.</p></td>
        </tr>
  </table>";
			echo "<iframe name=\"window\" src=\"dfk2.php?host=".$host."&fecha=".$fecha."&userbk=".$userbk."\"width=\"800\" frameborder=\"0\" scrolling=\"no\" id=\"iframe\" onload='javascript:resizeIframe(this);'></iframe>";
			$indice = $indice + 1;
		}
		?>
        <h3 align="left"><A name="AnalysisofOracleDataBase">4.3. Analysis of Oracle Data Base</a></h3>
        <?php
        $stid = oci_parse($con, $consulta);
		oci_execute($stid);
		$indice = 1;
		while(($row = 	oci_fetch_object($stid)) != false){
			$host = $row->HOST;
			$userbk = $row->USERBK;
			
		$consulta3="select distinct(host) from tablespaces where host = '".$host."'";
		$stid3 = oci_parse($con, $consulta3);
		oci_execute($stid3);
		while(($row3 = 	oci_fetch_object($stid3)) != false){
			echo "<iframe name=\"window\" src=\"tbs2.php?host=".$host."&fecha=".$fecha."&userbk=".$userbk."&indice=".$indice."\"width=\"800\" frameborder=\"0\" scrolling=\"no\" id=\"iframe\" onload='javascript:resizeIframe(this);'></iframe>";
			$indice = $indice + 1;
		
		}
		}
            ?>
        <p align="left"><h3>
        <A name="Incidents">5.         Historical of Incidents </A></h3>
        <p><img src="../img/inci1.png"/>
        </p>
        <p><br />
          <img src="../img/inci2.png"/>
        </p>
        <p><br />
          <img src="../img/inci3.png"/>
          <br />
          
        </p>
        <h3><A name="Changes">6.         Historical of Changes </A></h3>
<p><img src="../img/cambios1.png"/>  </p>
<p><br />
  <img src="../img/cambios2.png"/>  </p>
<p><br />
  <img src="../img/cambios3.png"/>
  <br />
  <br />
</p>
<h3><A name="Conclusions">7.         Conclusions </A></h3>
<p>
<?php
if($cantidad > 2){
$stid = oci_parse($con, $consulta);
oci_execute($stid);	
while(($row = oci_fetch_object($stid))!=false){
	
$host = $row->HOST;	

$consulta5 = "select DISTINCT(db_sid) as NOMBREDB from db_stats where db_host ='".$host."' order by NOMBREDB asc";
$stid5 = oci_parse($con, $consulta5);
oci_execute($stid5);	

while(($row5 = oci_fetch_object ($stid5)) != false) { 
$nombredb = $row5->NOMBREDB;

$consulta1 = "select (db_host)as HOST, (db_fecha)as FECHA, db_sid, (db_tamano) as TOTAL from db_stats where db_fecha <= '".$fecha."' AND db_host LIKE '".$host."' AND db_sid LIKE '".$nombredb."' AND db_fecha = last_day(db_fecha) AND rownum <= 10  order by db_fecha asc";

$stid = oci_parse($con, $consulta1);
				oci_execute($stid);	
				$delta = 0;
				$promediodelta = 0;
				$deltacant = 0;
				while(($row = oci_fetch_object ($stid)) != false){
				if($delta <= 0){
					$delta2 = 0.00; 
					}else{
						$delta2 =(round(($row->TOTAL )/1024/1024/1024,2) - $delta);
				} 
				$delta = round(($row->TOTAL )/1024/1024/1024,2);
				$deltacant = $deltacant + 1;
				$promediodelta = ($promediodelta + $delta2);
					} 
					$texto=  "The tendency of growth average in the data base ".$nombredb." is of the order of ".round($promediodelta/($deltacant-1),2)."Gb monthly according to the data collected from ".$strfecha."/"."20".(substr($fecha, 6) - 1)." to the date";
					echo "<li>".strip_tags($texto)."</li>";
}
}
}
?>
</p>
<p>
  <?php 
$stid = oci_parse($con, $consulta);
oci_execute($stid);	
while(($row = oci_fetch_object($stid))!=false){
$host = $row->HOST;	
$consulta4 = "SELECT * FROM FILESYSTEMS WHERE FECHA LIKE '".$fecha."' AND TAMANIO>0 AND HOST LIKE '".$host."'";
$stid4 = oci_parse($con, $consulta4);
oci_execute($stid4);
while(($row4 = oci_fetch_object($stid4))!=false){
	if((($row4->USADO * 100)/$row4->TAMANIO) > 90 ){
	
echo " <li> To analize: These are the filesystem that surpasses the 90 percent of size: </li><br>\n";
echo "<br>";
$stid = oci_parse($con, $consulta);
oci_execute($stid);	 

echo "<table width=\"781\" border=\"0\" bgcolor=\"#948A54\">
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr bgcolor=\"#C4BD97\">
            <td align=\"center\"><strong>HOST </strong></td>
            <td align=\"center\"><strong>FILESYSTEM</strong></td>
            <td align=\"center\"><strong>SIZE</strong></td>
            <td align=\"center\"><strong>PERCENT</strong></td>
          </tr>";
while(($row = oci_fetch_object($stid))!=false){
$host = $row->HOST;	
$consulta4 = "SELECT * FROM FILESYSTEMS WHERE FECHA LIKE '".$fecha."' AND TAMANIO>0 AND HOST LIKE '".$host."'";
$stid4 = oci_parse($con, $consulta4);
oci_execute($stid4);
$recommendation = 0;
while(($row4 = oci_fetch_object($stid4))!=false){
	if((($row4->USADO * 100)/$row4->TAMANIO) > 85 && (($row4->USADO * 100)/$row4->TAMANIO) < 92){
		$recommendation = 1;
	echo "<tr bgcolor=\"#FFFFFF\">
    <td  width='6%'align='left' >".$host."</td>
    <td width='6%' align='left'>".$row4->PUNTO_MONTAJE."</td>
    <td width='6%'align='left' >".$row4->TAMANIO."</td>
      <td width='6%'align='left' >".round((($row4->USADO * 100)/$row4->TAMANIO),2)."% </td>
</tr>";
	}
	if((($row4->USADO * 100)/$row4->TAMANIO) > 92){
		$recommendation = 1;
	echo "<tr bgcolor=\"#FFFFFF\">
    <td  width='6%'align='left' ><span class=\"noventa\">".$host."</td>
    <td width='6%' align='left' ><span class=\"noventa\">".$row4->PUNTO_MONTAJE."</td>
    <td width='6%'align='left' ><span class=\"noventa\">".$row4->TAMANIO."</td>
    <td width='6%'align='left' ><span class=\"noventa\">".round((($row4->USADO * 100)/$row4->TAMANIO),2)."% </td>
  </tr>";
	}
}
}
echo  "</table >";
 echo "<table cellpadding='0' cellspacing='0' width=\"781\" bgcolor='#948A54'>";
 echo "	  <thead><tr bgcolor=\"#948A54\"><td width='2%'align='center'><p> </p></td>";
 echo "	  <td width='6%' align='center'></td>";
 echo "	  <td width='6%'align='center'></td>";
 echo "	  <td width='6%'align='center'></td>";
 echo "	  </tr></thead>";
 echo  "</table>";
}else{
}
}
}
?>
<div id="texto_chat"></div>
<br />
  <input type="image" src="../img/addcomment.png" onclick="AgregarLineaDeTexto()" />

 
<h3><A name="Recommendations">8.         Recommendations </A></h3>
<p>&nbsp;</p>
<li> To monthly continue realising Capacities on this application to establish tendencies.</li><br />
<?php
if($recommendation == 1){
echo "<li> To monitor the evolution of the FS's present that surpasses 85% of occupation. To evaluate if it is necessary to realise the order of Comuts of extension.</li>";
}
	?>
<div id="recommendation"></div>
<br />
  <input type="image" src="../img/addrecommendation.png" onclick="AgregarRecommendation()" />
</p>
<h3>&nbsp;</h3></p>
<p>&nbsp;</p></td>
    </tr>
    <tr>
      <td align="left">&nbsp;</td>
    </tr>
  </table>
  </DIV>
</div>
<a href="javascript:imprimirSelec('divimpresion')" >Imprime ficha</a>
</body>
</html>