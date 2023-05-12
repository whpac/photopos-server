<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

echo '{"success": false, "error": "Resource not found"}';
?>