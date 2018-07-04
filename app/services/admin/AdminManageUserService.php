<?php
class AdminManageUserService
{
	public function chkValidUserId($user_id)
	{
		$user_count = User::where('id', $user_id)->count();
		if($user_count)
			return true;
		return false;
	}

	public function fetchUserDetailsById($user_id)
	{
		$user_details = User::where('users.id', $user_id)->first();
		return $user_details;
	}

	public function fetchGroupDetails()
	{
		$group_details = Groups::lists('name','id');
		return $group_details;
	}

	public function getSrchVal($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : "";
	}

	public function setMemberFilterArr()
	{
		$this->filter_arr['user_name']= '';
		$this->filter_arr['id']= '';
		$this->filter_arr['user_code']= '';
		$this->filter_arr['name']= '';
		$this->filter_arr['user_email']= '';
		$this->filter_arr['user_group']= '';
		$this->filter_arr['group_name_srch']= '';
		$this->filter_arr['is_shop_owner']= '';
		$this->filter_arr['is_allowed_to_add_product']= '';
		$this->filter_arr['status']= '';
		$this->filter_arr['from_date']= '';
		$this->filter_arr['to_date']= '';
		$this->filter_arr['shop_status']= '';
		$this->filter_arr['shop_name']= '';
		if(CUtil::chkIsAllowedModule('featuredsellers'))
			$this->filter_arr['featured_sellers']= '';
	}

	public function setMemberSrchArr($input)
	{
		$this->srch_arr['user_name']= (isset($input['user_name']) && $input['user_name'] != '') ? $input['user_name'] : "";
		$this->srch_arr['id']= (isset($input['id']) && $input['id'] != '') ? $input['id'] : "";
		$this->srch_arr['user_code']= (isset($input['user_code']) && $input['user_code'] != '') ? $input['user_code'] : "";
		$this->srch_arr['name']= (isset($input['name']) && $input['name'] != '') ? $input['name'] : "";
		$this->srch_arr['user_email']= (isset($input['user_email']) && $input['user_email'] != '') ? $input['user_email'] : "";
		$this->srch_arr['user_group']= (isset($input['user_group']) && $input['user_group'] != '') ? $input['user_group'] : "";
		$this->srch_arr['group_name_srch']= (isset($input['group_name_srch']) && $input['group_name_srch'] != '') ? $input['group_name_srch'] : "";
		$this->srch_arr['is_shop_owner']= (isset($input['is_shop_owner']) && $input['is_shop_owner'] != '') ? $input['is_shop_owner'] : "";
		$this->srch_arr['is_allowed_to_add_product']= (isset($input['is_allowed_to_add_product']) && $input['is_allowed_to_add_product'] != '') ? $input['is_allowed_to_add_product'] : "";
		$this->srch_arr['status']= (isset($input['status']) && $input['status'] != '') ? $input['status'] : "";
		$this->srch_arr['from_date']= (isset($input['from_date']) && $input['from_date'] != '') ? $input['from_date'] : "";
		$this->srch_arr['to_date']= (isset($input['to_date']) && $input['to_date'] != '') ? $input['to_date'] : "";
		$this->srch_arr['shop_status']= (isset($input['shop_status']) && $input['shop_status'] != '') ? $input['shop_status'] : "";
		$this->srch_arr['shop_name']= (isset($input['shop_name']) && $input['shop_name'] != '') ? $input['shop_name'] : "";
		if(CUtil::chkIsAllowedModule('featuredsellers'))
			$this->srch_arr['featured_sellers']= (isset($input['featured_sellers']) && $input['featured_sellers'] != '') ? $input['featured_sellers'] : "";
	}

