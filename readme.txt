=== Woo Preview Emails ===
Contributors: digamberpradhan
Tags: WooCommerce, Emails, Preview, WooCommerce Preview Emails
Requires at least: 3.8
Tested up to: 4.9.4
Stable tag: 1.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An Extension for WooCommerce that allows you to Preview Email Templates.

== Description ==
An Extension for WooCommerce that allows you to Preview Email Templates.

1. Just install the plugin and an admin section will be generated that lists the different Emails that WooCommerce sends.
2. Choose the templates and an Order
3. A preview of the selected E-mail will be shown 

== Installation ==

1. Upload `woocommerce-preview-emails` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You're done go to Preview Emails section in the admin menu and start previewing

== Frequently Asked Questions ==

= Send Test Mails to yourself =
You can now send test mails, to test on actual devices

*note: If the mail isn't seen in you inbox, please first check your spam folder

== Screenshots ==

1. Menu Location
2. Preview Order E-mails
3. Preview User E-mails
4. Example
5. Send emails to yourself to check on other devices
== Changelog ==
= 1.4.1 =
Show compatibility with WooCommerce version 3.4.4 

= 1.4.0 =
Fixed fatal error issue when selecting orders via dropdown

= 1.3.1 =
Added support for On Hold payment status using BACS, thanks again to @prasidhda https://profiles.wordpress.org/prasidhda for pointing out the issue

= 1.3.0 =
Fixed issue where emails send function was causing fatal error, thanks to @prasidhda https://profiles.wordpress.org/prasidhda

= 1.2.11 =
Added WC version compatibility check https://woocommerce.wordpress.com/2017/08/28/new-version-check-in-woocommerce-3-2/ 

= 1.2.10 =
- Enhancement : Added filter to allow shop managers or any other capability to preview e-mails

= 1.2.9 = 
- Fix: Fixed possible conflict with other plugins that create custom emails for woocommerce (known conflict with https://woocommerce.com/products/woocommerce-order-status-manager/)

= 1.2.8 = 
- Formatted Code to make it easier for other developers to review
- Removed filter after adding when sending tested emails to avoid unwanted emails being sent to customers
- Tested against recent version of WordPress and WooCommerce

= 1.2.7 =
- Fix: issue with wrong constant being used for file path https://wordpress.org/support/topic/php-notice-in-php-7-0-x/ 

= 1.2.6 =
- Added ability to search for particular orders
- Updated Compatibility tag with WooCommerce 3.0.3
- Added leave a rating text

= 1.2.5 =
28-12-2016: Menu location changed(under WooCommerce menu), User e-mails no longer need orders to be selected, added screenshots

= 1.2.4 =
Made update so that order number instead of order date

= 1.2.3 = 
Fix Broken back to admin url

= 1.2.1 =
Update Missing Changelog

= 1.2.0 =
Changed UI to make responsive testing easier

= 1.1.2 =
Changed number of orders visible to 10, to prevent unnecessary load on larger shops

= 1.1.1 =
You can now send test emails to yourself

= 1.1 =
Plugin is now translation ready, please find the .pot file in /languages 

= 1.0 =
* First Version







