<?php
class AdminDashboardService
{
	public function getDashboardUserStats(){

		$default_arr = array('total_members' => 0, 'activated_users' => 0, 'joined_today'=>0, 'joined_thisweek' => 0, 'joined_thismonth' => 0);
		$member_stats = array();
		$user = User::where('id','>','0');
		$member_stats['total_members'] = $user->count();
		$member_stats['activated_users'] = $user->where('activated',1)->count();
		//Starting date range
		$user = User::where('id','>','0');
		$user->where(DB::Raw("YEAR(created_at)"), '=', DB::Raw("YEAR(NOW())"));
		$member_stats['joined_thismonth'] = $user->where(DB::Raw("MONTH(created_at)"), '=', DB::Raw("MONTH(NOW())"))->count();
		$member_stats['joined_thisweek'] = $user->where(DB::Raw("WEEK(created_at)"), '=', DB::Raw("WEEK(NOW())"))->count();
		$member_stats['joined_today'] = $user->where(DB::Raw("DATE(created_at)"), '=', date('Y-m-d'))->count();

		$member_stats_details = $member_stats+$default_arr;
		return $member_stats_details;
	}

	public function getDashboardProductStats()
	{

		$default_arr = array('total_products' => 0, 'inactive_products' => 0, 'added_today'=>0, 'added_thisweek' => 0, 'added_thismonth' => 0);
		$product_stats = array();
		$product = Product::where('product_status','!=','Deleted');
		$product_stats['total_products'] = $product->count();//toBeActivated
		$product_stats['inactive_products'] = Product::where('product_status','=','ToActivate')->count();
		//Starting date range
		$product->where(DB::Raw("YEAR(product_added_date)"), '=', DB::Raw("YEAR(NOW())"));
		$product_stats['added_thismonth'] = $product->where(DB::Raw("MONTH(product_added_date)"), '=', DB::Raw("MONTH(NOW())"))->count();
		$product_stats['added_thisweek'] = $product->where(DB::Raw("WEEK(product_added_date)"), '=', DB::Raw("WEEK(NOW())"))->count();
		$product_stats['added_today'] = $product->where(DB::Raw("DATE(product_added_date)"), '=', date('Y-m-d'))->count();

		$product_stats_details = $product_stats+$default_arr;
		return $product_stats_details;

	}

	public function getDashboardSiteEarningDetails(){

		$default_arr = array('total_earnings' => 0, 'todays_earning' => 0, 'pending_payment'=>0);
		$site_earnings = array();
		$shop_order = ShopOrder::whereIn('order_status',array('payment_completed', 'refund_requested', 'refund_rejected'));
		$site_earnings['total_earnings'] = $shop_order->sum('site_commission');
		$site_earnings['todays_earning'] = $shop_order->where(DB::Raw('DATE(date_created)'),'=', date('Y-m-d'))->sum('site_commission');
		$site_earnings['pending_payment'] = ShopOrder::where('order_status','=','not_paid')->sum('site_commission');

		$site_earning_details = $site_earnings+$default_arr;
		return $site_earning_details;

	}

}

