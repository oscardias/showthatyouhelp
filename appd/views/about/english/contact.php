<h1>Contact and Support</h1>

<?php if($sent) : ?>
<div class="contact-answer">
    <p>Thank you!</p>
    <p>Your feedback is very important for the improvement of showthatyouhelp.com!</p>
    <p>We'll reply as soon as possible.</p>
</div>
<?php endif; ?>

<?php echo form_open(base_url('about/contact/send'), 'id="contact-form"'); ?>

<?php echo form_label('Reason:', 'reason'); ?>
<?php
$options = array(
    'contact' => 'Contact',
    'support' => 'Support',
    'invitation' => 'Invitation',
    'other' => 'Other'
);
echo form_dropdown('reason', $options);
?>

<?php echo form_label('Name:', 'name'); ?>
<?php echo form_input('name', $name, 'placeholder="Name"'); ?>

<?php echo form_label('Email:', 'email'); ?>
<?php echo form_input('email', $email, 'placeholder="Email"'); ?>

<?php echo form_label('Message:', 'message'); ?>
<?php echo form_textarea(array('name' => 'message','cols' => '40', 'rows' => '5', 'placeholder' => 'Type your message here...' )); ?>

<div class="form-buttons">
<?php echo form_submit('send', 'Send','class="gradient-btn"'); ?>
</div>

<?php echo form_close(); ?>