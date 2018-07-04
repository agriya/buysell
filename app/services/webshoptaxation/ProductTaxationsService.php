<?php

class MissingProductTaxationsParamsExecption extends Exception {}

class ProductTaxationsService
{
	public function addProductTaxation($inputs = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($inputs) || !is_array($inputs))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.input_array_empty'));

			if(!isset($inputs['taxation_id']) || $inputs['taxation_id'] <=0)
				throw new MissingProductTaxationsParamsExecption('Tax should not be empty. ');

			$taxations_det = Webshoptaxation::Taxations()->getTaxations(array('id' => $inputs['taxation_id']), 'first');

			if(!$taxations_det)
				throw new MissingProductTaxationsParamsExecption(trans('taxations.taxation_id_not_avail'));

			$user_id = $taxations_det->user_id;
			if(!$user_id)
				throw new MissingProductTaxationsParamsExecption(trans('taxations.something_went_wrong'));

			$inputs['user_id'] = $user_id;


			if($inputs['tax_fee'] == -0)
			{
				$inputs['tax_fee'] = 0;
			}
			$inputs['tax_fee'] = round($inputs['tax_fee'], 2); //number_format($inputs['tax_fee'], 2, '.', '');
			if($inputs['tax_fee'] <= 0)
			{
				throw new MissingProductTaxationsParamsExecption(trans('taxations.tax_fee_min'));
			}


