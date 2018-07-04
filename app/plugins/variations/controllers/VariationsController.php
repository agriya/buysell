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
use Session, Redirect, BaseController;
class VariationsController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->beforeFilter(function(){
			if(!CUtil::chkIsAllowedModule('variations'))
	    	{
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
		$variations = DB::table('variation')
							->Select('variation_id', 'name', 'user_id', 'date_added')
							->whereRaw('user_id = ?', array($this->logged_user_id))
							->paginate(10);
		$action_list = array('' => Lang::get('variations::variations.select'), 'delete' => Lang::get('variations::variations.delete'));
		$variations_obj = $this;
		return View::make('variations::index', compact('variations', 'action_list', 'variations_obj'));
	}

	public function getAddVariations($variation_id = 0)
	{
		Session::forget('error');
		$attribs = array();
		$logged_user_id = $this->logged_user_id;
		$action = 'add';
		if($variation_id <= 0)
		{
			$variation_details = $attributes_arr = array();
		}
		else
		{
			$variation_details = $this->variations_service->getVariationsDetails($variation_id, $logged_user_id);
			if(!$variation_details || count($variation_details) <= 0)
			{
				Session::flash('error', Lang::get('variations::variations.invalid_variation_id'));
				return Redirect::to('variations');
			}
			else
			{
				if($variation_details['user_id'] != $logged_user_id)
				{
					Session::flash('error', Lang::get('variations::variations.invalid_action'));
					return Redirect::to('variations');
				}
			}
			$action = 'edit';
			$attributes_arr = $this->variations_service->getVariationAttributesList($variation_id);
		}
		// To handle input value when error occurs
		if(Session::has('attributes_arr'))
		{
			$attribs	=   Session::get('attributes_arr');
			Session::forget('attributes_arr');
			if(Session::has('label_field_empty')){
				if(Session::get('label_field_empty') == 0)
					$error_message = Lang::get('variations::variations.variation_option_spl_char_err_msg');
				if(Session::get('label_field_empty') == 1)
					$error_message = Lang::get('variations::variations.variation_option_key_err_msg');
				Session::forget('label_field_empty');
			}
			Session::flash('error', $error_message);
		}
		return View::make('variations::addVariation', compact('variation_details', 'logged_user_id', 'action', 'variation_id', 'attributes_arr', 'attribs'));
	}

	public function postAddVariations()
	{
		$inputs = Input::all();
		$action = isset($inputs['action']) ? $inputs['action'] : 'add';
		$variation_id = isset($inputs['variation_id']) ? $inputs['variation_id'] : 0;
		if($action == 'add')
		{
			$rules = array('name' => 'required|unique:variation,name,NULL,variation_id,user_id,'.$this->logged_user_id);
		}
		else
		{
			if($variation_id > 0)
				$rules = array('name' => 'required|unique:variation,name,'.$variation_id.',variation_id,user_id,'.$this->logged_user_id);
		}
		$message = array('name.required' =>'Required', 'name.unique' =>Lang::get('variations::variations.variation_name_already_exist_err'));
		if(isset($inputs['option_key']) && COUNT($inputs['option_key']) > 0)
		{
			foreach($inputs['option_key'] AS $optKey => $opts)
			{
				$rules['option_key.'.$optKey] = 'Required|regex:'."/^([-a-z0-9_ ])+$/i";
				$rules['option_label.'.$optKey] = 'Required';
				//$rules['option_label.'.$optKey] = 'regex:'."/^([-a-z0-9_ ])+$/i";
			}
		}
		$validator = Validator::make($inputs, $rules, $message);
		if ($validator->fails())
		{ 
			$attributes_arr = array();
			if(isset($inputs['option_key']) && COUNT($inputs['option_key']) > 0)
			{
				$label_field_empty = 0;
				foreach($inputs['option_key'] AS $recKey => $rec)
				{
					if(trim($rec) != "")
					{
						$attributes_arr[$recKey]['option_key']  = $rec;
						$attributes_arr[$recKey]['option_label'] = isset($inputs['option_label'][$recKey]) ? $inputs['option_label'][$recKey] : "";
						if($attributes_arr[$recKey]['option_label'] == "")
							$label_field_empty = 1;
					}
				}
			}
			Session::put('attributes_arr', $attributes_arr);
			if( ( COUNT($attributes_arr) >= 1 || COUNT($attributes_arr) < 1 ) && $label_field_empty == 1)
			{
				Session::put('label_field_empty', $label_field_empty); // null label or null key
			}else if( ( COUNT($attributes_arr) >= 1 || COUNT($attributes_arr) < 1 ) && $label_field_empty == 0){
				Session::put('label_field_empty', $label_field_empty); // empty key
			}
			if($action == 'add')
				return Redirect::to('variations/add-variation')->withInput(Input::except(array('option_key', 'option_label')))->withErrors($validator);
			else
				return Redirect::to('variations/add-variation/'.$variation_id)->withInput(Input::except(array('option_key', 'option_label')))->withErrors($validator);
		}
		else
		{
			if($action == 'add')
			{
				$variation_id = $this->variations_service->addVariationEntry($inputs);
				Session::flash('success', Lang::get('variations::variations.variation_added_success_msg'));
			}
			else
			{
				$variation_id = $this->variations_service->updateVariationEntry($inputs);
				Session::flash('success', Lang::get('variations::variations.variation_updated_success_msg'));
			}
			return Redirect::to('variations');
		}
	}

	public function postVariationsListAction()
	{
		$variation_action = Input::has('variation_action') ? Input::get('variation_action') : 'delete';
		$selected_variation_id = Input::has('selected_variation_id') ? Input::get('selected_variation_id') : 0;
		$variation_ids = explode(",", $selected_variation_id);
		if(!empty($variation_ids))
		{
			$this->variations_service->deleteSelectedItems($variation_ids);
			Session::flash('success', Lang::get('variations::variations.selected_variations_deleted_msg'));
			return;
		}
		else
		{
			Session::flash('error', Lang::get('variations::variations.please_select_variation'));
			return;
		}
	}

	public function getVariationsAction()
	{
		$success_msg = "";
		if(Input::has('variation_id') && Input::has('action'))
		{
			$selected_variation_id = Input::get('variation_id');
			$variation_ids = explode(",", $selected_variation_id);
			$action = Input::get('action');
			switch($action)
			{
				case 'delete':
					$this->variations_service->deleteSelectedItems($variation_ids);
					$success_msg = Lang::get('variations::variations.variation_deleted_success_msg');
					break;

				default;
					$success_msg =  Lang::get('variations::variations.invalid_action');
					break;
			}
		}
		Session::flash('success', $success_msg);
		return Redirect::to('variations');
	}

	//Service functions start
	public function getVariationAttributesValues($variation_id)
	{
		$attr_values = '';
		$attributes = $this->variations_service->getVariationAttributesList($variation_id);
		if(count($attributes) > 0) {
			foreach($attributes as $attr)
			{
				$attr_values = ($attr_values == '') ? $attr['label'] : $attr_values.', '.$attr['label'];
			}
		}
		return $attr_values;
	}
	
	public function getVartionStock($product_id){
		$matrix_data = DB::table('item_variation_details')
							->select('item_variation_details.matrix_id', 'variation_attributes.label', 'item_variation_details.stock')
							->leftjoin('item_var_matrix_attributes', 'item_variation_details.matrix_id', '=' , 'item_var_matrix_attributes.matrix_id')
							->leftjoin('variation_attributes', 'item_var_matrix_attributes.attribute_id', '=' , 'variation_attributes.attribute_id')
							->whereRaw('item_variation_details.item_id = ?', array($product_id))
							->get();	
		return View::make('variations::variationStockDetails', compact('matrix_data'));				
	}

}