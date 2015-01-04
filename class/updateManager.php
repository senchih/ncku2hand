<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of updateManager
 *
 * @author User
 */
class updateManager {
    private $dbHandler;
    private $fbHandler;
    protected $groupId;
    
    function __construct($dbHandler, $fbHandler, $groupId) {
        $this->dbHandler = $dbHandler;
        $this->fbHandler = $fbHandler;
        $this->groupId = $groupId;
    }
    
    private function loadIdListPerPage($syntax, $page) {
        $this->fbHandler->setRequest($syntax);
        $feedObject = $this->fbHandler->executeAndGetGraphObject();
        
        if($feedObject->getProperty('data')) {
            // Case: GraphAPI returns non-empty page
            $indexInPage = 0;
            while($postObject = $feedObject->getProperty('data')->getProperty($indexInPage)) {
                // Loop: Iterates each item in a page
                $itemId = $postObject->getProperty('id');
                $itemUpdateTime = strtotime($postObject->getProperty('updated_time'));
                if($this->dbHandler->checkAndSetItemFresh($itemId, $itemUpdateTime)) {
                    // Case: Meets item that latest data already in db, 
                    // which means all updated items are loded
                    echo 'Old data reached in page ' . $page . '<br>';
                    return false;
                }
                $indexInPage++;
            }
            echo 'Page ' . $page . ' was loaded.<br>';
            return strstr($feedObject->getProperty('paging')->getProperty('next'), '/' . $this->groupId);
        } else {
            // Case: GraphAPI returns empty page
            echo 'Page ' . $page . ' is empty.<br>';
            return false;
        }
    }
    
    function fetchIdList() {
        if($this->fbHandler->loggedIn()) {
            // determine the syntax should be used
            if(!isset($_SESSION['syntaxForFetchingId'])) {
                $fbSyntax = '/'.$this->groupId.'/feed?fields=id,updated_time&limit=25';
                $_SESSION['indexForFetchingId'] = 1;
            } else {
                $fbSyntax = $_SESSION['syntaxForFetchingId'];
                $_SESSION['indexForFetchingId']++;
            }
            // use and get next syntax
            $fbSyntax = $this->loadIdListPerPage($fbSyntax, $_SESSION['indexForFetchingId']);
            
            if($fbSyntax) {
                $_SESSION['syntaxForFetchingId'] = $fbSyntax;
                return true;
            } else {
                return false;
            }
        } else {
            echo 'Facebook not logged in...<br>';
            return false;
        }
    }
    
    private function refreshSinglePhoto($itemId, $photoGraphObject) {
        $imageId = $photoGraphObject->getProperty('target')->getProperty('id');
        $width = $photoGraphObject->getProperty('media')->getProperty('image')->getProperty('width');
        $height = $photoGraphObject->getProperty('media')->getProperty('image')->getProperty('height');
        $imageUri = $photoGraphObject->getProperty('media')->getProperty('image')->getProperty('src');
        $this->dbHandler->updateItemPhoto($itemId, $imageId, $width, $height, $imageUri);
    }
    
    private function refreshAlbum($itemId, $albumGraphObject) {
        $singlePhotoSet = $albumGraphObject->getProperty('subattachments');
        if($singlePhotoSet) {
            // Case: Many photos in an item
            $photoIndex = 0;
            $photo = $singlePhotoSet->getProperty($photoIndex);
            while($photo) {
                $this->refreshSinglePhoto($itemId, $photo);
                $photo = $singlePhotoSet->getProperty($photoIndex++);
            }
        } else {
            // Case: One photo in an item
            $this->refreshSinglePhoto($itemId, $albumGraphObject);
        }
    }
    
    private function refreshComments($itemId) {
        $fbSyntax = '/' . $itemId . '/comments?limit=25';
        $commentIndex = 0;
        do {
            $this->fbHandler->setRequest($fbSyntax);
            $response = $this->fbHandler->executeAndGetGraphObject();
            $comments = $response->getProperty('data');
            if(!$comments) {
                break;
            }

            $commentIndexEachPage = 0;
            while($comment = $comments->getProperty($commentIndexEachPage)) {
                $this->dbHandler->updateComment($itemId, $comment, $commentIndex);

                $commentIndex++;
                $commentIndexEachPage++;
            }
        } while($fbSyntax = strstr($response->getProperty('paging')->getProperty('next'), '/' . $this->groupId));
    }
    
    function refreshItem($itemId) {
        $fbSyntax = '/' . $itemId . '?fields=' . 
                'id, from, message, type, created_time, updated_time, attachments';
        $this->fbHandler->setRequest($fbSyntax);
        $post = $this->fbHandler->executeAndGetGraphObject();

        $userId = $post->getProperty('from')->getProperty('id');
        $context = $post->getProperty('message');
        $createdTime = strtotime($post->getProperty('created_time'));
        $updatedTime = strtotime($post->getProperty('updated_time'));
        $this->dbHandler->updateItemInfo($itemId, $updatedTime, $createdTime, $context, $userId);

        //Store photos
        if($post->getProperty('type') == 'photo') {
            $attachment = $post->getProperty('attachments')->getProperty(0);
            $this->refreshAlbum($itemId, $attachment);
        }

        //Store comments
        $this->refreshComments($itemId);
    }
}
