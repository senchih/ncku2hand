<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of n2hFacebookConnector
 *
 * @author User
 */
require_once('..\\..\\inc\\autoload.php');
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;

class n2hFacebookConnector {
    private $helper;
    private $session;
    private $request;
    
    function __construct($appId, $appSecret, $redirectUri) {
        FacebookSession::setDefaultApplication($appId, $appSecret);
        $this->helper = new FacebookRedirectLoginHelper($redirectUri);
        try {
            $this->session = $this->helper->getSessionFromRedirect();
        } catch( FacebookRequestException $e ) {
            // When Facebook returns an error
            echo 'Exception occured, code: ' . $e->getCode();
            echo ' with message: ' . $e->getMessage() . '<br>';
        } catch( Exception $e ) {
            // When validation fails or other local issues
        }
    }
    
    function loggedIn() {
        return isset($this->session);
    }
    
    function setToken($token) {
        $this->session = new FacebookSession($token);
        try {
            $this->session->validate();
        } catch (FacebookRequestException $e) {
            // Session not valid, Graph API returned an exception with the reason.
            echo $e->getMessage();
        } catch (Exception $e) {
            // Graph API returned info, but it may mismatch the current app or have expired.
            echo $e->getMessage();
        }
    }
    
    function getExtendedToken() {
        $this->session = $this->session->getLongLivedSession();
        return $this->session->getToken();
    }
    
    function getLoginUrl() {
        return $this->helper->getLoginUrl();
    }
    
    function setRequest($syntax) {
        $this->request = new FacebookRequest($this->session, 'GET', $syntax);
    }
    
    function executeAndGetGraphObject() {
        try {
            $fbResponse = $this->request->execute();
            return $fbResponse->getGraphObject();
        } catch (FacebookRequestException $e) {
            // Session not valid, Graph API returned an exception with the reason.
            echo $e->getMessage();
        } catch (Exception $e) {
            // Graph API returned info, but it may mismatch the current app or have expired.
            echo $e->getMessage();
        }
    }
}
