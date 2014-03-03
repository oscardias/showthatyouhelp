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
            <?php echo lang('email_invite_subject'); ?>
            <a href="<?php echo user_profile($username); ?>" title="<?php printf(lang('home_view_profile'), $username); ?>"><?php echo (($full_name)?$full_name:$username); ?></a>
        </td>
    </tr>
    <tr>
        <td style="color: #333;" colspan="2">
            <a href="<?php echo user_profile($username); ?>" title="<?php printf(lang('home_view_profile'), $username); ?>"><?php echo (($full_name)?$full_name:$username); ?></a>
            <?php echo lang('email_invite_text'); ?>
        </td>
    </tr>
    
    <?php if($new_user) : ?>
    <tr>
        <td style="color: #333;" colspan="2">
            <?php echo lang('email_invite_description_new'); ?>
            <a href="<?php echo base_url('sign/up/'.$token); ?>"><?php echo base_url('sign/up/'.$token); ?></a>.
        </td>
    </tr>
    <tr>
        <td style="color: #333;" colspan="2">
            <a href="<?php echo base_url(); ?>">showthatyouhelp.com</a>
            <?php echo lang('home_description_1_link'); ?>
            <?php printf(lang('email_invite_more'), base_url('about')) ?>
        </td>
    </tr>
    <?php endif; ?>
    
    <tr><td colspan="2"></td></tr>
    <tr style="color: #FFF; background-color: #777; border-top: 1px solid #333; text-shadow: 1px 1px 0 #000; text-align: center;">
        <td colspan="2">
        <a href="<?php echo base_url('about'); ?>" style="color: #FFF"><?php echo lang('general_about'); ?></a>
        |
        <a href="<?php echo base_url('about/terms'); ?>" style="color: #FFF"><?php echo lang('general_terms'); ?></a>
        |
        <a href="<?php echo base_url('about/privacy'); ?>" style="color: #FFF"><?php echo lang('general_privacy'); ?></a>
        |
        &copy;<?php echo date('Y'); ?> <a href="<?php echo lang('general_softerize_link'); ?>" style="color: #FFF">Softerize</a>
        </td>
    </tr>
  </tbody>
</table>
</div>
</body>
