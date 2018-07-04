<?php
interface FavoriteInterface {

	public function getRules();

	public function validate($inputs = array());

	public function update($inputs = array());

	//public function initialize($data = array());

	//public function pay($data = array());

	//public function validate($data);
}
?>