<?php
/*
    PHP Class  : user.php
    Created on : 30/11/2009, 19:57:33
    Author     : Oscar
    Description:
        User actions controller.
*/
class User extends CI_Controller {
    function User(){
        parent::__construct();
        
        if(!$this->session->userdata('logged_in'))
            redirect('home');
        
        $this->lang->load('home', get_language_name());
        $this->lang->load('user', get_language_name());
    }
    
    function index()
    {
        redirect('home');
    }
    
    /*
     * User Profile
     */
    function profile()
    {
        if($this->input->post('update')) {
            // Validate information
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('full_name', lang('user_profile_name'), 'trim|max_length[255]');
            $this->form_validation->set_rules('bio', lang('user_profile_bio'), 'trim|max_length[255]');
            $this->form_validation->set_rules('language', lang('user_profile_language'), 'required');
            $this->form_validation->set_rules('website', lang('user_profile_website'), 'trim|max_length[255]|prep_url');
            
            // User submitted form
            $data = array(
                'full_name' => $this->input->post('full_name'),
                'bio' => $this->input->post('bio'),
                'language' => $this->input->post('language'),
                'website' => $this->input->post('website'),
                'image_name' => $this->input->post('image_name'),
                'image_ext' => $this->input->post('image_ext')
            );
            
            if($this->form_validation->run() === false)  {
                // Error in validation
                styh_load_view('profile/profile_edit', $data);
                return;
            }
            
            // Posted information is valid
            $this->load->model('user_model');
            
            // Upload picture if user selected one
            if($_FILES['picture']['name'] != "") {
                $this->load->library('upload');

                $config = array(
                    'allowed_types' => 'gif|png|jpg|jpeg',
                    'upload_path' => getcwd().'/upload/profile/'.$this->session->userdata('username'),
                    'max_size' => 2048,
                    'overwrite' => true
                );
                $this->upload->initialize($config);

                // Create path
                if(is_dir($config['upload_path'])) {
                    empty_folder($config['upload_path']);
                } else {
                    if(!mkdir($config['upload_path'], 0755, true)){
                        $error = array('upload_error' => lang('user_profile_picture_error'));
                        log_message('error', "user->profile() - mkdir({$config['upload_path']})");
                        return;
                    }
                }

                // Run the upload
                if (!$this->upload->do_upload('picture')) {
                    // Problem in upload
                    $error = array('upload_error' => $this->upload->display_errors());
                    log_message('error', "user->profile() - do_upload() - {$error['upload_error']}");

                    styh_load_view('profile/profile_edit', $error);
                    return;
                }

                // Resize images
                $upload_data = $this->upload->data();
                $error = $this->user_model->prepare_image($upload_data);
                if($error){
                    log_message('error', "user->profile() - prepare_image() - {$error}");

                    styh_load_view('profile/profile_edit', array('upload_error' => $error));
                    return;
                }

                $data['image_name'] = $upload_data['raw_name'];
                $data['image_ext'] = $upload_data['file_ext'];
            }
            
            // Update user info in the database
            if(!$this->user_model->update($data)) {
                // Error when updating user
                log_message('error', "user->profile() - update()");
            }
            //styh_load_view('profile/profile_edit', $data);
            redirect('home');
        } else {
            $this->load->model('user_model');
            $user_data = $this->user_model->get(array('id' => $this->session->userdata('id')));
            
            $data = array(
                'full_name' => $user_data['full_name'],
                'bio' => $user_data['bio'],
                'language' => $user_data['language'],
                'website' => $user_data['website'],
                'image_name' => $user_data['image_name'],
                'image_ext' => $user_data['image_ext']
            );

            styh_load_view('profile/profile_edit', $data);
        }
    }
    
