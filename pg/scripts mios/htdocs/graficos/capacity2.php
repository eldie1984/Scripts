<?php
$app =  $_GET['app']; 
$fecha =  $_GET['fecha'];
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
//INCLUDE CONECTION ARCHIVE
include_once "../conexion.php";

require_once 'PHPWord.php';
$PHPWord = new PHPWord();

//QUERYS
$consulta = "SELECT distinct(HOST), APP, TIPE, userbk FROM userbk WHERE app = '".$app."'";
$stid = oci_parse($con, $consulta);
oci_execute($stid);	
$consulta4 = "SELECT * FROM userbk WHERE app = '".$app."'";



//CREATE SESION ON PHPWORD
$section = $PHPWord->createSection(array('marginLeft'=>800, 'marginRight'=>800, 'marginTop'=>600, 'marginBottom'=>600));

//ADD A STYLE
$PHPWord->addFontStyle('indice1', array('name'=>'Times New Roman', 'size'=>12));
$PHPWord->addFontStyle('titulos', array('name'=>'Times New Roman', 'size'=>14, 'bold'=>true));
$PHPWord->addFontStyle('lala',array('align'=>'center'));
$styleTable = array('borderSize'=>6, 'borderColor'=>'948A54', 'cellMargin'=>80);
$styleFirstRow = array('bgColor'=>'948A54');
$styleSecondRow = array('align'=>'center','bgColor'=>'C4BD97');
$center = array('spaceAfter' => 0, 'align' => 'center');
$styleCell = array('align'=>'center');
$styleCellBTLR = array('align'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);
$PHPWord->addTableStyle('header', array('align'=>'center'));
$PHPWord->addTableStyle('tabla', null, $styleFirstRow);
$styleTableEncabezado = array('borderSize'=>6, 'borderColor'=>'000000','align'=>'center');
$PHPWord->addTableStyle('encabezado', $styleTableEncabezado, $styleCell);
$fontStyle = array('name'=>'Arial', 'size'=>9,'bold'=>true, 'align'=>'center');
$PHPWord->addTitleStyle(3, array('name'=>'Times New Roman', 'size'=>14, 'bold'=>true));
$PHPWord->addTitleStyle(2, array('name'=>'Times New Roman', 'size'=>14, 'bold'=>true));
$PHPWord->addTitleStyle(1, array('name'=>'Times New Roman', 'size'=>14, 'bold'=>true));
$styleCellN = array('align'=>'center','bgColor'=>'B1A0C7');
$styleCellA = array('align'=>'center','bgColor'=>'CCFFCC');
$styleCellV = array('align'=>'center','bgColor'=>'B8CCE4');
$styleCellVIO = array('align'=>'center','bgColor'=>'FCD5B4');
$styleCelldfk = array('align'=>'center','bgColor'=>'FAF87C');
$PHPWord->addParagraphStyle('pStyle', array('spacing'=>100));
$PHPWord->addParagraphStyle('header', array('align'=>'center'));
$PHPWord->addLinkStyle('NLink', array('name'=>'Times New Roman','size'=>12,'color'=>'0000FF', 'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
$fontStyleIndex = array('name'=>'Times New Roman','spaceAfter'=>20, 'size'=>12);

//HEADER

$header = $section->createHeader();
$table = $header->addTable($center);
$table->addRow();
$table->addCell(500)->addText('');
$table->addCell(5000,'header')->addImage('../img/dfk_clip_image001.png','header');
$table->addCell(500)->addText('');
$table->addRow();
$table->addCell(500)->addText('');
$table->addCell(5000,'header')->addText($app,'header');
$table->addCell(500)->addText('');
$table->addRow();
$table->addCell(500)->addText('');
$table->addCell(5000,'header')->addText('Report of Study of Performance','header');
$table->addCell(500)->addText('');

//FOOTER 

$footer = $section->createFooter();  
$footer->addPreserveText('Capacity Planning Operation                                 Page {PAGE} of {NUMPAGES}                                Print Date '.date('d/m/Y H:i:s').'', array('align'=>'center'));

//VARIABLES
$indice = 1;
$cantidad = 0;
$userstotal = 0;
$usernobk = 0;
$usernobkfree = 0;
$SOusado = 0;

//INDEX
$section->addTOC();

$stid = oci_parse($con, $consulta);
oci_execute($stid);
while(($row = oci_fetch_object($stid)) != false){
$consulta6 = "SELECT * FROM FILESYSTEMS WHERE FECHA LIKE '".$fecha."' AND TAMANIO>0 AND HOST LIKE '".$row->HOST."'";
//CALC SIZE USED AND TOTAL
$stid6 = oci_parse($con, $consulta6);
oci_execute($stid6);
$total = 0;
$usadotot = 0;	
while(($row6 = oci_fetch_object ($stid6)) != false) {
$total = $total + $row6->TAMANIO;
$usadotot = $usadotot + $row6->USADO;
}//END CALC SIZE USED AND TOTAL
}
$stid = oci_parse($con, $consulta);
oci_execute($stid);
while(($row = 	oci_fetch_object($stid)) != false){
$host = $row->HOST;
//Consulto todas las base para el host en tabla tablespaces
$consulta2="select distinct(nombredb) from tablespaces where host like '".$row->HOST."' order by nombredb asc";
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
}
}

//1. Objective

$section->addTitle('1. Objective', 1);

$section->addText('The objective of this document is to present the results of the Analysis realised with the information of '.$strfecha."/"."20".(substr($fecha, 6) - 1).' to '.$strfecha."/"."20".substr($fecha, 6).' on the servers of the application '.$app.'', 'indice1');	  

//2. Reach
$section->addTitle('2. Reach', 1);

$section->addListItem('The present Study of Performance includes an analysis of the hardware resources.', 0, 'indice1');
$section->addTextBreak(1);
$section->addListItem('The behavior of the servants is analyzed precise:', 0, 'indice1');
$stid = oci_parse($con, $consulta);
oci_execute($stid);
while(($row = oci_fetch_object($stid)) != false){
$section->addText('                '.$row->HOST.' - '.$row->APP, 'indice1');
}
$section->addTextBreak(1);
$section->addListItem('An analysis of changes and incidents is realised.', 0, 'indice1');
$section->addTextBreak(1);
$section->addListItem('The Data acquired on metric is based on the sources:', 0, 'indice1');
$textrun = $section->createTextRun('pStyle');
$textrun->addText('                              . Portal ','indice1');
$textrun->addLink('http://unix.inetpsa.com', null, 'NLink');
$textrun->addText(' (Technical documentation). ','indice1');
$textrun2 = $section->createTextRun('pStyle');
$textrun2->addText('                              . Portal ','indice1');
$textrun2->addLink('http://pacific.inetpsa.com/', null, 'NLink');
$textrun2->addText(' (Metric Unix & Hw-). ','indice1');

//3. Technical architecture
$section->addTitle('3. Technical architecture', 1);
$section->addTextBreak(1);
$section->addText('A continuacion se presentara la arquitectura de hardware del servidor virtual involucrado:', 'indice1');
$section->addTextBreak(1);
$table = $section->addTable('tabla');
$table->addRow(900);
$table->addCell(2000, $styleCell)->addText($app, array('name'=>'Arial', 'size'=>14, 'bold'=>true));
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addCell(2000, $styleCell)->addText(null, $fontStyle);
$table->addRow(500);
$table->addCell(2000, $styleSecondRow)->addText('Virtual Server', $fontStyle);
$table->addCell(5000, $styleSecondRow)->addText('Detail', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('Physique Server', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('Class', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('Operative System', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('TPU Min', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('TPU Max', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('RAM GB', $fontStyle);
$table->addCell(2000, $styleSecondRow)->addText('Swap GB', $fontStyle);
$stid4 = oci_parse($con, $consulta4);
			oci_execute($stid4);	
			$modulo = 1;
			while(($row4 = oci_fetch_object($stid4)) != false){
				$modulo++;
				if($row4->TPU == ""){
				$TPU = "--";
				}
				else{
				$TPU = $row4->TPU;
				}
				if($modulo%2==0){
			$table->addRow(500);	
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->HOST, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->TIPE, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->SERVER, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->CLASE, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->OS, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->TPUMIN, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($TPU, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->RAM, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('valign'=>'center','bgColor'=>'F2F2F2'))->addText($row4->SWAP, array('name'=>'Arial', 'size'=>8));
				}else{
					$table->addRow(500);	
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->HOST, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->TIPE, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->SERVER, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->CLASE, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->OS, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->TPUMIN, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($TPU, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->RAM, array('name'=>'Arial', 'size'=>8));
			$table->addCell(2000, array('align'=>'center','bgColor'=>'D8D8D8'))->addText($row4->SWAP, array('name'=>'Arial', 'size'=>8));
				}
			}
$section->addTextBreak(2);
//4. Capacity Planning
$section->addTitle('4. Capacity Planning', 1);
$section->addTextBreak(1);

$section->addTitle('   4.1.Consumptions of TPU and Memory ', 2);
$stid = oci_parse($con, $consulta);
oci_execute($stid);	
$indice = 1;
while(($row = 	oci_fetch_object($stid)) != false){
$section->addTitle('        4.1.'.$indice.' '.$row->TIPE.' - '.$row->HOST.'', 3);
$section->addTitle('        4.1.'.$indice.'.1. Consumption TPU ', 3);
$section->addTitle('        4.1.'.$indice.'.2. Consumption MEM ', 3);
$indice++;
$section->addTextBreak(1);
}

$section->addTitle('4.2. Analysis of Storage', 2);
$section->addTextBreak(1);

$stid = oci_parse($con, $consulta);
oci_execute($stid);
$indice = 1;
while(($row = 	oci_fetch_object($stid)) != false){	
$consulta7="select userbk from userbk where host = '".$row->HOST."'";
$stid7 = oci_parse($con, $consulta7);
oci_execute($stid7);
$row7 = oci_fetch_object($stid7);
$userbk = $row7->USERBK;

//CONSULTO SI TIENE BASE O NO
$consulta2="select base from userbk where host = '".$row->HOST."'";
$stid2 = oci_parse($con, $consulta2);
oci_execute($stid2);
$row2 = oci_fetch_object($stid2);
$base = $row2->BASE;
$si = "si";
//END (CONSULTO SI TIENE BASE O NO)

$section->addTitle('4.2.'.$indice.' Analysis of Storage File System Unix '.$row->HOST.' - General', 3);	
$section->addTextBreak(1);
$section->addText('Available free space on Files System existing on the virtual server '.$row->HOST.'', 'indice1');
$section->addTextBreak(1);


$table = $section->addTable($styleCell);
$table->addRow($styleCell);
$table->addCell(1500)->addText('');
$table->addCell(50)->addImage('../img/r1.png');
$table->addCell(1750)->addText("Operative System.");
$table->addCell(50)->addImage('../img/a1.png');
$table->addCell(1750)->addText("Software.");
$table->addRow($styleCell);
$table->addCell(3000)->addText('');
$table->addCell(50)->addImage('../img/v1.png');
$table->addCell(1750)->addText("Data and Archives.");
$table->addCell(50)->addImage('../img/vio1.png');
$table->addCell(1750)->addText("Backup.");
$section->addTextBreak(1);
$section->addText('The File Systems (-) dedicated Application they contain', 'indice1');	
$section->addTextBreak(1);
if(strcmp($base,$si) == 0){
		
$section->addListItem('Software.', 1,'indice1');
$section->addListItem('Application.', 1,'indice1');
$section->addListItem('Datafiles (Bd Oracle).', 1,'indice1');
$section->addListItem('Backup.', 1,'indice1');
$section->addTextBreak(2);

	$userbkusado = 0;
	$userbkdisp = 0;
$stid6 = oci_parse($con, $consulta6);
oci_execute($stid6);
$tabledfk = $section->addTable();
	$tabledfk->addRow();	
	$tabledfk->addCell(2000, $styleCelldfk)->addText('FILESYSTEM', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('TOTAL KB', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('USED', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('AVAIL', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('% Ocup', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('MOUNTED', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	while(($row6 = oci_fetch_object($stid6)) != false) {
if($row6->PUNTO_MONTAJE == $userbk){
	$userstotal = $userstotal + $row6->TAMANIO;
	$userbkusado = $row6->USADO;
	$tamanio = $row6->TAMANIO;
	 $disponible= $tamanio - $userbkusado;
	 $userbkporc = ($row6->USADO*100)/$tamanio;
	 $userbkdisp = $disponible;
	 $tabledfk->addRow(10);
	 $tabledfk->addCell(2000, $styleCellN)->addText($row6->NOMBREFS);
	 $tabledfk->addCell(2000, $styleCellN)->addText($row6->TAMANIO);
	 $tabledfk->addCell(2000, $styleCellN)->addText($row6->USADO);
	 $tabledfk->addCell(2000, $styleCellN)->addText($disponible);
	 $tabledfk->addCell(2000, $styleCellN)->addText(round($userbkporc).' %');
	 $tabledfk->addCell(2000, $styleCellN)->addText($row6->PUNTO_MONTAJE);
	}else{
	$resultado = strpos($row6->PUNTO_MONTAJE, "/user");
	if($resultado !== FALSE){
	$userstotal = $userstotal + $row6->TAMANIO;
	$tamanio = $row6->TAMANIO;
	 $usado2 = $row6->USADO;
	 $usernobkfree = $usernobkfree + ($row6->TAMANIO - $row6->USADO);
	 $usernobk = $usernobk + $usado2;
	 $disponible= $tamanio - $usado2;
	 $tabledfk->addRow(10);
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->NOMBREFS);
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->TAMANIO);
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->USADO);
	 $tabledfk->addCell(2000, $styleCellA)->addText($disponible);
	 $tabledfk->addCell(2000, $styleCellA)->addText(round(($usado2*100)/$tamanio).' %');
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->PUNTO_MONTAJE);
	}else{
	$resultado1 = strpos($row6->PUNTO_MONTAJE, "/soft");
	if($resultado1 !== FALSE){
	$SOusado = $row6->USADO;	
	$tamanio = $row6->TAMANIO;
	 $usado2 = $row6->USADO;
	 $disponible= $tamanio - $usado2;
	 	 $tabledfk->addRow(10);
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->NOMBREFS);
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->TAMANIO);
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->USADO);
		 $tabledfk->addCell(2000, $styleCellV)->addText($disponible);
		 $tabledfk->addCell(2000, $styleCellV)->addText(round(($usado2*100)/$tamanio).' %');
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->PUNTO_MONTAJE);
	}else{
	 $tamanio = $row6->TAMANIO;
	 $usado2 = $row6->USADO;
	 $disponible= $tamanio - $usado2;
	 $tabledfk->addRow(10);
	 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->NOMBREFS);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->TAMANIO);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->USADO);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($disponible);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText(round(($usado2*100)/$tamanio).' %');
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->PUNTO_MONTAJE);
	}
	}
 }
	}
$tabledfk->addRow();
$tabledfk->addCell(2000, $styleCelldfk)->addText('TOTAL', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText(round(($total/1024/1024),2).' GB', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('USED', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText(round(($usadotot/1024/1024),2).' GB', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('FREE', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$free = $total - $usadotot;
 $TOTAL = ($usernobkfree + $usernobk + $userbkusado + $userbkdisp + $SOusado)/1024/1024;
	$tabledfk->addCell(2000, $styleCelldfk)->addText(round(($free/1024/1024),2).' GB', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
$section->addTextBreak(1);
$indice++;
}else{
	
$section->addListItem('Software.', 1,'indice1');
$section->addListItem('Application.', 1,'indice1');
$section->addListItem('Backup.', 1,'indice1');
$section->addTextBreak(2);
	$userbkusado = 0;
	$userbkdisp = 0;
$stid6 = oci_parse($con, $consulta6);
oci_execute($stid6);	
$tabledfk = $section->addTable();
	$tabledfk->addRow();	
	$tabledfk->addCell(2000, $styleCelldfk)->addText('FILESYSTEM', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('TOTAL KB', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('USED', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('AVAIL', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('% Ocup', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('MOUNTED', array('name'=>'Arial', 'size'=>12,'bold'=>true, 'align'=>'center'));
	while(($row6 = oci_fetch_object($stid6)) != false) {
	$resultado = strpos($row6->PUNTO_MONTAJE, "/user");
	if($resultado !== FALSE){
	$userstotal = $userstotal + $row6->TAMANIO;
	$tamanio = $row6->TAMANIO;
	 $usado2 = $row6->USADO;
	 $usernobkfree = $usernobkfree + ($row6->TAMANIO - $row6->USADO);
	 $usernobk = $usernobk + $usado2;
	 $disponible= $tamanio - $usado2;
	 $tabledfk->addRow(10);
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->NOMBREFS);
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->TAMANIO);
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->USADO);
	 $tabledfk->addCell(2000, $styleCellA)->addText($disponible);
	 $tabledfk->addCell(2000, $styleCellA)->addText(round(($usado2*100)/$tamanio).' %');
	 $tabledfk->addCell(2000, $styleCellA)->addText($row6->PUNTO_MONTAJE);
	}else{	
		$resultado1 = strpos($row6->PUNTO_MONTAJE, "/soft");
	if($resultado1 !== FALSE){
	$SOusado = $row6->USADO;	
	$tamanio = $row6->TAMANIO;
	 $usado2 = $row6->USADO;
	 $disponible= $tamanio - $usado2;
	 $tabledfk->addRow(10);
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->NOMBREFS);
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->TAMANIO);
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->USADO);
		 $tabledfk->addCell(2000, $styleCellV)->addText($disponible);
		 $tabledfk->addCell(2000, $styleCellV)->addText(round(($usado2*100)/$tamanio).' %');
		 $tabledfk->addCell(2000, $styleCellV)->addText($row6->PUNTO_MONTAJE);
	}else{
	 $tamanio = $row6->TAMANIO;
	 $usado2 = $row6->USADO;
	 $disponible= $tamanio - $usado2;
	 	 $tabledfk->addRow(10);
	 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->NOMBREFS);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->TAMANIO);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->USADO);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($disponible);
		 $tabledfk->addCell(2000, $styleCellVIO)->addText(round(($usado2*100)/$tamanio).' %');
		 $tabledfk->addCell(2000, $styleCellVIO)->addText($row6->PUNTO_MONTAJE);
	}
	}
	}
	$tabledfk->addRow();
$tabledfk->addCell(2000, $styleCelldfk)->addText('TOTAL', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText(round(($total/1024/1024),2).' GB', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('USED', $fontStyle);
	$tabledfk->addCell(2000, $styleCelldfk)->addText(round(($usadotot/1024/1024),2).' GB', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$tabledfk->addCell(2000, $styleCelldfk)->addText('FREE', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
	$free = $total - $usadotot;
 $TOTAL = ($usernobkfree + $usernobk + $userbkusado + $userbkdisp + $SOusado)/1024/1024;
	$tabledfk->addCell(2000, $styleCelldfk)->addText(round(($free/1024/1024),2).' GB', array('name'=>'Arial', 'size'=>10,'bold'=>true, 'align'=>'center'));
$section->addTextBreak(1);
$indice++;

}
}
$section->addTitle('4.3. Analysis of Oracle Data Base', 2);
$stid = oci_parse($con, $consulta);
oci_execute($stid);
$indice = 1;
while(($row = 	oci_fetch_object($stid)) != false){
$host = $row->HOST;
//Consulto todas las base para el host en tabla tablespaces
$consulta2="select distinct(nombredb) from tablespaces where host like '".$row->HOST."' order by nombredb asc";
$stid2 = oci_parse($con, $consulta2);
oci_execute($stid2);	
while(($row2 = 	oci_fetch_object($stid2)) != false){
$section->addTitle('        4.3.'.$indice.'.Distribution of Tablespaces on Data Base - '.$row2->NOMBREDB.'', 3);
$indice++;
$section->addTitle('        4.3.'.$indice.'.Historial growth of Data Base - '.$row2->NOMBREDB.'', 3);
 $indice++;
$section->addTitle('        4.3.'.$indice.'.Proyected growth of Data Base - '.$row2->NOMBREDB.'', 3);
$indice++;
}
}



//5. Historical of Incidents
$section->addTitle('5. Historical of Incidents', 1);


//6. Historical of Changes
$section->addTitle('6. Historical of Changes ', 1);


//7. Conclusions
$section->addTitle('7. Conclusions ', 1);


//8. Recommendations
$section->addTitle('8. Recommendations', 1);
$section->addTextBreak(1);
$section->addText('To monthly continue realising Capacities on this application to establish tendencies.', 'indice1');


$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');

//SAVE THE ARCHIVE DOC
$objWriter->save('Capacity_'.$app.'_20'.substr($fecha, 6).''.substr($fecha, 3, 2).'.docx'); 
?>