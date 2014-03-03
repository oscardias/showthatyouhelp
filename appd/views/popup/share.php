<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo lang('home_share'); ?> | showthayouhelp</title>

<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url().'css/share_style.css'; ?>" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>js/share_scripts.js"></script>

<meta name="description" content="<?php echo lang('general_seo_description_full'); ?>" />
<meta name="keywords" content="<?php echo lang('general_seo_keywords'); ?>" />

</head>
<body class="popup">
    <div id="top-navigation">
        <?php echo anchor("/", "showthatyouhelp.com", 'class="navigation-home"'); ?>
        <div id="top-navigation-actions">
            <?php if($this->session->userdata('logged_in')) : ?>
            <?php printf(lang('share_sharing_as'), $this->session->userdata('username')); ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="main" class="container">
        <div class="updates-list-share">
            <div class="updates-list-header">
                <h2><?php echo lang('home_share'); ?></h2>
            </div>
            <?php echo form_open("share", array('id' => 'share')); ?>
            <?php echo form_textarea(array('name' => 'comment'), '', 'placeholder="'.lang('home_share_place_text').'"'); ?>
            <?php echo form_input('url', $url,'id="url" placeholder="'.lang('home_share_place_link').'"'.(isset($error)?' class="error_box"':'')); ?>
            
            <?php echo form_hidden('remote', ''); ?>
            <?php echo form_hidden('title', ''); ?>
            <?php echo form_hidden('description', ''); ?>
            <?php echo form_hidden('image', ''); ?>
            <?php echo form_hidden('icon', ''); ?>
            <?php echo form_hidden('domain', ''); ?>
            <?php echo form_hidden('site_name', ''); ?>
            
            <?php echo form_submit('share', lang('home_share_submit'),'id="submit" class="gradient-btn"'); ?>
            <?php echo form_close(); ?>
            <div class="clear"></div>
            <div id="updates-list-share-answer <?php echo (isset($error)?'':'hide'); ?>">
                <?php if(isset($error)) : ?>
                <p class="error-msg"><?php echo lang('share_error_msg'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="bottom-navigation">
        <?php echo anchor(base_url('about'), lang('general_about')); ?>
        |
        <?php echo anchor(base_url('about/terms'), lang('general_terms')); ?>
        |
        <?php echo anchor(base_url('about/privacy'), lang('general_privacy')); ?>
        |
        &copy;<?php echo date('Y'); ?> <?php echo anchor(lang('general_softerize_link'), "Softerize"); ?>
    </div>
</body>
</html>