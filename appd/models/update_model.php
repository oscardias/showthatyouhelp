<?php
/**
 * Update Model
 *
 * Class for Updates CRUD operations
 *
 * @package		STYH
 * @subpackage          Model
 * @category            Updates
 * @author		Oscar Dias
 */

class Update_model extends CI_Model {
    
    public $error_msg = '';
    
    /*
     * Create Update
     *
     * Create update according to data passed as parameter
     *
     * @access	public
     * @param   data    Array
     * @return  mixed
     */
    public function create($data, $add = array(), $remote_data = false)
    {
        // Begin transaction with the database
        $this->db->trans_begin();
        
        // Create links for metions
        $data['comment'] = prepare_text($data['comment']);
        $data['update_created'] = date('Y-m-d H:i:s');

        // Create update_details if not text
        if(!$this->db->insert('update', $data)) {
            $this->db->trans_rollback();
            return false;
        }
        
        $id = $this->db->insert_id();
        
        // Increase user updates count
        $this->db->where('id', $this->session->userdata('id'))->
                set('update_count', 'update_count + 1', false)->
                update('user');
        
        // Save possible mentions to DB
        $this->_createMentions ($id, $data['comment']);
            
        // Check for additional information
        if($data['type'] == 'link') {
            
            // Save URL information
            if($remote_data && $remote_data['remote'] == 1) {
                // Information already available in the array
                // Downloaded previously via AJAX
                if(!$this->createUrl($id, $add['link'], $remote_data)) {
                    log_message('error', "update_model->create() - createUrl() - no fetch {$add['link']}");
                    $this->db->trans_rollback();
                    return false;
                }
            } else {
                // Add URL information
                // Need to fetch information via URL
                if(!$this->createUrl($id, $add['link'])) {
                    log_message('error', "update_model->create() - createUrl() - fetch {$add['link']}");
                    $this->db->trans_rollback();
                    return false;
                }
            }
                    
        } else if($data['type'] == 'video') {
            
            // Add Video information
            if(!$this->createVideo($id, $add['video'])) {
                log_message('error', "update_model->create() - createVideo()");
                $this->db->trans_rollback();
                return false;
            }
            
        } else if($data['type'] == 'photo') {
            
            // Add photo information
            // Upload photo
            if(isset($_FILES['photo'])) {
                
                $this->load->library('upload');

                $config = array(
                    'allowed_types' => 'gif|png|jpg|jpeg',
                    'upload_path' => getcwd().'/upload/photo/'.$this->session->userdata('username'),
                    'max_size' => 1024 * 8,
                    'overwrite' => true
                );
                $this->upload->initialize($config);

                // Create path
                create_folder($config['upload_path'], false);

                // Run the upload
                if (!$this->upload->do_upload('photo')) {
                    // Problem in upload
                    $error = array('upload_error' => $this->upload->display_errors());
                    log_message('error', "update_model->create() - do_upload() - {$error['upload_error']}");
                    $this->error_msg = strip_tags($error['upload_error']);
                    $this->db->trans_rollback();
                    return false;
                }

                // Resize images
                $upload_data = $this->upload->data();
                
                if(!$this->createPhoto($id, $upload_data)){
                    log_message('error', "update_model->create() - createPhoto()");
                    $this->db->trans_rollback();
                    return false;
                }
                
            } else {
                log_message('error', "update_model->create() - createPhoto() - no photo submitted");
                $this->db->trans_rollback();
                return false;
            }
            
        }

        if ($this->db->trans_status() === FALSE) {
            log_message('error', "update_model->create() - Rollback transaction");
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    
    /*
     * Create Url
     *
     * Create the Url entry with url_details and site
     *
     * @access	public
     * @param   data    Array
     * @return  mixed
     */
    public function createUrl($update, $url, $remote_url_data = false)
    {
        return $this->_saveUrlInfo($update, $url, 'link', $remote_url_data);
    }
    
    /*
     * Create Video
     *
     * Create the video entry with url_details and site
     *
     * @access	public
     * @param   data    Array
     * @return  mixed
     */
    public function createVideo($update, $url)
    {
        return $this->_saveUrlInfo($update, $url, 'video');
    }
    
    /*
     * Create Photo
     *
     * Create the photo entry with url_details and site
     *
     * @access	public
     * @param   data    Array
     * @return  mixed
     */
    public function createPhoto($update, $upload_data)
    {
        // Create image in DB
        $insert = array(
            'image_folder' => $this->session->userdata('username'),
            'image_filename' => $upload_data['raw_name'],
            'image_fileext' => $upload_data['file_ext'],
            'image_created' => date('Y-m-d H:i:s')
        );
        
        if($this->db->insert('image', $insert)) {
            $id = $this->db->insert_id();
        } else {
            return false;
        }

        // Create relation with update
        $insert = array(
            'update' => $update,
            'image_id' => $id,
            'update_image_created' => date('Y-m-d H:i:s')
        );
        
        if(!$this->db->insert('update_image', $insert))
            return false;
        
        // Rename uploaded photo
        $newname = $upload_data['file_path'].$id.'-'.$upload_data['raw_name'].$upload_data['file_ext'];
        if(!@rename($upload_data['full_path'], $newname)) {
                log_message('error', "update_model->createPhoto() - rename - Uploaded image = {$upload_data['full_path']})");
                $newname = $upload_data['full_path'];
        }
        
        // Resize photo
        $this->load->library('image_lib'); 

        // Small image for the Updates List
        $this->_resizePhoto($upload_data,array('width' => 580,'height' => 330), $id, $newname, 'small');
        
        // Large image for the single update
        $this->_resizePhoto($upload_data,array('width' => 880,'height' => 495), $id, $newname, 'large');
        
        return true;
    }
    
    /*
     * Get Updates for Viewer
     *
     * Get the updates that the user sees
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function reshare($update, $new_comment)
    {
        // Begin transaction with the database
        $this->db->trans_begin();
        
        // Create links for metions
        $data = array(
            'user' => $this->session->userdata('id'),
            'type' => $update['type'],
            'comment' => prepare_text($new_comment),
            'update_reshare_update_id' => $update['update_id'],
            'update_reshare_user_id' => $update['user_id'],
            'update_created' => date('Y-m-d H:i:s')
        );

        // Rollback if error
        if(!$this->db->insert('update', $data)) {
            $this->db->trans_rollback();
            return false;
        }
        
        $id = $this->db->insert_id();
        
        // Increase user updates count
        $this->db->where('id', $this->session->userdata('id'))->
                set('update_count', 'update_count + 1', false)->
                update('user');
        
        // Save possible mentions to DB
        $this->_createMentions ($id, $data['comment']);
        
        // Create notification
        if(!$this->db->insert('user_notification',
                array(
                    'user_id' => $update['user_id'],
                    'user_id_created_by' => $this->session->userdata('id'),
                    'user_notification_type' => 'reshare',
                    'update_id' => $id,
                    'user_notification_read' => (($update['user_id'] == $this->session->userdata('id'))?1:0),
                    'user_notification_email' => (($update['user_id'] == $this->session->userdata('id'))?1:0),
                    'user_notification_created' => date('Y-m-d H:i:s')
                    )
                ))
            log_message('error', "update_model->reshare() - Insert into user_notification:".$this->db->_error_message());

        
        if($update['type'] == 'link' || $update['type'] == 'video'){
            // Copy URL/Video info
            $update_url = $this->db->where('id', $update['update_url_id'])->get('update_url')->row_array();
            $update_url['update'] = $id;
            $update_url['update_url_created'] = date('Y-m-d H:i:s');
            unset($update_url['id']);
            
            // Rollback if error
            if(!$this->db->insert('update_url', $update_url)) {
                $this->db->trans_rollback();
                return false;
            }
        }
        if($update['type'] == 'photo'){
            // Copy Photo info
            $update_image = array(
                'update' => $id,
                'image_id' => $update['image_id'],
                'update_image_created' => date('Y-m-d H:i:s')
            );
            
            // Rollback if error
            if(!$this->db->insert('update_image', $update_image)) {
                $this->db->trans_rollback();
                return false;
            }
        }
        
        if ($this->db->trans_status() === FALSE) {
            log_message('error', "update_model->create() - Rollback transaction");
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    
    /*
     * Create Comment
     *
     * Create comment according to data
     *
     * @access	public
     * @param   data    array
     * @return  mixed
     */
    public function createComment($data)
    {
        // Begin transaction with the database
        $this->db->trans_start();
        
        $data['update_comment_content'] = prepare_text($data['update_comment_content']);
        $data['update_comment_created'] = date('Y-m-d H:i:s');
        
        // Insert Comment
        if($this->db->insert('update_comment', $data)) {
            
            $id = $this->db->insert_id();
            
            // Save possible mentions to DB
            $this->_createMentions ($data['update_id'], $data['update_comment_content'], 'comment');
        
            // Update comment count
            if(!$this->db->where('id', $data['update_id'])->
                    set('update_comment_count', 'update_comment_count + 1', false)->
                    update('update')) {
                log_message('error', "update_model->createComment() - Update update:".$this->db->_error_message());
            }
            
            $update = $this->update_model->getSingle($data['update_id']);

            // Create notification
            if(!$this->db->insert('user_notification',
                    array(
                        'user_id' => $update['user_id'],
                        'user_id_created_by' => $this->session->userdata('id'),
                        'user_notification_type' => 'comment',
                        'update_id' => $data['update_id'],
                        'user_notification_read' => (($update['user_id'] == $this->session->userdata('id'))?1:0),
                        'user_notification_email' => (($update['user_id'] == $this->session->userdata('id'))?1:0),
                        'user_notification_created' => date('Y-m-d H:i:s')
                        )
                    )) {
                log_message('error', "update_model->createComment() - Insert into user_notification:".$this->db->_error_message());
            }
            
        } else {
            log_message('error', "update_model->createComment() - Insert into update_comment:".$this->db->_error_message());
        }
                        
        $this->db->trans_complete();
        
        if($this->db->trans_status() === false)
            return false;
        
        return $id;
    }
    
    /*
     * Get Updates for Viewer
     *
     * Get the updates that the user sees
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function getViewerUpdates($viewing_user, $page, $last_id = false)
    {
        $this->db->select(
                   'u.id as update_id,
                    user.username,
                    user.image_name,
                    user.image_ext,
                    view.user_show,
                    u.update_created,
                    u.comment,
                    u.type,
                    u.update_comment_count,
                    u.update_reshare_update_id,
                    ure.username as reshare_username,
                    s.domain,
                    s.name,
                    s.icon,
                    url.link,
                    ud.title,
                    ud.description,
                    urli.original,
                    urli.filename,
                    i.image_id,
                    i.image_folder,
                    i.image_filename,
                    i.image_fileext'
                )->from('update u')->
                join('view', 'u.user = view.user_show and view.user_view = ' . $viewing_user)->
                join('user', 'u.user = user.id')->
                join('update_image ui', 'ui.update = u.id', 'left')->
                join('image i', 'i.image_id = ui.image_id', 'left')->
                join('update_url uu', 'uu.update = u.id', 'left')->
                join('url', 'url.id = uu.url', 'left')->
                join('site s', 'url.site = s.id', 'left')->
                join('url_details ud', 'ud.id = uu.url_details', 'left')->
                join('url_image urli', 'urli.id = uu.url_image', 'left')->
                join('user ure', 'ure.id = u.update_reshare_user_id', 'left')->
                where('u.update_delete', 0)->
                order_by('u.id', 'desc')->limit(PER_PAGE_UPDATES, ($page-1) * PER_PAGE_UPDATES);
        
        // Filter loaded updates
        if($last_id) {
            $this->db->where('u.id <=', $last_id);
        }
        
        $get = $this->db->get();

        return $get->result_array();
    }
    
    /*
     * Count Updates for Viewer
     *
     * Get total number of updates that the user has
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function countViewerUpdates($viewing_user)
    {
        $this->db->select('count(u.id) as total')->from('update u')->
                join('view', 'u.user = view.user_show and view.user_view = ' . $viewing_user)->
                where('u.update_delete', 0);
        
        $get = $this->db->get()->row_array();

        return $get['total'];
    }
    
    /*
     * Count New Updates for Viewer
     *
     * Get total number of new updates that the user has
     *
     * @access	public
     * @param   viewing_user   User ID
     * @param   lst_id         Last Update ID
     * @return  mixed
     */
    public function countNewViewerUpdates($viewing_user, $last_id)
    {
        $this->db->select('count(u.id) as total')->from('update u')->
                join('view', 'u.user = view.user_show and view.user_view = ' . $viewing_user)->
                where('u.id >', $last_id)->
                where('u.update_delete', 0);
        
        $get = $this->db->get()->row_array();

        return $get['total'];
    }
    
    /*
     * Get New Updates for Viewer
     *
     * Get the new updates that the user sees
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function getNewViewerUpdates($viewing_user, $last_id)
    {
        $this->db->select(
                   'u.id as update_id,
                    user.username,
                    user.image_name,
                    user.image_ext,
                    view.user_show,
                    u.update_created,
                    u.comment,
                    u.type,
                    u.update_comment_count,
                    u.update_reshare_update_id,
                    ure.username as reshare_username,
                    s.domain,
                    s.name,
                    s.icon,
                    url.link,
                    ud.title,
                    ud.description,
                    urli.original,
                    urli.filename,
                    i.image_id,
                    i.image_folder,
                    i.image_filename,
                    i.image_fileext'
                )->from('update u')->
                join('view', 'u.user = view.user_show and view.user_view = ' . $viewing_user)->
                join('user', 'u.user = user.id')->
                join('update_image ui', 'ui.update = u.id', 'left')->
                join('image i', 'i.image_id = ui.image_id', 'left')->
                join('update_url uu', 'uu.update = u.id', 'left')->
                join('url', 'url.id = uu.url', 'left')->
                join('site s', 'url.site = s.id', 'left')->
                join('url_details ud', 'ud.id = uu.url_details', 'left')->
                join('url_image urli', 'urli.id = uu.url_image', 'left')->
                join('user ure', 'ure.id = u.update_reshare_user_id', 'left')->
                where('u.id >', $last_id)->
                where('u.update_delete', 0)->
                order_by('u.id', 'desc');
        
        $get = $this->db->get();

        return $get->result_array();
    }
    
    /*
     * Get Posted Updates
     *
     * Get the updates that the user posted
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function getPostedUpdates($post_user, $page, $uses_username = false, $last_id = false)
    {
        $this->db->select(
                   'u.id as update_id,
                    user.username,
                    user.image_name,
                    user.image_ext,
                    u.update_created,
                    u.comment,
                    u.type,
                    u.update_comment_count,
                    u.update_reshare_update_id,
                    ure.username as reshare_username,
                    s.domain,
                    s.name,
                    s.icon,
                    url.link,
                    ud.title,
                    ud.description,
                    urli.original,
                    urli.filename,
                    i.image_id,
                    i.image_folder,
                    i.image_filename,
                    i.image_fileext'
                )->from('update u')->
                //join('view', 'update.user = view.user_show and view.user_view = ' . $post_user)->
                join('user', 'u.user = user.id')->
                join('update_image ui', 'ui.update = u.id', 'left')->
                join('image i', 'i.image_id = ui.image_id', 'left')->
                join('update_url uu', 'uu.update = u.id', 'left')->
                join('url', 'url.id = uu.url', 'left')->
                join('site s', 'url.site = s.id', 'left')->
                join('url_details ud', 'ud.id = uu.url_details', 'left')->
                join('url_image urli', 'urli.id = uu.url_image', 'left')->
                join('user ure', 'ure.id = u.update_reshare_user_id', 'left');
        
        if($uses_username)
            $this->db->where('user.username', $post_user);
        else
            $this->db->where('u.user', $post_user);
        
        // Filter loaded updates
        if($last_id) {
            $this->db->where('u.id <=', $last_id);
        }
        
        $this->db->where('u.update_delete', 0)->
                order_by('u.id', 'desc')->limit(PER_PAGE_UPDATES, ($page-1) * PER_PAGE_UPDATES);
        
        $get = $this->db->get();

        return $get->result_array();
    }
    
    /*
     * Count Posted Updates
     *
     * Get total number of updates that the user posted
     *
     * @access	public
     * @param   post_user   User ID
     * @return  mixed
     */
    public function countPostedUpdates($post_user)
    {
        $this->db->select('count(id) as total')->
                from('update')->
                where('user', $post_user)->
                where('update_delete', 0);
        
        $get = $this->db->get()->row_array();

        return $get['total'];
    }
    
    /*
     * Count New Posted Updates
     *
     * Get total number of new updates that the user posted
     *
     * @access	public
     * @param   post_user   User ID
     * @return  mixed
     */
    public function countNewPostedUpdates($post_user, $last_id)
    {
        $this->db->select('count(id) as total')->
                from('update')->
                where('user', $post_user)->
                where('id >', $last_id)->
                where('update_delete', 0);
        
        $get = $this->db->get()->row_array();

        return $get['total'];
    }
        
    /*
     * Get New Posted Updates
     *
     * Get the new updates that the user posted
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function getNewPostedUpdates($post_user, $last_id)
    {
        $this->db->select(
                   'u.id as update_id,
                    user.username,
                    user.image_name,
                    user.image_ext,
                    u.update_created,
                    u.comment,
                    u.type,
                    u.update_comment_count,
                    u.update_reshare_update_id,
                    ure.username as reshare_username,
                    s.domain,
                    s.name,
                    s.icon,
                    url.link,
                    ud.title,
                    ud.description,
                    urli.original,
                    urli.filename,
                    i.image_id,
                    i.image_folder,
                    i.image_filename,
                    i.image_fileext'
                )->from('update u')->
                //join('view', 'update.user = view.user_show and view.user_view = ' . $post_user)->
                join('user', 'u.user = user.id')->
                join('update_image ui', 'ui.update = u.id', 'left')->
                join('image i', 'i.image_id = ui.image_id', 'left')->
                join('update_url uu', 'uu.update = u.id', 'left')->
                join('url', 'url.id = uu.url', 'left')->
                join('site s', 'url.site = s.id', 'left')->
                join('url_details ud', 'ud.id = uu.url_details', 'left')->
                join('url_image urli', 'urli.id = uu.url_image', 'left')->
                join('user ure', 'ure.id = u.update_reshare_user_id', 'left')->
                where('user.id', $post_user)->
                where('u.id >', $last_id)->
                where('u.update_delete', 0)->
                order_by('u.id', 'desc');
        
        $get = $this->db->get();

        return $get->result_array();
    }
    
    /*
     * Get Single
     *
     * Get the update information
     *
     * @access	public
     * @param   string  User name
     * @param   int     Update ID
     * @return  mixed
     */
    public function getSingle($update, $delete = 0)
    {
        $this->db->select(
                   'u.id as update_id,
                    user.id as user_id,
                    user.username,
                    user.image_name,
                    user.image_ext,
                    u.update_created,
                    u.comment,
                    u.type,
                    u.update_comment_count,
                    u.update_reshare_update_id,
                    uu.id as update_url_id,
                    ure.username as reshare_username,
                    s.domain,
                    s.name,
                    s.icon,
                    url.id as url,
                    url.link,
                    ud.title,
                    ud.description,
                    ud.keywords,
                    urli.original,
                    urli.filename,
                    i.image_id,
                    i.image_folder,
                    i.image_filename,
                    i.image_fileext'
                )->from('update u')->
                join('user', 'u.user = user.id')->
                join('update_image ui', 'ui.update = u.id', 'left')->
                join('image i', 'i.image_id = ui.image_id', 'left')->
                join('update_url uu', 'uu.update = u.id', 'left')->
                join('url', 'url.id = uu.url', 'left')->
                join('site s', 'url.site = s.id', 'left')->
                join('url_details ud', 'ud.id = uu.url_details', 'left')->
                join('url_image urli', 'urli.id = uu.url_image', 'left')->
                join('user ure', 'ure.id = u.update_reshare_user_id', 'left')->
                where('u.id', $update)->
                where('u.update_delete', $delete);
        
        $get = $this->db->get();

        return $get->row_array();
    }
    
    /*
     * Get Latest Updates
     *
     * Get the latest updates for the homepage
     *
     * @access	public
     * @param   viewing_user   User ID
     * @return  mixed
     */
    public function getLatestUpdates($limit = 10)
    {
        $this->db->select(
                   'u.id as update_id,
                    user.username,
                    user.image_name,
                    user.image_ext,
                    u.update_created,
                    u.comment,
                    u.type,
                    u.update_comment_count,
                    u.update_reshare_update_id,
                    url.link,
                    ud.title,
                    ud.description'
                )->from('update u')->
                join('user', 'u.user = user.id')->
                join('update_url uu', 'uu.update = u.id', 'left')->
                join('url', 'url.id = uu.url', 'left')->
                join('url_details ud', 'ud.id = uu.url_details', 'left')->
                where('u.update_delete', 0)->
                order_by('u.id', 'desc')->limit($limit);
        
        $get = $this->db->get();

        return $get->result_array();
    }    
    
    /*
     * Get Sitemap
     *
     * Get the update information for the sitemap
     *
     * @access	public
     * @param   string  User name
     * @param   int     Update ID
     * @return  mixed
     */
    public function getSitemap()
    {
        $this->db->select(
                   'u.id as update_id,
                    user.username,
                    u.update_created,
                    i.image_id,
                    i.image_folder,
                    i.image_filename,
                    i.image_fileext'
                )->from('update u')->
                join('user', 'u.user = user.id')->
                join('update_image ui', 'ui.update = u.id', 'left')->
                join('image i', 'i.image_id = ui.image_id', 'left')->
                where('u.update_delete', 0);
        
        $get = $this->db->get();

        return $get->result_array();
    }
    
    /*
     * Mark Deletion
     *
     * Mark update for deletion
     *
     * @access	public
     * @param   int     update id
     * @return  mixed
     */
    public function markDeleted($update_id){
        // Begin transaction with the database
        $this->db->trans_start();

        $this->db->where('id', $update_id)->
                where('user', $this->session->userdata('id'))->
                set('update_delete', 1)->
                set('update_delete_date', date('Y-m-d'))->
                update('update');
        
        // Decrease user updates count
        $this->db->where('id', $this->session->userdata('id'))->
                set('update_count', 'update_count - 1', false)->
                update('user');

        $this->db->trans_complete();
        
        if($this->db->trans_status() === false)
            return false;
        
        return true;
    }

    /*
     * UN Mark Deletion
     *
     * UN Mark update for deletion
     *
     * @access	public
     * @param   int     update id
     * @return  mixed
     */
    public function unMarkDeleted($update_id){
        // Begin transaction with the database
        $this->db->trans_start();

        $this->db->where('id', $update_id)->
                where('user', $this->session->userdata('id'))->
                set('update_delete', 0)->
                update('update');
        
        // Increase user updates count
        $this->db->where('id', $this->session->userdata('id'))->
                set('update_count', 'update_count + 1', false)->
                update('user');

        $this->db->trans_complete();
        
        if($this->db->trans_status() === false)
            return false;
        
        return true;
    }
    
    /*
     * Get Delete Updates
     *
     * Select all deleted updates with more than 14 days
     *
     * @access	public
     * @return  mixed
     */
    public function getDeletedUpdates(){
        $this->db->select('id')->
                from('update')->
                where('update_delete', 1)->
                where('update_delete_date <', 'DATE_SUB(NOW(), INTERVAL 14 DAY)', false);
        
        return $this->db->get()->result_array();
    }
    
    /*
     * Delete Update
     *
     * Delete the update and all related information
     * THIS METHOD SHOULD BE USED ONLY IN THE ADMIN CONTROLLER
     *
     * @access	public
     * @param   int     update id
     * @return  mixed
     */
    public function deleteUpdate($update_id){
        // Begin transaction with the database
        $this->db->trans_start();
        
        // Update URL count
        $update = $this->getSingle($update_id, 1);
        
        if($update['url']) {
            // Check if this is the only update using this url
            $num = $this->db->where('url', $update['url'])->get('update_url')->num_rows;
            
            if($num > 1) {
                // More than one share
                $this->db->where('id', $update['url'])->
                        set('share_count', 'share_count - 1', false)->
                        update('url');
            } else {
                // Only one share
                // Delete from DB
                $this->db->where('id', $update['url'])->
                        delete('url');

                $dir = getcwd().'/upload/url/'.$update['url'];
                if(is_dir($dir)) {
                    empty_folder($dir);
                    if(!remove_folder($dir)) {
                        log_message('error', "user_model->deleteUpdate() - Remove folder failed:".$dir);
                        return false;
                    }
                }
            }
        }
        
        // Delete files
        if($update['image_id']) {
            // Check if this is the only update using this image
            $num = $this->db->where('image_id', $update['image_id'])->get('update_image')->num_rows;
            
            if($num == 1) {
                // Delete from DB
                $this->db->where('image_id', $update['image_id'])->
                        delete('image');

                $dir = getcwd().'/upload/photo/'.$update['username'];
                remove_files(array(
                    $dir . '/' . $update['image_id'] . '-' . $update['image_filename'] . $update['image_fileext'],
                    $dir . '/' . $update['image_id'] . '-'  . $update['image_filename'] . '-large' . $update['image_fileext'],
                    $dir . '/' . $update['image_id'] . '-'  . $update['image_filename'] . '-small'  . $update['image_fileext']
                ));
            }
        }
        
        // Delete mentions
        $this->db->where('update_id', $update_id)->
                delete('user_notification');        
        
        // Delete update
        $this->db->where('id', $update_id)->
                delete('update');        
        
        $this->db->trans_complete();
        
        if($this->db->trans_status() === false)
            return false;
        
        return true;
    }
    
    /*
     * Get URL Information
     *
     * Get the information from an  URL
     *
     * @access	public
     * @param   string  URL
     * @return  mixed
     */
    public function getUrlInformation($url){
        return $this->_fetchUrl($url);
    }

    /*
     * Get Comments
     *
     * Get the update comments
     *
     * @access	public
     * @param   int     Update ID
     * @return  mixed
     */
    public function getComments($update)
    {
        $this->db->select(
                   'update_comment_id,
                    update_id,
                    user_id,
                    u.username,
                    u.image_name,
                    u.image_ext,
                    update_comment_created,
                    update_comment_content'
                )->from('update_comment uc')->
                join('user u', 'uc.user_id = u.id')->
                where('update_id', $update)->
                order_by('update_comment_id');
        
        return $this->db->get()->result_array();
    }

    /*
     * Get Comment
     *
     * Get single update comment
     *
     * @access	public
     * @param   string  User name
     * @param   int     Update ID
     * @return  mixed
     */
    public function getSingleComment($update_comment)
    {
        $this->db->select(
                   'update_comment_id,
                    update_id,
                    user_id,
                    u.username,
                    u.image_name,
                    u.image_ext,
                    update_comment_created,
                    update_comment_content'
                )->from('update_comment uc')->
                join('user u', 'uc.user_id = u.id')->
                where('update_comment_id', $update_comment);
        
        return $this->db->get()->row_array();
    }

    /*
     * Delete Comment
     *
     * Delete specific comment
     *
     * @access	public
     * @param   int     Update ID
     * @param   int     Update Comment ID
     * @return  mixed
     */
    public function deleteComment($update_id, $update_commment_id)
    {
        // Begin transaction with the database
        $this->db->trans_start();
        
        $this->db->where('update_comment_id', $update_commment_id)->
                delete('update_comment');
                
        $this->db->where('id', $update_id)->
                set('update_comment_count', 'update_comment_count - 1', false)->
                update('update');
        
        $this->db->trans_complete();
        
        if($this->db->trans_status() === false)
            return false;
        
        return true;
    }

    /*
     * PRIVATE METHODS
     */
    
    /*
     * Make Absolute Url
     *
     * Get relative or absolute link and return the absolute version
     *
     * @access	public
     * @param   data    Relative/Absolute link
     * @param   data    URL
     * @return  mixed
     */
    private function _makeAbsoluteUrl($address, $url)
    {
        if(strpos($address, 'http') === false) {
            $url = parse_url($url);
            
            if(strpos($address, '//') === 0) {
                return $url['scheme'] . ':' . $address;
            }
            
            $address = trim($address, '/');
            return $url['scheme'] . '://' . $url['host'] . '/' . $address;
        } else {
            return $address;
        }
    }
    
    /*
     * Save Url Info
     *
     * Get information and create database tables for remote utl
     *
     * @access	public
     * @param   data    Array
     * @return  mixed
     */
    private function _saveUrlInfo($update, $url, $type = 'link', $remote_url_data = false)
    {
        // Check if URL data was already downloaded
        if($remote_url_data) {
            // Data already available, no need to fetch again
            $url_data = $remote_url_data;
        } else {
            // Fetch URL
            $url_data = $this->_fetchUrl($url);
        }
        
        if(!$url_data)
            return false;
        
        // Check if site exists and has the same data
        $url_data['site'] = $this->_createSite(array(
            'url' => $url_data['domain'],
            'name' => (isset($url_data['site_name']))?trim($url_data['site_name']):'',
            'icon' => (isset($url_data['icon']))?trim($url_data['icon']):''
        ));
        
        if(!$url_data['site'])
            return false;
        
        // Check if url exists
        $update_url = array();
        
        $update_url['url'] = $this->_createUrl(array(
            'site' => $url_data['site'],
            'link' => ((isset($url_data['url']))?trim($url_data['url']):$url)
        ));
        
        if(!$update_url['url'])
            return false;
        
        // Check if url_details exists and has the same data
        $update_url['url_details'] = $this->_createUrlDetails(array(
            'url' => $update_url['url'],
            'title' => (isset($url_data['title']))?trim($url_data['title']):'',
            'description' => (isset($url_data['description']))?trim($url_data['description']):'',
            'keywords' => (isset($url_data['keywords']))?trim($url_data['keywords']):'',
            'og_type' => (isset($url_data['og_type']))?trim($url_data['og_type']):''
        ));
        
        if(!$update_url['url_details'])
            return false;
        
        // Check if url_image exists and has the same data
        if(isset($url_data['image']))
            $image = $url_data['image'];
        else
            $image = (isset($url_data['images'][0]))?$url_data['images'][0]:'';
        
        if($image && $image != base_url('images/icon/no-image.png')) {
            $update_url['url_image'] = $this->_createUrlImage(array(
                'url' => $update_url['url'], 
                'original' => (isset($url_data['player']) && $type == 'video')?trim($url_data['player']):trim($image)
            ), $url_data['domain'], $type);

            if(!$update_url['url_image'])
                log_message('error', "update_model->_saveUrlInfo() - _createUrlImage - image = $image");
        } else {
            $update_url['url_image'] = 0;
        }
        
        // Create relation update_url
        $update_url['update'] = $update;
        $update_url['update_url_created'] = date('Y-m-d H:i:s');
        
        if($this->db->insert('update_url', $update_url)) {
            return $this->db->insert_id();
        }
        return false;
    }
    
    /*
     * Get URL Details
     *
     * Get details from URL
     * Return an array with the following information:
     * title, images - array, description, keywords, site_name
     * og_type, url, icon, player - video
     *
     * @access	public
     * @param   string   URL
     * @return  mixed
     */
    private function _fetchUrl($url)
    {
        //fetching url data via curl
        $curl_result = file_get_contents_curl($url);

        if($curl_result && isset($curl_result['content']))
            $html = $curl_result['content'];
        else
            $html = file_get_contents($url);
            
        if(!$html)
            return false;
        
        // Try to get charset
        $header = $curl_result['header'];
        $charset = '';
        preg_match( '@([\w/+]+)(;\s+charset=(\S+))?@i', $header['content_type'], $matches ); 
        if ( isset( $matches[3] ) )
            $charset = $matches[3];
        
        if(!$charset) {
            preg_match( '@@i', $html, $matches ); 
            if ( isset( $matches[3] ) )
                $charset = $matches[3];
        }

        //parsing title and description begins here
        $return = array();
 
        if($charset) {
            $html = iconv( $charset, "utf-8", $html );
            $html = mb_convert_encoding($html, 'html-entities', 'utf-8');
        }
        
        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        // Get title
        $nodes = $doc->getElementsByTagName('title');
        if(!$nodes)
            return false;
        
        $return['title'] = $nodes->item(0)->nodeValue;
        $return['images'] = array();
        $main_image = '';

        //Get Meta Tags
        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++)
        {
            $meta = $metas->item($i);

            $meta_name = $meta->getAttribute('name');
            if(!$meta_name)
                $meta_name = $meta->getAttribute('property');

            if(!$meta_name)
                continue;

            $meta_name = strtolower($meta_name);

            switch ($meta_name) {
                case 'description':
                    $return['description'] = $meta->getAttribute('content');
                    break;
                case 'keywords':
                    $return['keywords'] = $meta->getAttribute('content');
                    break;
                case 'og:site_name':
                    $return['site_name'] = $meta->getAttribute('content');
                    break;
                case 'og:type':
                    $return['og_type'] = $meta->getAttribute('content');
                    break;
                case 'og:description':
                    if(!isset($return['description']))
                        $return['description'] = $meta->getAttribute('content');
                    else if(!$return['description'])
                        $return['description'] = $meta->getAttribute('content');
                    break;
                case 'og:image':
                    if($main_image != $meta->getAttribute('content')) {
                        $main_image = $meta->getAttribute('content');
                        $return['images'][] = $main_image;
                    }
                    break;
                case 'og:url':
                    $return['url'] = $meta->getAttribute('content');
                    break;
                case 'og:video:type':
                    if($meta->getAttribute('content') == 'application/x-shockwave-flash')
                        $has_og_video = true;
                    break;
                case 'og:video':
                    $og_video = $meta->getAttribute('content');
                    break;
                case 'twitter:image':
                    if($main_image != $meta->getAttribute('content')) {
                        $main_image = $meta->getAttribute('content');
                        $return['images'][] = $main_image;
                    }
                    break;
                case 'twitter:player':
                    if($meta->getAttribute('value'))
                        $return['player'] = $meta->getAttribute('value');
                    else
                        $return['player'] = $meta->getAttribute('content');
                    break;

                default:
                    break;
            }
        }

        if(isset($has_og_video) && isset($og_video) && !isset($return['player']))
            $return['player'] = $og_video;

        //Get Additional Info
        $links = $doc->getElementsByTagName('link');

        for ($i = 0; $i < $links->length; $i++)
        {
            $link = $links->item($i);
            $meta = $metas->item($i);

            $link_rel = $link->getAttribute('rel');

            if(!$link_rel)
                continue;

            $link_rel = strtolower($link_rel);

            switch ($link_rel) {
                case 'canonical':
                    if(!isset($return['url']))
                        $return['url'] = $link->getAttribute('href');
                    break;
                case 'image_src':
                    if($main_image != $meta->getAttribute('href')) {
                        $main_image = $meta->getAttribute('href');
                        $return['images'][] = $main_image;
                    }
                    break;
                case 'icon':
                    $return['icon'] = $link->getAttribute('href');
                    break;
                case 'shortcut icon':
                    $return['icon'] = $link->getAttribute('href');
                    break;

                default:
                    break;
            }
        }

        // Shared URL and base Domain
        if(!isset($return['url']))
            $return['url'] = $curl_result['header']['url'];
        else {
            $base_url = parse_url($url, PHP_URL_HOST);
            if(strpos($return['url'], 'http') === FALSE) {
                $return['url'] = "http://{$base_url}/{$return['url']}";
            }
        }

        $return['domain'] = parse_url(((isset($return['url']))?trim($return['url']):$url), PHP_URL_HOST);

        //Get Images
        $imgs = $doc->getElementsByTagName('img');
        $num_imgs = (($imgs->length < 10)?$imgs->length:10);

        for ($i = 0; $i < $num_imgs; $i++)
        {
            $img = $imgs->item($i);
            $return['images'][] = $this->_makeAbsoluteUrl($img->getAttribute('src'), $return['url']);
        }

        if(isset($return['icon']) && $return['icon'])
            $return['icon'] = $this->_makeAbsoluteUrl($return['icon'], $return['url']);
                
        return $return;
    }

    /*
     * Create Site
     *
     * Create site or update and return ID if it already exits
     *
     * @access	public
     * @param   mixed   site data
     * @return  mixed
     */
    private function _createSite($data)
    {
        // Extract domain from url
        $insert = array();
        $insert['domain'] = $data['url'];
        $insert['name'] = ($data['name'])?$data['name']:$insert['domain'];
        
        // Check if site exits
        $get = $this->db->where('domain', $insert['domain'])->get('site');
        
        if($get->num_rows == 1)
        {
            $row = $get->row_array();
            
            if($row['name'] != $insert['name']) {
                // Update site info
                $this->db->where('id', $row['id'])->update('site', $insert);
            }
            
            return $row['id'];
        }
        
        // Get remote image
        if($data['icon']) {
            $link_array = parse_url($data['icon']);
            $filename =  getcwd().'/upload/site/'.$insert['domain'].'/'.basename($link_array['path']);

            // Create path
            $dir_error = false;
            if(!is_dir( getcwd().'/upload/site/'.$insert['domain'])) {
                if(!mkdir( getcwd().'/upload/site/'.$insert['domain'], 0755, true)){
                    log_message('error', "update_model->_createUrlImage() - mkdir(". getcwd()."/upload/site/{$insert['domain']})");
                    $dir_error = true;
                    $insert['icon'] = '';
                }
            }

            if(!$dir_error) {
                if(file_get_contents_file($data['icon'], $filename))
                    $insert['icon'] = basename($filename);
                else
                    $insert['icon'] = '';
            }
        } else {
            $insert['icon'] = '';
        }
        
        $insert['site_created'] = date('Y-m-d H:i:s');
        
        // Create site
        if($this->db->insert('site', $insert)) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /*
     * Create url
     *
     * Create url or update and return ID if it already exits
     *
     * @access	public
     * @param   mixed   url data
     * @return  mixed
     */
    private function _createUrl($data)
    {
        // Check if url exits
        $get = $this->db->where('link', $data['link'])->get('url');
        
        if($get->num_rows > 0)
        {
            $row = $get->row_array();
            $this->db->where('id', $row['id'])->update('url', array('share_count' => $row['share_count'] + 1));
            return $row['id'];
        }
        
        // Create url
        $data['share_count'] = 1;
        $data['url_created'] = date('Y-m-d H:i:s');
        
        if($this->db->insert('url', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /*
     * Create url details
     *
     * Create url_details or update and return ID if it already exits
     *
     * @access	public
     * @param   mixed   url data
     * @return  mixed
     */
    private function _createUrlDetails($data)
    {
        // Check if url details exit using url id
        $this->db->select('*')->
                from('url_details')->
                where('url', $data['url']);
        $get = $this->db->get();
        
        if($get->num_rows > 0)
        {
            foreach ($get->result_array() as $row) {
                if($row['title'] == $data['title'] &&
                        $row['description'] == $data['description'] &&
                        $row['keywords'] == $data['keywords'] &&
                        $row['og_type'] == $data['og_type']) {
                    // Same info -> return ID
                    return $row['id'];
                }
            }
        }
        
        $data['url_details_created'] = date('Y-m-d H:i:s');
        
        // Create url_details
        if($this->db->insert('url_details', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /*
     * Create url image
     *
     * Create url_image if needed and return ID if it already exits
     *
     * @access	public
     * @param   mixed   url data
     * @return  mixed
     */
    private function _createUrlImage($data, $base_url, $type = 'link')
    {
        if(strpos($data['original'], 'http') === FALSE) {
            $data['original'] = "http://{$base_url}/{$data['original']}";
        }
        
        // Check if url image exit using url id
        $this->db->select('*')->
                from('url_image')->
                where('url', $data['url'])->
                where('original', $data['original']);
        $get = $this->db->get();
        
        if($get->num_rows > 0)
        {
            $row = $get->row_array();
            return $row['id'];
        }
        
        // Get remote image
        if($data['original'] && $type == 'link'){
            $link_array = parse_url($data['original']);
            $filename =  getcwd().'/upload/url/'.$data['url'].'/64'.basename($link_array['path']);

            // Create path
            $dir_error = false;
            if(!is_dir( getcwd().'/upload/url/'.$data['url'])) {
                if(!mkdir( getcwd().'/upload/url/'.$data['url'], 0755, true)){
                    log_message('error', "update_model->_createUrlImage() - mkdir(". getcwd()."/upload/url/{$data['url']})");
                    $dir_error = true;
                    $data['filename'] = '';
                }
            }

            if(!$dir_error) {
                if(file_get_contents_file($data['original'], $filename)){
                    $data['filename'] = 'upload/url/'.$data['url'].'/64'.basename($link_array['path']);

                    // Resize image
                    $this->load->library('image_lib'); 

                    list($orig_width, $orig_height) = getimagesize($filename);
                    $target_w = 128;
                    $target_h = 64;

                    $config = array();
                    $config['source_image'] = $filename;
                    // Detect target size
                    if($orig_width >= $target_w) {
                        // Resize according to height
                        $config['width']  = $target_w;
                        $config['height'] = $orig_height * $target_w / $orig_width;
                    } else if($orig_height >= $target_h) {
                        // Resize according to width
                        $config['height'] = $target_h;
                        $config['width']  = $orig_width * $target_h / $orig_height;
                    } else {
                        // Don't resize
                        $config['height'] = $orig_height;
                        $config['width']  = $orig_width;
                    }

                    $this->image_lib->clear();
                    $this->image_lib->initialize($config); 
                    if(!$this->image_lib->resize()){
                        $data['filename'] = '';
                        log_message('error', "update_model->_createUrlImage() - resize image - Url = {$data['url']}, Link = {$data['original']})");
                    }
                } else {
                    $data['filename'] = '';
                }
            }
        }
        
        $data['url_image_created'] = date('Y-m-d H:i:s');
        
        // Create url_details
        if($this->db->insert('url_image', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /*
     * Resize photo
     *
     * Resize shared photo to destination size
     *
     * @access	public
     * @param   mixed   url data
     * @return  mixed
     */
    private function _resizePhoto($upload_data, $target, $id, $original_name, $size)
    {
        if($upload_data['image_width'] > $target['width']){
            $config = array();
            $config['source_image'] = $original_name;
            $config['new_image'] = $upload_data['file_path'].$id.'-'.$upload_data['raw_name'].'-'.$size.$upload_data['file_ext'];
            // Detect target size
            if($upload_data['image_width'] >= $target['width']) {
                // Resize according to width
                $config['width']  = $target['width'];
                $config['height'] = $upload_data['image_height'] * $target['width'] / $upload_data['image_width'];
            } else {
                // Resize according to height
                $config['height'] = $target['height'];
                $config['width']  = $upload_data['image_width'] * $target['height'] / $upload_data['image_height'];
            }

            $this->image_lib->clear();
            $this->image_lib->initialize($config); 
            if(!$this->image_lib->resize()){
                log_message('error', "update_model->_resizePhoto() - resize image - Original image = {$original_name})");
            }
        } else {
            if (!@copy($original_name, $upload_data['file_path'].$id.'-'.$upload_data['raw_name'].'-'.$size.$upload_data['file_ext'])){
                log_message('error', "update_model->_resizePhoto() - copy image - Original image = {$original_name})");
            }
        }
    }

    /*
     * Create Mentions
     *
     * Create entry in update_mention for all users mentioned in comment
     *
     * @access	public
     * @param   mixed   url data
     * @return  mixed
     */
    private function _createMentions($update_id, $comment, $type = 'update')
    {
        if(!$comment)
            return true;
        
        // Find mention
        preg_match_all('/@(\w+)/',$comment, $matches);
        
        if(!$matches)
            return true;
        
        // Avoid user repetition
        $mentioned_users = array();

        foreach ($matches[1] as $username) {
            // Check if user exits using username
            $get = $this->db->select('id')->where('username', $username)->get('user');

            if($get->num_rows > 0)
            {
                $row = $get->row_array();
                
                if(isset($mentioned_users[$row['id']]))
                    $mentioned_users[$row['id']] += 1;
                else
                    $mentioned_users[$row['id']] = 1;
            }
        }

        // Save each mention
        foreach ($mentioned_users as $id => $count) {
            
            $get = $this->db->select('update_id')->where('update_id', $update_id)->where('user_id', $id)->get('update_mention');

            // Create update_mention
            if($get->num_rows > 0)
            {
                if(!$this->db->where('update_id', $update_id)->where('user_id', $id)->
                        set('user_mention_count', 'user_mention_count + '.$count, false)->
                        update('update_mention')) {
                    log_message('error', "update_model->_createMentions() - Update update_mention:".$this->db->_error_message());
                }
            } else {
                if(!$this->db->insert('update_mention', array('update_id' => $update_id, 'user_id' => $id, 'user_mention_count' => $count))) {
                    log_message('error', "update_model->_createMentions() - Insert into update_mention:".$this->db->_error_message());
                }
            }

            // Create notification
            if(!$this->db->insert('user_notification',
                    array(
                        'user_id' => $id,
                        'user_id_created_by' => $this->session->userdata('id'),
                        'user_notification_type' => (($type == 'update')?'mention':'comment_mention'),
                        'update_id' => $update_id,
                        'user_notification_read' => (($id == $this->session->userdata('id'))?1:0),
                        'user_notification_email' => (($id == $this->session->userdata('id'))?1:0),
                        'user_notification_created' => date('Y-m-d H:i:s')
                        )
                    ))
                log_message('error', "update_model->_createMentions() - Insert into user_notification:".$this->db->_error_message());
            
        }
        
        return true;
    }

    /*
     * Get page Title using Regex
     */
    private function _get_title($html) {
        return preg_match('!<title>(.*?)</title>!i', $html, $matches) ? $matches[1] : '';
    }
    
    /*
     * Get page Description using Regex
     */
    private function _get_meta($html, $meta) {
	// Get the 'content' attribute value in a <meta name="description" ... />
	$matches = array();
 
	// Search for <meta name="description" content="Buy my stuff" />
	preg_match('/<meta.*?name=("|\')'.$meta.'("|\').*?content=("|\')(.*?)("|\')/i', $html, $matches);
	if (count($matches) > 4) {
		return trim($matches[4]);
	}
 
	// Order of attributes could be swapped around: <meta content="Buy my stuff" name="description" />
	preg_match('/<meta.*?content=("|\')(.*?)("|\').*?name=("|\')'.$meta.'("|\')/i', $html, $matches);
	if (count($matches) > 2) {
		return trim($matches[2]);
	}
 
	// No match
	return null;
    }
    /*
     * Get page Description using Regex
     */
    private function _get_link($html, $link) {
	// Get the 'content' attribute value in a <meta name="description" ... />
	$matches = array();
 
	// Search for <meta name="description" content="Buy my stuff" />
	preg_match('/<link.*?rel=("|\')'.$link.'("|\').*?href=("|\')(.*?)("|\')/i', $html, $matches);
	if (count($matches) > 4) {
		return trim($matches[4]);
	}
 
	// Order of attributes could be swapped around: <meta content="Buy my stuff" name="description" />
	preg_match('/<meta.*?href=("|\')(.*?)("|\').*?rel=("|\')'.$link.'("|\')/i', $html, $matches);
	if (count($matches) > 2) {
		return trim($matches[2]);
	}
 
	// No match
	return null;
    }
}
?>
