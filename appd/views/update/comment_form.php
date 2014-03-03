<div class="updates-item-comment-form">
    <div class="updates-item-comment-form-answer" style="display:none;">
    </div>
    <?php echo form_open("share/comment", array('id' => 'comment_form')); ?>
    <?php echo form_textarea(array('name' => 'content'), '', 'placeholder="'.lang('home_comment_placeholder').'" '.(!isset($list)?' id="comment-textarea"':'')); ?>
    <?php echo form_hidden('update_id', $update_id); ?>
    <?php echo form_submit('comment', lang('home_comment_submit'),'id="comment" class="comment-submit gradient-btn"'); ?>
    <?php echo form_close(); ?>
</div>