			$rules = array(
				'taxation_id' 	=> 'required|numeric',
				'user_id' 		=> 'required|numeric',
				'product_id'	=> 'required|numeric',
				'tax_fee' 		=> 'required|numeric|min:0',
				'fee_type'		=> 'required|in:percentage,flat',
			);
			$valid_keys = array(
				'taxation_id'		=> '',
				'user_id' 			=> '',
				'product_id' 		=> '',
				'tax_fee' 			=> '',
				'fee_type'			=> '',
			);
			$inputs = array_intersect_key($inputs, $valid_keys);
			//$inputs = $inputs+$valid_keys;

			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$getProductTaxtions = $this->getProductTaxations(array('product_id' => $inputs['product_id'], 'taxation_id' => $inputs['taxation_id']), 'first');
				if(!$getProductTaxtions || count($getProductTaxtions) <=0)
				{
					$producttaxations = ProductTaxations::create($inputs);
					return $producttaxations->id;
				}
				else
					throw new MissingProductTaxationsParamsExecption(trans('taxations.tax_already_added'));

			}
			else
			{
				throw new MissingProductTaxationsParamsExecption($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}


	public function getProductTaxations($options = array(), $return_type = 'all')
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($options) || !is_array($options) || (is_array($options) && empty($options)))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.options_empty'));

			if(is_null($return_type)){$return_type = 'all';}
			if(!in_array($return_type, array('all','first')))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.producttax_return_type'));


			//Create model object for taxations
			//$producttaxations = ProductTaxations::with('taxations')->->orderby('id','asc');
			$producttaxations = ProductTaxations::with(array('taxations' => function($query) {$query->withTrashed();}))->orderby('id','asc');

			$valid_keys = array(
					'id' => '',
					'product_id' => '',
					'taxation_id' => ''
				);
			$options = array_intersect_key($options, $valid_keys);

			if(empty($options))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.producttax_options_invalid'));

			//Conditions based on input
			if(isset($options['id']) && $options['id'] > 0)
				$producttaxations->where('id','=',$options['id']);
			//if(isset($options['user_id']) && $options['user_id'] > 0)
				//$producttaxations->where('user_id','=',$options['user_id']);
			if(isset($options['product_id']) && $options['product_id'] > 0)
				$producttaxations->where('product_id','=',$options['product_id']);
			if(isset($options['taxation_id']) && $options['taxation_id'] > 0)
				$producttaxations->where('taxation_id','=',$options['taxation_id']);

			//Check the return type and get hte list
			if($return_type == 'first')
				$producttaxationslist = $producttaxations->first();
			else
				$producttaxationslist = $producttaxations->get();

			//Return the list
			if(count($producttaxationslist) > 0)
				return $producttaxationslist;
			else
				return false;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}


	public function updateProductTaxation($id = null, $inputs = array(), $conditions = array())
	{
		try
		{

			//Throw exceptions if inputs are wrong
			if(is_null($id) && empty($conditions))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.taxation_and_condition_empty'));

			//if conditions are specified then check are the valid inputs. If not throw the error
			if(!empty($conditions))
			{

				$valid_keys = array(
					'product_id' => '',
					'taxation_id' => ''
				);

				$conditions = array_intersect_key($conditions, $valid_keys);

				if(empty($conditions))
					throw new MissingProductTaxationsParamsExecption(trans('taxations.input_condition_mismatch'));

				$condition_rules = array(
					'product_id' 		=> 'required|numeric',
					'taxation_id'		=> 'required|numeric',
				);

				$condition_validator = Validator::make($conditions,$condition_rules);
				if($condition_validator->fails() && is_null($id))
				{
					throw new MissingProductTaxationsParamsExecption(trans('taxations.tax_fee_and_condition_required'));
				}

			}

			//Get the detail of the given taxation details from give id or from the conditions
			if(!is_null($id))
				$taxations_det = $this->getProductTaxations(array('id' => $id), 'first');
			elseif(!empty($conditions))
				$taxations_det = $this->getProductTaxations($conditions, 'first');
			else
				$taxations_det =false;

			if(!$taxations_det)
				throw new MissingProductTaxationsParamsExecption(trans('taxations.producttax_not_avail'));

			$product_taxation_id = $taxations_det->id;
			if(!$product_taxation_id)
				throw new MissingProductTaxationsParamsExecption(trans('taxations.producttax_went_wrong'));


			//Check the fields to update
			$rules = array(
				'tax_fee' 		=> 'sometimes|required|numeric|min:0',
				'fee_type'		=> 'sometimes|required|in:percentage,flat',
			);
			$valid_keys = array(
				'tax_fee' 			=> '',
				'fee_type'			=> '',
			);

			$inputs = array_intersect_key($inputs, $valid_keys);
			if(empty($inputs))
					throw new MissingProductTaxationsParamsExecption(trans('taxations.update_field_invalid'));

			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$affectedRows = ProductTaxations::where('id', '=', $product_taxation_id)->update($inputs);
				return $affectedRows;
			}
			else
			{
				throw new MissingProductTaxationsParamsExecption($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}

	public function deleteProductTaxation($id = null, $conditions = array())
	{

		try
		{

			//Throw exceptions if inputs are wrong
			if(is_null($id) && empty($conditions))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.taxation_and_condition_empty'));

			//if conditions are specified then check are the valid inputs. If not throw the error
			if(!empty($conditions))
			{

				$valid_keys = array(
					'product_id' => '',
					'taxation_id' => ''
				);

				$conditions = array_intersect_key($conditions, $valid_keys);

				if(empty($conditions))
					throw new MissingProductTaxationsParamsExecption(trans('taxations.input_condition_mismatch'));

				$condition_rules = array(
					'product_id' 		=> 'required|numeric',
					'taxation_id'		=> 'required|numeric',
				);

				$condition_validator = Validator::make($conditions,$condition_rules);
				if($condition_validator->fails() && is_null($id))
				{
					throw new MissingProductTaxationsParamsExecption(trans('taxations.tax_fee_and_condition_required'));
				}

			}

			//Get the detail of the given taxation details from give id or from the conditions
			if(!is_null($id))
				$taxations_det = $this->getProductTaxations(array('id' => $id), 'first');
			elseif(!empty($conditions))
				$taxations_det = $this->getProductTaxations($conditions, 'first');
			else
				$taxations_det = false;

			if(!$taxations_det)
				throw new MissingProductTaxationsParamsExecption(trans('taxations.producttax_not_avail'));

			$product_taxation_id = $taxations_det->id;
			if(!$product_taxation_id)
				throw new MissingProductTaxationsParamsExecption(trans('taxations.producttax_went_wrong'));


			$affectedRows = ProductTaxations::where('id', '=', $product_taxation_id)->delete();
			return $affectedRows;

		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}
	public function deleteProductAllTaxation($product_id = null)
	{
		try
		{


			//Throw exceptions if inputs are wrong
			if(is_null($product_id) || !is_numeric($product_id))
				throw new MissingProductTaxationsParamsExecption(trans('taxations.product_id_empty'));

			//if conditions are specified then check are the valid inputs. If not throw the error
			$affectedRows = 0;
			if($product_id > 0)
			{
				$affectedRows = ProductTaxations::where('product_id', '=', $product_id)->delete();
				return $affectedRows;
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}

}