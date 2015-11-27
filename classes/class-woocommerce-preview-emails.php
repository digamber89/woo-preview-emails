<?php
if( !class_exists('WooCommercePreviewEmails') ):
class WooCommercePreviewEmails{
	/**
	 * Instance of this class.
	 * @var object
	 */
	protected static $instance = null;
	/**
	 * Return an instance of this class.
	 * @return object A single instance of this class.
	 */
	public $emails;
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function __construct(){
		add_action('init', array($this, 'load'), 10 );
		add_action('wp_loaded', array($this, 'generate_result'), 20 );
		add_action('admin_menu', array($this, 'menu_page') );
	}
	public function menu_page(){
		add_menu_page( 'WooCommerce Preview Emails',
					   'Preview Emails',
					   'manage_options',
					   'digthis-woocommerce-preview-emails',
					   array($this,'generate_page')
					   );
	}
	public function generate_page(){
	?>
		<div class="wrap">
			<?php $this->generate_form(); ?>
		</div>
	<?php
	}

	public function previewEmail($orderID = NULL, $template = NULL, $args = NULL){
		if( !empty($orderID) || !empty($template) || !empty($args) ){
		$order = new WC_Order($orderID);
		?>
		<style><?php wc_get_template( 'emails/email-styles.php' ); ?></style>
		<?php
		 	wc_get_template( 'emails/email-header.php', $args );
	 	 	wc_get_template( $template, array( 'order' => $order ) );
		 	wc_get_template( 'emails/email-footer.php' ); 
		}
	}

	public function load(){
		$wooDir = plugin_dir_path( WC_PLUGIN_FILE );
		require_once( $wooDir.'includes/emails/class-wc-email.php' );
		$emails['WC_Email_New_Order']                 		= include( $wooDir.'includes/emails/class-wc-email-new-order.php' );
		$emails['WC_Email_Cancelled_Order']           		= include( $wooDir.'includes/emails/class-wc-email-cancelled-order.php' );
		$emails['WC_Email_Customer_Processing_Order'] 		= include( $wooDir.'includes/emails/class-wc-email-customer-processing-order.php' );
		$emails['WC_Email_Customer_Completed_Order']  		= include( $wooDir.'includes/emails/class-wc-email-customer-completed-order.php' );
		$emails['WC_Email_Customer_Refunded_Order']   		= include( $wooDir.'includes/emails/class-wc-email-customer-refunded-order.php' );
		$emails['WC_Email_Customer_Invoice']          		= include( $wooDir.'includes/emails/class-wc-email-customer-invoice.php' );
		$emails['WC_Email_Customer_Note']             		= include( $wooDir.'includes/emails/class-wc-email-customer-note.php' );
		$emails['WC_Email_Customer_Reset_Password']   		= include( $wooDir.'includes/emails/class-wc-email-customer-reset-password.php' );
		$emails['WC_Email_Customer_New_Account']      		= include( $wooDir.'includes/emails/class-wc-email-customer-new-account.php' );
		$emails = apply_filters( 'woocommerce_email_classes', $emails );
		$this->emails = $emails;
	}

	

	public function generate_form(){
		 ?>
		<form id="woocommerce-preview-email" action="" method="post">
		<?php 
			wp_nonce_field( 'woocommerce_preview_email', 'preview_email'); ?>
			<select name="choose_email">
			<?php foreach($this->emails as $index => $email):	?>
				<option value="<?php echo $index ?>"><?php echo $email->title; ?></option>
			<?php endforeach; ?>
			</select>
		<?php
			$args = array(
				'post_type' => 'shop_order',
				'posts_per_page' => -1,
				'post_status' => array_keys( wc_get_order_statuses() )
			);
		?>	
			<select name="orderID">
			<?php
				$orders = get_posts($args);
				foreach($orders as $order){
			?>
 				<option value="<?php echo $order->ID ?>"><?php echo $order->post_title; ?></option>
 			<?php }	?>
		</select>
		<input type="submit" name="submit" class="button button-large">
		</form>
	<?php
	}

	public function generate_result(){
		if( isset( $_POST['preview_email'] ) && wp_verify_nonce( $_POST['preview_email'] , 'woocommerce_preview_email' ) ):
				?>
				<style type="text/css">
					#tool-options{
						position: absolute;
						top:0;
						left:50%;
					}
				</style>
				<?php
				echo '<div id="tool-options">';
					$this->generate_form();
					/*Design Required*/
					#echo '<a href="'.admin_url().'">Back </a>';
				echo '</div>';
				$orderID = $_POST['orderID'];
				$index = $_POST['choose_email'];
				$current_email = $this->emails[$index];
				$html_template = $current_email->template_html;
				$email_heading = $current_email->heading;
				$args = array('email_heading' => $email_heading);
				$this->previewEmail( $orderID, $html_template, $args );
				die;
		endif;
	}
}
add_action('plugins_loaded', array('WooCommercePreviewEmails','get_instance'));
endif;
?>