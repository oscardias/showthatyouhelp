<div id="main" class="container">
    <h1><?php echo lang('admin_header'); ?></h1>
    
    <div>
        <p><?php echo anchor(base_url('admin/clean_database'), lang('admin_clean_database'), 'onclick="return confirm(\''.lang('admin_confirm').'\');"'); ?></p>
        <p><?php echo anchor(base_url('admin/generate_sitemap'), lang('admin_generate_sitemap')); ?></p>
        <p><?php echo anchor(base_url('admin/file_browser'), lang('admin_file_browser')); ?></p>
        <p><?php echo anchor(base_url('admin/data_browser'), lang('admin_data_browser')); ?></p>
    </div>

</div>