<?php
/*
    PHP Class  : admin.php
    Created on : 14/09/2012, 11:48
    Author     : Oscar
    Description:
        Controller with admin tasks.
*/
class Admin extends CI_Controller {
    
    function Admin(){
        parent::__construct();
        
        if(!$this->session->userdata('logged_in'))
            redirect('home');
        else{
            $this->load->model('user_model');
            if(!$this->user_model->isAdmin($this->session->userdata('id')))
                redirect('home');
        }
        
        // Language
        $this->lang->load('admin', get_language_name());
    }
    
    function index()
    {
        $header = array(
            'seo_title' => 'Admin'
        );
        styh_load_view('admin/admin', array(), $header);
    }
    
    /*
     * Clean Database
     */
    function clean_database()
    {
        $header = array(
            'extra_css' => array(
                base_url('css/admin')
            ),
            'seo_title' => 'Clean Database - Admin'
        );
        $data = array(
            'execution_list' => array(
                base_url('admin/clean_database_updates'),
                base_url('admin/clean_database_users')
            )
        );
        styh_load_view('admin/result', $data, $header);
    }
    
    function clean_database_updates()
    {
        $this->load->model('update_model');
        $updates = $this->update_model->getDeletedUpdates();
        $messages = array();

        foreach ($updates as $value) {
            if($this->update_model->deleteUpdate($value['id']))
                $messages[] = sprintf($this->lang->line('admin_update_removed'), $value['id']);
            else
                $messages[] = sprintf($this->lang->line('admin_update_remove_fail'), $value['id']);
        }
        
        $this->load->view('admin/messages', array('title' => $this->lang->line('admin_update_header'), 'messages' => $messages));
    }
    
    function clean_database_users()
    {
        $this->load->model('user_model');
        $users = $this->user_model->getDeletedUsers();
        $messages = array();

        foreach ($users as $value) {
            if($this->user_model->removeAccount($value['id'], $value['username']))
                $messages[] = sprintf($this->lang->line('admin_user_removed'), $value['id']);
            else
                $messages[] = sprintf($this->lang->line('admin_user_remove_fail'), $value['id']);
        }
                
        $this->load->view('admin/messages', array('title' => $this->lang->line('admin_user_header'), 'messages' => $messages));
    }

    /*
     * Generate Sitemap
     */
    function generate_sitemap()
    {
        $header = array(
            'extra_css' => array(
                base_url('css/admin')
            ),
            'seo_title' => $this->lang->line('admin_sitemap_title')
        );
        $data = array(
            'execution_list' => array(
                base_url('admin/generate_sitemap_execution')
            )
        );
        styh_load_view('admin/result', $data, $header);
    }
    
    function generate_sitemap_execution()
    {
        // Profiles
        $this->load->model('user_model');
        $data['users'] = $this->user_model->get_sitemap(false);
        
        // Updates
        $this->load->model('update_model');
        $data['updates'] = $this->update_model->getSitemap(false);
        
        $sitemap = $this->load->view('admin/sitemap', $data, true);        
        $filename = getcwd().'/upload/sitemap.xml';
        
        $messages = array();
        
        $fh = fopen($filename, 'w');
        
        if($fh === false)
            $messages[] = $this->lang->line('admin_sitemap_file_error');
        else {
            fwrite($fh, $sitemap);
            fclose($fh);
        }
        
        if(!$messages)
            $messages[] = $this->lang->line('admin_sitemap_ok');
        
        $this->load->view('admin/messages', array('title' => $this->lang->line('admin_sitemap_header'), 'messages' => $messages));
    }
    
    function file_browser()
    {
        $segment_array = $this->uri->segment_array();
        
        // first and second segments are our controller and the 'virtual root'
        $controller = array_shift( $segment_array );
        $method = array_shift( $segment_array );
        
        // build absolute path
        $path_in_url = '';
        foreach ( $segment_array as $segment ) $path_in_url.= $segment.'/';
        $absolute_path = getcwd().'/'.$path_in_url;
        $absolute_path = rtrim( $absolute_path ,'/' );
        
        // is it a directory or a file ?
        if ( is_dir( $absolute_path ))
        {
            // we'll need this to build links
            $this->load->helper('url');
            
            $dirs = array();
            $files = array();
            // let's traverse the directory
            if ( $handle = @opendir( $absolute_path )) 
            {
                while ( false !== ($file = readdir( $handle )))
                {
                    if (( $file != "." AND $file != ".." ))
                    {
                        if ( is_dir( $absolute_path.'/'.$file ))
                        {
                            $dirs[]['name'] = $file;
                        }
                        else
                        {
                            $files[]['name'] = $file;
                        }
                    }
                }
                closedir( $handle );
                sort( $dirs );
                sort( $files );
                
                
            }
            // parent directory
            // here to ensure it's available and the first in the array
            if ( $path_in_url != '' )
                array_unshift ( $dirs, array( 'name' => '..' ));
            
            // send the view
            $data = array(
                'controller' => $controller,
                'method' => $method,
                'virtual_root' => getcwd(),
                'path_in_url' => $path_in_url,
                'dirs' => $dirs,
                'files' => $files,
                );
            styh_load_view( 'admin/file_browser', $data );
        }
        else
        {
            // it's not a directory, but is it a file ?
            if ( is_file($absolute_path) )
            {
                // let's serve the file
                header ('Cache-Control: no-store, no-cache, must-revalidate');
                header ('Cache-Control: pre-check=0, post-check=0, max-age=0');
                header ('Pragma: no-cache');
                
                $text_types = array(
                    'php', 'css', 'js', 'html', 'txt', 'htaccess', 'xml'
                    );
                $ext = explode('.', $absolute_path);
                
                if( in_array( $ext[count($ext) - 1 ], $text_types) ) {
                    header('Content-Type: text/plain');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Length: ' . filesize( $absolute_path ));
                    header('Content-Disposition: attachment; filename=' . basename( $absolute_path ));
                }
                
                @readfile( $absolute_path ); 
            }
            else
            {
                show_404();
            }
        }
    }
    
    function data_browser($table = false, $page = 1)
    {
        $this->load->model('admin_model');
        
        if($table === false) {
            $tables = $this->admin_model->get_tables();
            styh_load_view('admin/data_tables', array('tables' => $tables));
            return;
        }
        
        $data['table'] = $table;
        $data['schema'] = $this->admin_model->get_schema($table);
        $data['data'] = $this->admin_model->get_data($table, $page);
        
        $total = $this->admin_model->get_total($table);
        
        // Pagination
        $this->load->library('pagination');

        $config['base_url'] = base_url('admin/data_browser/'.$table.'/');
        $config['uri_segment'] = 4;
        $config['total_rows'] = $total['total'];
        $config['per_page'] = PER_PAGE_ADMIN;

        $this->pagination->initialize($config); 

        $data['pagination'] = $this->pagination->create_links();
        $data['total_pages'] = $config['total_rows'] / $config['per_page'];
        $data['current_page'] = $page;
        
        styh_load_view('admin/data_tables', $data);
    }
}