<?php
/*
 * Main Class to handle preview emails*/
if( !class_exists('WooCommercePreviewEmails') ):
class WooCommercePreviewEmails{
	/**
	 * Instance of this class.
	 * @var object
	 */
	protected static $instance = null;
	private $recipient = '';
	/**
	 * Return an instance of this class.
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
	
	public function __construct(){
		add_action('init', array($this, 'load'), 10 );
		add_action('admin_init', array($this, 'generate_result'), 20 );
		add_action('admin_menu', array($this, 'menu_page'),90 );
		add_action( 'admin_enqueue_scripts', array($this, 'load_scripts'), 10, 1 );
		add_action('wp_ajax_woo_preview_orders_search', array($this,'woo_preview_orders_search') );
	}

	/*Ajax Callback to Search Orders*/
	public function woo_preview_orders_search(){
			
			$q = filter_input(INPUT_GET, 'q');
			
			$args = array(
						'post_type' => 'shop_order',
						'posts_per_page' => 10,
						'post_status' => array_keys( wc_get_order_statuses() ),
						'post__in' => array($q)
					);
			$response = array();
			$orders = new WP_Query($args);
			
			while( $orders->have_posts() ):
				$orders->the_post();
				$id    = get_the_id();
				$response[] = array('id' => $id, 'text' => '#order :'.$id );
			endwhile;
			
			wp_reset_postdata();
			
			wp_send_json( $response );
	}

	/**
	 * load woo preview scripts
	 * @param  [type] $hook [admin page suffix]
	 */
	public function load_scripts( $hook ) {

		if( $hook != 'woocommerce_page_digthis-woocommerce-preview-emails' ){
			return;
		}
		$my_plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );
		wp_register_style( 'woo-preview-email-select2-css', $my_plugin_url.'/assets/css/select2.min.css' );
		wp_register_script( 'woo-preview-email-select2-js', $my_plugin_url.'/assets/js/select2.min.js', array('jquery'), '', true );
		
		wp_enqueue_style( 'woo-preview-email-select2-css' );
		wp_enqueue_script( 'woo-preview-email-select2-js' );
	}

	public function load(){
		
		$page = filter_input(INPUT_GET, 'page'); 
		
		if( class_exists('WC_Emails') && $page == 'digthis-woocommerce-preview-emails' ) {
			
			$wc_emails = WC_Emails::instance();
			$emails = $wc_emails->get_emails();
			if( !empty($emails) )
				$this->emails = $emails;
		}

	}

	public function adminNotices(){
		 echo"<div class=\"$this->notice_class\"> <p>$this->notice_message</p></div>";
	}
	
	public function menu_page(){
		//moved into submenu
		add_submenu_page( 
			              'woocommerce',
			              'WooCommerce Preview Emails',
			              'Preview Emails',
			              apply_filters('woo_preview_emails_min_capability','manage_options'),
			              'digthis-woocommerce-preview-emails',
			              array($this,'generate_page')
			             );
	}
	
	public function generate_page(){ 
		?>
		<div class="wrap">
			<h2>Woo Preview Emails</h2><hr />
			<p class="description"> If you have found this plugin useful, please leave a <a href="https://wordpress.org/support/plugin/woo-preview-emails/reviews/#new-post" target="_blank">review</a>
			<p class="description"><?php _e("Note: E-mails require orders to exist before you can preview them",'woo-preview-emails'); ?></p>
			<?php $this->generate_form(); ?>
		</div>
		<?php 
	}

	public function generate_form(){
		 $choose_email = isset($_POST['choose_email'])?$_POST['choose_email']:'';
		 $orderID = isset($_POST['orderID'])?$_POST['orderID']:'';
		 $recipient_email = isset($_POST['email'])?$_POST['email']:'';
		 require_once WOO_PREVIEW_EMAILS_DIR.'/includes/views/form.php';
	}

	public function generate_result(){

		if( is_admin() && isset( $_POST['preview_email'] ) && wp_verify_nonce( $_POST['preview_email'] , 'woocommerce_preview_email' ) ):
			$condition = false;
			$wc_payment_gateways = WC_Payment_Gateways::instance();
					if( isset($_POST['choose_email']) 

						&& ( $_POST['choose_email'] == 'WC_Email_Customer_New_Account' || $_POST['choose_email'] == 'WC_Email_Customer_Reset_Password' ) 

						){
						$condition = true;

					}elseif(  ( ( isset($_POST['orderID']) && !empty($_POST['orderID']) ) || ( isset($_POST['search_order']) && !empty($_POST['search_order']) ) ) && ( isset($_POST['choose_email']) && !empty($_POST['choose_email']) ) ){
						$condition = true;
					}

			if( $condition == true) {
				$my_plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );
			
			/*Load the styles and scripts*/
			require_once WOO_PREVIEW_EMAILS_DIR.'/includes/views/result/style.php';
			require_once WOO_PREVIEW_EMAILS_DIR.'/includes/views/result/scripts.php';

			    /*Make Sure serached order is selected */
				$orderID 		 = absint( !empty($_POST['search_order'])? $_POST['search_order'] : $_POST['orderID'] );
				$index  	 	 = esc_attr( $_POST['choose_email'] );
				$recipeint_email = $_POST['email'];
				
				if( is_email( $recipeint_email ) ) {
					$this->recipient = $_POST['email'];
				} else {
					$this->recipient = '';
				}
				
				$current_email = $this->emails[$index];
				
				/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
				add_filter( 'woocommerce_email_recipient_' . $current_email->id, array($this,'no_recipient') );
				

				if( $index === 'WC_Email_Customer_Note' ) {
					/* customer note needs to be added*/	
					$customer_note = 'This is some customer note , just some dummy text nothing to see here';
					$args = array(
									'order_id'      => $orderID,
									'customer_note' => $customer_note
								 );
					$current_email->trigger($args);

				} else if ( $index === 'WC_Email_Customer_New_Account' ) {
					
					$user_id = get_current_user_id();
					$current_email->trigger($user_id);

				} else {
					
					$current_email->trigger($orderID);

				}

				$content = $current_email->get_content_html();
				$content = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );
				echo $content;
				/*This ends the content for email to be previewed*/
				/*Loading Toolbar to display for multiple email templates*/

				/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
				remove_filter( 'woocommerce_email_recipient_' . $current_email->id, array($this,'no_recipient') );
				?>
				<div id="tool-options">
					<div id="tool-wrap">
					<p>
						<strong>Currently Viewing Template File: </strong>
						<?php echo $current_email->template_html; ?>
					</p>
					<p class="description">
						<strong> Descripton: </strong>
						<?php echo $current_email->description; ?></p>
					<?php	$this->generate_form(); ?>
					<!-- admin url was broken -->
					<a class="button" href="<?php echo admin_url(); ?>"><?php _e('Back to Admin Area','woo-preview-emails'); ?></a>
					<a href="#" id="show_menu" class="show_menu">Show Menu</a> 
					</div>
				</div>
				<?php
				die;
			}else{
				$this->notice_message = 'Please specify both Order and Email';
				$this->notice_class   = 'error';
				add_action( 'admin_notices', array($this,'adminNotices') ); 
			}
		endif;
	}

	public function no_recipient($recipient){
		
		if ($this->recipient != '') {
			$recipient = $this->recipient;
		} else {
			$recipient = '' ;
		}
		
		return $recipient;
	}

}
add_action('plugins_loaded', array('WooCommercePreviewEmails','get_instance'));

endif;