    function settings($active = 'profile')
    {
        $user_id = $this->session->userdata('id');
        $message = '';
        
        $this->load->model('user_model');
        
        switch ($active) {
            case 'profile':
                $message = $this->_updateProfile();
        
                if($this->input->get('up') == 'ok')
                    $message = lang('user_profile_msg_ok');
                
                $user_data = $this->user_model->get(array('id' => $user_id));
                $data = array(
                    'full_name' => $user_data['full_name'],
                    'bio' => $user_data['bio'],
                    'language' => $user_data['language'],
                    'website' => $user_data['website'],
                    'image_name' => $user_data['image_name'],
                    'image_ext' => $user_data['image_ext']
                );
                break;
            case 'email':
                $message = $this->_updateEmail();
                
                $user_data = $this->user_model->get(array('id' => $user_id));
                $data = array(
                    'email' => $user_data['email']
                );
                break;
            case 'password':
                $message = $this->_updatePassword();
                break;
            case 'notification':
                $message = $this->_updateNotification();
                
                $extra = $this->user_model->get_extra($user_id);
                $data = array(
                    'notify_connect' => $extra['notifications']['notify_connect'],
                    'notify_mention' => $extra['notifications']['notify_mention'],
                    'notify_comment' => $extra['notifications']['notify_comment'],
                    'notify_reshare' => $extra['notifications']['notify_reshare'],
                    'notify_pending' => $extra['notifications']['notify_pending'],
                    'notify_other' => $extra['notifications']['notify_other']
                );
                break;
            case 'twitter':
                $data['oauth'] = $this->user_model->get_oauth('twitter', false, $user_id);
                break;
            case 'remove':
                // If user click to remove profile, user->remove() will be executed
                // No need to do anything
                break;

            default:
                break;
        }
        
        $data['active'] = $active;
        $data['message'] = $message;
        
        $header = array(
            'seo_title' => ($active == 'remove')?lang('user_profile_remove'):lang('user_profile_title'),
            'seo_description' => lang('user_profile_description'),
            'seo_keywords' => '[default]'
        );
        
        styh_load_view('settings/settings', $data, $header);
    }
    
    function remove($confirm = false){
        $this->load->model('user_model');
        
        $message = '';
        
        if($confirm == 'confirm') {
            if($this->user_model->markDeleted($this->session->userdata('id'))){
                redirect('sign/out');
            }
            //
            // TODO: Create a proper farewell screen
            //
            $message = lang('user_profile_remove_error');
        }
        
        $user_data = $this->user_model->get(array('id' => $this->session->userdata('id')));

        $data = array(
            'full_name' => $user_data['full_name'],
            'bio' => $user_data['bio'],
            'language' => $user_data['language'],
            'website' => $user_data['website'],
            'image_name' => $user_data['image_name'],
            'image_ext' => $user_data['image_ext'],
            'email' => $user_data['email'],
            'active' => 'remove',
            'remove' => true,
            'message' => $message
        );
        
        $header = array(
            'seo_title' => lang('user_profile_remove'),
            'seo_description' => lang('user_profile_remove_description'),
            'seo_keywords' => '[default]'
        );
        
        styh_load_view('settings/settings', $data, $header);
    }
    
    function connect($username)
    {
        $this->load->model('user_model');
        $result = $this->user_model->connect($this->session->userdata('id'), $username);
        
        if(IS_AJAX) {
            if($result)
                echo json_encode(array('result' => 1));
            else
                echo json_encode(array('result' => 0));
            
            return;
        }
        
        redirect("p/$username");
    }

    function disconnect($username)
    {
        $this->load->model('user_model');
        $result = $this->user_model->disconnect($this->session->userdata('id'), $username);
        
        if(IS_AJAX) {
            if($result)
                echo json_encode(array('result' => 1));
            else
                echo json_encode(array('result' => 0));
            
            return;
        }
        
        redirect("p/$username");
    }
    
