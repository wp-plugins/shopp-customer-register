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
</div>