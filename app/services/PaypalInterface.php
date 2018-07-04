<?php
class PaypalInterface implements PaymentInterface {

	protected $adaptive;

	public function setObject()
	{
		//$this->adaptive = Paypaladaptive::initialize();
		$this->adaptive = Paypaladaptive::initializePaypalProcess();
	}

	public function getObject()
	{
		return $this->adaptive;
	}

	public function setTestMode($test_mode = true)
	{
		$this->adaptive->setPaymentMode($test_mode);
	}

	public function initialize($data = array())
	{
		$this->adaptive->initializePayment($data);
	}

	public function pay($data = array())
	{
		Log::info("==================Details==========================");
			Log::info(print_r($data,1));
		Log::info("============================================");
		$this->adaptive->setPrimaryReceiver($data['primary_reciever']['paypal_email'], $data['primary_reciever']['amount']);
		if(isset($data['secondary_reciever'])) {
			foreach($data['secondary_reciever'] AS $val) {
				$this->adaptive->setSecondaryReceiver($val['paypal_email'], $val['amount']);
			}
		}
		$this->adaptive->setCancelURL($data['cancel_url']);
		$this->adaptive->setReturnURL($data['return_url']);
		$this->adaptive->setNotoficationURL($data['notification_url']);
		$this->adaptive->setPaymentNote($data['payment_note']);
		$this->adaptive->setCurrencyCode($data['currency_code']);
		$this->adaptive->setTrackingId();
		$pay_key_response = $this->adaptive->getPay();
		return $pay_key_response;
	}

	public function validate($data = '')
	{
		$this->adaptive->processIPN($data);
		return $this->adaptive->isTransactionOk();
	}

	public function getIpnData()
	{
		return $this->adaptive->getIpn();
	}

	public function getPaymentDetailsData()
	{
		return $this->adaptive->getPaymentDetails();
	}

	public function getPaymentReceiverDetailsData()
	{
		return $this->adaptive->getPaymentReceiverDetailsInArray();
	}

	public function getIsRefundTransaction()
	{
		return $this->adaptive->isRefundTransaction();
	}
	public function getPaymentMethod()
	{
		return $this->adaptive->getPaymentMethod();
	}
	public function getPaypalAdaptivePaymentTransaction()
	{
		return $this->adaptive->getPaypalAdaptivePaymentTransaction();
	}

	public function getPaypalAdaptivePaymentTransactionDetails()
	{
		return $this->adaptive->getPaypalAdaptivePaymentTransactionDetails();
	}
}
?>