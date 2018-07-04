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
class SitemapsController extends BaseController
{
	/**
	 * SitemapsController::__construct()
	 *
	 */
	public function __construct()
	{
        parent::__construct();
    }

	/**
	 * SitemapsController::index()
	 * Sitemap index
	 *
	 * @return
	 */
	public function getIndex()
    {
        // Get a general sitemap.
        Sitemap::addSitemap(URL::to('sitemap/general'));

        // Products
        Sitemap::addSitemap(URL::to('sitemap/products'));

        // Shops
        Sitemap::addSitemap(URL::to('sitemap/shops'));

        // Deals
        if (CUtil::chkIsAllowedModule('deals')) {
        	Sitemap::addSitemap(URL::to('sitemap/deals'));
        }

        // Return the sitemap to the client.
        return Sitemap::renderSitemapIndex();
    }

    /**
     * SitemapsController::getGeneral()
     * General urls
     *
     * @return
     */
    public function getGeneral()
    {
    	$created_at = User::whereRaw('id = ?', array('1'))->pluck('created_at');
        Sitemap::addTag(URL::to('/'), $created_at, 'hourly', '0.8');
        Sitemap::addTag(URL::to('users/login'), $created_at, 'monthly', '0.8');
        Sitemap::addTag(URL::to('users/signup'), $created_at, 'monthly', '0.8');
        Sitemap::addTag(URL::to('users/forgotpassword'), $created_at, 'monthly', '0.8');
        Sitemap::addTag(URL::to('product'), $created_at, 'daily', '0.8');
        Sitemap::addTag(URL::to('shop'), $created_at, 'daily', '0.8');
        Sitemap::addTag(URL::to('collections'), $created_at, 'daily', '0.8');
        Sitemap::addTag(URL::to('buy'), $created_at, 'daily', '0.8');
        Sitemap::addTag(URL::to('sell'), $created_at, 'daily', '0.8');
        $browse_cat = Products::getTopCategories();
        if (count($browse_cat) > 0) {
        	$productService = new ProductService();
        	foreach($browse_cat as $cat) {
        		Sitemap::addTag(URL::to('browse/'.$cat->seo_category_name), $created_at, 'hourly', '0.8');
        		$sub_cat = Products::getCategoriesList($cat->id);
        		if (count($sub_cat)) {
        			foreach($sub_cat as $sub){
						Sitemap::addTag(URL::to('browse/'.$cat->seo_category_name.'/'.$sub->seo_category_name), $created_at, 'hourly', '0.8');
        			}
				}
        	}
		}
		$footer_links = CUtil::getStaticPageFooterLinks();
		if (count($footer_links) > 0) {
			foreach($footer_links as $key => $val) {
				if ($val['page_type'] == 'external') {
					Sitemap::addTag(URL::to($val['external_link']), $created_at, 'monthly', '0.8');
				} else {
					Sitemap::addTag(URL::to($val['page_link']), $created_at, 'monthly', '0.8');
				}
			}
		}

        if (CUtil::chkIsAllowedModule('deals')) {
        	Sitemap::addTag(URL::to('deals'), $created_at, 'daily', '0.8');
        }
        return Sitemap::renderSitemap();
    }

    /**
     * SitemapsController::getProducts()
     * Products url
     *
     * @return
     */
    public function getProducts()
    {
		$product = Products::initialize();
    	$product_details = $product->getProductsList();
		if ($product_details) {
			$productService = new ProductService();
	        foreach ($product_details as $each_product)
	        {
	        	$product_url = $productService->getProductViewURL($each_product->id, $each_product);
	            Sitemap::addTag($product_url, $each_product->last_updated_date, 'weekly', '0.8');
	        }
		} else {
			$created_at = User::whereRaw('id = ?', array('1'))->pluck('created_at');
			Sitemap::addTag(URL::to('product'), $created_at, 'daily', '0.8');
		}
	    return Sitemap::renderSitemap();
    }

    /**
     * SitemapsController::getShops()
     * Shops url
     *
     * @return
     */
    public function getShops()
    {
		$shop_obj = Products::initializeShops();
		$shops_list = $shop_obj->getShopList();
		if ($shops_list) {
			$productService = new ProductService();
	        foreach ($shops_list as $each_shop)
	        {
	        	$shop_url = $productService->getProductShopURL($each_shop->id, $each_shop);
	            Sitemap::addTag($shop_url, $each_shop->created_at, 'weekly', '0.8');
	            Sitemap::addTag($shop_url.'/shop-policy', $each_shop->created_at, 'weekly', '0.8');
	            Sitemap::addTag($shop_url.'/shop-reviews', $each_shop->created_at, 'weekly', '0.8');
	        }
		} else {
			$created_at = User::whereRaw('id = ?', array('1'))->pluck('created_at');
			Sitemap::addTag(URL::to('shop'), $created_at, 'daily', '0.8');
		}
        return Sitemap::renderSitemap();
    }

    /**
     * SitemapsController::getDeals()
     * Deals url
     *
     * @return
     */
    public function getDeals()
    {
    	$deal_serviceobj = new \DealsService();
    	$deal_list = $deal_serviceobj->getDealList('new');
    	if ($deal_list) {
    		foreach($deal_list as $each_deal){
    			$deal_url = $deal_serviceobj->getDealViewUrl($each_deal);
    			Sitemap::addTag($deal_url, $each_deal->date_deal_from, 'weekly', '0.8');
    		}
		} else {
	    	$created_at = User::whereRaw('id = ?', array('1'))->pluck('created_at');
	    	Sitemap::addTag(URL::to('deals'), $created_at, 'daily', '0.8');
		}
		return Sitemap::renderSitemap();
    }
}
?>