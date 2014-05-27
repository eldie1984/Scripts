<!DOCTYPE HTML>
<html><head>

    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>TPU </title>
<?php
$host =  $_GET['host'];	
$indice3 =  $_GET['indice3'];

if (isset($_GET['fecha'])){
	$g_fecha =  $_GET['fecha'];	
} 
else {
	$g_fecha = date('d/m/Y');
}


$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$consulta = "select 
TO_CHAR(fecha,'YYYY') as anio
,TO_CHAR(fecha,'MM') as mes
,TO_CHAR(fecha,'DD') as dia
,round(avg(cpu_usr * tpu / 100 +cpu_sys * tpu / 100),2) as CPU,  TPU 
,round(avg((MEMORIA * mem_assigned / 100 ) /1024),2) as memoria,  (mem_assigned /1024) as mem_assigned 
from host_stats
where host like '".$host."'
and fecha >= to_char(ADD_MONTHS(TO_DATE('".$g_fecha."','DD-MM-YYYY'), -12))
and fecha < '".$g_fecha."' 
group by TO_CHAR(fecha,'YYYY'), TO_CHAR(fecha,'MM'), TO_CHAR(fecha,'DD'), TPU, mem_assigned
order by TO_CHAR(fecha,'YYYY'),TO_CHAR(fecha,'MM'),TO_CHAR(fecha,'DD') asc";

$stid = oci_parse($con,"ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '. '");

oci_execute($stid);

$stid = oci_parse($con, $consulta);
	oci_execute($stid);	
	$num=0;
	$CPU='';
	$TPU='';
	while(($row = oci_fetch_object ($stid)) != false) {
		if ( $num == 0 ){
		$CPU=$row->CPU;
		$TPU=$row->TPU;
		$MEM=$row->MEMORIA;
		$M_ASIGNED=$row->MEM_ASSIGNED;
		$num=1;
		$dia=$row->DIA;
		$mes=$row->MES -1;
		$anio=$row->ANIO;
		}
		else {
			$CPU=$CPU.", ".$row->CPU;
			$TPU=$TPU.", ".$row->TPU;
			$MEM=$MEM.", ".$row->MEMORIA;
			$M_ASIGNED=$M_ASIGNED.", ".$row->MEM_ASSIGNED;
		}
	}
	

?>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
$(function () {
        $('#container').highcharts({
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
                    pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", 0, 0, 0)"; ?>
                }
            },
            series: [{
                name: 'TPU_ALLOC',
                data: [<?php 
						echo $CPU;
						?>]
    
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
$(function memoria() {
		$('#MEM').highcharts({
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
<script src="../../js/modules/exporting.js"></script>

<?php echo "<h3>                     4.1.".$indice3.".1.       Consumption TPU</h3><br>\n";?>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php
echo "<h3>                     4.1.".($indice3).".2.       Consumption MEM</h3><br>\n";
?>
<div id="MEM" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</body>
</html>
