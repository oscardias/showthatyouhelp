<?php
/*
    PHP File   : profile
    Created on : 07/12/2009, 18:06:35
    Author     : Oscar
    Description:
        User profile controller.
*/
class Profile extends CI_Controller {

    function Profile()
    {
        parent::__construct();
        
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('profile', get_language_name());
    }
    
    function index()
    {
        redirect('home');
    }

    function view($username, $page = 1)
    {
        // Time translation
        $this->lang->load('date', get_language_name());
        
        // Load user information
        $this->load->model('user_model');
        $user_data = $this->user_model->get(array('username' => $username));
        
        if(!$user_data) {
            styh_load_view('profile/profile_empty', array(
                'username' => $username
            ), array(
                'seo_title' => lang('profile_empty'),
                'seo_description' => lang('profile_empty_description'),
                'seo_keywords' => lang('profile_empty_keywords'),
                'seo_add' => array(
                        'card' => 'summary',
                        'type' => 'profile',
                        'site' => '@showthatyouhelp',
                        'url' => base_url("p/$username")
                    )
            ));
            return;
        }

        $data = array(
            'user_id' => $user_data['id'],
            'username' => $user_data['username'],
            'full_name' => $user_data['full_name'],
            'bio' => $user_data['bio'],
            'website' => $user_data['website'],
            'image_name' => $user_data['image_name'],
            'image_ext' => $user_data['image_ext']
        );

        // User lists
        $data['viewing'] = $this->user_model->getViewOrShow($user_data['id'], 'view', 0);
        $data['viewing_total'] = $this->user_model->countViewOrShow($user_data['id']);
        
        $data['showing'] = $this->user_model->getViewOrShow($user_data['id'], 'show', 0);
        $data['showing_total'] = $this->user_model->countViewOrShow($user_data['id'], 'show');
        
        if($this->session->userdata('logged_in'))
            $data['viewing_current'] = $this->user_model->isViewing($this->session->userdata('id'),$user_data['id']);
        else
            $data['viewing_current'] = false;

        //Load updates information
        $this->load->model('update_model');
        $data['updates'] = $this->update_model->getPostedUpdates($user_data['id'], $page);

        // Pagination
        $this->load->library('pagination');

        $config['base_url'] = base_url('p/'.$username.'/posts');
        $config['uri_segment'] = 4;
        $config['per_page'] = PER_PAGE_UPDATES;
        $config['total_rows'] = $this->update_model->countPostedUpdates($user_data['id']);

        $this->pagination->initialize($config); 

        $data['pagination'] = $this->pagination->create_links();
        $data['total_pages'] = $config['total_rows'] / $config['per_page'];
        
        // Check for notifications
        if($this->session->userdata('logged_in')) {
            $this->load->model('notification_model');
            if($this->notification_model->unreadCount($this->session->userdata('id'), false, 'connect'))
                $this->notification_model->markread($this->session->userdata('id'), false, false, 'connect');
        }
        
        // Header Information
        $header = array(
            'seo_title' => sprintf(lang('profile_user'), $username),
            'seo_description' => sprintf(lang('profile_user_description'), $username),
            'seo_keywords' => sprintf(lang('profile_user_keywords'), $username),
            'redirect_url' => base_url('p/'.$username),
            'seo_add' => array(
                    'card' => 'summary',
                    'type' => 'profile',
                    'site' => '@showthatyouhelp',
                    'url' => base_url("p/$username")
                )
        );
                
        // Meta next/prev
        if($page <= $data['total_pages'])
            $header['seo_add']['next'] = base_url('p/'.$username.'/posts/' . ($page + 1));
        if($page > 1)
            $header['seo_add']['prev'] = base_url('p/'.$username.'/posts/' . ($page - 1));

        styh_load_view('profile/profile', $data, $header);
    }
    
