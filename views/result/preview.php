<?php
/**
 * Email preview template
 *
 * @var array $args
 */

/** @var \WC_Email $current_email */
$current_email           = $args['current_email'];
/** @var string $currently_used_template */
$currently_used_template = $args['currently_used_template'];
/** @var string $email_type */
$email_type              = $args['email_type'];
/** @var string $content */
$content                 = $args['content'];
/** @var \Codemanas\WooPreviewEmails\Main $main */
$main                    = $args['main'];
/** @var string $plugin_url */
$plugin_url              = $args['plugin_url'];
?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( get_locale() ); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e( 'Previewing Emails', 'woo-preview-emails' ); ?></title>
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
                   style="text-align: left; display: flex; align-items: center;"
                   href="<?php echo esc_url( admin_url( 'admin.php?page=codemanas-woocommerce-preview-emails' ) ); ?>"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    &nbsp;
                    <?php esc_html_e( 'Back to Admin Area', 'woo-preview-emails' ); ?></a>
            </div>
            <p>
                <strong><?php esc_html_e( 'Viewing Template File:', 'woo-preview-emails' ); ?> </strong><br/>
				<?php echo esc_html( $currently_used_template ); ?>
            </p>
            <p class="description">
                <strong><?php esc_html_e( 'Description:', 'woo-preview-emails' ); ?> </strong>
				<?php echo wp_kses_post( $current_email->description ); ?>
            </p>
			<?php $main->generate_form(); ?>
        </div>
    </div>
    <div class="cm-WooPreviewEmail-emailContent cm-WooPreviewEmail-emailContent__<?php echo esc_attr( $email_type ); ?>"><?php echo $content; ?></div>
    <div class="tool-bar-toggler">
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            <span class="hide-controls"><?php esc_html_e( 'Hide Controls', 'woo-preview-emails' ); ?></span>
            <span class="show-controls"><?php esc_html_e( 'Show Controls', 'woo-preview-emails' ); ?></span>
        </a>
    </div>
</div>

</body>
</html>
