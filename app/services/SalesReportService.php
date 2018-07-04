<?php

class SalesReportService
{
	public function getSalesReportProductWise($product_id = null, $inputs = array(), $limit =20)
	{
		$sales_report = array();
		$sales_report = ShopOrderItem::select('shop_order_item.*')->join('shop_order', 'shop_order.id','=','shop_order_item.order_id')
									->whereIn('shop_order.order_status',array('payment_completed','refund_requested', 'refund_rejected'));
		if(!is_null($product_id) && $product_id > 0)
			$sales_report->where('shop_order_item.item_id','=',$product_id);

		if(isset($inputs['product_code']) && $inputs['product_code'] != '')
			$sales_report->where('shop_order_item.item_id', '=', CUtil::getProductIdUsingCode($inputs['product_code']));

		if(isset($inputs['from_date']) && $inputs['from_date'] != '')
			$sales_report->where('shop_order_item.date_added','>=',$inputs['from_date']);

		if(isset($inputs['to_date']) && $inputs['to_date'] != '')
			$sales_report->where('shop_order_item.date_added','<=',$inputs['to_date']);

		$sales_report = $sales_report->orderby('shop_order_item.date_added', 'desc')->orderby('shop_order_item.item_id', 'desc')->paginate($limit);
		return $sales_report;
	}
	public function getEarnedDetailsProductWise($product_id){
		$earned_details = ShopOrderItem::selectRaw("sum(shop_order_item.seller_amount) as seller_earned, sum(shop_order_item.site_commission) as site_earned")->join('shop_order', 'shop_order.id','=','shop_order_item.order_id')
									->whereIn('shop_order.order_status',array('payment_completed','refund_requested', 'refund_rejected'))
									->where('shop_order_item.item_id', '=', $product_id)->get()->toArray();
		$earned = array();
		if(!empty($earned_details))
			$earned_details = reset($earned_details);
		$earned['seller_earned'] = (isset($earned_details['seller_earned']) && $earned_details['seller_earned'] > 0)?$earned_details['seller_earned']:0;
		$earned['site_earned'] = (isset($earned_details['site_earned']) && $earned_details['site_earned'] > 0)?$earned_details['site_earned']:0;
		return $earned;
	}

	public function getSalesReportMemberWise($inputs = array(), $limit =20)
	{
		//SELECT `shop_order_item`.item_owner_id, SUM(`shop_order_item`.item_qty) AS total_sales, SUM(`shop_order_item`.seller_amount) AS owners_earning, SUM(`shop_order_item`.site_commission) AS site_earning FROM `shop_order_item` INNER JOIN `shop_order` ON `shop_order`.`id` = `shop_order_item`.`order_id` WHERE `shop_order`.`order_status` IN ('payment_completed', 'refund_requested', 'refund_rejected') GROUP BY `shop_order_item`.item_owner_id ORDER BY total_sales DESC;
		$sales_report = array();
		$sales_report = ShopOrderItem::select(DB::Raw("`shop_order_item`.item_owner_id, `shop_order`.currency, SUM(`shop_order_item`.item_qty) AS total_sales, SUM(`shop_order_item`.seller_amount) AS owners_earning, SUM(`shop_order_item`.site_commission) AS site_earning"))
						->join('shop_order', 'shop_order.id','=','shop_order_item.order_id')
						->whereIn('shop_order.order_status',array('payment_completed','refund_requested', 'refund_rejected'))
						->groupby('shop_order_item.item_owner_id');

		if(isset($inputs['owner_id'])) {
			$sales_report->where('shop_order_item.item_owner_id', '=', BasicCUtil::getUserIDFromCode($inputs['owner_id']) );

		}
		if(isset($inputs['from_date']) && $inputs['from_date'] != '')
			$sales_report->where('shop_order_item.date_added','>=',$inputs['from_date']);

		if(isset($inputs['to_date']) && $inputs['to_date'] != '')
			$sales_report->where('shop_order_item.date_added','<=',$inputs['to_date']);

		$sales_report = $sales_report->orderby('shop_order_item.item_owner_id','desc')->paginate($limit);
		return $sales_report;
	}
	public function getSalesReportSingleMemberWise($user_id = NULL, $inputs = array(), $limit = 20)
	{
		$sales_report = array();
		$sales_report = ShopOrderItem::select(DB::Raw("`shop_order_item`.item_id, `shop_order_item`.item_owner_id, `shop_order`.currency,
							`product`.product_name,`product`.url_slug,`product`.product_code,
							 SUM(`shop_order_item`.item_qty) AS total_sales, SUM(`shop_order_item`.seller_amount) AS owners_earning, SUM(`shop_order_item`.site_commission) AS site_earning"))
						->join('shop_order', 'shop_order.id','=','shop_order_item.order_id')
						->leftJoin('product', 'product.id', '=', 'shop_order_item.item_id')
						->whereIn('shop_order.order_status',array('payment_completed','refund_requested', 'refund_rejected'))
						->groupby('shop_order_item.item_id')
						->where('shop_order_item.item_owner_id', $user_id);

		if(isset($inputs['from_date']) && $inputs['from_date'] != '')
			$sales_report->where('shop_order_item.date_added','>=',$inputs['from_date']);

		if(isset($inputs['to_date']) && $inputs['to_date'] != '')
			$sales_report->where('shop_order_item.date_added','<=',$inputs['to_date']);

		$sales_report = $sales_report->orderby('shop_order_item.item_id','desc')->paginate($limit);
		return $sales_report;
	}

	public function getEarnedDetailsMemberWise($user_id)
	{
		$earned_details = ShopOrderItem::selectRaw("sum(shop_order_item.seller_amount) as seller_earned, sum(shop_order_item.site_commission) as site_earned")->join('shop_order', 'shop_order.id','=','shop_order_item.order_id')
									->whereIn('shop_order.order_status',array('payment_completed','refund_requested', 'refund_rejected'))
									->where('shop_order_item.item_owner_id', '=', $user_id)->get()->toArray();
		$earned = array();
		if(!empty($earned_details))
			$earned_details = reset($earned_details);
		$earned['seller_earned'] = (isset($earned_details['seller_earned']) && $earned_details['seller_earned'] > 0)?$earned_details['seller_earned']:0;
		$earned['site_earned'] = (isset($earned_details['site_earned']) && $earned_details['site_earned'] > 0)?$earned_details['site_earned']:0;
		return $earned;
	}
}