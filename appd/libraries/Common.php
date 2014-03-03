<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Common {
    
    var $CI;
    
    function Common()
    {
        
        $this->CI =& get_instance();
        
    }
    
    /*
     * User
     */
    function userGetNotificationsCount(){
        
        $this->CI->load->model('notification_model');
        $count = $this->CI->notification_model->unreadCount($this->CI->session->userdata('id'));
        
        if($count > 0)
            return '<span id="notification-count">' . $count . '</span>';
        else
            return '';
        
    }
    
}