<?php
class CouponService
{
	public function generateSlug($name = '')
	{
		if(is_null($name) || $name == '')
			return '';
		else
			return \Str::slug($name);
	}

	public function addCoupon($inputs){
		$collection = new Coupon();
		$collection_id  = $collection->addNew($inputs);
		return $collection_id;
	}

	public function setCouponsFilter($inputs = array()){
		if(isset($inputs['coupon_code']))
			$this->coupon_code = $inputs['coupon_code'];
	}

	public function getCoupons($user_id, $return_type = 'get', $limit = 10)
	{
		$coupon = Coupon::where('user_id',$user_id);
		if(isset($this->coupon_code) && $this->coupon_code!='')
			$coupon->where('coupon_code', 'like', '%'.$this->coupon_code.'%');
		$coupon->orderby('id','desc');
		if($return_type == 'paginate')
			return $coupon->paginate($limit);
		else
			return $coupon->get();
	}

	public function addCollectionProducts($inputs = array())
	{
		if(!is_array($inputs) || empty($inputs))
			return false;

		DB::table('collection_products')->insert($inputs);
		return true;
	}

	public function getCouponDetails($coupon_id)
	{
		$coupon = Coupon::where('id',$coupon_id)->first();
		if(count($coupon) > 0)
			return $coupon;
		else
			return false;
	}

	public function getCouponDetailsByCode($coupon_code)
	{
		$coupon = Coupon::where('coupon_code',$coupon_code)->first();
		if(count($coupon) > 0)
			return $coupon;
		else
			return false;
	}

	public function getCollectionProductIds($collection_id)
	{
		$collection_products = CollectionProduct::where('collection_id',$collection_id)->orderby('order','asc')->lists('product_id');
		if(count($collection_products) > 0)
			return $collection_products;
		else
			return false;
	}

	public function updateCoupon($coupon_id, $inputs = array())
	{
		if(!is_array($inputs) || empty($inputs))
			return false;

		$coupon = new Coupon();
		$valid_inputs = $coupon->getTableFields();
		$valid_input_keys = array_fill_keys($valid_inputs,'');
		$inputs = array_intersect_key($inputs,$valid_input_keys);
		$couponupdate = Coupon::where('id',$coupon_id)->update($inputs);
		return $couponupdate;
	}

	public function removeCollectionProducts($collection_id)
	{
		$collection_products = CollectionProduct::where('collection_id',$collection_id)->delete();
	}

	public function deleteCoupon($coupon_id = 0)
	{
		if(is_null($coupon_id) || $coupon_id<=0)
			return false;

		$deleted = Coupon::where('id',$coupon_id)->delete();
		return $deleted;
	}

	public function validateCouponCode($coupon_code, $item_owner_id, $total_amount)
	{
		$coupon_det = $this->getCouponDetailsByCode($coupon_code);
		$return_arr = array('status' => 'failure', 'error_message' => trans('common.invalid_coupon_code'));
		if($coupon_det && count($coupon_det)>0)
		{
			if($coupon_det->status == 'Active')
			{
				$today = strtotime(date('Y-m-d'));
				$from_date = strtotime($coupon_det->from_date);
				$to_date = strtotime($coupon_det->to_date);

				if($today >= $from_date && $today <= $to_date)
				{
					$price_from = Cutil::formatAmount($coupon_det->price_from);
					$price_to = Cutil::formatAmount($coupon_det->price_to);
					if($coupon_det->user_id == $item_owner_id)
					{
						$price_restriction = $coupon_det->price_restriction;
						$valid = false;
						$error_message = '';
						switch($price_restriction)
						{
							case 'none':
								$valid = true;
								break;

							case 'between':
								if(($total_amount >= $price_from) && ($total_amount <=$price_to))
									$valid = true;
								else
									$error_message = 'Coupon not applicable since total amount not in between the specified amount';
								break;

							case 'less_than':
								if($total_amount < $price_from)
									$valid = true;
								else
									$error_message = 'Coupon not applicable since total amount greater than specified amount';
								break;

							case 'greater_than':
								if($total_amount > $price_from)
									$valid = true;
								else
									$error_message = 'Coupon not applicable since total amount less than specified amount';
								break;

							case 'equal_to':
								if($total_amount == $price_from)
									$valid = true;
								else
									$error_message = 'Coupon not applicable since total amount not equal to the specified amount';
								break;
						}
						if($valid)
						{
							$offer_type = $coupon_det->offer_type;
							$offer_amount = Cutil::formatAmount($coupon_det->offer_amount);
							if(strtolower($offer_type) == 'percentage')
							{
								$discount_amount = $total_amount*($offer_amount/100);
								$discount_amount = round($discount_amount,2);
								$discounted_amount = $total_amount-$discount_amount;
								$return_arr = array('status' => 'success', 'discount_amount' => $discount_amount, 'discounted_amount' => $discounted_amount);
							}
							elseif(strtolower($offer_type) == 'flat')
							{
								if($total_amount >= $offer_amount)
								{
									$discount_amount = $offer_amount;
									$discount_amount = round($discount_amount,2);
									$discounted_amount = $total_amount-$discount_amount;
									$return_arr = array('status' => 'success', 'discount_amount' => $discount_amount, 'discounted_amount'=> $discounted_amount);
								}
								else
								{
									$return_arr = array('status' => 'failure', 'error_message' => 'Coupon not applicable since offer amount is greater than the total amount');
								}
							}
						}
						else
						{
							$return_arr = array('status' => 'failure', 'error_message' => $error_message);
						}
					}
					else
					{
						$return_arr = array('status' => 'failure', 'error_message' => trans('common.invalid_coupon_code'));
					}
				}
				else
				{
					$return_arr = array('status' => 'failure', 'error_message' => 'Coupon Expired');
				}
			}
			else
			{
				$return_arr = array('status' => 'failure', 'error_message' => trans('common.invalid_coupon_code'));
			}
		}
		else{
			$return_arr = array('status' => 'failure', 'error_message' => trans('common.invalid_coupon_code'));
		}

		if(isset($return_arr['status']) && $return_arr['status']=='success')
		{
			$return_arr['discount_amount_formatted'] = CUtil::convertAmountToCurrency($return_arr['discount_amount'], Config::get('generalConfig.site_default_currency'), '', true);
			$return_arr['discount_amount_formatted_curr'] = CUtil::convertAmountToCurrency($return_arr['discount_amount'], Config::get('generalConfig.site_default_currency'), '', true);

			$return_arr['discounted_amount_formatted'] = CUtil::convertAmountToCurrency($return_arr['discounted_amount'], Config::get('generalConfig.site_default_currency'), '', true);
			$return_arr['discounted_amount_formatted_curr'] = CUtil::convertAmountToCurrency($return_arr['discounted_amount'], Config::get('generalConfig.site_default_currency'), '', true);
		}
		return $return_arr;
	}
}