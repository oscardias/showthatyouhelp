<div id="main" class="container about-page">
    <div class="about-menu">
        <?php echo anchor('about', lang('about_title'), 'class="'.(($active == 'about')?'selected':'').'"'); ?>
        <?php //TODO: echo anchor('about/images', 'Featured Images', 'class="'.(($active == 'images')?'selected':'').'"'); ?>
        <?php echo anchor('about/terms', lang('about_terms_title'), 'class="'.(($active == 'terms')?'selected':'').'"'); ?>
        <?php echo anchor('about/privacy', lang('about_privacy_title'), 'class="'.(($active == 'privacy')?'selected':'').'"'); ?>
        <?php echo anchor('about/contact', lang('about_contact_title'), 'class="'.(($active == 'contact')?'selected':'').'"'); ?>
    </div>
    
    <div class="about-body">
        <?php $this->load->view('about/' . $language . '/' . $active); ?>
    </div>
    
    <div class="clear"></div>
    
</div>