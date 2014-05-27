<!DOCTYPE HTML>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/modules/jquery.min.js"></script>
<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/data.js"></script>
<script src="../../js/modules/drilldown.js"></script>
<script src="../../js/modules/exporting.js"></script>

<?php
$host =  $_GET['host'];	
$fecha =  $_GET['fecha'];
$indice =  $_GET['indice'];

$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
?>
<title>File Systems - Storage - <?php echo $host; ?></title>


        
<style type="text/css">
body {
	background-color: #FFF;
}
.alerta {
	color: #F00;
}
</style>

<body>
<?php

//FUNCION GAUSS PARA LA TENDENCIA

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
//TERMINA FUNCION GAUSS


$consulta5 = "select DISTINCT(db_sid) as NOMBREDB from db_stats where db_host ='".$host."' order by NOMBREDB asc";
$stid5 = oci_parse($con, $consulta5);
oci_execute($stid5);
$num = 0;	

while(($row5 = oci_fetch_object ($stid5)) != false) { 
$nombredb = $row5->NOMBREDB;

//Genero una query que calcule cuantos meses hay almacenados, si es menor a 2 no muestra grafico
$consulta2= "select count(*) as cantidad from db_stats where db_fecha <= '".$fecha."' AND db_fecha = last_day(db_fecha) AND db_sid = '".$nombredb."' order by db_fecha desc";

$consulta3 = "select sum(TOTSIZ)/1024/1024/1024 AS TOTAL, sum(AVALSIZE)/1024/1024/1024 AS DISPONIBLE from tablespaces where FECHA LIKE '".$fecha."' AND HOST LIKE '".$host."' AND NOMBREDB LIKE '".$row5->NOMBREDB."'order by fecha desc";
																	
$stid3 = oci_parse($con, $consulta3);
oci_execute($stid3);		
while(($row3 = oci_fetch_object ($stid3)) != false) {
$total = $row3->TOTAL;
$disponible = $row3->DISPONIBLE;
}

?>         
<table width="800" border="0" align="center">

  <tr>
    <td><h3>
      <blockquote>
    </h3>
      <h3>         4.3.<?php echo $indice; ?>. Distribution of Tablespaces DB <?php echo $row5->NOMBREDB; ?></h3>
        <table width="200" border="0" align="center" bgcolor="#FFC875">
          <tr>
            <td><table width="409" border="0" align="center">
              <tr>
                <td align="center"><strong>Tablespaces Summary </strong></td>
              </tr>
              <tr>
                <td></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table width="412" border="0">
              <tr bgcolor="#FFFF99">
                <td align="center"><strong>Total GB</strong></td>
                <td align="center"><strong>Used</strong></td>
                <td align="center"><strong>Available</strong></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#DFDFDF"><?php echo round(($total),2); ?></td>
                <td align="center" bgcolor="#DFDFDF"><?php echo round(($total-$disponible),2); ?></td>
                <td align="center" bgcolor="#DFDFDF"><?php echo round(($disponible),2); ?></td>
              </tr>
            </table></td>
          </tr>
      </table>
        <p>&nbsp;</p>
        <div id="<?PHP echo "container".$row5->NOMBREDB;?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        <p>
          <?php
//Total Size
	  
$Total = 0;
//Calculo el ultimo mes en que la base crecio

$consulta1 = "select (db_host)as HOST, (db_fecha)as FECHA, db_sid, (db_tamano) as TOTAL from db_stats where db_fecha <= '".$fecha."' AND db_host LIKE '".$host."' AND db_sid LIKE '".$nombredb."' AND db_fecha = last_day(db_fecha) AND rownum <= 11  order by db_fecha asc";

				$stid = oci_parse($con, $consulta1);
				oci_execute($stid);	
				$delta = 0;
				$promediodelta = 0;
				$deltacant = 0;
				$fechapositiva = $fecha;
				while(($row = oci_fetch_object ($stid)) != false){
					if($delta <= 0){
					$delta2 = 0.00; 
					}else{
						$delta2 =(round(($row->TOTAL )/1024/1024/1024,2) - $delta);
				} 
				$delta = round(($row->TOTAL )/1024/1024/1024,2);
				if($delta2 > 0){
			
				$fechapositiva = $row->FECHA;
				}
					} 
					
$consulta8 = "select distinct(db_fecha) AS FECHA from db_stats where db_fecha < '".$fechapositiva."' AND db_fecha = last_day(db_fecha) 
AND db_host LIKE 'yvas4180' AND db_sid LIKE 'BM2PO' order by db_fecha DESC";

$stid8 = oci_parse($con, $consulta8);
oci_execute($stid8);	
$row8 = oci_fetch_object ($stid8);
$fechaanterior = $row8->FECHA;	
			
//Espacio disponible tablespace

$Disponible = 0;
$consulta = "select (db_host)as HOST, (db_fecha)as FECHA, db_sid, (db_tamano) as TOTAL from db_stats where db_fecha <= '".$fechapositiva."' and db_fecha >= '".$fechaanterior."' AND db_host LIKE '".$host."' AND db_sid LIKE '".$nombredb."' order by db_fecha asc";

$stid = oci_parse($con, $consulta);
oci_execute($stid);	
	
//Calculo Tendencia y proyeccion

//n cantidad de valores 
$N = 0;
//Meses x GigasBytes
$MxG = 0;
$mes = 0; 
//N2 Y G2
$N2 = 0;
$G2 = 0;
$n4 = 0;
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

// Sumatoria de mes al cuadrado
$nal2 = 0;
$stid2 = oci_parse($con, $consulta2);
oci_execute($stid2);
$row2 = oci_fetch_object ($stid2);
$cantidad = $row2->CANTIDAD;



?>
<H3>4.3.<?php echo $indice + 1; ?>. Historical growth of Data Base
      </H3>
      <?php
if($cantidad < 2){
		echo "<div id=\"alerta\" class=\"alerta2\">";
	echo "The compiled information is smaller to 2 months for ".$nombredb."";
	echo "</div>";

}else{
	
while(($row = oci_fetch_object ($stid)) != false) {
$ocupado = ($row->TOTAL)/1024/1024/1024;	
$N = $N + 1;
if($N < 50){
$Xten = $N;
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

$MxG = $MxG + ($ocupado * $N);
//G2 para calcular b (mes + mes + ...) * (GB + GB + ....)
$N2= $N + $N2;
$G2 = $G2 + $ocupado; 
//calculo C = nx(mes^2 + mes^2 + ...)
$N3 = $N * $N;
$n4 = $n4 + $N3;
//Sumatoria de mes al cuadrado
$nal2 = $nal2 + $N3;
}

//proyeccion
//calculo a
$Aproy = (($nal2 * $G2) - ($N2*$MxG))/(($N*$nal2)-($N2*$N2));
//calculo B
$Bproy = (($N*$MxG)-($N2*$G2))/(($N*$nal2)-($N2 * $N2));

//COMPLETO LA MATRIZ PARA LA FUNCION GAUSS

$Matriz= array(
array(round($Xten),round($EXten),round($Xexp2),round($Xexp3),round($Yten)),
array(round($EXten),round($Xexp2),round($Xexp3),round($Xexp4),round($XY)),
array(round($Xexp2),round($Xexp3),round($Xexp4),round($Xexp5),round($Xexp2Y)),
array(round($Xexp3),round($Xexp4),round($Xexp5),round($Xexp6),round($Xexp3Y)),
);

/*echo $Matriz[0][0]."|".$Matriz[0][1]."|".$Matriz[0][2]."|".$Matriz[0][3]."|".$Matriz[0][4]."|"."<br>";
echo $Matriz[1][0]."|".$Matriz[1][1]."|".$Matriz[1][2]."|".$Matriz[1][3]."|".$Matriz[1][4]."|"."<br>";
echo $Matriz[2][0]."|".$Matriz[2][1]."|".$Matriz[2][2]."|".$Matriz[2][3]."|".$Matriz[2][4]."|"."<br>";
echo $Matriz[3][0]."|".$Matriz[3][1]."|".$Matriz[3][2]."|".$Matriz[3][3]."|".$Matriz[3][4]."|"."<br>";
echo "<br>";

echo "<br>";*/
//$Result = GausJordan($valor,4,4);
$Result = GausJordan($Matriz,4,5);

   

//echo $Result[0][0]."|".$Result[0][1]."|".$Result[0][2]."|".$Result[0][3]."|".$Result[0][4]."|"."<br>";
//echo $Result[1][0]."|".$Result[1][1]."|".$Result[1][2]."|".$Result[1][3]."|".$Result[1][4]."|"."<br>";
//echo $Result[2][0]."|".$Result[2][1]."|".$Result[2][2]."|".$Result[2][3]."|".$Result[2][4]."|"."<br>";
//echo $Result[3][0]."|".$Result[3][1]."|".$Result[3][2]."|".$Result[3][3]."|".$Result[3][4]."|"."<br>";



$a =  $Result[0][4]."<br>";
$b = $Result[1][4]."<br>";
$c = $Result[2][4]."<br>";
$d = $Result[2][4]."<br>";

//echo "A: ".$a."<br>";
//echo "B: ".$b."<br>";
//echo "C: ".$c."<br>";
//echo "D: ".$d."<br>";







// formula de tendencia final ax4 + bx3 + cx2 + dx + e = 0;

$consulta4 = "select extract(YEAR from (db_fecha)) AS ANO,extract(MONTH from (db_fecha)) AS MES,extract(DAY from (db_fecha)) AS DIA, DB_HOST AS HOST, DB_SID AS BASE, DB_TAMANO AS TAMANO from (SELECT * FROM db_stats UNION SELECT * FROM db_stats) where DB_SID = '".$row5->NOMBREDB."' AND DB_HOST = '".$host."' and db_fecha <= '".$fecha."'order by DB_FECHA asc";
?>
        </p>
      </blockquote></td>
  </tr>
  <tr>
    <td>
    <?php
    //CALCULO SI LA PROYECCION EN NEGATIVA
	
	$stid4 = oci_parse($con, $consulta4);
	oci_execute($stid4);
	while(($row4 = oci_fetch_object ($stid4)) != false){
	$ultimo = round($row4->TAMANO/1024/1024/1024,2);
	}

	
    $diferencia = (round(($Aproy + $Bproy * ($N+1)),2) - $ultimo);
	$primero = (round(($Aproy + $Bproy * ($N+1)),2) - $diferencia);
	for($i = 1; $i < 90; $i++){
	$ultimo2 = (round(($Aproy + $Bproy * ($N+$i)),2) - $diferencia);
	}	  
                  
if($ultimo2 - $primero <= 0.00){
	?>
    <script type="text/javascript">
	function <?PHP echo $nombredb;?>()
      {
	var contenedor = document.createElementById(<?PHP echo "'#container3".$nombredb."'";?>);
    var nuevaImagen = new Image();

        alert("Se procede a la carga en memoria de la imagen");
        nuevaImagen = cargarImagen("../img/alert.png");
        var imagen = new Image();
        imagen.onload = imagenCargada;
        imagen.src = url;
        return imagen;
	  }
		</script>
        <?php
}else{ 
?>                   
                    
      <script type="text/javascript">
$(function <?PHP echo $nombredb;?>() {
	
        $(<?PHP echo "'#container3".$nombredb."'";?>).highcharts({
            chart: {
                type: 'areaspline'
            },
            title: {
                text: 'Projected growth - <?php echo $nombredb ?>'
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'top',
                x: 150,
                y: 100,
                floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF'
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
                    text: 'Used GB'
                }
            },
            tooltip: {
                shared: true,
                valueSuffix: ' GB'
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                 area: {
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },

                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                },
				spline: {
                    lineWidth: 4,
                    states: {
                        hover: {
                            lineWidth: 5
                        }
                    },
                    marker: {
                        enabled: false
                    },
                    pointInterval: 3600000, // one hour
                    pointStart: Date.UTC(2009, 9, 6, 0, 0, 0)
                }

            },
            series: [{
                name: 'Used GB',
				type: 'area',
                data: [<?php
				
				$stid4 = oci_parse($con, $consulta4);
				oci_execute($stid4);
				while(($row4 = oci_fetch_object ($stid4)) != false){
					echo "[Date.UTC(".$row4->ANO.",".($row4->MES - 1).",".$row4->DIA."),".round($row4->TAMANO/1024/1024/1024,2)."],";$ultimo = round($row4->TAMANO/1024/1024/1024,2);}?>]
					
            },{
                name: 'Projected',
				type: 'area',
				color: '#990000',
                data: [<?php
				//traigo la fecha en formato fecha PHP
				$consulta6 = "select extract(YEAR from (db_fecha)) AS ANO, 									extract(MONTH from (db_fecha)) AS MES,extract(DAY from (db_fecha)) AS DIA, DB_HOST AS HOST, DB_SID AS BASE, DB_TAMANO AS TAMANO from (SELECT * FROM db_stats UNION SELECT * FROM db_stats) where DB_SID = '".$row5->NOMBREDB."' AND DB_HOST = '".$host."' and db_fecha = '".$fecha."'order by DB_FECHA asc";
				$stid6 = oci_parse($con, $consulta6);
				oci_execute($stid6);
				$consulta7= "SELECT distinct(TO_CHAR(db_fecha, 'MM/DD/YYYY')) as FECHA from db_stats where db_fecha = '".$fecha."'";
$stid7 = oci_parse($con, $consulta7);
oci_execute($stid7);
$row7 = oci_fetch_object ($stid7);
$fecha2 = $row7->FECHA;
				$fecha3 = date("m/d/Y", strtotime("$fecha2"));
				//CHANCHADA!
				
				$diferencia = (round(($Aproy + $Bproy * ($N+1)),2) - $ultimo);							
				for($i = 1; $i < 90; $i++){
   					$año= substr($fecha3, 6);
					$dia= substr($fecha3, 3, 2);
					$mes= substr($fecha3, 0,2);
					echo "[Date.UTC(".$año.",".($mes-1).",".$dia."),".(round(($Aproy + $Bproy * ($N+$i)),2) - $diferencia)."],";
					$fecha3 = date("m/d/Y", strtotime("$fecha3 +1 day"));
					}?>]
            }],
			chart: {
        events: {
            load: function () {
                var ch = this;
                setTimeout(function(){
                    ch.exportChart();
                },1);
            }
        }
    },
        });
    });
		</script>
        <?php
}
?>  
        
<table width="206" border="0" bgcolor="#948A54" cellspacing="0" cellpadding="0" align="center">
            <tr>
              <td align="center"><strong>Historical growth</strong></td>
              <p>&nbsp;</p>
            </tr>
            <tr>
              <td><table width="207" align="center">
                <tr bgcolor="#C4BD97">
                  <td><strong>Month</strong></td>
                  <td><strong>Used Gb</strong></td>
                  <td><strong>Delta Gb</strong></td>
                </tr>
                <?php 
				
				
				
				$stid = oci_parse($con, $consulta1);
				oci_execute($stid);	
				$delta = 0;
				$promediodelta = 0;
				$deltacant = 0;
				while(($row = oci_fetch_object ($stid)) != false){
				echo "<tr>";
				echo "<td bgcolor=\"#FFFFFF\" align=\"center\">".$row->FECHA."</td>";				
				echo "<td bgcolor=\"#FFFFFF\" align=\"center\">".round(($row->TOTAL )/1024/1024/1024,2)."</td>";
				if($delta <= 0){
					$delta2 = 0.00; 
					}else{
						$delta2 =(round(($row->TOTAL )/1024/1024/1024,2) - $delta);
				} 
				echo "<td bgcolor=\"#FFFFFF\" align=\"center\">".round(($delta2),2)."</td>";
				$delta = round(($row->TOTAL )/1024/1024/1024,2);
				$deltacant = $deltacant + 1;
				$promediodelta = ($promediodelta + $delta2);
					} 
				echo "</tr>";	?>
                <tr bgcolor="#C4BD97">
                  <td>&nbsp;</td>
                  <td><strong>Average</strong></td>
                  <td align="center"><?php echo round($promediodelta/($deltacant-1),2); ?></td>
                </tr>
              </table>
              <?php }?>
              </td>
            </tr>
        </table>
      <p>&nbsp;</p>
      <script type="text/javascript">
	  $(function <?PHP echo $nombredb;?>() {
        $(<?PHP echo "'#container2".$nombredb."'";?>).highcharts({
            chart: {
                zoomType: 'x',
                spacingRight: 10
            },
            title: {
                text: 'Accumulated growth <?php echo $row5->NOMBREDB;?>'
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
                    text: 'Used GB'
                }
            },
            tooltip: {
                shared: true
            },
            legend: {
                enabled: false
            },
             plotOptions: {
                 area: {
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },

                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
            series: [{
                type: 'area',
                name: 'GB',
				pointInterval: 24 * 3600 * 1000,
                data: [<?php
				

$stid4 = oci_parse($con, $consulta4);
oci_execute($stid4);
while(($row4 = oci_fetch_object ($stid4)) != false) {
echo "[Date.UTC(".$row4->ANO.",  ".($row4->MES - 1).", ".$row4->DIA."), ".round($row4->TAMANO/1024/1024/1024, 2)."],";
}?>]
            }],
			chart: {
        events: {
            load: function () {
				var nombre = "<?php echo "His".$host.$nombredb; ?>";
                var ch = this;
                setTimeout(function(){
                    ch.exportChart({filename : nombre});
                },1);
            }
        }
    }
        });
    });

      
      </script>
      <div id="<?PHP echo "container2".$nombredb."";?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
      <script type="text/javascript">
