<?php
/*
    PHP Class  : ajax.php
    Created on : 07/08/2012, 23:17
    Author     : Oscar
    Description:
        Controller dedicated to general AJAX functionality.
*/
class Ajax extends CI_Controller {
    function Ajax(){
        parent::__construct();
        
        if(!IS_AJAX)
            redirect('home');
    }
    
    function index()
    {
        redirect('home');
    }
    
    function validate_sign_up(){
        // Language
        $this->lang->load('user', get_language_name());
        // Validate information
        $this->load->library('form_validation');

        if($this->input->post('email') !== false)
            $this->form_validation->set_rules('email', lang('user_profile_email'), 'trim|required|valid_email|is_unique[user.email]|max_length[255]');
        if($this->input->post('username') !== false)
            $this->form_validation->set_rules('username', lang('user_profile_username'), 'trim|required|is_unique[user.username]|username_check|min_length[3]|max_length[50]');
        if($this->input->post('password') !== false)
            $this->form_validation->set_rules('password', lang('user_profile_password'), 'trim|required|min_length[6]');
        if($this->input->post('password_confirm') !== false)
            $this->form_validation->set_rules('password_confirm', lang('user_profile_password_confirm'), 'trim|required|matches[password]');
        if($this->input->post('current_password') !== false)
            $this->form_validation->set_rules('current_password', lang('user_profile_password_current'), 'trim|required|validate_password');
        if($this->input->post('full_name') !== false)
            $this->form_validation->set_rules('full_name', lang('user_profile_name'), 'trim|max_length[255]');
        if($this->input->post('bio') !== false)
            $this->form_validation->set_rules('bio', lang('user_profile_bio'), 'trim|max_length[255]');
        if($this->input->post('website') !== false)
            $this->form_validation->set_rules('website', lang('user_profile_website'), 'trim|max_length[255]|prep_url');

        if($this->form_validation->run() === false)  {
            // Error in validation
            echo json_encode (array(
                'result' => 0,
                'email' => form_error('email', '<div>', '</div>'),
                'username' => form_error('username', '<div>', '</div>'),
                'password' => form_error('password', '<div>', '</div>'),
                'password_confirm' => form_error('password_confirm', '<div>', '</div>'),
                'current_password' => form_error('current_password', '<div>', '</div>'),
                'full_name' => form_error('full_name', '<div>', '</div>'),
                'bio' => form_error('bio', '<div>', '</div>'),
                'website' => form_error('website', '<div>', '</div>')
            ));
            die();
        }
        
        echo json_encode (array(
            'result' => 1
        ));
    }
    
    function display_updates_list($page, $last_id){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        //Load updates information
        $this->load->model('update_model');
        $data['updates'] = $this->update_model->getViewerUpdates($this->session->userdata('id'), $page, $last_id);
        echo $this->load->view('update/updates_list', $data, true);
    }
    
    function get_new_updates_count($last_id, $json = false){
        // Language
        $this->lang->load('home', get_language_name());
        //Load updates information
        $this->load->model('update_model');
        
        $data['total_new'] = $this->update_model->countNewViewerUpdates($this->session->userdata('id'), $last_id);
        $data['refresh_url'] = base_url('home');
        
        if($json){
            echo json_encode(array(
                'html' => $this->load->view('update/refresh_message', $data, true),
                'count' => $data['total_new']
            ));
        } else {
            if($data['total_new'])
                echo $this->load->view('update/refresh_message', $data, true);
            else
                echo '';
        }
    }
    
    function display_new_updates($last_id){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        //Load updates information
        $this->load->model('update_model');
        $data['updates'] = $this->update_model->getNewViewerUpdates($this->session->userdata('id'), $last_id);
        echo $this->load->view('update/updates_list', $data, true);
    }
    
    function update_delete($update_id){
        // Language
        $this->lang->load('home', get_language_name());
        $this->load->model('update_model');
        $this->update_model->markDeleted($update_id);
        echo $this->load->view('update/delete_undo', array('update_id' => $update_id), true);
    }
    
    function update_undo_delete($update_id){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        $this->load->model('update_model');
        $this->update_model->unMarkDeleted($update_id);
        $update = $this->update_model->getSingle($update_id);
        echo $this->load->view('update/updates_list', array('updates' => array(0 => $update)), true);
    }
    
    function display_posts_list($username, $page, $last_id){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        //Load updates information
        $this->load->model('update_model');
        $data['updates'] = $this->update_model->getPostedUpdates($username, $page, true, $last_id);
        echo $this->load->view('update/updates_list', $data, true);
    }
    
    function get_new_posts_count($username, $user_id, $last_id, $json = false){
        // Language
        $this->lang->load('home', get_language_name());
        //Load updates information
        $this->load->model('update_model');
        
        $data['total_new'] = $this->update_model->countNewPostedUpdates($user_id, $last_id);
        $data['refresh_url'] = base_url('p/' + $username);
        
        if($json){
            echo json_encode(array(
                'html' => $this->load->view('update/refresh_message', $data, true),
                'count' => $data['total_new']
            ));
        } else {
            if($data['total_new'])
                echo $this->load->view('update/refresh_message', $data, true);
            else
                echo '';
        }
    }
    
    function display_new_posts($user_id, $last_id){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        //Load updates information
        $this->load->model('update_model');
        $data['updates'] = $this->update_model->getNewPostedUpdates($user_id, $last_id);
        echo $this->load->view('update/updates_list', $data, true);
    }
    
    function display_comment_form($id)
    {
        // Language
        $this->lang->load('home', get_language_name());
        echo $this->load->view('update/comment_form', array('update_id' => $id, 'list' => true), true);
    }
    
    function display_comment_list($id)
    {
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        $this->load->model('update_model');
        $comments = $this->update_model->getComments($id);
        echo $this->load->view('update/comment_list', array('comments' => $comments), true);
    }
    
    function get_unread_notifications_count(){
        // Language
        $this->lang->load('general', get_language_name());
        //Load notifications information
        $this->load->model('notification_model');
        $count = $this->notification_model->unreadCount($this->session->userdata('id'));
        
        if($count > 0)
            $html = lang('general_notifications').'<span id="notification-count">' . $count . '</span>';
        else
            $html = lang('general_notifications');
        
        echo json_encode(array('html' => $html, 'count' => $count));
    }
    
    function display_notifications_list($page){
        // Language
        $this->lang->load('notifications', get_language_name());
        $this->lang->load('home', get_language_name());
        $this->lang->load('date', get_language_name());
        //Load notifications information
        $this->load->model('notification_model');
        $data['notifications'] = $this->notification_model->get($this->session->userdata('id'), $page);
        echo $this->load->view('notification/notifications_list', $data, true);
    }
    
    function display_search_list($term, $page){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('user', get_language_name());
        //Load updates information
        $this->load->model('user_model');
        $data['search_results'] = $this->user_model->search($term, $this->session->userdata('id'), $page);
        echo $this->load->view('search/search_list', $data, true);
    }

    function display_users_list($type, $username, $page){
        // Language
        $this->lang->load('home', get_language_name());
        $this->lang->load('user', get_language_name());
        //Load users list
        $this->load->model('user_model');
        $user_data = $this->user_model->get(array('username' => $username));
        $data['users'] = $this->user_model->getViewOrShow($user_data['id'], ($type == 'viewing')?'view':'show', $page);
        echo $this->load->view('profile/users_list', $data, true);
    }
        
}