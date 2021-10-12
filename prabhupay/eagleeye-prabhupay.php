<?php
	/*
	Plugin Name: PrabhuPay Woocommerce
	Plugin URI: https://dmadhav.com.np
	Description: PrabhuPay Payment Gateway for WooCommerce
	Version: 1.0.1
	Author: Madhav Dhungana
	Author URI: https://dmadhav.com.np
	License: GPL v2 or later
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
    Text Domain: eagleeye_prabhupay
	*/
	
defined( 'ABSPATH' ) or exit;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EAGLEEYE_PRABHUPAY_VERSION', '1.1.0' );

define( 'EAGLEEYE_PRABHUPAY_PATH', plugin_dir_path( __FILE__ ) );
define( 'EAGLEEYE_PRABHUPAY_URL', plugin_dir_url( __FILE__ ) );

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;
/*
* This action hook registers our PHP class as a WooCommerce payment gateway
*/
add_filter( 'woocommerce_payment_gateways', 'eagleeye_prabhupay_add_gateway_class', 10 );
function eagleeye_prabhupay_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Gateway_Eagleeye_Prabhupay';
	return $gateways;
}

include_once EAGLEEYE_PRABHUPAY_PATH . 'includes/class-eagleeye-prabhupay-wc-gateway.php';
include_once EAGLEEYE_PRABHUPAY_PATH . 'includes/eagleeye-prabhupay-hooks.php';
