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
		global $wpdb;

		$q        = sanitize_text_field( filter_input( INPUT_POST, 'query' ) );
		$response = [];


		$table_name = $wpdb->prefix . 'wc_orders';
		$query = $wpdb->prepare("SELECT ID FROM $table_name WHERE type = 'shop_order' AND CAST(ID AS CHAR) LIKE %s", $wpdb->esc_like( $q ) . '%' );

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			// Table doesn't exist, fallback to {$wpdb->prefix}posts
			$table_name = "{$wpdb->prefix}posts";
			$query = $wpdb->prepare("SELECT ID FROM $table_name WHERE post_type = 'shop_order' AND CAST(ID AS CHAR) LIKE %s", $wpdb->esc_like( $q ) . '%' );
		}


		$order_ids = $wpdb->get_col($query);

		if ( $order_ids ) {
			foreach ( $order_ids as $order_id ) {
				$response[] = [ 'value' => $order_id, 'label' => '#order :' . $order_id ];
			}
		}
		wp_reset_postdata();
		wp_send_json( $response );
		wp_die();
	}
}