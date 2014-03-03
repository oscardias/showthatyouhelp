<?php

/*
 * Additional validations
 */

class MY_Form_validation extends CI_Form_validation {
    
    public function username_check($str)
    {
        if (!preg_match("/^[A-Za-z0-9_]+$/", $str)) {
            $this->set_message('username_check', 'The %s field may contain only letters, numbers and underscores.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function validate_password($str)
    {
        $this->CI->load->model('user_model');
        if($this->CI->user_model->validate($this->CI->session->userdata('username'), $str))
            return true;

        $this->set_message('validate_password', '%s is wrong.');
        return false;
    }
    
    public function is_unique($str, $field)
    {
        list($table, $field)=explode('.', $field);
        
        if($this->CI->session->userdata('logged_in')) {
            $query = $this->CI->db->limit(1)->where($field, $str)->
                    where('id !=', $this->CI->session->userdata('id'))->
                    where('user_delete', 0)->
                    get($table);
        } else {
            $query = $this->CI->db->limit(1)->where($field, $str)->
                    where('user_delete', 0)->
                    get($table);
        }

        return $query->num_rows() === 0;
    }
}