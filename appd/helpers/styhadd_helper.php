<?php

/**
 * showthatyouhelp additional Helpers
 *
 * @package		STYH
 * @subpackage          Helpers
 * @category            Other
 * @author		Oscar Dias
 */

// ------------------------------------------------------------------------

/**
 * Remove directory files
 *
 * Remove files inside folder
 *
 * @access	public
 * @param	string	folder
 * @return	string
 */
if ( ! function_exists('empty_folder'))
{
    function empty_folder($dir)
    {
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if($object != '.' AND $object != '..')
            {
                unlink($dir.'/'.$object);
            }
        }
        
        reset($objects);
    }
}

/**
 * Remove directory recursively
 *
 * Remove folder completely
 *
 * @access	public
 * @param	string	folder
 * @return	string
 */
if ( ! function_exists('remove_folder'))
{
    function remove_folder($dir)
    {
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if($object != '.' AND $object != '..')
            {
                if(is_dir($dir.'/'.$object))
                    remove_folder($dir.'/'.$object);
                else
                    unlink($dir.'/'.$object);
            }
        }
        
        reset($objects);
        return rmdir($dir);
    }
}

/**
 * Remove files
 *
 * Remove files in the array
 *
 * @access	public
 * @param	array	files
 * @return	string
 */
if ( ! function_exists('remove_files'))
{
    function remove_files($files)
    {
        foreach ($files as $file)
        {
            if(is_dir($file))
                remove_folder($file);
            else
                unlink($file);
        }
    }
}

/**
 * Create folder
 *
 * Create folder if it does not exists
 *
 * @access	public
 * @param	string	folder
 * @return	string
 */
if ( ! function_exists('create_folder'))
{
    function create_folder($dir, $clear = true)
    {
        if(is_dir($dir)) {
            if($clear)
                empty_folder($dir);
        } else {
            if(!mkdir($dir, 0755, true)){
                log_message('error', "create_folder() - mkdir({$dir})");
                return false;
            }
        }
        return true;
    }
}

/**
 * Is XHR
 *
 * Check if request is jQuery AJAX
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('isXhr'))
{
    function isXhr() {  
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

/**
 * Get Language Name
 *
 * Get user language from cookie and return lagunage name
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('get_language_name'))
{
    function get_language_name($lang = false) {
        if($lang === false) {
            if(!isset($_COOKIE['language'])) {
                if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                    setcookie('language', substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), time()+60*60*24*30, '/');
                    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                } else {
                    setcookie('language', 'en', 0, '/');
                    $lang = 'en';
                }
            } else
                $lang = $_COOKIE['language'];
        }
        
        switch ($lang) {
            case 'pt':
                return 'portuguese';

            default:
                return 'english';
        }
    }
}

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @access	public
 * @param	string	the language line
 * @param	string	the id of the form element
 * @return	string
 */
if ( ! function_exists('lang'))
{
	function lang($line, $id = '')
	{
		$CI =& get_instance();
		$line = $CI->lang->line($line);

		if ($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}

		return $line;
	}
}
