<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo '{"success": false, "error": "Invalid request method"}';
    exit;
}

$requestContent = json_decode(file_get_contents('php://input'), true);

$username = $requestContent['username'];
$password = $requestContent['password'];

$successful = ($username == 'admin') && ($password == 'admin');

if($successful){
    echo '{"success": true, "sessionId": "1234567890"}';
} else {
    echo '{"success": false, "error": "Invalid login or password"}';
}
?>