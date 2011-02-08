<?php
/*
Plugin Name: Shopp Customer Register
Version: 0.3
Description: Register customer accounts for Shopp without having to order
Plugin URI: https://bitbucket.org/maca134/shopp-customer-register/wiki/Home
Author: Matthew McConnell
Author URI: http://www.maca134.co.uk/
*/

$plugin_dir = plugin_basename(__FILE__); $plugin_dir = str_replace( basename($plugin_dir), '', $plugin_dir );
define('SHOPPREGCUST_DIR', WP_PLUGIN_DIR . '/' . $plugin_dir);
define('SHOPPREGCUST_URL', WP_PLUGIN_URL . '/' . $plugin_dir);


/**
 * Shoppregcust Class
 * 
 * @package shoppregcust
 * @author Matthew McConnell
 */
class shoppregcust {
    public $user = array();
    
    private $views_path     = 'views/';
    private $table_prefix = '';
    private $shopp_version = array('1.1.5', '1.1.6');
    private $error_message = '';
    private $shopp_account_type = '';

    /**
     * shoppregcust::__construct()
     * 
     * @return
     */
    public function __construct()
    {
        global $Shopp;
        $no_errors = true;

        if (isset($Shopp))
        {
            if (!in_array($Shopp->Settings->registry['version'], $this->shopp_version))
            {
                $no_errors = false;
                $this->error_message = 'Shopp Customer Register has only been tested on the following Shopp versions: ' . implode(', ', $this->shopp_version);
            }
            if ($Shopp->Settings->registry['account_system'] == 'none') 
            {
                $no_errors = false;
                $this->error_message = 'Shopp \'Customer Accounts\' is set to \'No Accounts\', so user registrations is disabled.';
            }
        }
        else
        {
            $no_errors = false;
            $this->error_message = 'The Shopp plugin needs to be activated for Shopp Customer Register to work.';
        }
        if ($no_errors)
        {
            add_shortcode('shopp_regform', array(&$this, 'shortcode'));
            global $table_prefix;
            $this->shopp_account_type = $Shopp->Settings->registry['account_system'];
            $this->table_prefix = $table_prefix;
        }
        else
        {
            add_action('admin_notices', array(&$this, 'admin_notices'));
        }
    }
    /**
     * shoppregcust::admin_notices()
     * 
     * @return
     */
    public function admin_notices()
    {
        ?><div id="message" class="updated"><p><?php echo $this->error_message; ?></p></div><?php
    }    
    /**
     * shoppregcust::shortcode()
     * 
     * @return
     */
    public function shortcode()
    {
        $data = array();
        $data['show_form'] = true;
        $data['shopp_account_type'] = $this->shopp_account_type;
        
        if (isset($_POST['customer']))
        {
            
            $user = $this->add_user($_POST['customer']);
                
            if (!$user)
            {
                $data['form_error'] = $this->form_error;
            }
            else
            {
                $this->user = $user;
                $data['show_form'] = false;
                $data['form_success'] = 'Thankyou for registering.';
                do_action('scr_registration_success');
            }
        }
        $html = $this->view('form.php', $data);
        return ($html !== false) ? $html : '';
    }   
    /**
     * shoppregcust::add_user()
     * 
     * @param mixed $data
     * @return
     */
    private function add_user($data)
    {
        require_once(ABSPATH."/wp-includes/registration.php");
        
        $Errors =& ShoppErrors();
        $Errors->reset();
        if (empty($data['email'])) 
        {
            $this->form_error = 'Email address is required.';
            return false;
        }
        if ($this->email_exists($data['email'])) 
        {
            $this->form_error = 'Email address is already registered with another Shopp customer.';
            return false;
        }
        if (empty($data['password'])) 
        {
            $this->form_error = 'Password is required.';
            return false;
        }
        if ($data['password'] !== $data['confirm-password']) 
        {
            $this->form_error = 'Passwords do not match.';
            return false;
        } 
        if ($this->shopp_account_type == 'wordpress')
        {
            if (empty($data['loginname'])) 
            {
                $this->form_error = 'Username is already registered.';
                return false;                
            }
            if (email_exists($data['email']))
            {
                $this->form_error = 'Email address is already registered with another Wordpress user.';
                return false;                 
            }
        }
        
        $shopp_customer = new Customer();
        $shopp_customer->updates($data);
        
        if ($this->shopp_account_type == 'wordpress') 
        {
            $shopp_customer->create_wpuser(); // not logged in, create new account
            $data['wpuser'] = $shopp_customer->wpuser;
            unset($shopp_customer->password);
            if ($Errors->exist(SHOPP_ERR)) 
            {
                $shopp_error = $Errors->get(SHOPP_ERR);
                $this->form_error = implode(', ', $shopp_error[0]->messages);
                return false;
            }
        }
        else
        {       
            $shopp_customer->password = wp_hash_password($data['password']);
        }        
        $shopp_customer->save();
                
        if ($Errors->exist(SHOPP_ERR)) 
        {
            $shopp_error = $Errors->get(SHOPP_ERR);
            $this->form_error = implode(', ', $shopp_error[0]->messages);
            return false;
        }                
        return $data;
    }
    /**
     * shoppregcust::email_exists()
     * 
     * @param mixed $email
     * @return
     */
    private function email_exists($email)
    {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT `email` FROM `' . $this->table_prefix . SHOPP_DBPREFIX . 'customer` WHERE `email` = "' . $email . '"');
        return (count($rows) > 0) ? true : false;        
    } 
    /**
     * shoppregcust::view()
     * 
     * @param string $file
     * @param mixed $data
     * @param bool $return_html
     * @return
     */
    private function view($file = 'none', $data = array(), $return_html = true)
    {
        $view_file = SHOPPREGCUST_DIR . $this->views_path . $file;
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
}

/**
 * shoppregcust()
 * 
 * @return
 */
function shoppregcust() { 
	global $shoppregcust; 
	$shoppregcust = new shoppregcust(); 
}
add_action('init', 'shoppregcust');
