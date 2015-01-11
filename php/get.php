<?php
register_shutdown_function( "fatal_handler" );
function fatal_handler() {
    print_r(error_get_last());
}
require_once '../../inc/config.php';
require_once '../../n2h_core/class/n2hDatabaseWrapper.php';

//Connect to DB
$dbHandler = new n2hDatabaseWrapper(
        $_ncku2hand['dbServerName'], 
        $_ncku2hand['dbUserName'], 
        $_ncku2hand['dbPassword'], 
        $_ncku2hand['dbName']
        );

$requestType = filter_input(INPUT_GET, 'action');
switch($requestType) {
    case "getItemsByCursor":
        echo json_encode($dbHandler->getItemsAfterId(100));
        break;
    case "getRandomImages":
        echo json_encode($dbHandler->getRandomImages(36));
        break;
    case "getImageById":
        $imageId = filter_input(INPUT_GET, 'id');
        if($imageId) {
            $imagePath = $dbHandler->getImagePath($imageId);
            header("Content-type: image/jpeg");
            echo file_get_contents($imagePath);
        }
        break;
}