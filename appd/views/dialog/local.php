<div <?php echo (!isset($is_popup))?' id="main" class="container"':''; ?>>
    <div id="update-share-form">
        <?php echo form_open("share/local/".$item['update_id']); ?>
        <?php echo form_textarea(array('name' => 'comment'), 
                set_value('comment', $item['reshare_comment']), 
                'placeholder="'.lang('home_share_place_text').'" class="'.((isset($is_popup))?'popup-screen':'full-screen').'"'); ?>

        <?php if(!isset($is_popup)) : ?>
        <?php echo form_submit('share', lang('home_share_submit'),'id="submit" class="gradient-btn"'); ?>
        <?php endif; ?>

        <?php echo form_close(); ?>
    </div>
    
    <div id="updates-list-share-answer" <?php if(!$error) echo 'style="display:none"'; ?>>
        <?php echo $error; ?>
    </div>
    
    <div id="update-<?php echo $item['update_id']; ?>" class="update-single-entry <?php echo (!isset($is_popup))?'':'popup'; ?>">
        <?php $this->load->view('update/single_info'); ?>
    </div>
    
    <div class="clear"></div>
    
</div>