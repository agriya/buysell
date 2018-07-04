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
class UnSubscribeController extends BaseController
{

	function __construct()
	{
		parent::__construct();
		$this->userService = new UserAccountService();
    }

    public function getIndex($code)
    {
    	$is_valid_code = $this->userService->chkValidUnsubscribeCode($code);
    	$email = NewsletterSubscriber::where('unsubscribe_code', $code)->pluck('email');
    	$unsubscribe_email = '';
		if($email != '')
    		$unsubscribe_email = $email;

    	if(!$is_valid_code)
    		$d_arr['error_msg'] = trans('unsubscribe.already_unsubscribe');
		return View::make('unSubscribe', compact('code', 'd_arr', 'unsubscribe_email'));
	}

	public function postIndex()
	{
		$code = Input::get('code');
		$is_valid_code = $this->userService->chkValidUnsubscribeCode($code);
		if($is_valid_code)
		{
			$this->userService->setAsUnsubscribed($code);
			$d_arr['success_msg'] = trans('unsubscribe.success_msg');
		}
		else
		{
			$d_arr['error_msg'] = trans('unsubscribe.already_unsubscribe');
		}
		return View::make('unSubscribe', compact('code', 'd_arr'));
	}
}