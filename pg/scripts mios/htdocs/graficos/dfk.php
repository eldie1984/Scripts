<!DOCTYPE HTML>
<html><head>

    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>File Systems - Storage - <?php echo $host; ?></title>

	
<?php

$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

$host =  $_GET['host'];	
$fecha =  $_GET['fecha'];
$userbk =  $_GET['userbk'];
//$aplicacion = htmlspecialchars(trim($_POST['aplicacion']));

$usadotot = 0;
$total = 0;
$dataspaceused = 0;
$userstotal = 0;
$usernobk = 0;
//tamaño usado del userbk
$userbkusado = 0;
$SOusado = 0;
$usernobkfree = 0;

$consulta = "SELECT * FROM FILESYSTEMS WHERE FECHA LIKE '".$fecha."' AND TAMANIO>0 AND HOST LIKE '".$host."'";
$stid = oci_parse($con, $consulta);
oci_execute($stid);																				
$row = oci_fetch_object($stid);
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">		
$(function () {	

    Highcharts.data({
        csv: document.getElementById('tsv').innerHTML,
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
            $('#container').highcharts({
                chart: {
                    type: 'pie'
                },
                title: {
                    text: '<?php echo "File Systems - Storage - ".$row->HOST ?>'
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
				}
				
            })

        }
    });
});
    

		</script>
    <link href="../style.css" rel="stylesheet" type="text/css">
    <?php
$stid = oci_parse($con, $consulta);
oci_execute($stid);		
while(($row = oci_fetch_object ($stid)) != false) {
$total = $total + $row->TAMANIO;
$usadotot = $usadotot + $row->USADO;

}

?>
    <style type="text/css">
    body {
	background-color: #E8E8E8;
}
    </style>
</head>
<body>
<div id="content">
<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/data.js"></script>
<script src="../../js/modules/drilldown.js"></script>
<script src="../../js/modules/exporting.js"></script>

<!-- Data from www.netmarketshare.com. Select Browsers => Desktop share by version. Download as tsv. -->


<table width="200" border="0" align="center">
  <tr>
    <td><table border="1" cellspacing="0" cellpadding="0" width="658">
      <tr>
        <td width="135" align="center" valign="middle"><p><strong>&nbsp;</strong><strong>DP/DSIN/PDA</strong></p></td>
        <td width="401" align="center" valign="middle"><p><strong>&nbsp;</strong><strong><img src="../img/dfk_clip_image001.png" alt="" width="211" height="34"></strong></p></td>
        <td width="114" align="center" valign="middle"><p><strong>Documentatión</strong></p></td>
        </tr>
      <tr>
        <td width="135" align="center" valign="middle"><p><strong> </strong><strong>usage interne </strong></p></td>
        <td width="401" align="center" valign="middle"><p id="cusco_central"><strong>&nbsp;<em><strong><?php 
	$consulta2 = "SELECT * FROM USERBK WHERE HOST LIKE '".$host."'";
$stid2 = oci_parse($con, $consulta2);
oci_execute($stid2);																				
$row2 = oci_fetch_object($stid2);	
echo $row2->APP; ?></strong></em></strong></p>
          <p><strong>Report of Study Performance</strong></p></td>
        <td width="114" align="center" valign="middle"><p><strong>GENERAL</strong></p></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="center"><h3>&nbsp;</h3>
      <h3>4.2.1 Analysis of Storage File System Unix  <?php echo $host; ?> – General</h3>
      <table width="677" height="42" border="0">
        <tr>
          <td align="left">Available free space  on Files System existing on the virtual server <?php echo $host; ?>.</td>
        </tr>
      </table>
      <table width="360" border="0" align="center">
        <tr>
          <td width="13" bgcolor="#FCD5B4">&nbsp;</td>
          <td width="158" align="left">Operative System.</td>
          <td width="11" align="left" bgcolor="#B8CCE4">&nbsp;</td>
          <td width="160" align="left">Software.</td>
        </tr>
        <tr>
          <td bgcolor="#CCFFCC">&nbsp;</td>
          <td align="left">Data and Archives.</td>
          <td align="left" bgcolor="#B1A0C7">&nbsp;</td>
          <td align="left">Backup.</td>
        </tr>
