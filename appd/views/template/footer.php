    <div class="bottom-navigation">
        <?php echo anchor(base_url('about'), lang('general_about')); ?>
        |
        <?php echo anchor(base_url('about/terms'), lang('general_terms')); ?>
        |
        <?php echo anchor(base_url('about/privacy'), lang('general_privacy')); ?>
        |
        &copy;<?php echo date('Y'); ?> <?php echo anchor(lang('general_softerize_link'), "Softerize"); ?>
    </div>
</body>
</html>