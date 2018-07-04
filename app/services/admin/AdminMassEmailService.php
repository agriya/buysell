<?php
class AdminMassEmailService
{
	public function getValidatorRule($field)
	{
		$rules = array(
				'subject' 		=> 	'Required',
				'content' 		=>	'Required',
				'send_on' 		=>	'Required',
				'from_name' 	=>	'Required',
				'from_email' 	=>	'Required|Email',
				'repeat_every' 	=>	'numeric',
				'repeat_for' 	=>	'numeric'
		);
		return isset($rules[$field])? $rules[$field] : 'Required';
	}

	public function setMassEmailArr($input,$type)
	{
		switch($type)
		{
			case 'composer':
			$this->MassEmail_arr['from_name'] 			= (isset($input['from_name']) && $input['from_name'] != '') ? $input['from_name'] : "";
			$this->MassEmail_arr['from_email'] 			= (isset($input['from_email']) && $input['from_email'] != '') ? $input['from_email'] : "";
			$this->MassEmail_arr['subject'] 			= (isset($input['subject']) && $input['subject'] != '') ? $input['subject'] : "";
			$this->MassEmail_arr['content'] 			= (isset($input['content']) && $input['content'] != '') ? $input['content'] : "";
			$this->MassEmail_arr['send_on'] 			= (isset($input['send_on']) && $input['send_on'] != '') ? $input['send_on'] : "";
			$this->MassEmail_arr['send_to']				= (isset($input['mail_to']) && $input['mail_to'] != '') ? $input['mail_to'] : 'all';
			$this->MassEmail_arr['send_to_user_status']	= (isset($input['user_status']) && $input['user_status'] != '') ? $input['user_status'] : 'all';
			$this->MassEmail_arr['getusers']			= (isset($input['getusers']) && $input['getusers'] != '') ? $input['getusers'] : "";
			$this->MassEmail_arr['offer_newsletter']	= (isset($input['offer_newsletter']) && $input['offer_newsletter'] != '') ? $input['offer_newsletter'] : "no";
			$this->MassEmail_arr['repeat_every'] 		= (isset($input['repeat_every']) && $input['repeat_every'] != '') ? $input['repeat_every'] : "";
			$this->MassEmail_arr['repeat_for'] 			= (isset($input['repeat_for']) && $input['repeat_for'] != '') ? $input['repeat_for'] : "";
			break;

			case 'list':
			$this->MassEmail_arr['subject'] 			= (isset($input['subject']) && $input['subject'] != '') ? $input['subject'] : "";
			$this->MassEmail_arr['send_on'] 			= (isset($input['send_on']) && $input['send_on'] != '') ? $input['send_on'] : "";
			$this->MassEmail_arr['status'] 				= (isset($input['status']) && $input['status'] != '') ? $input['status'] : "";
			$this->MassEmail_arr['from-date']			= (isset($input['from-date']) && $input['from-date'] != '') ? $input['from-date'] : "";
			$this->MassEmail_arr['to-date']				= (isset($input['to-date']) && $input['to-date'] != '') ? $input['to-date'] : "";
			$this->MassEmail_arr['order_by']			= (!isset($input['order_by'])) ? 'desc' : ($input['order_by']=='desc'? "desc":"asc");
			$this->MassEmail_arr['order_by_field']		= (!isset($input['order_by_field'])) ? 'id' : ($input['order_by_field']);
			break;
		}
	}

	public function getMassEmailVal($key)
	{
		return (isset($this->MassEmail_arr[$key])) ? $this->MassEmail_arr[$key] : "";
	}

