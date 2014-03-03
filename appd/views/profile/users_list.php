    <?php if($users) : ?>
    <?php foreach ($users as $item) { ?>
    <div class="updates-list-item">
        
        <?php $this->load->view('profile/small_complete', array(
            'username' => $item['username'],
            'image_name' => $item['image_name'],
            'image_ext' => $item['image_ext'],
            'full_name' => $item['full_name'],
            'bio' => $item['bio'],
            'website' => $item['website'],
            'user_show' => $item['user_show']
        )); ?>
    
    </div>
    <?php } ?>
    <?php else : ?>
    <div class="updates-list-item">
        <h2><?php echo lang('user_no_users'); ?></h2>
    </div>
    <?php endif; ?>
    <div class="clear"></div>