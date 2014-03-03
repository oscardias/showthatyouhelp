<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'phpmailer/class.phpmailer.php';

class PHPMail extends PHPMailer {
    
    var $CI;
    
    public function PHPMail($exceptions = false)
    {
        
       parent::__construct($exceptions);
        
       $this->CI =& get_instance();
      
    }
    
    public function customSend($info)
    {
        $this->CI->config->load('email');
        
        $body             = $info['body'];
        
        $this->IsSMTP();
        $this->CharSet    = "UTF-8";
        $this->SMTPAuth   = true;                  // enable SMTP authentication
        $this->SMTPSecure = "tls";                 // sets the prefix to the servier
        $this->Host       = $this->CI->config->item('smtp_host');
        $this->Port       = $this->CI->config->item('smtp_port');

        $this->Username   = $this->CI->config->item('smtp_user');
        $this->Password   = $this->CI->config->item('smtp_pass');

        $this->From       = $info['from'];
        $this->FromName   = $info['from_name'];
        $this->Subject    = $info['subject'];
        $this->AltBody    = strip_tags($body);
        $this->WordWrap   = 50; // set word wrap

        $this->MsgHTML($body);

        $this->AddAddress($info['to'], $info['to_name']);
        
        $this->AddBCC('support@showthatyouhelp.com');

        $this->IsHTML(true); // send as HTML

        return $this->Send();
    }
    
    public function prepareBatch()
    {
        $this->CI->config->load('email');
        
        $this->IsSMTP();
        $this->CharSet    = "UTF-8";
        $this->SMTPAuth   = true;                  // enable SMTP authentication
        $this->SMTPSecure = "tls";                 // sets the prefix to the servier
        $this->Host       = $this->CI->config->item('smtp_host');
        $this->Port       = $this->CI->config->item('smtp_port');

        $this->Username   = $this->CI->config->item('smtp_user');
        $this->Password   = $this->CI->config->item('smtp_pass');
        
        $this->WordWrap   = 50; // set word wrap

        $this->IsHTML(true); // send as HTML
    }
    
    public function customSendBatch($info)
    {
        $this->From       = $info['from'];
        $this->FromName   = $info['from_name'];
        $this->Subject    = $info['subject'];
        
        $body             = $info['body'];
        $this->AltBody    = strip_tags($body);
        $this->MsgHTML($body);

        $this->AddAddress($info['to'], $info['to_name']);
        $this->AddBCC('support@showthatyouhelp.com');

        $result = $this->Send();
        $this->ClearAllRecipients();
        
        return $result;
    }
    
}