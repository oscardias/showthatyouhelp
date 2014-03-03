<?php echo anchor($item['link'], $item['title'], 'target="_blank"'); ?>
<div class="updates-list-item-link-info">
    <?php if($item['filename']) : ?>
    <img src="<?php echo base_url().$item['filename']; ?>" title="<?php echo $item['title']; ?>" />
    <?php endif; ?>
    
    <p class="link-description"><?php echo $item['description'].((strlen($item['description']) >= 255)?'...':''); ?></p>
    <small style="background-image: url(<?php echo site_favicon($item['domain'], $item['icon']); ?>)">

        <?php echo anchor('http://'.$item['domain'].'/', $item['name'], 'target="_blank"'); ?>
    </small>
</div>
<div class="clear"></div>