</table>
      <table width="681" height="199" border="0">
        <tr>
          <td align="left"><p>The   File Systems (·) dedicated Application they   contain</p>
            <p>. Programs the Application.<br>
              . Logs of processes of the Application.<br>
              . Interfaces.<br>
              . Datafiles (Bd Oracle).<br>
              . Archives (Bd Oracle).<br>
            . Compressed files for Backup.</p></td>
        </tr>
  </table></td>
  </tr>
  <tr>
    <td><p>&nbsp;</p>
      <table width="678" height="458" border="0">
        <tr>
          <td align="right" bgcolor="#FFFFFF"><?php
$stid = oci_parse($con, $consulta);
oci_execute($stid);
 echo "<table cellpadding='0' cellspacing='0' width='700' bgcolor='#FFFFFF'>";
 echo "	  <thead><tr><td width='2%'align='center'><p> FILESYSTEM</p></td>";
 echo "	  <td width='6%' align='center'>TOTAL KB</td>";
 echo "	  <td width='6%'align='center'>USED</td>";
 echo "	  <td width='6%'align='center'>AVAIL</td>";
  echo "	  <td width='6%'align='center'>% Ocup</td>";
 echo "	  <td width='9%'align='center'>MOUNTED</td>";
 echo "	  </tr></thead>";
while(($row = oci_fetch_object($stid)) != false) {
	if($row->PUNTO_MONTAJE == $userbk){
	$userstotal = $userstotal + $row->TAMANIO;
	$userbkusado = $row->USADO;
	$tamanio = $row->TAMANIO;
	 $disponible= $tamanio - $userbkusado;
	 $userbkporc = ($row->USADO*100)/$row->TAMANIO;
	 $userbkdisp = $disponible;
        echo "<tr bgcolor='#B1A0C7'><td align='left'>".$row->NOMBREFS."</td>";
		echo "<td align='right'>".$row->TAMANIO."</td>";
		echo "<td align='right'>".$row->USADO."</td>";
		echo "<td align='right'>".$disponible."</td>";
				echo "<td align='center'>".round($userbkporc)."%    </td>";
		echo "<td align='left'>   ".$row->PUNTO_MONTAJE."</td>";
		echo "</tr>";
	}else{
	$resultado = strpos($row->PUNTO_MONTAJE, "/user");
	if($resultado !== FALSE){
	$userstotal = $userstotal + $row->TAMANIO;
	$tamanio = $row->TAMANIO;
	 $usado2 = $row->USADO;
	 $usernobkfree = $usernobkfree + ($row->TAMANIO - $row->USADO);
	 $usernobk = $usernobk + $usado2;
	 $disponible= $tamanio - $usado2;
        echo "<tr bgcolor='#CCFFCC'><td align='left'>".$row->NOMBREFS."</td>";
		echo "<td align='right'>".$row->TAMANIO."</td>";
		echo "<td align='right'>".$row->USADO."</td>";
		echo "<td align='right'>".$disponible."</td>";
				echo "<td align='center'>".round(($usado2*100)/$tamanio)."%    </td>";
		echo "<td align='left'>   ".$row->PUNTO_MONTAJE."</td>";
		echo "</tr>";
	}else{
		$resultado1 = strpos($row->PUNTO_MONTAJE, "/soft");
	if($resultado1 !== FALSE){
	$SOusado = $SOusado + $row->TAMANIO;	
	$tamanio = $row->TAMANIO;
	 $usado2 = $row->USADO;
	 $disponible= $tamanio - $usado2;
        echo "<tr bgcolor='#B8CCE4'><td align='left'>".$row->NOMBREFS."</td>";
		echo "<td align='right'>".$row->TAMANIO."</td>";
		echo "<td align='right'>".$row->USADO."</td>";
		echo "<td align='right'>".$disponible."</td>";
				echo "<td align='center'>".round(($usado2*100)/$tamanio)."%    </td>";
		echo "<td align='left'>   ".$row->PUNTO_MONTAJE."</td>";
		echo "</tr>";
	}else{
	$SOusado = $SOusado + $row->TAMANIO;
	 $tamanio = $row->TAMANIO;
	 $usado2 = $row->USADO;
	 $disponible= $tamanio - $usado2;
        echo "<tr bgcolor='#FCD5B4'><td align='left'>".$row->NOMBREFS."</td>";
		echo "<td align='right'>".$row->TAMANIO."</td>";
		echo "<td align='right'>".$row->USADO."</td>";
		echo "<td align='right'>".$disponible."</td>";
				echo "<td align='center'>".round(($usado2*100)/$tamanio)."%    </td>";
		echo "<td align='left'>   ".$row->PUNTO_MONTAJE."</td>";
		echo "</tr>";
	}
	}
	}
 }
