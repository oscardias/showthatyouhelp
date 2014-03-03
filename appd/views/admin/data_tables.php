<div id="main" class="container">
    <small>
        <?php echo anchor(base_url('admin'), lang('admin_header')); ?> >
        <?php echo anchor(base_url('admin/data_browser'), lang('admin_data_browser')); ?>
        <?php echo ((isset($table))?'> '.$table:''); ?>
    </small>
    <h1><?php echo lang('admin_data_browser'); ?></h1>
    <?php if(isset($tables)) : ?>
    
    <h2><?php echo lang('admin_data_browser_tables'); ?></h2>
    <?php foreach ($tables as $table) { ?>
    <?php echo anchor('admin/data_browser/'.$table['Name'], $table['Name']); ?><br/>
    <?php } ?>
    
    <?php else : ?>
    
    <h2><?php echo $table; ?></h2>
    
    <div style="overflow-x: auto;width:920px">
        <table style="border:1px solid #ddd;border-collapse: collapse;">
            <tr style="font-size:10px;">
                <?php foreach ($schema as $field) { ?>
                <?php if($field['Field'] != 'password') : ?>
                <th title="<?php echo $field['Field']; ?>" style="border:1px solid #ddd">
                    <?php
                    $parts = explode('_', $field['Field']);
                    foreach ($parts as $key => $value) {
                        if($key > 0) echo '_';
                        echo $value.' ';
                    }
                    //echo substr($field['Field'], 0, 10);
                    ?>
                </th>
                <?php endif; ?>
                <?php } ?>
            </tr>
            <?php foreach ($data as $row) { ?>
            <tr>
                <?php foreach ($schema as $field) { ?>
                <?php if($field['Field'] != 'password') : ?>
                <td style="border:1px solid #ddd;padding:0;"><div style="height:100px;overflow-y: auto;padding:1px"><?php echo ($row[$field['Field']]); ?></div></td>
                <?php endif; ?>
                <?php } ?>
            </tr>
            <?php } ?>    
        </table>
    </div>
    
    <div style="text-align:center">
    <?php echo $pagination; ?>
    </div>
    
    <?php endif; ?>
</div>