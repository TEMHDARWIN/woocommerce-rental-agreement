=== WooCommerce Rental Agreement Return URL ===
Contributors: TEMHDARWIN
Tags: woocommerce, rental, order, return
Requires at least: 5.0
Tested up to: 6.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A small plugin that redirects the WooCommerce order return URL to /rental-agreement/ and passes order_id and key as query parameters.

== Description ==

This plugin modifies the URL customers are sent to after completing checkout so they land on a rental agreement page. The page receives two query parameters: order_id and key. The rental-agreement page should validate those values before showing any sensitive information.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install it via the WordPress plugin uploader using the ZIP from the repository.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Create a page with slug `rental-agreement` and implement verification for `order_id` and `key`.

== Changelog ==

= 1.0.0 =
* Initial release.
