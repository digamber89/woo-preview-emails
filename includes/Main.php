<?php

namespace Codemanas\WooPreviewEmails;

defined( 'ABSPATH' ) || exit;

class Main {
	public static ?Main $instance = null;
	private string $recipient = '';
	private string $plugin_url = '';
	public array $emails = [];
	public string $notice_message = '';
	public string $notice_class = '';
	private string $choose_email = '';

	/**
	 * @return Main|null
	 */
	public static function get_instance(): ?Main {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		$this->plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );
		add_action( 'admin_menu', [ $this, 'add_preview_mail_page' ], 90 );
		add_action( 'init', [ $this, 'load_email_classes' ], 999 );
		//generates result
		add_action( 'admin_init', [ $this, 'email_preview_output' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ], 10, 1 );
		add_filter( 'plugin_action_links_woo-preview-emails/woocommerce-preview-emails.php', [ $this, 'settings_link' ], 20 );

		//HPOS Compatibility
		add_action( 'before_woocommerce_init', [ $this, 'hpos_compatible' ] );
	}

	//mark as HPOS compatibility
	public function hpos_compatible(): void {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOO_PREVIEW_EMAILS_FILE, true );
		}
	}

	public function settings_link( $links ) {
		// Build and escape the URL.
		$url = esc_url( add_query_arg(
			'page',
			'codemanas-woocommerce-preview-emails',
			get_admin_url() . 'admin.php'
		) );
		// Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
		// Adds the link to the end of the array.
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * @return void
	 */
	public function load_email_classes(): void {
		$page = filter_input( INPUT_GET, 'page' );
		if ( class_exists( 'WC_Emails' ) && $page == 'codemanas-woocommerce-preview-emails' ) {
			$wc_emails = \WC_Emails::instance();
			$emails    = $wc_emails->get_emails();
			if ( ! empty( $emails ) ) {
				$unset_booking_emails      = apply_filters( 'woo_preview_emails_unset_booking_emails', UnsupportedEmails::unset_booking_emails() );
				$unset_subscription_emails = apply_filters( 'woo_preview_emails_unset_subscription_emails', UnsupportedEmails::unset_subscription_emails() );
				$unset_membership_emails   = apply_filters( 'woo_preview_emails_unset_memebership_emails', UnsupportedEmails::unset_membership_emails() );

				if ( ! empty( $unset_booking_emails ) ) {
					foreach ( $unset_booking_emails as $unset_booking_email ) {
						if ( isset( $emails[ $unset_booking_email ] ) ) {
							unset( $emails[ $unset_booking_email ] );
						}
					}
				}

				if ( ! empty( $unset_subscription_emails ) ) {
					foreach ( $unset_subscription_emails as $unset_subscription_email ) {
						if ( isset( $emails[ $unset_subscription_email ] ) ) {
							unset( $emails[ $unset_subscription_email ] );
						}
					}
				}

				if ( ! empty( $unset_membership_emails ) ) {
					foreach ( $unset_membership_emails as $unset_membership_email ) {
						if ( isset( $emails[ $unset_membership_email ] ) ) {
							unset( $emails[ $unset_membership_email ] );
						}
					}
				}

				$this->emails = $emails;
			}
		}

	}

	/**
	 * load woo preview scripts
	 *
	 * @param  [type] $hook [admin page suffix]
	 */
	public function load_scripts( $hook ): void {
		if ( $hook != 'woocommerce_page_codemanas-woocommerce-preview-emails' ) {
			return;
		}

		if ( ! file_exists( WOO_PREVIEW_EMAILS_DIR . '/assets/main.asset.php' ) ) {
			return;
		}
		$assets_file = include WOO_PREVIEW_EMAILS_DIR . '/assets/main.asset.php';
		wp_register_script( 'woo-preview-emails__main', $this->plugin_url . '/assets/main.js', $assets_file['dependencies'], $assets_file['version'], true );
		wp_enqueue_script( 'woo-preview-emails__main' );
		wp_register_style( 'woo-preview-emails__style', $this->plugin_url . '/assets/style-main.css', $assets_file['dependencies'], $assets_file['version'] );
		wp_enqueue_style( 'woo-preview-emails__style' );
	}

	/**
	 * @return void'
	 */
	public function adminNotices(): void { ?>
        <div class="<?php echo esc_attr( $this->notice_class ); ?>"><p><?php echo esc_html( $this->notice_message ); ?></p></div>
		<?php
	}

	/**
	 * @return void
	 */
	public function add_preview_mail_page(): void {
		//moved into submenu
		add_submenu_page( 'woocommerce',
			'WooCommerce Preview Emails',
			__( 'Preview Emails', 'woo-preview-emails' ),
			apply_filters( 'woo_preview_emails_min_capability', 'manage_options' ),
			'codemanas-woocommerce-preview-emails',
			[ $this, 'generate_the_admin_page' ]
		);
	}

	public function generate_the_admin_page(): void {
		$icon = plugins_url( '/images/wpe.png', WOO_PREVIEW_EMAILS_FILE );
		?>
        <div class="wrap">
            <h2 style="display:none">Placeholder to show messages</h2>
            <style>
                .woo-preview-emails-header {
                    display: flex;
                    align-items: center;
                    gap: 1em;
                    flex-wrap: wrap;
                }

                .woo-preview-emails-header #message,
                .woo-preview-emails-header .notice {
                    width: 100%;
                }
            </style>
            <div class="woo-preview-emails-header">
                <img src="<?php echo esc_url( $icon ); ?>" alt="Woo Preview Emails" width="60px" height="60px"/>
                <h2>Woo Preview Emails</h2>
            </div>
			<?php
			if ( ! in_array( 'woo-preview-emails-pro/woo-preview-emails-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				?>
                <div id="message" class="notice notice-success is-dismissible">
                    <h3><?php esc_html_e( 'Need more features', 'woo-preview-emails' ); ?> ?</h3>
                    <p>
                        <a href="https://www.codemanas.com/downloads/preview-e-mails-for-woocommerce-pro">Check out the
                            pro version here</a> which lets you view WooCommerce Booking and WooCommerce Subscription
                        templates.</p>
                </div>
			<?php } ?>
			<?php $this->generate_form(); ?>
			<?php include_once WOO_PREVIEW_EMAILS_DIR . '/views/promotions.php' ?>
        </div>
		<?php
	}

	public function generate_form(): void {
		$this->choose_email = isset( $_POST['choose_email'] ) ? sanitize_text_field( $_POST['choose_email'] ) : '';
		$orderID            = isset( $_POST['orderID'] ) ? sanitize_text_field( $_POST['orderID'] ) : '';
		$recipient_email    = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$email_type         = filter_input( INPUT_POST, 'email_type' );
		$email_type         = ! empty( $email_type ) ? $email_type : 'html';

		if ( is_admin() && isset( $_POST['preview_email'] ) ) {
			load_template( WOO_PREVIEW_EMAILS_DIR . '/views/form.php', true,
				[
					'emails'       => $this->emails,
					'orderID'      => $orderID,
					'recipient'    => $recipient_email,
					'choose_email' => $this->choose_email,
					'email_type'   => $email_type,
				] );
		} else {
			do_action( 'woo_preview_emails_before_form' );
			//Custom tab implementation
			$tabs = apply_filters( 'woo_preview_emails_tabs', false );
			if ( ! $tabs ) {
				load_template( WOO_PREVIEW_EMAILS_DIR . '/views/form.php', true,
					[
						'emails'       => $this->emails,
						'orderID'      => $orderID,
						'recipient'    => $recipient_email,
						'choose_email' => $this->choose_email,
						'email_type'   => $email_type,
					] );
			}
			do_action( 'woo_preview_emails_after_form' );
		}
	}

	/**
	 * Verify if current request is an authorized preview request.
	 *
	 * @return bool
	 */
	private function is_valid_preview_request(): bool {
		if ( ! is_admin() || ! isset( $_POST['preview_email'] ) ) {
			return false;
		}

		$preview_email = sanitize_text_field( wp_unslash( $_POST['preview_email'] ) );
		if ( ! wp_verify_nonce( $preview_email, 'woocommerce_preview_email' ) ) {
			return false;
		}

		if ( ! current_user_can( apply_filters( 'woo_preview_emails_min_capability', 'manage_options' ) ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to preview emails.', 'woo-preview-emails' ), '', [ 'response' => 403 ] );
		}

		return true;
	}

	/**
	 * Check if email can be previewed based on chosen email and order ID.
	 *
	 * @param   string  $choose_email
	 * @param   int     $order_id
	 *
	 * @return bool
	 */
	private function can_preview_email( string $choose_email, int $order_id ): bool {
		if ( empty( $choose_email ) || ! isset( $this->emails[ $choose_email ] ) ) {
			return false;
		}

		// Needs to be called to get shipping and payment gateways data.
		if ( function_exists( 'WC' ) ) {
			WC()->payment_gateways();
			WC()->shipping();
		}

		$is_user_email = in_array( $choose_email, [ 'WC_Email_Customer_New_Account', 'WC_Email_Customer_Reset_Password' ], true );

		return $is_user_email || $order_id > 0;
	}

	/**
	 * Get the path of the template file currently in use.
	 *
	 * @param   \WC_Email  $current_email
	 *
	 * @return string
	 */
	private function get_template_file_path( \WC_Email $current_email ): string {
		$template      = $current_email->get_template( 'template_html' );
		$local_file    = $current_email->get_theme_template_file( $template );
		$core_file     = $current_email->template_base . $template;
		$template_file = apply_filters( 'woocommerce_locate_core_template', $core_file, $template, $current_email->template_base, $current_email->id );
		$template_dir  = apply_filters( 'woocommerce_template_directory', 'woocommerce', $template );

		if ( file_exists( $local_file ) ) {
			return trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template;
		}

		return plugin_basename( $template_file );
	}

	/**
	 * Trigger email with appropriate arguments based on email type.
	 *
	 * @param   \WC_Email  $current_email
	 * @param   string     $email_class
	 * @param   int        $order_id
	 *
	 * @return void
	 */
	private function trigger_email( \WC_Email $current_email, string $email_class, int $order_id ): void {
		$additional_data = apply_filters( 'woo_preview_additional_orderID', false, $email_class, $order_id, $current_email );

		if ( $additional_data ) {
			do_action( 'woo_preview_additional_order_trigger', $current_email, $additional_data );

			return;
		}

		if ( $email_class === 'WC_Email_Customer_Note' ) {
			$customer_note = 'This is some customer note , just some dummy text nothing to see here';
			$current_email->trigger( [
				'order_id'      => $order_id,
				'customer_note' => $customer_note,
			] );
		} elseif ( $email_class === 'WC_Email_Customer_New_Account' ) {
			$current_email->trigger( get_current_user_id() );
		} elseif ( str_starts_with( $email_class, 'WCS_Email' ) && class_exists( 'WC_Subscription' ) && is_subclass_of( $current_email, 'WC_Email' ) && function_exists( 'wcs_get_subscriptions_for_order' ) ) {
			$order_subscriptions = wcs_get_subscriptions_for_order( $order_id );
			if ( ! empty( $order_subscriptions ) && ! in_array( $current_email->id, [ 'customer_payment_retry', 'payment_retry' ], true ) ) {
				$subscription = array_pop( $order_subscriptions );
				$current_email->trigger( $subscription );
			} else {
				$current_email->trigger( $order_id, wc_get_order( $order_id ) );
			}
		} else {
			$current_email->trigger( $order_id );
		}
	}

	/**
	 * Generate formatted email content for preview.
	 *
	 * @param   \WC_Email  $current_email
	 * @param   string     $email_type
	 * @param   string     $email_class
	 * @param   int        $order_id
	 *
	 * @return string
	 */
	private function generate_email_content( \WC_Email $current_email, string $email_type, string $email_class, int $order_id ): string {
		add_filter( 'woocommerce_email_recipient_' . $current_email->id, [ $this, 'no_recipient' ] );
		// Since WooCommerce 5.0.0 - we require this to make sure emails are resent.
		add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );

		$this->trigger_email( $current_email, $email_class, $order_id );

		$current_email->email_type = $email_type;
		$content                   = $current_email->get_content();
		$content                   = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );

		remove_filter( 'woocommerce_email_recipient_' . $current_email->id, [ $this, 'no_recipient' ] );
		remove_filter( 'woocommerce_new_order_email_allows_resend', '__return_true', 10 );

		return $content;
	}

	/**
	 * Render the preview page view and terminate execution.
	 *
	 * @param   \WC_Email  $current_email
	 * @param   string     $currently_used_template
	 * @param   string     $email_type
	 * @param   string     $content
	 *
	 * @return void
	 */
	private function render_preview_view( \WC_Email $current_email, string $currently_used_template, string $email_type, string $content ): void {
		load_template(
			WOO_PREVIEW_EMAILS_DIR . '/views/result/preview.php',
			true,
			[
				'current_email'           => $current_email,
				'currently_used_template' => $currently_used_template,
				'email_type'              => $email_type,
				'content'                 => $content,
				'main'                    => $this,
				'plugin_url'              => $this->plugin_url,
			]
		);
		die;
	}

	/**
	 * Handle email preview rendering on form submission.
	 *
	 * @return void
	 */
	public function email_preview_output(): void {
		if ( ! $this->is_valid_preview_request() ) {
			return;
		}

		$order_id     = absint( ! empty( $_POST['search_order'] ) ? $_POST['search_order'] : ( $_POST['orderID'] ?? 0 ) );
		$choose_email = sanitize_text_field( $_POST['choose_email'] ?? '' );
		$email_type   = sanitize_text_field( filter_input( INPUT_POST, 'email_type' ) ?: 'html' );

		if ( ! $this->can_preview_email( $choose_email, $order_id ) ) {
			$this->notice_message = __( 'Please specify both Order and Email', 'woo-preview-emails' );
			$this->notice_class   = 'error';
			add_action( 'admin_notices', [ $this, 'adminNotices' ] );

			return;
		}

		do_action( 'woo_preview_emails_before_email_render', $_POST );

		$this->plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );
		$recipient_email  = sanitize_text_field( $_POST['email'] ?? '' );
		$this->recipient  = is_email( $recipient_email ) ? $recipient_email : '';

		$current_email           = $this->emails[ $choose_email ];
		$currently_used_template = $this->get_template_file_path( $current_email );
		$content                 = $this->generate_email_content( $current_email, $email_type, $choose_email, $order_id );

		$this->render_preview_view( $current_email, $currently_used_template, $email_type, $content );
	}

	public function no_recipient( $recipient ): string {
		if ( $this->recipient != '' ) {
			$recipient = $this->recipient;
		} else {
			$recipient = '';
		}

		return $recipient;
	}

}

Main::get_instance();
