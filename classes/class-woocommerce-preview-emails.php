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
		add_action('wp_loaded', array($this, 'generate_result'), 20 );
		add_action('admin_menu', array($this, 'menu_page') );
	}
	public function load(){
		if( class_exists('WC_Emails') ){
			$wc_emails = WC_Emails::instance();
			$emails = $wc_emails->get_emails();
			if( !empty($emails) )
				$this->emails = $emails;
		}

	}

	function adminNotices(){
		 echo"<div class=\"$this->notice_class\"> <p>$this->notice_message</p></div>";
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
	 	 	wc_get_template( $template, array( 
	 	 		'order' => $order,
	 	 		'email_heading'=> $args['email_heading'],
	 	 		 )
	 	 	);
		 	wc_get_template( 'emails/email-footer.php' );
		}
	}

	public function generate_form(){
		 ?>
		<form id="woocommerce-preview-email" action="" method="post">
		<table class="form-table">
		<tr>
		<?php 
			wp_nonce_field( 'woocommerce_preview_email', 'preview_email'); ?>
			<th>
			<label for="choose_email">Choose Email</label>
			</th>
			<td>
			<select id="choose_email" name="choose_email">
				<option value="">Choose Email</option>
			<?php foreach($this->emails as $index => $email):	?>
				<option value="<?php echo $index ?>"><?php echo $email->title; ?></option>
			<?php endforeach; ?>
			</select>
			</td>
		</tr>
		<tr>
		<?php
			$args = array(
				'post_type' => 'shop_order',
				'posts_per_page' => -1,
				'post_status' => array_keys( wc_get_order_statuses() )
			);
		?>	
			<th>
			<label for="orderID">
				Choose Order
			</label>
			</th>
			<td>
			<select name="orderID">
				<option value="">Choose Order</option>
			<?php
				$orders = get_posts($args);
				foreach($orders as $order){
			?>
 				<option value="<?php echo $order->ID ?>"><?php echo $order->post_title; ?></option>
 			<?php }	?>
			</select>
			</td>
		</tr>
		<tr>
		<td colspan="2"><input type="submit" name="submit" class="button button-primary"></td>
		</tr>
		</table>
		</form>
	<?php
	}

	public function generate_result(){
		if( isset( $_POST['preview_email'] ) && wp_verify_nonce( $_POST['preview_email'] , 'woocommerce_preview_email' ) ):
			if( isset($_POST['orderID']) && !empty($_POST['orderID']) && isset($_POST['choose_email']) && !empty($_POST['choose_email']) ){
			?>
				<style type="text/css">
					#tool-options{
						position: absolute;
						top:0;
					}
				</style>
				<?php
				echo '<div id="tool-options">';
					$this->generate_form();
					/*Design Required*/
					#echo '<a href="'.admin_url().'">Back </a>';
				echo '</div>';
				$orderID = absint( $_POST['orderID'] );
				$index = esc_attr( $_POST['choose_email'] );
				$current_email = $this->emails[$index];
				$html_template = $current_email->template_html;
				$email_heading = $current_email->heading;
				$args = array('email_heading' => $email_heading);
				$this->previewEmail( $orderID, $html_template, $args );
				die;
			}else{
				$this->notice_message = 'Please specify both Order and Email';
				$this->notice_class   = 'error';
				add_action( 'admin_notices', array($this,'adminNotices') ); 
			}
		endif;
	}

}
add_action('plugins_loaded', array('WooCommercePreviewEmails','get_instance'));

endif;
?>