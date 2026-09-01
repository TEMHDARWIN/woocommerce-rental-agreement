<?php
/**
 * Plugin Name: WooCommerce Rental Agreement Return URL
 * Plugin URI: https://github.com/TEMHDARWIN/woocommerce-rental-agreement
 * Description: Redirects WooCommerce return URL to a rental agreement page including order ID and order key. Also handles FluentForm client-side redirects when an order_key is present.
 * Version: 1.0.4
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
 * Preferred FluentForm hook: modify the submission confirmation to perform a redirect.
 * This runs for form ID 3 and checks the saved submission for an order_key field.
 */
add_filter( 'fluentform/submission_confirmation', function( $confirmation, $form, $entryId ) {
    // Only run for form ID 3
    if ( empty( $form ) || (int) $form->id !== 3 ) {
        return $confirmation;
    }

    // Ensure the wpFluent() helper exists and the submissions table is available
    if ( ! function_exists( 'wpFluent' ) ) {
        return $confirmation;
    }

    $entry = wpFluent()->table( 'fluentform_submissions' )->where( 'id', $entryId )->first();
    if ( ! $entry || empty( $entry->response ) ) {
        return $confirmation;
    }

    $formData = json_decode( $entry->response, true );
    if ( ! is_array( $formData ) ) {
        return $confirmation;
    }

    $order_key = isset( $formData['order_key'] ) ? sanitize_text_field( $formData['order_key'] ) : '';

    if ( $order_key ) {
        $redirect = 'https://scrambler.blog/checkout/bute-order-confirmation/308/?key=' . rawurlencode( $order_key );
        return array(
            'redirectUrl' => esc_url_raw( $redirect ),
            'type'        => 'redirect',
        );
    }

    return $confirmation;
}, 10, 3 );


/**
 * Final approach: inject a small JS snippet on the Rental Agreement page footer that listens for
 * Fluent Forms' client-side success events and redirects using the 'key' parameter from the page URL.
 */
add_action( 'wp_footer', function() {
    // Change 241 to your Rental Agreement page ID if different.
    if ( ! is_page( 241 ) ) {
        return;
    }

    ?>
    <script>
    function temhRedirect() {
        var key = new URLSearchParams(window.location.search).get('key');
        if (key) {
            window.location.href = 'https://scrambler.blog/checkout/bute-order-confirmation/308/?key=' + key;
        }
    }
    document.addEventListener('fluentform_submission_success', temhRedirect);
    document.addEventListener('ff_submission_success', temhRedirect);
    document.addEventListener('fluentFormSubmissionSuccess', temhRedirect);
    </script>
    <?php
}, 10 );
