<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Refresh items (v150103)</title>
    </head>
    <body>
        <?php
        session_start();
        require_once('..\\..\\inc\\config.php');
        require_once('class\\n2hDatabaseWrapper.php');
        require_once('class\\n2hFacebookConnector.php');
        require_once('class\\updateManager.php');

        $dbHandler = new n2hDatabaseWrapper(
                $_ncku2hand['dbServerName'], 
                $_ncku2hand['dbUserName'], 
                $_ncku2hand['dbPassword'], 
                $_ncku2hand['dbName']
                );
        
        $fbHandler = new n2hFacebookConnector(
                $_ncku2hand['fbAppId'],
                $_ncku2hand['fbAppSecret'],
                $_ncku2hand['fbRedirectUri']
                );
        $fbHandler->setToken($dbHandler->getToken());
        
        $manager = new updateManager(
                $dbHandler, 
                $fbHandler, 
                $_ncku2hand['groupId']
                );

        // main procedure
        $manager->refresh(2);
        echo '<a>end of the php</a><br>';
        ?>
    </body>
</html>
