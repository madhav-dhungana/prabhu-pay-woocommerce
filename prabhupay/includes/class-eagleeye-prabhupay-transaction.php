<?php
	
	//add_action( 'plugins_loaded', function() { new Eagleeye_Prabhupay_Transaction(); } );
	
	class Eagleeye_Prabhupay_Transaction {
		
		public $merchantId, $merchant_password, $url, $invoice_prefix, $testmode, $payment;
		public function __construct(){
		
		}
		
		public function eagleeye_prabhupay_prepare_details(){
			$payment_gateway_id = 'eagleeye_prabhupay';
			$payment_gateways   = WC_Payment_Gateways::instance();
			//var_dump($payment_gateways->payment_gateways());
			$payment_gateway    = $payment_gateways->payment_gateways()[$payment_gateway_id];
			$this->testmode = $payment_gateway->testmode;
			$this->merchantId = $payment_gateway->merchantId;
			$this->merchant_password = $payment_gateway->merchant_password;
			$this->url = $payment_gateway->url;
			$this->invoice_prefix = $payment_gateway->invoice_prefix;
		}

		
		public function manjul_javascript_variables(){ ?>
			<script type="text/javascript">
	            var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
			</script><?php
		}
		
		public function eagleeye_prabhupay_checkoutform(){
			?>
			<div class="manjul_modal_wrapper">
				<div id="eagleeye_phonenumber" class="eagleeye_modal">
					<div class="eagleeye_modal-content">
						<span class="eagleeye_close">&times;</span>
						<div class="logo">
							<img src="<?php echo EAGLEEYE_PRABHUPAY_URL ?>/assets/img/prabhupay_logo.png" alt="" />
						</div>
						<form id="eagleeye_prabhupay_form" name="eagleeye_prabhupay_form">
							<div class="eagleeye_prabhupay_phone_wrapper">
								<label for="eagleeye_prabhupay_phone_input"><?php echo esc_html('Please Enter your PrabhuPAY User ID', 'eagleeye_prabhupay') ?></label>
								<input name="eagleeye_prabhupay_phone" type="text" id="eagleeye_prabhupay_phone_input" class="eagleeye_prabhupay_phone_input" placeholder="9847092226" />
								<button onclick="return false;" value="getOtp" id="eagleeye_getotp">Get OTP</button>
							</div>
						</form>
					</div>
				</div>
			</div>
            <div id="eagleeye_prabhupay_loading">
                <img src="<?php echo EAGLEEYE_PRABHUPAY_URL ?>/assets/img/loader.gif" alt="loading">
            </div>
			<?php
		}
		
		//request for otp (AJAX)
		public function eagleeye_process_for_otp(){
			$this->eagleeye_prabhupay_prepare_details();
			
			$phone = sanitize_text_field( $_POST["eagleeye_prabhupay_phone"] );
			$order_id = sanitize_text_field( $_POST["order_id"] );
			$order   = wc_get_order( $order_id );
			$total = $order->get_total();;
			
			$eagleeye_prabhupay_details = [
				"amount" 		=> $total,
				"invoiceNo"		=>  $this->invoice_prefix . $order_id  . '-' . uniqid(),
				"txnDate"		=> date('m/d/Y'),
				"merchantId"	=> $this->merchantId,
				"cellPhone"		=> $phone,
				"password"		=> $this->merchant_password,
				"remarks"		=> "Payment for Order number" . $order_id
			];
			
			$eagleeye_prabhupay_details = json_encode($eagleeye_prabhupay_details);
			
			
			$response = wp_remote_post( $this->url . 'GetOtp', array(
					'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
					'body' => $eagleeye_prabhupay_details,
					'data_format' => 'body',
				)
			);
			
			$msg = json_decode($response['body'], true);
			$status = $msg['status'];
			$message = $msg['message'];
			$success = $msg['success'];
			
			if($status === "00"){
				//true
				echo json_encode($response);
				wp_die();
			}
			
			else{
				//TODO
				echo json_encode($response);
				wp_die();
			}
		}
		
		public function eagleeye_prabhupay_verify_otp(){
			$this->eagleeye_prabhupay_prepare_details();
			
			$otp = sanitize_text_field( $_POST["eagleeye_prabhupay_otp"] );
			$phone = sanitize_text_field( $_POST["eagleeye_prabhupay_phone"] );
			$transactionId = sanitize_text_field( $_POST["transactionId"] );
			$order_id = sanitize_text_field( $_POST["order_id"] );
			$order = wc_get_order( $order_id );
			$total = $order->get_total();;
			
			$eagleeye_prabhupay_details = [
				"merchantId"	=> $this->merchantId,
				"password"		=> $this->merchant_password,
				"transactionId"	=> $transactionId,
				"otp"			=> $otp,
				"cellPhone"		=> $phone
			];
			
			$eagleeye_prabhupay_details = json_encode($eagleeye_prabhupay_details);
			
			
			$response = wp_remote_post( $this->url . 'ConfirmPayment', array(
					'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
					'body' => $eagleeye_prabhupay_details,
					'data_format' => 'body',
				)
			);
			
			$msg = json_decode($response['body'], true);
			$status = $msg['status'];
			$message = $msg['message'];
			$success = $msg['success'];
			if($status === "00"){
				//true
				$url = $order->get_checkout_order_received_url();
				$result = ['status'=> 'success', 'further'=>'complete', 'url'=>$url ];
			}
			elseif( $status === "96" ){
				//TODO
				$result = ['status'=> 'failed', 'further'=>'reOTP', 'msg'=> $message];
			}
			else{
				//TODO
				$url = wc_get_checkout_url();;
				$result = ['status'=> 'failed', 'further'=>'toCheckout', 'url'=>$url ];
			}
			
			echo json_encode($result);
			wp_die();
		}
	}
