<div id="notifications-window">
<?php if($notifications) : ?>
    <div class="list-items-wrap">
    <?php foreach ($notifications as $item) { ?>
        <div class="list-item <?php echo ($item['user_notification_read'])?'':'unread'; ?>">

        <?php if($item['user_notification_type'] == 'connect') : ?>

            <?php echo anchor(user_profile($item['username']), ($item['full_name'])?$item['full_name']:$item['username']); ?>
            <?php echo lang('notifications_subscribed'); ?>

        <?php elseif($item['user_notification_type'] == 'mention') : ?>

            <?php if($item['user_id'] == $this->session->userdata('id')) : ?>
                <?php echo lang('notifications_mentioned_self'); ?>
            <?php else : ?>
                <?php echo anchor(user_profile($item['username']),($item['full_name'])?$item['full_name']:$item['username']); ?>
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
    </div>
    <?php echo anchor('home/notifications', lang('notifications_view_all'), 'class="gradient-btn notifications-view-all"'); ?>
<?php else : ?>
    <div class="list-item">
        <?php echo lang('notifications_no_items'); ?>
    </div>
<?php endif; ?>
</div>