    function search()
    {
        // Load user information
        $this->load->model('user_model');
        $user_data = $this->user_model->get(array('id' => $this->session->userdata('id')));

        $data = array(
            'username' => $user_data['username'],
            'full_name' => $user_data['full_name'],
            'bio' => $user_data['bio'],
            'website' => $user_data['website'],
            'image_name' => $user_data['image_name'],
            'image_ext' => $user_data['image_ext']
        );

        // Get query strings parameters
        $data['s'] = $this->input->get('s', TRUE);
        $page = $this->input->get('page', TRUE);
        if(!$page)
            $page = 1;
        
        // Run search in the database
        $data['search_results'] = $this->user_model->search($data['s'], $this->session->userdata('id'), $page);
        
        // Activate updates JS
        $header = array(
            'seo_title' => sprintf(lang('user_search_title'), $data['s']),
            'seo_description' => lang('user_search_description'),
            'seo_keywords' => '[default]'
        );
        
        // Pagination
        $this->load->library('pagination');

        $config['base_url'] = base_url('user/search?s='.$data['s']);
        $config['total_rows'] = $this->user_model->countSearch($data['s'], $this->session->userdata('id'));
        $config['per_page'] = PER_PAGE_DEFAULT;
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'page';

        $this->pagination->initialize($config); 

        $data['pagination'] = $this->pagination->create_links();
        $data['total_pages'] = $config['total_rows'] / $config['per_page'];
        
        styh_load_view('search/search_user', $data, $header);
    }
    
    function recommend()
    {
        // SEO Info
        $header = array(
            'seo_title' => lang('user_recommend_title'),
            'seo_description' => lang('user_recommend_description'),
            'seo_keywords' => '[default]'
        );
        
        $this->load->model('user_model');
        $user_data = $this->user_model->get(array('id' => $this->session->userdata('id')));

        $data = array(
            'username' => $user_data['username'],
            'full_name' => $user_data['full_name'],
            'bio' => $user_data['bio'],
            'website' => $user_data['website'],
            'image_name' => $user_data['image_name'],
            'image_ext' => $user_data['image_ext']
        );
        
        $data['recommended_users'] = $this->user_model->getRecommendedUsers($this->session->userdata('id'), 0);
        
        styh_load_view('recommend', $data, $header);
    }
    
    function remove_twitter()
    {
        $this->load->model('user_model');
        $this->user_model->remove_oauth($this->session->userdata('id'));
        
        redirect('user/settings/twitter');
    }
    
