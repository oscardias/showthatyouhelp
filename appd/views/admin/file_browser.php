<div id="main" class="container">
    <small>
    <?php echo anchor(base_url('admin'), lang('admin_header')); ?> > <?php echo lang('admin_file_browser'); ?>
    </small>
    <h1><?php echo lang('admin_file_browser'); ?></h1>
    <h2><?php echo $virtual_root.'/'.$path_in_url ?></h2>
    <?php
        $prefix = $controller.'/'.$method.'/'.$path_in_url;
        if (!empty($dirs)) foreach( $dirs as $dir )
            echo '/'.anchor($prefix.$dir['name'], $dir['name']).'<br>';

        if (!empty($files)) foreach( $files as $file )
            echo anchor($prefix.$file['name'], $file['name']).'<br>';
    ?>
</div>