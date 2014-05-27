<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chequeo de 7 am</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script>
$(function() {
    var moveLeft = 0;
    var moveDown = 0;
    $('a.popper').hover(function(e) {

        var target = '#' + ($(this).attr('data-popbox'));

        $(target).show();
        moveLeft = $(this).outerWidth();
        moveDown = ($(target).outerHeight() / 2);
    }, function() {
        var target = '#' + ($(this).attr('data-popbox'));
        $(target).hide();
    });

    $('a.popper').mousemove(function(e) {
        var target = '#' + ($(this).attr('data-popbox'));

        leftD = e.pageX + parseInt(moveLeft);
        maxRight = leftD + $(target).outerWidth();
        windowLeft = $(window).width() - 40;
        windowRight = 0;
        maxLeft = e.pageX - (parseInt(moveLeft) + $(target).outerWidth() + 20);

        if(maxRight > windowLeft && maxLeft > windowRight)
        {
            leftD = maxLeft;
        }

        topD = e.pageY - parseInt(moveDown);
        maxBottom = parseInt(e.pageY + parseInt(moveDown) + 20);
        windowBottom = parseInt(parseInt($(document).scrollTop()) + parseInt($(window).height()));
        maxTop = topD;
        windowTop = parseInt($(document).scrollTop());
        if(maxBottom > windowBottom)
        {
            topD = windowBottom - $(target).outerHeight() - 20;
        } else if(maxTop < windowTop){
            topD = windowTop + 20;
        }

        $(target).css('top', topD).css('left', leftD);


    });

});
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<style media="screen" type="text/css">

#content {
position:relative;
left:-150px;
width: 620px;
margin: 10px auto;
padding: 20px;
background: #FFF; 
border: 1px solid #CCC;
}

.popbox {
    display: none;
    position: absolute;
    z-index: 99999;
    width: 620px;
    padding: 10px;
    background: #EEEFEB;
    color: #000000;
    border: 1px solid #4D4F53;
    margin: 0px;
    -webkit-box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
    box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
}
.popbox h2
{
    background-color: #4D4F53;
    color:  #E3E5DD;
    font-size: 14px;
    display: block;
    width: 100%;
    margin: -10px 0px 8px -10px;
    padding: 5px 10px;
}


body {
	background-color: #CCC;
}
</style>
<?php 
set_time_limit (50000);
$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

$consulta = "select id,host from userbk where id in (select distinct(host) from ejecuciones)";
$stid = oci_parse($con, $consulta);
oci_execute($stid);
?>
</head>
<body>
<?php 

$consulta3 = "select host,sesion from ejecuciones group by host,sesion";
$stid3 = oci_parse($con, $consulta3);
oci_execute($stid3);
while(($row3 = oci_fetch_object ($stid3)) != false) {
	$consulta4="select uproc,estado,TO_CHAR(inicio,'DD-MM-YY HH24:MI:SS') as inicio, TO_CHAR(fin,'DD-MM-YY HH24:MI:SS') as fin 
from ejecuciones
where host='".$row3->HOST."'
and sesion='".$row3->SESION."'";

echo "<div id=\"".$row3->SESION.$row3->HOST."\" class=\"popbox\">
    <table width=\"612\" border=\"0\">
              <tr bgcolor=\"#FFFF99\">
                <td align=\"center\"><strong>UPROC</strong></td>
                <td align=\"center\"><strong>Estado</strong></td>
				<td align=\"center\" ><strong>Inicio</strong></td>
				<td align=\"center\"><strong>Fin</strong></td>
				
              </tr>";
			  $stid4 = oci_parse($con, $consulta4);
	oci_execute($stid4);
	while(($row4 = oci_fetch_object ($stid4)) != false) {
			  echo "<tr><td align=\"center\">".$row4->UPROC."</td>";
				if (( $row4->ESTADO == 3 )){
                	echo '<td align="center" bgcolor="#00FF00">TERMINE</td>';
				}
				elseif (( $row4->ESTADO == 5)){
					echo '<td align="center" bgcolor="#0066FF">EXECUTION_EN_COURS</td>';
				}
				elseif (( $row4->ESTADO == 4)){
					echo '<td align="center" bgcolor="#FF9933">HORAIRE_DEPASSE</td>';
				}
				elseif (( $row4->ESTADO == 2)){
					echo '<td align="center" bgcolor="#FF0000">INCIDENTE</td>';
				}
				elseif (( $row4->ESTADO == 1)){
					echo '<td align="center" bgcolor="#FFFF00">ATTENTE_EVENEMENT</td>';
				}
				else{
					echo '<td align="center" ></td>';
				}
				echo "<td align=\"center\">".$row4->INICIO."</td>
				 <td align=\"center\">".$row4->FIN."</td>
				  </tr>";
				
	}
	echo "</table>
</div>";
}
?>

<?php while(($row = oci_fetch_object ($stid)) != false) { 
 echo "<h1>".$row->HOST."</h1>";
 echo "
 <div id=\"content\">
		<table width=\"612\" border=\"0\">
              <tr bgcolor=\"#FFFF99\">
                <td align=\"center\"><strong>Sesion</strong></td>
                <td align=\"center\"><strong>Estado</strong></td>
				<td align=\"center\" ><strong>Inicio</strong></td>
				<td align=\"center\"><strong>Fin</strong></td>
				
              </tr>";
	$consulta2="select sesion,estado,TO_CHAR(min(inicio),'DD-MM-YY HH24:MI:SS') as inicio, TO_CHAR(max(fin),'DD-MM-YY HH24:MI:SS') as fin 
from ejecuciones
where host='".$row->ID."'
and sesion not in (select sesion from ejecuciones where estado <> 3) group by sesion, estado
union 
select sesion,estado,TO_CHAR(min(inicio),'DD-MM-YY HH24:MI:SS') as inicio, '00-00-00 00:00:00' as fin 
from ejecuciones
where host='".$row->ID."'
and estado <> 3 group by sesion, estado ";

	$stid2 = oci_parse($con, $consulta2);
	oci_execute($stid2);
	while(($row2 = oci_fetch_object ($stid2)) != false) {
              echo "<tr><td align=\"center\"><a href=\"#\" class=\"popper\" data-popbox=\"".$row2->SESION.$row->ID."\">".$row2->SESION."</a></td>";
				if (( $row2->ESTADO == 3 )){
                	echo '<td align="center" bgcolor="#00FF00">TERMINE</td>';
				}
				elseif (( $row2->ESTADO == 5)){
					echo '<td align="center" bgcolor="#0066FF">EXECUTION_EN_COURS</td>';
				}
				elseif (( $row2->ESTADO == 4)){
					echo '<td align="center" bgcolor="#FF9933">HORAIRE_DEPASSE</td>';
				}
				elseif (( $row2->ESTADO == 2)){
					echo '<td align="center" bgcolor="#FF0000">INCIDENTE</td>';
				}
				elseif (( $row2->ESTADO == 1)){
					echo '<td align="center" bgcolor="#FFFF00">ATTENTE_EVENEMENT</td>';
				}
				else{
					echo '<td align="center" ></td>';
				}
				echo "<td align=\"center\">".$row2->INICIO."</td>
				 <td align=\"center\">".$row2->FIN."</td>
				  </tr>";
				
	}
	echo "</table>
	</div>";
}
?>

</body>
</html>