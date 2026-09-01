<?php
/**
 * Plugin Name: WooCommerce Rental Agreement Return URL
 * Plugin URI: https://github.com/TEMHDARWIN/woocommerce-rental-agreement
 * Description: Redirects WooCommerce return URL to a rental agreement page including order ID and order key. Also handles a FluentForm submission redirect when an order_key is present.
 * Version: 1.0.1
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
    if ( ! is_a( $order, 'WC_Order' ) ) {
        return $return_url;
    }

    $order_id  = $order->get_id();
    $order_key = $order->get_order_key();

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


/**
 * FluentForm submission redirect for the Rental Agreement form.
 *
 * Form ID is set to 3 per user's request.
 * This handles both normal and AJAX submissions (returns JSON for AJAX).
 */
add_action( 'fluentform/submission_inserted', function( $entryId, $formData, $form ) {
    // Only run for your Rental Agreement form - form ID 3
    if ( empty( $form ) || (int) $form->id !== 3 ) {
        return;
    }

    $order_key = isset( $formData['order_key'] ) ? sanitize_text_field( wp_unslash( $formData['order_key'] ) ) : '';

    if ( ! $order_key ) {
        return;
    }

    $redirect_url = esc_url_raw( 'https://scrambler.blog/checkout/bute-order-confirmation/308/?key=' . rawurlencode( $order_key ) );

    // If the form submits via AJAX, return JSON so client-side JS can handle the redirect.
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        wp_send_json_success( array( 'redirect' => $redirect_url ) );
    } else {
        wp_safe_redirect( $redirect_url );
        exit;
    }
}, 10, 3 );
