<?php
/*
    PHP Class  : home.php
    Created on : 30/11/2009, 13:30:33
    Author     : Oscar
    Description:
        Main page controller.
*/
class Home extends CI_Controller {
    
    private $error_msg = '';
    
    function Home()
    {
        parent::__construct();
        
        // Language
        $this->lang->load('home', get_language_name());
    }
    
    function index($page = 1, $type = 'text')
    {
        if($this->session->userdata('logged_in')){
            // Time translation
            $this->lang->load('date', get_language_name());
        
            // Load user information
            $this->load->model('user_model');
            $user_data = $this->user_model->get(array('id' => $this->session->userdata('id')));
            
            $data = array(
                'username' => $user_data['username'],
                'full_name' => $user_data['full_name'],
                'bio' => $user_data['bio'],
                'website' => $user_data['website'],
                'image_name' => $user_data['image_name'],
                'image_ext' => $user_data['image_ext'],
                'invites' => $user_data['invites']
            );
            
            $data['viewing'] = $this->user_model->getViewOrShow($this->session->userdata('id'), 'view', 0);
            $data['viewing_total'] = $this->user_model->countViewOrShow($this->session->userdata('id'));
            
            $data['showing'] = $this->user_model->getViewOrShow($this->session->userdata('id'), 'show');
            $data['showing_total'] = $this->user_model->countViewOrShow($this->session->userdata('id'), 'show');
            
            $data['recommended_users'] = $this->user_model->getRecommendedUsers($this->session->userdata('id'), $data['viewing_total']);

            //Load updates information
            $this->load->model('update_model');
            $data['updates'] = $this->update_model->getViewerUpdates($this->session->userdata('id'), $page);

            // Activate updates JS
            $header = array(
                'seo_title' => $this->lang->line('home_seo_title'),
                'seo_description' => $this->lang->line('home_seo_description'),
                'seo_add' => array(
                        'card' => 'summary',
                        'site' => '@showthatyouhelp',
                        'url' => base_url()
                    )
            );
            
            // Type of sharing method
            $data['sharing'] = $type;
            
            // Pagination
            $this->load->library('pagination');

            $config['base_url'] = base_url('home/index/');
            $config['total_rows'] = $this->update_model->countViewerUpdates($this->session->userdata('id'));;
            $config['per_page'] = PER_PAGE_UPDATES;
            
            $this->pagination->initialize($config); 

            $data['pagination'] = $this->pagination->create_links();
            $data['total_pages'] = $config['total_rows'] / $config['per_page'];
            
            // Meta next/prev
            if($page <= $data['total_pages'])
                $header['seo_add']['next'] = base_url('home/index/' . ($page + 1));
            if($page > 1)
                $header['seo_add']['prev'] = base_url('home/index/' . ($page - 1));
            
            // Error handling
            if($this->error_msg) {
                $data['error_msg'] = $this->error_msg;
                $this->error_msg = '';
            }
                    
            styh_load_view('home_signed', $data, $header);
        } else {
            $header = array(
                'hide_actions' => true,
                'extra_js' => array(
                    base_url('js/sign_in')
                    )
            );
            
            $this->load->model('update_model');
            $data = array(
                'username' => '',
                'password' => '',
                'invalid' => false,
                'latest_updates' => $this->update_model->getLatestUpdates()
            );
            
            // Check if language was changed
            if(isset($_GET['lang'])) {
                setcookie('language', $_GET['lang'], time()+60*60*24*30, '/');
                redirect('home');
            }            
            
            // Set redirect
            if($this->input->get('redirect'))
                $data['redirect_url'] = $this->input->get('redirect');
            
            styh_load_view('home', $data, $header);
        }
    }
    
