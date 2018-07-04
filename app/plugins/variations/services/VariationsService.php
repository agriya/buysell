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
class VariationsService
{
	public $attrib_labels_arr = array();
	public $logged_user_id = '';

	function __construct()
	{
		$this->logged_user_id = \BasicCUtil::getLoggedUserId();
	}

	public function getProductSwapImageCount($prod_id)
	{
		$count = 0;
		if($prod_id != '' && $prod_id > 0)
		{
			$count = DB::table('item_swap_image')->whereRaw('item_id = ?', array($prod_id))->count();
		}
		return $count;
	}

	public function insertSwapImageFiles($data_arr)
	{
		$attribute_id = NULL;
		if(!empty($data_arr))
			$attribute_id = DB::table('item_swap_image')->insertGetId($data_arr);
		return $attribute_id;
	}

	public function populateProductSwapImages($product_id = 0)
	{
		$resources_arr = array();
		if($product_id == 0)
			return $resources_arr;

		$resources_arr = array();
		$resources = DB::table('item_swap_image')
						->select('swap_image_id', 'item_id', 'filename', 'ext', 'width', 'height', 'title',
								'l_width', 'l_height', 't_width', 't_height', 'server_url')
						->whereRaw('item_id = ?', array($product_id))
						->orderBy('swap_image_id', 'ASC')
						->get();
		if(count($resources) > 0)
		{
			foreach($resources as $key => $data)
			{
				$resources_arr[$key] = array('resource_id' 		=> $data->swap_image_id,
											'item_id' 			=> $data->item_id,
											'filename' 			=> $data->filename,
											'filename_thumb' 	=> $data->filename . 'T.' . $data->ext,
											'filename_large'	=> $data->filename . 'L.' . $data->ext,
											'filename_original' => $data->filename . '.' . $data->ext,
											'width' 			=> $data->width,
											'height' 			=> $data->height,
											't_width' 			=> $data->t_width,
											't_height' 			=> $data->t_height,
											'l_width' 			=> $data->l_width,
											'l_height' 			=> $data->l_height,
											'ext' 				=> $data->ext,
											'title' 			=> $data->title,
											'server_url'	 	=> $data->server_url);
			}
		}
		return $resources_arr;
	}

	public function updateProductSwapImageTitle($resource_id, $title)
	{
		DB::table('item_swap_image')->whereRaw('swap_image_id = ?', array($resource_id))->update(array('title' => $title));
	    return true;
	}

	public function deleteProductSwapImage($row_id)
	{
		DB::table('item_swap_image')->whereRaw('swap_image_id = ?', array($row_id))->delete();
		return true;
	}

	public function getItemVariationsGenerateHeaders($product_id)
	{
		$head_label_arr = array();
		$variation_arr = DB::table('item_variation')
								->select('variation.variation_id', 'variation.name', 'variation.help_text')
								->join('variation', 'item_variation.variation_id', '=' , 'variation.variation_id')
								->whereRaw('item_variation.item_id = ?', array($product_id))
								->orderBy('item_variation.variation_id', 'ASC')
								->get();
		if(count($variation_arr) > 0)
		{
			foreach($variation_arr as $key => $row)
			{
				$head_label_arr[$row->variation_id]['label'] = $row->name;
			}
		}
		return $head_label_arr;
	}

	public function populateItemVariations($product_id)
	{
		$result_arr = $matrix_data_arr = array();
		$matrix_data = DB::table('item_variation_details')
							->select('item_variation_details.matrix_id', 'variation_attributes.label', 'item_variation_details.price',
									'item_variation_details.giftwrap_price', 'item_variation_details.shipping_price',
									'item_variation_details.stock', 'item_variation_details.swap_img_id',
									'item_variation_details.price_impact', 'item_variation_details.giftwrap_price_impact',
									'item_variation_details.shipping_price_impact',
									'item_swap_image.filename', 'item_swap_image.ext', 'item_swap_image.width', 'item_swap_image.height',
									'item_swap_image.title', 'item_swap_image.l_width', 'item_swap_image.l_height',
									'item_swap_image.t_width', 'item_swap_image.t_height', 'item_swap_image.server_url', 'item_variation_details.is_default',
									'item_variation_details.is_active', 'item_var_matrix_attributes.attribute_id')
							->leftjoin('item_var_matrix_attributes', 'item_variation_details.matrix_id', '=' , 'item_var_matrix_attributes.matrix_id')
							->leftjoin('variation_attributes', 'item_var_matrix_attributes.attribute_id', '=' , 'variation_attributes.attribute_id')
							->leftjoin('item_swap_image', 'item_variation_details.swap_img_id', '=' , 'item_swap_image.swap_image_id')
							->whereRaw('item_variation_details.item_id = ?', array($product_id))
							->get();

		$show_giftwrap = ($this->chkIsUsingGiftwrap($product_id)) ? 1 : 0;
		$show_stock = ($this->chkIsDisregardStock($product_id)) ? 0 : 1;
		if(count($matrix_data) > 0)
		{
			foreach($matrix_data as $kay => $row)
			{
				$matrix_data_arr[$row->matrix_id]['is_active'] =$row->is_active;
				$matrix_data_arr[$row->matrix_id]['swap_img_id'] =$row->swap_img_id;
				if($row->filename != '' && $row->ext != '')
				{
					$matrix_data_arr[$row->matrix_id]['filename_thumb'] =$row->filename . 'T.' . $row->ext;
					$matrix_data_arr[$row->matrix_id]['filename_large'] =$row->filename . 'L.' . $row->ext;
					$matrix_data_arr[$row->matrix_id]['filename_original'] =$row->filename . '.' . $row->ext;
				}
				else
				{
					$matrix_data_arr[$row->matrix_id]['filename_thumb'] = '';
					$matrix_data_arr[$row->matrix_id]['filename_large'] = '';
					$matrix_data_arr[$row->matrix_id]['filename_original'] = '';
				}

				$matrix_data_arr[$row->matrix_id]['matrix_id'] =$row->matrix_id;
				$matrix_data_arr[$row->matrix_id]['attrib_label'][] =$row->label;
				$matrix_data_arr[$row->matrix_id]['price'] =(round($row->price) != 0 ) ? $row->price : 0;
				if($matrix_data_arr[$row->matrix_id]['price'] != 0 && $row->price_impact =='increase')
					$matrix_data_arr[$row->matrix_id]['price'] = $row->price;
					//$matrix_data_arr[$row->matrix_id]['price'] = '+'.$row->price;

				$matrix_data_arr[$row->matrix_id]['giftwrap_price'] =(round($row->giftwrap_price) != 0 ) ? $row->giftwrap_price : 0;
				if($matrix_data_arr[$row->matrix_id]['giftwrap_price'] != 0 && $row->giftwrap_price_impact =='increase')
					$matrix_data_arr[$row->matrix_id]['giftwrap_price'] = $row->giftwrap_price;
					//$matrix_data_arr[$row->matrix_id]['giftwrap_price'] = '+'.$row->giftwrap_price;

				$matrix_data_arr[$row->matrix_id]['shipping_price'] =(round($row->shipping_price) != 0 ) ? $row->shipping_price : 0;
				if($matrix_data_arr[$row->matrix_id]['shipping_price'] != 0 && $row->shipping_price_impact =='increase')
					//$matrix_data_arr[$row->matrix_id]['shipping_price'] = '+'.$row->shipping_price;
					$matrix_data_arr[$row->matrix_id]['shipping_price'] = $row->shipping_price;

				$matrix_data_arr[$row->matrix_id]['stock'] =$row->stock;
				$matrix_data_arr[$row->matrix_id]['title'] =$row->title;
				$matrix_data_arr[$row->matrix_id]['t_width'] =$row->t_width;
				$matrix_data_arr[$row->matrix_id]['t_height'] =$row->t_height;
				$matrix_data_arr[$row->matrix_id]['is_default'] =$row->is_default;
				$matrix_data_arr[$row->matrix_id]['attribute_id'] =$row->attribute_id;
				$matrix_data_arr[$row->matrix_id]['changeSwapImgLink'] = URL::action('ProductAddController@getProductSwapImagesList').'?product_id='.$product_id.'&r_fnname=srcUpdImg';
				$matrix_data_arr[$row->matrix_id]['show_giftwrap'] = $show_giftwrap;
				$matrix_data_arr[$row->matrix_id]['show_stock'] = $show_stock;
			}
		}
		//$show_matrix_block = (COUNT($matrix_data_arr) > 0) ? 1 : 0;
		$result_arr['matrix_data_arr'] = $matrix_data_arr;
		$result_arr['show_giftwrap'] = $show_giftwrap;
		$result_arr['show_stock'] = $show_stock;
		return $result_arr;
	}

	public function chkIsUsingGiftwrap($product_id)
	{
		if(CUtil::chkIsAllowedModule('variations'))
		{
			if($product_id != '' && $product_id > 0)
			{
				$count = DB::table('product')->whereRaw('id = ? AND accept_giftwrap = ?', array($product_id, 1))->count();
				if($count > 0)
					return true;
			}
		}
		return false;
	}

	public function chkIsDisregardStock($product_id)
	{
		// Dis regard stock not considered now.
		return false;
	}

	public function getSelctedVariationGroupsByUser($product_id)
	{
		$sel_var_grp_id = 0;
		$product_details = DB::table('product')
								->select('variation_group_id')
								->whereRaw('id = ?', array($product_id))
								->first();
		if(count($product_details) > 0) {
			$sel_var_grp_id = $product_details->variation_group_id;
		}
		return $sel_var_grp_id;
	}

	public function getVariationGroupsByUser($user_id = 0)
	{
		$var_grp = array();
		$var_grp_arr = DB::table('variation_group')
								->select('variation_group_id', 'variation_group_name')
								->whereRaw('user_id = ?', array($user_id))
								->get();
		if(count($var_grp_arr) > 0)
		{
			foreach($var_grp_arr as $key => $val)
			{
				$var_grp[$val->variation_group_id] = $val->variation_group_name;
			}
		}
		return $var_grp;
	}

