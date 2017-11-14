<?php
/**
 * paura-product-manage.php
 *
 * @author belinsky
 * @package paura-product-manage
 * @since 1.0.0
 *
 * Plugin Name: paura-product-manage
 * Description: products management.
 * Version: 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
define( 'PAUPRO_PLUGIN_VERSION', '1.0.0' );
define( 'PAUPRO_PLUGIN_DOMAIN', 'paura-product-manage' );
define( 'PAUPRO_PLUGIN_URL', WP_PLUGIN_URL . '/paura-product-manage' );
define( 'PAUTBL_BRAND', $wpdb->prefix .'paura_brand');
define( 'PAUTBL_PRODUCT', $wpdb->prefix .'paura_product');
define( 'PAUTBL_COLOR', $wpdb->prefix .'paura_color');
define( 'PAUTBL_SIZE', $wpdb->prefix .'paura_size');

/** 
 * paupro class_alias
*/

class Paura_Product_Manage{

	 public function __construct(){
		
		include_once('template/paura-manage.php');
		include_once('template/paura-ajax.php');
		add_action('init',array( &$this, 'init'));
	 }
	 public static function init(){
		add_action('admin_menu',array(__class__, 'admin_menu'));
	 }
	 
	 public static function admin_menu(){
		 $page = add_menu_page(
			__( 'Product Manage', 'textdomain' ),
			'Design Manage',
			'manage_options',
			'paura_design_manage',
			array(__class__,'paura_manage_func'),
			plugins_url( 'paura-product-manage/images/icon.png' ),
			6
		);
		add_action( 'load-' . $page, array( __CLASS__, 'load' ) );
	 }
	 
	 public static function load() {
		 
		wp_register_script( 'paura_script', PAUPRO_PLUGIN_URL . '/js/paura_script.js', array( 'jquery' ), PAUPRO_PLUGIN_VERSION, true );
		wp_register_style( 'paura_style', PAUPRO_PLUGIN_URL . '/css/paura_style.css', array(), PAUPRO_PLUGIN_VERSION );
		wp_enqueue_script( 'paura_script' );
		wp_enqueue_style( 'paura_style' );
		wp_localize_script( 'paura_script', 'ajax_object', array( 'ajax_url' => admin_url( '/admin-ajax.php' ), 'value' => 1234 ) );
	 }
	 
	 public static function paura_manage_func(){
		do_action('pm_output');
	 }
}
new Paura_Product_Manage();
?>