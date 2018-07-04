<?php

class AdminManageShopService
{
	public function setShopSrchArr($input)
	{
		$this->srch_arr['shop_name'] =(isset($input['shop_name']) && $input['shop_name'] != '') ? $input['shop_name'] : "";
		/*$this->srch_arr['user_code'] =(isset($input['user_code']) && $input['user_code'] != '') ? $input['user_code'] : "";
		$this->srch_arr['user_name']= (isset($input['user_name']) && $input['user_name'] != '') ? $input['user_name'] : "";
		$this->srch_arr['user_email']= (isset($input['user_email']) && $input['user_email'] != '') ? $input['user_email'] : "";*/
		$this->srch_arr['shop_featured']= (isset($input['shop_featured']) && $input['shop_featured'] != '') ? $input['shop_featured'] : "";
	}

	public function getSrchVal($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : '';
	}

	public function setShopFilter($shop_obj)
	{
		if($this->getSrchVal('shop_name'))
		{
			$shop_obj->setFilterShopName($this->getSrchVal('shop_name'));
		}

		if($this->getSrchVal('shop_featured'))
		{
			$shop_obj->setFilterIsFeaturedShop($this->getSrchVal('shop_featured'));
		}
	}
	public function getCountryList()
	{
		$country_list_arr = array();
		$country_list_arr = Products::getCountryList();
		return $country_list_arr;
	}

	public function fetchUserDetails($ident, $type)
	{
		$search_cond ='users.user_id = '.$ident;
		if($type == 'code')
			$search_cond ='users.user_code = '.$ident;

		$user_details = array();
		$user_details['err_msg'] = '';
		$user_details['own_profile'] = 'No';
		$udetails = User::whereRaw($search_cond)
								->first(array('users.first_name', 'users.id', 'users.last_name', 'users.email', 'users.activated',
											'users.activated_at'));

		if(count($udetails) > 0)
		{
			$user_details['user_code'] 		= BasicCUtil::setUserCode($udetails['id']);
			$user_details['email'] 			= $udetails['email'];
			$user_details['user_id'] 		= $user_id = $udetails['id'];
			$user_details['first_name'] 	= $udetails['first_name'];
			$user_details['last_name'] 		= $udetails['last_name'];
			$user_display_name 				= $udetails['first_name'].' '.substr($udetails['last_name'], 0,1);
			$user_details['display_name'] 	= ucwords($user_display_name);
			$user_details['activated_at'] 	= $udetails['activated_at'];
			$user_details['activated'] 	= $udetails['activated'];
		}
		else
		{
			$user_details['err_msg'] = 'No such user found';
		}
		return $user_details;
	}


	public function checkIsValidMember($user_id, $user_type='Member')
	{
		$memberCount = User::where('user_id', $user_id)->count();
		if($memberCount)
			return true;
		return false;
	}

	public function updateShopFeaturedByAdmin($shop_id, $action, $shop_obj)
	{
		$shop = $shop_obj->getShopDetailsWithFilter();
		if($shop)
		{
			if(strtolower($action) == 'setfeatured')
			{
				$shop_obj->setIsFeaturedShop('Yes');
				$shop_obj->setShopFeaturedStatus($shop_id);
				$success_msg = trans('admin/manageShops.shoplist_set_featured_suc_msg');
			}
			else//if(strtolower($action) == 'removefeatured')
			{
				$shop_obj->setIsFeaturedShop('No');
				$shop_obj->setShopFeaturedStatus($shop_id);
				$success_msg = trans('admin/manageShops.shoplist_remove_featured_suc_msg');
			}
		}
		return $success_msg;
	}


}