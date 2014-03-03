<div class="settings-form-page settings-remove">
    <?php if($remove) : ?>
        <p class="settings-description"><strong><?php echo lang('user_profile_remove_sure'); ?></strong> <?php echo lang('user_profile_remove_undone'); ?></p>
        <div class="form-buttons">
            <?php echo anchor('user/remove/confirm', lang('user_profile_remove_confirm'), 'id="remove-account" class="gradient-btn"'); ?>
        </div>
    <?php else : ?>
        <p class="settings-description"><?php echo lang('user_profile_remove_notice'); ?></p>
        <div class="form-buttons">
            <?php echo anchor('user/remove', lang('user_profile_remove'), 'id="remove-account" class="gradient-btn"'); ?>
        </div>
    <?php endif; ?>
</div>
