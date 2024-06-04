<?php

namespace Codemanas\WooPreviewEmails;

class Main {
	public static ?Main $instance = null;
	private string $recipient;
	private string $plugin_url;
	public $emails = null;
	public $notice_message = null;
	public $notice_class = null;
	private string $choose_email;

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
	public function hpos_compatible() {
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
	public function load_email_classes() {
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
	public function load_scripts( $hook ) {
		if ( $hook != 'woocommerce_page_codemanas-woocommerce-preview-emails' ) {
			return;
		}

		$assets_file = include WOO_PREVIEW_EMAILS_DIR . '/assets/main.asset.php';


		wp_register_script( 'woo-preview-emails__main', $this->plugin_url . '/assets/main.js', $assets_file['dependencies'], $assets_file['version'], true );
		wp_enqueue_script( 'woo-preview-emails__main' );

		wp_register_style( 'woo-preview-emails__vendor', $this->plugin_url . '/assets/main.css', $assets_file['dependencies'], $assets_file['version'] );
		wp_enqueue_style( 'woo-preview-emails__vendor' );

		wp_register_style( 'woo-preview-emails__style', $this->plugin_url . '/assets/style-main.css', $assets_file['dependencies'], $assets_file['version'] );
		wp_enqueue_style( 'woo-preview-emails__style' );
	}

	/**
	 * @return void'
	 */
	public function adminNotices() {
		?>
        <div class="<?php echo $this->notice_class; ?>"><p><?php echo $this->notice_message; ?></p></div>
		<?php
	}

	/**
	 * @return void
	 */
	public function add_preview_mail_page() {
		//moved into submenu
		add_submenu_page( 'woocommerce',
			'WooCommerce Preview Emails',
			__( 'Preview Emails', 'woo-preview-emails' ),
			apply_filters( 'woo_preview_emails_min_capability', 'manage_options' ),
			'codemanas-woocommerce-preview-emails',
			[ $this, 'generate_the_admin_page' ]
		);
	}

	public function generate_the_admin_page() {
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
                    <h3><?php _e( 'Need more features', 'woo-preview-emails' ); ?> ?</h3>
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

	public function generate_form() {
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

	public function email_preview_output() {

		$preview_email = filter_input( INPUT_POST, 'preview_email' );
		$choose_email  = filter_input( INPUT_POST, 'choose_email' );
		$order_id      = filter_input( INPUT_POST, 'orderID' );
		$search_order  = filter_input( INPUT_POST, 'search_order' );
		$email_type    = filter_input( INPUT_POST, 'email_type' );
		$email_type    = ! empty( $email_type ) ? $email_type : 'html';
		$order_id      = ! empty( $search_order ) ? $search_order : $order_id;

		if ( is_admin() && wp_verify_nonce( $preview_email, 'woocommerce_preview_email' ) ):
			$show_email = false;

			//needs to be called to get shipping and payment gateways data
			WC()->payment_gateways();
			WC()->shipping();

			if ( ( $choose_email == 'WC_Email_Customer_New_Account' || $choose_email == 'WC_Email_Customer_Reset_Password' ) ) {
				$show_email = true;
			} elseif ( ( ! empty( $_POST['orderID'] ) || ! empty( $_POST['search_order'] ) ) && ( ! empty( $choose_email ) ) ) {
				$show_email = true;
			}

			if ( $show_email ) {
				do_action( 'woo_preview_emails_before_email_render', $_POST );
				$this->plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );
				/*Make Sure searched order is selected */
				$orderID         = absint( ! empty( $_POST['search_order'] ) ? $_POST['search_order'] : $_POST['orderID'] );
				$index           = sanitize_text_field( $_POST['choose_email'] );
				$recipient_email = sanitize_text_field( $_POST['email'] );


				if ( is_email( $recipient_email ) ) {
					$this->recipient = $recipient_email;
				} else {
					$this->recipient = '';
				}

				$current_email = $this->emails[ $index ];
				//template
				$template                = $current_email->get_template( 'template_html' );
				$local_file              = $current_email->get_theme_template_file( $template );
				$core_file               = $current_email->template_base . $template;
				$template_file           = apply_filters( 'woocommerce_locate_core_template', $core_file, $template, $current_email->template_base, $current_email->id );
				$template_dir            = apply_filters( 'woocommerce_template_directory', 'woocommerce', $template );
				$base_template_location  = plugin_basename( $template_file );
				$currently_used_template = file_exists( $local_file ) ? trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template : $base_template_location;


				/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
				add_filter( 'woocommerce_email_recipient_' . $current_email->id, [ $this, 'no_recipient' ] );

				// Since WooCommerce 5.0.0 - we require this to make sure emails are resent
				add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );
				$additional_data = apply_filters( 'woo_preview_additional_orderID', false, $index, $orderID, $current_email );

				//@todo make this more elegant
				if ( $additional_data ) {
					do_action( 'woo_preview_additional_order_trigger', $current_email, $additional_data );
				} else {
					if ( $index === 'WC_Email_Customer_Note' ) {
						/* customer note needs to be added*/
						$customer_note = 'This is some customer note , just some dummy text nothing to see here';
						$args          = array(
							'order_id'      => $orderID,
							'customer_note' => $customer_note,
						);
						$current_email->trigger( $args );

					} elseif ( $index === 'WC_Email_Customer_New_Account' ) {
						$user_id = get_current_user_id();
						$current_email->trigger( $user_id );
					} elseif ( strpos( $index, 'WCS_Email' ) === 0 && class_exists( 'WC_Subscription' ) && is_subclass_of( $current_email, 'WC_Email' ) && function_exists( 'wcs_get_subscriptions_for_order' ) ) {
						/* Get the subscriptions for the selected order */
						$order_subscriptions = wcs_get_subscriptions_for_order( $orderID );
						if ( ! empty( $order_subscriptions ) && $current_email->id != 'customer_payment_retry' && $current_email->id != 'payment_retry' ) {
							/* Pick the first one as an example */
							$subscription = array_pop( $order_subscriptions );
							$current_email->trigger( $subscription );
						} else {
							$current_email->trigger( $orderID, wc_get_order( $orderID ) );
						}
					} else {
						$current_email->trigger( $orderID );
					}
				}

				//set the type of email:
				$current_email->email_type = $email_type;
				$content                   = $current_email->get_content();
				$content                   = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );

				/*This ends the content for email to be previewed*/
				/*Loading Toolbar to display for multiple email templates*/
				/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
				remove_filter( 'woocommerce_email_recipient_' . $current_email->id, [ $this, 'no_recipient' ] );
				remove_filter( 'woocommerce_new_order_email_allows_resend', '__return_true', 10 );
				?>
                <!DOCTYPE html>
                <html lang="<?php echo esc_attr( get_locale() ); ?>">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title><?php _e( 'Previewing Emails', 'woo-preview-emails' ); ?></title>
					<?php
					/*Load the styles and scripts*/
					require_once WOO_PREVIEW_EMAILS_DIR . '/views/result/style.php';
					require_once WOO_PREVIEW_EMAILS_DIR . '/views/result/scripts.php';
					?>
                </head>
                <body>
                <div class="cm-WooPreviewEmail">
                    <div id="tool-options">
                        <div id="tool-wrap">
                            <div style="text-align: left">
                                <a class="button"
                                   style="text-align: left"
                                   href="<?php echo admin_url( 'admin.php?page=codemanas-woocommerce-preview-emails' ); ?>"><< <?php _e( 'Back to Admin Area', 'woo-preview-emails' ); ?></a>
                            </div>
                            <p>
                                <strong>Viewing Template File: </strong><br/>
								<?php echo esc_html( $currently_used_template ); ?>
                            </p>
                            <p class="description">
                                <strong> Description: </strong>
								<?php echo $current_email->description; ?>
                            </p>
							<?php $this->generate_form(); ?>
                        </div>
                    </div>
                    <div class="cm-WooPreviewEmail-emailContent cm-WooPreviewEmail-emailContent__<?php echo esc_attr( $email_type ); ?>"><?php echo $content; ?></div>
                    <div class="tool-bar-toggler">
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                            </svg>
                            <span class="hide-controls">Hide Controls</span>
                            <span class="show-controls">Show Controls</span>
                        </a>
                    </div>
                </div>

                </body>
                </html>
				<?php
				die;
			} else {
				$this->notice_message = 'Please specify both Order and Email';
				$this->notice_class   = 'error';
				add_action( 'admin_notices', array( $this, 'adminNotices' ) );
			}
		endif;
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
