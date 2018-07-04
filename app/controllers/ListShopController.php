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
class ListShopController extends BaseController
{
	public function __construct()
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->shopService = new ShopService();
	}

	/**
	 * ListShopController::getIndex()
	 *
	 * @return
	 */
	public function getIndex()
	{
		$shop_obj = Products::initializeShops();
		$prod_obj = Products::initialize();

		$this->shopservice = new ListShopService();
		$service_obj = new ProductService;

		$this->shopservice->setListShopsFilterArr();
		$this->shopservice->setListShopsSrchArr(Input::All(), $shop_obj);
		$country_arr = $this->shopservice->getCountryList();

		$list_type = (Input::has('list_type') && Input::get('list_type') != 'recently_added') ? Input::get('list_type') : 'recently_added';
		$shop_obj->setOrderByField($list_type);
		$shop_obj->setShopPagination(20);
		$shops_list = $shop_obj->getShopList();
		return View::make('listShops', compact('shops_list', 'country_arr', 'service_obj', 'shop_obj', 'prod_obj', 'list_type'));
	}
}