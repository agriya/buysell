<?php
class EmailNotifierInterface implements NotifierInterface {
	public function notify($user, $data)
	{
		$content = isset($data['content'])?$data['content']:'';
		$content = str_replace('VAR_SITE_NAME', Config::get('generalConfig.site_name'), $content);
		$content = str_replace('VAR_SITE_URL', URL::to('/'), $content);
		$data['content'] = $content;

		$subject = isset($data['subject'])?$data['subject']:'Mail from '.Config::get('generalConfig.site_name');
		$subject = str_replace('VAR_SITE_NAME', Config::get('generalConfig.site_name'), $subject);
		$subject = str_replace('VAR_SITE_URL', URL::to('/'), $subject);
		$data['subject'] = $subject;

		try {
		    Mail::send('emails.commonMailFormat', $data, function($m) use ($user, $data)
			{
				$email = (isset($user->email) && $user->email!='')?$user->email:( (isset($user['email']) && $user['email']!='')?$user['email']:Config::get('generalConfig.invoice_email'));
				$name = (isset($user->first_name) && $user->first_name!='')?$user->first_name:( (isset($user['diaplay_name']) && $user['diaplay_name']!='')?$user['diaplay_name']:'');
			    $m->to($email, $name);
				$m->subject($data['subject']);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}
}
?>