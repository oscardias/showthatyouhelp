<?php
/**
 * User Model
 *
 * Class for Users CRUD operations
 *
 * @package		STYH
 * @subpackage          Model
 * @category            Posts
 * @author		Oscar Dias
 */

class User_model extends CI_Model {

    /**
     * Validate
     *
     * Validate user credentials.
     *
     * @access	public
     * @param   username    Unique Username
     * @param   password    User Password
     * @return  bool
     */
    public function validate($user, $password)
    {
        // Check if user entered email or username
        $this->db->select('id, password')->
                where('user_delete', 0);

        if(filter_var($user, FILTER_VALIDATE_EMAIL)) {
            // Valid email
            $this->db->where('email', $user);
        } else {
            // Username
            $this->db->where('username', $user);
        }

        // Check if user was found and get information
        $query = $this->db->get('user');

        if($query->num_rows == 1) {
            $row = $query->row_array();
            $id = $row['id'];
            $hash = $row['password'];
        } else {
            return false;
        }

        $this->config->load('config_passhash');
        $this->load->library('PassHash', array(
            'iteration_count_log2' => $this->config->item('phpass_iteration_count_log2'),
            'portable_hashes' => $this->config->item('phpass_portable_hashes')
        ));

        if($this->passhash->CheckPassword($password, $hash))
            return $id;
        else
            return false;

    }

    /**
     * Validate Previous
     *
     * Validate previous user credentials - recovery.
     *
     * @access	public
     * @param   username    Unique Username
     * @param   password    User Password
     * @return  bool
     */
    public function validate_previous($user, $password, $token)
    {
        // Check if user entered email or username
        $this->db->select('id, user_recover_id, user_recover_password as password')->
                join('user_recover', 'id = user_id')->
                where('user_delete', 0)->
                where('user_recover_token', $token)->
                where('user_recover_date >=', 'DATE_SUB(NOW(), INTERVAL 7 DAY)', false);

        if(filter_var($user, FILTER_VALIDATE_EMAIL)) {
            // Valid email
            $this->db->where('email', $user);
        } else {
            // Username
            $this->db->where('username', $user);
        }

        // Check if user was found and get information
        $query = $this->db->get('user');

        if($query->num_rows == 1) {
            $row = $query->row_array();
            $id = $row['id'];
            $hash = $row['password'];
        } else {
            return false;
        }

        $this->config->load('config_passhash');
        $this->load->library('PassHash', array(
            'iteration_count_log2' => $this->config->item('phpass_iteration_count_log2'),
            'portable_hashes' => $this->config->item('phpass_portable_hashes')
        ));

        if($this->passhash->CheckPassword($password, $hash)){
            $this->_restoreAccount($id, $hash);

            return $id;
        } else
            return false;

    }

