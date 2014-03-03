<?php
/*
    PHP Class  : language.php
    Created on : 05/12/2012, 12:27
    Author     : Oscar
    Description:
        Language actions controller.
*/
class Language extends CI_Controller {
    
    function index(){
        redirect('home');
    }
    
    function js() {
        Header("content-type: application/x-javascript");
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        $this->load->view('language/js');
    }
    
}