<?php
register_shutdown_function("fatal_handler");
function fatal_handler() {
    print_r(error_get_last());
}
require_once $_ncku2hand['rootPath'].'inc/config.php';
require_once $_ncku2hand['rootPath'].'n2h_core/class/n2hDatabaseWrapper.php';

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
        $limit = filter_input(INPUT_POST, 'limit');
        $filter = json_decode(filter_input(INPUT_POST, 'filter'));
        if($cursor) {
            $result = $dbHandler->getFilteredItemsAfterId($limit, $filter, $cursor);
        } else {
            $result = $dbHandler->getFilteredItemsAfterId($limit, $filter);
        }
        foreach ($result as &$item) {
            $item['item_message'] = str_replace("\n", "<br>", $item['item_message']);
        }
        echo json_encode($result);
        break;
        
    case "getRandomImages":
        echo json_encode($dbHandler->getRandomImages(36));
        break;
}