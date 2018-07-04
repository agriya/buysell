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
class AdminMassEmailController extends BaseController
{
	function __construct()
	{
		parent::__construct();
       	$this->massEmail 	= new AdminMassEmailService();
    }

	public function getCompose($method='', $id=0)
	{
		$d_arr 				= array();
		$d_arr['pageTitle'] = trans("admin/massEmail.title");
		$mail_details 		= array();
		if($method=='edit' && ($id != '' && $id != 0))
		{
			$mail_details 		= $this->massEmail->getComposer($id);
		}
		else if($method=='view' && ($id != '' && $id != 0))
		{
			$mail_details = $this->massEmail->getComposer($id);
			return View::make('admin/massEmailComposerView', compact('d_arr','mail_details'));
		}

		return View::make('admin/massEmailComposer', compact('d_arr','mail_details'));
	}
	public function postCompose()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$rule = array(
						'subject'		=>  $this->massEmail->getValidatorRule('subject'),
						'content' 		=>  $this->massEmail->getValidatorRule('content'),
						'send_on' 		=>  $this->massEmail->getValidatorRule('send_on'),
						'from_name' 	=>  $this->massEmail->getValidatorRule('from_name'),
						'from_email' 	=>  $this->massEmail->getValidatorRule('from_email'),
						'repeat_every' 	=>  $this->massEmail->getValidatorRule('repeat_every'),
						'repeat_for' 	=>  $this->massEmail->getValidatorRule('repeat_for'),
					);
			if((!Input::has('mail_to')) && (!Input::has('getusers')))
			{
				$rule['send_to_all'] = 'Required';
			}
			if((Input::has('mail_to') && Input::get('mail_to') != 'newsletter') || Input::has('getusers'))
			{
				if((!Input::has('user_status')))
				{
					$rule['user_status_err'] = 'Required';
				}
			}
			if(Input::has('mail_to') && Input::get('mail_to') == 'newsletter')
			{
				if((!Input::has('offer_newsletter')))
				{
					$rule['offer_newsletter_err'] = 'Required';
				}
			}
			$messages = array('repeat_every.numeric' => trans('admin/massEmail.composer.number_err_msg'),
							  'repeat_for.numeric' => trans('admin/massEmail.composer.number_err_msg'));
			$validator = Validator::make(Input::all(), $rule, $messages);
			if($validator->fails())
			{
				$url = '';
				if(Input::get('id'))
					$url='/edit/'.Input::get('id');
				return Redirect::to('admin/mass-email/compose'.$url)->withErrors($validator)->withInput(Input::all());
			}
			else
			{
				$this->massEmail->setMassEmailArr(Input::All(), 'composer');
				if(Input::get('id'))
				{
					$this->massEmail->UpdateComposeEmail(Input::get('id'));
					$message=trans('admin/massEmail.composer.update_success');
				}
				else
				{
					$this->massEmail->AddComposeEmail();
					$message=trans('admin/massEmail.composer.added_success');
				}
				return Redirect::to('admin/mass-email/list')->with('success_message',$message);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/mass-email/list')->with('error_message',$errMsg);
		}
	}

	public function getList()
	{
		$this->massEmail->setMassEmailArr(Input::All(),'list');
		$d_arr['search_entries_arr'] = trans('common.per_page');
		$d_arr['search_status_arr'] = array('pending'=> trans('admin/massEmail.list.pending'), 'sent' => trans('admin/massEmail.list.sent'), 'cancelled' => trans('admin/massEmail.list.cancelled'), 'progress' => trans('admin/massEmail.list.progress'));
		$d_arr['pageTitle'] = trans("admin/massEmail.title");
		$perPage    = (Input::has('perpage')) ? Input::get('perpage') : 10;
		$q = $this->massEmail->buildMassMailListQuery();
		$details 	= $q->paginate($perPage);
		return View::make('admin/massEmailList',compact('details','d_arr'));
	}

	public function getChangeMassMailStatus($id=0)
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			if($id == 0)
				return Redirect::to('admin/mass-email/list');
			$this->massEmail->ChangeMassMailStatus($id);
			return Redirect::to('admin/mass-email/list');
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/mass-email/list')->with('error_message',$errMsg);
		}
	}

	public function getListVariable()
	{
		$details = array();
		$details['{$userid}'] = trans('admin/massEmail.user_id');
		$details['{$firstname}'] = trans('admin/massEmail.first_name');
		$details['{$lastname}'] = trans('admin/massEmail.last_name');
		$details['{$email}'] = trans('admin/massEmail.email');
		$details['{$signature}'] = trans('admin/massEmail.signature');
		return View::make('admin/massEmailVariableList', compact('details'));
	}

	public function getPreviewMassMail()
	{
		return View::make('admin/previewMassMail');
	}

	public function postPreviewMassMail()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$messages = array();
			$messages['preview_email.required']= trans('common.required');
			$messages['preview_email.required']= trans('common.required');
			$rules = array(
				'preview_email' 			=>  'Required|email',
				'preview_mass_mail' 			=>  'Required'
			);
			$validator   = Validator::make(Input::all(), $rules, $messages);
			if(Input::has('preview_mass_mail') && Input::get('preview_mass_mail') != '')
			{
				if($validator->fails())
				{
					return Redirect::to('admin/mass-email/preview-mass-mail')->withErrors($validator)->withInput(Input::all());
				}
				else
				{
					$success_msg = trans('admin/massEmail.composer.preview_send_success');
					$result = $this->massEmail->sendPreviewMail(Input::all());
					if ($result) {
						return Redirect::to('admin/mass-email/preview-mass-mail')->with('success_message', $success_msg);
					} else {
						$error_msg = Session::get('mailer_error');
						return Redirect::to('admin/mass-email/preview-mass-mail')->with('error_message', $error_msg);
					}
				}
			}
			else
			{
				$error_msg = trans('admin/massEmail.composer.preview_send_error');
				return Redirect::to('admin/mass-email/preview-mass-mail')->with('error_message', $error_msg);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/mass-email/preview-mass-mail')->with('error_message',$errMsg);
		}
	}

	public function getShowMailUsers()
	{
		$action = Input::get('action');
		$mail_id = Input::get('mail_id');

		$d_arr = array();
		if($action == 'sent')
			$d_arr['pageTitle'] = trans('admin/massEmail.list.mail_sent_users');
		else
			$d_arr['pageTitle'] = trans('admin/massEmail.list.mail_notsent_users');
		$user_list = $user_details = array();
		if($mail_id) {
			$is_valid_mail = MassMail::where('id', $mail_id)->count();
			if($is_valid_mail) {
				$q = $this->massEmail->buildMailUsersQuery($action, $mail_id);
				$page 		= (Input::has('page')) ? Input::get('page') : 1;
				$start 		= (Input::has('start')) ? Input::get('start') : 10;
				$perPage	= 10;
				$user_list 	= $q->paginate($perPage);
				foreach($user_list AS $userKey => $user) {
					$user_details[$userKey]= $user;
				}
				$member_list_count = count($user_details);
				return View::make('admin/mailUsersList', compact('d_arr', 'user_list', 'user_details'));
			}
			else {
				$d_arr['error_msg'] = trans("admin/massEmail.list.invalid_mail_id");
				return View::make('admin/mailUsersList', compact('d_arr'));
			}
		}
		else {
			return Redirect::to('admin/mass-email/list');
		}
	}

	public function getDeleteMassEmail($mass_email_id = '')
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			if($mass_email_id)
			{
				$this->massEmail->deleteMassEmail($mass_email_id);
				$success_msg = trans('admin/massEmail.list.email_delete_success');
				return Redirect::to('admin/mass-email/list')->with('success_message', $success_msg);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/mass-email/list')->with('error_message',$errMsg);
		}
	}

	public function getResendMassEmail($mass_email_id = '')
	{
		if($mass_email_id)
		{
			$this->massEmail->resendMassEmail($mass_email_id);
			$success_msg = trans('admin/massEmail.list.resend_mail_success');
			return Redirect::to('admin/mass-email/list')->with('success_message', $success_msg);
		}
	}
}