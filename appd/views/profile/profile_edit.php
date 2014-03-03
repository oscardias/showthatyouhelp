<div id="main" class="container profile-edit-page">
    <h1><?php echo lang('user_profile_edit'); ?></h1>
    <?php
    echo form_open_multipart('user/profile');
    ?>
    
    <div class="profile-edit-info">
        <table>
            <tr>
                <?php $error_msg = form_error('full_name', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_name'), 'full_name'); ?>
                </td>
                <td class="input">
                    <?php echo form_input('full_name', set_value('full_name', (isset($full_name)?$full_name:'')), 'id="full_name" maxlength="255"'.(($error_msg)?' class="error_box"':'')); ?>
                </td>
                <td>
                    <div class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('bio', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_bio'), 'bio'); ?>
                </td>
                <td class="input">
                    <?php echo form_textarea(array('name' => 'bio','cols' => '25', 'rows' => '3'), set_value('bio', (isset($bio)?$bio:'')), 'id="bio"'.(($error_msg)?' class="error_box"':'')); ?>
                    <small><?php echo lang('user_profile_bio_size'); ?> <span id="bio-length-remaining"><?php echo (isset($bio)?255-strlen($bio):255); ?></span></small>
                </td>
                <td>
                    <div class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('website', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_website'), 'website'); ?>
                </td>
                <td class="input">
                    <?php echo form_input('website', set_value('website', (isset($website)?$website:'')), 'id="website" maxlength="255"'.(($error_msg)?' class="error_box"':'')); ?>
                </td>
                <td>
                    <div class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('picture', '<div>', '</div>').(isset($upload_error)?'<div>'.$upload_error.'</div>':''); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_picture'), 'picture'); ?>
                </td>
                <td class="input">
                    <?php echo form_upload('picture', set_value('picture'), 'id="picture"'.(($error_msg)?' class="error_box"':'')); ?>
                    <small><?php echo lang('user_profile_picture_info'); ?></small>
                </td>
                <td>
                    <div class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="profile-image-large">
        <img src="<?php echo user_profile_image($this->session->userdata('username'), $image_name, $image_ext, 'large'); ?>"
             alt="<?php echo $this->session->userdata('username'); ?>'s Profile Image" />
    </div>
    <div class="clear"></div>
    <div class="form-buttons">
    <?php
    echo form_hidden('image_name', $image_name);
    echo form_hidden('image_ext', $image_ext);
    
    echo form_submit('update', lang('user_profile_save'),'class="gradient-btn"');
    echo form_button('cancel', lang('user_profile_cancel'), 'class="gradient-btn" onClick="window.location=\''.base_url().'\';"');
    echo form_close();
    ?>
    </div>

</div>