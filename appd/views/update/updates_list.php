<?php if($updates) : ?>
<?php foreach ($updates as $item) { ?>
<div id="update-<?php echo $item['update_id']; ?>" class="updates-list-item">

    <?php $this->load->view('update/single', array('item' => $item)); ?>
    
</div>
<?php } ?>
<?php else : ?>
<div class="updates-list-item">
    <h2><?php echo lang('home_no_updates'); ?></h2>
</div>
<?php endif; ?>