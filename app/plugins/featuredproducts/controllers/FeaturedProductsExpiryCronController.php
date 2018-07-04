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
namespace App\Plugins\FeaturedProducts\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products;
use Session, Redirect, BaseController;
class FeaturedProductsExpiryCronController extends \BaseController
{
	public function getIndex()
	{
		$featured_products = DB::table('product')
							->select('product.id', 'product.is_featured_product', 'product.featured_product_expires')
							->whereRaw('product.is_featured_product = ? AND product.featured_product_expires < Now()',
										array('Yes'))
							->take(10)
							->get();
		if (count($featured_products) > 0)	{
			foreach($featured_products as $product) {
				$update_arr = array("is_featured_product" => "No");
				DB::table('product')->whereRaw('id = ?', array($product->id))->update($update_arr);
			}
		}
	}
}

?>