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
namespace App\Plugins\FeaturedSellers\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config;
use Session, Redirect, BaseController;
class FeaturedSellersExpiryCronController extends \BaseController
{
	public function getIndex()
	{
		$featured_sellers = DB::table('users')
							->select('users.id', 'users.is_featured_seller', 'users.featured_seller_expires')
							->whereRaw('users.is_featured_seller = ? AND users.featured_seller_expires < Now()',
										array('Yes'))
							->take(10)
							->get();
		if (count($featured_sellers) > 0)	{
			foreach($featured_sellers as $seller) {
				$update_arr = array("is_featured_seller" => "No");
				DB::table('users')->whereRaw('id = ?', array($seller->id))->update($update_arr);
			}
		}
	}
}

?>