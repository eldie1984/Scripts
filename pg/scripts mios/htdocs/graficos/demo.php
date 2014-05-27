<?php
// Include the PHPWord.php, all other classes were loaded by an autoloader
require_once 'PHPWord.php';

// Create a new PHPWord Object
$PHPWord = new PHPWord();

// Every element you want to append to the word document is placed in a section. So you need a section:
$section = $PHPWord->createSection();

// After creating a section, you can append elements:
$section->addText('Objective ');
$section->addText('Reach ');
$section->addText('Technical architecture ');
$section->addText('Capacity Planning');
$section->addText('Hello world!');
$section->addText('Hello world!');
$section->addText('Hello world!');
$section->addText('Hello world!');
$section->addText('Hello world!');
$section->addText('Hello world!');
$section->addText('Hello world!');
$section->addText('Hello world!');


// You can directly style your text by giving the addText function an array:
$section->addText('Hello world! I am formatted.', array('name'=>'Tahoma', 'size'=>16, 'bold'=>true));
$section->addImage('../graficos/Examples/_earth.JPG', array('width'=>210, 'height'=>210, 'align'=>'center'));



// If you often need the same style again you can create a user defined style to the word document
// and give the addText function the name of the style:
$PHPWord->addFontStyle('myOwnStyle', array('name'=>'Verdana', 'size'=>14, 'color'=>'1B2232'));
$section->addText('Hello world! I am formatted by a user defined style', 'myOwnStyle');

// You can also putthe appended element to local object an call functions like this:

// At least write the document to webspace:
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save('helloWorld.docx'); 
?>