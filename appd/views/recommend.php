<div id="main" class="container updates-list">
    <h1 class="page-title"><?php echo lang('user_recommend_title'); ?></h1>
    <div id="updates-list-left">
        <?php if($recommended_users) : ?>
        <?php foreach ($recommended_users as $user) { ?>
        <div id="user-<?php echo $user['username']; ?>" class="updates-list-item">

            <?php $this->load->view('profile/large_complete', $user); ?>

        </div>
        <?php } ?>
        <?php else : ?>
        <div class="updates-list-item">
            <h2><?php echo lang('home_no_users'); ?></h2>
        </div>
        <?php endif; ?>
    </div>
    <div id="updates-list-right">
        <div class="updates-list-profile relative-block">
            
        <?php $this->load->view('profile/small_user', array(
            'username' => $username,
            'image_name' => $image_name,
            'image_ext' => $image_ext,
            'full_name' => $full_name,
            'bio' => $bio,
            'website' => $website
        )); ?>
            
        </div>

        <div class="fixed-block">
            <div class="updates-list-actions">
                <?php echo anchor("home", lang('user_recommend_finished'), 'title="'.lang('user_recommend_finished').'" class="gradient-btn"'); ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>