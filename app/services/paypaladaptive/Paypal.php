<?php

class Paypal {

	private $api_username;

	private $api_password;

	private $api_signature;

	private $api_appid;

	private $api_endpoint;

	private $api_version;

	private $api_mode;

	private $detail_level;

	private $error_language;

	private $cert_key_pem_file;

	private $env;

	private $ip_address;

	private $request_data_format;

	private $response_data_format;

	private $fees_payer;

	private $sandbox_email;

	private $ipn_notification_url;

	private $primary_reciever;

	private $primary_reciever_amount;

	private $secondary_reciever;

	private $secondary_reciever_amount;

	private $cancel_url;

	private $return_url;

	private $paypal_approval_url;

	private $tracking_id;

	public $adaptive_payment_method = 'chained';

	public $currency_code = '';

	private $pay_data = array();

	public function setPaymentMode($test_mode) {
		if($test_mode) {
			$this->env	= 'test';
		}
		else {
			$this->env	= 'live';
		}
	}

	public function initializePayment($data) {
		$this->api_username				= $data['api_username'];
		$this->api_password				= $data['api_password'];
		$this->api_signature			= $data['api_signature'];
		$this->api_appid				= $data['api_appid'];
		$this->api_endpoint				= Config::get('paypaladaptive.'.$this->env.'_end_point');
		//$this->cert_key_pem_file		= Config::get('paypaladaptive.'.$this->env.'_certificate_key_pem');

		$this->api_version				= Config::get('paypaladaptive.api_version');
		$this->api_mode					= Config::get('paypaladaptive.api_mode');
		$this->detail_level				= Config::get('paypaladaptive.detail_level');
		$this->error_language			= Config::get('paypaladaptive.error_lang');
		$this->request_data_format		= Config::get('paypaladaptive.request_data_format');
		$this->response_data_format		= Config::get('paypaladaptive.response_data_format');
		$this->fees_payer				= $data['fees_payer'];
		$this->adaptive_payment_method	= Config::get('payment.paypal_adaptive_payment_method');//'parallel';

		$this->currency_code			= 'USD';//$CFG['payment']['paypal']['currency_code'];

		$this->ip_address				= (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))? $_SERVER['REMOTE_ADDR'] : '';

		$this->sandbox_email 			= '';

