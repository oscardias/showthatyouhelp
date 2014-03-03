<?php if($item) : ?>
<div class="update-single-entry">

    <?php $this->load->view('update/single', array('is_single' => true,'item' => $item)); ?>
    
</div>
<?php else : ?>
<div class="update-single-entry">
    <h2><?php printf(lang('home_update_not_found'), user_profile($username)); ?></h2>
</div>
<?php endif; ?>
<div class="clear"></div>