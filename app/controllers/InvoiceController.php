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
class InvoiceController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
		$this->PayCheckOutService = new PayCheckOutService();
		if(CUtil::chkIsAllowedModule('sudopay')) {
			$this->sudopay_service = new \SudopayService();
			$mode = (Config::get('plugin.sudopay_payment_test_mode')) ? 'test' : 'live';
			$this->sudopay_credential = array(
			    'api_key' => Config::get('plugin.sudopay_'.$mode.'_api_key'),
			    'merchant_id' => Config::get('plugin.sudopay_'.$mode.'_merchant_id'),
			    'website_id' => Config::get('plugin.sudopay_'.$mode.'_website_id'),
			    'secret' => Config::get('plugin.sudopay_'.$mode.'_secret_string')
			);
			$this->sa = new \SudoPay_API($this->sudopay_credential);
			$this->sc = new \SudoPay_Canvas($this->sa);
		}
    }

	public function getIndex()
	{
		$status = (Input::has('status') && Input::get('status') == 'Unpaid') ? Input::get('status'):'Paid';
    	//echo $invoice_status;exit;
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$invoice_details = DB::table('common_invoice')
    					   ->leftjoin('credits_log','common_invoice.reference_id','=','credits_log.credit_id')
    					   ->leftjoin('shop_order','common_invoice.reference_id','=','shop_order.id')
    					   ->select('common_invoice.common_invoice_id','common_invoice.reference_type','common_invoice.user_id','common_invoice.reference_type','common_invoice.reference_id','common_invoice.currency','common_invoice.amount','common_invoice.is_credit_payment','common_invoice.paypal_amount','common_invoice.status','common_invoice.date_paid','common_invoice.date_added','credits_log.credit_id','credits_log.currency','credits_log.amount','credits_log.user_notes','credits_log.date_added','shop_order.date_created','shop_order.total_amount','shop_order.currency','shop_order.order_status','shop_order.date_created','shop_order.id')
    					   ->where('common_invoice.user_id',$logged_user_id);
		if ($status == "Paid") {
			$invoice_details->where('common_invoice.status',"paid");
		} else{
			$invoice_details->where('common_invoice.status',"unpaid");
		}
		$invoice_details = $invoice_details->orderBy('common_invoice_id', 'DESC')
											->paginate(10);
		//echo "<pre>"; print_r($invoice_details); echo "</pre>";exit;
		$get_common_meta_values = Cutil::getCommonMetaValues('my-invoice');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
    	return View::make('myInvoiceList', compact('invoice_details','status'));
	}
	public function getInvoiceDetails($common_invoice_id = Null)
	{
		$i = Input::has('invoice')?Input::get('invoice'):'invoice';
		$s = Input::has('s')?Input::get('s'):'';
		//echo Input::get('s');exit;
		if(is_null($common_invoice_id))
			return Redirect::action('InvoiceController@getIndex')->with('error_message',trans('myPurchases.invalid_invoice_details'));
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$invoice_details = DB::table('common_invoice')
							->select('common_invoice.currency as invoice_currency', 'common_invoice.*', 'credits_log.*', 'shop_order.*')
							->leftjoin('credits_log','common_invoice.reference_id','=','credits_log.credit_id')
							// join shop order when invoice referrence type is products
							->leftjoin('shop_order', function($join){
							 	$join->on('shop_order.id', '=', 'common_invoice.reference_id');
							 	$join->on('common_invoice.reference_type', '=', DB::raw("'Products'"));
							 })

							//->leftjoin('shop_order','common_invoice.reference_id','=','shop_order.id')
							->whereRaw('common_invoice_id = ? AND user_id = ?', array($common_invoice_id, $logged_user_id))->first();

		if(count($invoice_details) <=0 || (isset($invoice_details->user_id) && $invoice_details->user_id !=$logged_user_id))
			return Redirect::action('InvoiceController@getIndex')->with('error_message',trans('myPurchases.invalid_invoice_details'));

		if(isset($invoice_details->status) &&  $invoice_details->status == 'Draft')
			return Redirect::action('InvoiceController@getIndex')->with('error_message',trans('myPurchases.invalid_invoice_details'));

		if(isset($invoice_details->reference_type) &&  $invoice_details->reference_type == 'Products')
			return Redirect::action('InvoiceController@getIndex')->with('error_message',trans('myPurchases.invalid_invoice_details'));

		if($invoice_details->reference_type == 'Products')
			$order_details = $this->PayCheckOutService->checkValidOrderId($invoice_details->reference_id);

		$shipping_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $invoice_details->reference_id));
		$d_arr = array();
		if($invoice_details->status == 'Unpaid' && $invoice_details->reference_type == 'Products') {
			if(CUtil::chkIsAllowedModule('sudopay')) {
				$d_arr['sudopay_service'] = $this->sudopay_service; //sudopay service obj
				$d_arr['sc'] = $this->sc; //sudopay canvas obj
				$d_arr['sa'] = $this->sa; //sudopay api obj
				$d_arr['sudopay_credential'] = $this->sudopay_credential;
				//echo '<pre>';print_r($d_arr['sudopay_credential']);echo '</pre>';exit;
				$plan_details = $this->sa->callGetPlan();
				$sudopay_brand = '';
				$sudopay_fees_payer = 'site';
				if(isset($plan_details['brand']) && $plan_details['brand'] != '') {
					$sudopay_brand = $plan_details['brand'];
					$sudopay_fees_payer = $plan_details['sudopay_fees_payer'];
				}
				if($sudopay_brand == 'SudoPay Branding') {
					$d_arr['is_credit'] = 'No';
					$d_arr['reference_type'] = 'Products';
					$d_arr['logged_user_id'] = $logged_user_id;
					$d_arr['sudopay_fields_arr'] = $this->sudopay_service->getSudopayFieldsArr($invoice_details->reference_id, $order_details, $invoice_details, $d_arr);
				}
				$d_arr['sudopay_brand'] = $sudopay_brand;
				$d_arr['sudopay_fees_payer'] = $sudopay_fees_payer;
			}
		}
		$get_common_meta_values = Cutil::getCommonMetaValues('invoice-details');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('viewInvoice', compact('invoice_details','i','s', 'd_arr', 'shipping_details'));
	}
}
?>