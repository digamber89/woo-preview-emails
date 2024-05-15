<?php
/*Not the WordPress way but i'm trying to avoid any accidental CSS that would affect how the email template looks*/
$assets         = include( WOO_PREVIEW_EMAILS_DIR . '/assets/main.asset.php' );
$main_css       = add_query_arg( [ 'ver' => $assets['version'] ], $this->plugin_url . '/assets/main.css' );
$style_main_css = add_query_arg( [ 'ver' => $assets['version'] ], $this->plugin_url . '/assets/style-main.css' );
?>
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $main_css ) ?>">
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $style_main_css ) ?>">