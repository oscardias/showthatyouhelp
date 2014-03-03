<div id="main" class="container updates-list updates-profile">
    <div id="updates-list-left">
        
        <div class="updates-list-profile">
        
        <?php $this->load->view('profile/large_complete', array(
            'username' => $username,
            'image_name' => $image_name,
            'image_ext' => $image_ext,
            'full_name' => $full_name,
            'bio' => $bio,
            'website' => $website
        )); ?>
        
        </div>
        
    </div>
    
    <div id="updates-list-right">
        
        <div class="updates-list-view">
            <div class="updates-list-header">
                <h2><?php printf(lang('profile_viewing_title'), $username); ?>
                <?php if($viewing_total) : ?>
                <small>(<?php echo $viewing_total; ?>)</small>
                <?php else : ?>
                <small><?php echo lang('home_no_one'); ?></small>
                <?php endif; ?>
                </h2>
            </div>
            <?php if($viewing) : ?>
            <?php foreach ($viewing as $user) { ?>
            <a href="<?php echo user_profile($user['username']); ?>" title="<?php printf(lang('home_view_profile'), $user['username']); ?>">
                <img src="<?php echo user_profile_image($user['username'], $user['image_name'], $user['image_ext'], 'thumb'); ?>"
                    alt="<?php printf(lang('home_profile_image'), $user['username']); ?>" /></a>
            <?php } ?>
            <a href="<?php echo base_url('p/'.$username.'/viewing'); ?>" title="<?php echo lang('home_view_all'); ?>">
                <img src="<?php echo base_url('images/icon/more.png'); ?>"
                    alt="<?php echo lang('home_view_all'); ?>" /></a>
            <?php endif; ?>
            <div class="clear"></div>
        </div>
        
        <div class="updates-list-view relative-block">
            <div class="updates-list-header">
                <h2><?php printf(lang('profile_viewers_title'), $username); ?>
                <?php if($showing_total) : ?>
                <small>(<?php echo $showing_total; ?>)</small>
                <?php else : ?>
                <small><?php echo lang('home_no_one'); ?></small>
                <?php endif; ?>
                </h2>
            </div>
            <?php if($showing) : ?>
            <?php foreach ($showing as $user) { ?>
            <a href="<?php echo user_profile($user['username']); ?>" title="<?php printf(lang('home_view_profile'), $user['username']); ?>">
                <img src="<?php echo user_profile_image($user['username'], $user['image_name'], $user['image_ext'], 'thumb'); ?>"
                    alt="<?php printf(lang('home_profile_image'), $user['username']); ?>" /></a>
            <?php } ?>
            <a href="<?php echo base_url('p/'.$username.'/viewers'); ?>" title="<?php echo lang('home_view_all'); ?>">
                <img src="<?php echo base_url('images/icon/more.png'); ?>"
                    alt="<?php echo lang('home_view_all'); ?>" /></a>
            <?php endif; ?>
            <div class="clear"></div>
        </div>
        
        <?php if(!isset($is_single)) : ?>
        <div class="advertisement-block fixed-block">
            <?php $this->load->view('advertisement/single'); ?>
        </div>
        <?php endif; ?>
        
    </div>
    
    <div <?php echo (isset($is_single))?'id="entry"':''; ?>>

        <?php if(isset($is_single)) : ?>

            <?php $this->load->view('update/update_single', array('item' => $update)); ?>

        <?php else : ?>

            <div id="updates-list-refresh-wrap">
                <input type="hidden" value="profile" id="updates-list-refresh-type" name="updates-list-refresh-type"/>
                <input type="hidden" value="<?php echo ($updates)?$updates[0]['update_id']:'0'; ?>" id="updates-list-refresh-last" name="updates-list-refresh-last"/>
                <input type="hidden" value="<?php echo ($user_id)?$user_id:''; ?>" id="updates-list-user-id" name="updates-list-user-id"/>
                <input type="hidden" value="<?php echo ($username)?$username:''; ?>" id="updates-list-username" name="updates-list-username"/>
                <input type="hidden" value="<?php echo time(); ?>" id="updates-list-refresh-stamp" name="updates-list-refresh-stamp-diff"/>

                <div id="updates-list-refresh" class="hide">
                </div>
            </div>
            
            <?php $this->load->view('update/updates_list', array('updates' => $updates)); ?>
            
            <?php if($total_pages > 1) : ?>
            <div id="updates-list-load-wrap">
                <?php echo anchor('ajax/display_posts_list/'.$username.'/2', lang('home_load_more'), 'id="updates-list-load" class="gradient-btn updates-list-load-more"'); ?>
                <input type="hidden" value="<?php echo $total_pages; ?>" id="updates-list-load-total-pages"/>

                <div id="updates-list-load-more-pages">
                <?php echo $pagination; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
    
    <div class="clear"></div>

</div>
