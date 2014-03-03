<h1>Featured Images</h1>

<p>So you think you have a nive image to feature in our home page? Please follow the steps below. If your image is approved, it will be featured in our home page.</p>

<ol>
    
    <li>
        <?php echo anchor("home", "Sign in"); ?>
        <?php echo ($this->session->userdata('logged_in'))?'<small>[<strong>OK</strong>]</small>':''; ?>
    </li>

    <?php if($this->session->userdata('logged_in')) : ?>
    <li>
        Select one of the images you've shared:
        <div class="featured_images">
            
        </div>
    </li>

    <li>
        <?php echo anchor("home", "Sign in"); ?>
        <?php echo ($this->session->userdata('logged_in'))?'<small><strong>[OK]</strong></small>':''; ?>
    </li>
    <?php endif; ?>

</ol>


