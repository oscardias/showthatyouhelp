<h1>Contato e Suporte</h1>

<?php if($sent) : ?>
<div class="contact-answer">
    <p>Obrigado!</p>
    <p>Sua participação é muito importante para melhorarmos o showthatyouhelp.com!</p>
    <p>Vamos responder assim que possível.</p>
</div>
<?php endif; ?>

<?php echo form_open(base_url('about/contact/send'), 'id="contact-form"'); ?>

<?php echo form_label('Razão:', 'reason'); ?>
<?php
$options = array(
    'contact' => 'Contato',
    'support' => 'Suporte',
    'invitation' => 'Convite',
    'other' => 'Outra'
);
echo form_dropdown('reason', $options);
?>

<?php echo form_label('Nome:', 'name'); ?>
<?php echo form_input('name', $name, 'placeholder="Nome"'); ?>

<?php echo form_label('Email:', 'email'); ?>
<?php echo form_input('email', $email, 'placeholder="Email"'); ?>

<?php echo form_label('Mensagem:', 'message'); ?>
<?php echo form_textarea(array('name' => 'message','cols' => '40', 'rows' => '5', 'placeholder' => 'Digite sua mensagem aqui...' )); ?>

<div class="form-buttons">
<?php echo form_submit('send', 'Enviar', 'class="gradient-btn"'); ?>
</div>

<?php echo form_close(); ?>