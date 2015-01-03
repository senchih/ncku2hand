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
                if($this->dbHandler->chechAndSetItemFresh($itemId, $itemUpdateTime)) {
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
    
    function loadIdList($pageLimit) {
        if($this->fbHandler->loggedIn()) {
            $fbSyntax = '/' . $this->groupId . '/feed' . 
                        '?fields=' . 
                        'id,' . 
                        'updated_time' . 
                        '&limit=25';
            echo 'Fetching start time: ' . time() . '<br>';
            $page = 1;
            while($fbSyntax) {
                $fbSyntax = $this->loadIdListPerPage($fbSyntax, $page);
                if($page == $pageLimit) {
                    echo 'Page limit ' . $pageLimit . ' reached.<br>';
                    break;
                }
                $page++;
            }
            echo 'Fetching end time: ' . time() . '<br>';
        } else {
            echo 'Token invalid or something...<br>';
        }
    }
    
    function refreshItem($itemId) {
        $fbSyntax = '/' . $itemId . 
                '?fields=' . 
                'id,' . 
                'from,' . 
                'message,' . 
                'type,' . 
                'created_time,' . 
                'updated_time,' . 
                'attachments';
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
            $subAttachment = $attachment->getProperty('subattachments');
            if($subAttachment) {
                // Multi-photo case
                $photoIndex = 0;
                while($photo = $subAttachment->getProperty($photoIndex)) {
                    $imageId = $photo->getProperty('target')->getProperty('id');
                    $width = $photo->getProperty('media')->getProperty('image')->getProperty('width');
                    $height = $photo->getProperty('media')->getProperty('image')->getProperty('height');
                    $imageUri = $photo->getProperty('media')->getProperty('image')->getProperty('src');
                    $this->dbHandler->updateItemPhoto($itemId, $imageId, $width, $height, $imageUri);
                    $photoIndex++;
                }
            } else {
                // Single photo case
                    $imageId = $attachment->getProperty('target')->getProperty('id');
                    $width = $attachment->getProperty('media')->getProperty('image')->getProperty('width');
                    $height = $attachment->getProperty('media')->getProperty('image')->getProperty('height');
                    $imageUri = $attachment->getProperty('media')->getProperty('image')->getProperty('src');
                    $this->dbHandler->updateItemPhoto($itemId, $imageId, $width, $height, $imageUri);
            }
        }

        //Store comments
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
}
