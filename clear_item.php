<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Clear Item Data</title>
    </head>
    <body>
        <?php
        require_once('..\\..\\inc\\config.php');
        require_once('class\\n2hDatabaseWrapper.php');
        require_once('class\\updateManager.php');

        (new n2hDatabaseWrapper(
                $_ncku2hand['dbServerName'], 
                $_ncku2hand['dbUserName'], 
                $_ncku2hand['dbPassword'], 
                $_ncku2hand['dbName']))->clearItem();
        ?>
    </body>
</html>
