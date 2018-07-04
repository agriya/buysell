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
set_time_limit(0);
class MassMailCronController extends BaseController
{
	public function getIndex()
	{
		$details = MassMail::where('status', 'progress')->first();
		//if no progress record, fetch the pending record scheduled for today
		if(!$details)
		{
			$details =  MassMail::where('status', 'pending')
						->whereRaw("DATE_FORMAT( send_on, '%Y-%m-%d' ) <= CURDATE()")
						->where('is_deleted', 0)
						->first();
		}

		if($details)
		{
			$limit	= Config::get('site.cron_mail_sending_limit');
			MassMail::where('id', $details['id'])->update(array('status' => 'progress'));
			if($details['send_to'] == 'all' || $details['send_to'] == 'picked_user')
			{
				if($details['send_to'] == 'all')
				{
					$userDetails = User::select('id', 'email', 'first_name', 'last_name');
					if($details['upto_user_id'] != '' || $details['upto_user_id'] != 0)
					{
						$userDetails = $userDetails->where('id', '>', $details['upto_user_id']);
					}
				}
				else if($details['send_to']== 'picked_user')
				{
					$userDetails = MassMailUsers::select('mass_mail_users.user_id', 'users.email', 'users.first_name', 'users.last_name');
					$userDetails = $userDetails->join('users','users.id', '=', 'mass_mail_users.user_id');
					$userDetails = $userDetails->where('mass_mail_users.mass_email_id', '=', $details['id']);
					if($details['upto_user_id']!='' || $details['upto_user_id']!=0)
					{
						$userDetails->where('mass_mail_users.user_id', '>', $details['upto_user_id']);
					}
				}
				if($details['send_to_user_status']== 'active')
				{
					$userDetails = $userDetails->whereRaw('activated = ? AND is_banned = ?', array('1', '0'));
				}
				else if($details['send_to_user_status']== 'inactive')
				{
					$userDetails = $userDetails->whereRaw('activated = ? OR is_banned = ?', array('0', '1'));
				}
				$userDetails = $userDetails->where('subscribe_newsletter', '1');
				if($details['send_to']== 'picked_user')
				{
					$userDetails->orderby('mass_mail_users.user_id', 'asc');
				}
				$userDetails = $userDetails->take($limit);
				$userDetails = $userDetails->get();
			}
			else if($details['send_to']== 'newsletter')
			{
				$userDetails = NewsletterSubscriber::select('id', 'user_id', 'email', 'first_name', 'last_name');
				$userDetails = $userDetails->where('status', 'active');
				if($details['upto_user_id']!='' || $details['upto_user_id']!=0)
				{
					$userDetails = $userDetails->where('id','>',$details['upto_user_id']);
				}
				if($details['offer_newsletter'] == 'yes')
				{
					$userDetails = $userDetails->where('user_id', '>', 0);
				}
				$userDetails = $userDetails->take($limit);
				$userDetails = $userDetails->get();
			}
			if(count($userDetails) > 0)
			{
				$mail_template = "emails/massMail";
				$data['subject'] = $subject = $details['subject'];
				$data['content'] = $details['content'];
				$uptoUserId = 0;
				foreach ($userDetails as $inc => $user)
				{
					$data['name'] = $user['first_name'].' '.$user['last_name'];
					$user['from_email']   = $details['from_email'];
					$user['from_name']   = $details['from_name'];

					$search_arr = array('{$userid}', '{$firstname}', '{$lastname}', '{$email}', '{$signature}');
					$signature = nl2br(Config::get('site.mass_email_signature'));
					$replace_arr = array($user['user_id'], $user['first_name'], $user['last_name'], $user['email'], $signature);
					$data['content'] =  str_replace($search_arr, $replace_arr, $details['content']);
					$data['subject'] = $subject = str_replace($search_arr, $replace_arr, $details['subject']);

					$unsubscribe_code = '';
					if($details['send_to']== 'newsletter')
						$unsubscribe_code = $this->getUnsubscribeCode($user['id']);
					$data['unsubscribe_code'] =  $unsubscribe_code;
					try {
						Mail::send($mail_template, 	$data, 	function($m) use ($user, $subject)
						{
							$m->from($user['from_email'], $user['from_name']);
							$m->to($user['email'])->subject($subject);
						}
						);
					} catch (Exception $e) {
						//return false
						CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
					}
					if($details['send_to']== 'newsletter' || $details['send_to'] == 'all')
						$uptoUserId = $user['id'];
					else
						$uptoUserId = $user['user_id'];

					MassMail::where('id', $details['id'])->update(array('upto_user_id' => $uptoUserId));

					//Add in Mass mail sent users
					$mass_mail_sent_user = new MassMailSentUsers;
					$mass_mail_sent_user_arr = array();
					$mass_mail_sent_user_arr['mass_email_id'] = $details['id'];
					if($details['send_to']== 'newsletter' || $details['send_to'] == 'all')
						$mass_mail_sent_user_arr['user_id'] = $user['id'];
					else
						$mass_mail_sent_user_arr['user_id'] = $user['user_id'];
					$mass_mail_sent_user_arr['date_added'] = DB::raw('Now()');
					$mass_mail_sent_user->addNew($mass_mail_sent_user_arr);
				}
			}
			else
			{
				MassMail::where('id', $details['id'])->update(array('status' => 'sent'));
				$reschedule = false;
				$this->massEmail = new AdminMassEmailService();
				if($details['reschedule_id'] == 0) {
					$parent_id = $details['id'];
					if($details['repeat_every'] != "" && $details['repeat_every'] > 0) {
						if(($details['reschedule_times'] + 1) < $details['repeat_for']) {
							$reschedule = true;
							$this->massEmail->resendMassEmail($details['id'], true, $details['repeat_every'], $details['id']);
						}
					}
				}
				else {
					$parent_id = $details['reschedule_id'];
					$parent_details = MassMail::where('id', $parent_id)->first();
					if(($parent_details['reschedule_times'] + 1) < $parent_details['repeat_for']) {
						$reschedule = true;
						$this->massEmail->resendMassEmail($details['id'], true, $parent_details['repeat_every'], $parent_details['id']);
					}
				}
				//Update reschedule times
				if($reschedule) {
					MassMail::where('id', $parent_id)->increment('reschedule_times');
				}
			}
		}
	}

	public function getUnsubscribeCode($id)
	{
		$unsubscribe_details = NewsletterSubscriber::where('id', $id)->first();
		if(count($unsubscribe_details) > 0)
		{
			return $unsubscribe_details->unsubscribe_code;
		}
		return '';
	}
}