<!DOCTYPE HTML>
<html><head>

    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>TPU </title>

	
<?php
$host =  $_GET['host'];	



		if (isset($_GET['desde'])){
			$desde =  $_GET['desde'];	
		} 
		else {
			$desde = "";
		}
		
		if (isset($_GET['hasta'])){
			$hasta =  $_GET['hasta'];	
		} 
		else {
			$hasta = "";
		}


$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$consulta = "select (cpu_usr * 5 +cpu_sys *5)  as CPU
,500  as TPU
,131072 as mem_asigned
,(MEMORIA * 131072 / 100) as memoria
,TO_CHAR(fecha,'DD') as dia
,TO_CHAR(fecha,'MM') as mes
,TO_CHAR(fecha,'YYYY') as anio
,TO_CHAR(fecha,'HH24') as hora
,TO_CHAR(fecha,'MI') as minuto
from host_stats
where host like '".$host."'";
if ( $desde != "") {
	$consulta=$consulta."
and fecha > '".$desde."' " ; }
if ( $hasta != "") {
	$consulta=$consulta."
and fecha < '".$hasta."' ";
}
$consulta=$consulta."
order by fecha asc";

$stid = oci_parse($con,"ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '. '");

oci_execute($stid);

$stid = oci_parse($con, $consulta);
	oci_execute($stid);	
	$num=0;
	$CPU='';
	$TPU='';
	$MEM='';
	$M_ASIGNED='';
	while(($row = oci_fetch_object ($stid)) != false) {
		if ( $num == 0 ){
		$CPU=$row->CPU;
		$TPU=$row->TPU;
		$MEM=$row->MEMORIA;
		$M_ASIGNED=$row->MEM_ASIGNED;
		$num=1;
		$dia=$row->DIA;
		$mes=$row->MES -1;
		$anio=$row->ANIO;
		$hora=$row->HORA;
		$min=$row->MINUTO;
		}
		else {
			$CPU=$CPU.", ".$row->CPU;
			$TPU=$TPU.", ".$row->TPU;
			$MEM=$MEM.", ".$row->MEMORIA;
			$M_ASIGNED=$M_ASIGNED.", ".$row->MEM_ASIGNED;
		}
	}
	

?>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">

$(function () {
        $('#CPU').highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: 'CPU USE %'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    overflow: 'justify'
                }
            },
            yAxis: {
                title: {
                    text: 'TPU USE'
                }
            },
            tooltip: {
                valueSuffix: ' m/s'
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
                    pointInterval: 600000, // 10 min
                    pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", 0, 0, 0)"; ?>
                }
            },
            series: [{
                name: 'CPU',
                data: [<?php 
						echo $CPU;
						?>]
    
            }, {
                name: 'TPU',
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
                text: 'Memory Use'
            },
            subtitle: {
                text: null
            },
            xAxis: {
				type: 'datetime',
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
                name: 'ASSIGNED',
				pointInterval: 600000,
                pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", ".$hora." , ".$min.", 0)"; ?>,
                data: [<?php 
						echo $M_ASIGNED;
						?>]
				}, {
                name: 'MEM',
				pointInterval: 600000,
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
<script src="../../js/highcharts.js"></script>


<div id="CPU" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<div id="MEM" style="min-width: 310px; height: 400px; margin: 0 auto"></div>


	</body>
</html>