    /**
     * Create User
     *
     * Create user in the database.
     *
     * @access	public
     * @return  mixed
     */
    public function create($data)
    {
        // Begin transaction with the database
        $this->db->trans_begin();

        // Use phpass PasswordHash library to hash the password
        if($data['password']) {
            $this->config->load('config_passhash');
            $this->load->library('PassHash', array(
                'iteration_count_log2' => $this->config->item('phpass_iteration_count_log2'),
                'portable_hashes' => $this->config->item('phpass_portable_hashes')
            ));
            $data['password'] = $this->passhash->HashPassword($data['password']);

            if (strlen($data['password']) < 20) {
                log_message('error', "user_model->create() - Hash length < 20");
                return false;
            }
        }

        $data['user_created'] = date('Y-m-d H:i:s');

        if(!$this->db->insert('user', $data)) {
            log_message('error', "user_model->create() - insert(user) - Email = {$data['email']}");
            $this->db->trans_rollback();
            return false;
        }

        $id = $this->db->insert_id();

        if($id) {
            // User view his own posts
            $viewing = array(
                'user_view' => $id,
                'user_show' => $id,
                'view_created' => date('Y-m-d H:i:s')
            );

            if(!$this->db->insert('view', $viewing)){
                log_message('error', "user_model->create() - insert(view) - User ID = {$id})");
                $this->db->trans_rollback();
                return false;
            }
        }

        // Save Twitter sing in
        if($this->session->userdata('oauth_id')) {
            $this->create_oauth(array(
                'user_id'        => $id,
                'username'       => $this->session->userdata('oauth_username'),
                'oauth_provider' => 'twitter',
                'oauth_uid'      => $this->session->userdata('oauth_uid'),
                'oauth_token'    => $this->session->userdata('oauth_token'),
                'oauth_secret'   => $this->session->userdata('oauth_token_secret')
            ));
        }

        // Send email to Softerize
        $email_data = array(
            'reason' => 'New User',
            'name' => 'Support showthatyouhelp',
            'email' => 'support@showthatyouhelp.com',
            'message' => "New user created: User ID = $id, Email = {$data['email']}, Username = {$data['username']}"
        );

        // Send contact email
        $this->load->library('PHPMail');
        if (!$this->phpmail->customSend(array(
            'body' => $this->load->view('emails/contact_softerize', $email_data, true),
            'from' => 'support@showthatyouhelp.com',
            'from_name' => 'Support showthatyouhelp',
            'subject' => '[showthatyouhelp] '.$email_data['reason'],
            'to' => 'support@showthatyouhelp.com',
            'to_name' => 'Support showthatyouhelp'
        )))
            log_message('error', "user_model->create() - customSend() - ".$this->phpmail->ErrorInfo);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $id;
        }
    }

    /**
     * Update User
     *
     * Update user info in the database.
     *
     * @access	public
     * @return  mixed
     */
    public function update($data)
    {
        if(isset($data['password']) && $data['password']) {
            // Use phpass PasswordHash library to hash the password
            $this->config->load('config_passhash');
            $this->load->library('PassHash', array(
                'iteration_count_log2' => $this->config->item('phpass_iteration_count_log2'),
                'portable_hashes' => $this->config->item('phpass_portable_hashes')
            ));
            $data['password'] = $this->passhash->HashPassword($data['password']);

            if (strlen($data['password']) < 20) {
                log_message('error', "user_model->create() - Hash length < 20");
                return false;
            }
        }

        $this->db->where('id', $this->session->userdata('id'));
        return $this->db->update('user', $data);
    }

    /**
     * Send Recover Email
     *
     * Generate and save recover information and send email notification
     *
     * @access	public
     * $param   string  username
     * @return  mixed
     */
    public function sendRecoverEmail($username)
    {
        // Translation
        $this->lang->load('general', get_language_name());
        $this->lang->load('email', get_language_name());

        $user = $this->get(array('username_email' => $username));

        if(!$user)
            return false;

        $recover = $this->_getRecoverData($user['password']);

        if(!$recover)
            return false;

        // Send new password
        $email_data = array(
            'new_password' => $recover['password'],
            'token' => $recover['recover_token']
        );

        // Send email
        $this->load->library('PHPMail');
        if (!$this->phpmail->customSend(array(
            'body' => $this->load->view('emails/password_recover', $email_data, true),
            'from' => 'support@showthatyouhelp.com',
            'from_name' => lang('email_support_from_name'),
            'subject' => lang('email_recover_subject'),
            'to' => $user['email'],
            'to_name' => ($user['full_name'])?$user['full_name']:$user['username']
        ))) {
            log_message('error', "user_model->sendRecoverEmail() - customSend() - ".$this->phpmail->ErrorInfo);
            return false;
        }

        return $this->_setRecoverData($user['id'], $recover);
    }

    /**
     * Get
     *
     * Get the user information
     *
     * @access	public
     * @param   array      User data
     * @return  mixed
     */
    public function get($data)
    {
        if(isset($data['id'])) $this->db->where('id', $data['id']);
        if(isset($data['username'])) $this->db->where('username', $data['username']);
        if(isset($data['email'])) $this->db->where('email', $data['email']);

        if(isset($data['username_email'])) {
            if(filter_var($data['username_email'], FILTER_VALIDATE_EMAIL)) {
                // Valid email
                $this->db->where('email', $data['username_email']);
            } else {
                // Username
                $this->db->where('username', $data['username_email']);
            }
        }

        $this->db->where('user_delete', 0);

        $get = $this->db->get('user');

        if($data === false) return $get->result_array();
        return $get->row_array();
    }

    /**
     * Get Sitemap
     *
     * Get the user information for sitemap generation
     *
     * @access	public
     * @return  mixed
     */
    public function get_sitemap()
    {
        $this->db->select('user.*, max(update_created) as last_mod')->
                from('user')->
                join('update', 'user.id = update.user', 'left')->
                where('user_delete', 0)->
                group_by('user.id');

        $get = $this->db->get();

        return $get->result_array();
    }

    /**
     * Is Viewing
     *
     * Check if user is viewing another user updates
     *
     * @access	public
     * @param   int      User viewing
     * @param   int      User showing
     * @return  mixed
     */
    public function isViewing($view, $show)
    {
        $this->db->where('user_view', $view);
        $this->db->where('user_show', $show);

        $get = $this->db->get('view');

        if($get->num_rows == 1)
            return true;

        return false;
    }

    /**
     * Get Viewing or Showing
     *
     * Get the connected users
     *
     * @access	public
     * @param   id      User ID
     * @return  mixed
     */
    public function getViewOrShow($id, $mode = 'view', $page = 1)
    {
        $this->db->select('*')->from('user')->
                where('user_delete', 0)->
                order_by('view_created', 'desc')->
                order_by('user_created', 'desc');
        if($page)
            $this->db->limit(PER_PAGE_DEFAULT, ($page-1) * PER_PAGE_DEFAULT);
        else
            $this->db->limit(15);

        if($mode == 'view')
            $this->db->join('view', "user.id = view.user_show and view.user_view = {$id} and user.id != $id");
        else
            $this->db->join('view', "user.id = view.user_view and view.user_show = {$id} and user.id != $id");

        $get = $this->db->get();

        return $get->result_array();
    }

    /**
     * Get Recommended Users
     *
     * Get recommended users that the user is not following
     *
     * @access	public
     * @param   id      User ID
     * @return  mixed
     */
    public function getRecommendedUsers($id, $viewing_total)
    {
        if($viewing_total > 10)
            return array();

        if($viewing_total > 5)
            $limit = 10 - $viewing_total;
        else
            $limit = 5;

        $this->db->select('user.username, user.image_name, user.image_ext, user.full_name, user.bio, user.website')->from('user')->
                where('user_delete', 0)->
                where("user.id NOT IN (SELECT `user_show` FROM (`view`) WHERE `user_view` = $id)", null, false) ->
                order_by('update_count', 'desc')->
                limit($limit);

        $get = $this->db->get();

        return $get->result_array();
    }

    /**
     * Count View Or Show
     *
     * Count total connected users
     *
     * @access	public
     * @param   id      User ID
     * @param   mode    View Or Show
     * @return  mixed
     */
    public function countViewOrShow($id, $mode = 'view')
    {
        $this->db->select('count(user.id) as total')->
                from('user')->
                where('user_delete', 0);

        if($mode == 'view')
            $this->db->join('view', "user.id = view.user_show and view.user_view = {$id} and user.id != $id");
        else
            $this->db->join('view', "user.id = view.user_view and view.user_show = {$id} and user.id != $id");

        $get = $this->db->get()->row_array();

        return $get['total'];
    }

    /**
     * Prepare Image
     *
     * Resizes the profile image to different sizes
     *
     * @access	public
     * @param   data    array with uploaded image info
     * @return  mixed
     */
    public function prepare_image($data)
    {
        // Resize in three different sizes
        $target = array(
            0 => array('name' => 'large', 'width' => 128, 'height' => 128),
            1 => array('name' => 'medium', 'width' => 64, 'height' => 64),
            2 => array('name' => 'small', 'width' => 42, 'height' => 42),
            3 => array('name' => 'thumb', 'width' => 24, 'height' => 24)
        );

        $this->load->library('image_lib');

        for($i = 0; $i < 4; $i++) {
            // Image library settings
            $config = array();
            $config['source_image'] = $data['full_path'];
            $config['new_image']    = $data['file_path'].$data['raw_name'].'_'.$target[$i]['name'].$data['file_ext'];

            // Detect target size
            if(($data['image_width'] / $data['image_height']) >= ($target[$i]['width'] / $target[$i]['height'])) {
                // Resize according to height
                $config['width']  = $data['image_width'] * $target[$i]['height'] / $data['image_height'];
                $config['height'] = $target[$i]['height'];
            } else {
                // Resize according to width
                $config['width']  = $target[$i]['width'];
                $config['height'] = $data['image_height'] * $target[$i]['width'] / $data['image_width'];
            }

            $this->image_lib->clear();
            $this->image_lib->initialize($config);
            if(!$this->image_lib->resize()){
                return $this->image_lib->display_errors();
            }
        }

        return '';
    }


    /**
     * Connect
     *
     * Connect user 1 (view) to user 2 (show)
     *
     * @access	public
     * @param   int     View user ID
     * @param   string  Show user name
     * @return  mixed
     */
    public function connect($view_id, $show_username)
    {
        // Begin transaction with the database
        $this->db->trans_start();

        $this->db->select('id')->where('username', $show_username)->
                where('user_delete', 0);
        $get = $this->db->get('user');

        if($get->num_rows == 0)
            return false;

        $show = $get->row_array();

        $data = array(
            'user_view' => $view_id,
            'user_show' => $show['id'],
            'view_created' => date('Y-m-d H:i:s')
        );

        $this->db->insert('view', $data);

        // Create notification
        if(!$this->db->insert('user_notification',
                array(
                    'user_id' => $show['id'],
                    'user_id_created_by' => $view_id,
                    'user_notification_type' => 'connect',
                    'update_id' => 0,
                    'user_notification_read' => 0,
                    'user_notification_created' => date('Y-m-d H:i:s')
                    )
                ))
            log_message('error', "user_model->connect() - Insert into user_notification:".$this->db->_error_message());

        $this->db->trans_complete();

        if($this->db->trans_status() === false)
            return false;

        return true;
    }

    /**
     * Disconnect
     *
     * Disconnect user 1 (view) to user 2 (show)
     *
     * @access	public
     * @param   int     View user ID
     * @param   string  Show user name
     * @return  mixed
     */
    public function disconnect($view_id, $show_username)
    {
        $this->db->select('id')->where('username', $show_username)->
                where('user_delete', 0);
        $get = $this->db->get('user');

        if($get->num_rows == 0)
            return false;

        $show = $get->row_array();

        return $this->db->where('user_view', $view_id)->
                where('user_show', $show['id'])->
                delete('view');
    }

    /**
     * Search
     *
     * Search user for username and full_name
     *
     * @access	public
     * @param   string  Term to search
     * @param   int     ID of logged user
     * @return  mixed
     */
    public function search($term, $user_id = 0, $page = 1)
    {
        $this->db->select('id, username, full_name, bio, image_name, image_ext, website, user_show')->
                from('user')->
                where("(username LIKE '%".$this->db->escape_like_str($term)."%' or full_name LIKE '%".$this->db->escape_like_str($term)."%')")->
                where('user_delete', 0)->
                limit(PER_PAGE_DEFAULT, ($page-1) * PER_PAGE_DEFAULT);

        if($user_id)
            $this->db->join('view', "view.user_show = user.id and view.user_view = {$user_id}", 'left')->
                    where('user.id !=', $user_id);

        $get = $this->db->get();

        if($get->num_rows == 0)
            return false;

        return $get->result_array();
    }

    /**
     * Count Search
     *
     * Count search results
     *
     * @access	public
     * @param   string  Term to search
     * @param   int     ID of logged user
     * @return  mixed
     */
    public function countSearch($term, $user_id = 0)
    {
        $this->db->select('count(id) as total')->
                from('user')->
                where("(username LIKE '%".$this->db->escape_like_str($term)."%' or full_name LIKE '%".$this->db->escape_like_str($term)."%')")->
                where('user_delete', 0);

        if($user_id)
            $this->db->join('view', "view.user_show = user.id and view.user_view = {$user_id}", 'left')->
                    where('user.id !=', $user_id);

        $get = $this->db->get()->row_array();

        return $get['total'];
    }

    /*
     * Mark Deletion
     *
     * Mark user for deletion
     *
     * @access	public
     * @param   int     user id
     * @return  mixed
     */
    public function markDeleted($user_id){
        // Begin transaction with the database
        $this->db->trans_start();

        // Define all user updates as deleted
        $this->db->where('user', $user_id)->
                set('update_delete', 1)->
                set('update_delete_date', date('Y-m-d'))->
                update('update');

        // Define user as deleted
        $this->db->where('id', $user_id)->
                set('user_delete', 1)->
                set('user_delete_date', date('Y-m-d'))->
                update('user');

        $this->db->trans_complete();

        if($this->db->trans_status() === false)
            return false;

        return true;
    }

    /*
     * Get Delete Users
     *
     * Select all deleted users with more than 1 month
     *
     * @access	public
     * @return  mixed
     */
    public function getDeletedUsers(){
        $this->db->select('id, username')->
                from('user')->
                where('user_delete', 1)->
                where('user_delete_date <', 'DATE_SUB(NOW(), INTERVAL 1 MONTH)', false);

        return $this->db->get()->result_array();
    }

    /**
     * Remove Account
     *
     * Deletes de user account and all related data
     *
     * @access	public
     * @param   int     ID of logged user
     * @return  mixed
     */
    public function removeAccount($user_id, $username)
    {
        $this->db->where('id', $user_id)->
                delete('user');

        // Clear user folders
        $folder = getcwd().'/upload/profile/'.$username;
        if(is_dir($folder)) {
            empty_folder($folder);
            if(!remove_folder($folder)) {
                log_message('error', "user_model->removeAccount() - Remove folder failed:".$folder);
                return false;
            }
        }

        return true;
    }

    /**
     * Is Admin
     *
     * Validate if the user is admin
     *
     * @access	public
     * @param   int     ID of logged user
     * @return  mixed
     */
    public function isAdmin($user_id)
    {
        // Check if user entered email or username
        $this->db->select('id')->
                where('id', $user_id)->
                where('user_delete', 0)->
                where('is_admin', 1);

        // Check if user was found and get information
        $query = $this->db->get('user');

        if($query->num_rows == 1)
            return true;
        else
            return false;
    }

    /**
     * Invite User
     *
     * Send email with invitation for the user
     *
     * @access	public
     * @param   int     ID of logged user
     * @param   string  Email of invited user
     * @return  mixed
     */
    public function invite_user($user_id, $invite_email)
    {
        // Translation
        $this->lang->load('general', get_language_name());
        $this->lang->load('email', get_language_name());

        $user_data = $this->get(array('id' => $user_id));

        // Check if invited user exists
        $this->db->select('id')->
                where('email', $invite_email)->
                where('user_delete', 0);

        // Check if user was found and get information
        $query = $this->db->get('user');

        // Notify existing user
        $this->load->helper('string');
        $token = random_string('unique');

        $email_data = array(
            'username' => $user_data['username'],
            'full_name' => $user_data['full_name'],
            'invite_email' => $invite_email,
            'new_user' => ($query->num_rows == 0),
            'token' => $token
        );

        $this->load->library('PHPMail');
        if (!$this->phpmail->customSend(array(
            'body' => $this->load->view('emails/invite_user', $email_data, true),
            'from' => 'support@showthatyouhelp.com',
            'from_name' => lang('email_support_from_name'),
            'subject' => lang('email_invite_subject').' '.(($user_data['full_name'])?$user_data['full_name']:$user_data['username']),
            'to' => $invite_email,
            'to_name' => $invite_email
        ))) {
            log_message('error', "user_model->invite_user() - customSend() - ".$this->phpmail->ErrorInfo);
        }

        // Save invite info
        $this->db->insert('user_invite', array(
            'user_id' => $user_id,
            'user_invite_email' => $invite_email,
            'user_invite_token' => $token,
            'user_invite_date' => date('Y-m-d H:i:s')
        ));

        // Reduce invites left
        $this->db->where('id', $user_id)->
                    set('invites', 'invites - 1', false)->
                    update('user');

        return true;
    }

    /**
     * Check Invite Token
     *
     * Validate if the invite token is correct
     *
     * @access	public
     * @param   string   Invite token
     * @return  mixed
     */
    public function checkInvite($token)
    {
        // Check if user entered email or username
        $this->db->select('*')->
                where('user_invite_token', $token);

        // Check if user was found and get information
        $query = $this->db->get('user_invite');

        if($query->num_rows == 1)
            return $query->row_array();
        else
            return array();
    }

    /**
     * Clear Invite
     *
     * Remove invite from database
     *
     * @access	public
     * @param   string   Invite token
     * @return  mixed
     */
    public function clearInvite($invite_id)
    {
        $this->db->where('user_invite_id', $invite_id);
        return $this->db->delete('user_invite');
    }

    /**
     * Set Last Login
     *
     * Record last login date for the user
     *
     * @access	public
     * @param   int     User ID
     * @return  mixed
     */
    public function set_last_login($user_id)
    {
        $data['last_login_date'] = date('Y-m-d');
        $this->db->where('id', $user_id);
        return $this->db->update('user', $data);
    }

    /**
     * Get Extra
     *
     * Get the extra information fot he given user
     *
     * @access	public
     * @param   int     User ID
     * @return  mixed
     */
    public function get_extra($user_id)
    {
        $this->db->where('user_id', $user_id);
        $get = $this->db->get('user_extra');

        if($get->num_rows() == 0) {
            // Create default extra information
            $notify = json_encode(array(
                'notify_connect' => '1',
                'notify_mention' => '1',
                'notify_comment' => '1',
                'notify_reshare' => '1',
                'notify_pending' => '1',
                'notify_other' => '1'
            ));

            $extra = array(
                'user_id' => $user_id,
                'notifications' => $notify
            );

            $this->db->insert('user_extra', $extra);
        } else {
            $extra = $get->row_array();
        }

        $extra['notifications'] = json_decode($extra['notifications'], TRUE);

        if(!isset($extra['notifications']['notify_connect'])) $extra['notifications']['notify_connect'] = '1';
        if(!isset($extra['notifications']['notify_mention'])) $extra['notifications']['notify_mention'] = '1';
        if(!isset($extra['notifications']['notify_comment'])) $extra['notifications']['notify_comment'] = '1';
        if(!isset($extra['notifications']['notify_reshare'])) $extra['notifications']['notify_reshare'] = '1';
        if(!isset($extra['notifications']['notify_pending'])) $extra['notifications']['notify_pending'] = '1';
        if(!isset($extra['notifications']['notify_other'])) $extra['notifications']['notify_other'] = '1';

        return $extra;
    }

    /**
     * Get OAuth
     *
     * Get the information from an external sign in
     *
     * @access	public
     * @param   int     OAuth ID
     * @return  mixed
     */
    public function get_oauth($oauth_provider, $oauth_id = FALSE, $user_id = FALSE)
    {
        $this->db->where('oauth_provider', $oauth_provider);
        if($oauth_id)
            $this->db->where('oauth_uid', $oauth_id);
        if($user_id)
            $this->db->where('user_id', $user_id);
        $get = $this->db->get('user_oauth');

        if($get->num_rows() == 0)
            return false;
        else
            return $get->row_array();
    }

    /**
     * Create OAuth
     *
     * Create the information from an external sign in
     *
     * @access	public
     * @param   int     OAuth Data
     * @return  mixed
     */
    public function create_oauth($oauth_data)
    {
        return $this->db->insert('user_oauth', $oauth_data);
    }

    /**
     * Update OAuth
     *
     * Update the information from an external sign in
     *
     * @access	public
     * @param   int     OAuth Data
     * @return  mixed
     */
    public function update_oauth($oauth_data)
    {
        $this->db->where('user_id', $oauth_data['user_id']);
        return $this->db->update('user_oauth', $oauth_data);
    }

    /**
     * Remove OAuth
     *
     * Remove the information from an external sign in
     *
     * @access	public
     * @param   int     OAuth Data
     * @return  mixed
     */
    public function remove_oauth($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('user_oauth');
    }

    /**
     * Update Extra
     *
     * Save extra user information
     *
     * @access	public
     * @param   mixed   Data
     * @return  mixed
     */
    public function update_extra($data)
    {
        if(isset($data['notifications']))
            $data['notifications'] = json_encode($data['notifications']);

        $this->db->where('user_id', $data['user_id']);
        return $this->db->update('user_extra', $data);
    }

    /*
     * PRIVATE METHODS
     */
    /**
     * Get Recover Data
     *
     * Generate new password and recover data
     *
     * @access	public
     * @return  mixed
     */
    private function _getRecoverData($bkp_pass)
    {
        $this->load->helper('string');

        $token = random_string('unique');
        $password = random_string('alnum', 12);

        $data = array(
            'password' => $password,
            'recover_token' => $token,
            'recover_password' => $bkp_pass,
            'recover_date' => date('Y-m-d')
            );

        return $data;
    }

    /**
     * Set Recover Data
     *
     * Save recover data to information in the database
     *
     * @access	public
     * @return  mixed
     */
    private function _setRecoverData($user_id, $data)
    {
        // Begin transaction with the database
        $this->db->trans_start();

        // Use phpass PasswordHash library to hash the password
        $this->config->load('config_passhash');
        $this->load->library('PassHash', array(
            'iteration_count_log2' => $this->config->item('phpass_iteration_count_log2'),
            'portable_hashes' => $this->config->item('phpass_portable_hashes')
        ));

        $data['password'] = $this->passhash->HashPassword($data['password']);

        if (strlen($data['password']) < 20) {
            log_message('error', "user_model->_setRecoverData() - Hash length < 20");
            return false;
        }

        // Insert into recovery table
        $this->db->insert('user_recover', array(
            'user_id' => $user_id,
            'user_recover_token' => $data['recover_token'],
            'user_recover_password' => $data['recover_password'],
            'user_recover_date' => $data['recover_date']
            ));

        // Update user table
        $this->db->where('id', $user_id);
        $this->db->update('user', array('password' => $data['password']));

        $this->db->trans_complete();

        if($this->db->trans_status() === false)
            return false;

        return true;
    }


    /**
     * Restore Account
     *
     * Overwrite current password with restored one
     *
     * @access	public
     * @return  mixed
     */
    private function _restoreAccount($user_id, $hash)
    {
        // Begin transaction with the database
        $this->db->trans_start();

        $this->db->where('user_id', $user_id)->
                where('user_recover_date <', 'DATE_SUB(NOW(), INTERVAL 7 DAY)', false)->
                delete('user_recover');

        // Update user table
        $this->db->where('id', $user_id);
        $this->db->update('user', array('password' => $hash));

        $this->db->trans_complete();

        if($this->db->trans_status() === false)
            return false;

        return true;
    }
}
?>