    function share($type = 'text')
    {
        if($this->session->userdata('logged_in')){
            if($this->input->post('share') || $this->input->post('remote')) {
                // Validate form
                $this->load->library('form_validation');
                
                // Check mandatory information
                switch ($this->input->post('type')) {
                    case 'text':
                        $type = 'text';
                        $this->form_validation->set_rules('comment', lang('home_share_text_valid'), 'trim|required|htmlspecialchars');
                        break;
                    case 'link':
                        $type = 'link';
                        $this->form_validation->set_rules('comment', lang('home_share_text_valid'), 'trim|htmlspecialchars');
                        $this->form_validation->set_rules('url', lang('home_share_link_valid'), 'trim|required|prep_url');
                        break;
                    case 'video':
                        $type = 'video';
                        $this->form_validation->set_rules('comment', lang('home_share_text_valid'), 'trim|htmlspecialchars');
                        $this->form_validation->set_rules('video', lang('home_share_video_valid'), 'trim|required|prep_url');
                        break;
                    case 'photo':
                        $type = 'photo';
                        $this->form_validation->set_rules('comment', lang('home_share_text_valid'), 'trim|htmlspecialchars');
                        $this->form_validation->set_rules('photo', lang('home_share_photo_valid'), 'callback_required_file[photo]');
                        break;
                }
                
                if($this->form_validation->run() === false)  {
                    if(IS_AJAX){
                        // Return error
                        echo json_encode(array(
                            'result' => 0,
                            'html' => $this->load->view('template/error_msg', array('error_msg' => strip_tags(validation_errors())), true),
                            'fields' => json_encode(array(
                                'comment' => form_error('comment'),
                                'url' => form_error('url'),
                                'video' => form_error('video'),
                                'photo' => form_error('photo')
                            ))
                        ));
                        return;
                    } else {
                        $this->error_msg = strip_tags(validation_errors());
                        $this->index(1, $type);
                        return;
                    }
                }
                
                $this->load->model('update_model');
                
                // Save update information
                $data = array(
                    'user' => $this->session->userdata('id'),
                    'type' => $this->input->post('type'),
                    'comment' => $this->input->post('comment')
                );
                
                if($this->input->post('remote')) {
                    $remote_data = array(
                        'remote' => $this->input->post('remote'),
                        'title' => $this->input->post('title'),
                        'description' => $this->input->post('description'),
                        'image' => $this->input->post('image'),
                        'icon' => $this->input->post('icon'),
                        'domain' => $this->input->post('domain'),
                        'site_name' => $this->input->post('site_name')
                    );
                } else {
                    $remote_data = false;
                }

                // Create update
                $id = $this->update_model->create(
                        $data,
                        array(
                            'link' => $this->input->post('url'),
                            'video' => $this->input->post('video')
                        ),
                        $remote_data
                    );

                // Check if it is an AJAX call
                if(IS_AJAX){
                    if($id) {
                        // Return success
                        echo json_encode(array(
                            'result' => 1
                        ));
                        return;
                    } else {
                        // Return error
                        echo json_encode(array(
                            'result' => 0,
                            'html' => $this->load->view('template/error_msg', array(
                                'error_msg' => lang('home_error_generic')
                                ), true)
                        ));
                        return;
                    }
                } else {
                    if(!$id)
                        $this->error_msg = $this->update_model->error_msg;
                }
                $this->index(1, $type);
            } else {
                $this->index(1, $type);
            }
        } else {
            redirect('home');
        }
    }
    
    function remove($update_id)
    {
        if($this->session->userdata('logged_in')){
            $this->load->model('update_model');
            $this->update_model->markDeleted($update_id);
        }
        
        redirect('home');
    }
    
    public function required_file($str, $field)
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['name'] == '')
        {
            $this->form_validation->set_message('required_file', lang('home_error_required_file'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    function notifications($page = 1, $mark_all_read = false){
        if($this->session->userdata('logged_in')){
            // Language
            $this->lang->load('notifications', get_language_name());
            // Time translation
            $this->lang->load('date', get_language_name());        
            
            $this->load->model('notification_model');

            // Mark notifications as read
            if($mark_all_read){
                $this->notification_model->markRead($this->session->userdata('id'));

                if(IS_AJAX) {
                    echo json_encode(array(
                        'result' => 1
                    ));
                    return;
                }
            }

            // Get notifications
            $data = array(
                'notifications' => $this->notification_model->get($this->session->userdata('id'), $page)
                );

            if(IS_AJAX) {
                echo $this->load->view('notification/list_popup', array('notifications' => $data['notifications']), true);
                return;
            }

            // Load user information
            $this->load->model('user_model');
            $user_data = $this->user_model->get(array('id' => $this->session->userdata('id')));

            $data = array(
                'username' => $user_data['username'],
                'full_name' => $user_data['full_name'],
                'bio' => $user_data['bio'],
                'website' => $user_data['website'],
                'image_name' => $user_data['image_name'],
                'image_ext' => $user_data['image_ext'],
                'notifications' => $data['notifications']
            );

            // Pagination
            $this->load->library('pagination');

            $config['base_url'] = base_url('home/notifications/');
            $config['total_rows'] = $this->notification_model->countNotifications($this->session->userdata('id'));
            $config['per_page'] = PER_PAGE_DEFAULT;

            $this->pagination->initialize($config); 

            $data['pagination'] = $this->pagination->create_links();
            $data['total_pages'] = $config['total_rows'] / $config['per_page'];
            $data['current_page'] = $page;

            // Activate updates JS
            $header = array(
                'seo_title' => lang('notifications_title'),
                'seo_description' => lang('notifications_seo_description'),
                'seo_keywords' => '[default]',
                'seo_add' => array(
                        'card' => 'summary',
                        'site' => '@showthatyouhelp',
                        'url' => base_url('home/notifications/')
                    )
            );

            // Meta next/prev
            if($page <= $data['total_pages'])
                $header['seo_add']['next'] = base_url('home/notifications/' . ($page + 1));
            if($page > 1)
                $header['seo_add']['prev'] = base_url('home/notifications/' . ($page - 1));

            styh_load_view('notification/list_full', $data, $header);
        } else {
            redirect('home?redirect=' .  urlencode(base_url('home/notifications')));
        }
    }    
    
    function invite(){
        if($this->session->userdata('logged_in')){
            // Validate information
            $this->load->library('form_validation');

            $this->form_validation->set_rules('invite_email', lang('home_invite_text'), 'trim|required|valid_email');

            if($this->form_validation->run() === false)  {
                if(IS_AJAX){
                    echo json_encode(array(
                        'result' => 0,
                        'error' => form_error('invite_email', '<p class="error-msg">')
                    ));
                    return;
                } else
                    $this->index ();
            }

            $this->load->model('user_model');
            $this->user_model->invite_user($this->session->userdata('id'), $this->input->post('invite_email'));

            if(IS_AJAX){
                echo json_encode(array(
                    'result' => 1
                ));
                return;
            }

            $this->index ();
        } else {
            redirect('home');
        }
    }
}