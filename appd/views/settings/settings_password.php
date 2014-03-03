<div class="settings-form-page settings-password">
    <div>
        <?php $error_msg = form_error('current_password', '<div>', '</div>'); ?>
        <?php echo form_label(lang('user_profile_password_current'), 'current_password'); ?>
        <?php echo form_password('current_password', set_value('current_password', $current_password), 'id="current_password" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
        <div id="error_current_password" class="error-msg">
            <?php echo $error_msg; ?>
        </div>
    </div>
    <div>
        <?php $error_msg = form_error('password', '<div>', '</div>'); ?>
        <?php echo form_label(lang('user_profile_password_new'), 'password'); ?>
        <?php echo form_password('password', set_value('password', $password), 'id="password" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
        <div id="error_password" class="error-msg">
            <?php echo $error_msg; ?>
        </div>
    </div>
    <div>
        <?php $error_msg = form_error('password_confirm', '<div>', '</div>'); ?>
        <?php echo form_label(lang('user_profile_password_confirm'), 'password_confirm'); ?>
        <?php echo form_password('password_confirm', set_value('password_confirm', $password_confirm), 'id="password_confirm" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
        <div id="error_password_confirm" class="error-msg">
            <?php echo $error_msg; ?>
        </div>
    </div>
</div>