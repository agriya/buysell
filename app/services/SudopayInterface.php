<?php
class SudopayInterface implements PaymentInterface {

	protected $sudopay_service;
	protected $sa;
	protected $sc;

	public function setObject()
	{
		//$this->adaptive = Paypaladaptive::initialize();
		//$this->adaptive = Paypaladaptive::initializePaypalProcess();
		$this->sudopay_service = new \SudopayService();
		$mode = (Config::get('plugin.sudopay_payment_test_mode')) ? 'test' : 'live';
		$this->sudopay_credential = array(
		    'api_key' => Config::get('plugin.sudopay_'.$mode.'_api_key'),
		    'merchant_id' => Config::get('plugin.sudopay_'.$mode.'_merchant_id'),
		    'website_id' => Config::get('plugin.sudopay_'.$mode.'_website_id'),
		    'secret' => Config::get('plugin.sudopay_'.$mode.'_secret_string')
		);
		$this->sa = new \SudoPay_API($this->sudopay_credential);
		$this->sc = new \SudoPay_Canvas($this->sa);
	}

	public function getObject()
	{
		return $this->sa;
	}

	public function setTestMode($test_mode = true)
	{

	}

	public function initialize($data = array())
	{
		//$this->sudopay_service->initializePayment($data);
	}

	public function pay($data = array())
	{
		Log::info("================== Data start ==========================");
			Log::info(print_r($data, 1));
		Log::info("================== Data end =========================");
		//$pay_key_response = $this->sc->makeMarketplaceCapturePayment($data);
		if($data['action'] == 'marketplace-capture') {
			unset($data['action']);
			$pay_key_response = $this->sa->callMarketplaceCapture($data);
		}
		else {
			unset($data['action']);
			$pay_key_response = $this->sa->callCapture($data);

		}
		Log::info("================== Response start ==========================");
			Log::info(print_r($pay_key_response, 1));
		Log::info("================== Response end =========================");
		return $pay_key_response;
	}

	public function validate($data = '')
	{
		/*$this->adaptive->processIPN($data);
		return $this->adaptive->isTransactionOk();*/
	}

	/*public function getIpnData()
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
	}*/
}
?>