<div class="profile-image-medium">
    <img src="<?php echo user_profile_image($username, $image_name, $image_ext, 'medium'); ?>"
        alt="<?php printf(lang('home_profile_image'), $username); ?>" />
</div>

<h1><?php echo ($full_name)?$full_name:$username; ?></h1>
<?php if($full_name) : ?>
<h2 class="profile-username"><?php echo $username; ?></h2>
<?php endif; ?>

<?php echo anchor(user_profile($username), lang('home_view_public_profile'), 'title="View your public profile" class="profile-btn gradient-btn"'); ?>

<div class="clear"></div>