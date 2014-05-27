<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body {
	background-color: #CCC;
}
</style>
<head>
<?php
//THE VARIABLE APLICATION AND DATE
$app =  $_GET['app']; 
$fecha =  $_GET['fecha'];

//INCLUDE TO CONNECTION ARCHIVE
include_once "../conexion.php";
include_once "JavaScripts.html";

//MONTH TO TEXT
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
} //CLOSE MONTH TO DATE

//INICIALIZACION DE VARIABLES

//incremento para indice CONSUMOS DE TPU Y MEMORIA
$indice = 1;
//incremento para indice ANALISIS DE BASE DE DATOS
$indice2 = 1;
//Cantidad de fechas disponibles
$cantidad = 0;

//QUERYS

$consulta = "SELECT distinct(HOST), APP, TIPE, userbk FROM userbk WHERE app = '".$app."'";

//$consulta2="select distinct(nombredb) from tablespaces where host 			like '".$host."' order by nombredb asc"; EN LINEA 

//$consulta3= "select count(*) as cantidad from db_stats where db_fecha <= '".$fecha."' AND db_fecha = last_day(db_fecha) AND db_sid = '".$row2->NOMBREDB."' order by db_fecha desc";

$consulta4 = "SELECT * FROM userbk WHERE app = '".$app."'";

//$consulta5 = "select TO_CHAR(fecha,'YYYY') as anio,TO_CHAR(fecha,'MM') as mes,TO_CHAR(fecha,'DD') as dia,round(avg(cpu_usr * tpu / 100 +cpu_sys * tpu / 100),2) as CPU,  TPU ,round(avg((MEMORIA * mem_assigned / 100 ) /1024),2) as memoria,  (mem_assigned /1024) as mem_assigned from host_statswhere host like '".$host."'and fecha >= to_char(ADD_MONTHS(TO_DATE('".$fecha."','DD-MM-YYYY'), -12))and fecha < '".$fecha."' group by TO_CHAR(fecha,'YYYY'), TO_CHAR(fecha,'MM'), TO_CHAR(fecha,'DD'), TPU, mem_assigned order by TO_CHAR(fecha,'YYYY'),TO_CHAR(fecha,'MM'),TO_CHAR(fecha,'DD') asc";

//EJECUTO $CONSULTA
$stid = oci_parse($con, $consulta);
oci_execute($stid);	



?>
<!-- PAGE TITLE (APLICATION + DATE) -->
<title>Capacity <?php echo $_GET['app']."-".substr($fecha, 3, 2)."/20".substr($fecha, 6); ?></title>
<!-- LINK TO CSS ARCHIVE -->
<link href="../style.css" rel="stylesheet" type="text/css" />


</head>
<body>
<div id="content">

  <p><a href="#objetive"style="text-decoration:none; color: #000;">1.     Objective <br />
    </a> <a href="#reach"style="text-decoration:none; color: #000;">2.     Reach <br />
    </a> <a href="#technicalarchitecture"style="text-decoration:none; color: #000;">3.     Technical architecture <br />
    </a> <a href="#capacityplanning"style="text-decoration:none; color: #000;">4.     Capacity  Planning <br />
    </a>        <a href="#Consumptions"style="text-decoration:none; color: #000;">4.1.     <strong>Consumptions of TPU and Memory</strong></a><br />
  <?php  while(($row = 	oci_fetch_object($stid)) != false){
				  $indice3 = $indice;
				echo "              4.1.".$indice.".       ".$row->TIPE." - ".$row->HOST."<br>\n";
				echo "                     4.1.".$indice.".1.       Consumption TPU<br>\n";
				echo "                     4.1.".$indice.".2.       Consumption MEM<br>\n";
				$indice = $indice + 1;
				}?>
    <a href="#AnalysisofStorage"style="text-decoration:none; color: #000;">4.2       <strong>Analysis of Storage</strong><br />
  </a>
  <?php
			 $indice = 1;
			 //EJECUTO $CONSULTA
              $stid = oci_parse($con, $consulta);
			  oci_execute($stid);
			  while(($row = oci_fetch_object($stid)) != false){
			  echo "              4.2.".$indice.".       Analysis de Storage File System  Unix ".$row->HOST." - General<br>\n";
			  $indice = $indice + 1;
			  }
