<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type, X-Session-Id');

$resource = null;
if(isset($_GET['resource']))
    $resource = $_GET['resource'];

if(file_exists($resource.'.json')){
    $resource .= '.json';
    echo(file_get_contents($resource));
    return;
}

$resData = explode('/', $resource, 2);

if($resData[0] == 'MapTile') {
    $tile = explode(',', $resData[1], 2);

    // Connect to the DB
    $db = new mysqli('localhost', 'root', '', 'photopos');

    // Load the map tile
    $sql = "SELECT * FROM points WHERE tile_lat = $tile[0] AND tile_lng = $tile[1]";
    $result = $db->query($sql);

    $points = [];

    for($i = 0; $i < $result->num_rows; $i++){
        $row = $result->fetch_assoc();
        $points[] = [
            'type' => 'Point',
            'data' => [
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'label' => $row['label'],
                'description' => $row['description'],
                'wikiArticle' => $row['wiki'],
                'qId' => $row['qid']
            ]
        ];
    }

    // Build the JSON
    $tile = [
        'expires' => null,
        'success' => true,
        'payload' => [
            'type' => 'MapTile',
            'data' => [
                'points' => $points
            ]
        ]
    ];

    echo(json_encode($tile));
    return;
}

if($resData[0] == 'PointList') {
    $db = new mysqli('localhost', 'root', '', 'photopos');

    if(!isset($_SERVER['HTTP_X_SESSION_ID'])){
        echo '{"success": false, "error": "No session ID"}';
        exit;
    }

    $sessionId = $_SERVER['HTTP_X_SESSION_ID'];
    $result = $db->query('SELECT * FROM sessions WHERE id = "'.$sessionId.'" LIMIT 1');
    if($result->num_rows == 0){
        echo '{"success": false, "error": "Invalid session ID"}';
        exit;
    }
    $user_id = $result->fetch_assoc()['user_id'];

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestContent = file_get_contents('php://input');
        $requestContent = addslashes($requestContent);

        $db->query('REPLACE INTO lists (user_id, list_data) VALUES ('.$user_id.', "'.$requestContent.'")');
        return;
    }else{
        $result = $db->query('SELECT * FROM lists WHERE user_id = '.$user_id.' LIMIT 1');
        if($result->num_rows == 0){
            echo '{"success": true, "payload": {"type": "PointList", "data": []}}';
            return;
        }
        $row = $result->fetch_assoc();
        echo $row['list_data'];
        return;
    }
}

echo '{"success": false, "error": "Resource not found"}';
?>