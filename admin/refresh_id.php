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
