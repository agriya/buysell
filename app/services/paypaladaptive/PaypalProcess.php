<?php

class PaypalProcess extends Paypal {

	private $ipn_data 				= array();

	private $payment_details		= array();

	private $paypal_adaptive_transaction_id	 = 0;

	private $paypal_adaptive_transaction_details	 = array();

	//private $paypal_trans			= array();

	//private $invoice_item_details	= array();

	private $errno					= 0;

	private $primary_receiver		= 0; //by default for parallel

	private $secondary_receiver		= 1; //by default for parallel

	public $log_details = true;

	public $payment_method = '';

	function __construct()
	{
		//$this->paypal_obj = Paypaladaptive::initialize();
		//$this->paypal_obj->setPaymentMode(true);
		//$this->paypal_obj->initializePayment();
		$this->paypal_transaction = Paypaladaptive::initializePaypalTransaction();
	}

	/**
	 * PaypayAdaptivePaymentsProcess::processIPN()
	 *
	 * @return
	 */
	public function processIPN($ipn_request)
	{
		// set ipn posted data
		$this->setIPNPostVars($ipn_request);

		// get payment details by using paykey from ipn data and set the details for usage by the class
		$this->setPaymentDetails();

		// set primary and secondary receivers index
		$this->setPrimarySecondaryReceivers();

		// make sure that transaction is valid and without errors.
		$this->validateTransaction();

		// record the transaction and status
		$this->logTransaction();
	}

