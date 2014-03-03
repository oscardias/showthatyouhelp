<?php echo'<?xml version="1.0" encoding="UTF-8" ?>' ?>
 
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc><?php echo base_url();?></loc>
        <image:image>
            <image:loc><?php echo base_url('images/logo.png'); ?></image:loc> 
        </image:image>
        <lastmod><?php echo date('Y-m-01'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>
    
    <url>
        <loc><?php echo base_url('about');?></loc>
        <image:image>
            <image:loc><?php echo base_url('images/logo.png'); ?></image:loc> 
        </image:image>
        <lastmod><?php echo date('Y-m-01'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <url>
        <loc><?php echo base_url('about/terms');?></loc>
        <image:image>
            <image:loc><?php echo base_url('images/logo.png'); ?></image:loc> 
        </image:image>
        <lastmod><?php echo date('Y-m-01'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <url>
        <loc><?php echo base_url('about/privacy');?></loc>
        <image:image>
            <image:loc><?php echo base_url('images/logo.png'); ?></image:loc> 
        </image:image>
        <lastmod><?php echo date('Y-m-01'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
 
    <url>
        <loc><?php echo base_url('about/contact');?></loc>
        <image:image>
            <image:loc><?php echo base_url('images/logo.png'); ?></image:loc> 
        </image:image>
        <lastmod><?php echo date('Y-m-01'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
 
    <?php foreach($users as $user) { ?>
    
    <url>
        <loc><?php echo base_url('p/'.$user['username']); ?></loc>
        <image:image>
            <image:loc><?php echo user_profile_image($user['username'], $user['image_name'], $user['image_ext'], 'large'); ?></image:loc> 
        </image:image>
        <lastmod><?php echo date('Y-m-d', ($user['last_mod'])?strtotime($user['last_mod']):strtotime($user['user_created'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>
    <?php } ?>
    
    <?php foreach($updates as $update) { ?>
    
    <url>
        <loc><?php echo base_url('p/'.$update['username'].'/'.$update['update_id']); ?></loc>
        <?php if($update['image_id']) : ?>
        
        <image:image>
            <image:loc><?php echo base_url()."upload/photo/{$update['image_folder']}/{$update['image_id']}-{$update['image_filename']}{$update['image_fileext']}"; ?></image:loc> 
        </image:image>
        <?php endif; ?>
        
        <lastmod><?php echo date('Y-m-d', strtotime($update['update_created'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php } ?>
 
</urlset>