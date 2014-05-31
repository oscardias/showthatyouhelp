<?php

/**
 * showthatyouhelp URL Helpers
 *
 * @package		STYH
 * @subpackage          Helpers
 * @category            URL
 * @author		Oscar Dias
 */

// ------------------------------------------------------------------------

/**
 * Post Image URL
 *
 * Return URL for post's image.
 *
 * @access	public
 * @param	string	image array info
 * @param	string	size
 * @return	string
 */
if ( ! function_exists('image_url'))
{
	function image_url($image, $size = 'original')
	{
            switch ($size) {
                case 'small':
                    return base_url()."upload/photo/{$image['image_folder']}/{$image['image_id']}-{$image['image_filename']}-small{$image['image_fileext']}";
                    break;
                case 'large':
                    return base_url()."upload/photo/{$image['image_folder']}/{$image['image_id']}-{$image['image_filename']}-large{$image['image_fileext']}";
                    break;
                case 'original':
                    return base_url()."upload/photo/{$image['image_folder']}/{$image['image_id']}-{$image['image_filename']}{$image['image_fileext']}";
                    break;
                default:
                    return base_url()."upload/photo/{$image['image_folder']}/{$image['image_id']}-{$image['image_filename']}{$image['image_fileext']}";
            }
		
	}
}

/**
 * Icon Tag
 *
 * Return icon IMG tag
 *
 * @access	public
 * @param	string	Icon name
 * @return	string
 */
if ( ! function_exists('icon_img_tag'))
{
    function icon_img_tag($image, $title = false)
    {
        return '<img src="'.base_url().'images/icon/'.$image.'"'.(($title)?' alt="'.$title.'" ':'').' />';
    }
}

/**
 * User Profile
 *
 * Return URL for user profile.
 *
 * @access	public
 * @param	string	username
 * @return	string
 */
if ( ! function_exists('user_profile'))
{
    function user_profile($user)
    {
        return base_url()."p/{$user}";
    }
}

/**
 * User Update
 *
 * Return URL for user update.
 *
 * @access	public
 * @param	string	username
 * @param	int	update ID
 * @return	string
 */
if ( ! function_exists('user_update'))
{
    function user_update($username, $update, $pos = 'entry')
    {
        if($pos)
            return base_url("p/$username/$update#{$pos}");
        else
            return base_url("p/$username/$update");
    }
}

/**
 * User Profile Image
 *
 * Return URL for user profile image.
 *
 * @access	public
 * @param	string	username
 * @return	string
 */
if ( ! function_exists('user_profile_image'))
{
    function user_profile_image($user, $image_name, $image_ext, $size = 'small')
    {
        if($image_name)
            return base_url()."upload/profile/{$user}/{$image_name}_{$size}{$image_ext}";
        else
            return base_url()."images/profile/default_{$size}.png";
    }
}

/**
 * Site Fav Icon
 *
 * Return favicon IMG url
 *
 * @access	public
 * @param	string	site domain
 * @return	string
 */
if ( ! function_exists('site_favicon'))
{
    function site_favicon($domain, $image)
    {
        if($image)
            return base_url()."upload/site/{$domain}/{$image}";
        else
            return base_url()."images/favicon.png";
    }
}

/**
 * Share Button
 *
 * Share button for facebook, google+
 *
 * @access	public
 * @param	string	username
 * @param	int	update ID
 * @return	string
 */
if ( ! function_exists('share_button'))
{
    function share_button($target, $username, $update, $site_title = false, $comment = false, $image = false, $twitter = false)
    {
        if($comment)
            if($username == $twitter)
                $url_comment = htmlspecialchars_decode(strip_tags($comment));
            else {
                $url_comment = preg_replace('#@([^>]*)#i', '$1', strip_tags($comment));
                $url_comment = htmlspecialchars_decode($url_comment);
            }
        
        switch ($target) {
            case 'styh':
                $link = base_url()."share/local/".$update;
                $title = lang('home_share_showthatyouhelp');
                return '<a href="'.$link.'" title="'.$title.'" class="entry-btn gradient-btn local-share-action"><i class="styh-btn"></i></a>';
            
            case 'facebook':
                $link = 'http://www.facebook.com/dialog/feed?app_id=251130335006292&link='.
                    urlencode(user_update($username, $update, false)).
                    ($image?'&picture='.urlencode($image):'').
                    ($site_title?'&name='.urlencode($site_title):'').
                    ('&caption='.urlencode($username.'\'s update on showthatyouhelp.com')).
                    ($comment?'&description='.urlencode($url_comment):'').
                    '&redirect_uri='.urlencode(user_update($username, $update, false));
                $title = lang('home_share_facebook');
                return '<a href="'.$link.'" title="'.$title.'" class="entry-btn gradient-btn remote-share-action" target="_blank"><i class="facebook-btn"></i></a>';

            case 'gplus':
                $link = "https://plus.google.com/share?url=".urlencode(user_update($username, $update, false));
                $title = lang('home_share_google');
                return '<a href="'.$link.'" title="'.$title.'" class="entry-btn gradient-btn remote-share-action" target="_blank"><i class="gplus-btn"></i></a>';
            
            case 'twitter':
                if($comment){
                    $text = character_limiter($url_comment, 90, '...');
                } else {
                    $text = $username.'\'s update';
                }
                
                $link = "https://twitter.com/share?url=".urlencode(user_update($username, $update, false)).
                        '&via=showthatyouhelp'.
                        ($text?'&text='.urlencode($text):'');
                $title = lang('home_share_twitter');
                return '<a href="'.$link.'" title="'.$title.'" class="entry-btn gradient-btn remote-share-action" target="_blank"><i class="twitter-btn"></i></a>';
        }
    }
}

