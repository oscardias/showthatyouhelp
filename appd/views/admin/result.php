<div id="main" class="container">
    <small>
    <?php echo anchor(base_url('admin'), lang('admin_header')); ?> > <?php echo $seo_title; ?>
    </small>
    <h1><?php echo $seo_title; ?></h1>
    
    <?php if(isset($execution_list)) : ?>
    <script type="text/javascript">
    $(window).load(function(){
        var admin_messages = $('#admin-messages');
        var loading = '<div class="admin-messages-item"><div class="loading"></div></div>';
        
    <?php foreach ($execution_list as $value) { ?>
            
        $.ajaxQueue({
            type: 'GET',
            url: '<?php echo $value; ?>',
            beforeSend: function(){
                admin_messages.append(loading);
            },
            success: function(data){
                admin_messages.children().last().remove();
                admin_messages.append(data);
            }
        });
        
    <?php } ?>
    
    });
    </script>
        
    <div id="admin-messages">
    </div>
        
    <?php endif; ?>

</div>