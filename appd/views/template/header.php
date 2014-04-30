<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $seo_title; ?></title>

<?php
switch (ENVIRONMENT) {
    case 'production':
        $minified = '.min';
    break;
 
    default:
        $minified = '';
    break;
}

$refresh_version = '?v=1.15';
?>

<link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url('css/style'.$minified.'.css'.$refresh_version); ?>" />
<!--[if IE 9]>
<link type="text/css" rel="stylesheet" href="<?php echo base_url('css/styleIE9'.$minified.'.css'.$refresh_version); ?>" />
<![endif]-->
<!--[if lte IE 8]>
<link type="text/css" rel="stylesheet" href="<?php echo base_url('css/styleIE8'.$minified.'.css'.$refresh_version); ?>" />
<![endif]-->

<?php if(isset($extra_css) && $extra_css) : ?>
<?php foreach ($extra_css as $value) { ?>
<link type="text/css" rel="stylesheet" href="<?php echo $value.$minified.'.css'.$refresh_version; ?>" />
<?php } ?>
<?php endif; ?>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
</script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('language/js/output.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/scripts'.$minified.'.js'.$refresh_version); ?>"></script>


<?php if(isset($extra_js) && $extra_js) : ?>
<?php foreach ($extra_js as $value) { ?>
<script type="text/javascript" src="<?php echo $value.$minified.'.js'.$refresh_version; ?>"></script>
<?php } ?>
<?php endif; ?>

<meta name="description" content="<?php echo $seo_description; ?>" />
<meta name="keywords" content="<?php echo $seo_keywords; ?>" />

<?php if($seo_add) : ?>
<?php if(isset($seo_add['next'])) : ?><link rel="next" href="<?php echo $seo_add['next']; ?>" /><?php endif; ?>
<?php if(isset($seo_add['prev'])) : ?><link rel="prev" href="<?php echo $seo_add['prev']; ?>" /><?php endif; ?>

<meta name="twitter:card" content="<?php  echo $seo_add['card']; ?>">
<meta name="twitter:site" content="<?php  echo $seo_add['site']; ?>">
<meta name="twitter:url" content="<?php  echo $seo_add['url']; ?>">
<meta name="twitter:title" content="<?php  echo $seo_add['title']; ?>">
<meta name="twitter:description" content="<?php  echo $seo_add['short_description']; ?>">
<meta name="twitter:image" content="<?php  echo $seo_add['image']; ?>">

<meta property="og:type" content="<?php  echo $seo_add['type']; ?>">
<meta property="og:url" content="<?php  echo $seo_add['url']; ?>">
<meta property="og:title" content="<?php  echo $seo_add['title']; ?>">
<meta property="og:description" content="<?php  echo $seo_add['description']; ?>">
<meta property="og:image" content="<?php  echo $seo_add['image']; ?>">
<?php endif; ?>

<?php if(ENVIRONMENT === 'production') : ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-34576670-1', 'showthatyouhelp.com');
  ga('send', 'pageview');

</script>
<?php endif; ?>

</head>
<body>
    <div id="top-navigation">
        <?php echo anchor("/", "showthatyouhelp.com", 'class="navigation-home"'); ?>
        <?php if(!isset($hide_actions)) : ?>
            <?php if($this->session->userdata('logged_in') === true) : ?>
            <div id="search-form">
                <?php echo form_open('user/search', array('method' => 'get')); ?>
                <?php echo form_input('s', '', 'placeholder="'.lang('home_search').'"'); ?>
                <input type="submit" name="" value="&nbsp;">
                <?php echo form_close(); ?>
            </div>
        
            <ul id="top-navigation-actions">
                <li>
                    <div class="top-navigation-image">
                    <img src="<?php echo $user_profile_image; ?>" alt="@<?php echo $this->session->userdata('username'); ?>" />
                    </div>
                    <?php echo anchor(user_profile($this->session->userdata('username')), '@'.$this->session->userdata('username'), 'class="top-link"'); ?>
                </li>
                <li>
                    <?php echo anchor('home/notifications', lang('general_notifications') . $notification_count, 'id="notifications-link" class="top-link"'); ?>
                </li>
                <li class="menu">
                    <a href="#" id="top-navigation-menu-icon" class="top-link">&nbsp;</a>
                    <ul id="top-navigation-menu-window">
                        <li><?php echo anchor('home', '<i></i>'.lang('general_home'), 'class="home"'); ?></li>
                        <li><?php echo anchor("user/settings", '<i></i>'.lang('general_settings'), 'class="settings"'); ?></li>
                        <li><?php echo anchor("sign/out", '<i></i>'.lang('general_sign_out'), 'class="signout"'); ?></li>
                    </ul>
                </li>
            </ul>
            <?php else : ?>
            <ul id="top-navigation-actions">
                <?php echo anchor("sign/up", lang('general_sign_up')); ?>
                |
                <?php echo anchor("home".(isset($redirect_url)?"?redirect=$redirect_url":""), lang('general_sign_in')); ?>
            </ul>
            <?php endif; ?>
        <?php else : ?>
        <ul id="top-navigation-actions">
            <?php if(isset($_COOKIE['language']) && $_COOKIE['language'] == 'pt') : ?>
        <a href="<?php echo base_url('home/index?lang=en'); ?>" title="Switch to English">English</a>
            <?php else : ?>
        <a href="<?php echo base_url('home/index?lang=pt'); ?>" title="Mudar para Português">Português</a>
            <?php endif; ?>
        </ul>
        <?php endif; ?>
    </div>