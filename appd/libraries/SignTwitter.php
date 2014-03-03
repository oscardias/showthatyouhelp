<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'twitteroauth/twitteroauth.php';

class SignTwitter extends TwitterOAuth {
    
    function __construct($params = array())
    {
        
        if(isset($params['oauth_token']) && isset($params['oauth_token_secret']))
            parent::__construct($params['key'], $params['secret'], $params['oauth_token'], $params['oauth_token_secret']);
        else
            parent::__construct($params['key'], $params['secret']);
        
    }
    
}