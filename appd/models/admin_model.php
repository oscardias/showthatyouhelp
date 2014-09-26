<?php
/**
 * Admin Model
 *
 * Model for the admin section
 *
 * @package		STYH
 * @subpackage          Model
 * @category            Admin
 * @author		Oscar Dias
 */

class Admin_model extends CI_Model {

    /**
     * Get Tables
     *
     * Get all tables from the database
     *
     * @access	public
     * @return  bool
     */
    public function get_tables()
    {
        $query = $this->db->query('show table status');
        if ($query->num_rows == 0) {
            return array();
        }
        return $query->result_array();
    }

    /**
     * Get Schema
     *
     * Get table schema
     *
     * @access	public
     * @param   table   Table to be fetched
     * @return  bool
     */
    public function get_schema($table)
    {
        $query = $this->db->query("describe `".$this->db->escape_str($table)."`");
        if ($query->num_rows == 0) {
            return array();
        }
        return $query->result_array();
    }

    /**
     * Get Data
     *
     * Get all data from a given table
     *
     * @access	public
     * @param   table   Table to be fetched
     * @param   offset  Offset according to pagination
     * @return  bool
     */
    public function get_data($table, $page)
    {
        return $this->db->limit(PER_PAGE_ADMIN, ($page-1) * PER_PAGE_ADMIN)->
                get($table)->
                result_array();
    }

    /**
     * Get Total
     *
     * Get total number of rows
     *
     * @access	public
     * @param   table   Table to be fetched
     * @return  bool
     */
    public function get_total($table)
    {
        return $this->db->select('count(*) as total')->
                get($table)->
                row_array();
    }

}
?>
