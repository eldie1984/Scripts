<!DOCTYPE HTML>
<html><head>

    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>CPU USE </title>

	
<?php

$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}


?>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
$(function () {
	$('#container_now').highcharts({
	
	    chart: {
	        type: 'gauge',
	        plotBorderWidth: 1,
	        plotBackgroundColor: {
	        	linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	        	stops: [
	        		[0, '#FFF4C6'],
	        		[0.3, '#FFFFFF'],
	        		[1, '#FFF4C6']
	        	]
	        },
	        plotBackgroundImage: null,
	        height: 200
	    },
	
	    title: {
	        text: 'YVASA850'
	    },
	    
	    pane: [{
	        startAngle: -45,
	        endAngle: 45,
	        background: null,
	        center: ['25%', '145%'],
	        size: 300
	    }, {
	    	startAngle: -45,
	    	endAngle: 45,
	    	background: null,
	        center: ['75%', '145%'],
	        size: 300
	    }],	    		        
	
	    yAxis: [{
	        min: 0,
	        max: 100,
	        minorTickPosition: 'outside',
	        tickPosition: 'outside',
	        labels: {
	        	rotation: 'auto',
	        	distance: 20
	        },
	        plotBands: [{
	        	from: 80,
	        	to: 100,
	        	color: '#C02316',
	        	innerRadius: '100%',
	        	outerRadius: '105%'
	        }],
	        pane: 0,
	        title: {
	        	text: 'CPU<br/><span style="font-size:8px">%</span>',
	        	y: -40
	        }
	    }, {
	        min: 0,
	        max: 100,
	        minorTickPosition: 'outside',
	        tickPosition: 'outside',
	        labels: {
	        	rotation: 'auto',
	        	distance: 20
	        },
	        plotBands: [{
	        	from: 70,
	        	to: 100,
	        	color: '#C02316',
	        	innerRadius: '100%',
	        	outerRadius: '105%'
	        }],
	        pane: 1,
	        title: {
	        	text: 'MEM<br/><span style="font-size:8px">%</span>',
	        	y: -40
	        }
	    }],
	    
	    plotOptions: {
	    	gauge: {
	    		dataLabels: {
	    			enabled: false
	    		},
	    		dial: {
	    			radius: '100%'
	    		}
	    	}
	    },
	    	
	
	    series: [{
	        data: [0],
	        yAxis: 0
	    }, {
	        data: [0],
	        yAxis: 1
	    }]
	
	},
	
	
	// Let the music play
	function(chart) {
	    setInterval(function() {
	        var left = chart.series[0].points[0],
	            right = chart.series[1].points[0],
	            leftVal, 
				inc=0
				
        
	            inc = (<?php 
				$consulta = "select cpu_usr, memoria from host_stats where fecha in (select max(fecha) from host_stats) ";
$stid = oci_parse($con, $consulta);
oci_execute($stid);	
$row = oci_fetch_object($stid);
echo $row->CPU_USR;															
				?>)
				inc2= (<?php 
				echo $row->MEMORIA;															
				?>)
				
	
	        leftVal =  inc;
	        rightVal = inc2;
	        
	
	        left.update(leftVal, false);
	        right.update(rightVal, false);
	        chart.redraw();
	
	    }, 500);
	
	});
});

		</script>
	</head>
	<body>
<script src="../../js/highcharts.js"></script>
<script src="../../js/highcharts-more.js"></script>
<script src="../../js/modules/exporting.js"></script>

<div id="container_now" style="width: 600px; height: 300px; margin: 0 auto"></div>

	</body>
</html>

