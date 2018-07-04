<?php
class AdminSalesController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
    {
    	$salesReportService = new SalesReportService();
		$inputs = Input::all();
		$product_sales_report = $salesReportService->getSalesReportProductWise(null, $inputs, 20);
		return View::make('admin.salesReportAllProducts', compact('product_sales_report'));
	}
	public function getProduct($product_id = null){
		if(is_null($product_id) || !ctype_digit($product_id) || $product_id<=0)
			return Redirect::action('AdminSalesController@getIndex')->with('error_message', 'Select valid product id to view sales report');

		$prod_obj = Products::initialize($product_id);
		$product_details = $prod_obj->getProductDetails();


		$inputs = Input::all();
		$salesReportService = new SalesReportService();
		$product_sales_report = $salesReportService->getSalesReportProductWise($product_id, $inputs, 20);

		$earned_details = $salesReportService->getEarnedDetailsProductWise($product_id);
		return View::make('admin.salesReportSingleProduct', compact('product_sales_report', 'product_details', 'earned_details', 'product_id'));
	}
	public function getMemberWise(){

		$salesReportService = new SalesReportService();
		$inputs = Input::all();
		$owner_sales_report = $salesReportService->getSalesReportMemberWise($inputs, 20);
		return View::make('admin.salesReportAllOwners', compact('owner_sales_report'));
	}
	public function getMember($user_id = null){

		if(is_null($user_id) || !ctype_digit($user_id) || $user_id<=0)
			return Redirect::action('AdminSalesController@getMemberWise')->with('error_message', 'Select valid owner id to view sales report');

		$user_details = CUtil::getUserDetails($user_id);


		$inputs = Input::all();
		$salesReportService = new SalesReportService();
		$owner_sales_report = $salesReportService->getSalesReportSingleMemberWise($user_id, $inputs, 20);


		$earned_details = $salesReportService->getEarnedDetailsMemberWise($user_id);
		return View::make('admin.salesReportSingleOwner', compact('owner_sales_report', 'user_details', 'earned_details', 'user_id'));
	}

}