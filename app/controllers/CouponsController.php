<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
class CouponsController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->beforeFilter(function(){
			if(!CUtil::isUserAllowedToAddProduct()) {
				//Session::flash('error_message', trans('common.invalid_action'));
				return Redirect::to('users/request-seller');
			}
		}, array('except' => array('')));
    }

	//Taxations
	public function getIndex($user_id = null)
	{
		$couponservice = new CouponService();
		$err_msg = '';
		$is_search_done = 0;
		$inputs=Input::all();
		$user_id = BasicCUtil::getLoggedUserId();
		$couponservice->setCouponsFilter($inputs);
		$coupons = $couponservice->getCoupons($user_id, 'paginate', 10);
		$get_common_meta_values = Cutil::getCommonMetaValues('my-coupons');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('couponsList', compact('coupons', 'error_message', 'is_search_done'));
	}

	public function getAdd()
	{
		$user_id = BasicCUtil::getLoggedUserId();
		$is_edit = 0;
		$coupon_det = array();
		$get_common_meta_values = Cutil::getCommonMetaValues('add-coupons');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addCoupon',compact('user_id', 'is_edit', 'coupon_det'));
	}

	public function postAdd()
	{
		$inputs =Input::all();

		//if($inputs['from_date']!='')
//			$inputs['from_date'] = date_format(date_create_from_format('d/m/Y', $inputs['from_date']), 'Y-m-d');
//		if($inputs['to_date']!='')
//			$inputs['to_date'] = date_format(date_create_from_format('d/m/Y', $inputs['to_date']), 'Y-m-d');

		//$inputs['from_date'] = ($inputs['from_date']!='')?date('Y-m-d', strtotime($inputs['from_date'])):'';
		//$inputs['to_date'] = ($inputs['to_date'])?date('Y-m-d', strtotime($inputs['to_date'])):'';

		$rules = array(
			'coupon_code' 		=> 'required|alpha_dash|Unique:coupons,coupon_code',
			'from_date' 		=> 'required|after:'.date('Y-m-d',strtotime("-1 days")),
			'to_date'			=> 'required|after:'.date('Y-m-d', strtotime($inputs['from_date'].' -1 days')),
			'price_restriction'	=> 'required',
			'price'				=> 'required_if_in:'.$inputs['price_restriction'].',less_than,greater_than, equal_to|IsValidPrice|numeric|Min:0',
			'price_from'		=> 'required_if:price_restriction,between|IsValidPrice|numeric|Min:0',
			'price_to'			=> 'required_if:price_restriction,between|IsValidPrice|numeric|Min:0||GreaterThan:'.$inputs['price_from'].','.$inputs['price_to'],
			'offer_type'		=> 'required',
			'offer_amount'		=> 'required|numeric'
		);
		$couponservice = new CouponService();
		$messages = array('price_to.greater_than' => Lang::get('coupon.price_should_be_greater'));
		$validator = Validator::make($inputs,$rules,$messages);
		if($validator->passes())
		{
			if($inputs['price_restriction']!='none' && $inputs['price_restriction']!='between')
				$inputs['price_from'] = $inputs['price'];

			if(isset($inputs['price']) && $inputs['price'] >0)
				$inputs['price'] = Cutil::formatAmount($inputs['price']);

			if(isset($inputs['price_from']) && $inputs['price_from'] >0)
				$inputs['price_from'] = Cutil::formatAmount($inputs['price_from']);

			if(isset($inputs['price_to']) && $inputs['price_to'] >0)
				$inputs['price_to'] = Cutil::formatAmount($inputs['price_to']);

			$copon_id  = $couponservice->addCoupon($inputs);
			if($copon_id > 0)
			{
				return Redirect::action('CouponsController@getIndex')->with('success_message',Lang::get('coupon.coupon_added_success'));
			}
			else
			{
				return Redirect::action('CouponsController@getAdd')->with('error_message',Lang::get('common.some_problem_try_later'))->withInput();
			}
		}
		else
		{
			return Redirect::action('CouponsController@getAdd')->with('error_message', trans('common.correct_errors'))->withInput()->withErrors($validator);
		}
	}

	public function getUpdate($coupon_id = 0)
	{
		if(is_null($coupon_id) || $coupon_id <=0)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.select_valid_coupon'));

		$user_id = BasicCUtil::getLoggedUserId();
		$couponService = new CouponService();
		$coupon_det = $couponService->getCouponDetails($coupon_id);
		if(!$coupon_det)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.select_valid_coupon'));

		if(isset($coupon_det->price_from) && $coupon_det->price_from > 0)
			$coupon_det->price_from = Cutil::formatAmount($coupon_det->price_from);
		if(isset($coupon_det->price_to) && $coupon_det->price_to > 0)
			$coupon_det->price_to = Cutil::formatAmount($coupon_det->price_to);
		if(isset($coupon_det->price) && $coupon_det->price > 0)
			$coupon_det->price_to = Cutil::formatAmount($coupon_det->price);
		if(isset($coupon_det->offer_amount) && $coupon_det->offer_amount > 0)
			$coupon_det->offer_amount = Cutil::formatAmount($coupon_det->offer_amount);
		if($coupon_det->user_id!=$user_id)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('common.not_authorize'));
		if(isset($coupon_det->price_restriction) && $coupon_det->price_restriction != 'between' && $coupon_det->price_restriction != 'none')
			$coupon_det->price = Cutil::formatAmount($coupon_det->price_from);

		//$coupon_det->from_date = date_format(date_create_from_format('Y-m-d', $coupon_det->from_date), 'd/m/Y');
		//$coupon_det->to_date = date_format(date_create_from_format('Y-m-d', $coupon_det->to_date), 'd/m/Y');

		$is_edit = 1;
		$get_common_meta_values = Cutil::getCommonMetaValues('edit-coupons');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addCoupon',compact('user_id','coupon_det', 'is_edit'));
	}

	public function postUpdate($coupon_id = 0)
	{
		if(is_null($coupon_id) || $coupon_id <=0)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.select_valid_coupon'));

		$user_id = BasicCUtil::getLoggedUserId();
		$couponService = new CouponService();
		$coupon_det = $couponService->getCouponDetails($coupon_id);

		if(!$coupon_det)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.select_valid_coupon'));

		if($coupon_det->user_id!=$user_id)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('common.not_authorize'));

		$inputs = Input::all();
		$rules = array(
			'coupon_code' 		=> 'required|alpha_dash|Unique:coupons,coupon_code,'.$coupon_id,
			'from_date' 		=> 'required',
			'to_date'			=> 'required|after:'.date('Y-m-d', strtotime($inputs['from_date'].' -1 days')),
			'price_restriction'	=> 'required',
			'price'				=> 'required_if_in:'.$inputs['price_restriction'].',less_than,greater_than, equal_to',
			'price_from'		=> 'required_if:price_restriction,between|IsValidPrice|numeric|Min:0',
			'price_to'			=> 'required_if:price_restriction,between|IsValidPrice|numeric|Min:0||GreaterThan:'.$inputs['price_from'].','.$inputs['price_to'],
			'offer_type'		=> 'required',
			'offer_amount'		=> 'required|numeric'
		);

		if(isset($inputs['price_restriction']) && $inputs['price_restriction'] == 'none')
		{
			$inputs['price_from'] = ''; $inputs['price_to'] = '';
		}
		if(isset($inputs['price_restriction']) && $inputs['price_restriction'] != 'between')
		{
			$inputs['price_to'] = '';
		}
		$messages = array('price_to.greater_than' => Lang::get('coupon.price_should_be_greater'));
		$validator = Validator::make($inputs,$rules,$messages);
		if($validator->passes())
		{
			if($inputs['price_restriction']!='none' && $inputs['price_restriction']!='between')
				$inputs['price_from'] = $inputs['price'];

			if(isset($inputs['price']) && $inputs['price'] >0)
				$inputs['price'] = Cutil::formatAmount($inputs['price']);

			if(isset($inputs['price_from']) && $inputs['price_from'] >0)
				$inputs['price_from'] = Cutil::formatAmount($inputs['price_from']);

			if(isset($inputs['price_to']) && $inputs['price_to'] >0)
				$inputs['price_to'] = Cutil::formatAmount($inputs['price_to']);

			$updated = $couponService->updateCoupon($coupon_id, $inputs);
			if($updated)
			{
				return Redirect::action('CouponsController@getIndex')->with('success_message',Lang::get('coupon.coupon_update_success'));
			}
			else
			{
				return Redirect::action('CouponsController@getUpdate', $coupon_id)->with('error_message',Lang::get('common.some_problem_try_later'))->withInput();
			}
		}
		else
		{
			return Redirect::action('CouponsController@getUpdate', $coupon_id)->with('error_message', trans('common.correct_errors'))->withInput()->withErrors($validator);
		}
	}

	public function postAction()
	{
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$coupon_id = Input::get('coupon_id');
		$coupon_action = Input::get('coupon_action');

		if(is_null($coupon_id) || $coupon_id <=0)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.select_valid_coupon'));

		$user_id = BasicCUtil::getLoggedUserId();
		$couponService = new CouponService();
		$coupon_det = $couponService->getCouponDetails($coupon_id);
		if(!$coupon_det)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.select_valid_coupon'));

		if($coupon_det->user_id!=$user_id)
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('common.not_authorize'));

		if($coupon_action!='')
		{
			$success_msg = '';
			switch($coupon_action)
			{
				case 'delete':
					$deleted = $couponService->deleteCoupon($coupon_id);
					if($deleted)
						$success_msg = Lang::get('coupon.deleted_successfully');
					break;

				case 'deactivate':
					$update_arr = array('status' => 'InActive');
					$updated = $couponService->updateCoupon($coupon_id, $update_arr);
					if($updated)
						$success_msg = Lang::get('coupon.deactivated_successfully');
					break;

				case 'activate':
					$update_arr = array('status' => 'Active');
					$updated = $couponService->updateCoupon($coupon_id,$update_arr);
					if($updated)
						$success_msg = Lang::get('coupon.activated_successfully');
					break;
			}

			if($success_msg!='')
			{
				return Redirect::action('CouponsController@getIndex')->with('success_message',$success_msg);
			}
			else
			{
				return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('coupon.some_problem_try_later'));
			}
		}
		else
			return Redirect::action('CouponsController@getIndex')->with('error_message',Lang::get('common.select_valid_action'));
	}
}