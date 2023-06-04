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

$username = addslashes($username);

$db = new mysqli('localhost', 'root', '', 'photopos');
$db->query('INSERT INTO users (username, password_hash) VALUES ("'.$username.'", "'.hash('sha256', $password).'")');
?>