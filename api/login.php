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

$db = new mysqli('localhost', 'root', '', 'photopos');
$result = $db->query('SELECT * FROM users WHERE username = "'.$username.'" LIMIT 1');
if($result->num_rows == 0){
    echo '{"success": false, "error": "Invalid login or password"}';
    exit;
}

$row = $result->fetch_assoc();
if($row['password_hash'] != hash('sha256', $password)){
    echo '{"success": false, "error": "Invalid login or password"}';
    exit;
}

$sessionId = uniqid();
$db->query('INSERT INTO sessions (id, user_id) VALUES ("'.$sessionId.'", '.$row['id'].')');

echo '{"success": true, "sessionId": "'.$sessionId.'"}';

?>