var d = document.getElementById('<?PHP echo "container2".$nombredb."";?>');
d.innerHTML = "<img src='../cal.gif' width='16' height='16'>";	
</script>
      <p>&nbsp;</p></td>

    </tr>
  <tr>
    <td><H3> 4.3.<?php echo $indice + 2; ?>. Projected growth of Data Base</H3>
      <p>
      <?php
	  if($cantidad < 2){
		echo "<div id=\"alerta\" class=\"alerta2\">";
	echo "The compiled information is smaller to 2 months for ".$nombredb."";
	echo "</div>";
}else{
	?>  
      <div id="<?PHP echo "container3".$nombredb."";?>"></div>
      
      <?php
      if($ultimo2 - $primero > 0.00){	 
	  echo "<p>- The projected information is extracted between ".$fechaanterior." and ".$fechapositiva." when the data base has a positive growth.</p>"; 
	  }else{
	  echo "<div id=\"alerta\" class=\"alerta2\">";
	echo "Is not possible make projeted with the information extracted.";
	echo "</div>";
	  }
	  
}   
	  ?></p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
        </table>
  <script type="text/javascript">		
$(function <?PHP echo $nombredb;?>() {	

    Highcharts.data({
        csv: document.getElementById(<?PHP echo "'tsv".$nombredb."'";?>).innerHTML,
        itemDelimiter: '\t',
        parsed: function (columns) {

            var brands = {},
                brandsData = [],
                versions = {},
                drilldownSeries = [];
            
            // Parse percentage strings
            columns[1] = $.map(columns[1], function (value) {
                if (value.indexOf('%') === value.length - 1) {
                    value = parseFloat(value);
                }
                return value;
            });

            $.each(columns[0], function (i, name) {
                var brand,
                    version;

                if (i > 0) {

                    // Remove special edition notes
                    name = name.split(' -')[0];

                    // Split into brand and version
                    version = name.match(/([0-9]+[\.0-9x]*)/);
                    if (version) {
                        version = version[0];
                    }
                    brand = name.replace(version, '');

                    // Create the main data
                    if (!brands[brand]) {
                        brands[brand] = columns[1][i];
                    } else {
                        brands[brand] += columns[1][i];
                    }

                    // Create the version data
                    if (version !== null) {
                        if (!versions[brand]) {
                            versions[brand] = [];
                        }
                        versions[brand].push(['' + version, columns[1][i]]);
                    }
                }
                
            });

            $.each(brands, function (name, y) {
                brandsData.push({ 
                    name: name, 
                    y: y,
                    drilldown: versions[name] ? name : null
                });
            });
            $.each(versions, function (key, value) {
                drilldownSeries.push({
                    name: key,
                    id: key,
                    data: value
                });
            });

            // Create the chart
            $(<?PHP echo "'#container".$nombredb."'";?>).highcharts({
                chart: {
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.y:.1f}%'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                }, 

                series: [{
                    name: '',
                    colorByPoint: true,
                    data: brandsData
                }],
                drilldown: {
                    series: drilldownSeries
                }
            })

        }
    });
});

		</script>
       
