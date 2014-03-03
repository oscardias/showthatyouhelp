<div id="main" class="container updates-list">
    <div id="updates-list-left">
        
        <?php $this->load->view('notification/notifications_list', array('notifications' => $notifications)); ?>

        <?php if($total_pages > 1) : ?>
        <div id="updates-list-load-wrap">
            <?php echo anchor('ajax/display_notifications_list/2', lang('home_load_more'), 'id="updates-list-load" class="gradient-btn updates-list-load-more"'); ?>
            <input type="hidden" value="<?php echo $total_pages; ?>" id="updates-list-load-total-pages"/>

            <div id="updates-list-load-more-pages">
            <?php echo $pagination; ?>
            </div>
        </div>
        <?php endif; ?>        
        
    </div>
    
    <div id="updates-list-right">
        
        <div class="updates-list-profile relative-block">
            
        <?php $this->load->view('profile/small_user', array(
            'username' => $username,
            'image_name' => $image_name,
            'image_ext' => $image_ext,
            'full_name' => $full_name,
            'bio' => $bio,
            'website' => $website
        )); ?>
            
        </div>

        <div class="fixed-block">
            <div class="updates-list-actions">
                <?php echo anchor("home/notifications/$current_page/mark_all_read", lang('notifications_mark_all_read'), 'id="mark-all-read" title="'.lang('notifications_mark_all_read').'" class="gradient-btn"'); ?>
            </div>
        
            <div class="advertisement-block">
                <?php $this->load->view('advertisement/single'); ?>
            </div>
        </div>

        
    </div>
    <div class="clear"></div>
</div>
