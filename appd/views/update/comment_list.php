<div class="updates-item-comments-wrap">
    <?php foreach($comments as $key => $comment) { ?>
    <?php $this->load->view('update/comment_single', array('key' => $key, 'comment' => $comment)); ?>
    <?php } ?>
</div>
