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
class AdminCancellationPolicyController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
    {
    	$logged_user_id = BasicCUtil::getLoggedUserId();
		$d_arr['user_id'] = $logged_user_id;
		$d_arr['pageTitle'] = trans('admin/cancellationpolicy.cancellation_policy');//;
		$cancellation_policy = Products::initializeCancellationPolicy();
		$cancellation_policy = $cancellation_policy->getCancellationPolicyDetails($logged_user_id);
    	$this->header->setMetaTitle(trans('meta.admin_manage_cancellation_policy'));
		return View::make('admin.cancellationPolicy', compact('cancellation_policy','d_arr'));
	}
	public function postIndex()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			if(Input::has('add_cancellation_policy'))
			{
				$cancellationPolicyService = new CancellationPolicyService();
				$cancellation_policy = Products::initializeCancellationPolicy();
				$logged_user_id = BasicCUtil::getLoggedUserId();
				$input_arr = Input::All();
				if(Input::get('id') >0)
					$cancellation_policy->setCancellationPolicyId($logged_user_id);

				$cancellation_policy->setUserId($logged_user_id);
				if (Input::hasFile('shop_cancellation_policy_file'))
				{
					if($_FILES['shop_cancellation_policy_file']['error'])
					{
						$error_message = trans("common.uploader_max_file_size_err_msg");
						return Redirect::action('AdminCancellationPolicyController@postIndex')->withInput()->withErrors($v)->with('error',$error_message);
					}
				}
				$rules = array(
								'cancellation_policy_text' => 'required_without:shop_cancellation_policy_file',
								'shop_cancellation_policy_file' => 'required_without:cancellation_policy_text|mimes:'.str_replace(' ', '', Config::get("webshoppack.shop_cancellation_policy_allowed_extensions")).'|max:'.Config::get("webshoppack.shop_cancellation_policy_allowed_file_size"),
							);

				$message = array('shop_cancellation_policy_file.mimes' => trans('common.uploader_allow_format_err_msg'),
							'shop_cancellation_policy_file.max' => trans('common.uploader_max_file_size_err_msg'),
							'required_without' => trans('admin/cancellationpolicy.either_cancellation_text_or_file_required')
						);
				$v = Validator::make(Input::all(), $rules, $message);
				if ($v->fails())
				{
					$errors = $v->errors();
					return Redirect::action('AdminCancellationPolicyController@postIndex')->withInput()->withErrors($v)->with('error', 'Enter group name');
				}
				else
				{
					if (Input::hasFile('shop_cancellation_policy_file'))
					{
						$file = Input::file('shop_cancellation_policy_file');
						$file_ext = $file->getClientOriginalExtension();
						$file_name = Str::random(20);
						$destinationpath = URL::asset(Config::get("webshoppack.shop_cancellation_policy_folder"));
						$img_arr = $cancellationPolicyService->updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath);

						$cancellation_policy->setCancellationPolicyFilename($img_arr['file_name']);
						$cancellation_policy->setCancellationPolicyFiletype($img_arr['file_ext']);
						$cancellation_policy->setCancellationPolicyServerUrl($img_arr['file_server_url']);


						$cancellation_policy->setCancellationPolicyText('');

					}
					elseif(Input::has('cancellation_policy_text'))
					{
						$cancellation_policy->setCancellationPolicyText(Input::get('cancellation_policy_text'));
						$cancellation_policy->setCancellationPolicyFilename('');
						$cancellation_policy->setCancellationPolicyFiletype('');
						$cancellation_policy->setCancellationPolicyServerUrl('');
						$cancellationPolicyService->deleteShopCancellationPolicyFile();
					}

					$resp = $cancellation_policy->save();
					$respd = json_decode($resp, true);
					if ($respd['status'] == 'error') {
						$error_message = '';
						if(count($respd['error_messages']) > 0) {
							foreach($respd['error_messages'] AS $err_msg) {
								$error_message .= "<p>".$err_msg."</p>";
							}
						}
						return Redirect::action('AdminCancellationPolicyController@postIndex')->withInput()->with('error', 'Enter group name');
					}
					return Redirect::action('AdminCancellationPolicyController@postIndex')->with('success_message', trans('admin/cancellationpolicy.policy_added_successfully'));
				}
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminCancellationPolicyController@postIndex')->with('error_message',$errMsg);
		}
	}

	public function getDeleteCancellationPolicy()
	{

		$this->CancellationPolicyService = new CancellationPolicyService();

		$resource_id 	= Input::get("resource_id");

		if($resource_id != "")
		{
			$delete_status = $this->CancellationPolicyService->deleteShopCancellationPolicyFile($resource_id, Config::get("webshoppack.shop_cancellation_policy_folder"));

			if($delete_status)
			{
				return Response::json(array('result' => 'success'));
			}
		}
		return Response::json(array('result' => 'error'));
	}
}