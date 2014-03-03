<?php
/*
    PHP Class  : about.php
    Created on : 28/08/2012, 12:46
    Author     : Oscar
    Description:
        Controller responsible for about pages.
*/
class About extends CI_Controller {
    
    public $language;
    
    function __construct()
    {
        parent::__construct();
        
        // Language
        $this->language = get_language_name();
        $this->lang->load('about', $this->language);
    }
    
    function index()
    {
        $data = array(
            'active' => 'about',
            'language' => $this->language
        );
        
        $header = array(
            'extra_js' => array(
                base_url('js/includes/excanvas'),
                base_url('js/includes/jquery.jqplot'),
                base_url('js/about')
                ),
            'extra_css' => array(
                base_url('css/about')
                ),
            'seo_title' => lang('about_title'),
            'seo_description' => lang('about_description'),
            'seo_keywords' => '[default]',
            'seo_add' => array(
                    'card' => 'summary',
                    'site' => '@showthatyouhelp',
                    'url' => base_url('about')
                )
        );
        
        styh_load_view('about/index', $data, $header);
    }

//    FUTURE FEATURE - TODO - Task #44
//    function images()
//    {
//        $data = array(
//            'active' => 'images'
//        );
//        
//        $header = array(
//            'seo_title' => 'About',
//            'seo_description' => 'About at showthatyouhelp.com, a [default]',
//            'seo_keywords' => '[default]'
//        );
//        
//        styh_load_view('about/index', $data, $header);
//    }
    
    function terms()
    {
        $data = array(
            'active' => 'terms',
            'language' => $this->language
        );

        $header = array(
            'seo_title' => lang('about_terms_title'),
            'seo_description' => lang('about_terms_description'),
            'seo_keywords' => '[default]',
            'seo_add' => array(
                    'card' => 'summary',
                    'site' => '@showthatyouhelp',
                    'url' => base_url('about/terms')
                )
        );

        styh_load_view('about/index', $data);
    }
    
    function privacy()
    {
        $data = array(
            'active' => 'privacy',
            'language' => $this->language
        );

        $header = array(
            'seo_title' => lang('about_privacy_title'),
            'seo_description' => lang('about_privacy_description'),
            'seo_keywords' => '[default]',
            'seo_add' => array(
                    'card' => 'summary',
                    'site' => '@showthatyouhelp',
                    'url' => base_url('about/privacy')
                )
        );
        
        styh_load_view('about/index', $data, $header);
    }
    
    function contact($send = false)
    {
        $data = array(
            'active' => 'contact',
            'name' => '',
            'email' => '',
            'sent' => $send,
            'language' => $this->language
        );
        
        $header = array(
            'extra_css' => array(
                base_url('css/about')
                ),
            'seo_title' => lang('about_contact_title'),
            'seo_description' => lang('about_contact_description'),
            'seo_keywords' => '[default]',
            'seo_add' => array(
                    'card' => 'summary',
                    'site' => '@showthatyouhelp',
                    'url' => base_url('about/contact')
                )
        );
        
        if($send) {
            $email_data = array(
                'reason' => ucfirst($this->input->post('reason')),
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'message' => $this->input->post('message')
            );
            
            // Send contact email
            $this->load->library('PHPMail');
            if (!$this->phpmail->customSend(array(
                'body' => $this->load->view('emails/contact_softerize', $email_data, true),
                'from' => 'support@showthatyouhelp.com',
                'from_name' => 'Support showthatyouhelp',
                'subject' => '[showthatyouhelp] '.$email_data['reason'],
                'to' => 'support@showthatyouhelp.com',
                'to_name' => 'Support showthatyouhelp'
            )))
                log_message('error', "about->contact() - customSend() - ".$this->phpmail->ErrorInfo);
        }
        
        styh_load_view('about/index', $data, $header);
    }
}