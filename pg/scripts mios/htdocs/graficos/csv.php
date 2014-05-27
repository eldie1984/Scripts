<?php
//SI EL ARCHIVO SE ENVIÃ" Y ADEMÃS SE SUBIO CORRECTAMENTE
if (isset($_FILES["archivo"]) && is_uploaded_file($_FILES['archivo']['tmp_name'])) {
$fp = fopen ($_FILES['archivo']['tmp_name'], "r" ); 
while (( $data = fgetcsv ( $fp , 1000 , "," )) !== FALSE ) { // Mientras hay líneas que leer... 
$i = 0; 
foreach($data as $row) {
echo "Campo $i: $row<br>n"; // Muestra todos los campos de la fila actual 
$i++ ; 
} 
echo "<br><br>nn";
 } 
fclose ( $fp ); 
  
} else 
 echo "Error de subida";
?>	

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
$(function () {
        $('#cambios').highcharts({
    
            chart: {
                type: 'column'
            },
    
            title: {
                text: 'Total fruit consumtion, grouped by gender'
            },
    
            xAxis: {
                categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Number of fruits'
                }
            },
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
    
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
    
            series: [{
                name: 'TRACE',
                data: [5, 3, 4, 7, 2],
                stack: 'male'
            }, {
                name: 'VERSION',
                data: [3, 4, 4, 2, 5],
                stack: 'male'
            }, {
                name: 'TRT EXCEPT.',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            }, {
                name: 'MODVERS',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            },{
                name: 'PROGRES',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            },{
                name: 'PARAM',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            },{
                name: 'MAINPRE',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            },{
                name: 'CONSIGN',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            },{
                name: 'CORINCI',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            }]
        });
    });
    

		</script>
        <div id="cambios" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

        <script type="text/javascript">
$(function() {
        $('#incidentes').highcharts({
    
            chart: {
                type: 'column'
            },
    
            title: {
                text: 'Total fruit consumtion, grouped by gender'
            },
    
            xAxis: {
                categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Number of fruits'
                }
            },
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
    
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
    
            series: [{
                name: 'NONE',
                data: [5, 3, 4, 7, 2],
                stack: 'male'
            }, {
                name: 'SLAVE',
                data: [3, 4, 4, 2, 5],
                stack: 'male'
            }, {
                name: 'MASTER',
                data: [2, 5, 6, 2, 1],
                stack: 'male'
            }, {
                name: 'OTHERS',
                data: [3, 0, 4, 4, 3],
                stack: 'male'
            }]
        });
    });
    

		</script>
	</head>
	<body>
<script src="../../js/highcharts.js"></script>


<div id="incidentes" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

	</body>
</html>