?>
    <a href="#AnalysisofOracleDataBase"style="text-decoration:none; color: #000;">4.3       <strong>Analysis of Oracle Data Base</strong><br />
  </a>
  <?php
			  $indice = 1;
			  $indice3 = 1;
			  $stid = oci_parse($con, $consulta);
			  oci_execute($stid);
			  while(($row = oci_fetch_object($stid)) != false){
			  $host = $row->HOST;
			  //Consulto todas las base para el host en tabla tablespaces
			 $consulta2="select distinct(nombredb) from tablespaces where host like '".$host."' order by nombredb asc";
			 $stid2 = oci_parse($con, $consulta2);
			 oci_execute($stid2);	
			 
			 while(($row2 = oci_fetch_object($stid2)) != false){
				 
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
  <a href="#Incidents"style="text-decoration:none; color: #000;">5.         Historical of Incidents <br />
  </a> <a href="#Changes"style="text-decoration:none; color: #000;">6.         Historical of Changes <br />
  </a> <a href="#Conclusions"style="text-decoration:none; color: #000;">7.         Conclusions <br />
</a> <a href="#Recommendations"style="text-decoration:none; color: #000;"> 8.         Recommendations</a> </p>
  <h3><a name="objetive" id="objetive">1. Objective</a></h3>
  <p>The objective of this document is to present the results of the Analysis realised with the information of <?php echo $strfecha."/"."20".(substr($fecha, 6) - 1);?> to <?php echo $strfecha."/"."20".substr($fecha, 6);?> on the servers of the application <?php echo $app; ?> </p>
  <h3><a name="reach" id="reach">2. Reach</a></h3>
  <ul>
    <li>The present Study of Performance includes an analysis of the hardware resources.</li>
    <li>The behavior of the servants is analyzed precise:</li>
    <br />
    <?php
	
	//VUELVO A EJECUTAR $CONSULTA
	
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
               Information of Statistics of Bases de Datos Oracle.</p>
  </ul>
  <h3><a name="technicalarchitecture" id="technicalarchitecture">3. Technical architecture</a></h3>
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
		  //EXEC $CONSULTA4
			$stid4 = oci_parse($con, $consulta4);
			oci_execute($stid4);	
			
			while(($row4 = oci_fetch_object($stid4)) != false){
				if($row4->TPU == ""){
				$TPU = "--";
				}
				else{
				$TPU = $row4->TPU;
				}
			echo "<tr bgcolor=\"#FFFFFF\">
			<td align=\"center\">".$row4->HOST."</td>
            <td align=\"center\">".$row4->TIPE."</td>
            <td align=\"center\">".$row4->SERVER."</td>
            <td align=\"center\">".$row4->CLASE."</td>
            <td align=\"center\">".$row4->OS."</td>
            <td align=\"center\">".$row4->TPUMIN."</td>
            <td align=\"center\">".$TPU."</td>
            <td align=\"center\">".$row4->RAM."</td>
            <td align=\"center\">".$row4->SWAP."</td>
			</tr>";
			}
		  ?>
  </table>
  <h3><a name="capacityplaning" id="capacityplaning">4. Capacity Planning</a></h3>
  <p>&nbsp;</p>
  <?php
  //EJECUTO $CONSULTA
$stid = oci_parse($con, $consulta);
oci_execute($stid);	

while(($row = oci_fetch_object($stid)) != false){
$host = $row->HOST;

$consulta5 = "select 
TO_CHAR(fecha,'YYYY') as anio
,TO_CHAR(fecha,'MM') as mes
,TO_CHAR(fecha,'DD') as dia
,round(avg(cpu_usr * tpu / 100 +cpu_sys * tpu / 100),2) as CPU,  TPU 
,round(avg((MEMORIA * mem_assigned / 100 ) /1024),2) as memoria,  (mem_assigned /1024) as mem_assigned 
from host_stats
where host like '".$host."'
and fecha >= to_char(ADD_MONTHS(TO_DATE('".$fecha."','DD-MM-YYYY'), -12))
and fecha < '".$fecha."' 
group by TO_CHAR(fecha,'YYYY'), TO_CHAR(fecha,'MM'), TO_CHAR(fecha,'DD'), TPU, mem_assigned
order by TO_CHAR(fecha,'YYYY'),TO_CHAR(fecha,'MM'),TO_CHAR(fecha,'DD') asc";

$stid0 = oci_parse($con,"ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '. '");
oci_execute($stid0);

$stid5 = oci_parse($con, $consulta5);
oci_execute($stid5);	

	$num=0;
	$CPU='';
	$TPU='';
	while(($row5 = oci_fetch_object ($stid5)) != false) {
		if ( $num == 0 ){
		$CPU=$row5->CPU;
		$TPU=$row5->TPU;
		$MEM=$row5->MEMORIA;
		$M_ASIGNED=$row5->MEM_ASSIGNED;
		$num=1;
		$dia=$row5->DIA;
		$mes=$row5->MES -1;
		$anio=$row5->ANIO;
		}
		else {
			$CPU=$CPU.", ".$row5->CPU;
			$TPU=$TPU.", ".$row5->TPU;
			$MEM=$MEM.", ".$row5->MEMORIA;
			$M_ASIGNED=$M_ASIGNED.", ".$row5->MEM_ASSIGNED;
		}
	}
	

?>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
$(function <?php echo "TPU".$host."";?>() {
        $(<?php echo "'#TPU".$host."'";?>).highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
           xAxis: {
                type: 'datetime',
                maxZoom: 20 * 24 * 3600000, // fourteen days
                title: {
                    text: null
                }
            },
            yAxis: {
                title: {
                    text: 'TPU USE'
                }
            },
            tooltip: {
                valueSuffix: ''
            },
            plotOptions: {
                spline: {
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 2
                        }
                    },
                    marker: {
                        enabled: false
                    },
                    pointInterval: 24 * 3600 * 1000,
                }
            },
            series: [{
                name: 'TPU_ALLOC',
                data: [ <?PHP
					while(($row5 = oci_fetch_object ($stid5)) != false) {
						echo "Date.UTC(".$anio.", ".$mes.", ".$dia.",".$row5->CPU."),";
					}?>]
    
            }, {
                name: 'TPU_USED',
                data: [<?php 
						echo $TPU;
						?>]
            }]
            ,
            navigation: {
                menuItemStyle: {
                    fontSize: '10px'
                }
            }
        });
    });
    

		</script>

