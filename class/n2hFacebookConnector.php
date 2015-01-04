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
require_once('..\\inc\\autoload.php');
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;

class n2hFacebookConnector {
    private $helper;
    private $session;
    private $request;
    
    function __construct($appId, $appSecret, $redirectUri) {
        FacebookSession::setDefaultApplication($appId, $appSecret);
        $this->helper = new FacebookRedirectLoginHelper($redirectUri);
        $this->session = $this->helper->getSessionFromRedirect();
    }
    
    function loggedIn() {
        return isset($this->session);
    }
    
    function setToken($token) {
        $this->session = new FacebookSession($token);
        $this->session->validate();
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
        $fbResponse = $this->request->execute();
        return $fbResponse->getGraphObject();
    }
}
