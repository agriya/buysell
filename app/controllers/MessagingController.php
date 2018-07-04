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
class MessagingController extends BaseController
{
	function __construct()
	{
		parent::__construct();
		$this->messagingService = new MessagingService();
    }

    public function getIndex($message_type = 'inbox')
    {
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$message_type = (is_null($message_type) || $message_type=='' || !in_array($message_type,array('inbox','sent','trash','saved')))?'inbox':$message_type;
    	$messages = $this->messagingService->getMessagesList($message_type);
    	$inbox_unread_count = $this->messagingService->getInboxUnreadCount();
    	switch($message_type){
			case 'inbox':
				$meta_key_detail = 'inbox-message';
				break;
			case 'sent':
				$meta_key_detail = 'sent-message';
				break;
			case 'trash':
				$meta_key_detail = 'trash-message';
				break;
			case 'saved':
				$meta_key_detail = 'saved-message';
				break;
			default:
				$meta_key_detail = 'inbox-message';
				break;
		}
		$get_common_meta_values = Cutil::getCommonMetaValues($meta_key_detail);
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
    	return View::make('messagesList', compact('messages', 'message_type','inbox_unread_count', 'logged_user_id'));
	}
	public function getCompose()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$inbox_unread_count = $this->messagingService->getInboxUnreadCount();
		$inputs = Input::all();
		$action_details = array('user_names'=>'','subject' => '', 'message_text' => '');
		if(isset($inputs['message_id']) && $inputs['message_id'] >=0)
		{
			$message_details = $this->messagingService->getMessageDetails($inputs['message_id']);
			if($message_details && ($message_details->from_user_id==$logged_user_id || $message_details->to_user_id==$logged_user_id))
			{
				$from_user_details = CUtil::getUserDetails($message_details->from_user_id);
				$message_text = '<p>---------Original Message ---------<br>'.
								'From: '.$from_user_details['user_name'].'<br>'.
								'Sent: '.CUtil::FMTDate($message_details->date_added, "Y-m-d H:i:s", "").'<br>'.
								'Subjec: '.$message_details->subject.'</p>'.
								'<p>'.$message_details->message_text.'</p>';

				$action_details['message_text'] = $message_text;
				$action = (isset($inputs['action']) && $inputs['action'] != '')?$inputs['action']:'reply';
				if($action == 'forward')
				{
					$action_details['subject'] = 'FWD: '.$message_details->subject;
				}
				else
				{
					$from_user_details = CUtil::getUserDetails($message_details->from_user_id);
					$action_details['subject'] = 'RE: '.$message_details->subject;
					$action_details['user_names'] = $from_user_details['user_name'];
				}
			}
		}
		$user_details = User::select('user_name', 'id')->where('activated', '=', '1')->lists('user_name', 'user_name');
		$get_common_meta_values = Cutil::getCommonMetaValues('compose-message');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('messageCompose',compact('user_details', 'logged_user_id','inbox_unread_count','action_details'));
	}
	public function postCompose()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$inputs = Input::all();
		$rules = array('user_names'=>'required','subject'=>'required','message_text' => 'required');
		$validator = Validator::make($inputs,$rules);
		if($validator->passes())
		{
			$user_names = $inputs['user_names'];

			$error_user_name = array();
			$user_ids = array();
			if(is_array($user_names) && !empty($user_names))
			{
				foreach($user_names as $user_name)
				{
					$user_id = $this->messagingService->getUserIdFromUsernames($user_name);
					//echo "<br>user_id: ".$user_id;
					if(!$user_id || $user_id=='' || $user_id==$logged_user_id)
						$error_user_name[]=$user_name;
					else
						$user_ids[] = $user_id;
				}
			}
			if(!empty($error_user_name))
			{
				$invalid_user_name = implode(',',$error_user_name );
				return Redirect::back()->withInput()->with('error_message',trans('common.sorry').', \''.$invalid_user_name.'\' '. trans('mailbox.is_not_valid_username'));
			}
			$messageAddService = new MessageAddService();
			if(!empty($user_ids))
			{
				foreach($user_ids as $user_id)
				{
					$data_arr = array();
					$data_arr['from_user_id'] = $logged_user_id;
					$data_arr['user_code'] = $user_id;
					$data_arr['subject'] = $inputs['subject'];
					$data_arr['message_text'] = $inputs['message_text'];
					$data_arr['open_alert_needed'] = isset($inputs['open_alert_needed'])?$inputs['open_alert_needed']:'No';

					$message_id = $messageAddService->addMessage($data_arr);
				}
			}
			$after_goto = (isset($inputs['after_goto']) && $inputs['after_goto']!='')?$inputs['after_goto']:'inbox';
			$after_goto = in_array($after_goto,array('inbox','sent','compose'))?$after_goto:'inbox';
			if($after_goto!='compose')
				return Redirect::action('MessagingController@getIndex',$after_goto)->with('success_message',trans('mailbox.mail_sent_successfully'));
			else
				return Redirect::action('MessagingController@getCompose')->with('success_message',trans('mailbox.mail_sent_successfully'));
		}
		else
			return Redirect::back()->withInput()->withErrors($validator)->with('error_message',trans('common.enter_valid_inputs'));
	}

	public function getViewMessage($message_id = null)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if(is_null($message_id) || $message_id=='' || !ctype_digit($message_id) || $message_id<=0)
			return Redirect::to('messages')->with('error_message', trans('mailbox.invalid_mail_id'));

		$message_details = $this->messagingService->getMessageDetails($message_id);
		if(!$message_details || count($message_details) <=0)
			return Redirect::to('messages')->with('error_message', trans('mailbox.invalid_mail_id'));

		if($message_details->from_user_id!=$logged_user_id && $message_details->to_user_id!=$logged_user_id)
			return Redirect::to('messages')->with('error_message', trans('common.invalid_action'));

		$inputs = Input::all();
		$message_type = (isset($inputs['message_type']) && $inputs['message_type']!='')?$inputs['message_type']:'';
		if($message_type=='')
		{
			$message_type = ($message_details->from_user_id==$logged_user_id)?'sent':'inbox';
		}
		$message_status = ($message_details->from_user_id == $logged_user_id)?$message_details->from_message_status:$message_details->to_message_status;

		if($message_type!='trash' && strtolower($message_status)=='deleted')
			return Redirect::action('MessagingController@getIndex',$message_type);
		if($message_type=='inbox' && $message_details->to_message_status=='Unread')
		{
			if($message_details->open_alert_needed == 'Yes')
				$this->sendMessageOpenedAlert($message_details);
			$update = $this->messagingService->updateMessageStatus(array($message_id), 'read', $logged_user_id);
		}

		$previous_next_message_ids = $this->messagingService->getPreviousNextMessages($message_id,$message_type, $logged_user_id);
		//echo "<pre>";print_r($previous_next_message_ids);echo "</pre>";exit;

		$inbox_unread_count = $this->messagingService->getInboxUnreadCount();

		return View::make('messageView', compact('message_details', 'message_type', 'message_id','inbox_unread_count', 'logged_user_id', 'previous_next_message_ids', 'message_status'));
	}

	public function postChangeMsgStatus()
	{
		if(Input::get("action") != "" && Input::get("message_id") != "")
		{
			$updated_msg_id = $this->listMessagingService->updateMsgStatus(Input::get("message_id"), Input::get("action"));
			return Response::json($updated_msg_id);
		}
	}
	public function postBulkMessageAction(){

		$inputs = Input::all();
		$rules=array('message_ids' => 'required','action' => 'required|in:save,delete');
		$validator = Validator::make($inputs,$rules);
		if($validator->passes())
		{
			$action = $inputs['action'];
			$message_ids = is_array($inputs['message_ids'])?$inputs['message_ids']:array();
			$logged_user_id = BasicCUtil::getLoggedUserId();

			$update = $this->messagingService->updateMessageStatus($message_ids, $action, $logged_user_id);
			if($update)
				return Redirect::back()->with('success_message', trans('mailbox.message_status_update_success'));
			else
				return Redirect::back()->with('error_message', trans('common.some_problem_try_later'));
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator)->with('error_message', trans('common.some_problem_try_later'));
		}
	}
	public function sendMessageOpenedAlert($message_details)
	{
		if($message_details->to_message_status == 'Unread')
		{
			$message = new Message;
			$data_arr= array();
			$user_details = CUtil::getUserDetails($message_details->to_user_id);
			$data_arr['from_user_id'] = $message_details->to_user_id;
			$data_arr['to_user_id'] = $message_details->from_user_id;
			$data_arr['subject'] = Lang::get('mailbox.message_notification_for')." : ".$message_details->subject;
			$data_arr['message_text'] = Lang::get('mailbox.message_notification_text', array('mail_to' => $user_details['user_name'], 'opened_on' => CUtil::FMTDate(date('Y-m-d H:i:s'), "Y-m-d H:i:s", "d M, Y  h:i A")));
			$data_arr['open_alert_needed'] = 'No';
			$data_arr['last_replied_date'] = date('Y-m-d H:i:s');
			$data_arr['date_added'] = date('Y-m-d H:i:s');
			$message_id = $message->addNew($data_arr);
		}
	}
	public function getContactUs()
	{
		return View::make('contactUs',compact('logged_user_id','inbox_unread_count','action_details'));
	}
	public function postContactUs(){

	}
}