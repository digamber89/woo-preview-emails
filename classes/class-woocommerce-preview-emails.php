<?php
/*
 * Main Class to handle preview emails*/
if ( ! class_exists( 'WooCommercePreviewEmails' ) ):
	class WooCommercePreviewEmails {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;
		private $plugin_url, $choose_email, $orderID, $recipient;
		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public $emails = null, $notice_message = null, $notice_class = null;

		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );
			add_action( 'init', array( $this, 'load' ), 999 );
			add_action( 'admin_init', array( $this, 'generate_result' ), 20 );
			add_action( 'admin_menu', array( $this, 'menu_page' ), 90 );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 10, 1 );
			add_action( 'wp_ajax_woo_preview_orders_search', array( $this, 'woo_preview_orders_search' ) );
		}

		/*Ajax Callback to Search Orders*/
		public function woo_preview_orders_search() {

			$q = sanitize_text_field( filter_input( INPUT_GET, 'q' ) );

			$args     = array(
				'post_type'      => 'shop_order',
				'posts_per_page' => 10,
				'post_status'    => array_keys( wc_get_order_statuses() ),
				'post__in'       => array( $q )
			);
			$response = array();
			$orders   = new WP_Query( $args );

			while ( $orders->have_posts() ):
				$orders->the_post();
				$id         = get_the_id();
				$response[] = array( 'id' => $id, 'text' => '#order :' . $id );
			endwhile;

			wp_reset_postdata();

			wp_send_json( $response );
		}

		/**
		 * load woo preview scripts
		 *
		 * @param  [type] $hook [admin page suffix]
		 */
		public function load_scripts( $hook ) {

			if ( $hook != 'woocommerce_page_digthis-woocommerce-preview-emails' ) {
				return;
			}
			wp_register_style( 'woo-preview-email-select2-css', $this->plugin_url . '/assets/css/select2.min.css' );
			wp_register_script( 'woo-preview-email-select2-js', $this->plugin_url . '/assets/js/select2.min.js', array( 'jquery' ), '', true );

			wp_enqueue_style( 'woo-preview-email-select2-css' );
			wp_enqueue_script( 'woo-preview-email-select2-js' );
		}

		public function load() {

			$page = filter_input( INPUT_GET, 'page' );

			if ( class_exists( 'WC_Emails' ) && $page == 'digthis-woocommerce-preview-emails' ) {

				$wc_emails = WC_Emails::instance();
				$emails    = $wc_emails->get_emails();
				if ( ! empty( $emails ) ) {
					//Filtering out booking emails becuase it won't work from this plugin
					//Buy PRO version if you need this capability
					$unset_booking_emails = array(
						'WC_Email_New_Booking',
						'WC_Email_Booking_Reminder',
						'WC_Email_Booking_Confirmed',
						'WC_Email_Booking_Notification',
						'WC_Email_Booking_Cancelled',
						'WC_Email_Admin_Booking_Cancelled',
						'WC_Email_Booking_Pending_Confirmation'
					);

					//Filtering out subscription emails becuase it won't work from this plugin
					//Buy PRO version if you need this capability
					$unset_subscription_emails = array(
						'WCS_Email_New_Renewal_Order',
						'WCS_Email_New_Switch_Order',
						'WCS_Email_Processing_Renewal_Order',
						'WCS_Email_Completed_Renewal_Order',
						'WCS_Email_Completed_Switch_Order',
						'WCS_Email_Customer_Renewal_Invoice',
						'WCS_Email_Cancelled_Subscription',
						'WCS_Email_Expired_Subscription',
						'WCS_Email_On_Hold_Subscription'
					);

					//Filtering out membership emails becuase it won't work from this plugin
					//Buy PRO version if you need this capability
					$unset_membership_emails = array(
						'WC_Memberships_User_Membership_Note_Email',
						'WC_Memberships_User_Membership_Ending_Soon_Email',
						'WC_Memberships_User_Membership_Ended_Email',
						'WC_Memberships_User_Membership_Renewal_Reminder_Email',
					);

					$unset_booking_emails      = apply_filters( 'woo_preview_emails_unset_booking_emails', $unset_booking_emails );
					$unset_subscription_emails = apply_filters( 'woo_preview_emails_unset_subscription_emails', $unset_subscription_emails );
					$unset_membership_emails   = apply_filters( 'woo_preview_emails_unset_memebership_emails', $unset_membership_emails );

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

		public function adminNotices() {
			echo "<div class=\"$this->notice_class\"><p>$this->notice_message</p></div>";
		}

		public function menu_page() {
			//moved into submenu
			add_submenu_page( 'woocommerce', 'WooCommerce Preview Emails', __( 'Preview Emails', 'woo-preview-emails' ), apply_filters( 'woo_preview_emails_min_capability', 'manage_options' ), 'digthis-woocommerce-preview-emails', array( $this, 'generate_page' ) );
		}

		public function generate_page() {
			?>
            <div class="wrap">
                <h2>Woo Preview Emails</h2>
				<?php
				if ( ! in_array( 'woo-preview-emails-pro-addon/woo-preview-emails-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					?>
                    <div id="message" class="notice notice-warning">
                        <h3>Need more features ?</h3>
                        <p>
                            <a href="https://www.codemanas.com/downloads/preview-e-mails-for-woocommerce-pro">Check out the pro version here</a> which lets you view WooCommerce Booking and WooCommerce Subscription templates.</p>
                    </div>
                    <div id="message" class="notice notice-warning">
                        <p>If you have found this plugin useful, please leave a <a href="https://wordpress.org/support/plugin/woo-preview-emails/reviews/#new-post" target="_blank">review</a>
                        <p><strong><?php _e( "Note: E-mails require orders to exist before you can preview them", 'woo-preview-emails' ); ?></strong></p>
                    </div>
				<?php } ?>

				<?php $this->generate_form(); ?>
            </div>
			<?php
		}

		public function generate_form() {
			$this->choose_email = isset( $_POST['choose_email'] ) ? sanitize_text_field( $_POST['choose_email'] ) : '';
			$this->orderID      = isset( $_POST['orderID'] ) ? sanitize_text_field( $_POST['orderID'] ) : '';
			$recipient_email    = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';

			if ( is_admin() && isset( $_POST['preview_email'] ) ) {
				require_once WOO_PREVIEW_EMAILS_DIR . '/views/form.php';
			} else {
				do_action( 'woo_preview_emails_before_form' );

				//Custom tab implmentation
				$tabs = apply_filters( 'woo_preview_emails_tabs', false );
				if ( ! $tabs ) {
					require_once WOO_PREVIEW_EMAILS_DIR . '/views/form.php';
				}

				do_action( 'woo_preview_emails_after_form' );
			}
		}

		public function generate_result() {

			if ( is_admin() && isset( $_POST['preview_email'] ) && wp_verify_nonce( $_POST['preview_email'], 'woocommerce_preview_email' ) ):
				$condition = false;
				WC()->payment_gateways();
				WC()->shipping();
				if ( isset( $_POST['choose_email'] ) && ( $_POST['choose_email'] == 'WC_Email_Customer_New_Account' || $_POST['choose_email'] == 'WC_Email_Customer_Reset_Password' ) ) {
					$condition = true;
				} elseif ( ( ( isset( $_POST['orderID'] ) && ! empty( $_POST['orderID'] ) ) || ( isset( $_POST['search_order'] ) && ! empty( $_POST['search_order'] ) ) ) && ( isset( $_POST['choose_email'] ) && ! empty( $_POST['choose_email'] ) ) ) {
					$condition = true;
				}

				if ( $condition == true ) {
					$this->plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );

					/*Load the styles and scripts*/
					require_once WOO_PREVIEW_EMAILS_DIR . '/views/result/style.php';
					require_once WOO_PREVIEW_EMAILS_DIR . '/views/result/scripts.php';

					/*Make Sure serached order is selected */
					$orderID         = absint( ! empty( $_POST['search_order'] ) ? $_POST['search_order'] : $_POST['orderID'] );
					$index           = sanitize_text_field( $_POST['choose_email'] );
					$recipient_email = sanitize_text_field( $_POST['email'] );

					if ( is_email( $recipient_email ) ) {
						$this->recipient = $recipient_email;
					} else {
						$this->recipient = '';
					}

					$current_email = $this->emails[ $index ];
					/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
					add_filter( 'woocommerce_email_recipient_' . $current_email->id, [ $this, 'no_recipient' ] );
					// Since WooCommerce 5.0.0 - we require this to make sure emails are resent
					add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );

					$additional_data = apply_filters( 'woo_preview_additional_orderID', false, $index, $orderID, $current_email );
					if ( $additional_data ) {
						do_action( 'woo_preview_additional_order_trigger', $current_email, $additional_data );
					} else {
						if ( $index === 'WC_Email_Customer_Note' ) {
							/* customer note needs to be added*/
							$customer_note = 'This is some customer note , just some dummy text nothing to see here';
							$args          = array(
								'order_id'      => $orderID,
								'customer_note' => $customer_note
							);
							$current_email->trigger( $args );

						} else if ( $index === 'WC_Email_Customer_New_Account' ) {
							$user_id = get_current_user_id();
							$current_email->trigger( $user_id );
						} else if ( strpos( $index, 'WCS_Email' ) === 0 && class_exists( 'WC_Subscription' ) && is_subclass_of( $current_email, 'WC_Email' ) ) {
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

					$content = $current_email->get_content_html();
					$content = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );
					echo $content;
					/*This ends the content for email to be previewed*/
					/*Loading Toolbar to display for multiple email templates*/

					/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
					remove_filter( 'woocommerce_email_recipient_' . $current_email->id, [ $this, 'no_recipient' ] );
					remove_filter( 'woocommerce_new_order_email_allows_resend', '__return_true', 10 );
					?>
                    <div id="tool-options">
                        <div id="tool-wrap">
                            <p>
                                <strong>Currently Viewing Template File: </strong><br/>
								<?php echo wc_locate_template( $current_email->template_html ); ?>
                            </p>
                            <p class="description">
                                <strong> Descripton: </strong>
								<?php echo $current_email->description; ?>
                            </p>
							<?php $this->generate_form(); ?>
                            <!-- admin url was broken -->
                            <a class="button" href="<?php echo admin_url( 'admin.php?page=digthis-woocommerce-preview-emails' ); ?>"><?php _e( 'Back to Admin Area', 'woo-preview-emails' ); ?></a>
                        </div>
                    </div>
                    <div class="menu-toggle-wrapper">
                        <a href="#" id="show_menu" class="show_menu">Show Menu</a>
                    </div>
					<?php
					die;
				} else {
					$this->notice_message = 'Please specify both Order and Email';
					$this->notice_class   = 'error';
					add_action( 'admin_notices', array( $this, 'adminNotices' ) );
				}
			endif;
		}

		public function no_recipient( $recipient ) {
			if ( $this->recipient != '' ) {
				$recipient = $this->recipient;
			} else {
				$recipient = '';
			}

			return $recipient;
		}

	}

	add_action( 'plugins_loaded', array( 'WooCommercePreviewEmails', 'get_instance' ) );

endif;
