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