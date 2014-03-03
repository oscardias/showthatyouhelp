<div class="profile-image-small">
<a href="<?php echo user_profile($item['username']); ?>" title="<?php printf(lang('home_view_profile'), $item['username']); ?>">
    <img src="<?php echo user_profile_image($item['username'], $item['image_name'], $item['image_ext'], 'small'); ?>"
        alt="<?php printf(lang('home_profile_image'), $item['username']); ?>" /></a>
</div>
<h2><?php echo anchor(user_profile($item['username']), $item['username']); ?></h2>
<small class="time-since" stamp="<?php echo strtotime($item['update_created']); ?>"><?php echo timeSince(strtotime($item['update_created'])); ?></small>

<?php if($item['reshare_username']) : ?>
<small>
    -
    <?php echo anchor(user_update($item['reshare_username'], $item['update_reshare_update_id']), lang('home_update_previous')); ?>
    <?php echo lang('home_update_by'); ?>
    <?php echo anchor(user_profile($item['reshare_username']), '@'.$item['reshare_username']); ?>
</small>
<?php endif; ?>

<div class="clear"></div>
<?php if($item['comment']) : ?>

    <hr class="updates-item-divider" />

    <?php if(isset($is_single)) : ?>
    <p><?php echo $item['comment']; ?></p>
    <?php else : ?>
    <p><?php echo character_limiter($item['comment'], 500, '...<br />'.anchor(user_update($item['username'], $item['update_id']), lang('home_update_read_more'))); ?></p>
    <?php endif; ?>

<?php endif; ?>

<?php if($item['type'] != 'text') : ?>
<hr class="updates-item-divider" />
<?php endif; ?>

<?php if($item['type'] == 'link') : ?>
<?php //print_r($item); ?>
<div class="updates-list-item-link">
    <?php $this->load->view('update/link', array('item' => $item)); ?>
</div>
<?php endif; ?>

<?php if($item['type'] == 'video') : ?>
<div class="updates-list-item-video <?php echo (!isset($is_popup))?'':'popup'; ?>">
    <?php if($item['original']) : ?>
        <?php if(preg_match('#^http:\/\/(.*)\.(gif|png|jpg|jpeg)$#i', $item['original'])) : ?>
        <p><?php echo anchor($item['link'], $item['title']); ?></p>
        <a href="<?php echo $item['link']; ?>" title="<?php echo $item['title']; ?>">
            <img src="<?php echo $item['original']; ?>" alt="<?php echo $item['title']; ?>" /></a>
        <?php else : ?>
        
            <?php if(isset($is_single)) : ?>
            <iframe width="880" height="495" src="<?php echo $item['original']; ?>" frameborder="0" allowfullscreen></iframe>
            <?php elseif(isset($is_popup)) : ?>
            <iframe width="340" height="190" src="<?php echo $item['original']; ?>" frameborder="0" allowfullscreen></iframe>
            <?php else : ?>
            <iframe width="580" height="330" src="<?php echo $item['original']; ?>" frameborder="0" allowfullscreen></iframe>
            <?php endif; ?>
        
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php if($item['type'] == 'photo') : ?>
<div class="updates-list-item-photo <?php echo (!isset($is_popup))?'':'popup'; ?>">
    <?php if(isset($is_single)) : ?>
    <a href="<?php echo image_url($item); ?>" target="_blank">
        <img src="<?php echo image_url($item, 'large'); ?>" alt="<?php echo lang('home_update_users_photo'); ?>" /></a>
    <?php else : ?>
    <a href="<?php echo user_update($item['username'], $item['update_id']); ?>">
        <img src="<?php echo image_url($item, 'small'); ?>" alt="<?php echo lang('home_update_users_photo'); ?>" /></a>
    <?php endif; ?>
</div>
<?php endif; ?>
