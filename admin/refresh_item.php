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
        $pageType = 'admin';
        require '../php/header_gen.php';
        
        $token = $dbHandler->getToken();
        $fbHandler->setToken($token);
        $manager = new updateManager(
                $dbHandler, 
                $fbHandler, 
                $_ncku2hand['groupId']
                );
        
        if(isset($_SESSION['noUnfreshItem']) && $_SESSION['noUnfreshItem']) {
        } else {
            echo '<meta http-equiv="refresh" content="0">';
        }
        ?>
        <title>refresh v150104</title>
    </head>
    <body>
        <?php
        $unfreshId = $dbHandler->fetchUnfreshId();
        if($unfreshId) {
            $manager->refreshItem($unfreshId);
            echo 'Item id: '.$unfreshId.' are refreshed';
        } else {
            $_SESSION['noUnfreshItem'] = true;
            echo 'Items are all fresh';
        }
        ?>
    </body>
</html>
