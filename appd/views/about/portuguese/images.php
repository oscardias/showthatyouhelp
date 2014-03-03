<h1>Imagens em Destaque</h1>

<p>Se você acha que tem uma linda foto que pode ser exibida na nossa página principal, siga os seguintes passos.
    Se sua imagem for aprovada, ela será exibida na nossa página inicial.</p>

<ol>
    
    <li>
        <?php echo anchor("home", "Entre"); ?>
        <?php echo ($this->session->userdata('logged_in'))?'<small>[<strong>OK</strong>]</small>':''; ?>
    </li>

    <?php if($this->session->userdata('logged_in')) : ?>
    <li>
        Selecione uma imagem que você compartilhou:
        <div class="featured_images">
            
        </div>
    </li>

    <li>
        <?php echo anchor("home", "Entre"); ?>
        <?php echo ($this->session->userdata('logged_in'))?'<small><strong>[OK]</strong></small>':''; ?>
    </li>
    <?php endif; ?>

</ol>


