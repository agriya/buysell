<?php namespace App\Plugins\Deals\Controllers;
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
use CUtil, BasicCUtil, URL, DB, Lang, View, Input, Validator, Products ;
use Session, Redirect, BaseController;

class AdminDealsController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->deal_service = new \DealsService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->beforeFilter(function(){
			if(!CUtil::chkIsAllowedModule('deals'))
	    	{
				return Redirect::to('/admin');
			}
		});
	}

	public function getManageDeals()
	{

		$deal_list = $d_arr = array();
		$error_message = '';
		$deal_serviceobj = $this->deal_service;
		$inputs = Input::all();
		// Fetch purchased count
		$purchased_count = $deal_serviceobj->getDealPurchasedCount();

		$deal_list = $deal_serviceobj->getAllDealsAdmin($inputs);

		$d_arr['deal_status_arr'] = array(	'to_activate'	=> Lang::get('deals::deals.DEAL_STATUS_TO_ACTIVATE'),
											'active'		=> Lang::get('deals::deals.DEAL_STATUS_ACTIVE'),
											'deactivated'	=> Lang::get('deals::deals.DEAL_STATUS_DEACTIVATED'),
											'closed'		=> Lang::get('deals::deals.DEAL_STATUS_CLOSED'),
											'expired'		=> Lang::get('deals::deals.DEAL_STATUS_EXPIRED'));

		$actions = array(''=> trans('common.select'),'activate' => Lang::get('deals::deals.activate_lbl'), 'de-activate' => Lang::get('deals::deals.deactivate_lbl'), 'unfeature' => Lang::get('deals::deals.remove_featured'));
		$meta_title = Lang::get('deals::deals.meta_manage_deal');
		$this->header->setMetaTitle($meta_title);
		return View::make('deals::admin.manageDeals', compact('deal_list', 'purchased_count', 'deal_serviceobj', 'd_arr', 'actions'));
	}

	public function postManageDeals()
	{
		$inputs = Input::all();
		$deal_serviceobj = $this->deal_service;
		if(isset($inputs['list_action']) && $inputs['list_action'] != '')
		{
			$deal_action = (isset($inputs['list_action']) && $inputs['list_action'] != '') ? strtolower($inputs['list_action']) : "";
			$deal_id = (isset($inputs['ident']) && $inputs['ident'] != '') ? $inputs['ident'] : 0;
			if($deal_id && $deal_action != '')
			{
				switch($deal_action)
				{
					case 'unfeature':	//	 Deal featured status is changed
						if($deal_serviceobj->unFeatureDealByAdmin($deal_id))
						{
							Session::flash('success_message', Lang::get('deals::deals.unfeatured_success_msg'));
						}
						break;

					case 'close':
						if($deal_serviceobj->closeDealByAdmin($deal_id))
						{
							Session::flash('success_message', Lang::get('deals::deals.deal_closed_success_msg'));
						}
						break;
				}
			}
		}

		if(isset($inputs['action']) && $inputs['action'] != '')
		{
			$list_action = (isset($inputs['action']) && $inputs['action'] != '') ? strtolower($inputs['action']) : "";
			switch($list_action)
			{
				case 'activate':
					# Deal status is changed as Active
					if($deal_serviceobj->changeDealStatusByAdmin($inputs, 'active'))
					{
						Session::flash('success_message', Lang::get('deals::deals.activate_success_msg'));
					}
					break;

				case 'de-activate':
					# Deal status is changed as Active
					if($deal_serviceobj->changeDealStatusByAdmin($inputs, 'de-activate'))
					{
						Session::flash('success_message', Lang::get('deals::deals.deactivate_success_msg'));
					}
					break;
			}
		}
		return Redirect::to('admin/deals/manage-deals');
	}

	public function getManageFeaturedDeals()
	{
		$deal_list = $d_arr = array();
		$error_message = '';
		$deal_serviceobj = $this->deal_service;
		$inputs = Input::all();
		$deal_list = $deal_serviceobj->getAllFeaturedDealsAdmin();
		$actions = array(''=> trans('common.select'),'unfeature' => Lang::get('deals::deals.remove_featured'));
		$meta_title = Lang::get('deals::deals.meta_manage_featureddeal');
		$this->header->setMetaTitle($meta_title);
		return View::make('deals::admin.manageFeaturedDeals', compact('deal_list', 'error_message', 'deal_serviceobj', 'd_arr', 'actions'));
	}

	public function postManageFeaturedDeals()
	{
		$inputs = Input::all();
		$message = array();

		$deal_serviceobj = $this->deal_service;
		$list_action = (isset($inputs['action']) && $inputs['action'] != '') ? strtolower($inputs['action']) : "";

		if(isset($inputs['set_featured']) && $inputs['set_featured'] == 'set_featured')
		{
			if($inputs['date_featured_from']!='')
			{
				$inputs['date_featured_from_lbl'] = $inputs['date_featured_from'];
				$inputs['date_featured_from'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_from']), 'Y-m-d');

			}
			if($inputs['date_featured_to']!='')
			{
				$inputs['date_featured_to_lbl'] = $inputs['date_featured_to'];
				$inputs['date_featured_to'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_to']), 'Y-m-d');
			}

			$rules['date_featured_from'] = 	'required|after:'.date('Y-m-d',strtotime("-1 days"));
			$rules['date_featured_to'] 	= 	'required|after:'.date('Y-m-d',strtotime($inputs['date_featured_from'].' -1 days'));

			$deal_id = isset($inputs['deal_id']) ? $inputs['deal_id'] : 0;
			if(!$deal_id || !$deal_serviceobj->chkIsValidDealId($deal_id))
			{
				Session::flash('error_message', Lang::get('deals::deals.invalid_deal_id_msg'));
				return Redirect::back()->withInput();
			}

			if(!$deal_serviceobj->chkIsValidFeaturedDate($inputs['deal_id'], $inputs['date_featured_from'], $inputs['date_featured_to']))
			{
				Session::flash('error_message', Lang::get('deals::deals.already_feature_avail_err_msg'));
				return Redirect::back()->withInput();
			}
			$validator = Validator::make($inputs, $rules, $message);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			else
			{
				$admin_comment 			= 'Deal set featured by admin.';
				$admin_comment 			.= '#@@# For the deal ID '. $inputs['deal_id'];
				$admin_comment 			.= '#@@# Featured from '.CUtil::FMTDate($inputs['date_featured_from'], 'Y-m-d', '');
				$admin_comment 			.= '#@@# Featured upto '.CUtil::FMTDate($inputs['date_featured_to'], 'Y-m-d', '');
				$admin_comment 			.= '#@@# Set featured on '.date("F j, Y, g:i a");
				$deal_details = $deal_serviceobj->getDealsDetails($inputs['deal_id']);

				$data_arr = array();
				$data_arr['deal_id'] 			= $inputs['deal_id'];
				$data_arr['date_featured_from'] = $inputs['date_featured_from'];
				$data_arr['date_featured_to'] 	= $inputs['date_featured_to'];
				$data_arr['admin_comment'] 		= $admin_comment;
				$data_arr['user_id'] 			= $deal_details->user_id;
				$data_arr['request_status'] 	= 'approved';

				if($deal_serviceobj->updateFeaturedDealByAdmin($data_arr))
				{
					$success_message = Lang::get('deals::deals.set_featured_success_msg');
					Session::flash('success_message', $success_message);
				}
			}
		}
		elseif($list_action == 'unfeature')
		{
			if($deal_serviceobj->removeFeaturedDealsByAdmin($inputs))
			{
				Session::flash('success_message', Lang::get('deals::deals.unfeatured_success_msg'));
			}
		}
		return Redirect::to('admin/deals/manage-featured-deals');
	}

	public function getPurchasedDetails($deal_id=0)
	{
		$d_arr = array();
		$d_arr['deal_id'] = $deal_id;
		$error_message = '';
		$deal_serviceobj = $this->deal_service;
		$purchase_details = array();
		$purchase_details = $deal_serviceobj->getDealPurchasedDetails($deal_id, 0, 'paginate', 10);
		return View::make('deals::admin.dealPurchasedDetails', compact('purchase_details', 'error_message', 'deal_serviceobj', 'd_arr'));
	}

	public function postPurchasedDetails()
	{
		$inputs = Input::all();

		$deal_id = (isset($inputs['deal_id'])) ? $inputs['deal_id'] : 0;
		$order_id = (isset($inputs['order_id'])) ? $inputs['order_id'] : 0;
		$item_id = (isset($inputs['item_id'])) ? $inputs['item_id'] : 0;

		if(!$deal_id || !$order_id || !$item_id)
			return Redirect::to('admin/deals/purchased-deals', array('deal_id', $deal_id));

		$deal_serviceobj = $this->deal_service;
		if(isset($inputs['list_action']) && $inputs['list_action'] == 'refund' )
		{
			$data_arr['deal_id'] = $deal_id;
			$data_arr['order_id'] = $order_id;
			$data_arr['item_id'] = $item_id;
			$deal_serviceobj->updateDealPurchasedRefundStatus($data_arr);
			Session::flash('success_message', Lang::get('deals::deals.set_refunded_success_msg'));
		}
		$redirectUrl = Url::to('admin/deals/purchased-details', array('deal_id' => $deal_id));
		return Redirect::to($redirectUrl);
	}

	public function getManageFeaturedRequests($request_type='all')
	{
		$deal_list = $d_arr = array();
		$error_message = '';
		$deal_serviceobj = $this->deal_service;
		$inputs = Input::all();
		$deal_list = $deal_serviceobj->getAllFeaturedDealsRequestsAdmin($request_type);
		$actions = array(''=> trans('common.select'),'unfeature' => 'Remove featured');
		$meta_title = Lang::get('deals::deals.meta_manage_featuredrequest');
		switch($request_type)
		{
			case 'pending':
				$meta_title = str_replace("VAR_TYPE", Lang::get('deals::deals.featured_req_pending_for_approval'), $meta_title);
				break;
			case 'approved':
				$meta_title = str_replace("VAR_TYPE", Lang::get('deals::deals.featured_req_approved'), $meta_title);
				break;
			case 'unapproved':
				$meta_title = str_replace("VAR_TYPE", Lang::get('deals::deals.featured_req_un_approved'), $meta_title);
				break;
			default:
				$meta_title = str_replace("VAR_TYPE", "", $meta_title);
		}
		$this->header->setMetaTitle($meta_title);
		return View::make('deals::admin.manageFeaturedRequests', compact('deal_list', 'error_message', 'deal_serviceobj', 'd_arr', 'actions'));
	}


	public function getViewFeaturedRequest($deal_id=0)
	{
		$deal_details = $d_arr = array();
		$deal_serviceobj = $this->deal_service;
		$deal_details = $deal_serviceobj->getFeaturedDealRequestDetailsByAdmin($deal_id);
		$error_message = ($deal_details && COUNT($deal_details) > 0) ? "" : Lang::get('deals::deals.invalid_acces_err_msg');
		return View::make('deals::admin.viewFeaturedRequest', compact('deal_details', 'error_message', 'deal_serviceobj', 'd_arr'));
	}


	public function getSetFeaturedRequest($deal_id=0)
	{
		$deal_details = $d_arr = array();
		$deal_serviceobj = $this->deal_service;
		$deal_details = $deal_serviceobj->getDealsDetails($deal_id);
		$error_message = ($deal_details && COUNT($deal_details) > 0) ? "" : Lang::get('deals::deals.invalid_acces_err_msg');

		return View::make('deals::admin.addFeaturedDeal', compact('deal_details', 'error_message', 'deal_serviceobj', 'd_arr'));
	}

	public function postSetFeaturedRequest()
	{
		$inputs = Input::all();
		$deal_serviceobj = $this->deal_service;
		$message = array();

		if(isset($inputs['approve_featured']) && $inputs['approve_featured'] == 'approve_featured')
		{
			if($inputs['date_featured_from']!='')
			{
				$inputs['date_featured_from_lbl'] = $inputs['date_featured_from'];
				$inputs['date_featured_from'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_from']), 'Y-m-d');

			}
			if($inputs['date_featured_to']!='')
			{
				$inputs['date_featured_to_lbl'] = $inputs['date_featured_to'];
				$inputs['date_featured_to'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_to']), 'Y-m-d');
			}

			$rules['date_featured_from'] = 	'required|after:'.date('Y-m-d',strtotime("-1 days"));
			$rules['date_featured_to'] 	= 	'required|after:'.date('Y-m-d',strtotime($inputs['date_featured_from'].' -1 days'));

			if(!$deal_serviceobj->chkIsValidFeaturedDate($inputs['deal_id'], $inputs['date_featured_from'], $inputs['date_featured_to']))
			{
				Session::flash('error_message', Lang::get('deals::deals.already_feature_avail_err_msg'));
				return Redirect::back()->withInput();
			}
			$validator = Validator::make($inputs, $rules, $message);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			else
			{
				$admin_comment 			= 'Deal set featured by admin.';
				$admin_comment 			.= '#@@# For the deal ID '. $inputs['deal_id'];
				$admin_comment 			.= '#@@# Featured from '.CUtil::FMTDate($inputs['date_featured_from'], 'Y-m-d', '');
				$admin_comment 			.= '#@@# Featured upto '.CUtil::FMTDate($inputs['date_featured_to'], 'Y-m-d', '');
				$admin_comment 			.= '#@@# Set featured on '.date("F j, Y, g:i a");

				$data_arr = array();
				$data_arr['deal_id'] 			= $inputs['deal_id'];
				$data_arr['date_featured_from'] = $inputs['date_featured_from'];
				$data_arr['date_featured_to'] 	= $inputs['date_featured_to'];
				$data_arr['admin_comment'] 		= $admin_comment;
				$data_arr['user_id'] 			= $inputs['user_id'];
				$data_arr['request_status'] 	= 'approved';

				if($deal_serviceobj->updateFeaturedDealByAdmin($data_arr))
				{
					$success_message = Lang::get('deals::deals.set_featured_success_msg');
					return View::make('deals::admin.setFeaturedRequest', compact('success_message'));
				}
			}
		}
	}


	public function getApproveFeaturedRequest($deal_id=0)
	{
		$deal_details = $d_arr = array();
		$deal_serviceobj = $this->deal_service;
		$deal_details = $deal_serviceobj->getFeaturedDealRequestDetailsByAdmin($deal_id, 'update');
		$error_message = ($deal_details && COUNT($deal_details) > 0) ? "" : Lang::get('deals::deals.invalid_acces_err_msg');
		return View::make('deals::admin.setFeaturedRequest', compact('deal_details', 'error_message', 'deal_serviceobj', 'd_arr'));
	}

	public function postApproveFeaturedRequest()
	{
		$inputs = Input::all();
		$deal_serviceobj = $this->deal_service;

		$message = array();
		if(isset($inputs['disapprove']) && $inputs['disapprove'] == 'disapprove')
		{

			$deal_id = $inputs['deal_id'];
			$admin_comment 			= 'Deal featured request rejected by admin.' ;
			$admin_comment 			.= '#@@# For the deal ID '. $deal_id;
			$admin_comment 			.= '#@@# Request updated on '.date("F j, Y, g:i a");
			if(isset($inputs['admin_comment'])  && $inputs['admin_comment'] != '')
				$admin_comment		.= '#@@# Admin comment: #@@#'. $inputs['admin_comment'];

			$data_arr = array();
			$data_arr['deal_id'] 		= $deal_id;
			$data_arr['admin_comment'] 	= $admin_comment;
			$data_arr['request_status'] = 'un_approved';
			$data_arr['deal_featured_days'] = $inputs['deal_featured_days'];
			$data_arr['requested_user'] = $inputs['user_id'];
			if($deal_serviceobj->unapproveFeatureRequest($data_arr))
			{
				$success_message = Lang::get('deals::deals.disapproved_success_msg');
				return View::make('deals::admin.setFeaturedRequest', compact('success_message'));
			}
		}
		elseif(isset($inputs['approve_featured']) && $inputs['approve_featured'] == 'approve_featured')
		{
			if($inputs['date_featured_from']!='')
			{
				$inputs['date_featured_from_lbl'] = $inputs['date_featured_from'];
				$inputs['date_featured_from'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_from']), 'Y-m-d');

			}
			if($inputs['date_featured_to']!='')
			{
				$inputs['date_featured_to_lbl'] = $inputs['date_featured_to'];
				$inputs['date_featured_to'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_to']), 'Y-m-d');
			}

			$rules['date_featured_from'] = 	'required|after:'.date('Y-m-d',strtotime("-1 days"));
			$rules['date_featured_to'] 	= 	'required|after:'.date('Y-m-d',strtotime($inputs['date_featured_from'].' -1 days'));

			if(!$deal_serviceobj->chkIsValidFeaturedDate($inputs['deal_id'], $inputs['date_featured_from'], $inputs['date_featured_to']))
			{
				Session::flash('error_message', Lang::get('deals::deals.already_feature_avail_err_msg'));
				return Redirect::back()->withInput();
			}
			$validator = Validator::make($inputs, $rules, $message);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			else
			{
				$admin_comment 			= 'Deal featured request approved by admin.';
				$admin_comment 			.= '#@@# For the deal ID '. $inputs['deal_id'];
				$admin_comment 			.= '#@@# Featured from '.CUtil::FMTDate($inputs['date_featured_from'], 'Y-m-d', '');
				$admin_comment 			.= '#@@# Featured upto '.CUtil::FMTDate($inputs['date_featured_to'], 'Y-m-d', '');
				$admin_comment 			.= '#@@# Set featured on '.date("F j, Y, g:i a");
				if(isset($inputs['admin_comment']) && $inputs['admin_comment'] != '')
					$admin_comment		.= '#@@# Admin comment = '. $inputs['admin_comment'];

				$data_arr = array();
				$data_arr['deal_id'] 			= $inputs['deal_id'];
				$data_arr['date_featured_from'] = $inputs['date_featured_from'];
				$data_arr['date_featured_to'] 	= $inputs['date_featured_to'];
				$data_arr['admin_comment'] 		= $admin_comment;
				$data_arr['user_id'] 			= $inputs['user_id'];
				$data_arr['request_status'] 	= 'approved';

				if($deal_serviceobj->updateFeaturedDealByAdmin($data_arr))
				{
					// Check alloted time was lower member requested days then return the fund to user a/c balance.
					if(\Config::has('plugin.deal_listing_fee') && \Config::get('plugin.deal_listing_fee') > 0)
					{
						$from	= date('d-m-Y', strtotime($inputs['date_featured_from']));
						$to		= date('d-m-Y', strtotime($inputs['date_featured_to']));
						$alloted_days=((strtotime($to) - strtotime($from))/ (60 * 60 * 24)) + 1; //it will count no. of days

						if($alloted_days < $inputs['deal_featured_days'])
						{
							$deviation_days = $inputs['deal_featured_days'] - $alloted_days;
							$input_det_arr = array();
							$input_det_arr['requested_days'] 	= $inputs['deal_featured_days'];
							$input_det_arr['approved_days'] 	= $alloted_days;
							$input_det_arr['requested_user'] 	= $inputs['user_id'];
							$input_det_arr['deviation_days'] 	= $deviation_days;
							$input_det_arr['transaction_type'] 	= 'Credit';
							$input_det_arr['deal_id'] 	= $inputs['deal_id'];
							$deal_id = $inputs['deal_id'];
							$dealDet = $deal_serviceobj->fetchDealDetailsById($data_arr['deal_id']);
							$txn_comment = str_replace('VAR_DEAL', $dealDet['deal_title'], Lang::get('deals::deals.featured_req_approve_refund_msg'));
							$txn_comment = str_replace('VAR_DAYS', $alloted_days, $txn_comment);
							$txn_comment = str_replace('VAR_DIFF_DAYS', $deviation_days, $txn_comment);

							$input_det_arr['transaction_comment'] = $txn_comment;
							$deal_serviceobj->revertFundToUserAccountBalance($input_det_arr);
						}
					}

					$success_message = Lang::get('deals::deals.set_featured_success_msg');
					return View::make('deals::admin.setFeaturedRequest', compact('success_message'));
				}
			}
		}
	}
}