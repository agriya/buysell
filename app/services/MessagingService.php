<?php

// @added manikandan_1333at10
class MessagingService
{
	/**
	 * MessagingService::sendMessageAddedNotification()
	 * Mail notification to admin & user regarding the message posted
	 * @param mixed $message_id
	 * @return
	 */
	public function sendMessageAddedNotification($message_id)
	{

		if($message_id)
		{
			//To Admin
			$message_details = Message::where('id', $message_id)->where('is_deleted', 0)->first();
			$data_arr['from_user_details'] = CUtil::getUserDetails($message_details->from_user_id);
			$data_arr['to_user_details'] = CUtil::getUserDetails($message_details->to_user_id);
			$data_arr['subject'] = trans('common.new_message_posted_mail_for_admin');
			$data_arr['message_text'] = $message_details->message_text;
			$data_arr['message_subject'] = $message_details->subject;
			$data_arr['date_posted'] = CUtil::FMTDate($message_details->date_added, "Y-m-d H:i:s", "");
			$data_arr['message_view_link'] = URL::action('MessagingController@getViewMessage',$message_id);

			try {
				Mail::send('emails.newMessagePostedMailForAdmin', $data_arr, function($m) use ($data_arr){
					$m->to(Config::get('generalConfig.support_email'));
					$m->subject($data_arr['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}

			//To User
			$from =  $data_arr['from_user_details']['display_name'];
			$data_arr['to_email'] = $data_arr['to_user_details']['email'];
			$data_arr['to_name'] = $data_arr['to_user_details']['display_name'];
			$data_arr['subject'] = str_replace(array('VAR_SITENAME','VAR_BUYERNAME'), array(Config::get('generalConfig.site_name'),$from), trans('common.contact_subject'));


			Event::fire('send.newmessageposted.mail', array($data_arr));

			/*Mail::send('emails.newMessagePostedMailForUser', $data_arr, function($m) use ($data_arr){
				$m->to($data_arr['to_email'], $data_arr['to_name']);
				$m->subject($data_arr['subject']);
			});*/

		}
	}

	public function getMessagesList($view_type = 'inbox', $user_id=null, $return_type = 'paginate', $limit = 10){

		if(is_null($user_id) || $user_id==''|| $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$messages = Message::orderby('message.id','desc');
		if($view_type=='inbox')
		{
			$messages->where('to_user_id',$user_id)->where('to_message_status','!=','Deleted');
		}
		elseif($view_type=='sent')
		{
			$messages->where('from_user_id',$user_id)->where('from_message_status','!=','Deleted');
		}
		elseif($view_type=='trash')
		{
			$messages->whereRaw('((to_user_id = ? and to_message_status = ?) OR (from_user_id = ? and from_message_status = ?))',array($user_id,'Deleted',$user_id,'Deleted'));
		}
		elseif($view_type=='saved')
		{
			$messages->whereRaw('((to_user_id = ? and to_message_status = ?) OR (from_user_id = ? and from_message_status = ?))',array($user_id,'Saved',$user_id,'Saved'));
		}

		if($return_type == 'paginate')
			$messages = $messages->paginate($limit);
		else
			$messages = $messages->get();

		return $messages;


	}
	public function getInboxUnreadCount($user_id = null){

		if(is_null($user_id) || $user_id=='' || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$count = Message::where('to_user_id',$user_id)->where('to_message_status','Unread')->count();
		return $count;

	}

	public function updateMessageStatus($message_ids = array(), $action = 'save', $user_id = null)
	{
		if(is_null($user_id) || $user_id != '' || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();

		if(!empty($message_ids))
		{
			$save_string = 'Saved';
			if($action == 'delete')
				$save_string = 'Deleted';
			elseif($action == 'read')
				$save_string = 'Read';
			elseif($action == 'unread')
				$save_string = 'Unread';
			$message_ids = implode(',',$message_ids);
			DB::update('UPDATE message SET from_message_status= CASE WHEN from_user_id = ? THEN ? ELSE from_message_status END , to_message_status= CASE WHEN to_user_id = ? THEN ? ELSE to_message_status END WHERE id IN ('.$message_ids.')', array($user_id,$save_string,$user_id,$save_string));
			return true;
		}
		return false;
	}

	public function getMessageDetails($message_id = null){
		if(is_null($message_id) || $message_id=='' || $message_id<=0)
			return false;
		$message_details = Message::where('id',$message_id)->first();
		return $message_details;

	}

	public function getPreviousNextMessages($message_id=null, $message_type=null, $user_id=null){
		//echo "<br>message_id: ".$message_id;

		$default_array = array('prev_id'=>0, 'next_id'=>0);
		//echo "<br>default_array: ";echo "<pre>";print_r($default_array);echo "</pre>";exit;
		if(is_null($message_id) || $message_id=='' || $message_id<=0 || is_null($message_type) || $message_type=='')
			return $default_array;
		if(is_null($user_id) || $user_id != '' || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();
		$message_type = in_array($message_type,array('inbox','sent','saved','trash'))?$message_type:'';
		$prev_id=0;$next_id=0;

		if($message_type!='')
		{
			switch($message_type)
			{
				case 'inbox':
					$prev_id = Message::where('to_user_id',$user_id)->where('to_message_status','!=', 'Deleted')->where('id','<',$message_id)->pluck('id');
					$next_id = Message::where('to_user_id',$user_id)->where('to_message_status','!=', 'Deleted')->where('id','>',$message_id)->pluck('id');
					break;
				case 'sent':
					$prev_id = Message::where('from_user_id',$user_id)->where('from_message_status','!=', 'Deleted')->where('id','<',$message_id)->pluck('id');
					$next_id = Message::where('from_user_id',$user_id)->where('from_message_status','!=', 'Deleted')->where('id','>',$message_id)->pluck('id');
					break;
				case 'trash':
					$prev_id = Message::whereRaw('((to_user_id = ? and to_message_status = ?) OR (from_user_id = ? and from_message_status = ?))',array($user_id,'Deleted',$user_id,'Deleted'))->where('id','<',$message_id)->pluck('id');
					$next_id = Message::whereRaw('((to_user_id = ? and to_message_status = ?) OR (from_user_id = ? and from_message_status = ?))',array($user_id,'Deleted',$user_id,'Deleted'))->where('id','>',$message_id)->pluck('id');
					break;
				case 'saved':
					$prev_id = Message::whereRaw('((to_user_id = ? and to_message_status = ?) OR (from_user_id = ? and from_message_status = ?))',array($user_id,'Saved',$user_id,'Saved'))->where('id','<',$message_id)->pluck('id');
					$next_id = Message::whereRaw('((to_user_id = ? and to_message_status = ?) OR (from_user_id = ? and from_message_status = ?))',array($user_id,'Saved',$user_id,'Saved'))->where('id','>',$message_id)->pluck('id');
					break;
			}
		}
		$final_array= array('prev_id'=>$prev_id,'next_id'=>$next_id);
		$return_arr = $final_array+$default_array;
		return $return_arr;
	}
	public function getUserIdFromUsernames($user_name=''){
		if($user_name == '')
			return false;
		$user_id = User::where('user_name',$user_name)->where('is_banned','!=','1')->pluck('id');
		if($user_id=='')
			return false;
		else
			return $user_id;
	}
}
