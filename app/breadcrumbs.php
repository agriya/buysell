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
//Login
Breadcrumbs::register('login', function($breadcrumbs) {
    $breadcrumbs->push(trans('breadcrumbs.user_home'), URL::to('/users'));
    $breadcrumbs->push(trans('breadcrumbs.login'), URL::to('/'));
});
Breadcrumbs::register('users_forgotpassword', function($breadcrumbs) {
    $breadcrumbs->push(trans('breadcrumbs.user_home'), URL::to('/users'));
    $breadcrumbs->push(trans('breadcrumbs.login'), URL::to('/users/login'));
    $breadcrumbs->push(trans('breadcrumbs.users_forgotpassword'), URL::to('/'));
});

//Signup
Breadcrumbs::register('signup', function($breadcrumbs) {
    $breadcrumbs->push(trans('breadcrumbs.user_home'), URL::to('/users'));
    $breadcrumbs->push(trans('breadcrumbs.signup'), URL::to('/'));
});

//View Cart
Breadcrumbs::register('cart', function($breadcrumbs) {
    $breadcrumbs->push(trans('breadcrumbs.user_home'), URL::to('/users'));
    $breadcrumbs->push(trans('breadcrumbs.cart'), URL::to('/'));
});
Breadcrumbs::register('checkout', function($breadcrumbs) {
    $breadcrumbs->push(trans('breadcrumbs.user_home'), URL::to('/users'));
    $breadcrumbs->push(trans('breadcrumbs.cart'), URL::to('/cart'));
    $breadcrumbs->push(trans('breadcrumbs.checkout'), URL::to('/'));
});
Breadcrumbs::register('pay-checkout', function($breadcrumbs) {
    $breadcrumbs->push(trans('breadcrumbs.user_home'), URL::to('/users'));
    $breadcrumbs->push(trans('breadcrumbs.cart'), URL::to('/cart'));
    $breadcrumbs->push(trans('breadcrumbs.checkout'), URL::to('/checkout'));
    $breadcrumbs->push(trans('breadcrumbs.pay-checkout'), URL::to('/'));
});

//Admin
//Manage Member
Breadcrumbs::register('home', function($breadcrumbs) {
    $breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/'));
});
Breadcrumbs::register('users_add', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_members'), URL::to('/admin/users'));
	$breadcrumbs->push(trans('admin/breadcrumb.users_add'), URL::to('/'));
});
Breadcrumbs::register('users_edit', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_members'), URL::to('/admin/users'));
	$breadcrumbs->push(trans('admin/breadcrumb.users_edit'), URL::to('/'));
});

//Manage Group
Breadcrumbs::register('group', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.group'), URL::to('/'));
});
Breadcrumbs::register('add_group', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.group'), URL::to('/admin/group'));
	$breadcrumbs->push(trans('admin/breadcrumb.group_add'), URL::to('/'));
});
Breadcrumbs::register('edit_group', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.group'), URL::to('/admin/group'));
	$breadcrumbs->push(trans('admin/breadcrumb.group_edit'), URL::to('/'));
});
Breadcrumbs::register('member_group', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.group'), URL::to('/admin/group'));
	$breadcrumbs->push(trans('admin/breadcrumb.group_member'), URL::to('/'));
});

//Manage Product
Breadcrumbs::register('product_list', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_list'), URL::to('/'));
});
Breadcrumbs::register('product_add', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_list'), URL::to('/admin/product/list'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_add'), URL::to('/'));
});
Breadcrumbs::register('product_edit', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_list'), URL::to('/admin/product/list'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_edit'), URL::to('/'));
});
Breadcrumbs::register('product_view', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_list'), URL::to('/admin/product/list'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_view'), URL::to('/'));
});
Breadcrumbs::register('manage_stocks', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_list'), URL::to('/admin/product/list'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_stocks'), URL::to('/'));
});

//Manage Product Catalog
Breadcrumbs::register('manage-product-catalog', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage-product-catalog'), URL::to('/'));
});

//Product Attributes Management
Breadcrumbs::register('product-attributes', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product-attributes'), URL::to('/'));
});

//Manage Taxations
Breadcrumbs::register('taxations_index', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.taxations_index'), URL::to('/'));
});
Breadcrumbs::register('taxations_add', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.taxations_index'), URL::to('/admin/taxations/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.taxations_add'), URL::to('/'));
});
Breadcrumbs::register('taxations_update', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.taxations_index'), URL::to('/admin/taxations/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.taxations_update'), URL::to('/'));
});

//Cancellation Policy
Breadcrumbs::register('cancellation_policy', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.cancellation_policy'), URL::to('/'));
});

