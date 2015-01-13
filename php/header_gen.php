<?php
//always report php error
register_shutdown_function("fatal_handler");
function fatal_handler() {
    print_r(error_get_last());
}
// print general information
echo '
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset = "UTF-8"/>
        <meta name = "keywords" content="二手,以物易物,賣東西,成大,找東西,MARKET,撿便宜,送東西,轉讓,學生優惠" />
        <meta name = "description" content ="成大二手交易平台">
        <link rel="shortcut icon" href="favicon.ico">';

// print general stylesheet & js
echo '
        <link rel="stylesheet" href="css/main.css" type="text/css">
        <link rel="stylesheet" href="css/header.css" type="text/css">';
echo '
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../inc/config.js"></script>
        <script type="text/javascript" src="js/main.js"></script>';

// print stylesheet & js for each page
switch ($pageType) {
    case 'index':
        echo '
                <link rel="stylesheet" href="css/welcome.css">';
        break;
    case 'box':
        echo '
                <link rel="stylesheet" href="css/style.css">
                <link rel="stylesheet" href="css/box.css">
                <link rel="stylesheet" href="css/hover.css">
                <script type="text/javascript" src="js/box.js"></script>';
        break;
    case 'sort':
        echo '
                <link rel="stylesheet" href="css/style.css">
                <link rel="stylesheet" href="css/box.css">
                <link rel="stylesheet" href="css/hover.css">
                <script type="text/javascript" src="js/sort.js"></script>';
        break;
    case 'list':
        echo '
                <link rel="stylesheet" href="css/forums.css" type="text/css">
                <script type="text/javascript" src="js/list.js"></script>';
        break;
}

// print fancybox plug-in if needed
if($pageType=='box' || $pageType=='sort') {
    echo '
            <link rel="stylesheet" href="../inc/src/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="../inc/src/fancybox/jquery.fancybox.pack.js"></script>
            <link rel="stylesheet" href="../inc/src/fancybox/helpers/jquery.fancybox-thumbs.css" type="text/css" media="screen" />
            <script type="text/javascript" src="../inc/src/fancybox/helpers/jquery.fancybox-thumbs.js"></script>';
}

// include libs
require_once $_ncku2hand['rootPath'].'inc/config.php';
require_once $_ncku2hand['rootPath'].'n2h_core/class/n2hDatabaseWrapper.php';
require_once $_ncku2hand['rootPath'].'n2h_core/class/n2hFacebookConnector.php';
require_once $_ncku2hand['rootPath'].'n2h_core/class/updateManager.php';

$dbHandler = new n2hDatabaseWrapper(
        $_ncku2hand['dbServerName'], 
        $_ncku2hand['dbUserName'], 
        $_ncku2hand['dbPassword'], 
        $_ncku2hand['dbName']
        );

$user = filter_input(INPUT_COOKIE, 'user_code');
$isAdmin = $dbHandler->isAdmin($user);
$isUser = $dbHandler->isUser($user);
$firstName = $dbHandler->getUserFirstName($user);
$systemHealthy = $dbHandler->systemHealthy();

if(!$systemHealthy && !($pageType=='admin')) {
    header('Location: http://syoukore.github.io/NCKU2hand-Bulletin/');
    exit();
}

if($pageType=='admin' && !$isAdmin) {
    header('Location: ../index.php?redirect=true');
    exit();
}

if(!$isUser && $pageType!='index') {
    header('Location: index.php?redirect=true');
    exit();
}

if($pageType=='index' || $pageType=='admin') {
    session_start();
    $fbHandler = new n2hFacebookConnector(
            $_ncku2hand['fbAppId'],
            $_ncku2hand['fbAppSecret'],
            $_ncku2hand['fbRedirectUri']
            );
}