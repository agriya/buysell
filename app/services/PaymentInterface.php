<?php
interface PaymentInterface {

	public function setObject();

	public function getObject();

	public function setTestMode($test_mode = true);

	public function initialize($data = array());

	public function pay($data = array());

	public function validate($data);
}
?>