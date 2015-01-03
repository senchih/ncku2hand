<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of n2hDatabaseWrapper
 *
 * @author User
 */
class n2hDatabaseWrapper {
    private $mysqli;
    public $healthy;
    
    function __construct($server, $user, $password, $db) {
        $this->healthy = true;
        
        // Construst mysqli wrapper
        $this->mysqli = new mysqli( $server,        // server
                                $user,          // user
                                $password);     // passwd
                
        //Check error
        if ($this->mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
            $this->healthy = false;
        }
        //Trivial settings
        $this->mysqli->select_db($db);
        $this->mysqli->query('SET NAMES utf8');
        
        //Check db format (for ncku2hand purpose)
        $result = $this->mysqli->query('SELECT * FROM system_status');
        if($result) {
            if($result->num_rows != 1) {
                echo 'Warning: Status not atomic!<br>';
                $this->healthy = false;
            }
        } else {
                echo 'Failed to check status table.' . '<br>';
                $this->healthy = false;
        }
    }
    
    function __destruct() {
        $this->mysqli->close();
    }
            
    function updateToken($newToken) {
        $stmt = $this->mysqli->prepare("UPDATE system_status SET access_token = ?");
        $stmt->bind_param("s", $newToken);
        return $stmt->execute();
    }
    
    function updateItemInfo($itemId, $updatedTime, $createdTime, $context, $userId) {
        $stmt = $this->mysqli->prepare("UPDATE items ".
                                    "SET item_updated_time=?,".
                                        "item_created_time=?,".
                                        "item_message=?,".
                                        "user_id=?,".
                                        "item_fresh=1 ".
                                    "WHERE item_id=?");
        $stmt->bind_param("iisss", $updatedTime, $createdTime, $context, $userId, $itemId);
        return $stmt->execute();
    }
    
    function updateComment($itemId, $comment, $commentIndex) {
        $commentId = $comment->getProperty('id');
        $commentMessage = $comment->getProperty('message');
        $commentUserId = $comment->getProperty('from')->getProperty('id');

        $stmt = $this->mysqli->prepare(
                "INSERT INTO comments ".
                    "(comment_id, comment_message, user_id, comment_order, item_id) ".
                "VALUES (?, ?, ?, ?, ?)".
                    "ON DUPLICATE KEY ".
                        "UPDATE comment_message=?");
        $stmt->bind_param("sssiss", $commentId, $commentMessage, $commentUserId, $commentIndex, $itemId, $commentMessage);
        return $stmt->execute();
    }
    
    function updateItemPhoto($itemId, $imageId, $width, $height, $imageUri) {
        $imagePath = 'img/' . $imageId . '.jpg';
        copy($imageUri, '../../storage/'.$imagePath);
                        
        $stmt = $this->mysqli->prepare(
                "INSERT INTO images ".
                    "(image_id, image_width, image_height, image_path, item_id) ".
                "VALUES (?, ?, ?, ?, ?) ".
                    "ON DUPLICATE KEY ".
                        "UPDATE image_width=?, image_height=?, image_path=?, item_id=?");
        $stmt->bind_param("siissiiss", $imageId, $width, $height, $imagePath, $itemId, $width, $height, $imagePath, $itemId);
        return $stmt->execute();
    }
    
    function getToken() {
        $result = $this->mysqli->query('SELECT access_token FROM system_status');
        if($result) {
            $row = $result->fetch_assoc();
            return $row['access_token'];
        } else {
            echo "Failed to fetch access_token." . '<br>';
            return false;
        }
    }
    
    function queryUnfreshList() {
        return $this->mysqli->query('SELECT item_id FROM items WHERE item_fresh=0');
    }
    
    function checkAndSetItemFresh($itemId, $updateTime) {
        $stmt = $this->mysqli->prepare("SELECT item_updated_time FROM items WHERE item_id=?");
        $stmt->bind_param('s', $itemId);
        $stmt->execute();
        $oldItemUpdateTime = $stmt->get_result()->fetch_array()['item_updated_time'];
        
        if($oldItemUpdateTime) {
            // Case: Old item
            if($updateTime>$oldItemUpdateTime) {
                // Case: The old item was found updated
                // update its date and unset item_fresh flag
                $stmt = $this->mysqli->prepare("UPDATE items SET item_updated_time=?,item_fresh=0 WHERE item_id=?");
                $stmt->bind_param("is", $updateTime, $itemId);
                $stmt->execute();
                return false;
            } else if($updateTime==$oldItemUpdateTime) {
                return true;
            }
        } else {
            // Case: New item (try creating new row)
            $stmt = $this->mysqli->prepare("INSERT INTO items(item_id, item_updated_time, item_fresh) VALUES (?, ?, 0)");
            $stmt->bind_param("si", $itemId, $updateTime);
            $stmt->execute();
            return false;
        }
    }
    
    function clearItem() {
        $this->mysqli->query("TRUNCATE comments");
        $this->mysqli->query("TRUNCATE images");
        $this->mysqli->query("TRUNCATE items");
    }
}
