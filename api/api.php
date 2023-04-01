<?php
header('Content-Type: application/json');

$resource = null;
if(isset($_GET['resource']))
    $resource = $_GET['resource'];

if(!file_exists($resource.'.json'))
    $resource = null;
else
    $resource .= '.json';

if($resource == null)
    echo '{"error": "Resource not found"}';
else
    echo file_get_contents($resource);
?>