//My Purchases List
Breadcrumbs::register('purchases_index', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.purchases_index'), URL::to('/'));
});
Breadcrumbs::register('purchases_orderdetails', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.purchases_index'), URL::to('/admin/purchases/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.purchases_orderdetails'), URL::to('/'));
});
//Unpaid Invoices List
Breadcrumbs::register('unpaid_invoices_list', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.unpaid_invoices_list'), URL::to('/'));
});

//Withdrawal Request List
Breadcrumbs::register('withdrawals_index', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.withdrawals_index'), URL::to('/'));
});

//Manage credits List
Breadcrumbs::register('manage_credits', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_members'), URL::to('/admin/users'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_credits'), URL::to('/'));
});

//User Details
Breadcrumbs::register('user_details', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_members'), URL::to('/admin/users'));
	$breadcrumbs->push(trans('admin/breadcrumb.user_details'), URL::to('/'));
});

//Site wallet
Breadcrumbs::register('site_wallet', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.site_wallet'), URL::to('/'));
});

//Manage Shipping template
Breadcrumbs::register('shipping_template_list', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_templates_list'), URL::to('/'));
});
Breadcrumbs::register('shipping_template_add', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_templates_list'), URL::to('/admin/shipping-template/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_templates_add'), URL::to('/'));
});
Breadcrumbs::register('shipping_template_edit', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_templates_list'), URL::to('/admin/shipping-template/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_templates_edit'), URL::to('/'));
});
Breadcrumbs::register('shipping_template_view', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_templates_list'), URL::to('/admin/shipping-template/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.shipping_template_view'), URL::to('/'));
});

//Currency exchange rate
Breadcrumbs::register('currency_exchange_rate', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.currency_exchange_rate'), URL::to('/'));
});

//Config management
Breadcrumbs::register('config_manage', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.config_management'), URL::to('/'));
});

//Site Logo
Breadcrumbs::register('site_logo', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.site_logo'), URL::to('/'));
});

//Manage Members
Breadcrumbs::register('users', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_members'), URL::to('/'));
});

//Manage Members
Breadcrumbs::register('seller_requests', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.seller_request'), URL::to('/'));
});

//Edit Sell Page Static Content
Breadcrumbs::register('edit_sell_page_static_content', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.edit_sell_page_static_content'), URL::to('/'));
});

//newsletter
Breadcrumbs::register('newsletter', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.newsletter'), URL::to('/'));
});

//sales_report
Breadcrumbs::register('sales_report', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_wise_sales_report'), URL::to('/'));
});

//product_sales_report
Breadcrumbs::register('product_sales_report', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_wise_sales_report'), URL::to('/admin/sales-report/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.product_sales_report'), URL::to('/'));
});

//owner_wise_sales_report
Breadcrumbs::register('owner_wise_sales_report', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.owner_wise_sales_report'), URL::to('/'));
});

//taxations
Breadcrumbs::register('taxations', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.taxations'), URL::to('/'));
});

//feedback
Breadcrumbs::register('feedback', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.feedback'), URL::to('/'));
});

//Transaction History
Breadcrumbs::register('transactions', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.transactions'), URL::to('/'));
});

//Category Meta Details
Breadcrumbs::register('category_meta_details', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.category_meta_details'), URL::to('/'));
});

//Manage Collection
Breadcrumbs::register('manage_collection', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_collection'), URL::to('/'));
});

//Reported Products
Breadcrumbs::register('reported_products', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.reported_products'), URL::to('/'));
});

//view_owner_wise_sales_report
Breadcrumbs::register('view_owner_wise_sales_report', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.owner_wise_sales_report'), URL::to('/admin/sales-report/member-wise'));
	$breadcrumbs->push(trans('admin/breadcrumb.view_owner_wise_sales_report'), URL::to('/'));
});

//newsletter Add
Breadcrumbs::register('add', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.newsletter'), URL::to('/admin/newsletter/index'));
	$breadcrumbs->push(trans('admin/breadcrumb.add'), URL::to('/'));
});

//Manage Static Page
Breadcrumbs::register('manage_static_page', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.manage_static_page'), URL::to('/'));
});

//Banner management
Breadcrumbs::register('banner_manage', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.banner_management'), URL::to('/'));
});

Breadcrumbs::register('featured_sellers', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.featured_sellers'), URL::to('/'));
});

Breadcrumbs::register('toppicks_users', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.toppicks_users'), URL::to('/'));
});

Breadcrumbs::register('favorite_produts', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.favorite_produts'), URL::to('/'));
});

//Newsletter Subscriber
Breadcrumbs::register('newsletter_subscriber', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.newsletter_subscriber'), URL::to('/'));
});

//Mass Email
Breadcrumbs::register('mass_email', function($breadcrumbs) {
	$breadcrumbs->push(trans('admin/breadcrumb.home'), URL::to('/admin'));
	$breadcrumbs->push(trans('admin/breadcrumb.mass_email'), URL::to('/'));
});
?>