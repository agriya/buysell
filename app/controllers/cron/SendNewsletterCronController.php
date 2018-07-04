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
class SendNewsletterCronController extends BaseController
{
	public function __construct()
	{
	}
	public function getIndex()
	{
		$pending_newsletters = Newsletter::whereIn('status',array('Pending','Started'))->take(10)->orderby('id','asc')->get();

		if(count($pending_newsletters) > 0)
		{
			foreach($pending_newsletters as $newsletter)
			{
				$newsletter_emails = NewsletterUsers::where('newsletter_id',$newsletter->id)->where('is_sent','No')->take(50)->orderby('id','asc')->get();
				if(count($newsletter_emails) > 0)
				{
					$sent_email_ids = array();
					foreach($newsletter_emails as $email)
					{
						$subject = $message = '';
						$subject = $newsletter->subject;
						$message = $newsletter->message;
						$data_arr = array();
						$replace_arr = array('VAR_SITE_NAME' => Config::get('generalConfig.site_name'), 'VAR_USERNAME' => $email->user_name, 'VAR_EMAIL' => $email->emaail);
						$subject = str_replace(array_keys($replace_arr), array_values($replace_arr), $subject);
						$message = str_replace(array_keys($replace_arr), array_values($replace_arr), $message);
						$user_name = $email->user_name;
						$data_arr['newsletter'] = compact('subject', 'message', 'user_name');
						$data_arr['subject'] = $subject;
						$data_arr['to'] = $email->email;
						//$data = Response::make(View::make('emails.newsLetter', compact('data_arr')));
						//echo "<pre>";print_r($data_arr);echo "</pre>";exit;
						//echo "<br>data: ".$data;exit;
						try {
							Mail::send('emails.newsLetter', $data_arr, function($m) use ($data_arr){
								$m->to($data_arr['to']);
								$m->subject($data_arr['subject']);
							});
						} catch (Exception $e) {
							//return false
							CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
						}
						$sent_email_ids[] = $email->id;
					}
					if(!empty($sent_email_ids))
					{
						//update the status for each mail
						NewsletterUsers::whereIn('id',$sent_email_ids)->update(array('is_sent'=>'Yes'));

						//update the total sent
						$sent_now = count($sent_email_ids);
						Newsletter::where('id',$email->newsletter_id)->increment('total_sent', $sent_now);
						Newsletter::where('id',$email->newsletter_id)->update(array('status'=> 'Started'));
					}

				}
				else
				{
					//if no mails are there to send then update the status as finished
					Newsletter::where('id',$newsletter->id)->update(array('status' => 'Finished'));
				}
			}
		}
	}
}