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
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => 'sentry.admin'), function()
{
	Route::any('admin/config-manage', 'ConfigManageController@configManage');
	Route::any('admin/clear-cache', 'ConfigManageController@clearCache');
	Route::get('admin', 'AdminDashboardController@getIndex');
	Route::get('admin/meta-details', 'AdminDashboardController@getMetaDetails');
	Route::post('admin/admin-update-language', 'AdminDashboardController@postAdminUpdateLanguage');
	Route::post('admin/meta-details', 'AdminDashboardController@postMetaDetails');
	Route::get('admin/users', 'AdminUserController@index');
	Route::get('admin/shops', 'AdminUserController@getShops');
	Route::get('admin/shop/edit/{shop_id}', 'AdminUserController@getEditShop');
	Route::post('admin/shop/edit/{shop_id}', 'AdminUserController@postEditShop');
	Route::get('admin/shop/delete-cancellation-policy', 'AdminUserController@getDeleteCancellationPolicy');
	Route::get('admin/shop/delete-shop-image', 'AdminUserController@getDeleteShopImage');
	Route::post('admin/shops/change-shop-status', 'AdminUserController@postChangeShopStatus');
	Route::get('admin/users/add', 'AdminUserController@getAddUsers');
	Route::post('admin/users/add', 'AdminUserController@postAddUsers');
	Route::get('admin/users/edit/{user_id}', 'AdminUserController@getEditUsers');
	Route::post('admin/users/edit/{user_id}', 'AdminUserController@postEditUsers');
	Route::get('admin/users/user-details/{user_id}', 'AdminUserController@getUserDetails');
	Route::any('admin/users/changestatus', 'AdminUserController@getChangeUserStatus');
	Route::any('admin/users/changesellerstatus', 'AdminUserController@getChangeSellerStatus');
	Route::post('admin/users/change-group-name', 'AdminUserController@postChangeGroupName');
	Route::get('admin/users/manage-credits', 'AdminUserController@getManageCredits');
	Route::post('admin/users/manage-credits', 'AdminUserController@postManageCredits');
	Route::get('admin/manage-favorite-sellers', 'AdminUserController@getManageFeaturedSellers');
	Route::post('admin/manage-favorite-sellers', 'AdminUserController@postManageFeaturedSellers');
	Route::get('admin/manage-favorite-sellers/delete', 'AdminUserController@deleteFeaturedSellers');
	Route::get('admin/manage-toppicks-users', 'AdminUserController@getManageToppicksUsers');
	Route::post('admin/manage-toppicks-users', 'AdminUserController@postManageToppicksUsers');
	Route::get('admin/manage-toppicks-users/delete', 'AdminUserController@deleteToppicksUsers');
	Route::get('admin/manage-favorite-products', 'AdminUserController@getManageFavoriteProducts');
	Route::post('admin/manage-favorite-products', 'AdminUserController@postManageFavoriteProducts');
	Route::get('admin/manage-favorite-products/delete', 'AdminUserController@deleteFavoriteProducts');
	Route::get('admin/users-auto-complete', 'AdminUserController@getUsersAutoComplete');
	Route::get('admin/shop-owners-auto-complete', 'AdminUserController@getShopOwnersNameAutoComplete');
	Route::get('admin/shop-owners-code-auto-complete', 'AdminUserController@getShopOwnersCodeAutoComplete');
	Route::get('admin/product-auto-complete', 'AdminUserController@getProductAutoComplete');
	Route::get('admin/search-members', 'AdminUserController@SearchMembers');
	Route::controller('admin/index-banner', 'AdminManageBannerController');
	Route::controller('admin/manage-language', 'AdminManageLanguageController');
	Route::controller('admin/email-templates', 'AdminManageEmailTemplateController');
	Route::controller('admin/purchases', 'AdminPurchasesController');
	Route::controller('admin/unpaid-invoice-list', 'AdminUnpaidInvoiceListController');
	Route::controller('admin/taxations', 'AdminTaxationsController');
	Route::controller('admin/group', 'GroupManageController');
	Route::controller('admin/cancellation-policy','AdminCancellationPolicyController');
	Route::controller('admin/product/list', 'AdminProductListController');
	Route::controller('admin/product/view/{slug_url}', 'AdminViewProductController');
	Route::controller('admin/shop', 'AdminManageShopController');
	Route::controller('admin/product/add', 'AdminProductAddController');
	Route::controller('admin/manage-product-catalog', 'AdminManageProductCatalogController');
	Route::controller('admin/product-category', 'AdminProductCategoryController');
	Route::controller('admin/category-attributes', 'AdminCategoryAttributesController');
	Route::controller('admin/product-attributes', 'AdminProductAttributesController');
	Route::controller('admin/withdrawals', 'AdminWithdrawalController');
	Route::controller('admin/manage-stocks', 'AdminManageStocksController');
	Route::controller('admin/shipping-template', 'AdminShippingTemplateController');
	Route::controller('admin/site-wallet', 'AdminWalletAccountController');
	Route::controller('admin/currency-exchange-rate', 'AdminExchangeRateController');
	Route::controller('admin/site-logo','AdminSiteLogoController');
	Route::get('admin/sell-static-page','AdminStaticPageController@getSellStaticPage');
	Route::post('admin/sell-static-page','AdminStaticPageController@postSellStaticPage');
	Route::controller('admin/static-page','AdminStaticPageController');
	Route::controller('admin/manage-banner','AdminSiteBannerController');
	Route::controller('admin/transactions','AdminTransactionsController');
	Route::controller('admin/feedback', 'AdminManageFeedbackController');
	Route::controller('admin/seller-requests', 'AdminSellerRequestController');
	Route::controller('admin/reported-products', 'AdminReportedProductsController');
	Route::controller('admin/sales-report', 'AdminSalesController');
	Route::controller('admin/collections', 'AdminManageCollectionsController');
	Route::controller('admin/newsletter-subscriber', 'AdminNewsletterSubscriberController');
	Route::controller('admin/newsletter', 'AdminNewsletterController');
	Route::controller('admin/product-comments', 'AdminProductCommentsController');
	Route::controller('admin/mass-email','AdminMassEmailController');

});


