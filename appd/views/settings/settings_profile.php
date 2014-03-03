<div class="settings-form-page settings-profile">
    <div class="profile-image-large">
        <img src="<?php echo user_profile_image($username, $image_name, $image_ext, 'large'); ?>"
             alt="<?php printf(lang('home_profile_image'), $username); ?>" />
    </div>
    <div class="settings-profile-column">
        <div>
            <?php $error_msg = form_error('full_name', '<div>', '</div>'); ?>
            <?php echo form_label(lang('user_profile_name'), 'full_name'); ?>
            <?php echo form_input('full_name', set_value('full_name', $full_name), 'id="full_name" maxlength="255" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
            <div id="error_full_name" class="error-msg">
                <?php echo $error_msg; ?>
            </div>
        </div>
        <div>
            <?php $error_msg = form_error('bio', '<div>', '</div>'); ?>
            <div class="settings-label">
                <?php echo form_label(lang('user_profile_bio'), 'bio'); ?>
            </div>
            <div class="settings-input">
                <?php echo form_textarea(array('name' => 'bio','cols' => '25', 'rows' => '3'), set_value('bio', $bio), 'id="bio"'.(($error_msg)?' class="error_box"':'')); ?>
                <small><?php echo lang('user_profile_bio_size'); ?> <span id="bio-length-remaining"><?php echo (255-strlen($bio)); ?></span></small>
            </div>
            <div id="error_bio" class="error-msg">
                <?php echo $error_msg; ?>
            </div>
        </div>
        <div>
            <?php $error_msg = form_error('website', '<div>', '</div>'); ?>
            <?php echo form_label(lang('user_profile_website'), 'website'); ?>
            <?php echo form_input('website', set_value('website', $website), 'id="website" maxlength="255" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
            <div id="error_website" class="error-msg">
                <?php echo $error_msg; ?>
            </div>
        </div>
        <div>
            <?php $error_msg = form_error('language', '<div>', '</div>'); ?>
            <?php echo form_label(lang('user_profile_language'), 'language'); ?>
            <?php echo form_dropdown('language', array('en' => 'English', 'pt' => 'Português'), set_value('language', $language), 'id="language" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
            <div id="error_language" class="error-msg">
                <?php echo $error_msg; ?>
            </div>
        </div>
        <div>
        <?php $error_msg = form_error('picture', '<div>', '</div>').$upload_error; ?>
        <div class="settings-label">
            <?php echo form_label(lang('user_profile_picture'), 'picture'); ?>
        </div>
        <div class="settings-input">
            <?php echo form_upload('picture', set_value('picture'), 'id="picture"'.(($error_msg)?' class="error_box"':'')); ?>
            <small><?php echo lang('user_profile_picture_info'); ?></small>
        </div>
        <div id="error_picture" class="error-msg">
            <?php echo $error_msg; ?>
        </div>
        </div>
    </div>
</div>