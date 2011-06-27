<?php
/*
Plugin Name: Shopp Customer Register
Version: 0.4.4
Description: Register customer accounts for Shopp without having to order
Plugin URI: https://bitbucket.org/maca134/shopp-customer-register/wiki/Home
Author: Matthew McConnell
Author URI: http://www.maca134.co.uk/
*/
if (! class_exists ( 'pluginAdminMenu' ))
	include_once 'pluginadminmenu/pluginadminmenu.php';

$plugin_dir = plugin_basename ( __FILE__ );
$plugin_dir = str_replace ( basename ( $plugin_dir ), '', $plugin_dir );
define ( 'SHOPPREGCUST_DIR', WP_PLUGIN_DIR . '/' . $plugin_dir );
define ( 'SHOPPREGCUST_URL', WP_PLUGIN_URL . '/' . $plugin_dir );

/**
 * Shoppregcust Class
 * 
 * @package shoppregcust
 * @author Matthew McConnell
 */
class shoppregcust {
	public $user = array ();
	
	private $views_path = 'views/';
	private $table_prefix = '';
	private $error_message = '';
	private $shopp_account_type = '';
	private $plugin_name = 'Shopp Reg';
	private $plugin_tag = 'shoppreg';
	private $options = array ();
	private $pluginAdminMenu;
	/**
	 * shoppregcust::__construct()
	 * 
	 * @return
	 */
	public function __construct() {
		global $Shopp;
		$no_errors = true;
		
		if (! isset ( $Shopp )) {
			$no_errors = false;
			$this->error_message = 'The Shopp plugin needs to be activated for Shopp Customer Register to work.';
		} elseif ($Shopp->Settings->registry ['account_system'] == 'none') {
			$no_errors = false;
			$this->error_message = 'Shopp \'Customer Accounts\' is set to \'No Accounts\', so user registrations is disabled.';
		} else {
			$this->setup_options ();
			add_shortcode ( 'shopp_regform', array (&$this, 'shortcode' ) );
			global $table_prefix;
			$this->shopp_account_type = $Shopp->Settings->registry ['account_system'];
			$this->table_prefix = $table_prefix;
		}
		add_action ( 'admin_notices', array (&$this, 'admin_notices' ) );
	}
	/**
	 * shoppregcust::setup_options()
	 * 
	 * @return void
	 */
	function setup_options() {
		$plugin_icon = SHOPPREGCUST_URL . 'plugin_icon.png';
		
		$this->pluginAdminMenu = new pluginAdminMenu ( $this->plugin_tag, $this->plugin_name, $plugin_icon );
		
		$this->options = $this->pluginAdminMenu->set_options ( array (array ('name' => 'General Settings', 'type' => 'section' ), array ('name' => 'To use this plugin just place [shopp_regform] to add a customer registration form.', 'type' => 'p' ), array ('name' => 'Show Billing', 'desc' => 'Check this to show billing form.', 'id' => $this->plugin_tag . '_show_billing', 'type' => 'checkbox' ), array ('name' => 'Thankyou Message', 'type' => 'textarea', 'id' => $this->plugin_tag . '_thankyou_message', 'desc' => 'Please enter a thankyou message to be displayed when the registration is complete.' ), array ('type' => 'close' ) ) );
	}
	/**
	 * shoppregcust::admin_notices()
	 * 
	 * @return
	 */
	public function admin_notices() {
		if (! empty ( $this->error_message )) {
			?><div id='message' class='updated'>
<p><?php
			echo $this->error_message;
			?></p>
</div><?php
		}
	}
	/**
	 * shoppregcust::shortcode()
	 * 
	 * @return
	 */
	public function shortcode() {
		$data = array ();
		$data ['show_form'] = true;
		$data ['shopp_account_type'] = $this->shopp_account_type;
		$this->show_billing = (isset ( $this->options ['shoppreg_show_billing'] ) && $this->options ['shoppreg_show_billing'] == 1) ? true : false;
		$this->thankyou_message = (isset ( $this->options ['shoppreg_thankyou_message'] ) && ! empty ( $this->options ['shoppreg_thankyou_message'] )) ? $this->options ['shoppreg_thankyou_message'] : 'Thankyou for registering.';
		
		if ($this->show_billing) {
			global $Shopp;
			$base = $Shopp->Settings->get ( 'base_operations' );
			$countries = $Shopp->Settings->get ( 'target_markets' );
			$selected_country = (isset ( $_POST ['billing'] ['country'] )) ? $_POST ['billing'] ['country'] : $base ['country'];
			$data ['countries_select_html'] = menuoptions ( $countries, $selected_country, true );
			$data ['show_billing'] = $this->show_billing;
		}
		if (isset ( $_POST ['customer'] )) {
			$user = $this->add_user ();
			
			if (! $user) {
				$data ['form_error'] = $this->form_error;
			} else {
				$this->user = $user;
				$data ['show_form'] = false;
				$data ['form_success'] = $this->thankyou_message;
				do_action ( 'scr_registration_success' );
			}
		}
		$html = $this->view ( 'form.php', $data );
		return ($html !== false) ? $html : '';
	}
	/**
	 * shoppregcust::add_user()
	 * 
	 * @param mixed $data
	 * @return
	 */
	private function add_user() {
		require_once (ABSPATH . '/wp-includes/registration.php');
		
		$Errors = & ShoppErrors ();
		$Errors->reset ();
		if (empty ( $_POST ['customer'] ['email'] )) {
			$this->form_error = 'Email address is required.';
			return false;
		}
		if ($this->email_exists ( $_POST ['customer'] ['email'] )) {
			$this->form_error = 'Email address is already registered with another Shopp customer.';
			return false;
		}
		if (empty ( $_POST ['customer'] ['password'] )) {
			$this->form_error = 'Password is required.';
			return false;
		}
		if ($_POST ['customer'] ['password'] !== $_POST ['customer'] ['confirm-password']) {
			$this->form_error = 'Passwords do not match.';
			return false;
		}
		if ($this->shopp_account_type == 'wordpress') {
			if (empty ( $_POST ['customer'] ['loginname'] )) {
				$this->form_error = 'Username is already registered.';
				return false;
			}
			if (email_exists ( $_POST ['customer'] ['email'] )) {
				$this->form_error = 'Email address is already registered with another Wordpress user.';
				return false;
			}
		}
		if ($this->show_billing) {
			if (empty ( $_POST ['billing'] ['address'] )) {
				$this->form_error = 'Street address is required.';
				return false;
			}
			if (empty ( $_POST ['billing'] ['city'] )) {
				$this->form_error = 'City is required.';
				return false;
			}
			if (empty ( $_POST ['billing'] ['state'] )) {
				$this->form_error = 'State is required.';
				return false;
			}
			if (empty ( $_POST ['billing'] ['postcode'] )) {
				$this->form_error = 'Postcode is required.';
				return false;
			}
		}
		
		$customer_data = $_POST ['customer'];
		
		$shopp_customer = new Customer ();
		$shopp_customer->updates ( $customer_data );
		
		if ($this->shopp_account_type == 'wordpress') {
			$shopp_customer->create_wpuser (); // not logged in, create new account
			$customer_data ['wpuser'] = $shopp_customer->wpuser;
			unset ( $shopp_customer->password );
			if ($Errors->exist ( SHOPP_ERR )) {
				$shopp_error = $Errors->get ( SHOPP_ERR );
				$this->form_error = implode ( ', ', $shopp_error [0]->messages );
				return false;
			}
		} else {
			$shopp_customer->password = wp_hash_password ( $customer_data ['password'] );
		}
		$shopp_customer->save ();
		
		if ($Errors->exist ( SHOPP_ERR )) {
			$shopp_error = $Errors->get ( SHOPP_ERR );
			$this->form_error = implode ( ', ', $shopp_error [0]->messages );
			return false;
		}
		if ($this->show_billing) {
			$billing_data = $_POST ['billing'];
			$shopp_billing = new Billing ();
			$shopp_billing->updates ( $billing_data );
			$shopp_billing->customer = $shopp_customer->id;
			$shopp_billing->save ();
		}
		
		return $customer_data;
	}
	/**
	 * shoppregcust::email_exists()
	 * 
	 * @param mixed $email
	 * @return
	 */
	private function email_exists($email) {
		global $wpdb;
		$rows = $wpdb->get_results ( 'SELECT `email` FROM `' . $this->table_prefix . SHOPP_DBPREFIX . 'customer` WHERE `email` = "' . $email . '"' );
		return (count ( $rows ) > 0) ? true : false;
	}
	/**
	 * shoppregcust::view()
	 * 
	 * @param string $file
	 * @param mixed $data
	 * @param bool $return_html
	 * @return
	 */
	private function view($file = 'none', $data = array(), $return_html = true) {
		$view_file = SHOPPREGCUST_DIR . $this->views_path . $file;
		if (file_exists ( $view_file )) {
			if (is_array ( $data ) && count ( $data ) > 0) {
				foreach ( $data as $key => $value ) {
					$key = str_replace ( '-', '', $key );
					$$key = $value;
				}
			}
			if ($return_html == true) {
				ob_start ();
				include $view_file;
				$contents = ob_get_contents ();
				ob_end_clean ();
				return $contents;
			} else {
				include $view_file;
			}
		} else {
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
	$shoppregcust = new shoppregcust ();
}
add_action ( 'init', 'shoppregcust' );
