<div id="main" class="container sign-up-page">
    <h1><?php echo lang('user_signup_title'); ?></h1>
    <?php
    echo form_open_multipart('sign/twitter', 'id="sign-up-form"');
    ?>

    <p><?php echo lang('user_twitter_signup_text'); ?></p>
    
    <fieldset>
        <legend><?php echo lang('user_signup_account'); ?></legend>
        <table>
            <tr>
                <?php $error_msg = form_error('email', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_email'), 'email'); ?>
                </td>
                <td class="input">
                    <?php echo form_input('email', set_value('email'), 'id="email" maxlength="255" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>

                    <span class="form-hint form-required" title="<?php echo lang('user_signup_required'); ?>">*</span>
                </td>
                <td>
                    <div id="error_email" class="error-msg">
                        <?php echo $error_msg; ?>
                        <?php if($error_msg) {
                            echo lang('user_email_exists_twitter');
                        } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('username', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_username'), 'username'); ?>
                </td>
                <td class="input">
                    <?php echo form_input('username', set_value('username', $username), 'id="username" maxlength="22" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>

                    <span class="form-hint form-required" title="<?php echo lang('user_signup_required'); ?>">*</span>
                    <span class="form-hint form-length" title="<?php printf(lang('user_signup_length'), '3') ?>">&ge;3</span>
                </td>
                <td>
                    <div id="error_username" class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <div class="form-buttons">
    <?php
    echo form_hidden('full_name', $full_name);
    echo form_hidden('bio', $bio);
    echo form_hidden('website', $website);
    echo form_hidden('picture', $picture);
    
    echo form_submit('register', lang('user_signup_register'),'class="gradient-btn"');
    echo form_reset('reset', lang('user_signup_reset'),'class="gradient-btn"');
    echo form_button('cancel', lang('user_signup_cancel'), 'class="gradient-btn" onClick="window.location=\''.base_url().'\';"');
    echo form_close();
    ?>
    </div>

</div>