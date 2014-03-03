<div id="main" class="container sign-up-page">
    <h1><?php echo lang('user_signup_title'); ?></h1>
    <?php
    echo form_open_multipart('sign/up', 'id="sign-up-form"');
    ?>

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
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('username', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_username'), 'username'); ?>
                </td>
                <td class="input">
                    <?php echo form_input('username', set_value('username'), 'id="username" maxlength="22" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>

                    <span class="form-hint form-required" title="<?php echo lang('user_signup_required'); ?>">*</span>
                    <span class="form-hint form-length" title="<?php printf(lang('user_signup_length'), '3') ?>">&ge;3</span>
                </td>
                <td>
                    <div id="error_username" class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('password', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_password'), 'password'); ?>
                </td>
                <td class="input">
                    <?php echo form_password('password', set_value('password'), 'id="password" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>

                    <span class="form-hint form-required" title="<?php echo lang('user_signup_required'); ?>">*</span>
                    <span class="form-hint form-length" title="<?php printf(lang('user_signup_length'), '6') ?>">&ge;6</span>
                </td>
                <td>
                    <div id="error_password" class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('password_confirm', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_password_confirm'), 'password_confirm'); ?>

                </td>
                <td class="input">
                    <?php echo form_password('password_confirm', set_value('password_confirm'), 'id="password_confirm" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>

                    <span class="form-hint form-required" title="<?php echo lang('user_signup_required'); ?>">*</span>
                </td>
                <td>
                    <div id="error_password_confirm" class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend><?php echo lang('user_signup_personal'); ?></legend>
        <table>
            <tr>
                <?php $error_msg = form_error('full_name', '<div>', '</div>'); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_name'), 'full_name'); ?>
                </td>
                <td class="input">
                    <?php echo form_input('full_name', set_value('full_name'), 'id="full_name" maxlength="255" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
                </td>
                <td>
                    <div id="error_full_name" class="error-msg">
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
                    <?php echo form_textarea(array('name' => 'bio','cols' => '25', 'rows' => '3'), set_value('bio'), 'id="bio"'.(($error_msg)?' class="error_box"':'')); ?>
                    <small><?php echo lang('user_profile_bio_size'); ?> <span id="bio-length-remaining">255</span></small>
                </td>
                <td>
                    <div id="error_bio" class="error-msg">
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
                    <?php echo form_input('website', set_value('website'), 'id="website" maxlength="255" class="validate-focusout '.(($error_msg)?'error_box':'').'"'); ?>
                </td>
                <td>
                    <div id="error_website" class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php $error_msg = form_error('picture', '<div>', '</div>').(isset($upload_error)?'<div>'.$upload_error.'</div>':''); ?>
                <td class="label">
                    <?php echo form_label(lang('user_profile_set_picture'), 'picture'); ?>
                </td>
                <td class="input">
                    <?php echo form_upload('picture', set_value('picture'), 'id="picture"'); ?>
                </td>
                <td>
                    <div id="error_picture" class="error-msg">
                        <?php echo $error_msg; ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <div class="form-buttons">
    <?php
    if(isset($token))
        echo form_hidden('token', $token);
    else
        echo form_hidden('token', '');
    
    echo form_submit('register', lang('user_signup_register'),'class="gradient-btn"');
    echo form_reset('reset', lang('user_signup_reset'),'class="gradient-btn"');
    echo form_button('cancel', lang('user_signup_cancel'), 'class="gradient-btn" onClick="window.location=\''.base_url().'\';"');
    echo form_close();
    ?>
    </div>

</div>