	public function AddComposeEmail()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$send_to 				= $this->getMassEmailVal('send_to');
		$send_to_user_status 	= $this->getMassEmailVal('send_to_user_status');
		$send_on 				= $this->getMassEmailVal('send_on');
		$subject   				= $this->getMassEmailVal('subject');
		$content  				= $this->getMassEmailVal('content');
		$from_name  			= $this->getMassEmailVal('from_name');
		$from_email  			= $this->getMassEmailVal('from_email');
		$reply_to_email  		= Config::get('mail.from_email');
		$offer_newsletter		= $this->getMassEmailVal('offer_newsletter');
		$status					= 'pending';
		$repeat_every			= $this->getMassEmailVal('repeat_every');
		$repeat_for				= $this->getMassEmailVal('repeat_for');
		$addComposerarr		= array('send_on' => $send_on,
									'subject' => $subject,
									'content' => $content,
									'send_to' => $send_to ,
									'send_to_user_status' => $send_to_user_status ,
							 		'from_email'=> $from_email,
									'from_name'=> $from_name,
									'reply_to_email' => $reply_to_email,
									'status' => $status,
									'offer_newsletter' => $offer_newsletter,
									'upto_user_id' => '0',
									'user_id' => $logged_user_id,
									'repeat_every' => $repeat_every,
									'repeat_for' => $repeat_for );
		$lid = MassMail::insertGetId($addComposerarr);
		$send_to_limit	= $this->getMassEmailVal('getusers');
		if($send_to == 'picked_user')
		{
			$limitusers = explode(",", $send_to_limit);
			for($i = 0; $i < count($limitusers); $i++)
			{
				$addUserComposerarr = array('mass_email_id' => $lid, 'user_id' => $limitusers[$i]);
				MassMailUsers::insert($addUserComposerarr);
			}
		}
		return true;
	}

	public function UpdateComposeEmail($id)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$send_to 				= $this->getMassEmailVal('send_to');
		$send_to_user_status 	= $this->getMassEmailVal('send_to_user_status');
		$send_on 			= $this->getMassEmailVal('send_on');
		$subject   			= $this->getMassEmailVal('subject');
		$content  			= $this->getMassEmailVal('content');
		$from_name  			= $this->getMassEmailVal('from_name');
		$from_email  			= $this->getMassEmailVal('from_email');
		$reply_to_email  	= Config::get('mail.from_email');
		$offer_newsletter		= $this->getMassEmailVal('offer_newsletter');
		$status				= 'pending';
		$repeat_every			= $this->getMassEmailVal('repeat_every');
		$repeat_for				= $this->getMassEmailVal('repeat_for');
		$updateComposerarr	= array('send_on' => $send_on,
									'subject' => $subject,
									'content' => $content,
									'send_to' => $send_to ,
									'send_to_user_status' => $send_to_user_status ,
									'from_email' => $from_email,
									'from_name' => $from_name,
									'reply_to_email' => $reply_to_email,
									'status' => $status,
									'offer_newsletter' => $offer_newsletter,
									'upto_user_id' => '0',
									'user_id' => $logged_user_id,
									'repeat_every' => $repeat_every,
									'repeat_for' => $repeat_for );

		MassMail::where('id','=',$id)->update($updateComposerarr);
		MassMailUsers::where('mass_email_id','=', $id)->delete();
		$send_to_limit	= $this->getMassEmailVal('getusers');
		if($send_to == 'picked_user')
		{
			$limitusers = explode(",", $send_to_limit);
			for($i = 0; $i < count($limitusers); $i++)
			{
				$addUserComposerarr = array('mass_email_id' => $id, 'user_id' => $limitusers[$i]);
				MassMailUsers::insert($addUserComposerarr);
			}
		}
		return true;
	}

	public function getComposer($id)
	{
		$arr = array();
		$this->qry = MassMail::Select('mass_mail.id', DB::raw("DATE_FORMAT(mass_mail.send_on,'%Y-%m-%d') As 'send_on'"), 'mass_mail.subject',
									'mass_mail.content', 'mass_mail.send_to', 'mass_mail.status', 'mass_mail.from_email', 'mass_mail.from_name',
									'mass_mail.send_to_user_status', 'mass_mail.offer_newsletter', 'mass_mail.repeat_every', 'mass_mail.repeat_for',
									'mass_mail.reschedule_id')
				->where('mass_mail.id', '=', $id)
				->where('mass_mail.is_deleted', 0)
				->first();
		$arr[0]=	$this->qry;
		if($arr[0]->reschedule_id == 0) {
			$this->qry1 = MassMailUsers::Select('mass_mail_users.user_id', 'users.first_name', 'users.last_name')
							->join('users','users.id','=','mass_mail_users.user_id')
							->where('mass_mail_users.mass_email_id','=',$id)
							->get();
		} else {
			$this->qry1 = MassMailUsers::Select('mass_mail_users.user_id', 'users.first_name', 'users.last_name')
							->join('users','users.id','=','mass_mail_users.user_id')
							->where('mass_mail_users.mass_email_id','=',$arr[0]->reschedule_id)
							->get();
		}
		$cnt=0;
		$value='';
		$name='';
		foreach($this->qry1 as $key => $val)
		{
			if($cnt>0)
			{
				$value=$value.',';
				$name=$name.',';
			}
			$value=$value.$val['user_id'];
			$name=$name.$val['first_name'].' '.$val['last_name'];
			$cnt++;
		}
		$arr[1]=$value;
		$arr[2]=$name;
		return $arr;
	}

	public function buildMassMailListQuery()
	{
		$this->qry = MassMail::Select('id', 'send_on', 'subject', 'status');
		if($this->getMassEmailVal('subject'))
		{
			$this->qry->where("subject", "like" ,'%'.$this->getMassEmailVal('subject').'%');
		}

		if($this->getMassEmailVal('status'))
		{
			$this->qry->where('status',$this->getMassEmailVal('status'));
		}

		if($this->getMassEmailVal('from-date') &&  $this->getMassEmailVal('to-date'))
		{
			$this->qry->where(DB::raw("DATE_FORMAT( send_on, '%Y-%m-%d' )"), '>=', $this->getMassEmailVal('from-date'));
			$this->qry->where(DB::raw("DATE_FORMAT( send_on, '%Y-%m-%d' )"), '<=', $this->getMassEmailVal('to-date'));
		}

		if(!$this->getMassEmailVal('from-date') &&  $this->getMassEmailVal('to-date'))
		{
			$this->qry->where(DB::raw("DATE_FORMAT( send_on, '%Y-%m-%d' )"), '<=', $this->getMassEmailVal('to-date'));
		}

		if($this->getMassEmailVal('from-date') &&  !$this->getMassEmailVal('to-date'))
		{
			$this->qry->where(DB::raw("DATE_FORMAT( send_on, '%Y-%m-%d' )"), '=', $this->getMassEmailVal('from-date'));
		}

		$this->qry->where('is_deleted', 0);
		$this->qry->orderby($this->getMassEmailVal('order_by_field'),$this->getMassEmailVal('order_by'));
		return $this->qry;
	}

	public function ChangeMassMailStatus($id)
	{
		MassMail::where('id','=',$id)->update(array('status' => 'cancelled '));
		return true;
	}

	public function buildMailUsersQuery($action, $mail_id)
	{
		if($action == 'sent')
		{
			$details = MassMail::where('id', $mail_id)->first();
			if($details)
			{
				if($details['send_to']== 'newsletter')
				{
					$this->qry = MassMailSentUsers::select('mass_mail_sent_users.user_id', 'newsletter_subscriber.email', 'newsletter_subscriber.first_name', 'newsletter_subscriber.last_name');
					$this->qry = $this->qry->join('newsletter_subscriber','mass_mail_sent_users.user_id','=','newsletter_subscriber.id');
					if($details['offer_newsletter'] == 'yes')
					{
						$this->qry = $this->qry->where('newsletter_subscriber.user_id', '>', 0);
					}
				}
				else
				{
					$this->qry = MassMailSentUsers::select('mass_mail_sent_users.user_id', 'users.email', 'users.first_name', 'users.last_name');
					$this->qry = $this->qry->join('users','users.id','=','mass_mail_sent_users.user_id');
				}
				if($details['reschedule_id'] == 0) {
					$this->qry = $this->qry->where('mass_mail_sent_users.mass_email_id', '=', $mail_id);
				} else {
					$this->qry = $this->qry->where('mass_mail_sent_users.mass_email_id', '=', $details['reschedule_id']);
				}
			}
		}
		else {
			$details = MassMail::where('id', $mail_id)->first();
			if($details)
			{
				if($details['send_to']== 'all')
				{
					$this->qry = User::select('id', 'email', 'first_name', 'last_name');
					if($details['send_to_user_status']== 'active')
					{
						$this->qry = $this->qry->whereRaw('activated = ? AND is_banned = ?', array('1', '0'));
					}
					else if($details['send_to_user_status']== 'inactive')
					{
						$this->qry = $this->qry->whereRaw('activated = ? OR is_banned = ?', array('0', '1'));
					}
					if($details['upto_user_id']!='' || $details['upto_user_id']!=0)
					{
						$this->qry = $this->qry->where('id','>',$details['upto_user_id']);
					}
					$this->qry = $this->qry->where('subscribe_newsletter', '1');
					$this->qry = $this->qry->orderby('id','asc');
				}
				else if($details['send_to']== 'picked_user')
				{
					$this->qry = MassMailUsers::select('mass_mail_users.user_id', 'users.email', 'users.first_name', 'users.last_name');
					$this->qry = $this->qry->join('users','users.id','=','mass_mail_users.user_id');
					if($details['send_to_user_status']== 'active')
					{
						$this->qry = $this->qry->whereRaw('activated = ? AND is_banned = ?', array('1', '0'));
					}
					else if($details['send_to_user_status']== 'inactive')
					{
						$this->qry = $this->qry->whereRaw('activated = ? OR is_banned = ?', array('0', '1'));
					}
					$this->qry = $this->qry->where('mass_mail_users.mass_email_id','=',$details['id']);
					if($details['upto_user_id']!='' || $details['upto_user_id']!=0)
					{
						$this->qry->where('mass_mail_users.user_id','>',$details['upto_user_id']);
					}
					$this->qry = $this->qry->where('subscribe_newsletter', '1');
					$this->qry->orderby('mass_mail_users.user_id','asc');
				}
				else if($details['send_to']== 'newsletter')
				{
					$this->qry = NewsletterSubscriber::select('id', 'user_id', 'email', 'first_name', 'last_name');
					$this->qry = $this->qry->where('status', 'active');
					if($details['offer_newsletter'] == 'yes')
					{
						$this->qry = $this->qry->where('user_id', '>', 0);
					}
					if($details['upto_user_id']!='' || $details['upto_user_id']!=0)
					{
						$this->qry = $this->qry->where('id','>',$details['upto_user_id']);
					}
					$this->qry = $this->qry->orderby('id','asc');
				}
			}
		}
		return $this->qry;
	}

	public function sendPreviewMail($input)
	{
		$data['subject'] = $subject = $this->replaceVariablesOnMailContent($input['subject']);
		$data['name'] = '';
		$user['from_email']  = $input['from_email'];
		if($user['from_email'] !=''){
			$user['from_name']   = $input['from_name'];
			$user['to_email']   = $input['preview_email'];
			$data['content'] =  $this->replaceVariablesOnMailContent($input['preview_mass_mail']);

			$unsubscribe_code = '';
			$data['unsubscribe_code'] =  $unsubscribe_code;

			$mail_template = "emails/massMail";
			try {
				Mail::send($mail_template, 	$data, 	function($m) use ($user, $subject)
				{
					$m->from($user['from_email'], $user['from_name']);
					$m->to($user['to_email'])->subject($subject);
				});
			} catch (Exception $e) {
			     //An error happened and the email did not get sent
			     //echo($e->getMessage());
			     Session::flash('mailer_error', $e->getMessage());
			     return false;
			}
			return true;
		}else{
			Session::flash('mailer_error', trans('admin/massEmail.composer.please_provide_from_email'));
		    return false;
		}
	}

	public function replaceVariablesOnMailContent($mail_content)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$user_details = User::where('id', $logged_user_id)->first();
		if(count($user_details) > 0)
		{
			$search_arr = array('{$userid}', '{$firstname}', '{$lastname}', '{$email}', '{$signature}');
			$signature = nl2br(Config::get('site.mass_email_signature'));
			$replace_arr = array($user_details->user_id, $user_details->first_name, $user_details->last_name, $user_details->email, $signature);
			return $replace_content = str_replace($search_arr, $replace_arr, $mail_content);
		}
		else
		{
			return $mail_content;
		}
	}

	public function deleteMassEmail($mass_email_id)
	{
		MassMail::where('id', $mass_email_id)->update(array("is_deleted" => "1"));
	}

	public function resendMassEmail($mass_email_id, $reschedule = false, $repeat_every = 0, $parent_id = 0)
	{
		$details = MassMail::where('id', $mass_email_id)->first();
		if($details) {
			$addComposerarr = array();
			$senddate = $details['send_on'];
			if($reschedule) {
				$startdate = $details['send_on'];
				$repeat = $details['repeat_every'];
				if($repeat_every > 0) {
					$repeat = $repeat_every;
				}
				$senddate = date('Y-m-d H:i:s', strtotime($startdate . " +". $repeat ." days"));
			}
			$addComposerarr['send_on'] = $senddate;
			$addComposerarr['subject'] = $details['subject'];
			$addComposerarr['content'] = $details['content'];
			$addComposerarr['send_to'] = $details['send_to'];
			$addComposerarr['send_to_user_status'] = $details['send_to_user_status'];
			$addComposerarr['from_email'] = $details['from_email'];
			$addComposerarr['from_name'] = $details['from_name'];
			$addComposerarr['reply_to_email'] = $details['reply_to_email'];
			$addComposerarr['status'] = 'pending';
			$addComposerarr['offer_newsletter'] = $details['offer_newsletter'];
			$addComposerarr['upto_user_id'] = '0';
			$addComposerarr['user_id'] = $details['user_id'];
			$addComposerarr['repeat_every'] = 1;
			$addComposerarr['repeat_for'] = 1;
			if(!$reschedule) {
				$addComposerarr['repeat_every'] = $details['repeat_every'];
				$addComposerarr['repeat_for'] = $details['repeat_for'];
			}
			$addComposerarr['reschedule_id'] = $parent_id;
			$insert_mail_id = MassMail::insertGetId($addComposerarr);

			//Insert Mass mail users
			$mail_user_det = MassMailUsers::where('mass_email_id', $mass_email_id)->get();
			if(count($mail_user_det) > 0) {
				foreach($mail_user_det as $mail_user) {
					$insertMailUserarr = array();
					$insertMailUserarr['mass_email_id'] = $insert_mail_id;
					$insertMailUserarr['user_id'] = $mail_user->user_id;
					MassMailUsers::insert($insertMailUserarr);
				}
			}
		}
	}
}