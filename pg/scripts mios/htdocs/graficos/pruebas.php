<?php
$content = "<table width=\"200\" border=\"1\">
  <tr>
    <td><img src=\"../img/dfk_clip_image001.png\" width=\"211\" height=\"34\"></td>
    <td>&dfhdgh;</td>
    <td>shghdfh;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
";

// Size – Denotes A4, Legal, A3, etc ——- size:8.5in 11.0in; for Legal size
// Margin – Set the margin of the word document – margin:0.5in 0.31in 0.42in 0.25in; [margin: top right bottom left]

$word_xmlns = "xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40′&#8221";
$word_xml_settings = "<xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>";
$word_landscape_style = "@page {size:8.5in 11.0in; margin:0.5in 0.31in 0.42in 0.25in;} div.Section1{page:Section1;}";
$word_landscape_div_start = "<div class='Section1′>";
$word_landscape_div_end = "</div>";
$content = '
<html '.$word_xmlns.'>
<head>'.$word_xml_settings.'<style type="text/css">
'.$word_landscape_style.' table,td {border:0px solid #FFFFFF;} </style>
</head>
<body>'.$word_landscape_div_start.$content.$word_landscape_div_end.'</body>
</html>
';

@header('Content-Type: application/msword');
@header('Content-Length: '.strlen($content));
@header('Content-disposition: inline; filename="testdocument.doc"');
echo $content;

?>