<?php
/*
    PHP File   : ssl_helper.php
    Created on : 14/01/2010, 19:38:55
    Author     : Oscar
    Description:
        SSL Helper for the Codeigniter.
*/
/**
 * Forces secure SSL connection
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('force_ssl'))
{
    function force_ssl()
    {
        $CI =& get_instance();

        // Set new base_url
        $CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);

        // Check port and redirect if needed
        if ($_SERVER['SERVER_PORT'] != 443)
        {
            redirect($CI->uri->uri_string());
        }
    }
}

// ------------------------------------------------------------------------

/* End of file ssl_helper.php */
/* Location: ./system/application/helpers/ssl_helper.php */