	public function buildMemberQuery()
	{
		$this->qry = DB::table('users')->leftjoin('users_groups', 'users.id', '=', 'users_groups.user_id')
									   ->join('groups','groups.id', '=', 'users_groups.group_id')
									   ->Select("users.created_at", "users.first_name", "users.last_name", "users.user_name", "users.email",
												"users.activated", "users.is_banned", "users.id", "users.is_requested_for_seller",
												"users.is_allowed_to_add_product", "is_shop_owner", "users_groups.group_id","groups.name as group_name");
		if (CUtil::getAuthUser()->id != 1)
			$this->qry->Where('users.id', '>', 1);

		//form the search query
		if($this->getSrchVal('user_code'))
		{
			$this->qry->whereRaw("(users.id = ? OR users.id = ?)", array(BasicCUtil::getUserIDFromCode($this->getSrchVal('user_code')), $this->getSrchVal('user_code')));
		}

		if($this->getSrchVal('id'))
		{
			$this->qry->whereRaw("users.id = ?", array($this->getSrchVal('id')));
		}

		if($this->getSrchVal('user_name'))
		{
			$name_arr = explode(" ",$this->getSrchVal('user_name'));
			if(count($name_arr) > 0)
			{
				$or_str = '(';
				foreach($name_arr AS $names)
				{
					if($or_str != '(')
						$or_str = $or_str.' OR ';
					$or_str = $or_str.' (users.first_name LIKE \'%'.addslashes($names).'%\' OR users.last_name LIKE \'%'.addslashes($names).'%\' )';
				}
				$or_str = $or_str.' )';
				$this->qry->whereRaw(DB::raw($or_str));
			}
		}

		if($this->getSrchVal('name'))
		{
			$this->qry->whereRaw(DB::raw('(users.user_name LIKE \'%'.addslashes($this->getSrchVal('name')).'%\')'));
		}

		if($this->getSrchVal('user_email'))
		{
			$this->qry->whereRaw(DB::raw('(users.email LIKE \'%'.addslashes($this->getSrchVal('user_email')).'%\')'));
		}

		if($this->getSrchVal('user_group'))
		{
			$this->qry->Where('users_groups.group_id', $this->getSrchVal('user_group'));
		}

		if($this->getSrchVal('group_name_srch'))
		{
			$this->qry->Where('users_groups.group_id', $this->getSrchVal('group_name_srch'));
		}

		if($this->getSrchVal('is_shop_owner'))
		{
			$this->qry->Where('users.is_shop_owner', $this->getSrchVal('is_shop_owner'));
		}

		if($this->getSrchVal('is_allowed_to_add_product'))
		{
			$this->qry->Where('users.is_allowed_to_add_product', $this->getSrchVal('is_allowed_to_add_product'));
		}

		if($this->getSrchVal('from_date')) {
			if($this->getSrchVal('to_date')) {
				$this->qry->whereRaw('DATE_FORMAT(users.created_at, \'%Y-%m-%d\') >= ?', array($this->getSrchVal('from_date')));
			} else {
				$this->qry->whereRaw('DATE_FORMAT(users.created_at, \'%Y-%m-%d\') = ?', array($this->getSrchVal('from_date')));
			}
		}

		if($this->getSrchVal('to_date')) {
			if($this->getSrchVal('from_date')) {
				$this->qry->whereRaw('DATE_FORMAT(users.created_at, \'%Y-%m-%d\') <= ?', array($this->getSrchVal('to_date')));
			} else {
				$this->qry->whereRaw('DATE_FORMAT(users.created_at, \'%Y-%m-%d\') = ?', array($this->getSrchVal('to_date')));
			}
		}

		if($this->getSrchVal('status'))
		{
			if($this->getSrchVal('status') == 'blocked')
			{
				$this->qry->Where('users.is_banned', 1);
			}
			else if($this->getSrchVal('status') == 'active')
			{
				$this->qry->Where('users.activated', 1 )->Where('users.is_banned', 0 );
			}
			else if($this->getSrchVal('status') == 'inactive')
			{
				$this->qry->Where('users.activated', 0);
			}
		}

		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			if($this->getSrchVal('featured_sellers')) {
				$this->qry->whereRaw('users.is_featured_seller = ?', array($this->getSrchVal('featured_sellers')));
			}
		}

