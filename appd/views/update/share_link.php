<?php if($type == 'video') : ?>
    <iframe width="246" height="140" src="<?php echo $player; ?>" frameborder="0" allowfullscreen></iframe>
<?php else : ?>
    <?php echo anchor($link, $title); ?>
    <div class="share-url-wrap">
        <div class="share-images">
            <div class="share-images-list">
            <?php foreach ($images as $key => $value) { ?>
                <img class="share-image single-image-<?php echo $key; ?> <?php echo ($key > 0)?'hide':'selected' ?>" src="<?php echo $value; ?>" />
            <?php } ?>
            </div>
            <?php if(count($images) > 1) : ?>
            <div class="share-images-count">
                <span id="share-images-left"></span>
                <span id="share-images-counter">1 / <?php echo count($images); ?></span>
                <span id="share-images-right"></span>
            </div>
            <?php endif; ?>
        </div>
        <p class="link-description"><?php echo substr($description, 0, 255).((strlen($description) > 255)?'...':''); ?></p>
        <small style="background-image: url(<?php echo $icon; ?>)">
            <?php echo anchor('http://'.$domain.'/', ($name)?$name:$domain); ?>
        </small>
    </div>
<?php endif; ?>
<div class="clear"></div>