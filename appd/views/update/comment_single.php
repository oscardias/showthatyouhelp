<div class="update-item-comment <?php echo ($key == 0)?'first':''; ?>">
    <div class="profile-image-thumb">
    <a href="<?php echo user_profile($comment['username']); ?>" title="<?php printf(lang('home_view_profile'), $comment['username']); ?>">
        <img src="<?php echo user_profile_image($comment['username'], $comment['image_name'], $comment['image_ext'], 'thumb'); ?>"
            alt="<?php printf(lang('home_profile_image'), $comment['username']); ?>" /></a>
    </div>
    
    <?php if($this->session->userdata('id') == $comment['user_id']) : ?>
    <?php echo anchor('share/remove_comment/'.$comment['update_comment_id'], icon_img_tag('remove.png'), 'title="'.lang('home_comment_remove').'" class="update-comment-remove comment-remove-action"'); ?>
    <?php endif; ?>
    
    <?php echo anchor(user_profile($comment['username']), $comment['username']); ?> (<small><?php echo timeSince(strtotime($comment['update_comment_created'])); ?></small>):
    <?php echo $comment['update_comment_content']; ?>
    <div class="clear"></div>
</div>
