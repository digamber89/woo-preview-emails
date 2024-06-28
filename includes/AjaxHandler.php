<?php

namespace Codemanas\WooPreviewEmails;

/**
 * Class AjaxHandler
 *
 * The AjaxHandler class is responsible for handling AJAX requests in WordPress.
 */
class AjaxHandler {
	private static $_instance = null;

	public static function get_instance() {
		return ( self::$_instance == null ) ? self::$_instance = new self() : self::$_instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_woo_preview_orders_search', [ $this, 'get_orders' ] );
	}

	public function get_orders() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return false;
		}

		$q        = sanitize_text_field( filter_input( INPUT_POST, 'query' ) );
		$response = [];
		$order = wc_get_order($q);
		if($order){
			$response[] = ['value' => $order->get_id(), 'label' => '#Order: '.$order->get_order_number()];
		}

		wp_send_json( $response );
		wp_die();
	}
}