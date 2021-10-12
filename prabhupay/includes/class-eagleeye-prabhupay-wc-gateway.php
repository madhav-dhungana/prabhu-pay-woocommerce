<?php
	
	add_action( 'plugins_loaded', 'eagleeye_prabhupay_init_gateway_class' );
	function eagleeye_prabhupay_init_gateway_class()
	{
		class WC_Gateway_Eagleeye_Prabhupay extends WC_Payment_Gateway
		{
			/**
			 * Class constructor, more about it in Step 3
			 */
			protected $notify_url;
			public function __construct()
			{
				$this->id = 'eagleeye_prabhupay';
				$this->icon = apply_filters( 'eagleeye_prabhupay_icon', plugins_url( 'assets/img/prabhupay_logo.png', plugin_dir_path( __FILE__ ) ) );
				$this->order_button_text = __( 'Pay with PrabhuPay', 'eagleeye_prabhupay' );
				$this->has_fields = false;
				$this->method_title = 'PrabhuPay';
				$this->method_description = 'Description of PrabhuPay payment';
				$this->supports = array(
					'products',
					'pre-orders'
				);
				
				// Method with all the options fields
				$this->init_form_fields();
				
				// Load the settings.
				$this->init_settings();
				$this->title = $this->get_option('title');
				$this->invoice_prefix = $this->get_option('invoice_prefix');
				$this->description = $this->get_option('description');
				$this->enabled = $this->get_option('enabled');
				$this->testmode = 'yes' == $this->get_option('testmode');
				//$this->merchantId = $this->get_option('merchantId');
				//$this->merchant_password = $this->get_option('merchant_password');

				$this->url = ( $this->testmode == 0 ) ? $this->get_option( 'live_url' ) : $this->get_option( 'test_url' );
				$this->merchantId = ( $this->testmode == 0 ) ? $this->get_option( 'live_merchantId' ) : $this->get_option( 'test_merchantId' );
				$this->merchant_password = ( $this->testmode == 0 ) ? $this->get_option( 'live_merchant_password' ) : $this->get_option( 'test_merchant_password' );
				$this->notify_url = WC()->api_request_url( $this->id );
				
				// This action hook saves the settings
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

				add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

				// Register a webhook here
				//add_action( 'woocommerce_api_eagleeye_prabhupay', array( $this, 'webhook' ) );
			}
			/**
			 * Initializes the settings
			 */
			public function init_form_fields()
			{
				$this->form_fields = apply_filters( 'eagleeye_prabhupay_form_fields',array(
					'enabled' => array(
						'title'       => 'Enable/Disable',
						'label'       => 'Enable PrabhuPay Gateway',
						'type'        => 'checkbox',
						'description' => 'Enable Here',
						'default'     => 'no'
					),
					'title' => array(
						'title'       => 'Title',
						'type'        => 'text',
						'description' => 'This controls the title which the user sees during checkout.',
						'default'     => 'PrabhuPay',
						'desc_tip'    => true,
					),
					'invoice_prefix' => array(
						'title'       => 'Invoice Prefix',
						'type'        => 'text',
						'description' => 'This is the prefix for incoice.',
						'default'     => 'PrabhuPay-INV-',
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => 'Description',
						'type'        => 'textarea',
						'description' => 'This controls the description which the user sees during checkout.',
						'default'     => 'Pay with PrabhuPay payment gateway.',
					),
					'testmode' => array(
						'title'       => 'Test mode',
						'label'       => 'Enable Test Mode',
						'type'        => 'checkbox',
						'description' => 'Place the payment gateway in test mode using test API keys.',
						'default'     => 'yes',
						'desc_tip'    => true,
					),
					'test_url' => array(
						'title'       => 'Test URL',
						'type'        => 'text'
					),
					'test_merchantId' => array(
						'title'       => 'Test Merchant Id',
						'type'        => 'text'
					),
					'test_merchant_password' => array(
						'title'       => 'Test Merchant Password',
						'type'        => 'text'
					),
					
					'live_url' => array(
						'title'       => 'Live URL',
						'type'        => 'text'
					),
					'live_merchantId' => array(
						'title'       => 'Live Merchant Id',
						'type'        => 'text'
					),
					'live_merchant_password' => array(
						'title'       => 'Live Merchant Password',
						'type'        => 'text'
					)
				));
			}
			
			/**
			 * Get the transaction URL.
			 *
			 * @param  WC_Order $order
			 * @return string
			 */
			/* public function get_transaction_url( $order ) {
				if ( $this->testmode ) {
					$this->view_transaction_url = 'https://testsys.prabhupay.com/api/Epayment/';
				} else {
					$this->view_transaction_url = 'https://testsys.prabhupay.com/api/Epayment/';
				}
				return parent::get_transaction_url( $order );
			} */

			/**
			 * Outputs scripts used for the payment gateway.
			 *
			 * @access public
			 */
			public function payment_scripts() {
				wp_enqueue_style('eagleeye_prabhupay-css', EAGLEEYE_PRABHUPAY_URL . 'assets/css/eagleeye-prabhupay.css', array(), '1.0.0');
				wp_enqueue_script( 'eagleeye_prabhupay-js', EAGLEEYE_PRABHUPAY_URL . 'assets/js/eagleeye-prabhupay.js', 'jQuery', '1.0.0', true );
			}

			/*
			 * We're processing the payments here, everything about it is in Step 5
			 */
			public function process_payment( $order_id )
			{
				global $woocommerce;
				//get order details
				//$order = wc_get_order($order_id);
				//var_dump( $order_id );
				$order   = new WC_Order( $order_id );
				$basetot = WC()->cart->total;
				$tot     = $basetot * 100;

				$redirectUrl =  add_query_arg(['prabhupay' => 'pay', 'order_id' => $order_id], wc_get_checkout_url());
				return array(
					'result' => 'success',
					'redirect' => $redirectUrl
				);
			}
		}
	}
