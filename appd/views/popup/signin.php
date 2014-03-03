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
    var base_url = <?php echo base_url(); ?>;
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
    <div id="main" class="landing">
        <div class="landing-wrap">
            <div class="landing-logo">
            </div>
            <div class="landing-info">
                <div class="sign-in-wrap">
                    <?php echo form_open("sign/popup"); ?>
                    <?php echo form_input('username', set_value('username'), 'maxlength="50" placeholder="'.lang('home_username').'" '.(($invalid)?'class="error_box"':'')); ?>
                    <?php echo form_password('password', set_value('password'), 'placeholder="'.lang('home_password').'" '.(($invalid)?'class="error_box"':'')); ?>
                    <?php echo form_hidden('share', (($share)?$share:base_url())); ?>
                    <?php echo form_submit('signin', lang('home_sign_in'), 'class="gradient-btn"'); ?>
                    <?php echo form_close(); ?>
                </div>
                <div class="sign-up-wrap">
                    New user? Register here:
                    <?php echo anchor("sign/up", lang('home_sign_up'), 'class="gradient-btn"'); ?>
                </div>
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