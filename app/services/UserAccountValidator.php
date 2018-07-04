<?php

class UserAccountValidator extends Illuminate\Validation\Validator
{

	public function validateIsBoolean($attribute, $value, $parameters)
	{
		if((boolean)$value == true || (boolean)$value == false)
		{
			return true;
		}
		return false;
	}
	public function validateIsValidPrice($attribute, $value, $parameters)
	{
		if (preg_match("/^[0-9]+(\\.[0-9]{1,2})?$/", $value))
			{
				return true;
			}
		return false;
	}

	public function validateCustAfter($attribute, $value, $parameters)
	{
		return strtotime($parameters[1]) > strtotime($parameters[0]);
	}

	public function validateCustEqualOrAfter($attribute, $value, $parameters)
	{
		return strtotime($parameters[1]) >= strtotime($parameters[0]);
	}

	public function validateGreaterThan($attribute, $value, $parameters)
	{
		return $parameters[1] > $parameters[0];
	}

	public function validateIsValidSlugUrl($attribute, $value, $parameters)
	{
		if (preg_match("/^[a-z0-9]([a-z0-9-])+?[a-z0-9]$/", $value))
		{
		      return true;
		}
		return false;
	}

	public function validateIsUserCodeExists($attribute, $value, $parameters)
	{
		//$count = DB::table('users_shop_details')->whereRaw("user_id = ? AND is_shop_owner = ?", array($parameters[0], $parameters[1]))->count();
		$count = DB::table('users')->whereRaw("id = ? AND is_shop_owner = ?", array($parameters[0], $parameters[1]))->count();
		if($count)
			return true;
		return false;
	}

	public function validateAllowedWithdrawalAmount($attribute, $value, $parameters)
	{
		$user_id = $parameters[0];
		$withdraw_amount = $parameters[1];
		$withdraw_currency = $parameters[2];
		return Credits::checkIsAllowedWithdrawalAmount($user_id, $withdraw_amount, $withdraw_currency);
	}

	public function validateAllowedMinWithdrawalAmount($attribute, $value, $parameters)
	{
		$withdraw_amount = $parameters[0];
		$withdraw_currency = $parameters[1];

		$min_withdrawal_amount = 0;
		if($withdraw_currency == "USD")
		{
			$min_withdrawal_amount = Config::has("payment.minimum_withdrawal_amount") ? Config::get("payment.minimum_withdrawal_amount") : 0;
		}
		elseif($withdraw_currency == "INR")
		{
			$min_withdrawal_amount = Config::has("payment.minimum_withdrawal_amount_inr") ? Config::get("payment.minimum_withdrawal_amount_inr") : 0;
		}
		if(round($withdraw_amount, 2) >= round($min_withdrawal_amount, 2))
			return true;
		return false;
	}

	public function validateAllowedWithdrawalCurrency($attribute, $value, $parameters)
	{
		//$user_id = getAuthUser()->user_id;
		$user_id = $parameters[0];
		$withdraw_currency = $parameters[1];
		return Credits::checkIsAllowedWithdrawalCurrency($user_id, $withdraw_currency);
	}

	public function validateIsValidSerialNumbers($attribute, $value, $parameters)
	{
		$serial_arr = explode("\n", $value);
		if (count($serial_arr) == trim($parameters[0]))
			{
				return true;
			}
		return false;
	}

	public function validateCheckEmptyLines($attribute, $value, $parameters)
	{
		$serial_arr = explode("\n", $value);
    	$empty_lines = false;
    	for($i = 0; $i < count($serial_arr); $i++) {
    		if (trim($serial_arr[$i]) == "") {
    			$empty_lines = true;
    		}
    	}
    	if ($empty_lines) {
    		return false;
    	}
		return true;
	}

	public function validateIsDuplicateSerialNumbers($attribute, $value, $parameters){
		$serial_arr = explode("\n", $value);
		$serial_arr = array_map('trim', $serial_arr);
		return (count($serial_arr) == count(array_unique($serial_arr)));
	}

