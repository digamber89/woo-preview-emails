<?php
/*
Plugin Name: Preview E-mails for WooCommerce
Description: An Extension for WooCommerce that lets you Preview Emails, without having to send them.
Plugin URI: https://www.digamberpradhan.com/preview-e-mails-for-woocommerce/
Author: Digamber Pradhan
Author URI: https://digamberpradhan.com/
Version: 2.1.1
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
WC requires at least: 3.0.0
WC tested up to: 6.6.1
Text Domain: woo-preview-emails
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly
defined( 'WOO_PREVIEW_EMAILS_DIR' ) || define( 'WOO_PREVIEW_EMAILS_DIR', dirname( __FILE__ ) );
defined( 'WOO_PREVIEW_EMAILS_FILE' ) || define( 'WOO_PREVIEW_EMAILS_FILE', __FILE__ );
require_once WOO_PREVIEW_EMAILS_DIR . '/includes/Bootstrap.php';