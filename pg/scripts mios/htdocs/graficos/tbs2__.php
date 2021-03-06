<!DOCTYPE HTML>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/data.js"></script>
<script src="../../js/modules/drilldown.js"></script>
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

//Espacio disponible tablespace

$Disponible = 0;
$consulta = "select (db_host)as HOST, (db_fecha)as FECHA, db_sid, (db_tamano) as TOTAL from db_stats where db_fecha <= '".$fecha."' AND db_host LIKE '".$host."' AND db_sid LIKE '".$nombredb."' AND db_fecha = last_day(db_fecha) AND rownum <= 10  order by db_fecha asc";
	
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

$MxG = $MxG + ($ocupado * $N);
//N2 y G2 para calcular b (mes + mes + ...) * (GB + GB + ....)
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

//tendencia
//calculo a
$a = $N * $MxG;	
//calculo B
$b = $N2 * $G2;
//calculo c
$c = $N * $n4;
//calculo d = (mes + mes + mes)^2
$d = $N2 * $N2;
//calculo m = (a-b)/(c-d)
$m = ($a-$b)/($c-$d);
//calculo e 
$e = $G2;
//calculo f
$f = $m * $N2;
//calculo b2
$b2 = ($e - $f)/$N;

// formula de tendencia final ax4 + bx3 + cx2 + dx + e = 0;

?>
        </p>
      </blockquote></td>
  </tr>
  <tr>
    <td>	  
      <script type="text/javascript">
$(function <?PHP echo $nombredb;?>() {
	
        $(<?PHP echo "'#container3".$nombredb."'";?>).highcharts({
            chart: {
                type: 'areaspline'
            },
            title: {
                text: 'Projected growth - <?php echo $nombredb." - ".$fecha; ?>'
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
                categories: [<?php 
				$stid = oci_parse($con, $consulta);
	oci_execute($stid);	
	$n = -1;
				while(($row = oci_fetch_object ($stid)) != false) {
					$n = $n + 1;
					echo "'".substr($row->FECHA, -5)."',";}
					?>'04/14','05/14','06/14'],
                plotBands: [{ // visualize the weekend
                    from: <?php echo $n; ?>,
                    to: <?php echo $n + 3; ?>,
                    color: 'rgba(68, 170, 213, .2)'
                }]
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
                areaspline: {
                    fillOpacity: 0.6
                }
            },
            series: [{
                name: 'Projected',
				color: '#990000',
                data: [<?php
				$stid = oci_parse($con, $consulta);
				oci_execute($stid);	
				while(($row = oci_fetch_object ($stid)) != false) { 
				$ocupado = ($row->TOTAL)/1024/1024/1024;
				echo round($ocupado,2).","; 
				}?>,<?php echo round(($Aproy + $Bproy * ($N+1)),2); ?>,<?php echo round(($Aproy + $Bproy * ($N+2)),2); ?>,<?php echo round(($Aproy + $Bproy * ($N+3)),2); ?>]
            }, {
                name: 'Used GB',
                data: [<?php
				$stid = oci_parse($con, $consulta);
				oci_execute($stid);	
				while(($row = oci_fetch_object ($stid)) != false) { 
				$ocupado = ($row->TOTAL)/1024/1024/1024;
				echo round($ocupado,2).","; 
				}?>]
            }, {
                name: 'Tendency',
				color: '#F60',
				type: 'spline',
                data: [<?php
				for($num=1; $num < $N+4; $num++){
				echo round(($m*$num)+$b2,2).",";}?>]
            }]
        });
    });
		</script>
        
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
                <?php $stid = oci_parse($con, $consulta);
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
                spacingRight: 20
            },
            title: {
                text: 'Accumulated growth <?php echo $row5->NOMBREDB;?>'
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                    '' :
                    ''
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
				$consulta4 = "select extract(YEAR from (db_fecha)) AS ANO, 									extract(MONTH from (db_fecha)) AS MES,extract(DAY from (db_fecha)) AS DIA, DB_HOST AS HOST, DB_SID AS BASE, DB_TAMANO AS TAMANO from (SELECT * FROM db_stats UNION SELECT * FROM db_stats) where DB_SID = '".$row5->NOMBREDB."' AND DB_HOST = '".$host."' and db_fecha <= '".$fecha."'order by DB_FECHA asc";

$stid4 = oci_parse($con, $consulta4);
oci_execute($stid4);
while(($row4 = oci_fetch_object ($stid4)) != false) {
echo "[Date.UTC(".$row4->ANO.",  ".($row4->MES - 1).", ".$row4->DIA."), ".round($row4->TAMANO/1024/1024/1024, 2)."],";
}?>]
            }]
        });
    });

      
      </script>
      <div id="<?PHP echo "container2".$nombredb."";?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
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
      <div id="<?PHP echo "container3".$nombredb."";?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div><?php } ?></p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
        </table>
<p>&nbsp;  </p>
<p>
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
?>