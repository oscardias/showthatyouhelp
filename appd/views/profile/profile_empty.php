<div id="main" class="container updates-list updates-profile">
    <div id="updates-list-left">
        
        <div class="updates-list-profile">
        
        <?php $this->load->view('profile/large_complete', array(
            'username' => $username,
            'image_name' => '',
            'image_ext' => '',
            'full_name' => lang('profile_empty_full_name'),
            'bio' => sprintf(lang('profile_empty_bio'), base_url('p/showthatyouhelp')),
            'website' => ''
        )); ?>
        
        </div>
        
    </div>
    
    <div id="updates-list-right">
        
        <div class="advertisement-block fixed-block">
            <?php $this->load->view('advertisement/single'); ?>
        </div>
        
    </div>
    
    <div class="clear"></div>

</div>
