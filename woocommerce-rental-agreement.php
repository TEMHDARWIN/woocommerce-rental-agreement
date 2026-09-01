<?php
/**
 * Plugin Name: WooCommerce Rental Agreement Return URL
 * Plugin URI: https://github.com/TEMHDARWIN/woocommerce-rental-agreement
 * Description: Redirects WooCommerce return URL to a rental agreement page including order ID and order key.
 * Version: 1.0.0
 * Author: TEMHDARWIN
 * License: GPL2
 * Text Domain: woocommerce-rental-agreement
 */

// Exit if accessed directly.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Replace WooCommerce return URL with a rental agreement page including order_id and key.
 *
 * @param string   $return_url The original return URL.
 * @param WC_Order $order      The order object.
 * @return string Modified return URL.
 */
function wra_get_return_url( $return_url, $order ) {
    // Make sure $order is a WC_Order object.
    if ( ! is_a( $order, 'WC_Order' ) ) {
        return $return_url;
    }

    $order_id  = $order->get_id();
    $order_key = $order->get_order_key();

    // Build the rental agreement URL safely.
    $url = add_query_arg(
        array(
            'order_id' => absint( $order_id ),
            'key'      => rawurlencode( $order_key ),
        ),
        home_url( '/rental-agreement/' )
    );

    return esc_url_raw( $url );
}

add_filter( 'woocommerce_get_return_url', 'wra_get_return_url', 10, 2 );
