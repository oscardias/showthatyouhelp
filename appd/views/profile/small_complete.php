<?php if($this->session->userdata('logged_in')) : ?>
    <?php if($username != $this->session->userdata('username')) : ?>
        <?php if($user_show) : ?>
        <?php echo anchor("user/disconnect/$username", '<i class="disconnect-btn"></i>', 'title="'.sprintf(lang('home_update_disconnect_long'), $username).'" class="profile-btn gradient-btn gradient-red disconnect-action connect-'.$username.'"'); ?>
        <?php else : ?>
        <?php echo anchor("user/connect/$username", '<i class="connect-btn"></i>', 'title="'.sprintf(lang('home_update_connect_long'), $username).'" class="profile-btn gradient-btn connect-action connect-'.$username.'"'); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<div class="profile-image-small">
    <a href="<?php echo user_profile($username); ?>" title="<?php printf(lang('home_view_profile'), $username); ?>">
        <img src="<?php echo user_profile_image($username, $image_name, $image_ext, 'small'); ?>"
            alt="<?php printf(lang('home_profile_image'), $username); ?>" /></a>
</div>

<h2><?php echo anchor(user_profile($username), ($full_name)?$full_name:$username); ?></h2>
<?php if($full_name) : ?>
<h3 class="profile-username"><?php echo anchor(user_profile($username), $username); ?></h3>
<?php endif; ?>

<?php if($bio) : ?>
<p class="profile-bio"><?php echo prepare_text($bio); ?></p>
<?php endif; ?>

<?php if($website) : ?>
<p class="profile-website"><?php echo anchor($website, $website, 'target="_blank"'); ?></p>
<?php endif; ?>

<div class="clear"></div>
