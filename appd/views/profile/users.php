<div id="main" class="container updates-list">
    
    <div id="updates-list-left">
        
        <?php $this->load->view('profile/users_list', array('users' => $users)); ?>

        <?php if($total_pages > 1) : ?>
        <div id="updates-list-load-wrap">
            <?php echo anchor('ajax/display_users_list/'.$type.'/'.$username.'/2', lang('home_load_more'), 'id="updates-list-load" class="gradient-btn updates-list-load-more"'); ?>
            <input type="hidden" value="<?php echo $total_pages; ?>" id="updates-list-load-total-pages"/>

            <div id="updates-list-load-more-pages">
            <?php echo $pagination; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
    <div id="updates-list-right">
        
        <div class="updates-list-profile relative-block">
            
        <?php $this->load->view('profile/small_complete', array(
            'username' => $username,
            'image_name' => $image_name,
            'image_ext' => $image_ext,
            'full_name' => $full_name,
            'bio' => $bio,
            'website' => $website
        )); ?>
            
        </div>

        <div class="advertisement-block fixed-block">
            <?php $this->load->view('advertisement/single'); ?>
        </div>
        
    </div>
    
    <div class="clear"></div>
</div>