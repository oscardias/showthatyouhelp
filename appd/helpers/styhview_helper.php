<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
    PHP File   : styhview_helper.php
    Created on : 30/11/2009, 15:12:42
    Author     : Oscar
    Description:
        Showthatyouhelp specific functions for view construction.
*/

// ------------------------------------------------------------------------

/**
 * Load a view adding header and footer
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('styh_load_view'))
{
    function styh_load_view($view, $data = '', $header = '')
    {
        $CI =& get_instance();
        
        // Language
        $CI->lang->load('general', get_language_name());
        
        // Prepare SEO fields
        if(isset($header['seo_title'])) {
            $header['seo_title'] = $header['seo_title'] .' - showthatyouhelp';
        } else {
            $header['seo_title'] = 'showthatyouhelp';
        }
        
        if(isset($header['seo_description'])) {
            $header['seo_description'] = str_replace('[default]', $CI->lang->line('general_seo_description_part'), strip_tags($header['seo_description']));
            $header['seo_description'] = character_limiter(str_replace('&quot;','\'', $header['seo_description']), 190, '...');
        } else
            $header['seo_description'] = $CI->lang->line('general_seo_description_full');
        
        if(isset($header['seo_keywords']))
            $header['seo_keywords'] = str_replace('[default]', $CI->lang->line('general_seo_keywords'), strip_tags($header['seo_keywords']));
        else
            $header['seo_keywords'] = $CI->lang->line('general_seo_keywords');
        
        // Twitter SEO
        if(!isset($header['seo_add'])) {
            // Default Additional details
            $header['seo_add'] = array(
                'card' => 'summary',
                'type' => 'website',
                'site' => '@showthatyouhelp',
                'url' => base_url(),
                'title' => 'showthatyouhelp.com',
                'description' => $header['seo_description'],
                'short_description' => $header['seo_description'],
                'image' => base_url('images/logo.png')
            );
        } else {
            // Custom Information
            if(!isset($header['seo_add']['card']))
                $header['seo_add']['card'] = 'summary';
            
            if(!isset($header['seo_add']['type']))
                $header['seo_add']['type'] = 'website';
            
            if(!isset($header['seo_add']['site']))
                $header['seo_add']['site'] = '@showthatyouhelp';
            
            if(!isset($header['seo_add']['url']))
                $header['seo_add']['url'] = base_url();
                
            if(isset($header['seo_add']['title']))
                $header['seo_add']['title'] = $header['seo_add']['title'] . ' - showthatyouhelp';
            else
                $header['seo_add']['title'] = $header['seo_title'];
            
            if(!isset($header['seo_add']['description'])) {
                $header['seo_add']['description'] = $header['seo_description'];
                $header['seo_add']['short_description'] = $header['seo_description'];
            } else {
                $header['seo_add']['short_description'] = character_limiter($header['seo_add']['description'], 190, '...');
            }
            
            if(!isset($header['seo_add']['image']))
                $header['seo_add']['image'] = base_url('images/logo.png');
        }
        
        // Profile image in the menu
        if($CI->session->userdata('logged_in') === true) {
            $CI->load->model('notification_model');
            $count = $CI->notification_model->unreadCount($CI->session->userdata('id'));
            if($count > 0)
                $header['notification_count'] = '<span id="notification-count">' . $count . '</span>';
            else
                $header['notification_count'] = '';
            
            $header['user_profile_image'] = user_profile_image(
                    $CI->session->userdata('username'),
                    $CI->session->userdata('image_name'),
                    $CI->session->userdata('image_ext'),
                    'thumb'
                    );
        }
        
        // Loads the view with the header and footer
        $CI->load->view('template/header', $header);
        $CI->load->view($view, $data);
        $CI->load->view('template/footer');
    }
}

// ------------------------------------------------------------------------

/* End of file styhview_helper.php */
/* Location: ./system/application/helpers/styhview_helper.php */