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
//@added by manikandan_133at10
class AdminManageShopController extends BaseController
{
	public function getIndex()
	{
		$shop_obj = Products::initializeShops();
		$per_page	= Config::get('webshoppack.shop_per_page_list');
		$this->manageShopService = new AdminManageShopService();
		$d_arr = $user_list = $user_details = $shop_details = array();

		$this->manageShopService->setShopSrchArr(Input::All());
		$this->manageShopService->setShopFilter($shop_obj);
		$shop_obj->setShopPagination($per_page);
		$shop_list = $shop_obj->getShopList();

		foreach($shop_list AS $shopKey => $shop) {
			$shop_details[$shopKey]= $shop;
		}

		$d_arr['allow_change_status'] = true;
		$d_arr['allow_edit_user'] = true;
		$country_arr = $this->manageShopService->getCountryList();
		return View::make('admin.listShops', compact('d_arr', 'shop_list', 'shop_details','country_arr','shop_obj'));
	}


	public function getChangestatus()
	{
		$shop_obj = Products::initializeShops();
		$this->manageShopService = new AdminManageShopService();

		if(Input::has('shop_id') && Input::has('action'))
		{
			$shop_id = Input::get('shop_id');
			$action = Input::get('action');
			$success_msg = "";
			//echo "Yes this was called", $user_id," action ", $action;
			$success_msg = $this->manageShopService->updateShopFeaturedByAdmin($shop_id, $action, $shop_obj);
		}
		return Redirect::to('admin/shop')->with('success_message', $success_msg);
	}

}