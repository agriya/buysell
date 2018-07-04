<?php

class MissingTaxationsParamsExecption extends Exception {}

class TaxationsService
{
	public function addTaxation($inputs = array(), $admin_mode = false)
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($inputs) || !is_array($inputs))
				throw new MissingTaxationsParamsExecption(trans('taxations.input_array_empty'));

			$rules = array(
				'user_id' 	=> 'required|numeric',
				'tax_name' 	=> 'required',
				'tax_slug'	=> 'sometimes|required|alpha_dash|Unique:taxations,tax_slug,NULL,id,user_id,'.$inputs['user_id'].',deleted_at,NULL',
				'tax_fee' 	=> 'required|numeric|min:0',
				'fee_type'	=> 'required|in:percentage,flat',
			);
			/*if($admin_mode) {
				$rules['tax_slug'] = 'sometimes|required|alpha_dash|Unique:taxations,tax_slug,NULL,id,deleted_at,NULL';
			}*/

			Log::info('validateion rules ====================>');
			Log::info(print_r($rules,1));
			$valid_keys = array(
				'user_id' 			=> '',
				'tax_name' 			=> '',
				'tax_description' 	=> '',
				'tax_slug'			=> '',
				'tax_fee' 			=> '',
				'fee_type'			=> '',
			);
			$inputs = array_intersect_key($inputs, $valid_keys);
			$inputs = $inputs+$valid_keys;
			if(!isset($inputs['tax_slug']) || (isset($inputs['tax_slug']) && $inputs['tax_slug'] == ''))
				$inputs['tax_slug'] = $this->generateSlug($inputs['tax_name']);

			//Use this if you need to generate a unique slug in taxation table
			//$this->generateTaxationSlug($inputs['tax_name'], $inputs['user_id']);

			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$taxations = Taxations::create($inputs);
				return $taxations->id;
			}
			else
			{
				throw new MissingTaxationsParamsExecption($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}

	public function getTaxations($options = array(), $return_type = 'all', $additional_options = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($options) || !is_array($options) || (is_array($options) && (!isset($options['id']) && !isset($options['user_id']))))
				throw new MissingTaxationsParamsExecption(trans('taxations.options_invalid'));

			if(is_null($return_type)){$return_type = 'all';}
			if(!in_array($return_type, array('all','list', 'first', 'paginate')))
				throw new MissingTaxationsParamsExecption(trans('taxations.return_type_invalid'));


			$orderby = (isset($additional_options['order_by']) && in_array($additional_options['order_by'],array('id','tax_name')))?$additional_options['order_by']:'id';
			$sortby = (isset($additional_options['sort_type']) && in_array($additional_options['sort_type'],array('asc','desc')))?$additional_options['sort_type']:'asc';

			$fetch_with_trashed = (isset($additional_options['include_deleted']) && $additional_options['include_deleted']==true)?true:false;
			//Create model object for taxations
			if($fetch_with_trashed)
				$taxations = Taxations::withTrashed()->orderby($orderby,$sortby);
			else
				$taxations = Taxations::orderby($orderby,$sortby);

			//Conditions based on input
			if(isset($options['id']))
			{
				if(is_array($options['id']) && !empty($options['id']))
					$taxations->whereIn('id',$options['id']);
				elseif($options['id'] > 0)
					$taxations->where('id','=',$options['id']);
			}
			//echo "from package<pre>";print_r($options['user_id']);echo "</pre>";exit;
			if(isset($options['user_id']))
			{
				if(is_array($options['user_id']) && !empty($options['user_id']))
					$taxations->whereIn('user_id',$options['user_id']);
				else
					$taxations->where('user_id','=',$options['user_id']);
				//if($options['user_id'] > 0)
			}

			if(isset($options['tax_name']) && $options['tax_name'] != '')
				$taxations->where('tax_name','like','%'.$options['tax_name'].'%');

			$limit = (isset($additional_options['limit']) && $additional_options['limit']!='')?$additional_options['limit']:'';

			//Check the return type and get hte list
			if($return_type == 'list')
				$taxationslist = $taxations->lists('tax_name','id');
			elseif($return_type == 'first')
				$taxationslist = $taxations->first();
			elseif($return_type == 'paginate')
				$taxationslist = $taxations->paginate($limit);
			else
				$taxationslist = $taxations->get();

			//Return the list
			if(count($taxationslist) > 0)
				return $taxationslist;
			else
				return false;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}


	public function updateTaxation($id = null, $inputs = array(), $admin_mode = false)
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($id))
				throw new MissingTaxationsParamsExecption(trans('taxations.taxation_id_empty'));

			$taxations_det = $this->getTaxations(array('id' => $id), 'first');
			if(!$taxations_det)
				throw new MissingTaxationsParamsExecption(trans('taxations.taxation_id_not_avail'));

			$user_id = $taxations_det->user_id;
			if(!$user_id)
				throw new MissingTaxationsParamsExecption(trans('taxations.something_went_wrong'));



			$rules = array(
				'tax_name' 	=> 'sometimes|required',
				'tax_slug'	=> 'sometimes|required|alpha_dash|Unique:taxations,tax_slug,'.$id.',id,user_id,'.$user_id.',deleted_at,NULL',
				'tax_fee' 	=> 'sometimes|required|numeric|min:0',
				'fee_type'	=> 'sometimes|required|in:percentage,flat',
			);
			/*if($admin_mode)
				$rules['tax_slug'] = 'sometimes|required|alpha_dash|Unique:taxations,tax_slug,'.$id.',id,deleted_at,NULL';*/
			$valid_keys = array(
				'tax_name' 			=> '',
				'tax_slug'			=> '',
				'tax_description' 	=> '',
				'tax_fee' 			=> '',
				'fee_type'			=> '',
			);
			$inputs = array_intersect_key($inputs, $valid_keys);
			//$inputs = $inputs+$valid_keys;
			if(!isset($inputs['tax_slug']) || (isset($inputs['tax_slug']) && $inputs['tax_slug'] == ''))
				$inputs['tax_slug'] = $this->generateSlug($inputs['tax_name']);

			//Use this if you need to generate a unique slug in taxation table
			//$this->generateTaxationSlug($inputs['tax_name'], $inputs['user_id']);

			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$affectedRows = Taxations::where('id', '=', $id)->update($inputs);
				return $affectedRows;
			}
			else
			{
				throw new MissingTaxationsParamsExecption($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}

	public function deleteTaxation($id = null, $inputs = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($id))
				throw new MissingTaxationsParamsExecption(trans('taxations.taxation_id_empty'));

			//$taxations_det = $this->getTaxations(array('id' => $id), 'first');
			//if(!$taxations_det)
			//	throw new MissingTaxationsParamsExecption(trans('taxations.taxation_id_not_avail'));


			$taxation = Taxations::find($id);//where('id', '=', $id)->softDeletes();//delete();
			$affectedRows = $taxation->delete();//softDeletes();
			return $affectedRows;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}

	public function generateSlug($name = '')
	{
		if(is_null($name) || $name == '')
			return '';
		else
			return Str::slug($name);
	}
	public function generateTaxationSlug($name = '', $user_id = '', $level=0)
	{
		if(is_null($name) || $name == '')
			return '';
		else
		{
			$slug = '';
			switch($level)
			{
				case 0:
					$slug = Str::slug($name);
					break;
				case 1:
					if($user_id != '')
						$slug = Str::slug($name.'-'.$user_id);
					break;
				case 2:
					$slug = Str::slug($name.'-'.rand(1,100000));
					break;
			}

			if($slug != '')
			{
				$input = array('tax_slug' => $slug);
				$rules = ($user_id != '')?array('tax_slug'	=> 'required|alpha_dash|Unique:taxations,tax_slug,NULL,id,user_id,'.$user_id):array('tax_slug'	=> 'required|alpha_dash|Unique:taxations,tax_slug');

				$validator = Validator::make($input,$rules);
				if($validator->passes())
					return $slug;
				else
				{
					if($level != 2)
						return $this->generateTaxationSlug($name, $user_id, $level+1);
					else
						return $this->generateTaxationSlug($name, $user_id, $level);
				}
			}
		}
	}

}