		$this->paypal_approval_url		= Config::get('paypaladaptive.'.$this->env.'_approval_url');//$CFG['paypal_adaptive_payments'][$env]['approval_url'];
		$this->isSecondaryReceiverSet = false;
	}

	public function setPrimaryReceiver($email, $amount, $chained = '')
	{
		$this->primary_reciever = $email;
		$this->primary_reciever_amount = $amount;
		if($chained)
			$this->isPrimary = $chained;
		else
			$this->isPrimary = ($this->adaptive_payment_method == 'chained') ? 'true' : 'false';
	}

	public function setSecondaryReceiver($email, $amount) {

		$this->secondary_reciever[] = array('email' => $email, 'amount' => $amount);
		//$this->secondary_reciever_amount[] = $amount;
		$this->isSecondaryReceiverSet =  true;
	}

	public function setCancelURL($cancel_url) {
		$this->cancel_url = $cancel_url;
	}

	public function setReturnURL($return_url) {
		$this->return_url = $return_url;
	}
	public function setNotoficationURL($notification_url) {
		$this->ipn_notification_url	= $notification_url;
	}

	public function setTrackingId($tracking_id = 0) {
		// tracking ID is generated and set if not explicitly given
		if ($tracking_id == 0) {
			$tracking_id = uniqid('TK-' . date("Ymd"));
		}

		$this->tracking_id = $tracking_id;
	}

	public function setCurrencyCode($currency_code = 'USD') {
		$this->currency_code = $currency_code;
	}

	public function getTrackingId() {
		return $this->tracking_id;
	}

	public function setPaymentNote($payment_note) {
		$this->payment_note = $payment_note;
	}

	public function getPay() {
		/* SENDER – Sender pays all fees (for personal, implicit simple/parallel payments; do not use for chained or unilateral payments)
    *

      PRIMARYRECEIVER – Primary receiver pays all fees (chained payments only)
    *

      EACHRECEIVER – Each receiver pays their own fee (default, personal and unilateral payments)
    *

      SECONDARYONLY – Secondary receivers pay all fees (use only for chained payments with one secondary receiver)
     */

		$default_feesPayer = 'EACHRECEIVER';

		if($this->fees_payer == 'PRIMARYRECEIVER' AND  $this->isPrimary == 'false')
				$this->fees_payer = $default_feesPayer;
		if($this->fees_payer == 'SECONDARYONLY' AND  !( $this->isPrimary != 'false' OR !$this->isSecondaryReceiverSet))
				$this->fees_payer = $default_feesPayer;

		$post_vars_nvp = '';

		$post_vars_nvp .= "requestEnvelope.errorLanguage=" . $this->error_language;
		$post_vars_nvp .= "&requestEnvelope.detailLevel=" . $this->detail_level;
		$post_vars_nvp .= "&actionType=PAY";

		$post_vars_nvp .= "&currencyCode=" . $this->currency_code;
		$post_vars_nvp .= "&feesPayer=" . $this->fees_payer;
		$post_vars_nvp .= "&memo=" . $this->payment_note;
		$post_vars_nvp .= "&trackingId=" . $this->tracking_id;

		$post_vars_nvp .= "&receiverList.receiver(0).email=" . $this->primary_reciever;
		$post_vars_nvp .= "&receiverList.receiver(0).amount=" . $this->primary_reciever_amount;
		//if secondary receiver not set, we cannot use chained payment,
		//since for chained payment, there must be only one primary and atleast one secondary
		if($this->isSecondaryReceiverSet)
			$post_vars_nvp .= "&receiverList.receiver(0).primary=".$this->isPrimary;
		else
			$post_vars_nvp .= "&receiverList.receiver(0).primary=false";
		if($this->isSecondaryReceiverSet)
		{
			$i=1;
			foreach($this->secondary_reciever as $sec_receiver)
			{
				$post_vars_nvp .= "&receiverList.receiver(".$i.").email="  . $sec_receiver['email'];
				$post_vars_nvp .= "&receiverList.receiver(".$i.").amount=" . $sec_receiver['amount'];
				$post_vars_nvp .= "&receiverList.receiver(".$i.").primary=false";
				$i++;
			}
		}

		$post_vars_nvp .= "&returnUrl=". $this->return_url;
		$post_vars_nvp .= "&cancelUrl=". $this->cancel_url;
		$post_vars_nvp .= "&ipnNotificationUrl=". $this->ipn_notification_url;

		$response = $this->sendRequest($post_vars_nvp, 'Pay');

		$response_arr = $this->deformatNVP($response);
		Log::info("=============response=============");
		Log::info(print_r($response_arr,1));
		Log::info("=============response=============");

		if (isset($response_arr['responseEnvelope.ack']) &&  $response_arr['responseEnvelope.ack'] == 'Success' &&
			isset($response_arr['paymentExecStatus']) &&  $response_arr['paymentExecStatus'] == 'CREATED') {
			return  'none@'.$response_arr['payKey'].'@'.$this->tracking_id.'@'.$this->paypal_approval_url . '?cmd=_ap-payment&paykey=' . $response_arr['payKey'];
		} else {
			$emsg = $response_arr['error(0).message'];
			return 'error@'.$emsg;
		}
	}

	public function redirectURLToPaypal($param) {
		return $this->paypal_approval_url . '?cmd=_ap-payment&paykey=' . $param;
	}

	public function sendRequest($post_vars_nvp = '', $api_operation = '') {
		$curl = curl_init();

		$url = $this->api_endpoint;

		if ($api_operation != '')
			$url = $this->api_endpoint . '/' . $api_operation;

		$ch = curl_init();
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_vars_nvp);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->buildHeaders());

		if($this->api_mode == 'Certificate') {
			curl_setopt($curl, CURLOPT_SSLCERT, $this->cert_key_pem_file);
		}

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
	}

	public function buildHeaders() {
		$headers =  array(
			'X-PAYPAL-REQUEST-DATA-FORMAT: ' . $this->request_data_format,
			'X-PAYPAL-RESPONSE-DATA-FORMAT: ' . $this->response_data_format,
			'X-PAYPAL-SECURITY-USERID:  ' . $this->api_username,
			'X-PAYPAL-SECURITY-PASSWORD: ' . $this->api_password,
			'X-PAYPAL-SECURITY-SIGNATURE: ' . $this->api_signature,
			'X-PAYPAL-SERVICE-VERSION: ' . $this->api_version,
			'X-PAYPAL-APPLICATION-ID: ' . $this->api_appid
		);

		if($this->env == 'sandbox' AND $this->sandbox_email != '') {
			array_push($headers, 'X-PAYPAL-SANDBOX-EMAIL-ADDRESS: ' . $this->sandbox_email);
		}

		return $headers;
	}

	private function deformatNVP($nvpstr) {
		$intial = 0;
	 	$nvpArray = array();

		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	     }

		 return $nvpArray;
	}

	public function paymentDetails($paykey) {
		$post_vars_nvp = '';

		$post_vars_nvp .= "requestEnvelope.errorLanguage=" . $this->error_language;
		$post_vars_nvp .= "&payKey=" . $paykey;

		$response = $this->sendRequest($post_vars_nvp, 'PaymentDetails');

		$response_arr = $this->deformatNVP($response);

		return $response_arr;
	}

	public function Refund($paykey)
	{
		$post_vars_nvp = '';

		$post_vars_nvp .= "requestEnvelope.errorLanguage=" . $this->error_language;
		$post_vars_nvp .= "&currencyCode=" . $this->currency_code;
		$post_vars_nvp .= "&payKey=" . $paykey;
		$post_vars_nvp .= "&ipnNotificationUrl=". $this->ipn_notification_url;

		$response = $this->sendRequest($post_vars_nvp, 'Refund');

		$response_arr = $this->deformatNVP($response);

		return $response_arr;
	}

	public function VerifyPaypalEmail($email)
	{
		$post_vars_nvp = '';
		$post_vars_nvp .= "requestEnvelope.errorLanguage=" . $this->error_language;
		$post_vars_nvp .= "&emailAddress=" . $email;
		$post_vars_nvp .= "&matchCriteria=NONE";
		$this->api_endpoint	= Config::get('paypaladaptive.'.$this->env.'_end_point_email_verify');
		$this->api_version = Config::get('paypaladaptive.api_version_paypal_email_verify');

		$response = $this->sendRequest($post_vars_nvp, 'GetVerifiedStatus');

		$response_arr = $this->deformatNVP($response);
		return $response_arr;
	}
}