	public function chkIsUserAddedVariationGroup($user_id = 0)
	{
		$count = 0;
		$count = DB::table('variation_group')->whereRaw('user_id = ?', array($user_id))->count();
		return $count;
	}

	public function chkIsUserAddedVariation($user_id = 0)
	{
		$count = 0;
		$count = DB::table('variation')->whereRaw('user_id = ?', array($user_id))->count();
		return $count;
	}

	public function populateVariationsInGroupByGroupId($group_id, $product_id = 0, $is_admin_side = 0)
	{
		$result_arr = $sel_var_arr = $sel_attrib_arr = $active_var_arr = $active_attrib_arr = array();
		//todo , if product id is passed, those attributes need to be fetched and
		// the corresponding checked / disabled ..
		//if item id is not passed, it is being set newly., have everything unchecked by default ..
		if($product_id)
		{
			//fetch the variations and attributes that are already set ..
			$item_variation_arr = DB::table('item_variation')
								->select('variation_id', 'is_active')
								->whereRaw('item_id = ?', array($product_id))
								->get();
			if(count($item_variation_arr) > 0)
			{
				foreach($item_variation_arr as $key => $val)
				{
					$sel_var_arr[] = $val->variation_id;
					if($val->is_active)
						$active_var_arr[] = $val->variation_id;
				}
			}

			$item_variation_attributes_arr = DB::table('item_variation_attributes')
								->select('attribute_id', 'is_active')
								->whereRaw('item_id = ?', array($product_id))
								->get();
			if(count($item_variation_attributes_arr) > 0)
			{
				foreach($item_variation_attributes_arr as $key => $val)
				{
					$sel_attrib_arr[] = $val->attribute_id;
					if($val->is_active)
						$active_attrib_arr[] = $val->attribute_id;
				}
			}
		}

		$resource_options_arr = array();
		$variation_group_items_arr = DB::table('variation_group_items')
								->select('variation.name', 'variation_group_items.variation_id')
								->join('variation', 'variation_group_items.variation_id', '=' , 'variation.variation_id')
								->whereRaw('variation_group_items.variation_group_id = ?', array($group_id))
								->get();
		if(count($variation_group_items_arr) > 0)
		{
			$inc = 0;
			foreach($variation_group_items_arr as $key => $row)
			{
				$resource_options_arr[$inc]['variation_id'] = $row->variation_id;
				$resource_options_arr[$inc]['name'] = $row->name;
				$resource_options_arr[$inc]['checked'] = in_array($row->variation_id, $active_var_arr) ? 'checked' : '';
				$variation_options_arr = array();
				$variation_options_arr = $this->fetchAttributeOptionsByVariationDetId($row->variation_id);
				foreach($variation_options_arr as $a_index => $arr)
				{
					if($resource_options_arr[$inc]['checked'] AND in_array($variation_options_arr[$a_index]['attribute_id'], $active_attrib_arr))
					{
						$variation_options_arr[$a_index]['checked'] = 'checked';
					}
					else
					{
						$variation_options_arr[$a_index]['checked'] = '';
					}
				}
				$resource_options_arr[$inc]['options_arr'] = $variation_options_arr;
				$inc++;
			}
		}

		//show the cancel button only, if the matrix has been already set ..
		$item_var_matrix_details_cnt = DB::table('item_var_matrix_details')
											->select('matrix_id')
											->whereRaw('item_id = ? AND is_active = 1', array($product_id))
											->count();
		$show_cancel = ($item_var_matrix_details_cnt > 0) ? 1 : 0;

		$result_arr['var_resource_options_arr'] = $resource_options_arr;
		$result_arr['var_show_cancel_button'] = $show_cancel;

		return $result_arr;
	}

	public function fetchAttributeOptionsByVariationDetId($variation_id)
	{
		$options_arr = array();

		$variation_attributes_arr = DB::table('variation_attributes')
								->select('attribute_id', 'label')
								->whereRaw('variation_id = ?', array($variation_id))
								->orderBy('position', 'ASC')
								->get();
		if(count($variation_attributes_arr) > 0)
		{
			$inc = 0;
			foreach($variation_attributes_arr as $key => $row)
			{
				$options_arr[$inc]['attribute_name'] = $row->label;
				$options_arr[$inc]['attribute_id'] = $row->attribute_id;
				$inc++;
			}
		}
		return $options_arr;
	}

	//function called when variations and attributes are selected from the variation group ..
	public function updateItemVariationAttribute($insert_arr)
	{
		DB::table('product')->whereRaw('id = ?', array($insert_arr['product_id']))->update(array('variation_group_id' => $insert_arr['variation_group_id']));
		$new_var_id_arr = $new_attr_arr = $var_attrib_details1 = array();
		$attr_id_arr = $insert_arr['attr_id_arr'];
		//instead of getting variations directly from the checked variation boxes,
		//get it from the checked attributes
		foreach($attr_id_arr AS $attr_index => $attr_id)
		{
			$data_arr = array();
			$data_arr = explode("_", $attr_id);
			//$attr_id is of the form variation id_attrib id
			//store the variation id for the attrib id
			$var_attrib_details1[$data_arr[2]] =$data_arr[1];
			$new_attr_arr[] = $data_arr[2];
			$new_var_id_arr[] = $data_arr[1];
		}
		$old_var_id_arr =$this->fetchAssignedVariaionIds($insert_arr['product_id']);
		$del_var_arr = array_diff($old_var_id_arr, $new_var_id_arr);
		//If a variation itself is removed or added, we can delete and reform the matrix, need to check invoice too?
		$re_form = false;
		$can_delete = !$this->isCartOrInvoiceExists($insert_arr['product_id']);
		if(!empty($del_var_arr))
		{
			//If a variation itself is removed , we can delete and reform the matrix
			$re_form = $can_delete;
			$details_arr =  array();
			$details_arr['item_id'] =  $insert_arr['product_id'];
			$details_arr['product_id'] =  $insert_arr['product_id'];
			$details_arr['id_arr'] =  $del_var_arr;
			if($can_delete)
				$this->removeAssignedVar($details_arr);	# Remove the existing variation record and the corresponding attributes
			else
				$this->deactivateAssignedVar($details_arr);	# Deactivate the existing variation record and the attributes

		}
		$add_var_arr = array_diff($new_var_id_arr, $old_var_id_arr);
		if($add_var_arr)
		{
			//If a variation itself is added , we can delete and reform the matrix
			$re_form = $can_delete;
			foreach($add_var_arr as $id)
			{
				// insert variations entry
				$details_arr =  array();
				$details_arr['item_id'] =  $insert_arr['product_id'];
				$details_arr['variation_id'] =  $id;
				$this->addItemVariationEntry($details_arr);
			}
		}
		$attr_id_arr = $insert_arr['attr_id_arr'];
		$assigned_attrib_details = $this->fetchAssignedAttrbIds($insert_arr['product_id']);
		//$assigned_attrib_details of the form		$return_arr['attrib_ids_arr'] = $attribs_arr;
		//$return_arr['attrib_ids_variations'] = $attribs_variation;
		$old_attr = $assigned_attrib_details['attrib_ids_arr'];
		$var_attrib_details2 = $assigned_attrib_details['attrib_ids_variations'];
		$del_attr_arr = array_diff($old_attr, $new_attr_arr);
		$add_attr_arr = array_diff($new_attr_arr, $old_attr);
		$var_attrib_details = array();
		$var_attrib_details = $var_attrib_details1+$var_attrib_details2;
		//code to activate assigned attrib arr
		foreach($new_attr_arr as $act_id)
		{
			$details_arr = array();
			$details_arr['product_id'] =  $insert_arr['product_id'];
			$details_arr['variation_id'] =  $var_attrib_details[$act_id];
			$details_arr['attribute_id'] =  $act_id;
			$this->activateAssignedAttr($details_arr); // deactivate alone
		}
		if(!empty($del_attr_arr))
		{
			foreach($del_attr_arr as $del_id)
			{
				//if the variation is in the deleted array, has been handled already , skip
				if(isset($var_attib_details[$del_id]))
				{
					if(in_array($var_attib_details[$del_id], $del_var_arr))
						continue;
				}
				$details_arr = array();
				$details_arr['product_id'] =  $insert_arr['product_id'];
				$details_arr['variation_id'] =  $var_attrib_details[$del_id];
				$details_arr['attribute_id'] =  $del_id;
				$this->deactivateAssignedAttr($details_arr); // deactivate alone
			}
		}
		if($add_attr_arr)
		{
			foreach($add_attr_arr as $add_id)
			{
				$details_arr = array();
				$details_arr['product_id'] =  $insert_arr['product_id'];
				$details_arr['variation_id'] =  $var_attrib_details[$add_id];
				$details_arr['attribute_id'] =  $add_id;
				$this->addItemVariationAttributeEntry($details_arr);
			}
		}
		$this->updateItemVariationMatrix($insert_arr['product_id'], $re_form);
	}

	public function fetchAssignedVariaionIds($product_id)
	{
		$varIds = array();
		$item_variation_arr = DB::table('item_variation')
								->select(DB::raw('DISTINCT(variation_id)'))
								->whereRaw('item_id = ?', array($product_id))
								->get();
		if(count($item_variation_arr) > 0)
		{
			foreach($item_variation_arr as $key => $row)
			{
				$varIds[] = $row->variation_id;
			}
		}
		return $varIds;
	}

	public function isCartOrInvoiceExists($product_id)
	{
		//first check if the item has been activated by checking the dateactivated
		//check if any rec in the invoice item table has this item
		//if needed check if it exists in cart too ..
		//todo implement the above.
		return false;
	}