    function update($username, $update)
    {
        if(!$update)
            redirect('p/'.$username);
        
        // Time translation
        $this->lang->load('date', get_language_name());
        
        // Load user information
        $this->load->model('user_model');
        $user_data = $this->user_model->get(array('username' => $username));

        $data = array(
            'username' => $user_data['username'],
            'full_name' => $user_data['full_name'],
            'bio' => $user_data['bio'],
            'website' => $user_data['website'],
            'image_name' => $user_data['image_name'],
            'image_ext' => $user_data['image_ext']
        );

        // User lists
        $data['viewing'] = $this->user_model->getViewOrShow($user_data['id'], 'view', 0);
        $data['viewing_total'] = $this->user_model->countViewOrShow($user_data['id']);
        
        $data['showing'] = $this->user_model->getViewOrShow($user_data['id'], 'show', 0);
        $data['showing_total'] = $this->user_model->countViewOrShow($user_data['id'], 'show');
        
        if($this->session->userdata('logged_in'))
            $data['viewing_current'] = $this->user_model->isViewing($this->session->userdata('id'),$user_data['id']);
        else
            $data['viewing_current'] = false;
        
        //Load update information
        $this->load->model('update_model');
        $data['update'] = $this->update_model->getSingle($update);
        
        if($data['update'] && $data['update']['username'] == $username)
            $data['comments'] = $this->update_model->getComments($update);
        else
            $data['update'] = array();
        
        // Set single entry variable
        $data['is_single'] = true;
        
        // Check for notifications
        if($this->session->userdata('logged_in')) {
            $this->load->model('notification_model');
            if($this->notification_model->unreadCount($this->session->userdata('id'), $update))
                $this->notification_model->markread($this->session->userdata('id'), $update);
        }
        
        // Header Info
        if($data['update']) {
            $description = '';
            if($data['update']['comment']) {
                $description = sprintf(lang('profile_update_description_comment'), $username, $data['update']['comment']);
            } else {
                if($data['update']['title'])
                    $description = sprintf(lang('profile_update_description_title'), $username, $data['update']['title']);
                else
                    $description = sprintf(lang('profile_update_description'), $username);
            }
            $header = array(
                'seo_title' => sprintf(lang('profile_update_title'), $username),
                'seo_description' => $description,
                'seo_keywords' => sprintf(lang('profile_update_keywords'), $username.(isset($data['update']['keywords'])?', '.$data['update']['keywords']:'')),
                'redirect_url' => base_url('p/'.$username.'/'.$update)
            );
        } else
            $header = array(
                'seo_title' => sprintf(lang('profile_user'), $username),
                'seo_description' => sprintf(lang('profile_user_description'), $username),
                'seo_keywords' => sprintf(lang('profile_user_keywords'), $username),
                'redirect_url' => base_url('p/'.$username)
            );
        
        // Additional Meta Definitions
        if($data['update']['type'] == 'photo') {
            $header['seo_add'] = array(
                            'card' => 'photo',
                            'site' => '@showthatyouhelp',
                            'image' => image_url($data['update'], 'small'),
                            'type' => 'profile',
                            'url' => base_url("p/$username/$update")
                        );
        } else {
            $header['seo_add'] = array(
                            'card' => 'summary',
                            'site' => '@showthatyouhelp',
                            'type' => 'profile',
                            'url' => base_url("p/$username/$update")
                        );
        }
        
        styh_load_view('profile/profile', $data, $header);
    }
    
    function list_users($type, $username, $page = 1){
        
        // Load user information
        $this->load->model('user_model');
        $user_data = $this->user_model->get(array('username' => $username));

        $data = array(
            'type' => $type,
            'username' => $user_data['username'],
            'full_name' => $user_data['full_name'],
            'bio' => $user_data['bio'],
            'website' => $user_data['website'],
            'image_name' => $user_data['image_name'],
            'image_ext' => $user_data['image_ext']
        );

        // Get users from database
        if($type == 'viewing') {
            $mode = 'view';
            // SEO fields
            $header = array(
                'seo_title' => sprintf(lang('profile_user_list_viewing'), $username),
                'seo_description' => sprintf(lang('profile_user_list_viewing_description'), $username),
                'seo_keywords' => sprintf(lang('profile_user_list_viewing_keywords'), $username)
            );
        } else {
            $mode = 'show';
            
            // SEO fields
            $header = array(
                'seo_title' => sprintf(lang('profile_user_list_viewers'), $username),
                'seo_description' => sprintf(lang('profile_user_list_viewers_description'), $username),
                'seo_keywords' => sprintf(lang('profile_user_list_viewers_keywords'), $username)
            );
        }
        
        $header['seo_add'] = array(
                        'card' => 'summary',
                        'type' => 'profile',
                        'site' => '@showthatyouhelp',
                        'url' => base_url("p/$username/$type")
                    );
        
        $data['users'] = $this->user_model->getViewOrShow($user_data['id'], $mode, $page);
        
        // Pagination
        $this->load->library('pagination');

        $config['base_url'] = base_url('p/'.$username.'/'.$type);
        $config['uri_segment'] = 4;
        $config['total_rows'] = $this->user_model->countViewOrShow($user_data['id'], $mode);
        $config['per_page'] = PER_PAGE_DEFAULT;

        $this->pagination->initialize($config); 

        $data['pagination'] = $this->pagination->create_links();
        $data['total_pages'] = $config['total_rows'] / $config['per_page'];
        
        // Meta next/prev
        if($page <= $data['total_pages'])
            $header['seo_add']['next'] = base_url('p/'.$username.'/'.$type . '/' . ($page + 1));
        if($page > 1)
            $header['seo_add']['prev'] = base_url('p/'.$username.'/'.$type . '/' . ($page - 1));

        styh_load_view('profile/users', $data, $header);
    }
    
}