<?php
/*
    PHP Class  : run.php
    Created on : 19/12/2012, 19:19
    Author     : Oscar
    Description:
        Controller that executes programmed tasks - cron jobs.
*/
class Run extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->input->is_cli_request() OR show_404();
    }
    
    function pending_notifications() {
        echo "Start Run->pending_notifications()".PHP_EOL;
        echo "Date: ".date('Y-m-d').PHP_EOL.PHP_EOL;
        
        $time_start = microtime(true);
        
        // Get users with pending notifications
        $this->load->model('notification_model');
        $pending = $this->notification_model->get_users_pending();
        
        // Send email to each user
        echo "Sending pending emails".PHP_EOL.PHP_EOL;

        $this->load->library('PHPMail');

        $this->phpmail->prepareBatch();

        foreach ($pending as $user) {
            // Check if user is receiving updates
            if($user['notifications']) {
                $notifications = json_decode($user['notifications'], TRUE);
                if(isset($notifications['notify_pending']) && $notifications['notify_pending'] == '0') {
                    echo "User {$user['username']} is not receving".PHP_EOL.PHP_EOL;
                    continue;
                }
            }
            
            echo "Sending email to {$user['username']}".PHP_EOL;
            
            $language['general'] = $this->lang->load('general', get_language_name($user['language']), TRUE);
            $language['email'] = $this->lang->load('email', get_language_name($user['language']), TRUE);
        
            $email_data = array(
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'num_notifications' => $user['num_notifications'],
                'language' => array(
                    'general' => $language['general'],
                    'email' => $language['email']
                )
            );
            
            if (!$this->phpmail->customSendBatch(array(
                'body' => $this->load->view('emails/pending_notifications', $email_data, true),
                'from' => 'support@showthatyouhelp.com',
                'from_name' => $language['email']['email_support_from_name'],
                'subject' => $language['email']['email_pending_subject'],
                'to' => $user['email'],
                'to_name' => (($user['full_name'])?$user['full_name']:$user['username'])
            ))) {
                log_message('error', "Run->pending_notifications() - customSend() - ".$this->phpmail->ErrorInfo);
                echo "Error sending email to {$user['username']}".PHP_EOL;
            }
            
            echo "Finished sending email to {$user['username']}".PHP_EOL.PHP_EOL;
        }
        echo "Finished sending emails".PHP_EOL;
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        echo "Run->pending_notifications() took {$time} seconds.".PHP_EOL;
        
        echo "End Run->pending_notifications()".PHP_EOL;
    }
    
    function single_notifications() {
        echo "Start Run->single_notifications()".PHP_EOL;
        echo "Date: ".date('Y-m-d').PHP_EOL.PHP_EOL;
        
        $time_start = microtime(true);
        
        // Get users with pending notifications
        $this->load->model('notification_model');
        $pending = $this->notification_model->get_notifications_pending();
        
        // Send email to each user
        echo "Sending single emails".PHP_EOL.PHP_EOL;
        
        $this->load->library('PHPMail');

        $this->phpmail->prepareBatch();
        
        foreach ($pending as $user) {
            // Check if user is receiving updates
            if($user['notifications']) {
                $notifications = json_decode($user['notifications'], TRUE);
                
                if($user['type'] == 'comment_mention')
                    $user['type'] = 'mention';
                
                if(isset($notifications['notify_'.$user['type']]) && $notifications['notify_'.$user['type']] == '0') {
                    echo "User {$user['username']} is not receving {$user['type']}".PHP_EOL.PHP_EOL;
                    continue;
                }
            }
            
            echo "Sending email to {$user['username']}".PHP_EOL;
            
            $language['general'] = $this->lang->load('general', get_language_name($user['language']), TRUE);
            $language['email'] = $this->lang->load('email', get_language_name($user['language']), TRUE);
            
            $email_data = array(
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'by_username' => $user['by_username'],
                'by_full_name' => $user['by_full_name'],
                'update_id' => $user['update_id'],
                'type' => $user['type'],
                'language' => array(
                    'general' => $language['general'],
                    'email' => $language['email']
                )
            );
            
            $by_user = ($user['by_full_name']?$user['by_full_name']:$user['by_username']);
            
            switch ($user['type']) {
                case 'connect':
                    $subject = sprintf($language['email']['email_connect_subject'], $by_user);
                    break;
                
                case 'mention':
                    $subject = sprintf($language['email']['email_mention_subject'], $by_user);
                    break;
                case 'comment_mention':
                    $subject = sprintf($language['email']['email_mention_subject'], $by_user);
                    break;
                
                case 'comment':
                    $subject = sprintf($language['email']['email_comment_subject'], $by_user);
                    break;
                
                case 'reshare':
                    $subject = sprintf($language['email']['email_reshare_subject'], $by_user);
                    break;

                default:
                    $subject = sprintf($language['email']['email_connect_subject'], $by_user);
                    break;
            }

            if (!$this->phpmail->customSendBatch(array(
                'body' => $this->load->view('emails/single_notifications', $email_data, true),
                'from' => 'support@showthatyouhelp.com',
                'from_name' => $language['email']['email_support_from_name'],
                'subject' => $subject,
                'to' => $user['email'],
                'to_name' => (($user['full_name'])?$user['full_name']:$user['username'])
            ))) {
                log_message('error', "Run->single_notifications() - customSend() - ".$this->phpmail->ErrorInfo);
                echo "Error sending email to {$user['username']}".PHP_EOL;
            }
            
            echo "Finished sending email to {$user['username']}".PHP_EOL.PHP_EOL;
        }
        echo "Finished sending emails".PHP_EOL;
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        // Mark pending notifications as sent
        $this->notification_model->set_notifications_sent();
        
        echo "Run->single_notifications() took {$time} seconds.".PHP_EOL;
        
        echo "End Run->single_notifications()".PHP_EOL;
    }
}