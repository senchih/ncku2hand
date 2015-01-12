<?php
register_shutdown_function("fatal_handler");
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

$requestType = filter_input(INPUT_POST, 'action');
switch($requestType) {
    case "getItemsByCursor":
        $cursor = filter_input(INPUT_POST, 'cursor');
        $filter = json_decode(filter_input(INPUT_POST, 'filter'));
        if($cursor) {
            echo json_encode($dbHandler->getFilteredItemsAfterId(5, $filter, $cursor));
        } else {
            echo json_encode($dbHandler->getFilteredItemsAfterId(30, $filter));
        }
        break;
        
    case "getRandomImages":
        echo json_encode($dbHandler->getRandomImages(36));
        break;
}