echo "<table cellpadding='0' cellspacing='0' width='700' bgcolor='#FFFFFF'>";
 echo "	  <thead><tr><td width='2%'align='center'><p> TOTAL</p></td>";
 echo "	  <td width='6%'align='center'>". round(($total/1024/1024),2)." GB </td>";
 echo "	  <td width='6%' align='center'>USED</td>";
 echo "	  <td width='6%'align='center'>". round(($usadotot/1024/1024),2)." GB </td>";
 echo "	  <td width='6%'align='center'>FREE</td>";
 $free = $total - $usadotot;
 $TOTAL = ($usernobkfree + $usernobk + $userbkusado + $userbkdisp + $SOusado)/1024/1024;
  echo "	  <td width='6%'align='center'>". round(($free/1024/1024),2)." GB </td>";
 echo "	  </tr></thead>";		
	echo "</table>";

		?>
            <pre id="tsv" style="display:none">lalala lalaal	lalala lalala lalala
<?php
echo "DB FREE 	".((($usernobkfree)/1024/1024)*100)/$TOTAL."%\n";
echo "DB USED 	".((($usernobk)/1024/1024)*100)/$TOTAL."%\n";
echo "BKP USED	".((($userbkusado)/1024/1024)*100)/$TOTAL."%\n";
echo "BKP FREE	".((($userbkdisp)/1024/1024)*100)/$TOTAL."%\n";
echo "S.O.	".((($SOusado)/1024/1024)*100)/$TOTAL."%\n";
?>
</pre>
            <p>&nbsp;</p>
              
            
            <table width="624" border="0" align="center" bgcolor="#FFC875">
              <tr>
                <td align="center" bgcolor="#FFC875"><strong>Storage Summary  Gb – <?php echo $host; ?></strong></td>
              </tr>
              <tr>
                <td><table width="618" border="0">
                  <tr bgcolor="#FFFF99">
                    <td align="center"><strong>DB Used</strong></td>
                    <td align="center"><strong>DB Free</strong></td>
                    <td align="center"><strong>S.O.</strong></td>
                    <td align="center"><strong>Bkp Used</strong></td>
                    <td align="center"><strong>Bkp Free</strong></td>
                  </tr>
                  <tr>
                    <td align="center" bgcolor="#DFDFDF"><?php echo round(($usernobk/1024/1024),2); ?></td>
                    <td align="center" bgcolor="#DFDFDF"><?php echo round((($usernobkfree)/1024/1024),2); ?></td>
                    <td align="center" bgcolor="#DFDFDF"><?php echo round((($SOusado)/1024/1024),2); ?></td>
                    <td align="center" bgcolor="#DFDFDF"><?php echo round(($userbkusado/1024/1024),2); ?></td>
                    <td align="center" bgcolor="#DFDFDF"><?php echo round(($userbkdisp/1024/1024),2); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table>
          </tr>
        <tr>
          <td width="1321" height="402" align="left" ><div id="container" "style="min-width: 310px; height: 400px; margin: 0 auto"></div></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="right"><?php 
	$consulta4 = "select count(*)as fecha, host from (select distinct(last_day(fecha)) as fecha, host from tablespaces where host = '".$host."') group by host";
$stid4 = oci_parse($con, $consulta4);
oci_execute($stid4);
$row4 = oci_fetch_object ($stid4);
if($row4->HOST == $host && $row4->FECHA > 1){
?><a href="tbs.php?host=<?php echo $host;?>&fecha=<?php echo $fecha?>">SEE TABLESPACE</a><?php
}

	?></td>
  </tr>
  </table>
<p></p>
</div>

</form>
</body>
</html>


