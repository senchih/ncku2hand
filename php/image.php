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

$imageId = filter_input(INPUT_GET, 'id');
if($imageId) {
    $imagePath = $dbHandler->getImagePath($imageId);
    header("Content-type: image/jpeg");
    echo file_get_contents($imagePath);
}