Route::group(array('before' => 'test.license'), function()
{
	Route::get('test-license', function(){
		$success_msg = 'Valid license!!';
		return View::make('license', compact('success_msg'));
	});
});

Route::group(array('before' => 'validate.license'), function()
{
	Route::controller('shipments', 'ShipmentsController');
	Route::controller('addressing', 'AddressingController');
	Route::get('users/activation/{activationCode}', 'AuthController@getActivate');
	Route::group(array('before' => 'sentry'), function()
	{
		Route::get('users/myaccount', 'AccountController@getIndex');
		Route::get('users/request-seller', 'AccountController@getSellerRequest');
		Route::post('users/request-seller', 'AccountController@postSellerRequest');
		Route::post('users/myaccount', 'AccountController@postIndex');
		Route::controller('users/my-withdrawals', 'MyWithdrawalController');
	});
	Route::get('contactus', 'MessagingController@getContactUs');
	Route::get('favorite/{user_code_seo_title}', 'FavoritesController@viewFavorites');
	Route::get('favorite-list/{user_code_seo_title}', 'FavoritesController@viewFavoritesProducts');
	Route::controller('favorites', 'FavoritesController');
	Route::controller('oauth', 'OAuthController');
	Route::get('circle/{user_code_seo_title}', 'UsersCircleController@viewCircle');
	Route::get('user/{user_code_seo_title}', 'ProfileController@viewProfile');//->where('user_code_seo_title', 'U[0-9]{6}'); //Call when parameter has user code format value
	Route::get('users/signup-pop/{formtype}/{authtype?}/{returnthread?}','AuthController@signupPopup');
	Route::post('users/signup-pop/{formtype}','AuthController@PostSignupPopup');
	Route::controller('users', 'AuthController');
	Route::any('collections', 'CollectionsController@getIndex');
	Route::controller('collections', 'CollectionsController');
	Route::any('buy', function(){ return View::make('static/buy'); });
	Route::group(array('before' => 'validate.seller'), function()
	{
		Route::any('sell', function(){return View::make('static/sell'); });
	});
	/*Route::group(array('prefix' => 'static'), function(){
		Route::any('categoryItemListing', function(){ return View::make('static/categoryItemListing'); });
		Route::any('categorySubItemListing', function(){ return View::make('static/categorySubItemListing'); });
		Route::any('terms', function(){ return View::make('static/terms'); });
		Route::any('privacy', function(){ return View::make('static/privacy'); });
	});*/
	Route::get('static/{slug_url}', 'HomeController@showStaticPage');
	Route::controller('product/view/{slug_url}', 'ViewProductController');
	Route::controller('sitemap', 'SitemapsController');

	Route::group(array('before' => 'sentry'), function()
	{
		Route::controller('messages', 'MessagingController');
		Route::controller('taxations', 'TaxationsController');
		Route::controller('shipping-template', 'ShippingTemplateController');
		Route::controller('coupons', 'CouponsController');
		Route::controller('mycollections', 'MyCollectionsController');
		Route::controller('feedback', 'FeedbackController');
		Route::controller('product/add', 'ProductAddController');
		Route::get('shop/user/message/add/{user_code}', 'MessageAddController@getAdd');
		Route::post('shop/user/message/add/{user_code}', 'MessageAddController@postAdd');
		Route::get('shop/users/shop-policy-details', 'ShopController@getShopPoicyDetails');
		Route::post('shop/users/shop-policy-details', 'ShopController@postShopPoicyDetails');
		Route::controller('shop/users/shop-details', 'ShopController');
		Route::get('myproducts', 'ProductController@productList');
		Route::post('myproducts/deleteproduct', 'ProductController@postProductAction');
		Route::post('reportitem', 'ProductController@postReportItem');
		Route::controller('purchases', 'PurchasesController');
		Route::controller('transactions', 'TransactionsController');
		Route::controller('wishlist', 'WishlistController');
		Route::controller('userscircle', 'UsersCircleController');
		Route::controller('walletaccount', 'WalletAccountController');
		Route::controller('invoice', 'InvoiceController');
		Route::controller('addresses', 'AddressesController');

		Route::any('checkout/coupon-details', 'CheckOutController@postCouponDetails');
		Route::any('checkout/shipping-address-popup', 'CheckOutController@getShippingAddressPopup');
		Route::any('checkout/proceed', 'CheckOutController@doCheckOut');
		Route::any('checkout/update-shipping-address', 'CheckOutController@postUpdateShippingAddress');
		Route::any('checkout/{owner_id}', 'CheckOutController@populateCheckOutItems');
		Route::any('checkout', 'CheckOutController@populateCheckOutItems');
		Route::any('pay-checkout-free/{order_id}', 'PayCheckOutController@payCheckOutFreeItems');
		Route::any('pay-checkout/{order_id}', 'PayCheckOutController@payCheckOutItems');
		Route::any('review-order/{order_id}', 'PayCheckOutController@reviewOrder');
		Route::any('proceedpayment', 'PayCheckOutController@generateInvoice');
	});
	Route::get('shop/favorites/{url_slug}', 'ViewShopController@viewFavorites');
	Route::controller('shop/{url_slug}', 'ViewShopController');
	Route::any('shop', 'ListShopController@getIndex');
	Route::get(
		'browse/{path}', function($path) {
	    $path = substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
	    $slugs = explode('/', $path);

	    $check = function($page, $slugs) use(&$check) {
	        if($page->parent_category_id == 1) {
	            return true;
	        }

	        //$parent = ProductCategory::find($page->parent_category_id);
	        $parent = Products::getCategoryDetails($page->parent_category_id);
	        if($parent->seo_category_name == array_pop($slugs)) {
	            return $check($parent, $slugs);
	        }
	    };
		$error = true;
	    foreach(Products::getCategoryDetailsBySlug(array_pop($slugs)) as $page) {
	        if($check($page, $slugs)) {
	        	$error = true;
	            break;
	        }
	    }
	    //todo : handle error
	    if(!isset($page)) {
			return ProductController::showCategoryList(0);
		}
		else {
			return ProductController::showCategoryList($page->id);
		}
	})->where('path', '.+');

	Route::get(
		'product/{path}', function($path) {
	    $path = substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
	    $slugs = explode('/', $path);

	    $check = function($page, $slugs) use(&$check) {
	        if($page->parent_category_id == 1) {
	            return true;
	        }

	        //$parent = ProductCategory::find($page->parent_category_id);
	        $parent = Products::getCategoryDetails($page->parent_category_id);
	        if($parent->seo_category_name == array_pop($slugs)) {
	            return $check($parent, $slugs);
	        }
	    };
		$error = true;
	    foreach(Products::getCategoryDetailsBySlug(array_pop($slugs)) as $page) {
	        if($check($page, $slugs)) {
	        	$error = true;
	            break;
	        }
	    }
	    //todo : handle error
	    if(!isset($page)) {
			//return ProductController::showList(0);
			return Redirect::to('product');
		}
		else {
			return ProductController::showList($page->id);
		}
	})->where('path', '.+');

	Route::any('updateCurrency', 'ProductController@updateCurrency');
	Route::any('updateLanguage', 'ProductController@updateLanguage');
	Route::any('updateShippingCountry', 'ProductController@updateShippingCountry');
	Route::any('product', 'ProductController@showList');
	Route::controller('cart', 'CartController');

	Route::any('payment/success', 'PayCheckOutController@paymentSuccess');
	Route::any('payment/cancel', 'PayCheckOutController@paymentcancel');
	Route::any('payment/process-paypal-adaptive', 'ProcessPaypalAdaptivePaymentController@processPaypalPost');
	Route::post('subscribe-newsletter', 'HomeController@postSubscribeNewsletter');
	Route::controller('unsubscribe/mail/{unsubscribe_code}', 'UnSubscribeController');

	// Cron
	Route::controller('cron/fetch-exchange-rates', 'FetchExchageRatesCronController');
	Route::controller('cron/send-newsletter', 'SendNewsletterCronController');
	Route::controller('cron/mass-mail', 'MassMailCronController');

	Route::get(
		'/{path}', function($path) {
	    $path = substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
	    $slugs = explode('/', $path);

	    $check = function($page, $slugs) use(&$check) {
	        if($page->parent_category_id == 1) {
	            return true;
	        }

	        //$parent = ProductCategory::find($page->parent_category_id);
	        $parent = Products::getCategoryDetails($page->parent_category_id);
	        if($parent->seo_category_name == array_pop($slugs)) {
	            return $check($parent, $slugs);
	        }
	    };
		$error = true;
	    foreach(Products::getCategoryDetailsBySlug(array_pop($slugs)) as $page) {
	        if($check($page, $slugs)) {
	        	$error = true;
	            break;
	        }
	    }
	    //todo : handle error
	    if(!isset($page)) {
	    	App::abort(404);
			//return ProductController::showList(0);
		}
		else {
			return ProductController::showList($page->id);
		}
	})->where('path', '.+');

	Route::get('/', 'HomeController@getIndex');


});

?>