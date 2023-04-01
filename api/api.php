<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$resource = null;
if(isset($_GET['resource']))
    $resource = $_GET['resource'];

if(!file_exists($resource.'.json'))
    $resource = null;
else
    $resource .= '.json';

if($resource == null)
    echo '{"success": false, "error": "Resource not found"}';
else
    echo file_get_contents($resource);
?>