<?php
/*
Plugin Name: Woo Preview Emails
Description: An Extension for WooCommerce that lets you Preview Emails, without having to send them.
Plugin URI: http://www.digamberpradhan.com.np/woocommerce-preview-e-mails/
Author: Digamber Pradhan
Author URI: http://digamberpradhan.com.np/
Version: 1.5.0
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
WC requires at least: 3.0.0
WC tested up to: 3.5.1
Text Domain: woo-preview-emails
Domain Path: /languages
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !defined('WOO_PREVIEW_EMAILS_DIR') ){
	define('WOO_PREVIEW_EMAILS_DIR', dirname(__FILE__));
}

if( !defined('WOO_PREVIEW_EMAILS_FILE') ){
	define('WOO_PREVIEW_EMAILS_FILE', __FILE__);
}

if( !function_exists('is_woocommerce_active') ){
	require_once('includes/woo-functions.php');
}

if( is_woocommerce_active() ){
	require_once('classes/class-woocommerce-preview-emails.php');
}

function woo_preview_emails_load_text_domain() {
    load_plugin_textdomain( 'woo-preview-emails', FALSE, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'woo_preview_emails_load_text_domain' );