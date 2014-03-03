<?php
/**
 * Notification Model
 *
 * @package		STYH
 * @subpackage          Model
 * @category            Notification
 * @author		Oscar Dias
 */

class Notification_model extends CI_Model {
    
    /**
     * Unread Count
     *
     * Get the number of unread notifications.
     *
     * @access	public
     * @param   user_id     ID of the user
     * @return  bool
     */
    public function unreadCount($user_id, $update_id = false, $type = false)
    {
        $this->db->select('count(user_notification_id) as total')->
                where('user_id', $user_id)->
                where('user_notification_read', 0);
        
        if($update_id)
            $this->db->where('update_id', $update_id);
            
        if($type)
            $this->db->where('user_notification_type', $type);
        
        $this->db->from('user_notification')->
                join('update', 'user_notification.update_id = update.id', 'left')->
                where('(user_notification.update_id = 0 OR update.update_delete = 0)');
        
        $get = $this->db->get()->row_array();
        return $get['total'];
    }
    
    /**
     * Count Notifications
     *
     * Get the number of notifications.
     *
     * @access	public
     * @param   user_id     ID of the user
     * @return  bool
     */
    public function countNotifications($user_id)
    {
        $this->db->select('count(user_notification_id) as total')->
                from('user_notification')->
                join('update', 'user_notification.update_id = update.id', 'left')->
                where('user_id', $user_id)->
                where('(user_notification.update_id = 0 OR update.update_delete = 0)');
        $get = $this->db->get()->row_array();
        return $get['total'];
    }
    
    /**
     * Mark as Read
     *
     * Mark notifications as read
     *
     * @access	public
     * @param   user_id     ID of the user
     * @return  bool
     */
    public function markRead($user_id, $update_id = false, $user_notification_id = false, $type = false)
    {
        $this->db->where('user_id', $user_id);
        
        if($update_id)
            $this->db->where('update_id', $update_id);
        
        if($user_notification_id)
            $this->db->where('user_notification_id', $user_notification_id);
        
        if($type)
            $this->db->where('user_notification_type', $type);
        
        if(!$this->db->set('user_notification_read', 1)->set('user_notification_email', 1)->update('user_notification')) {
            log_message('error', "notification_model->markRead() - Update notification:".$this->db->_error_message());
            return false;
        }
        
        return true;
    }
    
    /**
     * Get
     *
     * Get the user notifications
     *
     * @access	public
     * @param   user_id     ID of the user
     * @return  bool
     */
    public function get($user_id, $page = 1)
    {
        $this->db->select('u.id as user_id,
            u.username,
            u.image_name,
            u.image_ext,
            u.full_name,
            un.user_notification_type,
            un.update_id,
            un.user_notification_read,
            un.user_notification_created')->
                from('user_notification un')->
                join('user u', 'un.user_id_created_by = u.id')->
                join('update up', 'un.update_id = up.id', 'left')->
                where('un.user_id', $user_id)->
                where('(un.update_id = 0 OR up.update_delete = 0)')->
                order_by('user_notification_read asc, user_notification_created desc')->
                limit(PER_PAGE_DEFAULT, ($page - 1) * PER_PAGE_DEFAULT);
        return $this->db->get()->result_array();
    }
    
    /**
     * Get Users Pending
     *
     * Get users with pending notifications
     *
     * @access	public
     * @return  bool
     */
    public function get_users_pending()
    {
        $this->db->select('u.id as user_id,
            u.username,
            u.full_name,
            u.email,
            u.language,
            ue.notifications,
            count(un.user_notification_id) as num_notifications')->
                from('user u')->
                join('user_notification un', 'un.user_id = u.id')->
                join('user_extra ue', 'ue.user_id = u.id', 'left')->
                where('un.user_notification_read', 0)->
                order_by('u.id desc')->
                group_by('u.id');
        return $this->db->get()->result_array();
    }
    
    /**
     * Get notifications pending
     *
     * Get notifications with pending emails ('comment','mention','comment_mention','connect','reshare')
     *
     * @access	public
     * @return  bool
     */
    public function get_notifications_pending()
    {
        $this->db->select('u.id as user_id,
            u.username,
            u.full_name,
            u.email,
            u.language,
            ue.notifications,
            un.user_notification_type as type,
            un.update_id as update_id,
            u_by.username as by_username,
            u_by.full_name as by_full_name')->
                from('user u')->
                join('user_notification un', 'un.user_id = u.id')->
                join('user u_by', 'u_by.id = un.user_id_created_by AND u_by.id != u.id')->
                join('user_extra ue', 'ue.user_id = u.id', 'left')->
                where('un.user_notification_email', 0)->
                where('un.user_notification_read', 0)->
                order_by('u.id desc');
        return $this->db->get()->result_array();
    }

    /**
     * Set notifications sent
     *
     * Set notifications as sent
     *
     * @access	public
     * @return  bool
     */
    public function set_notifications_sent()
    {
        $this->db->where('user_notification_email', 0);
        
        if(!$this->db->set('user_notification_email', 1)->update('user_notification')) {
            log_message('error', "notification_model->set_notifications_sent() - Update notification:".$this->db->_error_message());
            return false;
        }
    }

}
