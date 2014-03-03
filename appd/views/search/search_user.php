<div id="main" class="container updates-list">
    
    <div id="updates-list-left">
        
        <?php $this->load->view('search/search_list', array('search_results' => $search_results)); ?>

        <?php if($total_pages > 1) : ?>
        <div id="updates-list-load-wrap">
            <?php echo anchor('ajax/display_search_list/'.(isset($s)?$s:'s').'/2', lang('home_load_more'), 'id="updates-list-load" class="gradient-btn updates-list-load-more"'); ?>
            <input type="hidden" value="<?php echo $total_pages; ?>" id="updates-list-load-total-pages"/>

            <div id="updates-list-load-more-pages">
            <?php echo $pagination; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
    <div id="updates-list-right">
        
        <div class="relative-block">
            
        </div>

        <div class="fixed-block">
            <div class="updates-list-search">
                <div class="updates-list-header">
                    <h2><?php echo lang('user_search_people'); ?></h2>
                </div>
                <div class="updates-list-search-form">
                    <?php echo form_open('user/search', array('method' => 'get')); ?>
                    <?php echo form_input('s', (isset($s)?$s:''), 'placeholder="search"'); ?>
                    <?php echo form_submit('', lang('user_search_go'), 'class="gradient-btn"'); ?>
                    <?php echo form_close(); ?>
                </div>
            </div>
        
            <div class="advertisement-block">
                <?php $this->load->view('advertisement/single'); ?>
            </div>
        </div>
        
    </div>
    
    <div class="clear"></div>
</div>