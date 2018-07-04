<?php
class NewsletterService
{
	public function addNewsletter($inputs = array())
	{
		unset($inputs['_token']);
		$data = array();
		$data['subject'] = $inputs['subject'];
		$data['message'] = $inputs['message'];
		$data['search_filter'] = serialize($inputs);
		$newsletter = new Newsletter();
		$newsleeter_id = $newsletter->addNew($data);
		return $newsleeter_id;
	}
	public function setUserDetailsFromFilter($newsletter_id = null, $inputs = array())
	{
		if(!is_array($inputs) || empty($inputs) || is_null($newsletter_id))
			return false;
		unset($inputs['_token']);

		$all_conditions = array();
		$parameters = array();
		$users = User::select('id','first_name','last_name', 'user_name', 'email')->orderby('id','asc');
		$users->addSelect(DB::Raw($newsletter_id));
		if(isset($inputs['username']) && $inputs['username']!='')
		{
			$all_conditions[] = 'user_name like ? ';
			$parameters[] = '%'.$inputs['username'].'%';
			$users->where('user_name', 'like', '%'.$inputs['username'].'%');
		}
		if(isset($inputs['first_name']) && $inputs['first_name']!='')
		{
			$all_conditions[] = 'first_name like ? ';
			$parameters[] = '%'.$inputs['first_name'].'%';
			$users->where('first_name', 'like', '%'.$inputs['first_name'].'%');
		}
		if(isset($inputs['last_name']) && $inputs['last_name']!='')
		{
			$all_conditions[] = 'last_name like ? ';
			$parameters[] = '%'.$inputs['last_name'].'%';
			$users->where('last_name', 'like', '%'.$inputs['last_name'].'%');
		}
		if(isset($inputs['email']) && $inputs['email']!='')
		{
			$all_conditions[] = 'email like ? ';
			$parameters[] = '%'.$inputs['email'].'%';
			$users->where('email', 'like', '%'.$inputs['email'].'%');
		}
		if(isset($inputs['doj_from_date']) && $inputs['doj_from_date']!='')
		{
			$all_conditions[] = 'created_at >= ? ';
			$parameters[] = $inputs['doj_from_date'];
			$users->where('created_at', '>=', '%'.$inputs['doj_from_date'].'%');
		}
		if(isset($inputs['doj_to_date']) && $inputs['doj_to_date']!='')
		{
			$all_conditions[] = 'created_at <= ? ';
			$parameters[] = $inputs['doj_to_date'];
			$users->where('created_at', '<=', '%'.$inputs['doj_to_date'].'%');
		}
		if(isset($inputs['last_login']) && $inputs['last_login']!='')
		{
			$all_conditions[] = 'last_login = ? ';
			$parameters[] = $inputs['last_login'];
			$users->where('last_login', '<=', '%'.$inputs['last_login'].'%');
		}
		if(isset($inputs['status']) && !empty($inputs['status']))
		{
			$condtions = array();
			$statuses = $inputs['status'];
			$table_name = (new User)->getTable();
			foreach($statuses as $status)
			{

				switch($status)
				{
					case 'Active':
							$condtions[] = $table_name.'.activated = 1';
						break;
					case 'InActive':
							$condtions[] = $table_name.'.activated = 0';
						break;
					case 'Blocked':
							$condtions[] = $table_name.'.is_banned = 1';
						break;
				}
			}
			if(!empty($condtions))
			{
				$ccondition = implode(' OR ',$condtions);
				$ccondition = '( '.$ccondition.' )';
				$users->whereRaw($ccondition);
				$all_conditions[] = $ccondition;
			}
		}
//		if(!empty($all_conditions))
//		$final_conditions = implode(' AND ',$all_conditions);
		$user_sql = $users->toSql();
		if(!empty($parameters) && $user_sql!='')
		{
			$newsletter_users_tbl = (new NewsletterUsers)->getTable();
			DB::statement("Insert into ".$newsletter_users_tbl." (user_id, first_name, last_name, user_name, email, newsletter_id) ".$user_sql, $parameters);
		}
		return true;
	}
	public function getAllNewsLetter($inputs = array(), $return_type = 'paginate', $limit = 10){

		$newsletters = Newsletter::orderby('id','desc');
		if(isset($inputs['subject']) && $inputs['subject']!='')
			$newsletters->where('subject', 'like', '%'.$inputs['subject'].'%');
		if(isset($inputs['date_sent']) && $inputs['date_sent']!='')
			$newsletters->where('updated_at', '=', $inputs['date_sent']);
		if(isset($inputs['status']) && $inputs['status']!='')
			$newsletters->where('status', '=', $inputs['status']);

		$page = (input::has('page') && input::has('page') > 0)?input::get('page'):1;
		Paginator::setCurrentPage($page);
		if($return_type =='paginate')
			$newsletters = $newsletters->paginate($limit);
		else
			$newsletters = $newsletters->get($limit);
		return $newsletters;

	}
	public function bulkUpdateNewsletter($newsletter_ids, $data){

		$update = false;
		if(!empty($newsletter_ids))
		{
			try
			{
				$qry = Newsletter::whereIn('id', $newsletter_ids)->update($data);
				$update = true;
			}
			catch(exception $e)
			{
				$update = false;
			}
		}
		return $update;
	}
	public function getNewsletterDetails($newsletter_id = null){
		if(is_null($newsletter_id) || $newsletter_id<=0)
			return false;

		$newsletter_details = Newsletter::find($newsletter_id);
		return $newsletter_details;
	}
}