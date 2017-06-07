<?php
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
		//moved into submenu
		add_submenu_page( 
			              'woocommerce',
			              'WooCommerce Preview Emails',
			              'Preview Emails',
			              'manage_options',
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
		 /*var_dump($email);*/
	?>
		<form id="woocommerce-preview-email" action="" method="post">
			<table class="form-table">
				<tr>
				<?php 
					wp_nonce_field( 'woocommerce_preview_email', 'preview_email'); ?>
					<th>
					<label for="choose_email"><?php _e('Choose Email','woo-preview-emails'); ?></label>
					</th>
					<td>
					<select id="choose_email" name="choose_email">
						<option value=""><?php _e('Choose Email','woo-preview-emails'); ?></option>
					<?php foreach($this->emails as $index => $email):	?>
						<option value="<?php echo $index ?>" <?php selected( $index, $choose_email ); ?>><?php echo $email->title; ?></option>
					<?php endforeach; ?>
					</select>
					</td>
				</tr>
				<tr>
				<?php
					$args = array(
						'post_type' => 'shop_order',
						'posts_per_page' => 10,
						'post_status' => array_keys( wc_get_order_statuses() )
					);
				?>	
					<th>
					<label for="orderID">
						<?php _e('Choose Order','woo-preview-emails'); ?>
					</label>
					</th>
					<td>
					<select name="orderID">
						<option value=""><?php _e('Choose Order','woo-preview-emails'); ?></option>
					<?php
						$orders = get_posts($args);
						foreach($orders as $order){
					?>
		 				<option value="<?php echo $order->ID ?>" <?php selected( $order->ID, $orderID ); ?> >#order : <?php echo $order->ID; ?></option>
		 			<?php }	?>
					</select>
					</td>
				</tr>
								<tr>
					<th>Search Orders<br/>
					<span id="search-description" class="description">Only use this field if you have particular orders, that are not listed above in the Choose Order Field. Type the Order ID only.
						Example: 90 </span></th>
					
					<td>
						<select name="search_order" id="woo_preview_search_orders" class="woo_preview_search_orders">
							<?php
								if( !empty($_POST['search_order'])){
									?>
										<option value="<?php echo $_POST['search_order']; ?>" selected="selected">#order : <?php echo $_POST['search_order']; ?></option>		
									<?php
								}
							?>
							<option value="">Search Orders</option>
						</select>
						<script type="text/javascript">
						jQuery( function($) {
							if( typeof ajaxurl == 'undefined' ){
								ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
							}
							$("#woo_preview_search_orders").select2({
								  placeholder: "Search Orders",
								 // data: [{ id:0, text:"something"}, { id:1, text:"something else"}],
								  ajax: {
								    url: ajaxurl,
								    dataType: 'json',
								    delay: 250,
								    data: function (params) {
								      return {
								        q: params.term, // search term
								        action: 'woo_preview_orders_search'
								      };
								    },
								    processResults: function (data, params) {
								      return {
								        results: data,
								      };
								    },
								    cache: true
								  },
								  minimumInputLength: 1
								});
							});
						</script>
					</td>
				</tr>
				<tr>
					<th>
						<label for="email">
							<?php _e('Mail to','woo-preview-emails'); ?>
						</label>
						</th>
					<td>
						<input type="email" name="email" id="email" value="<?php echo $recipient_email; ?>" /> <br />

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

		if( is_admin() && isset( $_POST['preview_email'] ) && wp_verify_nonce( $_POST['preview_email'] , 'woocommerce_preview_email' ) ):
			$condition = false;
					if( isset($_POST['choose_email']) 

						&& ( $_POST['choose_email'] == 'WC_Email_Customer_New_Account' || $_POST['choose_email'] == 'WC_Email_Customer_Reset_Password' ) 

						){
						$condition = true;

					}elseif(  ( ( isset($_POST['orderID']) && !empty($_POST['orderID']) ) || ( isset($_POST['search_order']) && !empty($_POST['search_order']) ) ) && ( isset($_POST['choose_email']) && !empty($_POST['choose_email']) ) ){
						$condition = true;
					}

			if( $condition == true) {
				$my_plugin_url = plugins_url( '', WOO_PREVIEW_EMAILS_FILE );

			?>
			<script src="<?php echo site_url().'/wp-includes/js/jquery/jquery.js'; ?>" type="text/javascript"></script>
				<script src="<?php echo $my_plugin_url.'/assets/js/select2.min.js'; ?>" type="text/javascript"></script>
				<link rel="stylesheet" type="text/css" href="<?php echo $my_plugin_url.'/assets/css/select2.min.css'; ?>">
				<style type="text/css">
					#search-description{
						display: none;
					}
					#tool-options{
						width: 590px;
						background: #fff;
						border-style: solid;
						border-width: 2px 2px 2px 0px;	
						position: fixed;
						top:30%;
						left:-590px;
						transition: all 0.8s ease-in-out;

					}

					#tool-options.active{
						left:0px;
					}


					#tool-wrap{ position: relative; }
					#show_menu{
						text-decoration: none;
						    padding: 10px;
						    color: #000;
						    position: absolute;
						    right: -72px;
						    top: 42%;
						    background: #fff;
						    transform: rotate(-90deg);
						    border: 2px solid;
					}
				</style>
				<script type="text/javascript">
					window.onload = function(){
						var show_menu = document.getElementById("show_menu");
						show_menu.addEventListener("click", function(e){
							var classes = document.getElementById("tool-options").classList;
							if ( classes[0] == undefined ){
								document.getElementById("tool-options").classList.add("active");
								show_menu.innerHTML = 'Hide Menu';
							}else{
								document.getElementById("tool-options").classList.remove("active");
								show_menu.innerHTML = 'Show Menu';
							}
						});

				}
					
				</script>
				<?php
				$orderID = absint( $_POST['orderID'] );
				$index = esc_attr( $_POST['choose_email'] );
				$recipeint_email = $_POST['email'];
				if( is_email($recipeint_email ) ){
					$this->recipient = $_POST['email'];

				}else{
					$this->recipient = '';
				}
				$current_email = $this->emails[$index];
				/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
				add_filter( 'woocommerce_email_recipient_' . $current_email->id, array($this,'no_recipient') );
				
				if( $index === 'WC_Email_Customer_Note' ){
					$customer_note = 'lorem ipsum';
					$args = array(
						'order_id'      => $orderID,
						'customer_note' => $customer_note
					);
					$current_email->trigger($args);
				}elseif( $index === 'WC_Email_Customer_New_Account' ){
					$user_id = get_current_user_id();
					$current_email->trigger($user_id);
				}
				else{
					$current_email->trigger($orderID);
				}


				$content = $current_email->get_content_html();
				$content = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );
				echo $content;

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
		if($this->recipient != ''){
			$recipient = $this->recipient;
		}else{
			$recipient = '' ;
		}
		return $recipient;
	}

}
add_action('plugins_loaded', array('WooCommercePreviewEmails','get_instance'));

endif;