	/**
	 * PaypalProcess::setIPNPostVars()
	 * set and sanitaize ipn data post vars
	 *
	 * @param mixed $request_fields
	 * @return
	 */
	public function setIPNPostVars($request_fields)
	{
		foreach ($request_fields as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $sub_key => $sub_value) {
					$this->ipn_data[$key][$sub_key] = htmlspecialchars(urldecode(trim($sub_value)));
				}
			} else {
				$this->ipn_data[$key] = htmlspecialchars(urldecode(trim($value)));
			}
		}
		//echo '<pre>';print_r($this->ipn_data);exit;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::setPaymentDetails()
	 * set payment details using the ipn data pay key
	 *
	 * @return
	 */
	public function setPaymentDetails()
	{
		if (!$this->isPayKey()) { die('<h2>Error</h2><p>Pay key not available in IPN data, cannot proceed!'); }
		$this->payment_details = $this->paymentDetails($this->getPayKey());
		//echo '<pre>';print_r($this->payment_details);exit;
		$this->setPaymentDetailsPostVars($this->payment_details);
	}

	/**
	 * PaypayAdaptivePaymentsProcess::setPaymentDetailsPostVars()
	 * set and sanitaize payment details post vars
	 *
	 * @param mixed $payment_details
	 * @return
	 */
	public function setPaymentDetailsPostVars($payment_details)
	{
		foreach ($payment_details as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $sub_key => $sub_value) {
					$this->payment_details[$key][$sub_key] = htmlspecialchars(urldecode(trim($sub_value)));
				}
			} else {
				$this->payment_details[$key] = htmlspecialchars(urldecode(trim($value)));
			}
		}
	}

	/**
	 * PaypayAdaptivePaymentsProcess::setPrimarySecondaryReceivers()
	 * Set primary and secondary receivers of the transaction
	 *
	 * @return
	 */
	private function setPrimarySecondaryReceivers()
	{
		//echo "<pre>";print_r($this->payment_details);echo "</pre>";exit;
		if ($this->payment_details['paymentInfoList.paymentInfo(0).receiver.primary']) {
			$this->primary_receiver = 0;
			$this->secondary_receiver = 1;
		} else if ($this->payment_details['paymentInfoList.paymentInfo(1).receiver.primary']) {
			$this->primary_receiver = 1;
			$this->secondary_receiver = 0;
		}
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getPaymentMethod()
	 * Set primary and secondary receivers of the transaction
	 *
	 * @return
	 */
	public function getPaymentMethod()
	{
		$this->payment_method = 'Parallel';
		if ($this->payment_details['paymentInfoList.paymentInfo(0).receiver.primary'] || $this->payment_details['paymentInfoList.paymentInfo(1).receiver.primary']) {
			$this->payment_method = 'Chained';
		}
		return $this->payment_method;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::validateTransaction()
	 *
	 * @return
	 */
	function validateTransaction()
	{
		$this->errno  |=  $this->_isVerified() ? 0 : (1<<0);
		$this->errno  |=  $this->_isVaidPaymentStatus() ? 0 : (1<<1);
	}

	/**
	 * PaypayAdaptivePaymentsProcess::_isVerified()
	 * Is tranasctionn verified
	 *
	 * @return
	 */
	function _isVerified()
	{
		$resp= ($this->ipn_data['pay_key'] == $this->payment_details['payKey'] &&
				$this->ipn_data['tracking_id'] == $this->payment_details['trackingId'] &&
				$this->payment_details['currencyCode'] == 'USD' &&
				(
					(Config::get('payment.paypal_test_mode') && $this->ipn_data['test_ipn'] == 1) ||
					(!Config::get('payment.paypal_test_mode') && $this->ipn_data['test_ipn'] != 1)
				) );
		return $resp;
	}

	function _isVaidPaymentStatus()
	{
		$res = strcmp(strtoupper(trim($this->payment_details['status'])), 'COMPLETED');
		if ($res == 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::logTransaction()
	 * Insert transaction of the payment details
	 *
	 * @return
	 */
	public function logTransaction()
	{
		$ipn_nv 				= $this->getIpnNV();
		$payment_details_nv 	= $this->getPaymentDetailsNV();

		if ($this->isRefundTransaction()) {
			$primary_receiver_trans_id = $this->payment_details['paymentInfoList.paymentInfo('.$this->primary_receiver . ').senderTransactionId'];
			$buyer_trans_id = '';
		} else  {
			$buyer_trans_id = $this->payment_details['paymentInfoList.paymentInfo(' . $this->primary_receiver . ').senderTransactionId'];
			$primary_receiver_trans_id = isset($this->payment_details['paymentInfoList.paymentInfo('.$this->secondary_receiver . ').senderTransactionId']) ? $this->payment_details['paymentInfoList.paymentInfo('.$this->secondary_receiver . ').senderTransactionId'] : '';
		}

		$payment_receiver_details = $this->getPaymentReceiverDetailsInArray();
		$payment_receiver_details_ser = serialize($payment_receiver_details);
		//echo $payment_receiver_details_ser;

		//Transaction entry made in adaptive payment package
		$this->paypal_transaction->setPayKey($this->ipn_data['pay_key']);
		$this->paypal_transaction->setTrackingId($this->payment_details['trackingId']);
		$this->paypal_transaction->setCurrencyCode($this->payment_details['currencyCode']);
		$this->paypal_transaction->setBuyerEmail($this->payment_details['sender.email']);
		$this->paypal_transaction->setReceiverDetails($payment_receiver_details_ser);
		$this->paypal_transaction->setIpnPostString($ipn_nv);
		$this->paypal_transaction->setPaymentDetailsString($payment_details_nv);
		$this->paypal_transaction->setErrorId($this->errno);
		$this->paypal_transaction->setStatus($this->payment_details['status']);
		$this->paypal_transaction->setBuyerTransId($buyer_trans_id);
		$paypal_adaptive_transaction_det = $this->paypal_transaction->add();
		$paypal_adaptive_transaction_det = json_decode($paypal_adaptive_transaction_det);


		$this->paypal_adaptive_transaction_id = isset($paypal_adaptive_transaction_det->transaction_id)?$paypal_adaptive_transaction_det->transaction_id:0;
	}


	public function getPaypalAdaptivePaymentTransaction()
	{
		return $this->paypal_adaptive_transaction_id;
	}

	public function getPaypalAdaptivePaymentTransactionDetails()
	{
		//if(!isset($this->paypal_adaptive_transaction_details))
		//{
			$trans_id = isset($this->paypal_adaptive_transaction_id)?$this->paypal_adaptive_transaction_id:0;
			//echo "<br>trans_id: ".$trans_id;exit;
			$this->paypal_adaptive_transaction_details = $this->paypal_transaction->getPaypalAdaptivePaymentDetails($trans_id);
		//}
		///echo "<pre>";print_r($this->paypal_adaptive_transaction_details);echo "</pre>";exit;
		return $this->paypal_adaptive_transaction_details;

	}

	/**
	 * PaypayAdaptivePaymentsProcess::isPayKey()
	 * check if pay_key field is set or not
	 *
	 * @return
	 */
	public function isPayKey()
	{
		return isset($this->ipn_data['pay_key']) && !empty($this->ipn_data['pay_key']) ? true : false;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getPayKey()
	 * return the ipn data pay key
	 *
	 * @return
	 */
	public function getPayKey()
	{
		return isset($this->ipn_data['pay_key']) && !empty($this->ipn_data['pay_key']) ? $this->ipn_data['pay_key'] : false;
	}


	/**
	 * PaypayAdaptivePaymentsProcess::getIpn()
	 * returns ipn data in array format
	 *
	 * @return
	 */
	public function getIpn()
	{
		return $this->ipn_data;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getIpnNV()
	 * returns ipn data in name value format
	 *
	 * @return
	 */
	public function getIpnNV()
	{
		$ipn_data_str = '';

		foreach($this->ipn_data as $key=>$value) {
			if (!is_array($value)) {
				$value = urlencode(stripslashes($value));
				$ipn_data_str .= "&$key=$value";
			} else {
				foreach ($value as $skey=>$svalue) {
					$svalue = urlencode(stripslashes($svalue));
					$ipn_data_str .= "&$key[$skey]=$svalue";
				}
			}
		}

		return $ipn_data_str;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getPaymentDetailsNV()
	 * returns payment details in array format
	 *
	 * @return
	 */
	public function getPaymentDetails()
	{
		return $this->payment_details;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getPaymentDetailsNV()
	 * returns payment details in name value format
	 *
	 * @return
	 */
	public function getPaymentDetailsNV()
	{
		$payment_details_str = '';

		foreach($this->ipn_data as $key=>$value) {
			if (!is_array($value)) {
				$value = urlencode(stripslashes($value));
				$payment_details_str .= "&$key=$value";
			} else {
				foreach ($value as $skey=>$svalue) {
					$svalue = urlencode(stripslashes($svalue));
					$payment_details_str .= "&$key[$skey]=$svalue";
				}
			}
		}

		return $payment_details_str;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::isRefundTransaction()
	 * Check if the tranasction is refund
	 *
	 * @return
	 */
	public function isRefundTransaction()
	{
		if ((isset($this->ipn_data['reason_code']) && strcmp($this->ipn_data['reason_code'], 'Refund') == 0) OR
		   (isset($this->ipn_data['transaction'][1]) && strcmp($this->ipn_data['transaction'][1], 'Reversed') == 0) OR
		   (isset($this->payment_details['paymentInfoList.paymentInfo(0).transactionStatus']) AND
		   		  ($this->payment_details['paymentInfoList.paymentInfo(0).transactionStatus'] == 'REVERSED'  OR
				   $this->payment_details['paymentInfoList.paymentInfo(0).transactionStatus'] == 'REFUNDED')
		   ) OR
		    (isset($this->payment_details['paymentInfoList.paymentInfo(1).transactionStatus']) AND
		   		  ($this->payment_details['paymentInfoList.paymentInfo(1).transactionStatus'] == 'REVERSED'  OR
				   $this->payment_details['paymentInfoList.paymentInfo(1).transactionStatus'] == 'REFUNDED')
		   )
		   )
		{
			$this->writetoTempFile( 'Is a  REfund Transaction');
			return true;
		}
		else
		{
			$this->writetoTempFile( 'Is not a Refund Transaction');
			return false;
		}
	}

	public function getPaymentReceiverDetailsInArray()
	{
		$receivers = array();
		//echo "<pre>";print_r($this->payment_details);echo "</pre>";
		for($i=0;$i<=5;$i++)
		{
			//paymentInfoList.paymentInfo(0).receiver.email
			if(isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.email']))
			{
				$email = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.email'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.email']:'';
				$amount = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.amount'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.amount']:'';
				$primary = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.primary'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.primary']:'';
				$transactionId = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').transactionId'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').transactionId']:'';
				$transactionStatus = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').transactionStatus'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').transactionStatus']:'';
				$paymentType = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.paymentType'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.paymentType']:'';
				$accountId = isset($this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.accountId'])?$this->payment_details['paymentInfoList.paymentInfo('.$i . ').receiver.accountId']:'';
				$receivers[$i] = compact('email','amount','primary','transactionId','transactionStatus','paymentType','accountId');
			}
		}
		$this->receiver_details = $receivers;
		//echo "<pre>";print_r($receivers);echo "</pre>";	exit;
		return $receivers;

	}

	/**
	 * PaypayAdaptivePaymentsProcess::isTransactionOk()
	 *
	 * @return
	 */
	public function isTransactionOk()
	{
		return( !$this->errno );
	}

	public function chkAndCreateFolder($folderName)
	{
		//echo $folderName;//exit;
		$folder_arr = explode('/', $folderName);
		$folderName = '';
		foreach($folder_arr as $key=>$value)
			{
				$folderName .= $value.'/';
				if($value == '..' or $value == '.')
					continue;
				if (!is_dir($folderName))
					{
						mkdir($folderName);
						@chmod($folderName, 0777);
					}
			}
	}

	public function createErrorLogFile($prefixFileName='')
	{
		if($this->log_details)
		{
			$temp_dir = public_path().'/files/temp_payment_log'.'/';
			$this->chkAndCreateFolder($temp_dir);
			$temp_file_path = $temp_dir.$prefixFileName.'_'.time().'_'.rand(1,100).'.txt';
			@$this->fp = fopen($temp_file_path, 'w');
		}
	}

	public function writetoTempFile($str)
	{
		if($this->log_details)
		{
			if(@!$this->fp)
			{
			$this->createErrorLogFile();
			}
			@fwrite($this->fp, $str);
		}
	}
}