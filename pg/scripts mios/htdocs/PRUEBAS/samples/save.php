<?php

$data = $_POST['imageData'];
list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);
file_put_contents('image.png', $data);

?>