<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Main Manager</title>
        <?php
        $pageType = 'admin';
        require '../php/header_gen.php';
        ?>
    </head>
    <body>
        <?php
        // main function
        if($fbHandler->loggedIn()) {
            // Case: in logging-in flow
            // get a long-lived token then update to db
            $token = $fbHandler->getExtendedToken();
            $dbHandler->updateToken($token);
            echo '<a href="https://developers.facebook.com/tools/debug/accesstoken?q='.
                $token . '">Check new token in Access Token Debugger</a><br>';
        } else {
            // Case: not in logging-in flow
            if(filter_input(INPUT_GET, 'clear')) {
                clearItems($dbHandler);
            }
            if(filter_input(INPUT_GET, 'seth')) {
                $dbHandler->setHealthy(true);
            }
            if(filter_input(INPUT_GET, 'rsth')) {
                $dbHandler->setHealthy(false);
            }
            
            // check pre-saved access_token
            $token = $dbHandler->getToken();
            echo '<a href="https://developers.facebook.com/tools/debug/accesstoken?q='.
                    $token.
                    '">Check old token in Access Token Debugger</a><br>';
            
            // show login url
            echo '<a href="' . $fbHandler->getLoginUrl() . '">request token from FACEBOOK</a><br>';
        }
        
        function clearItems($dbHandler) {
            $dbHandler->clearItem();

            $imgPath = $_ncku2hand['rootPath'].'storage/img/';
            $handle = opendir($imgPath);
            while (false !== ($entry = readdir($handle))) {
                if($entry=='.' || $entry=='..') {
                    continue;
                }
                unlink($imgPath . $entry);
            }
            echo 'Items in database are reseted.<br>';
        }
        ?>
        
        <a href="refresh_id.php">refresh item list</a><br>
        <a href="refresh_item.php">refresh item data</a><br>
        <a href="admin.php?clear=true">clear data(!!!NOT REVERSABLE!!!)</a><br>
        <a href="admin.php?seth=true">set healthy</a><br>
        <a href="admin.php?rsth=true">set unhealthy</a><br>
        <br>
        <a href="http://localhost/phpmyadmin/">Database UI</a><br>
        <a href="../index.php">Return</a><br>
    </body>
</html>
