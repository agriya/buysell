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
class AdminManageStocksController extends BaseController {
	function __construct()
	{
		parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
    }

    function getIndex(){
    	$p_details = $d_arr = $stocks_list = $stock_details = array();
    	$d_arr['p_id'] = 0;
    	//$d_arr['stock_id'] = 0;
    	$d_arr['mode'] = 'add';
    	$d_arr['edit_url'] = '';
    	$error_msg = '';
    	//To add/edit product details
		$p_id = (Input::has('product_id')) ? Input::get('product_id'): 0;

		if($p_id != '')
		{
			//To validate product id
			$prod_obj = Products::initialize($p_id);
			try
			{
				$d_arr['p_id'] = $p_id;
				$d_arr['edit_url'] = URL::to('admin/manage-stocks').'?product_id='.$p_id;
				/*$stock_id = (Input::has('stock_id')) ? Input::get('stock_id'): 0;
				$d_arr['stock_id'] = $stock_id;
				if($stock_id > 0) {
					$d_arr['mode'] = 'edit';
					$stock_details = $prod_obj->getProductStocksById($p_id, $stock_id);
					if (!$stock_details) {
						$error_msg = 'Invalid stock id';
						return Redirect::to($d_arr['edit_url'])->with('stock_error_message', $error_msg);
					}
				}*/

				$stock_details_arr = $prod_obj->getProductStocksList($p_id);
//				if(!$stock_details_arr) {
//					$error_msg = 'Invalid stock id';
//					return Redirect::to($d_arr['edit_url'])->with('stock_error_message', $error_msg);
//				}

				$stock_details = array();
				foreach($stock_details_arr as $stock) {
					if($stock['stock_country_id'] == 38) {
						$stock_details['stock_country_id_china'] = $stock['stock_country_id'];
						$stock_details['quantity_china'] = $stock['quantity'];
						$stock_details['serial_numbers_china'] = $stock['serial_numbers'];
					}
					if($stock['stock_country_id'] == 153) {
						$stock_details['stock_country_id_pak'] = $stock['stock_country_id'];
						$stock_details['quantity_pak'] = $stock['quantity'];
						$stock_details['serial_numbers_pak'] = $stock['serial_numbers'];
					}
				}

				$p_details = $prod_obj->getProductDetails();
				$countries = array('' => trans('common.select_a_country'));
				$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc', false);
				$countries_list = $countries+$countries_arr;
				$d_arr['countries_list'] = $countries_list;

				$stocks_list = $prod_obj->getProductStocksList($p_id);
			}
			catch (Exception $e)
			{
				if($e instanceOf ProductNotFoundException)
				{
					$error_msg = $e->getMessage();
				}
				else  if($e instanceOf InvalidProductIdException)
				{
					$error_msg = $e->getMessage();
				}
			}
		}
		else
		{
			$error_msg = 'Invalid request';
		}


		//Render user alert mesage
		if($error_msg != '')
		{
			Session::put('error_message', $error_msg);
			return Redirect::to('admin/product/list')->with('error_message', $error_msg);
		}
		$this->header->setMetaTitle(trans('meta.manage_stocks'));
		return View::make('admin/manageStocks', compact('p_details', 'stocks_list', 'stock_details', 'd_arr'));
    }

    function postIndex(){
    	$input_arr = Input::All();
    	//echo '<pre>';print_r($input_arr);'</pre>';exit;
    	$qry_str = $error_msg = $success_msg = '';
		//To add/edit product details
		$p_id = (Input::has('p_id')) ? Input::get('p_id'): 0;
		//$stock_id = (Input::has('stock_id')) ? Input::get('stock_id'): 0;
		if($p_id != '')
		{
			//To validate product id
			if(is_numeric($p_id)) {
				$prod_obj = Products::initialize($p_id);
				try
				{
					$china_country_id = 38;//country id for china in currency_exchange_rate tbl
					$pak_country_id = 153;//country id for china in currency_exchange_rate tbl
					$qry_str = 'product_id='.$p_id;
					$data_arr = array();
					$response = array();
					if(isset($input_arr['stock_country_id_china']) && $input_arr['stock_country_id_china'] == $china_country_id) {
						$data_arr['product_id'] = $p_id;
						$data_arr['stock_country_id'] = $input_arr['stock_country_id_china'];
						$data_arr['quantity'] = $input_arr['quantity_china'];
						$data_arr['serial_numbers'] = $input_arr['serial_numbers_china'];
						$response = $prod_obj->saveStocks($data_arr);
					}
					if(isset($input_arr['stock_country_id_pak']) && $input_arr['stock_country_id_pak'] == $pak_country_id) {
						$data_arr['product_id'] = $p_id;
						$data_arr['stock_country_id'] = $input_arr['stock_country_id_pak'];
						$data_arr['quantity'] = $input_arr['quantity_pak'];
						$data_arr['serial_numbers'] = $input_arr['serial_numbers_pak'];
						$response = $prod_obj->saveStocks($data_arr);
					}
					if ($response) {
						$json_data = json_decode($response, true);
						if($json_data['status'] == 'error')
						{
							foreach($json_data['error_messages'] AS $err_msg)
							{
								$error_msg .= "<p>".$err_msg."</p>";
							}
							/*if($stock_id > 0) {
								$qry_str .= '&stock_id='.$input_arr['stock_id'];
							}*/
							Session::put('error_message', $error_msg);
							return View::make('admin/manageStocks', compact('p_details', 'stocks_list', 'stock_details', 'd_arr'));
							//return Redirect::to('admin/manage-stocks?'.$qry_str)->with('error_message', $error_msg)->withInput();
						}
					}

					if(!isset($input_arr['stock_country_id_china'])) {
						$response = $prod_obj->deleteProductStocksByProductCountry($p_id, $china_country_id);
					}
					if(!isset($input_arr['stock_country_id_pak'])) {
						$response = $prod_obj->deleteProductStocksByProductCountry($p_id, $pak_country_id);
					}

					$success_msg = 'Stocks updated sucessfully';
				}
				catch (Exception $e)
				{
					if($e instanceOf ProductNotFoundException)
					{
						$error_msg = $e->getMessage();
					}
					else  if($e instanceOf InvalidProductIdException)
					{
						$error_msg = $e->getMessage();
					}
				}
			}
		}
		else
		{
			$error_msg = 'Invalid request';
		}

		//Render user alert mesage
		if($error_msg != '')
		{
			return Redirect::to('admin/manage-stocks?'.$qry_str)->with('error_message', $error_msg)->withInput();
		}

		return Redirect::to('admin/manage-stocks?'.$qry_str)->with('success_message', $success_msg);
    }

    public function postStockActions()
	{
		$action = Input::get('action');
		$p_id = Input::get('product_id');
		$stock_id = Input::get('stock_id');

		$prod_obj = Products::initialize($p_id);
		$response = $prod_obj->deleteProductStocks($stock_id);
		if($response)
		{
			echo json_encode(array(	'result'=>'success','stock_id'=> $stock_id));
		}
		else
		{
			echo json_encode(array(	'result'=>'failed','shipping_id'=> $stock_id));
		}
		exit;
	}
}