	public function validateRequiredIfIn($attribute, $value, $parameters)
	{
		if(!isset($parameters[0]))
			return true;
		$check_value = $parameters[0];
		$other = array_shift($parameters);
		if(empty($parameters))
			return true;
		if(in_array($check_value, $parameters))
		{
			if($value!='')
				return true;
			else
				return false;
		}
		else
			return true;

	}
	/*
	Added to check the duplicate in the db also
	public function validateIsSerialNumberExists($attribute, $value, $parameters)
	{
		$serial_arr = explode("\n", $value);
		$serial_arr = array_map('trim', $serial_arr);

		$serial_arr = array_map(array($this,'addnewline'), $serial_arr);
		$final_reg_exp_string = implode('|',$serial_arr);
		$count = DB::table('product_stocks')->whereRaw("serial_numbers REGEXP '".$final_reg_exp_string."'");
		if(isset($parameters[0]))
			$count->where('product_id','!=',$parameters[0]);
		if(isset($parameters[1]))
			$count->where('stock_country_id','!=',$parameters[1]);
		$count = $count->count();

		$queries = DB::getQueryLog();
		$last_query = end($queries);
		Log::info('final serial string count query: ');
		Log::info(print_r($last_query,1));


		if($count)
			return false;

		// check the same for othere countries
		$count = DB::table('product_stocks')->whereRaw("serial_numbers REGEXP '".$final_reg_exp_string."'");
		if(isset($parameters[0]))
			$count->where('product_id','=',$parameters[0]);
		if(isset($parameters[1]))
			$count->where('stock_country_id','!=',$parameters[1]);
		$count = $count->count();

		$queries = DB::getQueryLog();
		$last_query = end($queries);
		Log::info('final serial string count query: ');
		Log::info(print_r($last_query,1));

		if($count)
			return false;
		return true;
	}
	public function addnewline($string)
	{
		return '^'.$string.'$|^'.$string.'\r\n|\r\n'.$string.'$|\r\n'.$string.'\r\n';
		//return array($string,'%\r\n'.$string, $string.'\r\n%', '%\r\n'.$string.'\r\n%');
	}*/

	public function validateTopPicksUser($attribute, $value, $parameters)
	{
		$result = true;
		$user_id = User::whereRaw('user_name = ? ', array($parameters[0]))->pluck('id');
		if($user_id == '') {
			$this->addError($attribute, 'invalid_username', $parameters);
			$result = false;
		}
		else {
			$count = UsersTopPicks::whereRaw('user_id = ? AND top_pick_id != ?', array($user_id, $parameters[1]))->count();
			if($count) {
				$this->addError($attribute, 'username_exist', $parameters);
				$result = false;
			}
		}
		$fav_count = ProductFavorites::whereRaw('user_id = ?', array($user_id))->count();
		if($fav_count == 0) {
			$this->addError($attribute, 'atleast_one_favorite', $parameters);
			$result = false;
		}
		return $result;
	}

	public function validateFeaturedSellers($attribute, $value, $parameters)
	{
		$result = true;
		$user_id = User::whereRaw('user_name = ? ', array($parameters[0]))->pluck('id');
		if($user_id == '') {
			$this->addError($attribute, 'invalid_username', $parameters);
			$result = false;
		}
		else {
			$count = UsersFeatured::whereRaw('user_id = ? AND featured_id != ?', array($user_id, $parameters[1]))->count();
			if($count) {
				$this->addError($attribute, 'username_exist', $parameters);
				$result = false;
			}
		}
		$prod_count = Product::whereRaw('product_user_id = ? AND product_status = ?', array($user_id, 'Ok'))->count();
		if($prod_count == 0) {
			$this->addError($attribute, 'atleast_one_product', $parameters);
			$result = false;
		}
		return $result;
	}

	public function validateFavoriteProducts($attribute, $value, $parameters)
	{
		$result = true;
		$arr = explode('-',trim($parameters[0]));
		$product_id = Product::whereRaw('product_code = ? ', array($arr[0]))->pluck('id');
		if($product_id == '') {
			$this->addError($attribute, 'invalid_product', $parameters);
			$result = false;
		}
		else {
			$count = UsersFavoritesProducts::whereRaw('product_id = ? AND favorite_id != ?', array($product_id, $parameters[1]))->count();
			if($count) {
				$this->addError($attribute, 'product_code_exist', $parameters);
				$result = false;
			}
		}
		$fav_count = ProductFavorites::whereRaw('product_id = ?', array($product_id))->count();
		if($fav_count == 0) {
			$this->addError($attribute, 'atleast_one_favorite', $parameters);
			$result = false;
		}
		return $result;
	}

	public function validateIsValidSolveMedia($attribute, $value, $parameters)
	{
		$private_key = Config::get('generalConfig.verification_key');
		$hash_key = Config::get('generalConfig.authentication_key');

		$solvemedia_response = solvemedia_check_answer($private_key,
														$_SERVER["REMOTE_ADDR"],
														$parameters[1],
														$parameters[0],
														$hash_key);
		if ($solvemedia_response->is_valid)
		{
			return true;
		}
		return false;
	}
	public function validateCaptcha($attribute, $value, $parameters)
    {
    	if(class_exists('Mews\Captcha\Facades\Captcha'))
        	return Captcha::check($value);
        return false;
    }
}