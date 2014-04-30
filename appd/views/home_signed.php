<div id="main" class="container updates-list">
    <div id="updates-list-left">
        <div class="updates-list-share">
            <div class="update-list-share-form">
            <div class="updates-list-header">
                <h2><?php echo lang('home_share_title'); ?></h2>
            </div>
            <div id="update-list-share-btns">
                <?php echo anchor('home/share/text', '<i class="text-btn"></i>', 'title="'.lang('home_share_text').'" class="share-btn-action share-text'.(($sharing == 'text')?' selected':'').'"'); ?>
                <?php echo anchor('home/share/link', '<i class="url-btn"></i>', 'title="'.lang('home_share_link').'" class="share-btn-action share-link'.(($sharing == 'link')?' selected':'').'"'); ?>
                <?php echo anchor('home/share/video', '<i class="video-btn"></i>', 'title="'.lang('home_share_video').'" class="share-btn-action share-video'.(($sharing == 'video')?' selected':'').'"'); ?>
                <?php echo anchor('home/share/photo', '<i class="photo-btn"></i>', 'title="'.lang('home_share_photo').'" class="share-btn-action share-photo'.(($sharing == 'photo')?' selected':'').'"'); ?>
            </div>
                <?php echo form_open_multipart('home/share', 'id="home-share-form"'); ?>
                <?php echo form_textarea(array('name' => 'comment'), '', 'placeholder="'.lang('home_share_place_text').'" id="home-share-comment"'); ?>
                <div id="update-list-share-add" <?php if($sharing == 'text') echo 'style="display:none"'; ?>>
                <?php if($sharing == 'link') : ?>
                    <div id="update-list-share-add">
                    <?php echo form_input('url', '','id="url" placeholder="'.lang('home_share_place_link').'"'); ?>
                    </div>
                <?php endif; ?>
                <?php if($sharing == 'video') : ?>
                    <div id="update-list-share-add">
                    <?php echo form_input('video', '','id="video" placeholder="'.lang('home_share_place_video').'"'); ?>
                    </div>
                <?php endif; ?>
                <?php if($sharing == 'photo') : ?>
                    <div id="update-list-share-add">
                    <?php echo form_input('photo_styled', '','id="photo-styled" placeholder="'.lang('home_share_place_photo').'"'); ?>
                    <?php echo form_upload('photo', '', 'id="photo"'); ?>
                    </div>
                <?php endif; ?>
                </div>

                <?php echo form_hidden('remote', ''); ?>
                <?php echo form_hidden('title', ''); ?>
                <?php echo form_hidden('description', ''); ?>
                <?php echo form_hidden('image', ''); ?>
                <?php echo form_hidden('icon', ''); ?>
                <?php echo form_hidden('domain', ''); ?>
                <?php echo form_hidden('site_name', ''); ?>
                <?php echo form_hidden('player', ''); ?>

                <?php echo form_hidden('type', $sharing); ?>
                <?php echo form_submit('share', lang('home_share_submit'),'id="submit" class="gradient-btn"'); ?>
                <?php echo form_close(); ?>
                <div class="clear"></div>
                <div id="updates-list-share-answer" <?php if(!isset($error_msg)) echo 'style="display:none;"'; ?>>
                    <?php if(isset($error_msg)) : ?>
                    <p class="error-msg"><?php echo $error_msg; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div id="updates-list-refresh-wrap">
            <input type="hidden" value="home" id="updates-list-refresh-type" name="updates-list-refresh-type"/>
            <input type="hidden" value="<?php echo ($updates)?$updates[0]['update_id']:'0'; ?>" id="updates-list-refresh-last" name="updates-list-refresh-last"/>
            <input type="hidden" value="<?php echo time(); ?>" id="updates-list-refresh-stamp" name="updates-list-refresh-stamp-diff"/>

            <div id="updates-list-refresh" class="hide">
            </div>
        </div>
        
        <?php $this->load->view('update/updates_list', array('updates' => $updates)); ?>

        <?php if($total_pages > 1) : ?>
        <div id="updates-list-load-wrap">
            <?php echo anchor('ajax/display_updates_list/2', lang('home_load_more'), 'id="updates-list-load" class="gradient-btn updates-list-load-more"'); ?>
            <input type="hidden" value="<?php echo $total_pages; ?>" id="updates-list-load-total-pages"/>

            <div id="updates-list-load-more-pages">
            <?php echo $pagination; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
    <div id="updates-list-right">
        
        <div class="relative-block">
            <div class="updates-list-view">
                <div class="updates-list-header">
                    <h2><?php echo lang('home_viewing_title'); ?>
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
                    <i class="view-more"></i></a>
                <?php endif; ?>
                <div class="clear"></div>
            </div>
            
            <?php if($recommended_users) : ?>
            <div class="updates-list-view">
                <div class="updates-list-header">
                    <h2><?php echo lang('home_recommended_title'); ?></h2>
                </div>
                <?php foreach ($recommended_users as $user) { ?>
                <div class="recommended-user">
                    <a href="<?php echo user_profile($user['username']); ?>" title="<?php printf(lang('home_view_profile'), $user['username']); ?>">
                        <img src="<?php echo user_profile_image($user['username'], $user['image_name'], $user['image_ext'], 'thumb'); ?>"
                            alt="<?php printf(lang('home_profile_image'), $user['username']); ?>" /></a>
                    <a href="<?php echo user_profile($user['username']); ?>" title="<?php printf(lang('home_view_profile'), $user['username']); ?>" class="username">
                        <?php echo $user['username']; ?></a>
                </div>
                <?php } ?>
            </div>
            <?php endif; ?>

            <div class="updates-list-view">
                <div class="updates-list-header">
                    <h2><?php echo lang('home_viewers_title'); ?>
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
                    <i class="view-more"></i></a>
                <?php endif; ?>
                <div class="clear"></div>
            </div>

            <?php if($invites) : ?>
            <div class="updates-list-invite">
                <div class="updates-list-header">
                    <h2><?php echo lang('home_invite_title'); ?></h2>
                    <small><?php echo '<span id="updates-list-invite-count">'.$invites.'</span> '.lang(($invites == 1)?'home_invite_left':'home_invites_left'); ?></small>
                </div>
                <div class="updates-list-invite-form">
                    <?php $error = form_error('invite_email', '<p class="error-msg">'); ?>
                    <?php echo form_open('home/invite', 'id="user-invite-form"'); ?>
                    <?php echo form_input('invite_email', set_value('invite_email'), 'placeholder="'.lang('home_invite_text').'"'.(($error)?' class="error_box"':'')); ?>
                    <?php echo form_submit('invite', lang('home_invite_submit'), 'class="gradient-btn"'); ?>
                    <?php echo form_close(); ?>
                    <div id="updates-list-invite-answer" class="updates-list-answer <?php echo (($error)?'':'hide'); ?>">
                        <?php echo form_error('invite_email', '<p class="error-msg">'); ?>
                    </div>
                </div>
            </div>
            <?php endif ?>
        </div>
        
        <div class="fixed-block">
            <div class="advertisement-block">
                <?php $this->load->view('advertisement/single'); ?>
            </div>
        </div>
        
    </div>
    <div class="clear"></div>
</div>
