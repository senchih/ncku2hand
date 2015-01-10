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

$imageId = filter_input(INPUT_GET, 'id');

if($imageId) {
		
$result = $dbHandler->mysqli->query("
    SELECT image_path
        FROM images
        WHERE image_id=".$imageId
        );
		$imagePath = '../../storage/';
		$imagePath .= $result->fetch_assoc()["image_path"];
		
		header("Content-type: image/jpeg");
		echo file_get_contents($imagePath);
                }