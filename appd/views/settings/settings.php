<div id="main" class="container settings-edit-page">
    <h1><?php echo lang('user_profile_title'); ?></h1>
    
    <div class="settings-message <?php echo ($message?'':'hide'); ?>">
        <?php echo $message; ?>
    </div>
    
    <?php
    if($active == 'profile')
        echo form_open_multipart('user/settings/'.$active, 'id="settings-form"');
    else
        echo form_open('user/settings/'.$active, 'id="settings-form"');
    ?>
    
    <div class="settings-menu">
        <?php echo anchor('user/settings/profile', lang('user_settings_profile'), 'id="settings-menu-profile" class="settings-menu-item '.(($active == 'profile')?'selected':'').'"'); ?>
        <?php echo anchor('user/settings/email', lang('user_settings_email'), 'id="settings-menu-email" class="settings-menu-item '.(($active == 'email')?'selected':'').'"'); ?>
        <?php echo anchor('user/settings/password', lang('user_settings_password'), 'id="settings-menu-password" class="settings-menu-item '.(($active == 'password')?'selected':'').'"'); ?>
        <?php echo anchor('user/settings/notification', lang('user_settings_notification'), 'id="settings-menu-notification" class="settings-menu-item '.(($active == 'notification')?'selected':'').'"'); ?>
        <?php echo anchor('user/settings/twitter', lang('user_settings_twitter'), 'id="settings-menu-twitter" class="settings-menu-item '.(($active == 'twitter')?'selected':'').'"'); ?>
        <?php echo anchor('user/settings/remove', lang('user_settings_delete'), 'id="settings-menu-remove" class="settings-menu-item '.(($active == 'remove')?'selected':'').'"'); ?>
    </div>
    <div class="settings-form-wrap">
        <?php switch ($active) {
                case 'profile':
                    $this->load->view('settings/settings_profile', array(
                        'username' => $this->session->userdata('username'),
                        'image_name' => $image_name,
                        'image_ext' => $image_ext,
                        'full_name' => (isset($full_name)?$full_name:''),
                        'bio' => (isset($bio)?$bio:''),
                        'website' => (isset($website)?$website:''),
                        'upload_error' => (isset($upload_error)?'<div>'.$upload_error.'</div>':'')
                    ));
                    break;

                case 'email':
                    $this->load->view('settings/settings_email', array(
                        'email' => (isset($email)?$email:'')
                    ));
                    break;
                
                case 'password':
                    $this->load->view('settings/settings_password', array(
                        'current_password' => (isset($current_password)?$current_password:''),
                        'password' => (isset($password)?$password:''),
                        'password_confirm' => (isset($password_confirm)?$password_confirm:'')
                    ));
                    break;
                
                case 'notification':
                    $this->load->view('settings/settings_notification', array(
                        'current_password' => (isset($current_password)?$current_password:''),
                        'password' => (isset($password)?$password:''),
                        'password_confirm' => (isset($password_confirm)?$password_confirm:'')
                    ));
                    break;
                
                case 'twitter':
                    $this->load->view('settings/settings_twitter', array(
                        'oauth' => (isset($oauth)?$oauth:array())
                    ));
                    break;
                
                case 'remove':
                    $this->load->view('settings/settings_remove', array(
                        'remove' => (isset($remove) && $remove)
                    ));
                    break;
            } ?>
    </div>
    
    <div class="clear"></div>
    
    <div class="form-buttons <?php echo ($active == 'remove' || $active == 'twitter'?'hide':''); ?>">
        <?php
        echo form_submit('update', lang('user_profile_save'),'class="gradient-btn"');
        echo form_button('cancel', lang('user_profile_cancel'), 'class="gradient-btn" onClick="window.location=\''.base_url().'\';"');
        echo form_close();
        ?>
    </div>

</div>