/**
 * File Contents Get URL
 *
 * Get content of the web page via URL
 *
 * @access	public
 * @param	string	page url
 * @return	string
 */
if ( ! function_exists('file_get_contents_curl'))
{
    function file_get_contents_curl($url)
    {
        $ch      = curl_init( ); 
        if(!$ch)
            return false;
        
        $cookie_jar = tempnam('/tmp','cookie');
        
        $options = array( 
            CURLOPT_RETURNTRANSFER => true,    // return web page 
            CURLOPT_HEADER         => true,    // return headers 
            CURLOPT_FOLLOWLOCATION => true,    // follow redirects 
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL            => $url,
            CURLOPT_COOKIESESSION  => true,
            CURLOPT_COOKIEJAR      => $cookie_jar,
            CURLOPT_USERAGENT      => 'ShowThatYouHelp/1.0',
            CURLOPT_MAXREDIRS      => 10       // stop after 10 redirects 
        ); 

        curl_setopt_array( $ch, $options ); 
        $content = curl_exec( $ch ); 
        //$err     = curl_errno( $ch ); 
        //$errmsg  = curl_error( $ch ); 
        $header  = curl_getinfo( $ch ); 
        curl_close( $ch );
        
        //checking mime types
        if(strstr($header['content_type'],'text/html')) {
            return array('header' => $header, 'content' => $content);
        } else {
            return false;
        }
    }
}

/**
 * File Contents Get File
 *
 * Get content of the web page via URL
 *
 * @access	public
 * @param	string	image url
 * @return	string
 */
if ( ! function_exists('file_get_contents_file'))
{
    function file_get_contents_file($url, $target)
    {
        $ch = curl_init( );
        if(!$ch)
            return false;
        
        $options = array( 
            CURLOPT_HEADER         => false,    // return headers 
            CURLOPT_RETURNTRANSFER => true,     // return web page 
            CURLOPT_BINARYTRANSFER => true,     // return web page 
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => 'ShowThatYouHelp/1.0',
            CURLOPT_MAXREDIRS      => 5       // stop after 10 redirects 
        );
        curl_setopt_array( $ch, $options ); 
        
        $rawdata = curl_exec($ch);
        curl_close($ch);
        
        if(!$rawdata)
            return false;
        
        $fp = fopen($target, 'x');
        fwrite($fp, $rawdata);
        fclose($fp);
        
        return true;
    }
}

/**
 * Prepare text
 *
 * Prepare text to be displayed
 *
 * @access	public
 * @param	string	text
 * @return	string
 */
if ( ! function_exists('prepare_text'))
{
    function prepare_text($text)
    {
        $text = nl2br($text);
        
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        if(preg_match($reg_exUrl, $text, $url)) {
            $text = preg_replace(
                    $reg_exUrl,
                    "<a href=\"{$url[0]}\">{$url[0]}</a>",
                    $text);
        }
        
        return preg_replace(
            '/@(\w+)/',
            '<a href="'.base_url().'p/$1" title="$1\'s profile">@$1</a>',
            $text
        );
    }
}

/**
 * Remove mention link
 *
 * Remove mention links for reshare
 *
 * @access	public
 * @param	string	text
 * @return	string
 */
if ( ! function_exists('remove_mention_link'))
{
    function remove_mention_link($text)
    {
        return preg_replace(
                '#<a.*?>(@[^>]*)</a>#i', 
                '$1', 
                $text);
    }
}

/**
 * Time Since
 *
 * Show time since a given date/time
 *
 * @access	public
 * @param	int     $time
 * @return	string
 */
if ( ! function_exists('timeSince'))
{
    function timeSince ($time)
    {

        $time = time() - $time; // to get the time since that moment

        $tokens = array (
            31536000 => array(lang('date_year'), lang('date_years')), // 60 * 60 * 24 * 365
            2592000 => array(lang('date_month'), lang('date_months')), // 60 * 60 * 24 * 30
            604800 => array(lang('date_week'), lang('date_weeks')), // 60 * 60 * 24 * 7
            86400 => array(lang('date_day'), lang('date_days')), // 60 * 60 * 24
            3600 => array(lang('date_hour'), lang('date_hours')), // 60 * 60
            60 => array(lang('date_minute'), lang('date_minutes')), // 60
            1 => array(lang('date_second'), lang('date_seconds'))
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.(($numberOfUnits>1)?$text[1]:$text[0]).' '.lang('js_ago');
        }

        return lang('home_just_now');
    }
}

/**
 * View Comments
 *
 * Show button link with comment count
 *
 * @access	public
 * @param	mixed     Count
 * @return	string
 */
if ( ! function_exists('viewComments'))
{
    function viewComments ($item)
    {
        if($item['update_comment_count'] == 1) {
            return anchor(user_update($item['username'], $item['update_id'], 'comments'),
                    $item['update_comment_count'].' '.lang('js_comment'),
                    'title="'.lang('js_view_comments').'" class="gradient-btn relative updates-view-comments-btn view-comments-action"');
        } else if($item['update_comment_count'] > 1) {
            return anchor(user_update($item['username'], $item['update_id'], 'comments'),
                    $item['update_comment_count'].' '.lang('js_comments'),
                    'title="'.lang('js_view_comments').'" class="gradient-btn relative updates-view-comments-btn view-comments-action"');
        }
    }
}
