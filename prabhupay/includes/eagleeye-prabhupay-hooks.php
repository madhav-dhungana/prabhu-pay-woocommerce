<?php
	
	require_once EAGLEEYE_PRABHUPAY_PATH . 'includes/class-eagleeye-prabhupay-transaction.php';
	
	$eagleeye_prabhupay_transaction = new Eagleeye_Prabhupay_Transaction();
	
	//set admin_ajax url in head
	add_action ( 'wp_head', [ $eagleeye_prabhupay_transaction, 'manjul_javascript_variables' ] );

	//prabhupay checkout form
	add_action( 'woocommerce_after_checkout_form', [ $eagleeye_prabhupay_transaction, 'eagleeye_prabhupay_checkoutform' ] );

	//ajax request for OTP
	add_action('wp_ajax_eagleeye_process_for_otp', [ $eagleeye_prabhupay_transaction, 'eagleeye_process_for_otp' ] );
	add_action('wp_ajax_nopriv_eagleeye_process_for_otp', [$eagleeye_prabhupay_transaction, 'eagleeye_process_for_otp' ] );

	//verify OTP and Process Payment
	add_action('wp_ajax_eagleeye_prabhupay_verify_otp', [ $eagleeye_prabhupay_transaction, 'eagleeye_prabhupay_verify_otp' ] );
	add_action('wp_ajax_nopriv_eagleeye_prabhupay_verify_otp', [ $eagleeye_prabhupay_transaction, 'eagleeye_prabhupay_verify_otp' ] );

?>
