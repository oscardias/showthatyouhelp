<div id="main" class="landing">
    <div class="landing-wrap">
        <div class="landing-image" style="background-image:url(<?php echo base_url(); ?>images/landing/oscardias-01.jpg);">
            <div class="landing-logo">
            </div>
            <div class="landing-image-credits">
                <?php echo lang('home_image_by'); ?> <?php echo anchor(base_url('p/oscardias'), "@oscardias"); ?>
                <?php
                /* TODO - Task #44
                <a href="<?php echo base_url('about/images'); ?>" title="Featured Images"><img src="<?php echo base_url(); ?>images/icon/question.png"
                        alt="Featured images" /></a>
                 */
                ?>
            </div>
        </div>
        <div class="landing-info">
            <p><?php echo lang('home_description_1'); ?></p>
            <div id="sign-in-wrap">
                <?php if(!isset($recover_password)) : ?>
                
                <?php echo form_open("sign/in/".((isset($token))?$token:'')); ?>
                <?php echo form_input('username', $username, 'placeholder="'.lang('home_username').'" '.(($invalid)?'class="error_box"':'')); ?>
                <?php echo form_password('password', $password, 'placeholder="'.lang('home_password').'" '.(($invalid)?'class="error_box"':'')); ?>
                <?php echo form_hidden('redirect_url', ((isset($redirect_url))?$redirect_url:base_url())); ?>
                <?php echo form_submit('signin', lang('home_sign_in'), 'class="gradient-btn"'); ?>
                <?php echo form_close(); ?>
                                
                <?php if(!isset($password_sent)) : ?>
                    <?php echo anchor("sign/recover", lang('home_recover_password')); ?>
                <?php else : ?>
                    <?php echo lang('home_recover_answer'); ?>
                <?php endif; ?>
                
                <?php else : ?>
                
                <?php echo form_open("sign/recover"); ?>
                <?php echo form_input('username', $username, 'placeholder="'.lang('home_username').'" '.(($invalid)?'class="error_box"':'')); ?>
                <?php echo form_submit('recover', lang('home_recover_get_password'), 'class="gradient-btn"'); ?>
                <?php echo form_close(); ?>
                
                <?php endif; ?>
                
                <div class="clear"></div>
                <hr class="sign-divider"/>
                
                <div class="sign-up-external">
                    <?php echo lang('general_text_or'); ?>
                    <?php echo anchor("sign/twitter_sign", '<i class="twitter-btn"></i> '.lang('home_twitter_sign'), 'class="gradient-btn"'); ?>
                </div>
            </div>
            <div class="sign-up-wrap">
                <?php echo lang('home_new_user'); ?>
                <?php echo anchor("sign/up", lang('home_sign_up'), 'class="gradient-btn"'); ?>
            </div>
        </div>
    </div>
</div>