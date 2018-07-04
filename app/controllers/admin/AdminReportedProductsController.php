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
class AdminReportedProductsController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$reportedProductService = new ReportedProductService();
		$productService = new ProductService();
		$prod_obj = Products::initialize();
		$inputs = Input::all();
		$reported_products = $reportedProductService->getAllProductReports();
		if($reported_products && !empty($reported_products))
		{
			$perPage = 10;
			$currentPage = Input::get('page') - 1;
			$pagedData = array_slice($reported_products, $currentPage * $perPage, $perPage);
			$reported_products = Paginator::make($pagedData, count($reported_products), $perPage);
		}
		$actions = array('' => trans('common.select'), 'delete_report' => Lang::get('admin/reportedProduct.delete_report'));//'delete_product' => 'Delete Product'
		return View::make('admin.reportedProducts', compact('reported_products', 'productService', 'prod_obj', 'actions'));

	}
	public function getView($product_id = null){
		if(is_null($product_id) || $product_id <= 0)
			return Redirect::action('AdminReportedProductsController@getIndex')->with('error_message', Lang::get('admin/reportedProduct.select_valid_prodcut_to_view_reports'));
		$reportedProductService	= new ReportedProductService();
		$report_details = $reportedProductService->getSingleProductReport($product_id);
		$prod_obj = Products::initialize($product_id);
		$product_details = $prod_obj->getProductDetails();
		$productService = new ProductService();

		return View::make('admin.viewReportedProduct', compact('product_details', 'report_details', 'prod_obj', 'productService'));

	}
	public function postBulkAction()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$reportedProductService	= new ReportedProductService();

			$action = $inputs['action'];
			$action_done = false;
			if(in_array($action, array('delete_report')))//,'delete_product'
			{
				$action_done = $reportedProductService->bulkDeleteReport($inputs['ids']);
			}
			else
				$action_done = false;

			if($action_done)
				return Redirect::back()->with('success_message', Lang::get('admin/reportedProduct.reports_deleted_successfully'));
			else
				return Redirect::back()->with('error_message', Lang::get('admin/reportedProduct.there_are_some_problem_in_executing_selected_action'));
		}else{
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->with('error_message', $error_msg);
		}
	}
}
?>