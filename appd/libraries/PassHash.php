<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'PasswordHash.php';

class PassHash extends PasswordHash {
    
    function PassHash($params = array())
    {
        
        parent::PasswordHash($params['iteration_count_log2'], $params['portable_hashes']);
        
    }
    
}