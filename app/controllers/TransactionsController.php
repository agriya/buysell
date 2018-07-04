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
class TransactionsController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
        if(CUtil::chkIsAllowedModule('deals'))
		{
			$this->deal_service = new DealsService();
		}
	}
	public function getIndex()
    {
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$transactionService = new TransactionService();
		$inputs = Input::all();
		$transaction_details = $transactionService->getSiteTransactions($logged_user_id, 'paginate', 20, $inputs);
		$transaction_description = array();
		foreach($transaction_details as $each_transaction){
			if ($each_transaction->user_id == $logged_user_id) {
				$var_user = Lang::get('common.your_lowercase');
			} else {
				$user_details = CUtil::getUserDetails($each_transaction->user_id);
				$var_user = '<a href="'.$user_details['profile_url'].'">'.$user_details['display_name'].'</a>';
			}
			$description = '';
			switch($each_transaction->transaction_key){
				case 'purchase':
					$var_order = '';
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.purchase_credit');
						$var_order = '<a href="'.URL::action('PurchasesController@getSalesOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.purchase_debit');
						$var_order = '<a href="'.URL::action('PurchasesController@getOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$description = str_ireplace('VAR_ORDER', $var_order, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				case 'purchase_cancelled':
					if ($each_transaction->transaction_type == 'credit') {
					} else if ($each_transaction->transaction_type == 'debit') {
					} else {
						$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					break;
				case 'purchase_refunded':
					$var_order = '';
					$var_product = '';
					if($each_transaction->related_transaction_id && $each_transaction->related_transaction_id >0)
					{
						$curr_product_detaiils = array();
						if(!isset($all_product_details[$each_transaction->related_transaction_id]))
						{
							$product_obj = Products::initialize($each_transaction->related_transaction_id);
							$product_obj->setIncludeBlockedUserProducts(true);
							$product_obj->setIncludeDeleted(true);
							$product_det = $product_obj->getProductDetails();
							if(!empty($product_det))
								$product_det['view_url'] = Products::getProductViewUrl($product_det['id'], $product_det);

							$all_product_details[$each_transaction->related_transaction_id] = $product_det;
						}
						$curr_product_detaiils = $all_product_details[$each_transaction->related_transaction_id];
						if(!empty($curr_product_detaiils))
							$var_product = '<a target="_blank" href="'.$curr_product_detaiils['view_url'].'">'.$curr_product_detaiils['product_name'].'</a>';
						else
							$var_product = '<a href="#">Product Deleted</a>';
					}
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.purchase_refunded_credit');
						$var_order = '<a href="'.URL::action('PurchasesController@getOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.purchase_refunded_debit');
						$var_order = '<a href="'.URL::action('PurchasesController@getSalesOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$description = str_ireplace('VAR_ORDER', $var_order, $description);
					$description = str_ireplace('VAR_PRODUCT', $var_product, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				case 'purchase_fee_refunded':
					$var_order = '';
					$var_product = '';
					if($each_transaction->related_transaction_id && $each_transaction->related_transaction_id >0)
					{
						$curr_product_detaiils = array();
						if(!isset($all_product_details[$each_transaction->related_transaction_id]))
						{
							$product_obj = Products::initialize($each_transaction->related_transaction_id);
							$product_obj->setIncludeBlockedUserProducts(true);
							$product_obj->setIncludeDeleted(true);
							$product_det = $product_obj->getProductDetails();
							$product_det['view_url'] = Products::getProductViewUrl($product_det['id'], $product_det);

							$all_product_details[$each_transaction->related_transaction_id] = $product_det;
						}
						$curr_product_detaiils = $all_product_details[$each_transaction->related_transaction_id];
						if(!empty($curr_product_detaiils))
							$var_product = '<a target="_blank" href="'.$curr_product_detaiils['view_url'].'">'.$curr_product_detaiils['product_name'].'</a>';
						else
							$var_product = '<a href="#">Product Deleted</a>';
					}
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.purchase_fee_refunded_credit');
						$var_order = '<a href="'.URL::action('PurchasesController@getOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.purchase_fee_refunded_debit');
						$var_order = '<a href="'.URL::action('PurchasesController@getSalesOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$description = str_ireplace('VAR_ORDER', $var_order, $description);
					$description = str_ireplace('VAR_PRODUCT', $var_product, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				case 'withdrawal':
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.withdrawal_credit');
						if ($each_transaction->payment_type == 'paypal') {
							$description = str_ireplace('VAR_PAYMENT_METHOD', 'Paypal', $description);
						} else {
							$description = str_ireplace('VAR_PAYMENT_METHOD', 'Bank', $description);
						}
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.withdrawal_debit');
					} else {
						$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$var_withdrawal_page = '<a href="'.URL::action('MyWithdrawalController@getIndex').'">'.$each_transaction->reference_content_id.'</a>';
					$description = str_ireplace('VAR_WITHDRAWAL_PAGE', $var_withdrawal_page, $description);
					break;
				case 'product_listing_fee':
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.product_listing_fee_credit');
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.product_listing_fee_debit');
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$product_details = CUtil::getProductDetails($each_transaction->reference_content_id);
					$var_product_url = '';
					if ($product_details) {
						$var_product_url = '<a href="'.$product_details['view_url'].'">'.$product_details['details']['product_code'].'</a>';
					}
					$description = str_ireplace('VAR_PRODUCT_URL', $var_product_url, $description);
					break;
				case 'walletaccount':
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.walletaccount_credit');
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.walletaccount_debit');
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$var_invoice_page = '<a href="'.URL::action('InvoiceController@getInvoiceDetails', $each_transaction->reference_content_id).'">'.$each_transaction->reference_content_id.'</a>';
					$description = str_ireplace('VAR_INVOICE_PAGE', $var_invoice_page, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				case 'walletaccount_fromsite':
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.walletaccount_fromsite_credit');
					} else if ($each_transaction->transaction_type == 'debit') {
						//$description = Lang::get('transaction.walletaccount_debit');
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$var_invoice_page = '<a href="'.URL::action('InvoiceController@getInvoiceDetails', $each_transaction->reference_content_id).'">'.$each_transaction->reference_content_id.'</a>';
					$description = str_ireplace('VAR_INVOICE_PAGE', $var_invoice_page, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				case 'walletaccount_purchase':
					$var_order = '';
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.walletaccount_purchase_credit');
						$var_order = '<a href="'.URL::action('PurchasesController@getOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else if ($each_transaction->transaction_type == 'debit') {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$var = '<a href="'.URL::action('InvoiceController@getInvoiceDetails', $each_transaction->reference_content_id).'">'.$each_transaction->reference_content_id.'</a>';
					$description = str_ireplace('VAR_ORDER', $var_order, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;

				case 'deal_listfee_paid':
					if ($each_transaction->transaction_type == 'credit')
					{
						$description = Lang::get('deals::deals.deal_listing_fee_credit_user');
						if(isset($each_transaction->transaction_notes) && $each_transaction->transaction_notes != '')
						$description .= "<br />".$each_transaction->transaction_notes;
					}
					else if ($each_transaction->transaction_type == 'debit')
						$description = Lang::get('deals::deals.deal_listing_fee_debit_user');

					$dealDet = $this->deal_service->fetchDealDetailsById($each_transaction->reference_content_id);
					$var_deal_url = '';
					if ($dealDet)
					{
						$var_deal_url = '<a href="'.$dealDet['viewDealLink'].'">'.$dealDet['deal_title'].'</a>';
					}
					$description = str_ireplace('VAR_DEAL', $var_deal_url, $description);

					break;

				case 'deal_tipping_success':
					if ($each_transaction->transaction_type == 'credit')
						$description = Lang::get('deals::deals.deal_tip_sucess_credit_user');
					else if ($each_transaction->transaction_type == 'debit')
						$description = Lang::get('deals::deals.deal_tip_sucess_debit_user');

					$dealDet = $this->deal_service->fetchDealDetailsById($each_transaction->reference_content_id);
					$var_deal_url = '';
					if ($dealDet)
					{
						$var_deal_url = '<a href="'.$dealDet['viewDealLink'].'">'.$dealDet['deal_title'].'</a>';
					}
					$description = str_ireplace('VAR_DEAL', $var_deal_url, $description);

					break;

				case 'deal_tipping_failure':
					if ($each_transaction->transaction_type == 'credit')
						$description = Lang::get('deals::deals.deal_tip_failed_credit_user');
					else if ($each_transaction->transaction_type == 'debit')
						$description = Lang::get('deals::deals.deal_tip_failed_debit_user');

					$dealDet = $this->deal_service->fetchDealDetailsById($each_transaction->reference_content_id);
					$var_deal_url = '';
					if ($dealDet)
					{
						$var_deal_url = '<a href="'.$dealDet['viewDealLink'].'">'.$dealDet['deal_title'].'</a>';
					}
					$description = str_ireplace('VAR_DEAL', $var_deal_url, $description);

					break;
				case 'product_featured_fee':
					$description = '';
					if ($each_transaction->transaction_type == 'credit')
						$description = Lang::get('featuredproducts::featuredproducts.featured_listing_fee_credit_user');
					else if ($each_transaction->transaction_type == 'debit')
						$description = Lang::get('featuredproducts::featuredproducts.featured_listing_fee_debit_user');

					$product_details = CUtil::getProductDetails($each_transaction->reference_content_id);
					$var_product_url = '';
					if($product_details) {
						$var_product_url = '<a href="'.$product_details['view_url'].'">'.$product_details['details']['product_code'].'</a>';
					}
					$description = str_ireplace('VAR_PRODUCT_URL', $var_product_url, $description);

					break;
				case 'seller_featured_fee':
					$description = '';
					if ($each_transaction->transaction_type == 'credit')
						$description = Lang::get('featuredsellers::featuredsellers.seller_featured_listing_fee_credit_user');
					else if ($each_transaction->transaction_type == 'debit')
						$description = Lang::get('featuredsellers::featuredsellers.seller_featured_listing_fee_debit_user');

					$seller_id = $each_transaction->reference_content_id;
					$seller_details = CUtil::getUserDetails($seller_id);
					if ($seller_details)
						$var_seller = '<a href="'.URL::to('admin/users/user-details/'.$seller_id).'">'.$seller_details['display_name'].'</a>';
					$description = str_ireplace('VAR_SELLER', $var_seller, $description);

					break;
				case 'gateway_fee':
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.gateway_fee_credit');
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.gateway_fee_debit');
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$var_invoice_page = '<a href="'.URL::action('InvoiceController@getInvoiceDetails', $each_transaction->reference_content_id).'">'.$each_transaction->reference_content_id.'</a>';
					$description = str_ireplace('VAR_INVOICE_PAGE', $var_invoice_page, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				case 'gateway_fee_purchase':
					$var_order = '';
					if ($each_transaction->transaction_type == 'credit') {
						$description = Lang::get('transaction.gateway_fee_purchase_credit');
						$var_order = '<a href="'.URL::action('PurchasesController@getSalesOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else if ($each_transaction->transaction_type == 'debit') {
						$description = Lang::get('transaction.gateway_fee_purchase_debit');
						$var_order = '<a href="'.URL::action('PurchasesController@getOrderDetails', $each_transaction->reference_content_id).'">'.CUtil::setOrderCode($each_transaction->reference_content_id).'</a>';
					} else {
						//$description = $each_transaction->transaction_key.': '.$each_transaction->transaction_type.' transaction type not handled';
					}
					$description = str_ireplace('VAR_ORDER', $var_order, $description);
					if ($each_transaction->transaction_id)
						$description .= '<br />'.Lang::get('transaction.transaction_id').': '.$each_transaction->transaction_id;
					break;
				default:
					$description = 'Transaction:'.$each_transaction->transaction_key.': '.$each_transaction->transaction_type;
					break;
			} // switch
			$description = str_ireplace('VAR_PAYMENT_METHOD', $each_transaction->payment_type, $description);
			$description = str_ireplace('VAR_USER', $var_user, $description);
			//$transaction_description[$each_transaction->id] = $description.'<br /><br />******** Old Description display for reference, need to remove ******<br />'.$each_transaction->transaction_notes.'<br />'.$each_transaction->transaction_key.': '.$each_transaction->transaction_type.'<br />**************************************';
			$transaction_description[$each_transaction->id] = $description;
		}

		$product_obj = Products::initialize();
		$productService = new ProductService();
		$get_common_meta_values = Cutil::getCommonMetaValues('transactions-history');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('transactionsList', compact('transaction_details', 'transaction_description','product_obj','product_obj','productService'));
	}

}