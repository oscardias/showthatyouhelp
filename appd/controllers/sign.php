<?php
/*
    PHP Class  : sign.php
    Created on : 13/07/2012
    Author     : Oscar
    Description:
        Control user sign in/up/out.
*/
class Sign extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        
        // Language
        $this->lang->load('home', get_language_name());
    }
    
    function index()
    {
        redirect('home');
    }
    
    function in($token = false)
    {
        // Check if user has submitted
        if ($this->input->post('username') !== FALSE) {
            
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            
            $this->load->model('user_model');
            if(!$token)
                $id = $this->user_model->validate($username, $password);
            else
                $id = $this->user_model->validate_previous($username, $password, $token);
            
            if($id)
            {
                $this->_login($id);
                // Redirect
                if($this->input->post('redirect_url'))
                    redirect($this->input->post('redirect_url'));
                else
                    redirect('home');
            }
            
            $header = array(
                'hide_actions' => true,
                'extra_js' => array(
                    base_url('js/sign_in')
                    )
            );
            
            $data = array(
                'username' => $username,
                'password' => $password,
                'token' => $token,
                'invalid' => true,
                'redirect_url' => $this->input->post('redirect_url')
            );
            
            styh_load_view('home', $data, $header);
            
        } else {
            
            $header = array(
                'hide_actions' => true,
                'extra_js' => array(
                    base_url('js/sign_in')
                    )
            );
            
            $data = array(
                'username' => '',
                'password' => '',
                'token' => $token,
                'invalid' => false
            );
            
            if($this->input->get('redirect'))
                $data['redirect'] = $this->input->get('redirect');
            
            styh_load_view('home', $data, $header);
            
        }
    }
    
    function recover()
    {
        $invalid = false;
        
        if ($this->input->post('username') !== FALSE) {
            
            $this->load->model('user_model');
            $invalid = ! $this->user_model->sendRecoverEmail($this->input->post('username'));
            
        }

        $header = array(
            'hide_actions' => true,
            'extra_js' => array(
                base_url('js/sign_in')
                )
        );

        $data = array(
            'username' => $this->input->post('username'),
            'password' => '',
            'invalid' => $invalid
        );
        
        if($this->input->post('username') && !$invalid)
            $data['password_sent'] = true;
        else
            $data['recover_password'] = true;
        
        styh_load_view('home', $data, $header);
    }
    
    function popup()
    {
        // Check for sign in post
        if ($this->input->post('username') !== FALSE) {
            // Validate credentials
            $this->load->model('user_model');
            $id = $this->user_model->validate($this->input->post('username'), $this->input->post('password'));
            if($id)
            {
                $this->_login($id);
                // Redirect to share functionality
                $url = $this->input->post('share');
                redirect('share'.($url?'?url='.$url:''));
            }

            $data = array(
                'invalid' => true,
                'share' => $this->input->post('share')
            );
            $this->load->view('popup/signin', $data);
        } else {
            // Show popup signin
            $data = array(
                'invalid' => false,
                'share' => $this->input->get('url')
            );
            $this->load->view('popup/signin', $data);
        }
    }
    
    function up($token = false)
    {
        $this->lang->load('user', get_language_name());
        
        // Check if token was defined or posted
        if(!$token)
            $token = $this->input->post('token');
        
        // Check if invite token is valid
        $this->load->model('user_model');
        $user_invite = $this->user_model->checkInvite($token);
        
        // Open sign up for anyone - No need for user invite
        //if(!$user_invite)
        //    redirect('home');
        
        // SEO Info
        $header = array(
            'seo_title' => lang('home_sign_up'),
            'seo_description' => lang('home_signup_description'),
            'seo_keywords' => '[default]',
            'seo_add' => array(
                    'card' => 'summary',
                    'site' => '@showthatyouhelp',
                    'url' => base_url('sign/up')
                )
        );
        
        // Send token to view
        $view = array(
            'token' => $token
        );
            
        if($this->input->post('register')) {
            // User submitted form
            // Validate information
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('username', lang('user_profile_username'), 'trim|required|is_unique[user.username]|username_check|min_length[3]|max_length[22]');
            $this->form_validation->set_rules('password', lang('user_profile_password'), 'trim|min_length[6]');
            $this->form_validation->set_rules('password_confirm', lang('user_profile_password_confirm'), 'trim|required|required|matches[password]');
            $this->form_validation->set_rules('full_name', lang('user_profile_name'), 'trim|max_length[255]');
            $this->form_validation->set_rules('email', lang('user_profile_email'), 'trim|required|valid_email|is_unique[user.email]|max_length[255]');
            $this->form_validation->set_rules('bio', lang('user_profile_bio'), 'trim|max_length[255]');
            $this->form_validation->set_rules('website', lang('user_profile_website'), 'trim|max_length[255]|prep_url');
            
            if($this->form_validation->run() === false)  {
                // Error in validation
                styh_load_view('signup', $view, $header);
                return;
            }
            
            // Posted information is valid

            // User info array
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'language' => (isset($_COOKIE['language']))?$_COOKIE['language']:'en',
                'bio' => $this->input->post('bio'),
                'website' => $this->input->post('website')
            );

            // Upload picture if user selected one
            if($_FILES['picture']['name'] != "") {
                $this->load->library('upload');

                $config = array(
                    'allowed_types' => 'gif|png|jpg|jpeg',
                    'upload_path' => './upload/profile/'.$data['username'],
                    'max_size' => 1048 * 8,
                    'overwrite' => true
                );
                $this->upload->initialize($config);

                // Create path
                if(is_dir($config['upload_path'])) {
                    empty_folder($config['upload_path']);
                } else {
                    if(!mkdir($config['upload_path'], 0755, true)){
                        $error = array('upload_error' => lang('home_error_upload'));
                        log_message('error', "sign->up() - mkdir({$config['upload_path']})");
                        return;
                    }
                }

                // Run the upload
                if (!$this->upload->do_upload('picture')) {
                    // Problem in upload
                    $error = array('upload_error' => $this->upload->display_errors());
                    log_message('error', "sign->up() - do_upload() - {$error['upload_error']}");

                    styh_load_view('signup', $error, $header);
                    return;
                }

                // Resize images
                $upload_data = $this->upload->data();
                $error = $this->user_model->prepare_image($upload_data);
                if($error){
                    log_message('error', "sign->up() - prepare_image() - {$error}");

                    styh_load_view('signup', array('token' => $token, 'upload_error' => $error), $header);
                    return;
                }

                $data['image_name'] = $upload_data['raw_name'];
                $data['image_ext'] = $upload_data['file_ext'];
            } else {
                // No image selected
                $data['image_name'] = '';
                $data['image_ext'] = '';
            }

            // Create user in the database
            $id = $this->user_model->create($data);

            if($id) {
                // User created
                // Connect with invite
                if($user_invite) {
                    $this->user_model->connect($user_invite['user_id'], $data['username']);
                    $invited_by = $this->user_model->get(array('id' =>$user_invite['user_id']));
                    $this->user_model->connect($id, $invited_by['username']);
                    $this->user_model->clearInvite($user_invite['user_invite_id']);
                }
                
                // Login user
                $this->_login($id);

                redirect('user/recommend');
            } else {
                // Error when creating user
                log_message('error', "sign->up() - create()");
                
                styh_load_view('signup', $view, $header);
            }
            
        } else {
            // Loading page before submission
            styh_load_view('signup', $view, $header);
        }
    }

    
    function out()
    {
        $this->_logout();
        redirect('home');
    }
    
    // Twitter
    function twitter_sign()
    {
        
        $this->config->load('config_twitter');
        $this->load->library('SignTwitter', array(
            'key'    => $this->config->item('consumer_key'),
            'secret' => $this->config->item('consumer_secret')
        ));
        
        $request_token = $this->signtwitter->getRequestToken(base_url('sign/twitter'));
        
        if($request_token === false)
            redirect('sign/twitter_sign');
        
        if($this->signtwitter->http_code == 200) {
            $this->session->set_userdata(array(
                'oauth_token' => $request_token['oauth_token'],
                'oauth_token_secret' => $request_token['oauth_token_secret']
            ));

            $url = $this->signtwitter->getAuthorizeURL($request_token['oauth_token']);
            redirect($url);
        } else {
            log_message('error', "sign->twitter_sign()");
            show_error(lang('home_twitter_error'));
        }
        
    }
    
    function twitter()
    {
        $this->lang->load('user', get_language_name());
        
        $this->load->model('user_model');
        
        // SEO Info
        $header = array(
            'seo_title' => lang('home_sign_up'),
            'seo_description' => lang('home_signup_description'),
            'seo_keywords' => '[default]',
            'seo_add' => array(
                    'card' => 'summary',
                    'site' => '@showthatyouhelp',
                    'url' => base_url('sign/twitter')
                )
        );
        
        // Posted info
        if($this->input->post('register')) {
            // User submitted form
            $view = array(
                'username'  => $this->input->post('username'),
                'full_name' => $this->input->post('full_name'),
                'bio'       => $this->input->post('bio'),
                'website'   => $this->input->post('website'),
                'picture'   => $this->input->post('picture')
            );
            
            // Validate information
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('username', lang('user_profile_username'), 'trim|required|is_unique[user.username]|username_check|min_length[3]|max_length[22]');
            $this->form_validation->set_rules('email', lang('user_profile_email'), 'trim|required|valid_email|is_unique[user.email]|max_length[255]');
            
            if($this->form_validation->run() === false)  {
                // Error in validation
                styh_load_view('signup_twitter', $view, $header);
                return;
            }
            
            // Posted information is valid

            // User info array
            $data = array(
                'username' => $this->input->post('username'),
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'language' => (isset($_COOKIE['language']))?$_COOKIE['language']:'en',
                'bio' => $this->input->post('bio'),
                'website' => $this->input->post('website')
            );

            // Upload picture from Twitter
            if($this->input->post('picture')) {
                
                $origin = $this->input->post('picture');
                $filename = basename($origin);
                $path = getcwd().'/upload/profile/'.$data['username'].'/';
                
                if(is_dir($path)) {
                    empty_folder($path);
                } else {
                    if(!mkdir($path, 0755, true)){
                        log_message('error', "sign->twitter() - mkdir({$path})");
                        return;
                    }
                }
                
                if(file_get_contents_file($origin, $path.$filename)) {
                    $path_parts = pathinfo($path.$filename);
                    list($width, $height, $type, $attr) = getimagesize($path.'/'.$filename);
                    
                    // Resize images
                    $error = $this->user_model->prepare_image(array(
                        'full_path' => $path.$filename,
                        'file_path' => $path,
                        'raw_name' => $path_parts['filename'],
                        'file_ext' => '.' . $path_parts['extension'],
                        'image_width' => $width,
                        'image_height' => $height
                    ));
                    if($error){
                        log_message('error', "sign->twitter() - prepare_image() - {$error}");

                        styh_load_view('signup_twitter', array('upload_error' => $error), $header);
                        return;
                    }

                    $data['image_name'] = $path_parts['filename'];
                    $data['image_ext'] = '.' . $path_parts['extension'];
                } else {
                    $data['image_name'] = '';
                    $data['image_ext'] = '';
                }
            }

            // Create user in the database
            $id = $this->user_model->create($data);

            if($id) {
                // User created
                // Login user
                $this->_login($id);

                redirect('user/recommend');
            } else {
                // Error when creating user
                log_message('error', "sign->twitter() - create()");
                
                styh_load_view('signup', $view, $header);
                return;
            }
            
        }
        
        // First access - no post
        if($this->input->get('oauth_verifier') && $this->session->userdata('oauth_token') && $this->session->userdata('oauth_token_secret')){  
            // TwitterOAuth instance, with two new parameters we got in twitter_login.php  
            $this->config->load('config_twitter');
            $this->load->library('SignTwitter', array(
                'key'    => $this->config->item('consumer_key'),
                'secret' => $this->config->item('consumer_secret'),
                'oauth_token' => $this->session->userdata('oauth_token'),
                'oauth_token_secret' => $this->session->userdata('oauth_token_secret')
            ));
            // Let's request the access token  
            $access_token = $this->signtwitter->getAccessToken($this->input->get('oauth_verifier')); 
            
            // Let's get the user's info 
            $oauth_info = $this->signtwitter->get('account/verify_credentials'); 
            
            // Check if user exists
            $user_oauth = $this->user_model->get_oauth('twitter', $oauth_info->id);
            
            if($user_oauth) {
                // Update oauth
                $this->user_model->update_oauth(array(
                    'user_id'        => $user_oauth['user_id'],
                    'username'       => $oauth_info->screen_name,
                    'oauth_provider' => 'twitter',
                    'oauth_uid'      => $oauth_info->id,
                    'oauth_token'    => $access_token['oauth_token'],
                    'oauth_secret'   => $access_token['oauth_token_secret']
                ));
                
                // Sign user in
                $this->_login($user_oauth['user_id']);
                redirect('home');
            } else {
                if($this->session->userdata('logged_in')) {
                    // Already logged - associate users
                    $this->user_model->create_oauth(array(
                        'user_id'        => $this->session->userdata('id'),
                        'username'       => $oauth_info->screen_name,
                        'oauth_provider' => 'twitter',
                        'oauth_uid'      => $oauth_info->id,
                        'oauth_token'    => $access_token['oauth_token'],
                        'oauth_secret'   => $access_token['oauth_token_secret']
                    ));
                    redirect('home');
                } else {
                    // New user
                    // Save tokens in a session var 
                    $this->session->set_userdata(array(
                        'oauth_username'     => $oauth_info->screen_name,
                        'oauth_token'        => $access_token['oauth_token'],
                        'oauth_token_secret' => $access_token['oauth_token_secret'],
                        'oauth_uid'          => $oauth_info->id
                    ));

                    // Take him to registration
                    $data = array(
                        'full_name' => $oauth_info->name,
                        'username'  => $oauth_info->screen_name,
                        'bio'       => $oauth_info->description,
                        'website'   => $oauth_info->url,
                        'picture'   => $oauth_info->profile_image_url
                    );

                    // Finish registration
                    styh_load_view('signup_twitter', $data, $header);
                }
            }
        } else {  
            // Something's missing, go back to square 1  
            redirect('home');
        } 
    }

    /*
     * User Log In
     */
    function _login($id)
    {
        $this->load->model('user_model');
        $user = $this->user_model->get(array('id' => $id));
        
        // Set last login
        $this->user_model->set_last_login($id);
        
        $data = array(
            'id' => $id,
            'username' => $user['username'],
            'image_name' => $user['image_name'],
            'image_ext' => $user['image_ext'],
            'logged_in' => true
        );
        
        // Language
        setcookie('language', $user['language'], time()+60*60*24*30, '/');

        $this->session->set_userdata($data);
    }

    /*
     * User Log Out
     */
    function _logout()
    {
        $data = array(
            'id' => '',
            'username' => '',
            'logged_in' => ''
        );
        $this->session->unset_userdata($data);
    }
}