	public function removeAssignedVar($data_arr)
	{
		//array consists of item_id and ids to be deleted in id_arr
		//todo added deletion from mp_item_variation too
		$ids = implode(',', $data_arr['id_arr']);
		if($ids)
		{
			DB::table('item_variation')
				->whereRaw('item_id = ? AND variation_id in ( '.$ids.')', array($data_arr['product_id']))
				->delete();

			DB::table('item_variation_attributes')
				->whereRaw('item_id = ? AND variation_id in ( '.$ids.')', array($data_arr['product_id']))
				->delete();
		}
	}

	public function deactivateAssignedVar($data_arr)
	{
		//array consists of item_id and ids to be deleted in id_arr
		$ids = implode(',', $data_arr['id_arr']);
		if($ids)
		{
			DB::table('item_variation')
				->whereRaw('item_id = ? AND variation_id in ( '.$ids.')', array($data_arr['product_id']))
				->update(array('is_active' => 0));

			DB::table('item_variation_attributes')
				->whereRaw('item_id = ? AND variation_id in ( '.$ids.')', array($data_arr['product_id']))
				->update(array('is_active' => 0));
		}
	}

	public function addItemVariationEntry($data_arr)
	{
		$count = DB::table('item_variation')
					->whereRaw('item_id = ? AND variation_id = ?', array($data_arr['item_id'], $data_arr['variation_id']))
					->count();
		if($count > 0)
			return true;

		$data_arr_ins = array('item_id' => $data_arr['item_id'], 'variation_id' => $data_arr['variation_id']);
		$item_variation_id = DB::table('item_variation')->insertGetId($data_arr_ins);
	}

	public function fetchAssignedAttrbIds($product_id)
	{
		$attrIds = $attribs_arr = $attribs_variation = array();
		$item_variation_attributes_arr = DB::table('item_variation_attributes')
											->select('variation_id', 'attribute_id')
											->whereRaw('item_id = ?', array($product_id))
											->get();
		if(count($item_variation_attributes_arr) > 0)
		{
			foreach($item_variation_attributes_arr as $key => $row)
			{
				$attribs_arr[] = $row->attribute_id;
				$attribs_variation[$row->attribute_id] =  $row->variation_id;
			}
		}
		$return_arr['attrib_ids_arr'] = $attribs_arr;
		$return_arr['attrib_ids_variations'] = $attribs_variation;
		return $return_arr;
	}

	public function deactivateAssignedAttr($data_arr)
	{
		DB::table('item_variation_attributes')
				->whereRaw('item_id = ?', array($data_arr['product_id']))
				->whereRaw('variation_id = ?', array($data_arr['variation_id']))
				->whereRaw('attribute_id = ?', array($data_arr['attribute_id']))
				->update(array('is_active' => 0));
	}

	public function activateAssignedAttr($data_arr)
	{
		DB::table('item_variation_attributes')
				->whereRaw('item_id = ?', array($data_arr['product_id']))
				->whereRaw('variation_id = ?', array($data_arr['variation_id']))
				->whereRaw('attribute_id = ?', array($data_arr['attribute_id']))
				->update(array('is_active' => 1));
	}

	public function addItemVariationAttributeEntry($data_arr)
	{
		$data_arr_ins = array('item_id' => $data_arr['product_id'],
								'variation_id' => $data_arr['variation_id'],
								'attribute_id' => $data_arr['attribute_id']);
		$item_variation_id = DB::table('item_variation_attributes')->insertGetId($data_arr_ins);
	}

	public function updateItemVariationMatrix($product_id, $re_form = true)
	{
		// Matrix populate Starts
		$vars_list = $attribs_list_arr = $matrix_status = array();

		$item_variation_arr = DB::table('item_variation_attributes')
								->select('variation_id', 'attribute_id')
								->whereRaw('item_id = ?', array($product_id))
								->whereRaw('is_active = ?', array(1))
								->get();
		if(count($item_variation_arr) > 0)
		{
			foreach($item_variation_arr as $key => $row)
			{
				$vars_list[] = $row->variation_id;
				$attribs_list_arr[$row->variation_id][] = $row->attribute_id;
			}
		}

		$traits = array();
		//traits will hold the attributes selected in each variation as an array
		foreach($attribs_list_arr AS $attr )
		{
			$traits[] = $attr;
		}
		$combinations = array();
		$this->getArrayCombinations($traits, $combinations);
		// Populat Attribute Labels list
		$this->populateAttributeLabelsList($product_id);
		$matrix_data_arr = $matrix_attributes = $matrix_status = array();
		$inc = 0;
		//@todo -needs to add th functionality like unwanted entries to be remove and new entries add. instead of remove all and add new.
		if($re_form)
		{
			$this->removeMatrixEntry($product_id);
		}
		else
		{
			$assigned_mat = array();
			$item_var_matrix_attributes_arr = DB::table('item_var_matrix_attributes')
								->select('matrix_id', 'attribute_id')
								->whereRaw('item_id = ?', array($product_id))
								->orderBy('matrix_id', 'ASC')
								->orderBy('attribute_id', 'ASC')
								->get();
			if(count($item_var_matrix_attributes_arr) > 0)
			{
				foreach($item_var_matrix_attributes_arr as $key => $row)
				{
					$matrix_attributes[$row->matrix_id][] = $row->attribute_id;
					$matrix_status[$row->matrix_id] = 0;
				}
			}
		}
		foreach($combinations as $combination_data)
		{
			$matrix_found = false;
			//check if the matrix id exists for this combination, if so activate alone, else, insert
			if(!$re_form and count($matrix_attributes))
			{
				foreach($matrix_attributes as $matrix_id => $mat_att_id_arr)
				{
					if(!count(array_diff(array_merge($combination_data, $mat_att_id_arr), array_intersect($combination_data, $mat_att_id_arr))))
					{
						$matrix_status[$matrix_id] = 1;
						$matrix_found = true;
						break;
					}
				}
			}
			//if the matrix already exists, just noted above as to be acitivated
			if($matrix_found)
				continue;
			//matrix not found, proceed with adding
			$attrib_data = $attrib_ids = $labels = array();
			$inc1 = 0;
			foreach($combination_data AS $cdata)
			{
				$attrib_ids[] = $cdata;
				$labels[] = $this->attrib_labels_arr[$cdata];
				$attrib_data[$inc1]['attrib_id'] = $cdata;
				$attrib_data[$inc1]['attrib_label'] = $this->attrib_labels_arr[$cdata];
				$inc1++;
			}
			$this->addMatrixEntry($attrib_data, $product_id);
		}
		$active_matrix_ids = array();
		$inactive_matrix_ids = array();
		//deactivate the other matrix entries ..
		foreach($matrix_status as $id => $status)
		{
			if($status)
				$active_matrix_ids[] = $id;
			else
				$inactive_matrix_ids[] = $id;
		}
		$this->updateMatrixStatus($active_matrix_ids, $product_id, 1);
		$this->updateMatrixStatus($inactive_matrix_ids, $product_id, 0);
	}

	public function getArrayCombinations($main_array, &$combinations, $batch=array(), $index=0)
	{
	    if ($index >= count($main_array))
	    {
	        array_push($combinations, $batch);
	    }
		else
	    {
	        foreach ($main_array[$index] as $element)
	        {
	            $temp_array = $batch; array_push($temp_array, $element);
	            $this->getArrayCombinations($main_array, $combinations, $temp_array, $index+1);
	        }
		}
	}

	public function populateAttributeLabelsList($product_id)
	{
		$this->attrib_labels_arr = array();

		$item_variation_attributes_arr = DB::table('item_variation_attributes')
								->select('item_variation_attributes.attribute_id', 'variation_attributes.label')
								->join('variation_attributes', 'item_variation_attributes.attribute_id', '=' , 'variation_attributes.attribute_id')
								->whereRaw('item_variation_attributes.item_id = ?', array($product_id))
								->get();
		if(count($item_variation_attributes_arr) > 0)
		{
			foreach($item_variation_attributes_arr as $key => $row)
			{
				$this->attrib_labels_arr[$row->attribute_id] = $row->label;
			}
		}
	}

	public function removeMatrixEntry($product_id)
	{
		$count = DB::table('item_var_matrix_attributes')
					->whereRaw('item_id = ?', array($product_id))
					->count();
		if ($count > 0)
		{
			// Remove existing entry
			DB::table('item_var_matrix_attributes')->whereRaw('item_id = ?', array($product_id))->delete();
			DB::table('item_var_matrix_details')->whereRaw('item_id = ?', array($product_id))->delete();
			DB::table('item_variation_details')->whereRaw('item_id = ?', array($product_id))->delete();
		}
	}

	public function addMatrixEntry($attrib_data, $product_id)
	{
		//todo: only if the matrix id does not exists for this combination of attributes, this will be called
		// generate the matrix id and insert the attributes
		$data_arr_ins = array('item_id' => $product_id);
		$matrix_id = DB::table('item_var_matrix_details')->insertGetId($data_arr_ins);

		$contentArr = array();
		// item_var_matrix_attributes - for store attrib	- matrix id, attribute_id	eg: 1=> {M1, A1}, {M1, A2}, {M2, A4}, etc..
		//echo '<pre>';print_r($attrib_data);exit;
		foreach($attrib_data AS $attrs)
		{
			$contentArr[] = $attrs['attrib_label'];
			$matrix_data_arr = array('item_id' => $product_id,
										'matrix_id' => $matrix_id,
										'attribute_id' => $attrs['attrib_id']);
			$matrix_details_id = DB::table('item_var_matrix_attributes')->insertGetId($matrix_data_arr);
		}
		// item_var_matrix_details  - for store content	- item_id, matrix_id, content eg: 1=> {i1, m1, a1name#@#a2name}, 2=>{i1, m2, a4name}
		$contentVal = implode('#@@#', $contentArr);

		DB::table('item_var_matrix_details')
				->whereRaw('matrix_id = ?', array($matrix_id))
				->update(array('content' => $contentVal));

		// Insert into item variation details entry
		//todo - check this and remove, since matrix id is created now only , can directly insert.
		$count = DB::table('item_variation_details')
					->whereRaw('item_id = ?', array($product_id))
					->whereRaw('matrix_id = ?', array($matrix_id))
					->count();
		if ($count > 0)
		{
			return true;
		}
		$details_data_arr = array('item_id' => $product_id, 'matrix_id' => $matrix_id);
		$matrix_id = DB::table('item_variation_details')->insertGetId($details_data_arr);
	}