<script type="text/javascript">
$(function <?php echo "MEM".$host."";?>() {
		$(<?php echo "'#MEM".$host."'";?>).highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: null
            },
            subtitle: {
                text: null
            },
            xAxis: {
                type: 'datetime',
                maxZoom: 14 * 24 * 3600000, // fourteen days
                title: {
                    text: null
                }
            },
            yAxis: {
                title: {
                    text: 'Memoria MB'
                },
                labels: {
                    formatter: function() {
                        return this.value / 1 +' GB';
                    }
                }
            },
            tooltip: {
                pointFormat: '{series.name} <br> {point.y:,.0f}'
            },
            plotOptions: {
                area: {
                    
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 0,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            series: [{
                name: 'MEM_ALLOC',
				pointInterval: 24 * 3600 * 1000,
                pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", 0, 0, 0)"; ?>,
                data: [<?php 
						echo $M_ASIGNED;
						?>]
				}, {
                name: 'MEM_USED',
				pointInterval: 86400000,
                pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", 0, 0, 0)"; ?>,
                data: [<?php 
						echo $MEM;
						?>]
            }]
        });
    });
    
		</script>
	</head>
	<body>
<p>

<script src="../../js/highcharts.js"></script>

<?php echo "<h3>                     4.1.".$indice3.".1.       Consumption TPU</h3><br>\n";?>
<div id="<?php echo "TPU".$host."";?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php
echo "<h3>                     4.1.".($indice3).".2.       Consumption MEM</h3><br>\n";
?>
<div id="<?php echo "MEM".$host."";?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<?php
}
?>
  
</div>
</body>
</html>