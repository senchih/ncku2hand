<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="utf-8" />
        <title>Main Manager v150104</title>
    </head>
    <body>
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
        
        // main function
        if($fbHandler->loggedIn()) {
            // Case: in logging-in flow
            // get a long-lived token then update to db
            $token = $fbHandler->getExtendedToken();
            $dbHandler->updateToken($token);
            echo "<br>New token: " .$token. '<br>';
            echo '<a href="https://developers.facebook.com/tools/debug/accesstoken?q='.
                $token . '">Check new token in Access Token Debugger</a><br>';
        } else {
            // Case: not in logging-in flow
            // check pre-saved access_token
            $token = $dbHandler->getToken();
            if(filter_input(INPUT_GET, 'clear')) {
                $dbHandler->clearItem();
                
                $imgPath = '../storage/img/';
                $handle = opendir($imgPath);
                while (false !== ($entry = readdir($handle))) {
                    if($entry=='.' || $entry=='..') {
                        continue;
                    }
                    unlink($imgPath . $entry);
                }
                echo 'Items in database are reseted.<br>';
            }
            echo "<br>Token in the server: " .$token. '<br>';
            echo '<a href="https://developers.facebook.com/tools/debug/accesstoken?q='.
                    $token.
                    '">Check this token in Access Token Debugger</a><br>';
            // show login url
            echo '<a href="' . $fbHandler->getLoginUrl() . '">request token from FACEBOOK</a><br>';
        }
        
        ?>
        
        <a href="refresh_id.php">refresh item list</a><br>
        <a href="refresh_item.php">refresh item data</a><br>
        <a href="index.php?clear=true">clear data(!!!NOT REVERSABLE!!!)</a><br>
        <br>
        <a href="http://localhost/phpmyadmin/">Database UI</a><br>
    </body>
</html>
