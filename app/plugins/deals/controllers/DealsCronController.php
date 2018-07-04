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
namespace App\Plugins\Deals\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Products ;
use Session, Redirect, BaseController;

class DealsCronController extends \BaseController
{

	public function __construct()
	{
		$this->cron_limit = 5;
	}

	public function getIndex()
	{
	}

	public function fetchUpdateTippedDeals()
	{
		/*
		1. Get Tipping reached deal list
		2. Send notification to
			Admin - Tipping reached and transfer fund to seller a/c ..
			Seller -Tipping reached please ship the product items, with the deal purchased details and you will receive the fund the product purchasee soon,
			Buyer - Tipping reached for your purchase you will shortly receive your items.
		  - Update as notified to avoid repeated notification
		*/
		$this->deal_service = new \DealsService();

		$deal_list = DB::table('deal')->where('tipping_notified', 0)->where('deal_tipping_status', 'tipping_reached')
					->take($this->cron_limit)
					->orderBy('deal.deal_id')->get();

		if(COUNT($deal_list))
		{
			foreach($deal_list AS $deal)
			{
				$this->deal_service->updateDealTippedDetails($deal);
				// Send notification to admin, Seller, Buyer for deal tipped. Transfer funds seller account
			}
		}
	}

	public function fetchUpdateExpiredDeals()
	{
		/*
		1. Check pending_tipping & expired deals - check it reached tipping or not,
			- Tipping reached
				- Update status as tipping_reached alone
				- Do cron 1 notification works.

			- Tipping not reached then - update status as tipping_failed for shop_order, shop_order_item.
				- Mail to Admin - Tipping failed refund amount to buyer.
				- Mail to Seller - Tipping failed for your sale.
				- Mail to Buyer - Tipping failed you will receive your amount soon.

		*/

		$deal_list = array();
		$this->deal_service = new \DealsService();
		$deal_list = DB::table('deal')->where('tipping_notified', 0)
					->whereRaw('deal_status != \'expired\' ')
					->whereRaw('(((deal_tipping_status = \'pending_tipping\' OR deal_tipping_status = \'\' ) AND DATE_FORMAT(date_deal_to, \'%Y-%m-%d\') < CURDATE() ) OR (deal_status = \'closed\'))')
					->take($this->cron_limit)
					->orderBy('deal.deal_id')->get();

		if(COUNT($deal_list))
		{
			foreach($deal_list AS $deal)
			{
				$this->deal_service->updateDealExpiredDetails($deal);
				// Send notification to admin, Seller, Buyer for deal tipped.
				// Transfer funds seller account
			}
		}

	}
}