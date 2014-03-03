<a href="<?php echo $refresh_url; ?>" id="updates-list-refresh-url" title="<?php echo lang('home_view'); ?>">
<?php if($total_new == 1) : ?>
<?php echo lang('home_update_single'); ?>
<?php else : ?>
<?php printf(lang('home_update_many'), $total_new); ?>
<?php endif; ?>
</a>