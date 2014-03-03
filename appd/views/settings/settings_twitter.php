<div class="settings-form-page settings-twitter">
    <?php if($oauth) : ?>
        <p class="settings-description"><?php printf(lang('user_profile_twitter_connected'), $oauth['username']); ?></p>
        <div class="sign-up-external">
            <?php echo anchor("user/remove_twitter", '<i class="twitter-btn"></i> '.lang('home_twitter_remove'), 'class="gradient-btn"'); ?>
        </div>
    <?php else : ?>
        <p class="settings-description"><?php echo lang('user_profile_twitter_connect'); ?></p>
        <div class="sign-up-external">
            <?php echo anchor("sign/twitter_sign", '<i class="twitter-btn"></i> '.lang('home_twitter_sign'), 'class="gradient-btn"'); ?>
        </div>
    <?php endif; ?>
</div>
