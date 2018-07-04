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
class TaxationsController extends BaseController
{

	//Taxations
	public function getIndex($user_id = null)
	{
		$err_msg = '';
		$is_search_done = 0;
		$taxationslist = array();
		try
		{
			$user_id = BasicCUtil::getLoggedUserId();
			$search_arr = array('user_id' => $user_id);
			if(Input::has('tax_name') && Input::get('tax_name')!='')
				$search_arr['tax_name'] = Input::get('tax_name');

			$taxationslist = Webshoptaxation::Taxations()->getTaxations($search_arr, 'paginate');
			if(!$taxationslist) $taxationslist = array();
		}
		catch(Exception $e)
		{
			$err_msg = $e->getMessage();
		}
		$get_common_meta_values = Cutil::getCommonMetaValues('manage-taxations');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('taxationsList', compact('taxationslist', 'error_message', 'is_search_done'));

	}
	public function getAddTaxation()
	{
		$user_id = BasicCUtil::getLoggedUserId();
		$get_common_meta_values = Cutil::getCommonMetaValues('add-taxations');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addtaxation',compact('user_id'));
	}
	public function postAddTaxation()
	{
		try
		{
			$inputs =Input::all();
			$taxatonid = Webshoptaxation::Taxations()->addTaxation($inputs);
			if($taxatonid)
			{
				return Redirect::action('TaxationsController@getIndex')->with('success_message',trans('taxation.tax_added_success'));
			}
			else{
				return Redirect::action('TaxationsController@getAddTaxation')->with('error_message',trans('common.some_problem_try_later'))->withInput();
			}
		}
		catch(Exception $e)
		{
			return Redirect::action('TaxationsController@getAddTaxation')->with('error_message',$e->getMessage())->withInput();
		}
	}
	public function getUpdateTaxation($taxation_id = 0)
	{
		if(is_null($taxation_id) || $taxation_id <=0)
			return Redirect::action('TaxationsController@getIndex')->with('error_message',trans('taxation.select_valid_taxation'));
		$user_id = BasicCUtil::getLoggedUserId();
		$taxation_det = Webshoptaxation::Taxations()->getTaxations(array('id'=>$taxation_id, 'user_id' => $user_id), 'first');
		if(!$taxation_det)
			return Redirect::action('TaxationsController@getIndex')->with('error_message',trans('taxation.select_valid_taxation'));
		$get_common_meta_values = Cutil::getCommonMetaValues('update-taxations');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('updatetaxation',compact('user_id','taxation_det','taxation_id'));
	}
	public function postUpdateTaxation($taxation_id = 0)
	{
		try
		{
			if($taxation_id > 0)
			{
				$inputs = Input::all();

				$taxatonid = Webshoptaxation::Taxations()->updateTaxation($taxation_id, $inputs);
				if($taxatonid){
					return Redirect::action('TaxationsController@getIndex')->with('success_message', trans('taxation.tax_updated_success'));
				}
				else{
					return Redirect::action('TaxationsController@getUpdateTaxation',$taxation_id)->with('error_message',trans('taxation.some_problem_in_updating_taxation'))->withInput();
				}
			}
			else{
				return Redirect::action('TaxationsController@getUpdateTaxation',$taxation_id)->with('error_message',trans('common.some_problem_try_later'))->withInput();
			}
		}
		catch(Exception $e)
		{
			return Redirect::action('TaxationsController@getUpdateTaxation',$taxation_id)->with('error_message',$e->getMessage())->withInput();
		}
	}

	public function postDeleteTaxations()
	{
		try
		{
			$taxation_id = Input::get('taxation_id');
			if($taxation_id > 0)
			{
				$taxaton_id = Webshoptaxation::Taxations()->deleteTaxation($taxation_id);
				if($taxaton_id)
				{
					return Redirect::action('TaxationsController@getIndex')->with('success_message', trans('taxation.tax_deleted_success'));
				}
				else
				{
					return Redirect::action('TaxationsController@getIndex')->with('error_message',trans('common.some_problem_try_later'));
				}
			}
			else
				return Redirect::action('TaxationsController@getIndex')->with('error_message',trans('taxation.select_valid_taxation'));
		}
		catch(Exception $e)
		{
			return Redirect::action('TaxationsController@getIndex')->with('error_message',$e->getMessage());
		}
	}

}