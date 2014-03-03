<?php
/*
    PHP Class  : share.php
    Created on : 31/07/2012, 13:13
    Author     : Oscar
    Description:
        Share functionality controller.
*/
class Share extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        
        // Language
        $lang = get_language_name();
        $this->lang->load('home', $lang);
        $this->lang->load('general', $lang);
        $this->lang->load('share', $lang);
    }
    
    function index()
    {
        // Check if user is signed in
        if($this->session->userdata('logged_in')) {
            // Check for url query string
            $url = $this->input->get('url');
            
            if(!$url)
                $url = $this->input->post('url');
            
            if($url) {
                if($this->input->post('share') || $this->input->post('remote')) {
                    // Save update information
                    $data = array(
                        'user' => $this->session->userdata('id'),
                        'type' => 'link',
                        'comment' => $this->input->post('comment'),
                    );
                    
                    $remote_data = array(
                        'remote' => $this->input->post('remote'),
                        'title' => $this->input->post('title'),
                        'description' => $this->input->post('description'),
                        'image' => $this->input->post('image'),
                        'icon' => $this->input->post('icon'),
                        'domain' => $this->input->post('domain'),
                        'site_name' => $this->input->post('site_name')
                    );

                    $this->load->model('update_model');

                    // Create update with URL information
                    if($this->update_model->create($data, array('link' => $url), $remote_data)) {
                        // Url shared correctly
                        if(IS_AJAX){
                            echo json_encode(array('result' => 1));
                            return;
                        } else {
                            redirect('home');
                        }
                    } else {
                        // Something went wrong
                        if(IS_AJAX){
                            echo json_encode(array(
                                'result' => 0,
                                'html' => $this->load->view('template/error_msg', array('error_msg' => lang('share_error_link')), true)
                                ));
                            return;
                        } else {
                            $data = array(
                                'url' => $url,
                                'error' => true
                            );
                            $this->load->view('popup/share', $data);
                        }
                    }
                } else {
                    // Load sharing screen
                    $data = array(
                        'url' => $url
                    );
                    $this->load->view('popup/share', $data);
                }
            } else {
                redirect('home/share');
            }
        } else {
            // User not logged in - Show popup signin
            $data = array(
                'invalid' => false,
                'share' => $this->input->get('url')
            );
            $this->load->view('popup/signin', $data);
        }
    }
    
    function url() {
        if(!IS_AJAX)
            redirect('home');
        
        if($this->input->post('type') == 'link')
            $url = $this->input->post('url');
        else
            $url = $this->input->post('video');
        
        if($url) {
            $this->load->model('update_model');
            $data = $this->update_model->getUrlInformation($url);
            
            if(!$data) {
                echo json_encode(array(
                    'result' => 0,
                    'html' => $this->load->view('template/error_msg', array('error_msg' => lang('share_error_link')), true)
                    ));
                return;
            }
            
            if(isset($data['images']))
                $data['images'][] = base_url('images/icon/no-image.png');
            
            $view_data = array(
                'type' => $this->input->post('type'),
                'link' => (isset($data['url']))?$data['url']:'',
                'title' => (isset($data['title']))?$data['title']:'',
                'images'  => (isset($data['images']))?$data['images']:array(base_url('images/icon/no-image.png')),
                'description' => (isset($data['description']))?$data['description']:'',
                'icon' => (isset($data['icon']))?$data['icon']:'',
                'domain' => parse_url($data['url'], PHP_URL_HOST),
                'name' => (isset($data['site_name']))?$data['site_name']:'',
                'player' => (isset($data['player']))?$data['player']:''
            );
            
            if($view_data['type'] == 'video' && $view_data['player'] == '') {
                echo json_encode(array(
                    'result' => 0,
                    'html' => $this->load->view('template/error_msg', array('error_msg' => lang('share_error_video')), true)
                    ));
                return;
            }
            echo json_encode(array(
                'result' => 1,
                'html' => $this->load->view('update/share_link', $view_data, true),
                'url_json' => json_encode($data)
                ));
            return;
        } else{
            echo json_encode(array(
                'result' => 0,
                'html' => $this->load->view('template/error_msg', array('error_msg' => lang('share_error_link_empty')), true)
                ));
            return;
        }
    }
    
    function local($update_id)
    {
        if($this->session->userdata('logged_in')){
            // Get original update
            $this->load->model('update_model');
            $update = $this->update_model->getSingle($update_id);
            
            $error = false;
            
            if($this->input->post('comment') !== false) {
                // Validate form
                $this->load->library('form_validation');
                
                if($update['type'] == 'text')
                    $this->form_validation->set_rules('comment', lang('home_share_text_valid'), 'trim|required|htmlspecialchars');
                else
                    $this->form_validation->set_rules('comment', lang('home_share_text_valid'), 'trim|htmlspecialchars');
                
                if($this->form_validation->run() === false)  {
                    if(IS_AJAX){
                        // Return error
                        echo json_encode(array(
                            'result' => 0,
                            'html' => $this->load->view('template/error_msg', array('error_msg' => strip_tags(validation_errors())), true),
                            'fields' => json_encode(array(
                                'comment' => form_error('comment')
                            ))
                        ));
                        return;
                    } else {
                        $error = $this->load->view('template/error_msg', array('error_msg' => strip_tags(validation_errors())), true);
                    }
                } else {
                    // No errors
                    if($this->update_model->reshare($update, $this->input->post('comment'))) {
                        if(IS_AJAX) {
                            echo json_encode(array(
                                'result' => 1
                            ));
                            die();
                        } else {
                            redirect('home');
                        }
                    }
                }
            }
                        
            if($update['comment'])
                $update['reshare_comment'] = '@'.$update['username'].': '.strip_tags(remove_mention_link($update['comment']));
            else
                $update['reshare_comment'] = '';                
                
            if(IS_AJAX) {
                $this->load->view('dialog/local', array('item' => $update, 'is_popup' => true, 'error' => $error));
            } else {
                styh_load_view('dialog/local', array('item' => $update, 'is_single' => true, 'error' => $error));
            }
        } else {
            redirect('home');
        }
    }
    
    function comment()
    {
        if($this->session->userdata('logged_in')){
            if($this->input->post('update_id')) {
                $this->load->model('update_model');
                
                // Save comment information
                $data = array(
                    'update_id' => $this->input->post('update_id'),
                    'user_id' => $this->session->userdata('id'),
                    'update_comment_content' => $this->input->post('content')
                );
                
                // Check if something was commented
                if(!$this->input->post('content')) {
                    if(!IS_AJAX) {
                        // No AJAX
                        $update = $this->update_model->getSingle($data['update_id']);
                        redirect("u/{$update['username']}/{$data['update_id']}#comments");
                    } else {
                        // With AJAX
                        echo json_encode(array(
                            'result' => 0,
                            'html' => $this->load->view('template/error_msg', array('error_msg' => lang('share_error_comment')), true)
                            ));
                        return;
                    }
                }
                
                $this->load->model('update_model');
                
                // Create update comment
                $id = $this->update_model->createComment($data);
                if(!$id) {
                    if(!IS_AJAX) {
                        // No AJAX
                        $update = $this->update_model->getSingle($data['update_id']);
                        redirect("u/{$update['username']}/{$data['update_id']}#comments");
                    } else {
                        // With AJAX
                        echo json_encode(array(
                            'result' => 0,
                            'html' => $this->load->view('template/error_msg', array('error_msg' => lang('share_error_request')), true)
                            ));
                        return;
                    }
                }
                
                if(!IS_AJAX) {
                    // No AJAX
                    $update = $this->update_model->getSingle($data['update_id']);
                    redirect("u/{$update['username']}/{$data['update_id']}#comments");
                } else {
                    // With AJAX
                    $comment = $this->update_model->getSingleComment($id);
                    echo json_encode(array(
                        'result' => 1,
                        'html' => $this->load->view('update/comment_list', array('comments' => array(0 => $comment)), true),
                        'html_single' => $this->load->view('update/comment_single', array('key' => 1, 'comment' => $comment), true)
                        ));
                    return;
                }
            } else {
                redirect('home');
            }
        } else {
            redirect('home');
        }
    }    

    function remove_comment($update_comment_id)
    {
        if($this->session->userdata('logged_in')){
            $this->load->model('update_model');
            
            // Get comment information
            $comment = $this->update_model->getSingleComment($update_comment_id);
            
            // Check if it's the same user
            if($comment['user_id'] != $this->session->userdata('id')) {
                if(!IS_AJAX) {
                    $update = $this->update_model->getSingle($comment['update_id']);
                    redirect("u/{$update['username']}/{$comment['update_id']}#comments");
                } else {
                    echo json_encode(array(
                        'result' => 0
                        ));
                    return;
                }
            }
            
            // Remove comment
            $this->update_model->deleteComment($comment['update_id'], $update_comment_id);
            
            if(!IS_AJAX) {
                // No AJAX
                $update = $this->update_model->getSingle($comment['update_id']);
                redirect("u/{$update['username']}/{$comment['update_id']}#comments");
            } else {
                // With AJAX
                echo json_encode(array(
                    'result' => 1
                    ));
                return;
            }
        } else {
            redirect('home');
        }
    }    
}