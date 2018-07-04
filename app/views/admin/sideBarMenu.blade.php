<!-- BEGIN: SIDEBAR -->
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <li class="sidebar-toggler-wrapper">
                <!-- BEGIN: SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler mb15"></div>
                <!-- END: SIDEBAR TOGGLER BUTTON -->
            </li>

            <li {{ (Request::is('admin') ? 'class="start active"' : '') }}>
            	<a href="{{ URL::to('admin') }}">
                	<i class="fa fa-dashboard"></i> <span class="title">{{trans('admin/accountmenu.dashboard')}}</span>
                </a>
            </li>

            <li {{ ((Request::is('admin/config-manage/*') || Request::is('admin/config-manage') || Request::is('admin/index-banner') || Request::is('admin/site-logo/*') || Request::is('admin/site-logo') || Request::is('admin/manage-favorite-sellers') || Request::is('admin/manage-toppicks-users') || Request::is('admin/manage-favorite-products')) ? 'class="active open"' : '') }}>
            	<a href="javascript:void(0);"><i class="fa fa-gear"></i> <span class="title">{{trans('admin/accountmenu.settings')}}</span> <span class="arrow open"></span></a>
            	<ul class="sub-menu">
                	<li {{ (Request::is('admin/config-manage/*') || Request::is('admin/config-manage') ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/config-manage') }}">
                        	<i class="fa fa-gear"></i> <span class="title">{{trans('admin/accountmenu.settings')}}</span>
                        </a>
                    </li>
                    <li {{ (Request::is('admin/site-logo/*') || Request::is('admin/site-logo') ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminSiteLogoController@getIndex') }}">
                        	<i class="fa fa-bullseye"></i> <span class="title">{{trans('admin/accountmenu.site_logo')}}</span>
                        </a>
                    </li>
                    <li {{ ((Request::is('admin/index-banner') || Request::is('admin/manage-favorite-sellers') || Request::is('admin/manage-toppicks-users') || Request::is('admin/manage-favorite-products')) ? 'class="active open"' : '') }}>
                    	<a href="javascript:void(0);">
                        	<i class="fa fa-gears"></i> <span class="title">{{trans('admin/accountmenu.index_page_settings')}}</span> <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li {{ (Request::is('admin/index-banner') ? 'class="active"' : '') }} >
                            	<a href="{{ URL::to('admin/index-banner') }}">
                                	<i class="fa fa-arrow-circle-o-right"></i> {{trans('admin/accountmenu.index_banner')}}
                                </a>
                            </li>
                            <li {{ (Request::is('admin/manage-favorite-sellers') ? 'class="active"' : '') }}>
                            	<a href="{{ URL::action('AdminUserController@getManageFeaturedSellers') }}">
                                	<i class="fa fa-arrow-circle-o-right"></i> {{trans('admin/accountmenu.featured_sellers')}}
                                </a>
                            </li>
                            <li {{ (Request::is('admin/manage-favorite-products') ? 'class="active"' : '') }}>
                            	<a href="{{ URL::action('AdminUserController@getManageFavoriteProducts') }}">
                                	<i class="fa fa-arrow-circle-o-right"></i> {{trans('admin/accountmenu.favorite_products')}}
                                </a>
                            </li>
                            <li {{ (Request::is('admin/manage-toppicks-users') ? 'class="active"' : '') }}>
                            	<a href="{{ URL::action('AdminUserController@getManageToppicksUsers') }}">
                                	<i class="fa fa-arrow-circle-o-right"></i> {{trans('admin/accountmenu.top_pick_users')}}
                                </a>
                             </li>
                        </ul>
                    </li>
				</ul>
			<li>

            <li {{ ((Request::is('admin/users') || Request::is('admin/users/*') || Request::is('admin/shops') || Request::is('admin/shops/*') || Request::is('admin/seller-requests') || Request::is('admin/seller-requests/*') || Request::is('admin/group*')) ? 'class="active open"' : '') }}>
                <a href="javascript:void(0);"><i class="fa fa-user"></i> <span class="title">{{trans('admin/accountmenu.member_and_shop')}}</span> <span class="arrow open"></span></a>
				<ul class="sub-menu">
					<li {{ ((Request::is('admin/users') || Request::is('admin/users/*')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/users') }}"><i class="fa fa-user"></i> {{trans('admin/accountmenu.manage_member')}}</a>
                    </li>
                    <li {{ ((Request::is('admin/shops') || Request::is('admin/shops/*')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/shops') }}"><i class="fa fa-truck"></i> {{trans('admin/accountmenu.manage_shop')}}</a>
                    </li>
                    <li {{ ((Request::is('admin/seller-requests') || Request::is('admin/seller-requests/*')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::action('AdminSellerRequestController@getIndex') }}">
                    		<i class="fa fa-tags new-icon"><sup class="fa fa-question-circle"></sup></i> {{trans('admin/accountmenu.seller_request')}}
                        </a>
                    </li>
                    <li {{ (( Request::is('admin/group*')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/group') }}"><i class="fa fa-group"></i> {{trans('admin/accountmenu.manage_user_groups')}}</a>
                    </li>
				</ul>
			<li>
			<li {{ ((Request::is('admin/product/list') || Request::is('admin/product-comments/index') || Request::is('admin/sales-report') || Request::is('admin/sales-report/*') || Request::is('admin/manage-product-catalog') || Request::is('admin/product-attributes') || Request::is('admin/taxations/*') || Request::is('admin/taxations') || Request::is('admin/shipping-template/*') || Request::is('admin/shipping-template') || Request::is('admin/cancellation-policy/*') || Request::is('admin/cancellation-policy') || Request::is('admin/purchases/*') || Request::is('admin/feedback') || Request::is('admin/feedback/*')) ? 'class="active open"' : '') }}>
                <a href="javascript:void(0);"><i class="fa fa-briefcase"></i> <span class="title">{{trans('admin/accountmenu.marketplace')}}</span> <span class="arrow open"></span></a>
				<ul class="sub-menu">
					<li {{ (Request::is('admin/product/list') ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/product/list') }}"><i class="fa fa-indent"></i> {{trans('admin/accountmenu.manage_product')}}</a>
                    </li>
               		<!-- <li {{ (Request::is('admin/product-comments/index') ? 'class="active"' : '') }}>
               			<a href="{{ URL::action('AdminProductCommentsController@getIndex') }}"><i class="fa fa-comments"></i> {{trans('admin/accountmenu.manage_comments')}}</a>
                    </li> -->
                    <li {{ (Request::is('admin/sales-report') || Request::is('admin/sales-report/*') ? 'class="active open"' : '') }}><a href="javascript:void(0);"><i class="fa fa-comments"></i> <span class="title">{{trans('admin/accountmenu.sales_report')}}</span> <span class="arrow open"></span></a>
                    	<ul class="sub-menu">
                        	<li {{ ((Request::is('admin/sales-report') || Request::is('admin/sales-report/index') || Request::is('admin/sales-report/product/*')) ? 'class="active"' : '') }}>
                                <a href="{{ URL::action('AdminSalesController@getIndex') }}">
                                	<i class="fa fa-line-chart"></i> <span class="title">{{trans('admin/accountmenu.product_wise')}}</span>
                                </a>
                            </li>
                            <li {{ ((Request::is('admin/sales-report/member-wise') || Request::is('admin/sales-report/member/*') ? 'class="active"' : '')) }}>
                                <a href="{{ URL::action('AdminSalesController@getMemberWise') }}">
                                	<i class="fa fa-bar-chart"></i> <span class="title">{{trans('admin/accountmenu.owner_wise')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li {{ (Request::is('admin/manage-product-catalog') ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/manage-product-catalog') }}">
                        	<i class="fa fa-th-large"></i> <span class="title">{{trans('admin/accountmenu.manage_categories')}}</span>
                        </a>
                    </li>
                    <li {{ (Request::is('admin/product-attributes') ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/product-attributes') }}">
                        	<i class="fa fa-tasks"></i> <span class="title">{{trans('admin/accountmenu.manage_attributes')}}</span>
                        </a>
                    </li>
                    <li {{ ((Request::is('admin/taxations/*') || Request::is('admin/taxations')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/taxations') }}">
                        	<i class="fa fa-folder-open-o"></i> <span class="title">{{trans('admin/accountmenu.manage_taxations')}}</span>
                        </a>
                    </li>
                    <li {{ ((Request::is('admin/shipping-template/*') || Request::is('admin/shipping-template')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminShippingTemplateController@getIndex') }}">
                        	<i class="fa fa-plane"></i> <span class="title">{{trans('admin/accountmenu.shipping_templates')}}</span>
                        </a>
                    </li>
                    <li {{ ((Request::is('admin/cancellation-policy/*') || Request::is('admin/cancellation-policy')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminCancellationPolicyController@getIndex') }}">
                        	<i class="fa fa-file-text-o new-icon"><sup class="fa fa-times"></sup></i> <span class="title">{{trans('admin/accountmenu.cancellation_policy')}}</span>
                        </a>
                    </li>
                    <li {{ (Request::is('admin/purchases/*') ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminPurchasesController@getIndex') }}">
                        	<i class="fa fa-tags"></i> <span class="title">{{trans('admin/accountmenu.sales')}}</span>
                        </a>
                    </li>
                    <li {{ ((Request::is('admin/feedback') || Request::is('admin/feedback/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminManageFeedbackController@getIndex') }}">
                        	<i class="fa fa-comment-o"></i> <span class="title">{{trans('admin/accountmenu.manage_feedbacks')}}</span>
                        </a>
                    </li>
				</ul>
			<li>
			 @if(CUtil::chkIsAllowedModule('deals'))
            	<li {{ ((Request::is('admin/deals/*')) ? 'class="active open"' : '') }}>
                    <a href="javascript:void(0);"><i class="fa fa-tag"></i> <span class="title">{{ Lang::get('deals::deals.deals') }} - <small class="badge">{{ Lang::get('admin/accountmenu.plugin') }}</small></span> <span class="arrow open"></span></a>
                    <ul class="sub-menu">
                        <li {{ (Request::is('admin/deals/manage-deals*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/deals/manage-deals') }}">
                                <i class="fa fa-tags"></i> <span class="title">{{ Lang::get('deals::deals.admin_manage_deals_head') }}</span>
                            </a>
                        </li>
                        <li {{ (Request::is('admin/deals/manage-featured-deals*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/deals/manage-featured-deals') }}">
                                <i class="fa fa-file-text new-icon"><sup class="fa fa-tag"></sup></i>
								<span class="title">{{  Lang::get('deals::deals.admin_featured_deals_head') }}</span>
                            </a>
                        </li>
                        <li {{ (( Request::is('admin/deals/manage-featured-requests/*')) ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/deals/manage-featured-requests/all') }}">
                                <i class="fa fa fa-file new-icon"><sup class="fa fa-tag"></sup></i>
								<span class="title">{{ Lang::get('deals::deals.admin_featured_requests_head') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if(CUtil::chkIsAllowedModule('featuredproducts'))
            	<li {{ ((Request::is('admin/featuredproducts/*')) ? 'class="active open"' : '') }}>
                    <a href="javascript:void(0);"><i class="fa fa-star"></i> <span class="title">{{ Lang::get('featuredproducts::featuredproducts.featured_products') }} - <small class="badge">{{ Lang::get('admin/accountmenu.plugin') }}</small></span> <span class="arrow open"></span></a>
                    <ul class="sub-menu">
                        <li {{ (Request::is('admin/featuredproducts/manage-featured-product-plans*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/featuredproducts/manage-featured-product-plans') }}">
                                <i class="fa fa-cogs"></i> <span class="title">{{ Lang::get('featuredproducts::featuredproducts.settings') }}</span>
                            </a>
                        </li>
                        <li {{ (Request::is('admin/featuredproducts/manage-featured-products*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/featuredproducts/manage-featured-products') }}">
                                <i class="fa fa-shopping-cart"></i> <span class="title">{{  Lang::get('featuredproducts::featuredproducts.products') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(CUtil::chkIsAllowedModule('featuredsellers'))
            	<li {{ ((Request::is('admin/featuredsellers/*')) ? 'class="active open"' : '') }}>
                    <a href="javascript:void(0);"><i class="fa fa-star-o"></i> <span class="title">{{ Lang::get('featuredsellers::featuredsellers.featured_sellers') }} - <small class="badge">{{ Lang::get('admin/accountmenu.plugin') }}</small></span> <span class="arrow open"></span></a>
                    <ul class="sub-menu">
                        <li {{ (Request::is('admin/featuredsellers/manage-featured-sellers-plans*') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/featuredsellers/manage-featured-sellers-plans') }}">
                                <i class="fa fa-cogs"></i> <span class="title">{{ Lang::get('featuredsellers::featuredsellers.settings') }}</span>
                            </a>
                        </li>
                        <li {{ (Request::is('admin/featuredsellers/manage-featured-sellers') ? 'class="active"' : '') }}>
                            <a href="{{ URL::to('admin/featuredsellers/manage-featured-sellers') }}">
                                <i class="fa fa-shopping-cart"></i> <span class="title">{{  Lang::get('featuredsellers::featuredsellers.sellers') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <li {{ ((Request::is('admin/sudopay/*') || Request::is('admin/sudopay/sudopay-transaction-list/*') || Request::is('admin/unpaid-invoice-list/*') || Request::is('admin/withdrawals/*') || Request::is('admin/transactions') || Request::is('admin/transactions/*')) ? 'class="active open"' : '') }}>
                <a href="javascript:void(0);"><i class="fa fa-money"></i> <span class="title">{{trans('admin/accountmenu.payment')}}</span> <span class="arrow open"></span></a>
				<ul class="sub-menu">
					@if(CUtil::chkIsAllowedModule('sudopay'))
					<li {{ (Request::is('admin/sudopay/manage-payment-gateways') ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/sudopay/manage-payment-gateways') }}">
                        	<i class="fa fa-dollar"></i> <span class="title">{{trans('sudopay::sudopay.payment_gateways_menu')}}</span>
                        </a>
                    </li>
                    <li {{ (Request::is('admin/sudopay/sudopay-transaction-list') ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/sudopay/sudopay-transaction-list') }}">
                        	<i class="fa fa-refresh"></i> <span class="title">{{trans('sudopay::sudopay.sudopay_transaction')}}</span>
                        </a>
                    </li>
                    <li {{ (Request::is('admin/sudopay/sudopay-ipn-logs') ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/sudopay/sudopay-ipn-logs') }}">
                        	<i class="fa fa-database"></i> <span class="title">{{trans('sudopay::sudopay.sudopay_ipn_logs')}}</span>
                        </a>
                    </li>
                    @endif
            		<li {{ (Request::is('admin/unpaid-invoice-list/*') ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminUnpaidInvoiceListController@getIndex') }}">
                        	<i class="fa fa-comments-o"></i> <span class="title">{{trans('admin/accountmenu.unpaid_invoice')}}</span>
                        </a>
                    </li>
                    <li {{ (Request::is('admin/withdrawals/*') ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminWithdrawalController@getIndex') }}">
                        	<i class="fa fa-won"></i> <span class="title">{{trans('admin/accountmenu.withdrawals')}}</span>
                        </a>
                    </li>
                    <li {{ ((Request::is('admin/transactions') || Request::is('admin/transactions/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminTransactionsController@getIndex') }}">
                        	<i class="fa fa-file-text-o"></i> <span class="title">{{trans('admin/accountmenu.transaction_history')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li {{ (Request::is('admin/site-wallet/index') ? 'class="active"' : '') }}>
            	<a href="{{ URL::action('AdminWalletAccountController@getIndex') }}"><i class="fa fa-briefcase"></i> <span class="title">{{trans('admin/accountmenu.site_wallet')}}</span></a>
            </li>
            <li {{ ((Request::is('admin/reported-products/*') || Request::is('admin/reported-products')) ? 'class="active"' : '') }}>
                <a href="{{ URL::action('AdminReportedProductsController@getIndex') }}">
                	<i class="fa fa-warning"></i> <span class="title">{{trans('admin/accountmenu.reported_products')}}</span>
                </a>
            </li>
            <li {{ ((Request::is('admin/product-category/category-meta-details') || Request::is('admin/meta-details') || Request::is('admin/product-category/category-meta-details/*') || Request::is('admin/meta-details/*')) ? 'class="active open"' : '') }}>
                <a href="javascript:void(0);"><i class="fa fa-language"></i> <span class="title">{{trans('admin/accountmenu.meta_details')}}</span> <span class="arrow open"></span></a>
				<ul class="sub-menu">
                    <li {{ ((Request::is('admin/meta-details', 'admin/meta-details/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminDashboardController@getMetaDetails') }}"><i class="fa fa-cogs"></i> {{trans('admin/accountmenu.common_mata_details')}}</a>
                    </li>            
                    <li {{ ((Request::is('admin/product-category/category-meta-details') || Request::is('admin/product-category/category-meta-details/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminProductCategoryController@getCategoryMetaDetails') }}">
                            <i class="fa fa-list"></i> <span class="title">{{trans('admin/accountmenu.category_meta_details')}}</span>
                        </a>
                    </li>
                 </ul>
            </li>
            
			<li {{ ((Request::is('admin/collections') || Request::is('admin/collections/*')) ? 'class="active"' : '') }}>
				<a href="{{ URL::action('AdminManageCollectionsController@getIndex') }}">
                	<i class="fa fa-th"></i> <span class="title">{{trans('admin/accountmenu.manage_collections')}}</span>
                 </a>
			</li>
			<li {{ ( (Request::is('admin/manage-language','admin/manage-language/*','admin/email-templates/*', 'admin/email-templates')) ? 'class="active open"' : '') }}>
                <a href="javascript:void(0);"><i class="fa fa-language"></i> <span class="title">{{trans('admin/accountmenu.manage_languages')}}</span> <span class="arrow open"></span></a>
				<ul class="sub-menu">
                    <li {{ ((Request::is('admin/email-templates', 'admin/email-templates/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminManageEmailTemplateController@getIndex') }}"><i class="fa fa-cogs"></i> {{trans('admin/accountmenu.edit_email_templates')}}</a>
                    </li>
					<li {{ ((Request::is('admin/manage-language/settings')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/manage-language/settings') }}"><i class="fa fa-cog"></i> {{trans('admin/accountmenu.language_settings')}}</a>
                    </li>
					<li {{ ( (Request::is('admin/manage-language')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/manage-language') }}"><i class="fa fa-list"></i> {{trans('admin/accountmenu.language_list')}}</a>
                    </li>
                    <li {{ ((Request::is('admin/manage-language/file-edit')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/manage-language/file-edit') }}"><i class="fa fa-edit"></i> {{trans('admin/accountmenu.edit_languages')}}</a>
                    </li>
                    <li {{ ((Request::is('admin/manage-language/export')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/manage-language/export') }}"><i class="fa fa-upload"></i> {{trans('admin/accountmenu.export_language')}}</a>
                    </li>
                    <li {{ ((Request::is('admin/manage-language/import')) ? 'class="active"' : '') }}>
                    	<a href="{{ URL::to('admin/manage-language/import') }}"><i class="fa fa-download"></i> {{trans('admin/accountmenu.import_language')}}</a>
                    </li>
				</ul>
			<li>
             <li {{ ((Request::is('admin/manage-banner') || Request::is('admin/manage-banner/*') || Request::is('admin/static-page') || Request::is('admin/static-page/*') || Request::is('admin/sell-static-page') || Request::is('admin/sell-static-page/*')|| Request::is('admin/newsletter-subscriber') || Request::is('admin/newsletter-subscriber/*') || Request::is('admin/mass-email') || Request::is('admin/mass-email/*')) ? 'class="active open"' : '') }}>
                <a href="javascript:void(0);"><i class="fa fa-list-alt"></i> <span class="title">{{trans('admin/accountmenu.general')}}</span> <span class="arrow open"></span></a>
				<ul class="sub-menu">
					<li {{ ( (Request::is('admin/manage-banner') || Request::is('admin/manage-banner/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/manage-banner') }}">
                        	<i class="fa fa-image"></i> <span class="title">{{trans('admin/accountmenu.manage_banner')}}</span>
                        </a>
                    </li>
                	<li {{ ( (Request::is('admin/static-page') || Request::is('admin/static-page/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminStaticPageController@getIndex') }}">
                        	<i class="fa fa-columns"></i> <span class="title">{{trans('admin/accountmenu.static_pages')}}</span>
                        </a>
                    </li>
                    <li {{ ( (Request::is('admin/sell-static-page') || Request::is('admin/sell-static-page/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::action('AdminStaticPageController@getSellStaticPage') }}">
                        	<i class="fa fa-file-o"></i> <span class="title">{{trans('admin/accountmenu.static_sell_page')}}</span>
                        </a>
                    </li>
                    <li {{ ( (Request::is('admin/newsletter-subscriber') || Request::is('admin/newsletter-subscriber/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/newsletter-subscriber/list') }}">
                        	<i class="fa fa-user"></i> <span class="title">{{trans('admin/accountmenu.newsletter_subscriber')}}</span>
                        </a>
                    </li>
                    <li {{ ( (Request::is('admin/mass-email') || Request::is('admin/mass-email/*')) ? 'class="active"' : '') }}>
                        <a href="{{ URL::to('admin/mass-email/list') }}">
                        	<i class="fa fa-envelope"></i> <span class="title">{{trans('admin/accountmenu.mass_mail')}}</span>
                        </a>
                    </li>
				</ul>
			<li>
            <li {{ (Request::is('admin/currency-exchange-rate') ? 'class="active"' : '') }}>
                <a href="{{ URL::to('admin/currency-exchange-rate') }}">
                	<i class="fa fa-dollar"></i><span class="title">{{trans('admin/accountmenu.currency_exchange_rate')}}</span>
                </a>
            </li>
            <li><a href="{{ URL::to('users/logout') }}"><i class="fa fa-sign-out"></i> <span class="title">{{ Lang::get('common.logout')}}</span></a></li>
        </ul>
    </div>
</div>
<!-- END: SIDEBAR -->