<?php
//@added by senthil
class GroupManageController extends BaseController
{

	/**
	 * Constructor
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	public function __construct()
		{
 			parent::__construct();
		}

	/**
	 * To Fetch Group Details...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	public function getIndex()
		{
			$group_array = $userCountArray = array();
			$groups = Sentry::findAllGroups();
			if(count($groups) > 0) {
				foreach($groups as $key => $values) {
					$group_array[$key]['id'] = $values->id;
					$group_array[$key]['name'] = $values->name;
					$permission_array =  $values->permissions;
					if(isset($permission_array['system']) && $permission_array['system'] == '1') {
						$group_array[$key]['super_admin'] = '1';
					} else {
						$group_array[$key]['super_admin'] = '0';
					}
					$group_array[$key]['permissions'] = $values->permissions;
				}
			}

			$userCount = UsersGroups::Select('group_id', DB::raw('Count(group_id) as user_count'))
									->GroupBy('group_id')->get();
			//echo "<pre>";print_r($userCount);"</pre>";die();
			if(sizeof($userCount) > 0) {
				foreach($userCount as $key => $values) {
					$userCountArray[$values->group_id] = $values->user_count;
				}
			}
			//Set meta details
			$this->header->setMetaTitle(trans('meta.groups_title'));
			return View::make('admin.listGroup', compact('group_array', 'userCountArray'));
		}

	/**
	 * To Add Group members...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	public function getAdd()
		{
			$group_details = array('group_name'=>'');
			//Set meta details
			$this->header->setMetaTitle(trans('meta.create_group_title'));
			return View::make('admin.addGroup', compact('group_details'));
		}

	/**
	 * To Add Group members...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	 public function postAdd()
	 	{
	 		if(!BasicCUtil::checkIsDemoSite()){
				$rules = array('group_name' => 'Required');
				$messages = array();
				$input = Input::all();
				$validator = Validator::make($input, $rules, $messages);
				if ($validator->passes())
				{
				 	try
					{
						$group = Sentry::createGroup(array(
							'name'        => Input::get('group_name'),
						));
						Session::flash('success', trans('admin/manageGroups.group_added_successfully'));
						return Redirect::to('admin/group');
					}
					catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
					{
						$group_details = Input::all();
						Session::flash('error', trans('admin/manageGroups.group_name_exists'));
						//return View::make('admin.addGroup', compact('group_details'));
						return Redirect::to('admin/group/add')->withInput();
					}
				}
				else
				{
					return Redirect::to('admin/group/add')->withInput()->withErrors($validator)->with('error', 'Enter group name');
				}
			}else{
				$errMsg = Lang::get('common.demo_site_featured_not_allowed');
				return Redirect::to('admin/group/add')->with('error',$errMsg);
			}
		}

	/**
	 * Group Modify...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	 public function getEdit()
	 	{
	 		$super_admin = '';
			try
			{
				$group = Sentry::findGroupById(Input::get('id'));
				$group_array = $group->toArray();
				if(isset($group_array) && count($group_array['name']) > 0) {
					$group_details['id'] = $group_array['id'];
					$group_details['group_name'] = $group_array['name'];
					$group_details['permissions']= $group_array['permissions'];
					if(empty($group_details['permissions']))
						$group_details['super_admin'] = '0';
					else
						$group_details['permissions']= $group_array['permissions']['system'];
					if(isset($group_details['permissions']) && $group_details['permissions'] == '1')
						$group_details['super_admin'] = '1';
					else
						$group_details['super_admin'] = '0';
				}
				if ($group_details['super_admin'] == '0') {
					//Set meta details
					$this->header->setMetaTitle(trans('meta.update_group_title'));
					return View::make('admin.addGroup', compact('group_details'));
				}else{
					$group_details['super_admin'] = '1';
					Session::flash('error', 'Invalid Group Id');
					return Redirect::to('admin/group');
				}
			}
			catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
			{
				Session::flash('error', trans('admin/manageGroups.group_was_not_found'));
				return Redirect::to('admin/group');
			}
		}

	/**
	 * Group Modify...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	 public function postEdit()
	 	{
	 		if(!BasicCUtil::checkIsDemoSite()){
				$rules = array('group_name' => 'Required');
				$messages = array();
				$input = Input::all();
				$validator = Validator::make($input, $rules, $messages);
				if ($validator->passes())
				{
					try
					{
						$group = Sentry::findGroupById(Input::get('id'));
						$group->name = Input::get('group_name');
						if ($group->save())
						{
							Session::flash('success', trans('admin/manageGroups.group_updated_successfully'));
							return Redirect::to('admin/group');
						}
					}
					catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
					{
						$group_details = Input::all();
						Session::flash('error', trans('admin/manageGroups.group_name_exists'));
						return View::make('admin.addGroup', compact('group_details'));
					}
					catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
					{
						Session::flash('error', trans('admin/manageGroups.group_was_not_found'));
						return Redirect::to('admin/group');
					}
				}else{
					return Redirect::to('admin/group/edit?id='.$input['id'])->withErrors($validator)->with('error', 'Enter group name');
				}
			}else{
				$errMsg = Lang::get('common.demo_site_featured_not_allowed');
				return Redirect::back()->with('error',$errMsg);
			}
		}


	/**
	 * To Delete Single groups...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	 public function getDelete()
	  {
	  	if(!BasicCUtil::checkIsDemoSite()){
			try
				{
					$super_admin = '';
					// Find the group using the group id
					$group = Sentry::findGroupById(Input::get('id'));
					$group_array = $group->toArray();
					$group_details['permissions']= $group_array['permissions'];
						if(empty($group_details['permissions']))
							$group_details['super_admin'] = '0';
						else
							$group_details['permissions']= $group_array['permissions']['system'];
						if(isset($group_details['permissions']) && $group_details['permissions'] == '1')
							$group_details['super_admin'] = '1';
						else
							$group_details['super_admin'] = '0';

					$user_count = UsersGroups::where('group_id', Input::get('id'))->count();
					if($user_count > 0)
					{
						Session::flash('error', 'Group could not be deleted since it have members in it.');
						return Redirect::to('admin/group');
					}

					if ($group_details['super_admin'] == '0') {
						// Delete the group
						$group->delete();
						Session::flash('success', trans('admin/manageGroups.group_deleted_successfully'));
						return Redirect::to('admin/group');
					}else{
						$group_details['super_admin'] = '1';
						Session::flash('error', 'Invalid Group Id');
						return Redirect::to('admin/group');
					}
				}
			catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
				{
					Session::flash('error', trans('admin/manageGroups.group_was_not_found'));
					return Redirect::to('admin/group');
				}
			}else{
				$errMsg = Lang::get('common.demo_site_featured_not_allowed');
				return Redirect::back()->with('error',$errMsg);
			}
	  }

	/**
	 * To Delete Multiple groups...
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	public function postDelete()
		{
			if(!BasicCUtil::checkIsDemoSite()){
				try
				{
					// Find the group using the group id
					$group_arrays = Input::get('row_id');
					if(count($group_arrays) > 0) {
						for($i = 0; $i < count($group_arrays); $i++) {
							$group = Sentry::findGroupById($group_arrays[$i]);
							$group->delete();
						}
					}

					Session::flash('success', trans('admin/manageGroups.group_deleted_successfully'));
					return Redirect::to('admin/group');
				}
				catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
				{
					Session::flash('error', trans('admin/manageGroups.group_was_not_found'));
					return Redirect::to('admin/group');
				}
			}else{
				$errMsg = Lang::get('common.demo_site_featured_not_allowed');
				return Redirect::back()->with('error',$errMsg);
			}
		}

	/**
	 * getListGroupMembers
	 *
	 * @author		senthil
	 * @access 		protected
	 */
	public function getListGroupMembers()
	{
		$filterSet = 0;
		try
		{
			$group = Sentry::findGroupById(Input::get('groupId'));
			$group_array = $group->toArray();
			if(isset($group_array) && count($group_array['name']) > 0) {
				$group_details['id'] = $group_array['id'];
				$group_details['group_name'] = $group_array['name'];
			}
			//Set meta details
			$this->header->setMetaTitle(trans('meta.list_group_members_title'));

			$members = User::select('users.id', 'users.email', 'users.first_name', 'users.last_name')
								->leftjoin('users_groups', 'users.id', '=', 'users_groups.user_id')
							->whereRaw('users_groups.group_id = ?', array(Input::get('groupId')));
			if (Input::has('name')) {
				$members = $members->whereRaw('(users.first_name LIKE \'%'.addslashes(Input::get('name')).'%\' OR users.last_name LIKE \'%'.addslashes(Input::get('name')).'%\')');
				$filterSet = 1;
			}
			if(Input::has('email')) {
				$members = $members->whereRaw('users.email = ?', array(Input::get('email')));
				$filterSet = 1;
			}
			if(Input::has('activated'))
				$members = $members->whereRaw('users.activated = ?', array(Input::get('activated')));
			$sortBy = (Input::has('sortby')) ? Input::get('sortby') : 'id';
			$orderBy = (Input::has('order')) ? Input::get('order') : 'desc';
			$members  =	$members->orderBy('users.'.$sortBy,$orderBy)->paginate(15);
			$members_arr = array();
			if(count($members) > 0) {
				$inc=0;
				foreach($members as $member) {
					$members_arr[$inc]['user_id'] = $member->id;
					$members_arr[$inc]['email'] = $member->email;
					$members_arr[$inc]['first_name'] = $member->first_name;
					$members_arr[$inc]['last_name'] = $member->last_name;
					$inc++;
				}
			}
			$append_arr = array('filter' => $filterSet, 'name'=>Input::get('name'), 'email'=>Input::get('email'), 'groupId' => Input::get('groupId'));
			return View::make('admin.listGroupMembers', compact('members', 'members_arr', 'append_arr', 'group_details'));

		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			Session::flash('error', trans('admin/manageGroups.group_was_not_found'));
			return Redirect::to('admin/group');
		}
	}
}