	public function updateMatrixStatus($ids_arr, $product_id, $status)
	{
		if(count($ids_arr))
		{
			$ids = implode($ids_arr, ',');
			if($ids)
			{
				DB::table('item_var_matrix_details')
					->whereRaw('item_id = ? AND matrix_id in ('.$ids.')', array($product_id))
					->update(array('is_active' => $status));

				DB::table('item_variation_details')
					->whereRaw('matrix_id in ('.$ids.')', array())
					->update(array('is_active' => $status));
			}
		}
	}

	public function populateDefaultMatrixDetails($matrix_ids, $product_id)
	{
		$mat_det_arr = array();
		//initialize the values
		$mat_det_arr['matrix_id'] = implode(',', $matrix_ids);
		$mat_det_arr['attrib_labels'] = '';
		$mat_det_arr['price'] = '';
		$mat_det_arr['giftwrap_price'] = '';
		$mat_det_arr['shipping_price'] = '';
		$mat_det_arr['stock'] = 0;
		$mat_det_arr['swap_img_id'] = '';
		$mat_det_arr['filename_thumb'] = '';
		$mat_det_arr['filename_large'] = '';
		$mat_det_arr['filename_original'] = '';
		$mat_det_arr['changeSwapImgLink'] = URL::action('ProductAddController@getProductSwapImagesList').'?product_id='.$product_id.'&r_fnname=assgnImg';
		$mat_det_arr['title'] = '';
		$mat_det_arr['t_width'] = '';
		$mat_det_arr['t_height'] = '';
		$mat_det_arr['price_impact'] = '';
		$mat_det_arr['giftwrap_price_impact'] = '';
		$mat_det_arr['shipping_price_impact'] = '';
		$mat_det_arr['description'] = '';
		$mat_det_arr['is_active'] = '';
		$mat_det_arr['is_default'] = '';
		$mat_det_arr['show_giftwrap'] = ($this->chkIsUsingGiftwrap($product_id)) ? 1 : 0;;
		$mat_det_arr['show_stock'] = ($this->chkIsDisregardStock($product_id)) ? 0 : 1;
		return $mat_det_arr;
	}

	public function populateMatrixDetails($matrix_id, $product_id)
	{
		$mat_det_arr = array();
		$item_variation_details_arr = DB::table('item_variation_details')
							->select('item_var_matrix_details.content', 'item_variation_details.price', 'item_variation_details.giftwrap_price',
										'item_variation_details.shipping_price', 'item_variation_details.stock', 'item_variation_details.swap_img_id',
										'item_swap_image.filename', 'item_swap_image.ext', 'item_swap_image.width', 'item_swap_image.height',
										'item_swap_image.title', 'item_swap_image.l_width', 'item_swap_image.l_height',
										'item_variation_details.price_impact', 'item_variation_details.giftwrap_price_impact',
										'item_variation_details.shipping_price_impact', 'item_variation_details.description',
										'item_swap_image.t_width', 'item_swap_image.t_height', 'item_swap_image.server_url',
										'item_variation_details.is_active', 'item_variation_details.is_default', 'item_var_matrix_attributes.attribute_id')
							->leftjoin('item_var_matrix_attributes', 'item_variation_details.matrix_id', '=' , 'item_var_matrix_attributes.matrix_id')
							->leftjoin('item_var_matrix_details', 'item_variation_details.matrix_id', '=', 'item_var_matrix_details.matrix_id')
							->leftjoin('item_swap_image', 'item_variation_details.swap_img_id', '=', 'item_swap_image.swap_image_id')
							->whereRaw('item_variation_details.item_id = ? AND item_variation_details.matrix_id = ?', array($product_id, $matrix_id))
							->get();

		if(count($item_variation_details_arr) > 0)
		{
			foreach($item_variation_details_arr as $key => $row)
			{
				$labels_arr = explode('#@@#', $row->content);
				$mat_det_arr['attrib_labels'] = $labels_arr;
				$mat_det_arr['price'] = CUtil::formatAmount($row->price);
				$mat_det_arr['giftwrap_price'] = CUtil::formatAmount($row->giftwrap_price);
				$mat_det_arr['shipping_price'] = CUtil::formatAmount($row->shipping_price);
				$mat_det_arr['stock'] = $row->stock;
				$mat_det_arr['swap_img_id'] = $row->swap_img_id;
				$mat_det_arr['filename_thumb'] = $row->filename . 'T.' . $row->ext;
				$mat_det_arr['filename_large'] = $row->filename . 'L.' . $row->ext;
				$mat_det_arr['filename_original'] = $row->filename . '.' . $row->ext;
				$mat_det_arr['changeSwapImgLink'] = URL::action('ProductAddController@getProductSwapImagesList').'?product_id='.$product_id.'&r_fnname=assgnImg';
				$mat_det_arr['changeMatrixSwapImgLink'] = URL::action('ProductAddController@getProductSwapImagesList').'?product_id='.$product_id.'&r_fnname=srcUpdImg';
				$mat_det_arr['title'] =$row->title;
				$mat_det_arr['t_width'] =$row->t_width;
				$mat_det_arr['t_height'] =$row->t_height;
				$mat_det_arr['price_impact'] =$row->price_impact;
				$mat_det_arr['giftwrap_price_impact'] =$row->giftwrap_price_impact;
				$mat_det_arr['shipping_price_impact'] =$row->shipping_price_impact;
				$mat_det_arr['description'] =$row->description;
				$mat_det_arr['is_active'] =$row->is_active;
				$mat_det_arr['is_default'] =$row->is_default;
				$mat_det_arr['attribute_id'] =$row->attribute_id;
			}
		}
		$mat_det_arr['matrix_id'] = $matrix_id;
		$mat_det_arr['show_giftwrap'] = ($this->chkIsUsingGiftwrap($product_id)) ? 1 : 0;;
		$mat_det_arr['show_stock'] = ($this->chkIsDisregardStock($product_id)) ? 0 : 1;
		return $mat_det_arr;
	}

	public function removeMatrixEntryById($product_id, $matrix_id)
	{
		// Remove existing entry
		DB::table('item_var_matrix_attributes')
				->whereRaw('item_id = ? AND matrix_id = ?', array($product_id, $matrix_id))
				->delete();

		DB::table('item_var_matrix_details')
			->whereRaw('item_id = ? AND matrix_id = ?', array($product_id, $matrix_id))
			->delete();

		DB::table('item_variation_details')
			->whereRaw('item_id = ? AND matrix_id = ?', array($product_id, $matrix_id))
			->delete();
	}

	public function updateMatrixDetails($insert_arr)
	{
		$avail_fields = array('product_id', 'matrix_id', 'price', 'giftwrap_price', 'shipping_price', 'stock',
								'swap_img_id', 'description', 'price_impact', 'giftwrap_price_impact', 'shipping_price_impact', 'is_active');
		$field_value = array();
		foreach($avail_fields as $field_name)
		{
			if(isset($insert_arr[$field_name])  && $field_name != 'product_id' && $field_name != 'matrix_id')
			{
				$field_value[$field_name] = $insert_arr[$field_name];
			}
		}
		DB::table('item_variation_details')
			->whereRaw('item_id = ? AND matrix_id = ? ', array($insert_arr['product_id'], $insert_arr['matrix_id']))
			->update($field_value);

	//	$this->populateItemVariationsAjax($insert_arr['item_id']);
	}

	public function updateMatrixContentDetails($insert_arr)
	{
		DB::table('item_var_matrix_details')
			->whereRaw('item_id = ? AND matrix_id = ? ', array($insert_arr['product_id'], $insert_arr['matrix_id']))
			->update(array('is_active' => $insert_arr['is_active']));
	}

	public function setAsDefaultMatrix($data_arr)
	{
		DB::table('item_variation_details')
				->whereRaw('item_id = ? AND matrix_id = ? ', array($data_arr['product_id'], $data_arr['matrix_id']))
				->update(array('is_default' => $data_arr['is_default']));
		if($data_arr['is_default'] == '1'){
			DB::table('item_variation_details')
				->whereRaw('item_id = ? AND matrix_id <> ? ', array($data_arr['product_id'], $data_arr['matrix_id']))
				->update(array('is_default' => '0'));
		}
	}

	public function chkIsDefaultMatrixExist($item_id)
	{
		$defaultData = DB::table('item_variation_details')->whereRaw('is_default = 1 AND is_active = 1 AND item_id = ?', array($item_id))->first();
		if(COUNT($defaultData))
			return true;
		return false;
	}

	public function chkIsVariationStockExist($item_id)
	{
		$rec = DB::table('item_variation_details')->whereRaw('is_default = 1 AND is_active = 1 AND stock > 0 AND item_id = ?', array($item_id))->get();
		if(COUNT($rec) > 0)
			return true;
		return false;
	}

	public function getSelectAction($data_arr)
	{
		$select_action = array('' => Lang::get('common.select_action'),
								'all' => Lang::get('common.edit'),
								'edit_price' => Lang::get('variations::variations.matrix_edit_price'),
								'edit_shipping_fee' =>  Lang::get('variations::variations.matrix_edit_shipping_fee'),
								'edit_swap_image' => Lang::get('variations::variations.matrix_edit_swap_image'));

		if($data_arr['matrix_edit_giftwrap'] == 1)
		{
			$select_action += array('edit_gift_wrapprice' => Lang::get('variations::variations.matrix_edit_giftwrap_fee'));
		}
		if($data_arr['matrix_edit_stock'] == 1)
		{
			$select_action += array('edit_stock' => Lang::get('variations::variations.matrix_edit_stock'));
		}
		return $select_action;
	}

