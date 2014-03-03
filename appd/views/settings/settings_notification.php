<div class="settings-form-page settings-notification">
    <p class="settings-description"><?php echo lang('user_profile_notification_text'); ?></p>
    <table>
        <tr>
            <th><?php echo lang('user_profile_notification_header'); ?></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <td>
                <p class="label"><?php echo lang('user_profile_notification_connect'); ?></p>
                <small><?php echo lang('user_profile_notification_connect_info'); ?></small>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_yes'); ?>
                    <?php echo form_radio('notify_connect', '1', set_radio('notify_connect', '1', ($notify_connect == 1))); ?>
                </nobr>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_no'); ?>
                    <?php echo form_radio('notify_connect', '0', set_radio('notify_connect', '0', ($notify_connect == 0))); ?>
                </nobr>
            </td>
            <td>
                <?php $error_msg = form_error('notify_connect', '<div>', '</div>'); ?>
                <div id="error_notification_connect" class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <p class="label"><?php echo lang('user_profile_notification_mention'); ?></p>
                <small><?php echo lang('user_profile_notification_mention_info'); ?></small>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_yes'); ?>
                    <?php echo form_radio('notify_mention', '1', set_radio('notify_mention', '1', ($notify_mention == 1))); ?>
                </nobr>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_no'); ?>
                    <?php echo form_radio('notify_mention', '0', set_radio('notify_mention', '0', ($notify_mention == 0))); ?>
                </nobr>
            </td>
            <td>
                <?php $error_msg = form_error('notify_mention', '<div>', '</div>'); ?>
                <div id="error_notification_mention" class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <p class="label"><?php echo lang('user_profile_notification_comment'); ?></p>
                <small><?php echo lang('user_profile_notification_comment_info'); ?></small>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_yes'); ?>
                    <?php echo form_radio('notify_comment', '1', set_radio('notify_comment', '1', ($notify_comment == 1))); ?>
                </nobr>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_no'); ?>
                    <?php echo form_radio('notify_comment', '0', set_radio('notify_comment', '0', ($notify_comment == 0))); ?>
                </nobr>
            </td>
            <td>
                <?php $error_msg = form_error('notify_comment', '<div>', '</div>'); ?>
                <div id="error_notification_comment" class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <p class="label"><?php echo lang('user_profile_notification_reshare'); ?></p>
                <small><?php echo lang('user_profile_notification_reshare_info'); ?></small>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_yes'); ?>
                    <?php echo form_radio('notify_reshare', '1', set_radio('notify_reshare', '1', ($notify_reshare == 1))); ?>
                </nobr>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_no'); ?>
                    <?php echo form_radio('notify_reshare', '0', set_radio('notify_reshare', '0', ($notify_reshare == 0))); ?>
                </nobr>
            </td>
            <td>
                <?php $error_msg = form_error('notify_reshare', '<div>', '</div>'); ?>
                <div id="error_notification_reshare" class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <p class="label"><?php echo lang('user_profile_notification_pending'); ?></p>
                <small><?php echo lang('user_profile_notification_pending_info'); ?></small>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_yes'); ?>
                    <?php echo form_radio('notify_pending', '1', set_radio('notify_pending', '1', ($notify_pending == 1))); ?>
                </nobr>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_no'); ?>
                    <?php echo form_radio('notify_pending', '0', set_radio('notify_pending', '0', ($notify_pending == 0))); ?>
                </nobr>
            </td>
            <td>
                <?php $error_msg = form_error('notify_pending', '<div>', '</div>'); ?>
                <div id="error_notification_pending" class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <p class="label"><?php echo lang('user_profile_notification_other'); ?></p>
                <small><?php echo lang('user_profile_notification_other_info'); ?></small>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_yes'); ?>
                    <?php echo form_radio('notify_other', '1', set_radio('notify_other', '1', ($notify_other == 1))); ?>
                </nobr>
            </td>
            <td class="radio">
                <nobr>
                    <?php echo lang('user_profile_notification_no'); ?>
                    <?php echo form_radio('notify_other', '0', set_radio('notify_other', '0', ($notify_other == 0))); ?>
                </nobr>
            </td>
            <td>
                <?php $error_msg = form_error('notify_other', '<div>', '</div>'); ?>
                <div id="error_notification_other" class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            </td>
        </tr>
    </table>
</div>
