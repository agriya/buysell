<?php namespace App\Plugins\Variations\Controllers;
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
use CUtil, BasicCUtil, URL, DB, Lang, View, Input, Validator;
use Session, Redirect;
class VariationsGroupController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->beforeFilter(function(){
			if(!CUtil::chkIsAllowedModule('variations')){
				return Redirect::to('/');
			}
		});
		$this->variations_service = new \VariationsService();
		$this->beforeFilter(function(){
			if ($this->logged_user_id == 0) {
				return Redirect::to('/users/login');
			}
		}, array('except' => array('')));
	}

	public function getIndex()
	{
		$variations_group = DB::table('variation_group')
							->Select('variation_group_id', 'variation_group_name', 'short_description', 'user_id', 'date_added')
							->whereRaw('user_id = ?', array($this->logged_user_id))
							->paginate(10);
		$action_list = array('' => Lang::get('variations::variations.select'), 'delete' => Lang::get('variations::variations.delete'));
		$variations_obj = $this;
		return View::make('variations::variationsGroup', compact('variations_group', 'action_list', 'variations_obj'));
	}

	public function getAddVariationsGroup($variation_group_id = 0)
	{
		$grpExist = $this->variations_service->chkIsUserAddedVariation($this->logged_user_id);
		if(!$grpExist)
		{
			return Redirect::to('variations/add-variation')->with('error', Lang::get('variations::variations.variation_none_err_msg'));
		}
		$action = 'add';
		if($variation_group_id <= 0)
		{
			$variation_group_details = $attributes_arr = $assigned_variation_arr = array();
		}
		else
		{
			$variation_group_details = $this->variations_service->getVariationsGroupDetails($variation_group_id, $this->logged_user_id);
			if(!$variation_group_details || count($variation_group_details) <= 0)
			{
				Session::flash('error', Lang::get('variations::variations.invalid_variation_id'));
				return Redirect::to('variations/groups')->with('error_message', Lang::get('variations::variations.invalid_variation_id'));
			}
			else
			{
				if($variation_group_details['user_id'] != $this->logged_user_id)
				{
					Session::flash('error', Lang::get('variations::variations.invalid_action'));
					return Redirect::to('variations/groups')->with('error_message', Lang::get('variations::variations.invalid_action'));
				}
			}
			$action = 'edit';
			$assigned_variation_arr = $this->variations_service->fetchVariationsInGroupByGroupId($variation_group_id);
		}
		$avail_variation_arr = $this->variations_service->populateUserVariationList($this->logged_user_id);
		$logged_user_id = $this->logged_user_id;
		return View::make('variations::addVariationsGroup', compact('variation_group_details', 'logged_user_id', 'action', 'variation_group_id', 'avail_variation_arr', 'assigned_variation_arr'));
	}

	public function postAddVariationsGroup()
	{
		$inputs = Input::all();
		$action = isset($inputs['action']) ? $inputs['action'] : 'add';
		$variation_group_id = isset($inputs['variation_group_id']) ? $inputs['variation_group_id'] : 0;

		if($action == 'add')
		{
			$rules = array('variation_group_name' => 'required|unique:variation_group,variation_group_name,NULL,variation_group_id,user_id,'.$this->logged_user_id);
		}
		else
		{
			if($variation_group_id > 0)
				$rules = array('variation_group_name' => 'required|unique:variation_group,variation_group_name,'.$variation_group_id.',variation_group_id,user_id,'.$this->logged_user_id);
		}
		$rules['assigned_variation'] = 'required';
		$message = array('variation_group_name.required' =>'Required');
		$validator = Validator::make($inputs, $rules, $message);
		if ($validator->fails())
		{
			if($action == 'add')
				return Redirect::to('variations/add-group')->withInput()->withErrors($validator);
			else
				return Redirect::to('variations/add-group?variation_group_id='.$variation_group_id)->withInput()->withErrors($v);
		}
		else
		{
			if($action == 'add')
			{
				$variation_group_id = $this->variations_service->addVariationGroupEntry($inputs);
				Session::flash('success', Lang::get('variations::variations.variation_group_created_success_msg'));
			}
			else
			{
				$variation_group_id = $this->variations_service->updateVariationGroupEntry($inputs);
				Session::flash('success', Lang::get('variations::variations.variation_group_updated_success_msg'));
			}
			return Redirect::to('variations/groups');
		}
	}

	public function postGroupListAction()
	{
		$variation_group_action = Input::has('variation_group_action') ? Input::get('variation_group_action') : 'delete';
		$selected_variation_group_id = Input::has('selected_variation_group_id') ? Input::get('selected_variation_group_id') : 0;
		$group_ids = explode(",", $selected_variation_group_id);
		if(!empty($group_ids))
		{
			$this->variations_service->deleteSelectedGroups($group_ids);
			Session::flash('success', Lang::get('variations::variations.selected_variations_group_deleted_msg'));
			return;
		}
		else
		{
			Session::flash('error', Lang::get('variations::variations.please_select_variation_group'));
			return;
		}
	}

	public function getGroupAction()
	{
		$success_msg = "";
		if(Input::has('variation_group_id') && Input::has('action'))
		{
			$selected_variation_group_id = Input::get('variation_group_id');
			$variation_group_ids = explode(",", $selected_variation_group_id);
			$action = Input::get('action');
			switch($action)
			{
				case 'delete':
					$this->variations_service->deleteSelectedGroups($variation_group_ids);
					$success_msg = Lang::get('variations::variations.variation_group_deleted_success_msg');
					break;
				default;
					$success_msg = Lang::get('variations::variations.invalid_action');
					break;
			}
		}
		Session::flash('success', $success_msg);
		return Redirect::to('variations/groups');
	}

	//Service functions start
	public function getVariationsInGroupByGroupIdAsString($group_id)
	{
		$variations_values = '';
		$variations = $this->variations_service->fetchVariationsInGroupByGroupId($group_id);
		$variations_values = (isset($variations) && !empty($variations)) ? implode(',', $variations) : "";
		return $variations_values;
	}
}