<div class="settings-form-page settings-email">
    <div>
        <?php $error_msg = form_error('email', '<div>', '</div>'); ?>
        <?php echo form_label(lang('user_profile_email'), 'email'); ?>
        <?php echo form_input('email', set_value('email', $email), 'id="email" maxlength="255" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
        <div id="error_email" class="error-msg">
            <?php echo $error_msg; ?>
        </div>
    </div>
</div>
