<?php if($notifications) : ?>
<?php foreach ($notifications as $item) { ?>
<div class="updates-list-item <?php echo ($item['user_notification_read'])?'':'unread'; ?>">

    <?php if($item['user_notification_type'] == 'connect') : ?>
    <?php echo anchor(user_profile($item['username']), '<i class="view-btn"></i>', 'title="'.lang('notifications_view').'" class="entry-btn gradient-btn"'); ?>
    <?php else : ?>
    <?php echo anchor(user_update($item['username'], $item['update_id']), '<i class="view-btn"></i>', 'title="'.lang('notifications_view').'" class="entry-btn gradient-btn"'); ?>
    <?php endif; ?>

<div class="profile-image-thumb">
    <img src="<?php echo user_profile_image($item['username'], $item['image_name'], $item['image_ext'], 'thumb'); ?>"
        alt="<?php printf(lang('notifications_profile_image'), $item['username']); ?>" />
</div>

<p class="notification-username">
    <?php echo anchor(user_profile($item['username']), $item['username']); ?>
    <small>(<?php echo timeSince(strtotime($item['user_notification_created'])); ?></small>)
</p>

<div class="clear"></div>        

<hr class="updates-item-divider"/>

<?php if($item['user_notification_type'] == 'connect') : ?>

    <?php echo anchor(user_profile($item['username']), ($item['full_name'])?$item['full_name']:$item['username']); ?>
    <?php echo lang('notifications_subscribed'); ?>

<?php elseif($item['user_notification_type'] == 'mention') : ?>

    <?php if($item['user_id'] == $this->session->userdata('id')) : ?>
        <?php echo lang('notifications_mentioned_self'); ?>
    <?php else : ?>
        <?php echo anchor(user_profile($item['username']), ($item['full_name'])?$item['full_name']:$item['username']); ?>
        <?php echo lang('notifications_mentioned'); ?>
    <?php endif; ?>

    <?php echo anchor(user_update($item['username'], $item['update_id']), lang('notifications_update')); ?>.

<?php elseif($item['user_notification_type'] == 'reshare') : ?>

    <?php if($item['user_id'] == $this->session->userdata('id')) : ?>
        <?php echo lang('notifications_reshare_self'); ?>
    <?php else : ?>
        <?php echo anchor(user_profile($item['username']),($item['full_name'])?$item['full_name']:$item['username']); ?>
        <?php echo lang('notifications_reshare'); ?>
    <?php endif; ?>
    <?php echo anchor(user_update($item['username'], $item['update_id']), lang('notifications_update')); ?>.

<?php elseif($item['user_notification_type'] == 'comment_mention') : ?>

    <?php if($item['user_id'] == $this->session->userdata('id')) : ?>
        <?php echo lang('notifications_mention_self'); ?>
    <?php else : ?>
        <?php echo anchor(user_profile($item['username']),($item['full_name'])?$item['full_name']:$item['username']); ?>
        <?php echo lang('notifications_mention'); ?>
    <?php endif; ?>
    <?php echo anchor(user_update($item['username'], $item['update_id']), lang('notifications_update')); ?>.

<?php else : ?>

    <?php if($item['user_id'] == $this->session->userdata('id')) : ?>
        <?php echo lang('notifications_comment_self'); ?>
    <?php else : ?>
        <?php echo anchor(user_profile($item['username']), ($item['full_name'])?$item['full_name']:$item['username']); ?>
        <?php echo lang('notifications_comment'); ?>
    <?php endif; ?>

    <?php echo anchor(user_update($item['username'], $item['update_id']), lang('notifications_update')); ?>.

<?php endif; ?>

</div>
<?php } ?>
<?php else : ?>
<div class="updates-list-item">
    <h2><?php echo lang('notifications_no_items'); ?></h2>
</div>
<?php endif; ?>