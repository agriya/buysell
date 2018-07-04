<?php
class DummyInterface implements PaymentInterface {

	protected $dummy;

	public function setObject()
	{
		$this->dummy = new ProcessPaypalAdaptivePaymentsService();
	}

	public function getObject()
	{
		return $this->dummy;
	}

	public function setTestMode($test_mode = true)
	{
		echo 'This is Dummy interface - set test mode called';
	}

	public function initialize($data = array())
	{
		if(isset($data['order_id']) && $data['order_id'] > 0) {
			$this->dummy->setOrderDetails($data['order_id']);
			$this->dummy->setCommonInvoiceDetailsByReference('Products', $data['order_id']);
		}
	}

	public function pay($data = array())
	{
		echo 'This is Dummy interface - pay called';
	}

	public function validate($data = '')
	{
		$this->dummy->updateOrderStatus();
		$this->dummy->updateReceiversPaymentStatus('completed', 'Dummy');
		$this->dummy->generateInvoiceForOrder();
		$this->dummy->setOrderDetails($data);
		$this->dummy->sendInvoiceMailToUser();
	}
}
?>