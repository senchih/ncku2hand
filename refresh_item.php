<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Refresh items (v150102)</title>
    </head>
    <body>
        <?php
        session_start();
        require_once('..\\..\\inc\\config.php');
        require_once('n2hDatabaseWrapper.php');
        require_once('n2hFacebookConnector.php');

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

        // main procedure  
        $groupId = $_ncku2hand['groupId'];
        if($fbHandler->loggedIn()) {
            $fbSyntax = '/' . $groupId . '/feed' . 
                        '?fields=' . 
                        'id,' . 
                        'updated_time' . 
                        '&limit=25';

            $page = 1;
            $pageLimit = 2;

            echo 'Fetching start time: ' . time() . '</br>';
            while($page <= $pageLimit) {
                $fbHandler->setRequest($fbSyntax);
                $feedObject = $fbHandler->executeAndGetGraphObject();
                
                $oldDataReached = false;
                if($feedObject->getProperty('data')) {
                    // There are still a valid syntax in the last page, so check
                    $indexInPage = 0;
                    while($postObject = $feedObject->getProperty('data')->getProperty($indexInPage)) {
                        $itemId = $postObject->getProperty('id');
                        $itemUpdateTime = strtotime($postObject->getProperty('updated_time'));
                        $oldDataReached = $dbHandler->chechItemFresh($itemId, $itemUpdateTime);
                        if($oldDataReached) {
                            echo 'Old data reached in page ' . $page . '</br>';
                            break;
                        }
                        $indexInPage++;
                    }
                    if($oldDataReached) {
                        break;
                    }
                    echo 'Page ' . $page . ' was loaded.</br>';
                    $fbSyntax = strstr($feedObject->getProperty('paging')->getProperty('next'), '/' . $groupId);
                } else {
                    echo 'Page ' . $page . ' is empty.</br>';
                    break;
                }
                if($pageLimit==$page++) {
                    echo 'Page limit ' . $pageLimit . ' reached.</br>';
                }
            }
            echo 'Fetching end time: ' . time() . '</br>';
    refreshItem($fbHandler, $dbHandler, '769865533051239_769979826373143', $groupId);
            /*$result = $_mysqli->query('SELECT item_id FROM items WHERE item_fresh=0');
            while($unfreshItemId = $result->fetch_assoc()) {
                refreshItem($fbSession, $_mysqli, $unfreshItemId['item_id'], $groupId);
            }*/
        } else {
            echo 'Token invalid or something...</br>';
        }
        flush();
        echo '<a>end of the php</a></br></p>';

        function refreshItem($fbHandler, $dbHandler, $itemId, $groupId) {
            $fbSyntax = '/' . $itemId . 
                    '?fields=' . 
                    'id,' . 
                    'from,' . 
                    'message,' . 
                    'type,' . 
                    'created_time,' . 
                    'updated_time,' . 
                    'attachments';
            $fbHandler->setRequest($fbSyntax);
            $post = $fbHandler->executeAndGetGraphObject();

            $userId = $post->getProperty('from')->getProperty('id');
            $context = $post->getProperty('message');
            $createdTime = strtotime($post->getProperty('created_time'));
            $updatedTime = strtotime($post->getProperty('updated_time'));
            $dbHandler->updateItemInfo($itemId, $updatedTime, $createdTime, $context, $userId);

            //Store photos
            if($post->getProperty('type') == 'photo') {
                $attachment = $post->getProperty('attachments')->getProperty(0);
                if($subAttachment = $attachment->getProperty('subattachments')) {
                    // Multi-photo case
                    $photoIndex = 0;
                    while($photo = $subAttachment->getProperty($photoIndex)) {
                        $imageId = $photo->getProperty('target')->getProperty('id');
                        $width = $photo->getProperty('media')->getProperty('image')->getProperty('width');
                        $height = $photo->getProperty('media')->getProperty('image')->getProperty('height');
                        $imageUri = $photo->getProperty('media')->getProperty('image')->getProperty('src');
                        $dbHandler->updateItemPhoto($itemId, $imageId, $width, $height, $imageUri);
                        $photoIndex++;
                    }
                } else {
                    // Single photo case
                        $imageId = $attachment->getProperty('target')->getProperty('id');
                        $width = $attachment->getProperty('media')->getProperty('image')->getProperty('width');
                        $height = $attachment->getProperty('media')->getProperty('image')->getProperty('height');
                        $imageUri = $attachment->getProperty('media')->getProperty('image')->getProperty('src');
                        $dbHandler->updateItemPhoto($itemId, $imageId, $width, $height, $imageUri);
                }
            }

            //Store comments
            $fbSyntax = '/' . $itemId . '/comments?limit=25';
            $commentIndex = 0;
            do {
                $fbHandler->setRequest($fbSyntax);
                $comments = $fbHandler->executeAndGetGraphObject()->getProperty('data');
                if(!$comments) {
                    break;
                }

                $commentIndexEachPage = 0;
                while($comment = $comments->getProperty($commentIndexEachPage)) {
                    $dbHandler->updateComment($itemId, $comment, $commentIndex);

                    $commentIndex++;
                    $commentIndexEachPage++;
                }
            } while($fbSyntax = strstr($response->getGraphObject()->getProperty('paging')->getProperty('next'), '/' . $groupId));
        }
        ?>
    </body>
</html>
