<body bgcolor="001300">
<div align="center">
<br>

<table style="font-family: Arial, sans-serif; font-size: 12px; width: 580px; border: 10px solid #151" border="0" cellspacing="0" cellpadding="10" align="center" bgcolor="#FFFFFF">
  <tbody>
    <tr style="background-color: #2B9B0C; border: 1px solid #151">
        <td style="text-shadow: 1px 1px 0 #000" colspan="2">
            <a href="<?php echo base_url(); ?>" style="color: #FFF">showthatyouhelp.com</a>
        </td>
    </tr>
    <tr>
        <td style="font-size: 18px; color: #151;" colspan="2">
            <?php printf($language['email']['email_pending_hello'], (($full_name)?$full_name:$username)); ?>
        </td>
    </tr>
    <tr>
        <td style="color: #333;" colspan="2">
            <?php
            if($num_notifications > 1) {
                printf($language['email']['email_pending_text_more'], $num_notifications);
                printf($language['email']['email_pending_text_more_2'], base_url('home/notifications'));
            } else {
                printf($language['email']['email_pending_text_single'], 1);
                printf($language['email']['email_pending_text_single_2'], base_url('home/notifications'));
            } ?>
        </td>
    </tr>
    
    <tr>
        <td style="color: #333;" colspan="2">
            <?php echo $language['email']['email_notification_deactivate']; ?>
        </td>
    </tr>
    
    <tr><td colspan="2"></td></tr>
    <tr style="color: #FFF; background-color: #777; border-top: 1px solid #333; text-shadow: 1px 1px 0 #000; text-align: center;">
        <td colspan="2">
        <a href="<?php echo base_url('about'); ?>" style="color: #FFF"><?php echo $language['general']['general_about']; ?></a>
        |
        <a href="<?php echo base_url('about/terms'); ?>" style="color: #FFF"><?php echo $language['general']['general_terms']; ?></a>
        |
        <a href="<?php echo base_url('about/privacy'); ?>" style="color: #FFF"><?php echo $language['general']['general_privacy']; ?></a>
        |
        &copy;<?php echo date('Y'); ?> <a href="<?php echo $language['general']['general_softerize_link']; ?>" style="color: #FFF">Softerize</a>
        </td>
    </tr>
  </tbody>
</table>
</div>
</body>
