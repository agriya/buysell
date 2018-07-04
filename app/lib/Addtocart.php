<?php

class Addtocart {

	public static function initialize($cart_id = '')
	{
		return new Addtocarts($cart_id);
	}
}
?>