    /*
    * Private methods
    */
    private function _updateProfile(){
        if( ! empty( $_POST ) ) {
                $error_msg = lang('user_profile_msg_error');

            // Validate information
            $this->load->library('form_validation');

            $this->form_validation->set_rules('full_name', lang('user_profile_name'), 'trim|max_length[255]');
            $this->form_validation->set_rules('bio', lang('user_profile_bio'), 'trim|max_length[255]');
            $this->form_validation->set_rules('language', lang('user_profile_language'), 'required');
            $this->form_validation->set_rules('website', lang('user_profile_website'), 'trim|max_length[255]|prep_url');

            if($this->form_validation->run() === false)  {
                return '';
            }

            // User submitted form
            $data = array(
                'full_name' => $this->input->post('full_name'),
                'bio' => $this->input->post('bio'),
                'language' => $this->input->post('language'),
                'website' => $this->input->post('website')
            );

            // Posted information is valid
            $this->load->model('user_model');

            // Upload picture if user selected one
            if(isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {
                $this->load->library('upload');

                $config = array(
                    'allowed_types' => 'gif|png|jpg|jpeg',
                    'upload_path' => getcwd().'/upload/profile/'.$this->session->userdata('username'),
                    'max_size' => 1048 * 8,
                    'overwrite' => true
                );
                $this->upload->initialize($config);

                // Create path
                if(is_dir($config['upload_path'])) {
                    empty_folder($config['upload_path']);
                } else {
                    if(!mkdir($config['upload_path'], 0755, true)){
                        log_message('error', "user->_updateProfile() - mkdir({$config['upload_path']})");
                        return $error_msg;
                    }
                }

                // Run the upload
                if (!$this->upload->do_upload('picture')) {
                    // Problem in upload
                    log_message('error', "user->_updateProfile() - do_upload() - {$this->upload->display_errors()}");
                    return $error_msg;
                }
                
                // Resize images
                $upload_data = $this->upload->data();
                $error = $this->user_model->prepare_image($upload_data);
                if($error){
                    log_message('error', "user->_updateProfile() - prepare_image() - {$error}");
                    return $error_msg;
                }

                $data['image_name'] = $upload_data['raw_name'];
                $data['image_ext'] = $upload_data['file_ext'];
                
                // Update session
                $sess_update = array(
                    'image_name' => $data['image_name'],
                    'image_ext' => $data['image_ext']
                );
                $this->session->set_userdata($sess_update);
            }

            // Update user info in the database
            if(!$this->user_model->update($data)) {
                // Error when updating user
                log_message('error', "user->_updateProfile() - update() - {$this->db->_error_message()}");
                return $error_msg;
            }
            
            // Language
            setcookie('language', $data['language'], time()+60*60*24*30, '/');
            
            redirect(base_url('user/settings/profile?up=ok'));
        }
    }
    
    private function _updateEmail(){
        if( ! empty( $_POST ) ) {
            $error_msg = lang('user_profile_email_error');
            
            // Validate information
            $this->load->library('form_validation');

            $this->form_validation->set_rules('email', lang('user_profile_email'), 'trim|required|valid_email|is_unique[user.email]|max_length[255]');

            if($this->form_validation->run() === false)  {
                return '';
            }

            // User submitted form
            $data = array(
                'email' => $this->input->post('email')
            );
            
            $this->load->model('user_model');
            
            // Update user info in the database
            if(!$this->user_model->update($data)) {
                // Error when updating user
                log_message('error', "user->_updateEmail() - update() - {$this->db->_error_message()}");
                return $error_msg;
            }

            return lang('user_profile_email_ok');
        }
    }
    
    private function _updatePassword(){
        if( ! empty( $_POST ) ) {
            $this->load->model('user_model');
            
            $error_msg = lang('user_profile_password_error');
            
            // Validate information
            $this->load->library('form_validation');

            $this->form_validation->set_rules('current_password', lang('user_profile_password_current'), 'trim|required|validate_password');
            $this->form_validation->set_rules('password', lang('user_profile_password_new'), 'trim|required|min_length[6]');
            $this->form_validation->set_rules('password_confirm', lang('user_profile_password_confirm'), 'trim|required|matches[password]');

            if($this->form_validation->run() === false)  {
                return '';
            }

            // User submitted form
            $data = array(
                'password' => $this->input->post('password')
            );
            
            // Update user info in the database
            if(!$this->user_model->update($data)) {
                // Error when updating user
                log_message('error', "user->_updatePassword() - update() - {$this->db->_error_message()}");
                return $error_msg;
            }

            return lang('user_profile_password_ok');
        }
    }
    
    private function _updateNotification(){
        if( ! empty( $_POST ) ) {
            $error_msg = lang('user_profile_notification_error');
            
            // Validate information
            $this->load->library('form_validation');

            $this->form_validation->set_rules('notify_connect', lang('user_profile_notification_connect'), '');
            $this->form_validation->set_rules('notify_mention', lang('user_profile_notification_mention'), '');
            $this->form_validation->set_rules('notify_comment', lang('user_profile_notification_comment'), '');
            $this->form_validation->set_rules('notify_reshare', lang('user_profile_notification_reshare'), '');
            $this->form_validation->set_rules('notify_pending', lang('user_profile_notification_pending'), '');
            $this->form_validation->set_rules('notify_other', lang('user_profile_notification_other'), '');

            if($this->form_validation->run() === false)  {
                return '';
            }

            // User submitted form
            $data = array(
                'user_id' => $this->session->userdata('id'),
                'notifications' => array(
                    'notify_connect' => $this->input->post('notify_connect'),
                    'notify_mention' => $this->input->post('notify_mention'),
                    'notify_comment' => $this->input->post('notify_comment'),
                    'notify_reshare' => $this->input->post('notify_reshare'),
                    'notify_pending' => $this->input->post('notify_pending'),
                    'notify_other' => $this->input->post('notify_other')
                    )
            );
            
            $this->load->model('user_model');
            
            // Update user info in the database
            if(!$this->user_model->update_extra($data)) {
                // Error when updating user
                log_message('error', "user->_updateNotification() - update() - {$this->db->_error_message()}");
                return $error_msg;
            }

            return lang('user_profile_notification_ok');
        }
    }
    
}