		$this->qry->orderBy('users.created_at', 'desc');
		return $this->qry;
	}

	public function buildShopQuery()
	{
		$this->qry = DB::table('shop_details') ->join('users','users.id', '=', 'shop_details.user_id')
									   ->Select("shop_details.*","users.first_name", "users.last_name", "users.user_name", "users.email",
													"users.activated", "users.is_banned", "users.is_requested_for_seller",
													"users.is_allowed_to_add_product", "users.is_shop_owner", "users.shop_status", "users.paypal_id");

		if(CUtil::chkIsAllowedModule('featuredsellers'))
			$this->qry = $this->qry->addSelect("users.is_featured_seller", "users.featured_seller_expires");

		if (CUtil::getAuthUser()->id != 1)
			$this->qry->Where('users.id', '>', 1);

		//form the search query
		if($this->getSrchVal('user_code'))
		{
			$this->qry->whereRaw("users.id = ?", array(BasicCUtil::getUserIDFromCode($this->getSrchVal('user_code'))));
		}

		if($this->getSrchVal('id'))
		{
			$this->qry->whereRaw("users.id = ?", array($this->getSrchVal('id')));
		}

		if($this->getSrchVal('user_name'))
		{
			$name_arr = explode(" ",$this->getSrchVal('user_name'));
			if(count($name_arr) > 0)
			{
				$or_str = '(';
				foreach($name_arr AS $names)
				{
					if($or_str != '(')
						$or_str = $or_str.' OR ';
					$or_str = $or_str.' (users.first_name LIKE \'%'.addslashes($names).'%\' OR users.last_name LIKE \'%'.addslashes($names).'%\' )';
				}
				$or_str = $or_str.' )';
				$this->qry->whereRaw(DB::raw($or_str));
			}
		}

		if($this->getSrchVal('name'))
		{
			$this->qry->whereRaw(DB::raw('(users.user_name LIKE \'%'.addslashes($this->getSrchVal('name')).'%\')'));
		}

		if($this->getSrchVal('shop_name'))
		{
			$this->qry->whereRaw(DB::raw('(shop_details.shop_name LIKE \'%'.addslashes($this->getSrchVal('shop_name')).'%\')'));
		}

		if($this->getSrchVal('shop_status'))
		{
			if($this->getSrchVal('shop_status') == 'active')
				$this->qry->Where('users.shop_status', 1);
			else
				$this->qry->Where('users.shop_status', 0);
			//$this->qry->whereRaw(DB::raw('(users.user_name LIKE \'%'.addslashes($this->getSrchVal('name')).'%\')'));
		}

		if($this->getSrchVal('user_email'))
		{
			//$this->qry->WhereRaw('(users.paypal_id =? OR users.email =?)', array($this->getSrchVal('user_email'), $this->getSrchVal('user_email')));
			$this->qry->WhereRaw('(users.email =?)', array($this->getSrchVal('user_email')));
		}

		if($this->getSrchVal('group_name_srch'))
		{
			$this->qry->Where('users_groups.group_id', $this->getSrchVal('group_name_srch'));
		}

		if($this->getSrchVal('is_shop_owner'))
		{
			$this->qry->Where('users.is_shop_owner', $this->getSrchVal('is_shop_owner'));
		}

		if($this->getSrchVal('is_allowed_to_add_product'))
		{
			$this->qry->Where('users.is_allowed_to_add_product', $this->getSrchVal('is_allowed_to_add_product'));
		}

		if($this->getSrchVal('from_date')) {
			$this->qry->where('shop_details.created_at', '>=', $this->getSrchVal('from_date'));
		}

		if($this->getSrchVal('to_date')) {
			$this->qry->where('shop_details.created_at', '<=', $this->getSrchVal('to_date'));
		}

		if($this->getSrchVal('status'))
		{
			if($this->getSrchVal('status') == 'blocked')
			{
				$this->qry->Where('users.is_banned', 1);
			}
			else if($this->getSrchVal('status') == 'active')
			{
				$this->qry->Where('users.activated', 1);
			}
			else if($this->getSrchVal('status') == 'inactive')
			{
				$this->qry->Where('users.activated', 0);
			}
		}

		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			if($this->getSrchVal('featured_sellers')) {
				$this->qry->whereRaw('users.is_featured_seller = ?', array($this->getSrchVal('featured_sellers')));
			}
		}
		$this->qry->orderBy('users.created_at', 'desc');
		return $this->qry;
	}

	public static function getUserAnalyticsInfo($user_id)
	{
		$analytics_info = array();
		$analytics_info = UserGeoAnalytics::where('user_id', $user_id)->first();
		if(count($analytics_info) > 0)
		{
			$campaign_url = '';
			$ga_content = trim($analytics_info['content']);
			if(isset($analytics_info['content']) && $analytics_info['content'] != '-')
			{
				//Condition to add http if not exist in ga_source
				if(!preg_match('/http/', $analytics_info['source']))
				{
					$campaign_url = 'http://'.$analytics_info['source'].$ga_content;
				}
				else
				{
					$campaign_url = $analytics_info['source'].$ga_content;
				}
			}
			$analytics_info['campaign_url'] = $campaign_url;
			// geo byte info
			$geobyte_info = json_decode($analytics_info['geobyte_info']);
			if(isset($geobyte_info))
			{
				$geobyte_info_list['region_name'] = isset($geobyte_info->region_name) ? $geobyte_info->region_name : "";
				$geobyte_info_list['city'] = isset($geobyte_info->city) ? $geobyte_info->city : "";
				$geobyte_info_list['certainty'] = isset($geobyte_info->certainty) ? $geobyte_info->certainty : "";

				$others_arr = array();
				foreach($geobyte_info as $geoKey => $geoValue)
				{
					if($geoKey!= "region_name" && $geoKey!= "city" && $geoKey!= "certainty")
					{
						$others_arr[] = ucwords(str_replace("_", " ", $geoKey)).": ".$geoValue;
					}
				}
				$geobyte_info_list['others'] = implode(", ", $others_arr);
				$analytics_info['geobyte_info_list'] = $geobyte_info_list;
			}

			// maxmind info
			$maxmind_info = json_decode($analytics_info['maxmind_info']);
			if(isset($maxmind_info))
			{
				$maxmind_info_list['region_name'] = isset($geobyte_info->region_name) ? $geobyte_info->region_name : "";
				$maxmind_info_list['city'] = isset($geobyte_info->city) ? $geobyte_info->city : "";

				$others_arr = array();
				foreach($maxmind_info as $maxmindKey => $maxmindValue)
				{
					if($maxmindKey!= "region_name" && $maxmindKey!= "city")
					{
						$others_arr[] = ucwords(str_replace("_", " ", $maxmindKey)).": ".$maxmindValue;
					}
				}
				$maxmind_info_list['others'] = implode(", ", $others_arr);
				$analytics_info['maxmind_info_list'] = $maxmind_info_list;
			}
			$brwoser_info = json_decode($analytics_info['browser_info']);

			if(isset($brwoser_info))
			{
				$others_arr = array();
				foreach($brwoser_info as $brwdKey => $brwValue)
				{
					$others_arr[] = ucwords(str_replace("_", " ", $brwdKey)).": ".$brwValue;

				}
				$analytics_info['browser_info_list'] = implode(", ", $others_arr);;
			}
		}
		return $analytics_info;
	}

	public function checkIsValidMember($user_id, $user_type='Member')
	{
		$memberCount = User::where('id', $user_id)->count();
		if($memberCount)
			return true;

		return false;
	}

	/*public function updateUserActivationByAdmin($user_id, $action)
	{
		if(strtolower($action) == 'activate')
		{
			$user = User::where("id", $user_id)->where('activated', 0)->first();
			if($user)
			{
				$activation_code = $user->getActivationCode();
				$userService = new UserAccountService();
				$userService->activateUser($user, $activation_code, $auto_login = false);
			}
			$success_msg = trans('admin/manageMembers.memberlist_activated_suc_msg');
		}
		else
		{
			$user = User::where("id", $user_id)->first();
			$data_arr['activated'] = 0;
			User::where('id', $user_id)->update($data_arr);
			$success_msg = trans('admin/manageMembers.memberlist_deactivated_suc_msg');
		}
		// Add user log entry
		$data_arr['user_id'] 	= $user_id;
		$data_arr['added_by'] 	= getAuthUser()->id;
		$data_arr['date_added'] = date('Y-m-d H:i:s');
		$data_arr['log_message'] = $success_msg." Added by: ".getAuthUser()->first_name." On.".date('Y-m-d H:i:s');
		$userlog = new UserLog();
		$userlog->addNew($data_arr);
		return $success_msg;
	}*/

	/**
	 * AdminUserController::updateUserActivationByAdmin()
	 *
	 * @return success_msg
	 */
	public function updateUserActivationByAdmin($user_id, $action)
	{
		switch($action)
		{
			case 'activate':
				$user = User::where("id", $user_id)->where('activated', 0)->first();
				if($user)
				{
					$activation_code = $user->getActivationCode();
					$userService = new UserAccountService();
					$userService->activateUser($user, $activation_code, $auto_login = false);
				}
				$success_msg = Lang::get('admin/manageMembers.memberlist_activated_suc_msg');
				break;

			case 'deactivate':
				$user = User::where("id", $user_id)->first();
				$data_arr['activated'] = 0;
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$success_msg = Lang::get('admin/manageMembers.memberlist_deactivated_suc_msg');
				break;

			case 'block':
				$user = User::where("id", $user_id)->first();
				$data_arr['is_banned'] = 1;
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$success_msg = Lang::get('admin/manageMembers.memberlist_blocked_suc_msg');
				break;

			case 'unblock':
				$user = User::where("id", $user_id)->first();
				$data_arr['is_banned'] = 0;
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$success_msg = Lang::get('admin/manageMembers.memberlist_unblocked_suc_msg');
				break;

			case 'deactivateshop':
				//$user = User::where("id", $user_id)->first();
				$data_arr['shop_status'] = 0;
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$success_msg = Lang::get('admin/manageMembers.shop_deactivated_suc_msg');
				break;

			case 'activateshop':
				//$user = User::where("id", $user_id)->first();
				$data_arr['shop_status'] = 1;
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$success_msg = Lang::get('admin/manageMembers.shop_activated_suc_msg');
				break;

			case 'allow_to_add_product':
				$user = User::where("id", $user_id)->first();
				$data_arr['is_allowed_to_add_product'] = 'Yes';
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$success_msg = Lang::get('admin/manageMembers.memberlist_blocked_suc_msg');
				break;

			default;
				$success_msg = Lang::get('admin/manageMembers.memberlist_select_valid_actiion');
				break;
		}
		return $success_msg;
	}

	public function fetchUserDetails($ident, $type)
	{
		$user_details = array();
		$user_details['err_msg'] = '';
		$user_details['own_profile'] = 'No';

		$search_cond = "users.id = '".addslashes($ident)."'";
		if($type == 'code')
			$search_cond =" users.user_code = '".addslashes($ident)."'";

		$udetails = User::whereRaw($search_cond)
					->first(array('users.first_name', 'users.user_code', 'users.id', 'users.last_name', 'users.email', 'users.activated',
									'users.activated_at','users.last_login', 'users.about_me', 'users.user_status', 'users.user_access', 'users.phone'));
		if(count($udetails) > 0)
		{
			$user_details['user_code'] 		= $udetails['user_code'];
			$user_details['email'] 			= $udetails['email'];
			$user_details['user_id'] 		= $user_id = $udetails['id'];
			$user_details['first_name'] 	= $udetails['first_name'];
			$user_details['last_name'] 		= $udetails['last_name'];
			$user_display_name 				= $udetails['first_name'].' '.substr($udetails['last_name'], 0,1);
			$user_details['display_name'] 	= ucwords($user_display_name);
			$user_details['activated_at'] 	= $udetails['activated_at'];
			$user_details['last_login'] 	= $udetails['last_login'];
			$user_details['activated'] 		= $udetails['activated'];
			$user_details['phone'] 			= $udetails['phone'];
			$user_details['about_me'] 		= $udetails['about_me'];

			if($udetails['activated'] == 0)
				$user_details['user_status']= "ToActivate";
			elseif($udetails['user_status'] == "Deleted")
				$user_details['user_status']= "Locked";
			else
				$user_details['user_status']= $udetails['user_status'];
			$user_details['user_access']	= $udetails['user_access'];
			$admin_profile_url = CUtil::getUserDetails($user_id, 'admin_profile_url', $user_details);
			$user_details['profile_url'] = $admin_profile_url;
			$user_groups = $this->fetchUserGroupNames($user_details['user_id']);
			$user_details['user_groups'] = $user_groups;
		}
		else
		{
			$user_details['err_msg'] = 'No such user found';
			$user_details['profile_url'] = '';
		}
		return $user_details;
	}


	public function fetchUserGroupNames($user_id)
	{
		return  UserGroup::select("user_group.id", "user_group.group_name", 'user_group.has_admin_access')
									->join('user_group_members', 'user_group_members.group_id', '=', 'user_group.id')
									->where('user_group_members.user_id', $user_id)->get();
	}

	public function fetchGroups()
	{
		$groups = Sentry::findAllGroups();
		$group_array = array();
		if(count($groups) > 0) {
			foreach($groups as $key => $values) {
				$group_array[$values->id] = $values->name;
			}
		}
		return $group_array;
	}

	public function fetchUserDetailsByUsername($user_name)
	{
		$user_details = User::where('users.user_name', $user_name)->where('is_banned','0')->first();
		return $user_details;
	}

	public function fetchGroupMembersLists($group_id = null)
	{
		if(is_null($group_id))
			$group_id = Config::get('generalConfig.admin_group_id');
		$members = UsersGroups::where('group_id', '=', $group_id)->lists('user_id');
		return $members;
	}

	//Featured sellers functions start
	public function getFeaturedSellersSettings($id = 0)
 	{
 		if($id) {
 			return UsersFeatured::where('featured_id', $id)->first();
 		}
 	}

 	public function buildFeaturedSellersQuery()
	{
		return UsersFeatured::Select("featured_id", "user_id", "date_added")->orderBy('featured_id','DESC');
	}

	public function updateFeaturedSellers($input)
 	{
 		$user_name = $input['user_name'];
 		$user_id = User::whereRaw('user_name = ? ', array($user_name))->pluck('id');
 		if($user_id > 0) {
			if($input['settings_id']) {
				UsersFeatured::where('featured_id', $input['settings_id'])->update(array('user_id' => $user_id));
			}
			else {
				$id = UsersFeatured::insertGetId(array('user_id' => $user_id, 'date_added' => DB::Raw('Now()')));
			}
		}
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
 	}

 	public function deleteFeaturedSellersRec($id)
	{
		if($id) {
			UsersFeatured::where('featured_id', $id)->delete();
			$array_multi_key = array('featured_seller_banner_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
		}
	}
	//Featured sellers functions end

	//Top picks functions start
	public function getTopPicksSettings($id = 0)
 	{
 		if($id) {
 			return UsersTopPicks::where('top_pick_id', $id)->first();
 		}
 	}

 	public function buildTopPicksUsersQuery()
	{
		return UsersTopPicks::Select("top_pick_id", "user_id", "date_added")->orderBy('top_pick_id','DESC');
	}

	public function updateTopPicksUsers($input)
 	{
 		$user_name = $input['user_name'];
 		$user_id = User::whereRaw('user_name = ? ', array($user_name))->pluck('id');
 		if($user_id > 0) {
			if($input['settings_id']) {
				UsersTopPicks::where('top_pick_id', $input['settings_id'])->update(array('user_id' => $user_id));
			}
			else {
				$id = UsersTopPicks::insertGetId(array('user_id' => $user_id, 'date_added' => DB::Raw('Now()')));
			}
			$cache_key = 'users_top_picks_key';
			$forget_key = HomeCUtil::cacheForgot($cache_key);
			if($forget_key)
			{
				$users_top_picks = UsersTopPicks::Select("users_top_picks.top_pick_id", "users_top_picks.user_id", "users_top_picks.date_added")
											->join('users', 'users_top_picks.user_id', '=', 'users.id')
											->where('users.is_banned', '=', 0)
											->where('users.shop_status', '=', 1)
											->get()->toArray();
				HomeCUtil::cachePut($cache_key, $users_top_picks);
			}
		}
 	}

 	public function deleteTopPicksUsersRec($id)
	{
		if($id) {
			UsersTopPicks::where('top_pick_id', $id)->delete();
			$cache_key = 'users_top_picks_key';
			$forget_key = HomeCUtil::cacheForgot($cache_key);
			if($forget_key)
			{
				$users_top_picks = UsersTopPicks::Select("users_top_picks.top_pick_id", "users_top_picks.user_id", "users_top_picks.date_added")
											->join('users', 'users_top_picks.user_id', '=', 'users.id')
											->where('users.is_banned', '=', 0)
											->where('users.shop_status', '=', 1)
											->get()->toArray();
				HomeCUtil::cachePut($cache_key, $users_top_picks);
			}
		}
	}
	//Top picks functions end

	//Favorites products functions start
	public function getFavoriteProductsSettings($id = 0)
 	{
 		if($id) {
 			return UsersFavoritesProducts::where('favorite_id', $id)->first();
 		}
 	}

 	public function buildFavoriteProductsQuery()
	{
		return UsersFavoritesProducts::Select("favorite_id", "product_id", "date_added")->orderBy('favorite_id','DESC');
	}

	public function updateFavoriteProducts($input)
 	{
 		$product_code = $input['product_code'];
 		$product_id = Product::whereRaw('product_code = ? ', array($product_code))->pluck('id');
 		if($product_id > 0) {
			if($input['settings_id']) {
				UsersFavoritesProducts::where('favorite_id', $input['settings_id'])->update(array('product_id' => $product_id));
			}
			else {
				$id = UsersFavoritesProducts::insertGetId(array('product_id' => $product_id, 'date_added' => DB::Raw('Now()')));
			}
			$cache_key = 'users_favorites_products_key';
			$forget_key = HomeCUtil::cacheForgot($cache_key);
			if($forget_key)
			{
				$users_favorites_products = UsersFavoritesProducts::Select("favorite_id", "product_id", "date_added")->get()->toArray();
				HomeCUtil::cachePut($cache_key, $users_favorites_products);
			}
		}
 	}

 	public function deleteFavoriteProductsRec($id)
	{
		if($id) {
			UsersFavoritesProducts::where('favorite_id', $id)->delete();
			$cache_key = 'users_favorites_products_key';
			$forget_key = HomeCUtil::cacheForgot($cache_key);
			if($forget_key)
			{
				$users_favorites_products = UsersFavoritesProducts::Select("favorite_id", "product_id", "date_added")->get()->toArray();
				HomeCUtil::cachePut($cache_key, $users_favorites_products);
			}
		}
	}

	public function sendShopStatusUpdatedMailToUser($action='', $user_id = null)
	{
		if(is_null($user_id) || $user_id<=0)
			return;

		$user_details = CUtil::getUserDetails($user_id);
		$shop_obj = Products::initializeShops();
		$shop_obj->setIncludeBlockedUserShop(true);
    	$shop_details = $shop_obj->getShopDetails($user_id);

		$data['shop_details'] = $shop_details;
		$data['user_details'] = $user_details;
		$data['action'] = $action;
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$curr_user_details = CUtil::getUserDetails($logged_user_id);
		$data['curr_user_details'] = $curr_user_details;

		$from_name = '';
		$from_email = '';
		if(!$from_email)
		{
			$from_email = Config::get("mail.from_email");
		}
		if(!isset($from_name) ||  (isset($from_name)&& $from_name==''))
		{
			$from_name = Config::get("mail.from_name");
		}
		$d_arr['from_name'] = $from_name;
		$d_arr['from_email'] = $from_email;

		//mail to shop owner
		$view = 'emails.shopStatusChangedMailForSeller';
		$d_arr['to_email'] = $user_details['email'];
		if($action == 'deactivateshop')
			$d_arr['subject'] = Config::get('generalConfig.site_name')." - Your shop have been Deactivated";
		else
			$d_arr['subject'] = Config::get('generalConfig.site_name')." - Your shop have been Activated";
		$d_arr['content'] = $view;
		$d_arr['data'] = serialize($data);
		$d_arr['date_added']= new DateTime;
		$data1['to_email'] = $d_arr['to_email'];
		$data1['from_name'] = $d_arr['from_name'];
		$data1['from_email'] = $d_arr['from_email'];
		$data1['subject'] = $d_arr['subject'];
		try {
			Mail::send($view, $data,  function($message) use ($data1)
			{
				$to_arr = explode(',',  $data1['to_email']);
				foreach($to_arr as $to)
				{
					if($to != '')
						$message->to($to);
				}
				$message->from($data1['from_email'], $data1['from_name']);
				$message->subject($data1['subject']);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
		//Mail to admin
		$view = 'emails.shopStatusChangedMailForAdmin';
		if($action == 'deactivateshop')
			$d_arr['subject'] = Config::get('generalConfig.site_name')." - A shop have been Deactivated by ".$curr_user_details['display_name'];
		else
			$d_arr['subject'] = Config::get('generalConfig.site_name')." - A shop have been Activated by ".$curr_user_details['display_name'];

		$data1['to_email'] = Config::get("generalConfig.invoice_email");
		$data1['subject'] = $d_arr['subject'];
		try {
			Mail::send($view, $data,  function($message) use ($data1)
			{
				$to_arr = explode(',',  $data1['to_email']);
				foreach($to_arr as $to)
				{
					if($to != '')
						$message->to($to);
				}
				$message->from($data1['from_email'], $data1['from_name']);
				$message->subject($data1['subject']);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function sendShopDetailsUpdatedMailToUser($section='', $user_id = null)
	{
		if(is_null($user_id) || $user_id<=0)
			return

		$from_name = '';
		$from_email = '';
		if(!$from_email)
		{
			$from_email = Config::get("mail.from_email");
		}
		if(!isset($from_name) ||  (isset($from_name)&& $from_name==''))
		{
			$from_name = Config::get("mail.from_name");
		}
		$view = 'emails.shopDetailsChangedMailForSeller';
		//add an entry to the table
		$d_arr['from_name']		= $from_name;
		$d_arr['from_email'] 	= $from_email;

		$user_details = CUtil::getUserDetails($user_id);

		$data['user_details'] 	= $user_details;
		$data['section'] 		= $section;
		$d_arr['to_email'] 		= $user_details['email'];
		$d_arr['subject'] 		= Config::get('generalConfig.site_name')." - Your shop details have been changed";
		$d_arr['content'] 		= $view;
		$d_arr['data'] 			= serialize($data);
		$d_arr['date_added']	= new DateTime;

		/*$mailer = new MailSystemAlert;
		$arr = $mailer->filterTableFields($d_arr);
		$id = $mailer->insertGetId($arr);*/

		$data1['to_email'] 		= $d_arr['to_email'];
		$data1['from_name'] 	= $d_arr['from_name'];
		$data1['from_email'] 	= $d_arr['from_email'];
		$data1['subject'] 		= $d_arr['subject'];
		try {
			Mail::send($view, $data,  function($message) use ($data1)
			{
				$to_arr = explode(',',  $data1['to_email']);
				foreach($to_arr as $to)
				{
					if($to != '')
						$message->to($to);
				}
				$message->from($data1['from_email'], $data1['from_name']);
				$message->subject($data1['subject']);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}
	//Favorites products functions end
}