</p>
<p>&nbsp; </p>
<pre id="<?PHP echo "tsv".$nombredb;?>" style="display:none">lalala lalaal	lalala lalala lalala
<?php
echo "USED 	".(($total-$disponible)*100)/$total."%\n";
echo "AVAILABLE 	".($disponible*100)/$total."%\n";
?>
</pre>
<?php
}
/*, {
                name: 'Tendency',
				color: '#F60',
				type: 'spline',
                data: [<?php
				
				$stid4 = oci_parse($con, $consulta4);
				oci_execute($stid4);
				$num = 0;
				$b2 = $b;
				while(($row4 = oci_fetch_object ($stid4)) != false){
					
					$dato = $a + $b*$num + $c*pow($num,2)+$d*pow($num,3);
					
					echo "[Date.UTC(".$row4->ANO.",".($row4->MES - 1).",".$row4->DIA."),".round($dato,2)."],";
					$num = $num +1;}
					$consulta7= "SELECT distinct(TO_CHAR(db_fecha, 'MM/DD/YYYY')) as FECHA from 			db_stats where db_fecha = '".$_GET['fecha']."'";
$stid7 = oci_parse($con, $consulta7);
oci_execute($stid7);
$row7 = oci_fetch_object ($stid7);
$fecha2 = $row7->FECHA;
for($i = 1; $i < 90; $i++){
$dato = $a + $b*$num + $c*pow($num,2)+$d*pow($num,3);
   					$año= substr($fecha3, 6);
					$dia= substr($fecha3, 3, 2);
					$mes= substr($fecha3, 0,2);
					echo "[Date.UTC(".$año.",".($mes-4).",".$dia."),".round($dato,2)."],";
					$fecha3 = date("m/d/Y", strtotime("$fecha3 +1 day"));
					$num = $num + 1;
					}?>}]*/
?>
