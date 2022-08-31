<?php

namespace Codemanas\WooPreviewEmails;

class Bootstrap {
	public static ?Bootstrap $instance = null;
	private static $active_plugins;
	public static function get_instance(): ?Bootstrap {
		return is_null( self::$instance ) ? ( self::$instance = new self() ) : self::$instance;
	}

	public function __construct() {
		$this->load_dependencies();
		add_action( 'plugin_loaded', [ $this, 'init_plugin' ] );
		add_action( 'init', [ $this, 'load_text_domain' ] );
	}

	public static function init() {
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	/**
	 * @return bool
	 */
	public static function woocommerce_active_check(): bool {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
	}

	public function load_text_domain() {
		load_plugin_textdomain( 'woo-preview-emails', false, plugin_basename( WOO_PREVIEW_EMAILS_DIR ) . '/languages/' );
	}

	public function init_plugin() {
		if( self::woocommerce_active_check() ){
			Main::get_instance();
		}
	}

	private function load_dependencies() {
		require_once WOO_PREVIEW_EMAILS_DIR . '/vendor/autoload.php';
	}
}

Bootstrap::get_instance();