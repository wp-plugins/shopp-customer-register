<div id="pluginadminmenu-options" class="wrap">
    <h2><?php echo $pluginName; ?> Settings</h2>
    <h4>Uses the settings below to update <?php echo $pluginName; ?>'s settings</h4>
    <?php
    if (isset($_REQUEST['saved'])) {
        ?>
        <div class="ok_message"><?php echo $pluginName; ?> settings have been saved. </div>
        <?php
    }
    if (isset($_REQUEST['reset'])) {
        ?>
        <div class="ok_message"><?php echo $pluginName; ?> settings have been reset. </div>
        <?php
    }
    ?>

    <?php echo $options_html; ?>
    <p>If you like this plugin please consider donating</p>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="H33CVAE5A7RUN">
        <input type="image" name="submit" src="http://maca134.co.uk/plugins/paypal_donate.gif" alt="PayPal — The safer, easier way to pay online.">
        <img src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" alt="" width="1" height="1" border="0">
    </form>
</div>