<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="utf-8" />
        <?php
        // include libs
        register_shutdown_function( "fatal_handler" );
        function fatal_handler() {
            print_r(error_get_last());
        }
        
        require_once('../inc/config.php');
        require_once('class/n2hDatabaseWrapper.php');
        require_once('class/n2hFacebookConnector.php');
        require_once('class/updateManager.php');
        
        // initialize
        session_start();
        $fbHandler = new n2hFacebookConnector(
                $_ncku2hand['fbAppId'],
                $_ncku2hand['fbAppSecret'],
                $_ncku2hand['fbRedirectUri']
                );
        
        $dbHandler = new n2hDatabaseWrapper(
                $_ncku2hand['dbServerName'], 
                $_ncku2hand['dbUserName'], 
                $_ncku2hand['dbPassword'], 
                $_ncku2hand['dbName']
                );
        
        $token = $dbHandler->getToken();
        $fbHandler->setToken($token);
        $manager = new updateManager(
                $dbHandler, 
                $fbHandler, 
                $_ncku2hand['groupId']
                );
        
        if(!isset($_SESSION['refreshIdFlag'])) {
            echo '<meta http-equiv="refresh" content="0">';
        } else if ($_SESSION['refreshIdFlag']) {
            echo '<meta http-equiv="refresh" content="0">';
        }
        ?>
        <title>refresh v150104</title>
    </head>
    <body>
        <?php
        if(!isset($_SESSION['refreshIdFlag'])) {
            $_SESSION['refreshIdFlag'] = $manager->fetchIdList();
        } else if ($_SESSION['refreshIdFlag']) {
            $_SESSION['refreshIdFlag'] = $manager->fetchIdList();
        } else {
            echo 'Refresh disabled in current session.<br>';
        }
        ?>
    </body>
</html>
