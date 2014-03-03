<?php if($this->session->userdata('logged_in') && $item['username'] != $this->session->userdata('username')) : ?>
    <?php if(isset($item['user_show'])) : ?>
        <?php if($item['user_show']) : ?>
        <?php echo anchor("user/disconnect/{$item['username']}", '<i class="disconnect-btn"></i>', 'title="'.sprintf(lang('home_update_disconnect_long'), $item['username']).'" class="entry-btn gradient-btn gradient-red disconnect-action connect-'.$item['username'].'"'); ?>
        <?php else : ?>
        <?php echo anchor("user/connect/{$item['username']}", '<i class="connect-btn"></i>', 'title="'.sprintf(lang('home_update_connect_long'), $item['username']).'" class="entry-btn gradient-btn connect-action connect-'.$item['username'].'"'); ?>
        <?php endif; ?>
    <?php elseif(isset($viewing_current)) : ?>
        <?php if($viewing_current) : ?>
        <?php echo anchor("user/disconnect/{$item['username']}", '<i class="disconnect-btn"></i>', 'title="'.sprintf(lang('home_update_disconnect_long'), $item['username']).'" class="entry-btn gradient-btn gradient-red disconnect-action connect-'.$item['username'].'"'); ?>
        <?php else : ?>
        <?php echo anchor("user/connect/{$item['username']}", '<i class="connect-btn"></i>', 'title="'.sprintf(lang('home_update_connect_long'), $item['username']).'" class="entry-btn gradient-btn connect-action connect-'.$item['username'].'"'); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php else : ?>
    <?php echo anchor(base_url('home/remove/'.$item['update_id']), '<i class="remove-btn"></i>', 'title="'.lang('home_update_remove').'" class="entry-btn gradient-btn gradient-red remove-update-action"'); ?>
<?php endif; ?>

<?php
    // Main information about the update is encapsulated in this view
    $this->load->view('update/single_info');
?>

<hr id="comments" class="updates-item-divider" />

<?php if(!isset($is_single)) : ?>
    <?php echo viewComments($item); ?>
<?php endif; ?>

<?php $this->load->view('update/share', array('username' => $item['username'], 'update_id' => $item['update_id'], 'comment' => $item['comment'])); ?>
     
<?php if(!isset($is_single)) : ?>
    <?php if($this->session->userdata('logged_in')) : ?>
        <?php echo anchor(user_update($item['username'], $item['update_id']), '<i class="comment-btn"></i>', 'title="'.lang('home_comment').'" class="entry-btn gradient-btn comment-btn-click"'); ?>
    <?php endif; ?>

    <?php echo anchor(user_update($item['username'], $item['update_id']), '<i class="view-btn"></i>', 'title="'.lang('home_view').'" class="entry-btn gradient-btn"'); ?>
<?php endif; ?>

<div class="clear"></div>

<?php if(isset($is_single) && $item['update_comment_count']) : ?>
<?php $this->load->view('update/comment_list', array('comments' => $comments)); ?>
<?php endif; ?>

<?php if(isset($is_single) && $this->session->userdata('logged_in')) : ?>
<?php $this->load->view('update/comment_form', array('update_id' => $item['update_id'])); ?>
<?php endif; ?>
