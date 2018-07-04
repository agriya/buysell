<?php
class WalletInterface implements PaymentInterface {

	protected $wallet;

	public function setObject()
	{
		$this->wallet = new ProcessPaypalAdaptivePaymentsService();
	}

	public function getObject()
	{
		return $this->wallet;
	}

	public function setTestMode($test_mode = true)
	{
		echo 'This is Wallet interface - set test mode called';
	}

	public function initialize($data = array())
	{
		if(isset($data['order_id']) && $data['order_id'] > 0) {
			$this->wallet->setOrderDetails($data['order_id']);
			$this->wallet->setCommonInvoiceDetailsByReference('Products', $data['order_id']);
		}
	}

	public function pay($data = array())
	{
		echo 'This is Wallet interface - pay called';
	}

	public function validate($data = '')
	{
		$this->wallet->updateOrderStatus();
		$this->wallet->updateCommonInvoiceStatus(array('is_credit_payment' => 'Yes'));
		$this->wallet->updateReceiversPaymentStatus('completed', 'Wallet');
		$this->wallet->generateInvoiceForOrder();
		$this->wallet->setOrderDetails($data);
		$this->wallet->setCommonInvoiceDetailsByReference('Products', $data);
		$this->wallet->sendInvoiceMailToUser();
	}
}
?>