	public function convertHeaderLabelArrToStr($head_label_arr)
	{
		$head_label_str = '';
		if(count($head_label_arr) > 0)
		{
			foreach($head_label_arr as $val)
			{
				$head_label_str .= ($head_label_str == '') ? $val['label'] : ' '.$val['label'];
			}
		}
		return $head_label_str;
	}

	public function getProductSwapImagesList($product_id = 0)
	{
		$resources = array();
		$resources = DB::table('item_swap_image')
						->select('swap_image_id', 'item_id', 'filename', 'ext', 'width', 'height', 'title',
							'l_width', 'l_height', 't_width', 't_height', 'server_url')
						->whereRaw('item_id = ?', array($product_id))
						->orderBy('swap_image_id', 'ASC')->get();
		return $resources;
	}

	public function getSwapImageSrc($file_name)
	{
		$image_src = URL::asset(Config::get('variations::variations.swap_img_folder')).'/'.$file_name;
		return $image_src;
	}

	public function chkIsValidDecreasePriceChange($matrix_price, $item_id)
	{
		$price = 0;
		$item_price_det = DB::table('product_price_groups')->whereRaw('product_id = ?', array($item_id))->first();
		if(COUNT($item_price_det) > 0)
		{
			$price = $item_price_det->discount;
		}

		if(round($price) < round($matrix_price))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function chkIsValidDecreaseGiftPriceChange($matrix_price, $item_id)
	{
		$price = 0;
		$giftwrap_pricing = DB::table('product')->whereRaw('id = ?', array($item_id))->pluck('giftwrap_pricing');

		if(round($giftwrap_pricing) < round($matrix_price))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function getVariationsDetails($variation_id, $user_id)
	{
		$details = array();
		$variation_details = DB::table('variation')->whereRaw('variation_id = ? AND user_id = ?', array($variation_id, $user_id))->first();
		if(count($variation_details))
		{
			$details['variation_id'] = $variation_details->variation_id;
			$details['name'] = $variation_details->name;
			$details['help_text'] = $variation_details->help_text;
			$details['user_id'] = $variation_details->user_id;
			$details['date_added'] = $variation_details->date_added;
		}
		return $details;
	}

	public function deleteSelectedItems($variation_ids)
	{
		$var_list_arr = array_unique($variation_ids);
		foreach($var_list_arr AS $item_index => $var_id)
		{
	        $variation = DB::table('variation')
							->Select('variation_id')
							->whereRaw("variation_id = ? AND user_id = ?", array($var_id, $this->logged_user_id))
							->first();
			if(count($variation) > 0)
			{
				// Remove the variation entry
				DB::table('variation')->whereRaw("variation_id = ? AND user_id = ?", array($var_id, $this->logged_user_id))->delete();

				// remove item_variation_attribute entry
				DB::table('item_variation_attributes')->whereRaw("variation_id = ?", array($var_id))->delete();

				// remove entry from variation_group_items
				DB::table('variation_group_items')->whereRaw("variation_id = ?", array($var_id))->delete();

				$attributes_list = $this->getVariationAttributesList($var_id);
				if(count($attributes_list) > 0)
				{
					foreach($attributes_list as $attribute)
					{
						$attribute_id = $attribute['attribute_id'];
						$this->removeVariationAttributeEntry($attribute_id);
					}
				}
			}
		}
		return true;
	}

	public function addVariationEntry($inputs = array())
	{
		$variation_id = isset($inputs['variation_id']) ? $inputs['variation_id'] : 0;
		if(!$variation_id)
		{
			$name = isset($inputs['name']) ? $inputs['name'] : '';
			$help_text = isset($inputs['help_text']) ? $inputs['help_text'] : '';

			$data_arr = array('name' => $name,
							  'help_text' => $help_text,
							  'user_id' => $this->logged_user_id,
							  'date_added' => DB::raw('NOW()'));
			$variation_id = DB::table('variation')->insertGetId($data_arr);
		}
		$this->addVariationOptionEntry($variation_id, $inputs);
		return $variation_id;
	}

	public function updateVariationEntry($inputs = array())
	{
		$variation_id = isset($inputs['variation_id']) ? $inputs['variation_id'] : 0;
		if($variation_id)
		{
			$name = isset($inputs['name']) ? $inputs['name'] : '';
			$help_text = isset($inputs['help_text']) ? $inputs['help_text'] : '';

			$data_arr = array('name' => $name, 'help_text' => $help_text);
			DB::table('variation')->whereRaw('variation_id = ? AND user_id = ?', array($variation_id, $this->logged_user_id))->update($data_arr);
			$this->updateVariationOptionEntry($variation_id, $inputs);
		}
		return $variation_id;
	}

	public function addVariationOptionEntry($variation_id, $inputs = array())
	{
		$option_key_arr = isset($inputs['option_key']) ? $inputs['option_key'] : array();
		$option_label_arr = isset($inputs['option_label']) ? $inputs['option_label'] : array();
		if(count($option_key_arr) > 0)
		{
			for($i = 0; $i < count($option_key_arr); $i++)
			{
				$attribute_key = trim($option_key_arr[$i]);
				$label = isset($option_label_arr[$i]) ? trim($option_label_arr[$i]) : '';
				if($attribute_key != '' && $label != '')
				{
					$position_id = $this->getAttributePositionId($variation_id);

					$data_arr = array('variation_id' => $variation_id,
										  'attribute_key' => $attribute_key,
										  'label' => $label,
										  'position' => $position_id);
					$attribute_id = DB::table('variation_attributes')->insertGetId($data_arr);
			    }
		     }
		 }
	}

	public function updateVariationOptionEntry($variation_id, $inputs = array())
	{
		$option_key_arr = isset($inputs['option_key']) ? $inputs['option_key'] : array();
		$option_label_arr = isset($inputs['option_label']) ? $inputs['option_label'] : array();

		$assigned_attribs = array();
		// Get Existing attributes id for this variation
		$existing_attribs = $this->getAttributeIdsForVariation($variation_id);
		if(count($option_key_arr) > 0)
		{
			for($i = 0; $i < count($option_key_arr); $i++)
			{
				$attribute_key = isset($option_key_arr[$i]) ? trim($option_key_arr[$i]) : '';
				$label = isset($option_label_arr[$i]) ? trim($option_label_arr[$i]) : '';
				if($attribute_key != '' && $label != '')
				{
					$chk_data_arr = array();
					$chk_data_arr['attribute_key'] = $attribute_key;
					$chk_data_arr['variation_id'] = $variation_id;
					//$attribute_id_val = $this->checkIsAlreadyAddedAttribute($chk_data_arr);
					$attribute_id_val = isset($existing_attribs[$i]) ? $existing_attribs[$i] : 0;
					if($attribute_id_val != 0)
					{
						$assigned_attribs[] = $attribute_id_val;
						$data_arr = array('attribute_key' => $attribute_key, 'label' => $label);
						DB::table('variation_attributes')
							->whereRaw('attribute_id = ? AND variation_id = ?', array($attribute_id_val, $variation_id))
							->update($data_arr);
					}
					else
					{
						$position_id = $this->getAttributePositionId($variation_id);
						$data_arr = array('attribute_key' => $attribute_key,
											'variation_id' => $variation_id,
											'label' => $label,
											'position' => $position_id);
						$attribute_id = DB::table('variation_attributes')->insertGetId($data_arr);
					}
				}
			}
		}
		// Remove unwanted entries
		$del_rec_arr = array_diff($existing_attribs, $assigned_attribs);
		if(!empty($del_rec_arr))
		{
			foreach($del_rec_arr as $id)
			{
				# Remove the existing attributes
				$this->removeVariationAttributeEntry($id);
			}
		}
	}


	public function checkIsAlreadyAddedAttribute($data_arr)
	{
		$attributes_info = DB::table('variation_attributes')
							->Select('attribute_id')
							->whereRaw("attribute_key = ? AND variation_id = ? ", array($data_arr['attribute_key'], $data_arr['variation_id']))
							->first();
		if(count($attributes_info) > 0)
		{
        	return $attributes_info->attribute_id;
		}
		return 0;
	}

	public function removeVariationAttributeEntry($attribute_id)
	{
		DB::table('variation_attributes')->whereRaw('attribute_id	= ?', array($attribute_id))->delete();
		// delete entry from mp_item_var_matrix_attributes
		DB::table('item_var_matrix_attributes')->whereRaw('attribute_id	= ?', array($attribute_id))->delete();
	}

	public function getAttributePositionId($variation_id)
	{
		$pos_id = 0;
		$attributes_pos = DB::table('variation_attributes')
							->Select(DB::Raw("MAX(position) AS pos"))
							->whereRaw("variation_id = ?", array($variation_id))
							->first();
		if(count($attributes_pos) > 0)
		{
			if(isset($attributes_pos->pos))
        		$pos_id = $attributes_pos->pos;
		}
		return $pos_id+1;
	}

	public function getAttributeIdsForVariation($variation_id)
	{
		$ids_arr = array();
		$attributes_ids = DB::table('variation_attributes')
							->Select('attribute_id')
							->whereRaw("variation_id = ?", array($variation_id))
							->get();
		if(count($attributes_ids) > 0)
		{
			foreach($attributes_ids as $row)
			{
				$ids_arr[] = $row->attribute_id;
			}
		}
		return $ids_arr;
	}

	public function getVariationAttributesList($variation_id)
	{
		$attributes_arr = array();
		$attributes = DB::table('variation_attributes')
						->select('attribute_id', 'variation_id', 'attribute_key', 'label', 'position')
						->whereRaw('variation_id = ?', array($variation_id))
						->orderBy('position', 'ASC')
						->get();
		if(count($attributes) > 0)
		{
			foreach($attributes as $key => $values)
			{
				$attributes_arr[$key]['attribute_id'] = $values->attribute_id;
				$attributes_arr[$key]['variation_id'] = $values->variation_id;
				$attributes_arr[$key]['attribute_key'] = $values->attribute_key;
				$attributes_arr[$key]['label'] = $values->label;
				$attributes_arr[$key]['position'] = $values->position;
			}
		}
		return $attributes_arr;
	}

	public function deleteSelectedGroups($variation_ids)
	{
		$var_list_arr = array_unique($variation_ids);
		foreach($var_list_arr AS $item_index => $var_id)
		{
			// Remove the variation entry
			DB::table('variation_group')->whereRaw("variation_group_id = ? AND user_id = ?", array($var_id, $this->logged_user_id))->delete();

			// remove entry from variation_group_items
			DB::table('variation_group_items')->whereRaw("variation_group_id = ?", array($var_id))->delete();

		}
		return true;
	}

	public function getVariationsGroupDetails($variation_group_id, $user_id)
	{
		$details = array();
		$variation_group_details = DB::table('variation_group')
									->whereRaw('variation_group_id = ? AND user_id = ?', array($variation_group_id, $user_id))->first();
		if(count($variation_group_details)) {
			$details['variation_group_id'] = $variation_group_details->variation_group_id;
			$details['variation_group_name'] = $variation_group_details->variation_group_name;
			$details['short_description'] = $variation_group_details->short_description;
			$details['user_id'] = $variation_group_details->user_id;
			$details['date_added'] = $variation_group_details->date_added;
		}
		return $details;
	}

	public function addVariationGroupEntry($inputs = array())
	{
		$variation_group_id = isset($inputs['variation_group_id']) ? $inputs['variation_group_id'] : 0;
		$variation_group_name = isset($inputs['variation_group_name']) ? $inputs['variation_group_name'] : '';
		$short_description = isset($inputs['short_description']) ? $inputs['short_description'] : '';

		$data_arr = array('variation_group_name' => $variation_group_name,
						  'short_description' => $short_description,
						  'user_id' => $this->logged_user_id,
						  'date_added' => DB::raw('NOW()'));
		$variation_group_id = DB::table('variation_group')->insertGetId($data_arr);
		$this->addVariationItemToGroup($variation_group_id, $inputs);
		return $variation_group_id;
	}

	public function updateVariationGroupEntry($inputs = array())
	{
		$variation_group_id = isset($inputs['variation_group_id']) ? $inputs['variation_group_id'] : 0;
		if($variation_group_id)
		{
			$variation_group_name = isset($inputs['variation_group_name']) ? $inputs['variation_group_name'] : '';
			$short_description = isset($inputs['short_description']) ? $inputs['short_description'] : '';

			$data_arr = array('variation_group_name' => $variation_group_name,
							  'short_description' => $short_description,
							  'user_id' => $this->logged_user_id);
			DB::table('variation_group')->whereRaw('variation_group_id = ? AND user_id = ?', array($variation_group_id, $this->logged_user_id))
				->update($data_arr);
			$this->removeVariationsItemsInGroupById($variation_group_id);
			$this->addVariationItemToGroup($variation_group_id, $inputs);
		}
		return $variation_group_id;
	}


	public function addVariationItemToGroup($variation_group_id, $inputs = array())
	{
		if(isset($inputs['assigned_variation']) && !empty($inputs['assigned_variation']))
		{
			$assigned_variation = $inputs['assigned_variation'];
			foreach($assigned_variation AS $assigned_var)
			{
	    		$data_arr = array('variation_group_id' => $variation_group_id, 'variation_id' => $assigned_var);
				$variation_group_items_id = DB::table('variation_group_items')->insertGetId($data_arr);
			}
		}
	}

	public function removeVariationsItemsInGroupById($variation_group_id)
	{
		DB::table('variation_group_items')->whereRaw('variation_group_id = ?', array($variation_group_id))->delete();
	}

	public function populateUserVariationList($user_id)
	{
		$variations_list = DB::table('variation')
			        		->whereRaw('user_id = ?', array($user_id))
			        		->lists('name', 'variation_id');
		return $variations_list;
	}

	public function fetchVariationsInGroupByGroupId($group_id)
	{
		$variations_list = DB::table('variation_group_items')
								->select('variation.variation_id as variation_id_al', 'variation.name as name_al')
								->join('variation', 'variation_group_items.variation_id', '=' , 'variation.variation_id')
								->whereRaw('variation_group_items.variation_group_id = ?', array($group_id))
								->lists('name_al', 'variation_id_al');
		return $variations_list;
	}

	public function isItemAllowVariations($product_id=0)
	{
		if (isset($this->item_allow_variations))
			return $this->item_allow_variations;

		if(CUtil::chkIsAllowedModule('variations'))
		{
			$rec = DB::table('product')
			       		->whereRaw('id = ? AND use_variation = 1 AND is_downloadable_product= \'No\' ', array($product_id))->first();
			if(COUNT($rec) > 0)
			{
				$this->item_allow_variations = true;
				return true;
			}
		}
		$this->item_allow_variations = false;
		return true;
	}

	public function isItemAllowGiftwrap($product_id=0)
	{
		if (isset($this->item_allow_variations))
			return $this->item_allow_variations;

		if(CUtil::chkIsAllowedModule('variations'))
		{
			$rec = DB::table('product')
			       		->whereRaw('id = ? AND use_variation = 1 AND is_downloadable_product= \'No\' ', array($product_id))->first();
			if(COUNT($rec) > 0)
			{
				$this->item_allow_variations = true;
				return true;
			}
		}
		$this->item_allow_variations = false;
		return true;
	}


	public function populateVariationAttributes($item_id, $matrix_id=0, $user_id)
	{
		$respArr = $itemVarDetailsArr = $default_attribs = array();

		$default_details = DB::table('item_var_matrix_attributes')
							->leftjoin('item_variation_details', 'item_var_matrix_attributes.matrix_id', '=' , 'item_variation_details.matrix_id')
							->whereRaw('item_variation_details.item_id = ? ', array($item_id));
		if($matrix_id != 0)
		{
			$default_details = $default_details->whereRaw('item_var_matrix_attributes.matrix_id = ? ', array($matrix_id));
		}
		else
		{
			$default_details = $default_details->whereRaw('item_variation_details.is_default = \'1\' ');
		}
		$default_details = $default_details->get();
		if(COUNT($default_details) > 0)
		{
			foreach($default_details AS $row)
			{
				$default_attribs[] = $row->attribute_id;
			}
		}

		$this->attrib_labels_arr = $var_details = array();

		$variations_list = DB::table('variation')->whereRaw('user_id = ? ', array($user_id))->get();
		if(COUNT($variations_list) > 0)
		{
			foreach($variations_list as $variations)
			{
				$var_details[$variations->variation_id]['name'] = $variations->name;
				$var_details[$variations->variation_id]['help_text'] = $variations->help_text;
			}
		}

		$item_variation_attributes_arr = DB::table('item_variation_attributes')
								->select('item_variation_attributes.attribute_id', 'variation_attributes.label',
								'item_variation_attributes.variation_id', 'item_variation_attributes.attribute_id')
								->join('variation_attributes', 'item_variation_attributes.attribute_id', '=' , 'variation_attributes.attribute_id')
								->whereRaw('item_variation_attributes.item_id = ? AND is_active = \'1\' ', array($item_id));

		$variation_list = $item_variation_attributes_arr->orderBy('variation_attributes.position')->get();

		if(count($variation_list) > 0)
		{
			foreach($variation_list as $key => $row)
			{
				$itemVarDetailsArr[$row->variation_id]['variation_id'] = $row->variation_id;
				$itemVarDetailsArr[$row->variation_id]['name'] = isset($var_details[$row->variation_id]['name']) ? $var_details[$row->variation_id]['name'] : "";
				$itemVarDetailsArr[$row->variation_id]['help_text'] = isset($var_details[$row->variation_id]['help_text']) ? $var_details[$row->variation_id]['help_text'] : "";
				$itemVarDetailsArr[$row->variation_id]['help_text_url'] = '';
				$itemVarDetailsArr[$row->variation_id]['attrirb_det']['label'][] = $row->label;
				$itemVarDetailsArr[$row->variation_id]['attrirb_det']['attribute_id'][] = $row->attribute_id;

			}
		}
		$show_variation = (COUNT($itemVarDetailsArr) > 0) ? 1 : 0;
		$item_matr_details = array();
		foreach($itemVarDetailsArr AS $varKey=>$itemVars)
		{
			$item_matr_details[$varKey]['variation_id'] = $itemVars['variation_id'];
			$item_matr_details[$varKey]['name'] = $itemVars['name'];
			$item_matr_details[$varKey]['help_text'] = $itemVars['help_text'];
			$item_matr_details[$varKey]['help_text_url'] = $itemVars['help_text_url'];
			$inc = 0;
			foreach($itemVars['attrirb_det']['attribute_id'] AS $itemVarAttribs)
			{
				$item_matr_details[$varKey]['attrirb_det'][$inc]['attribute_id'] = $itemVarAttribs;
				$item_matr_details[$varKey]['attrirb_det'][$inc]['label'] = $itemVars['attrirb_det']['label'][$inc];
				$item_matr_details[$varKey]['attrirb_det'][$inc]['high_light'] = (in_array($itemVarAttribs, $default_attribs) ? 1 : 0);
				$inc++;
			}
		}
		$respArr['variation_list'] = $item_matr_details;
		$respArr['show_variation'] = $show_variation;
		return $respArr;
	}

	public function getItemMatrixDetailsArr($product_id, $default_shipping_fee=0, $price_before_discount = 0)
	{
		$matrix_data_arr = $def_img_det_arr = array();

		$site_default_currency = Config::get('generalConfig.site_default_currency');
		$ViewProductService = new ViewProductService();

		$rev_product_obj = Products::initialize($product_id);
		$rev_product_obj->setIncludeDeleted(true);
		$rev_product_obj->setIncludeBlockedUserProducts(true);
		//$rev_product_details = $rev_product_obj->getProductDetails();
		//$product_url = $productService->getProductViewURL($rev['product_id'], $rev_product_details);
		$p_img_arr = $rev_product_obj->getProductImage($product_id);
		//$p_thumb_img = $productService->getProductDefaultThumbImage($rev['product_id'], 'small', $p_img_arr);

		if (COUNT($p_img_arr) >0)
		{
			$def_img_det_arr['default_img'] = $p_img_arr['default_img'];
			$def_img_det_arr['default_img_ext'] = $p_img_arr['default_ext'];
			$def_img_det_arr['default_img_width'] = $p_img_arr['default_width'];
			$def_img_det_arr['default_img_height'] = $p_img_arr['default_height'];
			$def_img_det_arr['default_img_title'] = $p_img_arr['default_title'];
		}

		$matrix_data = DB::table('item_variation_details')
							->select('item_variation_details.matrix_id', 'variation_attributes.label', 'item_variation_details.price',
									'item_variation_details.giftwrap_price', 'item_variation_details.shipping_price',
									'item_variation_details.stock', 'item_variation_details.swap_img_id',
									'item_variation_details.price_impact', 'item_variation_details.giftwrap_price_impact',
									'item_variation_details.shipping_price_impact', 'item_variation_details.shipping_price',
									'item_swap_image.filename', 'item_swap_image.ext', 'item_swap_image.width', 'item_swap_image.height',
									'item_swap_image.title', 'item_swap_image.l_width', 'item_swap_image.l_height',
									'item_swap_image.t_width', 'item_swap_image.t_height', 'item_swap_image.server_url', 'item_variation_details.is_default',
									'item_variation_details.is_active', 'variation_attributes.attribute_id')
							->leftjoin('item_var_matrix_attributes', 'item_variation_details.matrix_id', '=' , 'item_var_matrix_attributes.matrix_id')
							->leftjoin('variation_attributes', 'item_var_matrix_attributes.attribute_id', '=' , 'variation_attributes.attribute_id')
							->leftjoin('item_swap_image', 'item_variation_details.swap_img_id', '=' , 'item_swap_image.swap_image_id')
							->whereRaw('item_variation_details.item_id = ?', array($product_id))
							->get();
		$inc =0;
		$file_path = URL::asset(Config::get('variations::variations.swap_img_folder')).'/';
		if(COUNT($matrix_data) > 0)
		{
			foreach($matrix_data AS $row)
			{
				$matId = $row->matrix_id;

				if($row->is_default == 1 && $row->swap_img_id == 0)
				{
					if($def_img_det_arr['default_img_ext'] != 0 || $def_img_det_arr['default_img_ext'] != ''){
						$item_image_folder = URL::asset(Config::get("webshoppack.photos_folder"))."/";
						$def_file_path	= $item_image_folder.$def_img_det_arr['default_img'].'L.'.$def_img_det_arr['default_img_ext'];
						$matrix_data_arr[$matId]['large_img_src'] =  $def_file_path;
						$matrix_data_arr[$matId]['large_img_dim'] = $def_img_det_arr['default_img_width'].'x' . $def_img_det_arr['default_img_height'];
						$matrix_data_arr[$matId]['thumb_img_src'] = $item_image_folder.$def_img_det_arr['default_img'].'T.'.$def_img_det_arr['default_img_ext'];
						$matrix_data_arr[$matId]['full_img_src']  = $item_image_folder.$def_img_det_arr['default_img'].'.'.$def_img_det_arr['default_img_ext'];
						$matrix_data_arr[$matId]['title'] 		  = $def_img_det_arr['default_img_title'];
						$matrix_data_arr[$matId]['t_width'] 	  = $def_img_det_arr['default_img_width'];
						$matrix_data_arr[$matId]['t_height'] 	  = $def_img_det_arr['default_img_height'];
					}else{
						$cfg_thumb_width = Config::get("webshoppack.photos_thumb_width");
						$cfg_thumb_height = Config::get("webshoppack.photos_thumb_height");
						$matrix_data_arr[$matId]['large_img_dim'] = $cfg_thumb_width.'x' . $cfg_thumb_height;
						$matrix_data_arr[$matId]['thumb_img_src'] = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_thumb_no_image");
						$matrix_data_arr[$matId]['large_img_src'] = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image");
						$matrix_data_arr[$matId]['full_img_src'] = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_small_no_image");
						$matrix_data_arr[$matId]['title'] = trans('viewProduct.no_image');
						$matrix_data_arr[$matId]['t_width'] = $cfg_thumb_width;
						$matrix_data_arr[$matId]['t_height'] = $cfg_thumb_height;
					}
				}
				else
				{
					$matrix_data_arr[$matId]['large_img_dim'] = ($row->l_width != 0 && $row->l_height != 0) ? $row->l_width.'x' . $row->l_height : "";
					$matrix_data_arr[$matId]['large_img_src'] = $matrix_data_arr[$matId]['thumb_img_src'] =
					$matrix_data_arr[$matId]['full_img_src']  = $matrix_data_arr[$matId]['title'] 	=
					$matrix_data_arr[$matId]['t_width'] 	  = $matrix_data_arr[$matId]['t_height'] = '';

					if($row->filename != '' && $row->ext != '')
					{
						$matrix_data_arr[$matId]['large_img_src'] =  $file_path.$row->filename.'L.'. $row->ext ;
						$matrix_data_arr[$matId]['thumb_img_src'] = $file_path.$row->filename . 'T.' . $row->ext ;
						$matrix_data_arr[$matId]['full_img_src'] = $file_path.$row->filename . '.' . $row->ext;
						$matrix_data_arr[$matId]['title'] 	= $row->title;
						$matrix_data_arr[$matId]['t_width'] 	= $row->t_width;
						$matrix_data_arr[$matId]['t_height'] = $row->t_height;
					}
				}

				$matrix_data_arr[$matId]['matrix_id'] =$row->matrix_id;
				$matrix_data_arr[$matId]['attrib_id'][] =$row->attribute_id;
				// Fetched price based on matrix
				$price_group = $ViewProductService->getPriceGroupsDetailsNew($product_id, $this->logged_user_id, 1, $row->matrix_id);
				$variation_price = $price_group['discount'];
				$variation_org_price = $price_group['price'];
				$before_discount = $price_before_discount;
				$variation_shipping_fee = $default_shipping_fee;
				$variation_deal_value = 0;
				if($row->price_impact != '')
				{
					switch($row->price_impact)
					{
						case 'increase':
							$variation_deal_value += $row->price;
							$variation_org_price += $row->price;
							$before_discount = ($price_before_discount) ? $price_before_discount + $row->price : 0;
							break;
						case 'decrease':
							$variation_deal_value += $row->price;
							$variation_org_price += $row->price;
							$before_discount = ($price_before_discount) ? $price_before_discount + $row->price : 0;
							break;
					}
					$variation_deal_value += $price_group['discount_usd'];
				}
				if($row->shipping_price_impact != '' && $default_shipping_fee >= 0)
				{
					switch($row->shipping_price_impact)
					{
						case 'increase':
							$variation_shipping_fee = $default_shipping_fee + $row->shipping_price;
							break;
						case 'decrease':
							$variation_shipping_fee = $default_shipping_fee + $row->shipping_price;
							break;
					}
				}
				$matrix_data_arr[$matId]['variation_price_default'] = $variation_shipping_fee;
				$variation_shipping_fee = ($variation_shipping_fee < 0) ? '' : $variation_shipping_fee;
				$net_amount = $variation_shipping_fee + $variation_price;
				$total_product_price_val = $default_shipping_fee + $variation_price;
				$matrix_data_arr[$matId]['price'] = addslashes(CUtil::convertAmountToCurrency($variation_price, $site_default_currency, '', true));
				$matrix_data_arr[$matId]['variation_shipping_fee'] = addslashes(CUtil::convertAmountToCurrency($variation_shipping_fee, $site_default_currency, '', true));
				$matrix_data_arr[$matId]['variation_price'] = $variation_price;
				$matrix_data_arr[$matId]['shipping_fee'] = addslashes(CUtil::convertAmountToCurrency($total_product_price_val, $site_default_currency, '', true));
				$matrix_data_arr[$matId]['net_price'] = addslashes(CUtil::convertAmountToCurrency($net_amount, $site_default_currency, '', true));
				$matrix_data_arr[$matId]['variation_org_price'] = addslashes(CUtil::convertAmountToCurrency($variation_org_price, $site_default_currency, '', true));
				$matrix_data_arr[$matId]['stock'] = $row->stock;
				$matrix_data_arr[$matId]['before_discount'] = addslashes(CUtil::convertAmountToCurrency($before_discount, $site_default_currency, '', true));
				$matrix_data_arr[$matId]['variation_deal_value'] = addslashes(CUtil::convertAmountToCurrency($variation_deal_value, $site_default_currency, '', true));
				$inc++;
			}
		}

		$matrix_details_arr = $matrix_data_arr;
		return json_encode($matrix_details_arr);
	}

	public function populateVariationAttributesByMatrixId($item_id, $matrix_id=0, $user_id)
	{
		$itemVarDetailsArr = $selected_attribs = $return_arr = array();

		$return_arr['variation_available'] = 0;
		$this->variation_name_arr = array();
		$variations_list = DB::table('variation')->whereRaw('user_id = ? ', array($user_id))->get();
		if(COUNT($variations_list) > 0)
		{
			foreach($variations_list as $variations)
			{
				$this->variation_name_arr[$variations->variation_id] = $variations->name;
			//	$var_details[$variations->variation_id]['help_text'] = $variations->help_text;
			}
		}
		$this->attrib_labels_arr = array();
		//$attributes_arr = DB::table('variation_attributes')->whereRaw('user_id = ? ', array($user_id))->get();
		$attributes_arr = DB::table('variation_attributes')->get();
		if(count($attributes_arr) > 0)
		{
			foreach($attributes_arr as $key => $row)
			{
				$this->attrib_labels_arr[$row->attribute_id] = $row->label;
			}
		}

		$item_variation_attributes_arr = DB::table('item_var_matrix_attributes')
								->select('item_var_matrix_attributes.attribute_id','item_variation_details.price',
								'item_variation_details.price_impact', 'item_variation_details.giftwrap_price',
								'item_variation_details.giftwrap_price_impact', 'item_variation_details.shipping_price',
								'item_variation_details.shipping_price_impact','item_variation_details.stock',
								'item_variation_details.swap_img_id','item_variation_attributes.variation_id')
								->leftjoin('item_variation_details', 'item_var_matrix_attributes.matrix_id', '=' , 'item_variation_details.matrix_id')
								->leftjoin('item_variation_attributes', 'item_var_matrix_attributes.attribute_id', '=', 'item_variation_attributes.attribute_id')
								->whereRaw('item_variation_details.item_id = ? AND item_var_matrix_attributes.matrix_id = ? ', array($item_id, $matrix_id))
								->groupBy('item_variation_attributes.attribute_id');
		$item_variation_attributes_arr = $item_variation_attributes_arr->get();

		if(COUNT($item_variation_attributes_arr) > 0)
		{
			foreach($item_variation_attributes_arr as $attr)
			{
				$itemVarDetailsArr[$matrix_id]['price'] = $attr->price;
				$itemVarDetailsArr[$matrix_id]['price_impact'] = $attr->price_impact;
				$itemVarDetailsArr[$matrix_id]['giftwrap_price'] = $attr->giftwrap_price;
				$itemVarDetailsArr[$matrix_id]['giftwrap_price_impact'] = $attr->giftwrap_price_impact;
				$itemVarDetailsArr[$matrix_id]['shipping_price'] = $attr->shipping_price;
				$itemVarDetailsArr[$matrix_id]['shipping_price_impact'] = $attr->shipping_price_impact;
				$itemVarDetailsArr[$matrix_id]['stock'] = $attr->stock;
				$itemVarDetailsArr[$matrix_id]['swap_img_id'] = $attr->swap_img_id;
				$itemVarDetailsArr[$matrix_id]['attribute_id'][] = $attr->attribute_id;
				$itemVarDetailsArr[$matrix_id]['variation_id'][] = $attr->variation_id;
				$itemVarDetailsArr[$matrix_id]['name'][] = isset($this->variation_name_arr) ? $this->variation_name_arr : "";
				$itemVarDetailsArr[$matrix_id]['label'][] = isset($this->attrib_labels_arr) ? $this->attrib_labels_arr : "";
			}
		}

		if(COUNT($itemVarDetailsArr) > 0)
		{
			$return_arr['variation_available'] = 1;
			foreach($itemVarDetailsArr AS $varKey=>$itemVars)
			{
				$return_arr['price'] = $itemVars['price'];
				$return_arr['price_impact'] = $itemVars['price_impact'];
				$return_arr['giftwrap_price'] = $itemVars['giftwrap_price'];
				$return_arr['giftwrap_price_impact'] = $itemVars['giftwrap_price_impact'];
				$return_arr['shipping_price'] = $itemVars['shipping_price'];
				$return_arr['shipping_price_impact'] = $itemVars['shipping_price_impact'];
				$return_arr['stock'] = $itemVars['stock'];
				$return_arr['swap_img_id'] = $itemVars['swap_img_id'];

				$swap_img = $swap_img_ext = $t_width = $t_height =  $img_src = '';
				if($itemVars['swap_img_id'] > 0)
				{
					$swapImgDet = DB::table('item_swap_image')->whereRaw('swap_image_id = ?', array($itemVars['swap_img_id']))->first();
					if(COUNT($swapImgDet) > 0)
					{
						$swap_img = $swapImgDet->filename;
						$swap_img_ext = $swapImgDet->ext;
						$t_width = $swapImgDet->t_width;
						$t_height = $swapImgDet->t_height;
						if($swap_img != "" && $swap_img_ext != "")
							$img_src = URL::asset(Config::get('variations::variations.swap_img_folder')).'/'.$swap_img.'T.'.$swap_img_ext;
					}
				}

				$return_arr['swap_img'] = $swap_img;
				$return_arr['swap_img_ext'] = $swap_img_ext;
				$return_arr['swap_img_src'] = $img_src;
				$return_arr['t_width'] = $t_width;
				$return_arr['t_height'] = $t_height;

				$inc = 0;
				foreach($itemVars['variation_id'] AS $itemVarAttribs)
				{
					$return_arr['attrirb_det'][$inc]['variation_id'] = $itemVarAttribs;
					$return_arr['attrirb_det'][$inc]['attribute_id'] = $itemVars['attribute_id'][$inc];
					$return_arr['attrirb_det'][$inc]['name'] = $itemVars['name'][$inc];
					$return_arr['attrirb_det'][$inc]['name'] = $this->variation_name_arr[$itemVarAttribs];
					$return_arr['attrirb_det'][$inc]['label'] = $itemVars['label'][$inc];
					$return_arr['attrirb_det'][$inc]['label'] = $this->attrib_labels_arr[$itemVars['attribute_id'][$inc]];
					$inc++;
				}
			}
		}
		return $return_arr;
	}

	public function chkIsAllowedVariationStock($item_id, $matrix_id, $qty)
	{
		$stock_avail_det = DB::table('item_variation_details')->whereRaw('item_id = ? AND matrix_id = ?', array($item_id, $matrix_id))->first();
		if(COUNT($stock_avail_det) > 0)
		{
			$stock_avail = $stock_avail_det->stock;
			if($stock_avail >= $qty)
			{
				return true;
			}
		}
		return false;
	}

	public function updateProductVariationSold($item_id, $qty=1, $matrix_id=0, $action='add')
	{
		if($matrix_id > 0)
		{
			$stock_avail_det = DB::table('item_variation_details')->whereRaw('item_id = ? AND matrix_id = ?', array($item_id, $matrix_id))->first();
			if(COUNT($stock_avail_det) > 0)
			{
				$stock_avail = $stock_avail_det->stock;
				if($action == 'Refund')
				{
					$stock_value = $stock_avail + $qty;
				}
				else
				{
					if($stock_avail > 0 && $stock_avail >= $qty)
						$stock_value = $stock_avail - $qty;
					else
						$stock_value = 0;
				}
				DB::table('item_variation_details')->whereRaw('item_id = ? AND matrix_id = ?', array($item_id, $matrix_id))
							->update(array('stock' => $stock_value ));
			}
		}
	}

	public function updateItemVariationAttributes($product_id, $attribute_id, $is_active)
	{
		DB::table('item_variation_attributes')
			->whereRaw('attribute_id = ? AND item_id = ? ', array($attribute_id, $product_id))
			->update(array('is_active' => $is_active));
	}

	public function removeItemVariationAttributes($product_id, $attribute_id)
	{
		DB::table('item_variation_attributes')
			->whereRaw('attribute_id = ? AND item_id = ? ', array($attribute_id, $product_id))
			->delete();
	}
	public function salesVariationCount($matrix_id)
	{
		$variation_sales_counts = DB::table('shop_order_item')
							->join('invoices', 'shop_order_item.id', '=', 'invoices.order_item_id')
							->whereNotIn('invoices.invoice_status', array('pending', 'refunded'))
							->whereRaw('shop_order_item.matrix_id = ?', array($matrix_id))
							->sum('shop_order_item.item_qty');
		return $variation_sales_counts;
	}
	public function shippingFeesAjax($product_id, $default_shipping_fee = '0'){
		$matrix_data = DB::table('item_variation_details')
						->select('item_variation_details.matrix_id', 'variation_attributes.label', 'item_variation_details.price',
								'item_variation_details.giftwrap_price', 'item_variation_details.shipping_price',
								'item_variation_details.stock', 'item_variation_details.swap_img_id',
								'item_variation_details.price_impact', 'item_variation_details.giftwrap_price_impact',
								'item_variation_details.shipping_price_impact', 'item_variation_details.shipping_price',
								'item_variation_details.is_default',
								'item_variation_details.is_active', 'variation_attributes.attribute_id')
						->leftjoin('item_var_matrix_attributes', 'item_variation_details.matrix_id', '=' , 'item_var_matrix_attributes.matrix_id')
						->leftjoin('variation_attributes', 'item_var_matrix_attributes.attribute_id', '=' , 'variation_attributes.attribute_id')
						->whereRaw('item_variation_details.item_id = ?', array($product_id))
						->get();
						$variation_shipping_fee = 0;
						if(COUNT($matrix_data) > 0)
						{
							foreach($matrix_data AS $row)
							{
								if($row->shipping_price_impact != '')
								{
									switch($row->shipping_price_impact)
									{
										case 'increase':
											$variation_shipping_fee = $default_shipping_fee + $row->shipping_price;
											break;
										case 'decrease':
											$variation_shipping_fee = $default_shipping_fee + $row->shipping_price;
											break;
									}
								}
							}
						}
						return $variation_shipping_fee;
		}

	public function getItemMatrixDetailsShippingFee($product_id, $matrix_id)
	{
		$return_shipping_amt = '0';
		$matrix_data = DB::table('item_variation_details')
					   ->select('item_variation_details.shipping_price')
					   ->whereRaw('item_variation_details.item_id = ? AND item_variation_details.matrix_id = ?', array($product_id, $matrix_id))
					   ->first();
		if(isset($matrix_data))
			$return_shipping_amt = $matrix_data->shipping_price;
		return $return_shipping_amt;
	}

}