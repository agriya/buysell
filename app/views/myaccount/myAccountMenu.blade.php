<!-- BEGIN: DESKTOP VIEW -->
<div class="acc-mobile margin-bottom-20">
	<div class="clearfix acc-mobilebutton">
		<a href="javascript:void(0);" class="acc_dropdown btn blue">
			@if(Input::get('acc_menu') == "")
				<i class="fa fa-chevron-circle-down pull-right margin-top-3"></i> <span>Show Account Menu</span>
			@else
				<i class="fa fa-chevron-circle-up pull-right margin-top-3"></i> <span>Hide Account Menu</span>
			@endif
		 </a>
	</div>

	<div id="acc_menu" class="mob-innerlist" @if(Input::get('acc_menu') == "") @endif>
		<ul class="manageacc-menu list-unstyled clearfix">
			<li {{ (Request::is('users/myaccount') ? 'class="active"' : '') }}>
				<a href="{{ URL::to('users/myaccount') }}"><i class="fa fa-edit"></i>{{ Lang::get('common.edit_profile')}}</a>
			</li>
			@if(CUtil::isUserAllowedToAddProduct())
                <li {{ (Request::is('shop/users/shop-details*') || Request::is('shop/users/shop-policy-details') || Request::is('myproducts') || Request::is('product/add') || Request::is('taxations*') || Request::is('purchases/my-sales') || Request::is('purchases/sales-order-details/*') || Request::is('shipping-template') || Request::is('shipping-template/*') || Request::is('coupons') || Request::is('coupons/*') || Request::is('users/request-seller*') || Request::is('importer') || Request::is('deals/*') || Request::is('variations*') ? 'class="active"' : '') }}>
                    <a href="" class="shop-accmenu">
                        @if(Input::get('shop_menu') == "")
                            <i class="fa fa-truck"></i><i class="fa fa-chevron-down pull-right margin-top-3"></i> <span>{{ Lang::get('common.manage_shop') }}</span>
                        @else
                            <i class="fa fa-truck"></i><i class="fa fa-chevron-up pull-right margin-top-3"></i> <span>{{ Lang::get('common.manage_shop') }}</span>
                        @endif
                    </a>
                    <ul class="list-unstyled" id="shop_menu">
                        <li {{ (Request::is('shop/users/shop-details*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('shop/users/shop-details') }}"><i class="fa fa-cog"></i> {{ Lang::get('common.shop_settings')}}</a>
                        </li>
                        <li {{ (Request::is('shop/users/shop-policy-details') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('shop/users/shop-policy-details') }}"><i class="fa fa-truck"></i> {{ Lang::get('common.shop_policies')}}</a>
                        </li>
                        <li {{ (Request::is('myproducts') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('myproducts') }}"><i class="fa fa-list-alt"></i> {{ Lang::get('common.my_products')}}</a>
                        </li>
                        <li {{ (Request::is('product/add') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('product/add') }}"><i class="fa fa-plus"></i> {{ Lang::get('common.add_product')}}</a>
                        </li>
                        <li {{ (Request::is('taxations*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::action('TaxationsController@getIndex') }}"><i class="fa fa-inbox"></i> {{ Lang::get('common.manage_taxations')}}</a>
                        </li>
                        <li {{ ((Request::is('purchases/my-sales') || Request::is('purchases/sales-order-details/*')) ? 'class="active"' : '') }}>
                            <a href="{{ URL::action('PurchasesController@getMySales') }}"><i class="fa fa-line-chart"></i> {{ Lang::get('common.my_sales')}}</a>
                        </li>
                        <li {{ ((Request::is('shipping-template') || Request::is('shipping-template/*')) ? 'class="active"' : '') }}>
                            <a href="{{ URL::action('ShippingTemplateController@getIndex') }}"><i class="fa fa fa-plane"></i> {{ Lang::get('common.my_shipping_templates')}}</a>
                        </li>
                        <li {{ ((Request::is('coupons') || Request::is('coupons/*')) ? 'class="active"' : '') }}>
                            <a href="{{ URL::action('CouponsController@getIndex') }}"><i class="fa fa-scissors"></i> {{ Lang::get('common.my_coupons')}}</a>
                        </li>

                        @if(CUtil::isUserAllowedToAddProduct() AND CUtil::chkIsAllowedModule('deals'))
                            <li {{ (Request::is('deals') || Request::is('deals/*') ? 'class="active"' : '') }}>
                                <a href="{{ URL::to('deals/my-deals') }}"><i class="fa fa-tags"></i> {{ Lang::get('deals::deals.my_deals_menu') }}</a>
                            </li>
                        @endif

                        @if(CUtil::isUserAllowedToAddProduct() AND CUtil::chkIsAllowedModule('importer'))
                            <li {{ (Request::is('importer') || Request::is('importer/*') ? 'class="active"' : '') }}>
                                <a href="{{ URL::to('importer') }}"><i class="fa fa-bar-chart"></i>{{ trans('common.csv_importer') }}</a>
                            </li>
                        @endif

                        @if(CUtil::isUserAllowedToAddProduct() AND CUtil::chkIsAllowedModule('variations'))
                            <li {{ (Request::is('variations') || Request::is('variations/add-variation*') ? 'class="active"' : '') }}>
                                <a href="{{ URL::to('variations') }}"><i class="fa fa-vine"></i> {{ Lang::get('variations::variations.my_variations_menu') }}</a>
                            </li>
                            <li {{ (Request::is('variations/groups') || Request::is('variations/add-group*') ? 'class="active"' : '') }}>
                                <a href="{{ URL::to('variations/groups') }}"><i class="fa fa-wechat"></i> {{ Lang::get('variations::variations.my_variation_groups_menu') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
			@else
				<li {{ (Request::is('users/request-seller*') ? 'class="active"' : '') }}>
					<a href="{{ URL::action('AccountController@getSellerRequest') }}"><i class="fa fa-user"><sup class="fa fa-dollar"></sup></i>{{ Lang::get('common.want_to_become_a_seller') }}</a>
				</li>
			@endif
			<li {{ ((Request::is('purchases/*') && !Request::is('purchases/my-sales') && !Request::is('purchases/sales-order-details/*')) ? 'class="active"' : '') }}>
				<a href="{{ URL::action('PurchasesController@getIndex') }}"><i class="fa fa-shopping-cart"></i> {{ Lang::get('common.my_purchases')}}</a>
			</li>
			<li {{ (Request::is('invoice*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('InvoiceController@getIndex') }}"><i class="fa fa-file-text-o"></i> {{ Lang::get('common.my_invoices')}}</a>
			</li>
			<li {{ (Request::is('feedback*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('FeedbackController@getIndex') }}"><i class="fa fa-comments-o"></i> {{ Lang::get('common.manage_feedbacks')}}</a>
			</li>
			<li {{ (Request::is('addresses/*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('AddressesController@getIndex') }}"><i class="fa fa-home"></i> {{ Lang::get('common.my_addresses')}}</a>
			</li>
			<li {{ (Request::is('mycollections/*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('MyCollectionsController@getIndex') }}"><i class="fa fa-list-alt"></i> {{ Lang::get('common.my_collections')}}</a>
			</li>
			<li {{ (Request::is('walletaccount*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('WalletAccountController@getIndex') }}"><i class="fa fa-cogs"></i> {{ Lang::get('common.my_wallet')}}</a>
			</li>
			<li {{ (Request::is('users/my-withdrawals*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('MyWithdrawalController@getIndex') }}"><i class="fa fa-won"></i> {{ Lang::get('common.my_withdrawals')}}</a>
			</li>
			<li {{ (Request::is('transactions*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('TransactionsController@getIndex') }}"><i class="fa fa-history"></i> {{ Lang::get('common.transaction_history')}}</a>
			</li>
			<li {{ (Request::is('messages*') ? 'class="active"' : '') }}>
				<a href="{{ URL::action('MessagingController@getIndex') }}"><i class="fa fa-envelope"></i> {{ Lang::get('common.mail_box')}}</a>
			</li>
			<li {{ (Request::is('users/logout')) }}>
				<a href="{{ URL::to('users/logout') }}"><i class="fa fa-sign-out"></i> {{ Lang::get('common.logout')}}</a>
			</li>
		</ul>
	</div>
</div>
<!-- END: DESKTOP VIEW -->