<h2><?php echo $title; ?></h2>
<div class="admin-messages-item">
    
<?php if(isset($messages) && $messages) : ?>
    
<?php foreach ($messages as $value) { ?>
    <p><?php echo $value; ?></p>
<?php } ?>
    
<?php else : ?>
<?php echo lang('admin_nothing'); ?>
<?php endif; ?>

</div>
