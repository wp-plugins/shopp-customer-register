<?php
$plugin_dir = plugin_basename(__FILE__); $plugin_dir = str_replace( basename($plugin_dir), '', $plugin_dir );
define('PLUGINADMINMENU_DIR', WP_PLUGIN_DIR . '/' . $plugin_dir);
define('PLUGINADMINMENU_URL', WP_PLUGIN_URL . '/' . $plugin_dir);

class pluginAdminMenu {
    
    private $plugin_name = '';
    private $plugin_tag = '';
    private $plugin_icon = '';  
      
    private $views_path = 'views/';
    private $options = array();
    private $html_options = array();

    
    function __construct($plugin_tag, $plugin_name, $plugin_icon = '') {
        $this->plugin_name = $plugin_name;
        $this->plugin_tag = $plugin_tag;
        $this->plugin_icon = $plugin_icon;
        
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
    }
    function admin_init()
    {
        wp_register_style('pluginAdminMenuStyles', PLUGINADMINMENU_URL . 'css/styles.css');
        wp_register_script('pluginAdminMenuScripts', PLUGINADMINMENU_URL . 'js/scripts.js');
    }
    function admin_menu()
    {
        $page = add_menu_page(
            $this->plugin_tag, 
            $this->plugin_name, 
            'administrator', 
            $this->plugin_tag, 
            array(&$this, 'admin_page'),
            $this->plugin_icon);
        add_action('admin_print_styles-' . $page, array(&$this, 'admin_print_styles'));
        add_action('admin_print_scripts-' . $page, array(&$this, 'admin_print_scripts'));

        if ( $_GET['page'] == strtolower($this->plugin_tag) ) 
        {
            if ( 'save' == $_REQUEST['action'] ) 
            {
                foreach ($this->html_options as $value) 
                {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
                }
                foreach ($this->html_options as $value) 
                {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) 
                    { 
                        update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
                    } 
                    else 
                    { 
                        delete_option( $value['id'] ); 
                    } 
                }
                header('Location: admin.php?page=' . strtolower($this->plugin_tag) . '&saved=true');
                die;
            }
            else if( 'reset' == $_REQUEST['action'] ) 
            {
                foreach ($this->html_options as $value) 
                {
                    delete_option( $value['id'] ); 
                }
                header('Location: admin.php?page=' . strtolower($this->plugin_tag) . '&reset=true');
                die;
            }
        }  
    }
    function admin_print_styles()
    {
        wp_enqueue_style('pluginAdminMenuStyles');
    }
    function admin_print_scripts()
    {
        wp_enqueue_script('pluginAdminMenuScripts');
    }
    function admin_page()
    {
        $data = array();
        $data['pluginName'] = $this->plugin_name;
        $data['options_html'] = $this->generate_html($this->html_options);
        $this->view('main_menu', $data);
    }
    function generate_html($options)
    {
        ob_start();
        ?>
        <form method="post">
        <?php
        foreach ($options as $value) 
        {
            switch ( $value['type'] ) 
            {
                case "close":
                    ?>
                        </div>
                        </div>
                        <br />
                    <?php 
                break;
                
                case "title":
                    ?>
                        <p>To easily use the <?php echo $this->plugin_name;?> plugin, you can use the menu below.</p>
                    <?php 
                break;
                case 'p':
					?><p><?php echo $value['name']; ?></p><?php
				break;
                case 'text':
                    ?>
                        <div class="pam_input pam_text">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( $this->options( $value['id'] ) != "") { echo stripslashes($this->options( $value['id'])  ); } else { echo $value['std']; } ?>" />
                        <small><?php echo $value['desc']; ?></small>
                        </div>
                    <?php
                break;
                
                case 'textarea':
                    ?>
                        <div class="pam_input pam_textarea">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( $this->options( $value['id'] ) != "") { echo stripslashes($this->options( $value['id']) ); } else { echo $value['std']; } ?></textarea>
                        <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
                        </div>
                    <?php
                break;
                
                case 'select':
                    ?>
                        <div class="pam_input pam_select">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                        <?php foreach ($value['options'] as $option) { ?>
                        <option <?php if ($this->options( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
                        </select>
                        <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
                        </div>
                    <?php
                break;
                case 'radio':
                    ?>
                        <div class="pam_radio">
                            <label><?php echo $value['name']; ?></label>
                            <?php foreach ($value['options'] as $i => $v) { ?>
                                <div class="input_radio">
                                    <input type="radio" id="<?php echo $value['id'] . $i; ?>" name="<?php echo $value['id']; ?>" value="<?php echo $i; ?>" <?php if ($this->options( $value['id'] ) == $i) { echo 'checked="checked"'; } ?> />
                                    <label for="<?php echo $value['id'] . $i; ?>"><?php echo $v; ?></label>
                                </div>
                            <?php }; ?>
                            <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
                        </div>
                    <?php
                break;
                
                case "checkbox":
                    ?>
                        <div class="pam_input pam_checkbox">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <?php if($this->options($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                        <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="1" <?php echo $checked; ?> />
                        <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
                        </div>
                    <?php 
                break;
                case "section":
                    $i++;
                    ?>
                        <div class="pam_section">
                        <div class="pam_title">
                            <h3><img src="<?php echo PLUGINADMINMENU_URL; ?>images/trans.png" class="inactive" /><?php echo $value['name']; ?></h3>
                            <span class="submit">
                                <input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
                            </span>
                            <div class="clear"></div>
                        </div>
                        <div class="pam_options">
                    <?php 
                break;
            }
        }
        ?>
            <input type="hidden" name="action" value="save" />
        </form>
        <form method="post">
            <p class="submit">
                <input name="reset" type="submit" value="Reset" />
                <input type="hidden" name="action" value="reset" />
            </p>
        </form>
        <?php
        $contents = ob_get_contents(); 
        ob_end_clean();
        return $contents;
    }
    function set_options($html_options)
    {
        $this->html_options = $html_options;
        $attr = array();
        foreach ($html_options as $key => $value)
        {
            switch ($value['type'])
            {
                case 'checkbox':
                case 'select':
                case 'text':
                case 'textarea':
                case 'radio':
                   $attr[$value['id']] = $value['std']; 
                   $this->options[$value['id']] = get_option($value['id'], $value['std']);
                default:
                break;
            }
        }
        return $this->options;
    }
    function options($key)
    {
        return (isset($this->options[$key])) ? $this->options[$key] : false;
    }
    function view($page, $data, $return_html = false)
    {
        $view_file = PLUGINADMINMENU_DIR . $this->views_path . $page . '.php';
        if (file_exists($view_file))
        {
            if (is_array($data) && count($data) > 0) { 
                foreach ($data as $key => $value) { 
                    $key = str_replace('-', '', $key); $$key = $value;  
                } 
            }
            if ($return_html == true) { 
                ob_start(); 
                include $view_file; 
                $contents = ob_get_contents(); 
                ob_end_clean(); 
                return $contents; 
            } 
            else { 
                include $view_file; 
            }
        }
        else
        {
            return false;
        }
    }
    function do_error($message) {
        throw new Exception($message);
    }
}


?>