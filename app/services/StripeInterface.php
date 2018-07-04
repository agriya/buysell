<?php
class StripeInterface implements PaymentInterface {

	public $test_mode = true;

	public function setTestMode($test_mode = true)
	{
		$this->test_mode = $test_mode;
	}

	public function initialize($data = array(), $obj)
	{
		return 'Stripe payment';
	}

	public function pay($data = array(), $obj)
	{
		return 'Stripe payment';
	}
}
?>