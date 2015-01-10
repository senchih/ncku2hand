<?php
require_once '../config.php';
require_once 'n2hDatabaseWrapper.php';

//Connect to DB
$dbHandler = new n2hDatabaseWrapper(
        $dbServer, 
        $dbUser, 
        $dbPassword, 
        $dbName
        );



$result = $dbHandler->mysqli->query("
    SELECT image_id, item_id
        FROM images
        ORDER BY RAND( )
        LIMIT 36"
        );

$i = 0;
$data = array();
while($row = $result->fetch_array()) {
    $data[$i] = $row;
    $i++;
}

echo json_encode($data);