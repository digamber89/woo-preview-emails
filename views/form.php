<?php
extract( $args );
$args = [
	'posts_per_page' => 10,
];
$type = wc_get_order_types( 'view-orders' );
if ( is_array( $type ) ) {
	$type         = array_diff( $type, [ 'shop_order_refund' ] );
	$args['type'] = $type;
}

$orders = wc_get_orders( $args );
?>
<form id="woocommerce-preview-email" action="" method="post" data-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
	<?php wp_nonce_field( 'woocommerce_preview_email', 'preview_email' ); ?>
    <div class="cm-woo-preview-emails-form">
        <div class="cm-woo-preview-emails-form__input-wrapper">
            <div class="cm-woo-preview-emails-form__label"><label for="choose_email"><?php _e( 'Choose Email', 'woo-preview-emails' ); ?></label></div>
            <div class="cm-woo-preview-emails-form__input-field">
                <select id="choose_email" name="choose_email" class="regular-text">
                    <option value=""><?php _e( 'Choose Email', 'woo-preview-emails' ); ?></option>
					<?php foreach ( $emails as $index => $email ): ?>
                        <option value="<?php echo $index ?>" <?php selected( $index, $choose_email ); ?>><?php echo $email->title; ?></option>
					<?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="cm-woo-preview-emails-form__input-wrapper">
            <div class="cm-woo-preview-emails-form__label">
                <label for="orderID"><?php _e( 'Choose Order', 'woo-preview-emails' ); ?></label>
            </div>
            <div class="cm-woo-preview-emails-form__input-field">
				<?php if ( ! empty( $orders ) ): ?>
                    <select name="orderID" id="orderID" class="regular-text">
                        <option value=""><?php _e( 'Choose Order', 'woo-preview-emails' ); ?></option>
						<?php
						foreach ( $orders as $order ) {
							$order_id = $order->get_id()
							?>
                            <option value="<?php echo $order_id ?>" <?php selected( $order_id, $orderID ); ?> >#order : <?php echo $order_id; ?></option>
						<?php } ?>
                    </select>
				<?php else: ?>
					<?php esc_html_e( 'There are currently no orders on your site - please add some orders first', 'woo-preview-emails' ); ?>
				<?php endif; ?>
            </div>
        </div>
        <div class="cm-woo-preview-emails-form__input-wrapper">
            <div class="cm-woo-preview-emails-form__label">
                <label for="woo_preview_search_orders"><?php _e( 'Search Orders', 'woo-preview-emails' ); ?></label>
            </div>
            <div class="cm-woo-preview-emails-form__input-field">
                <select name="search_order" id="woo_preview_search_orders" class="regular-text" class="regular-text">
					<?php
					if ( ! empty( $_POST['search_order'] ) ) {
						?>
                        <option value="<?php echo esc_attr( $_POST['search_order'] ); ?>" selected="selected">#order : <?php echo esc_attr( $_POST['search_order'] ); ?></option>
						<?php
					}
					?>
                    <option value=""><?php _e( 'Search Orders', 'woo-preview-emails' ); ?></option>
                </select>
            </div>
            <span class="description">
		        <?php _e( 'Only use this field if you have particular orders, that are not listed above in the Choose Order Field. Type the Order ID only. Example: 90', 'woo-preview-emails' ); ?>
            </span>
        </div>
        <div class="cm-woo-preview-emails-form__input-wrapper">
            <div class="cm-woo-preview-emails-form__label">
                <label for="email">
					<?php _e( 'Mail to', 'woo-preview-emails' ); ?>
                </label>
            </div>
            <div class="cm-woo-preview-emails-form__input-field cm-woo-preview-emails-form__input-field--select-mail">
                <input type="email" name="email" id="email" class="regular-text" value="<?php echo esc_attr( $recipient ); ?>"/>
                <input type="button" title="clear" alt="clear" name="clearEmail" id="clearEmail" class="clearEmail button button-secondary" value="Clear"/>
            </div>
        </div>
        <div class="cm-woo-preview-emails-form__input-wrapper">
            <div class="cm-woo-preview-emails-form__label"><label for="email_type"><?php _e( 'Email Type', 'woo-preview-emails' ); ?></label>
            </div>
            <div class="cm-woo-preview-emails-form__input-field">
                <select name="email_type" id="email_type">
                    <option value="html" <?php selected( 'html', $email_type ) ?>>HTML</option>
                    <option value="plain" <?php selected( 'plain', $email_type ) ?>>Plain / Text</option>
                </select>
            </div>
        </div>
    </div>
	<?php do_action( 'woo_preview_emails_addition_form_fields', $args, $orders ); ?>
    <p style="text-align: left"><input type="submit" name="submit" value="<?php _e( 'Preview', 'woo-preview-emails' ) ?>" class="button button-primary"></p>
</form>