<!-- BEGIN: HEADER -->
    <header class="header-bg">
    	<?php
		if(Request::is('users/myaccount') || Request::is('shop/users/*') || Request::is('myproducts') || Request::is('product/add') || Request::is('taxations/*') || Request::is('purchases/*') || Request::is('addresses/*') || Request::is('shipping-template*') || Request::is('mycollections/index') || Request::is('coupons/*') || Request::is('invoice*') || Request::is('wishlist/index') || Request::is('walletaccount/*') || Request::is('messages*') || Request::is('feedback') || Request::is('feedback/*') || Request::is('users/my-withdrawals/index') || Request::is('users/my-withdrawals') || Request::is('transactions') || Request::is('transactions/*') || Request::is('variations') || Request::is('variations/*') || Request::is('importer') || Request::is('importer/*') || Request::is('cart') || Request::is('checkout/*') || Request::is('cart') || Request::is('checkout/*') || Request::is('deals/my-deals') || Request::is('deals/update-deal/*') || Request::is('deals/my-featured-request') || Request::is('deals/set-featured/*') || Request::is('deals/add-deal'))
			$header_menu_cls = 'container-fluid';
		else
			$header_menu_cls = 'container';?>
		<div class="{{$header_menu_cls}}">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle btn btn-info" data-toggle="collapse" data-target=".navbar-collapse">
					<i class="fa fa-align-justify"></i>
				</button>

				<?php $logo_image = CUtil::getSiteLogo();
                    $image_url = isset($logo_image['image_url'])?$logo_image['image_url']:URL::asset('/images/header/logo/logo.png');
                 ?>
                <a href="{{ URL::to('/') }}" title="{{ Config::get('generalConfig.site_name') }}" class="logo">
                    <img src="{{ $image_url }}" alt="{{ Config::get('generalConfig.site_name') }}" />
                </a>
			</div>
			<div role="navigation" class="collapse navbar-collapse">
				<div class="header-right">
					<ul class="list-inline header-nav">
						<li><a href="{{Url::to('buy')}}" title="{{trans('common.buy')}}"><i class="fa fa-shopping-cart text-success"></i> {{trans('common.buy')}}</a></li>
						<li><a href="{{Url::to('sell')}}" title="{{trans('common.sell')}}"><i class="fa fa-line-chart text-warning"></i> {{trans('common.sell')}}</a></li>
						@if(CUtil::chkIsAllowedModule('deals'))
							<li><a href="{{Url::to('deals')}}" title="{{ Lang::get('deals::deals.deals') }}"><i class="fa fa-tags text-danger"></i> {{ Lang::get('deals::deals.deals') }}</a></li>
						@endif
						@if(!CUtil::isMember())
							@if(Config::get('login.enable_facebook') || Config::get('login.enable_twitter'))
								<li class="txt-rig">
									<small class="text-muted">{{trans('common.sign_in_with')}}</small>
									@if(Config::get('login.enable_facebook'))
										<a href="javascript:void(0);" onClick="gotoSocialInPage('{{ URL::action('OAuthController@getAuthorize', 'facebook') }}', 'facebook');" title="{{ Lang::get('auth/form.signin_with_facebook') }}" class="btn btn-xs bg-blue-steel"><i class="fa fa-facebook"></i></a>
									@endif
									@if(Config::get('login.enable_twitter'))
										<a href="javascript:void(0);" onClick="gotoSocialInPage('{{ URL::action('OAuthController@getAuthorize', 'twitter') }}', 'facebook');" title="{{ Lang::get('auth/form.signin_with_twitter') }}" class="btn btn-xs bg-blue"><i class="fa fa-twitter"></i></a>
									@endif
								</li>
							@endif
						@endif
					</ul>

					<div class="clearfix responsive-float @if(!CUtil::isMember()) resp-flt @else @endif">
						<ul class="nav-menu list-inline">
							@if(CUtil::isMember())
								<?php
									$user_id = BasicCUtil::getLoggedUserId();
									$user_details = CUtil::getUserDetails($user_id);
									$user_image = CUtil::getUserPersonalImage($user_id, "small", false);
								?>
								@if(CUtil::isAdmin())
									<li><a href="{{ URL::to('admin') }}">{{ Lang::get('common.admin') }}</a></li>
								@endif
                                <li class="profile-dropmenu dropdown {{ (Request::is('users/myaccount') ? 'active' : '') }}">
                                    <a id="drop1" role="button" data-toggle="dropdown" href="{{ URL::to('users/myaccount') }}">
                                        <img src="{{ $user_image['image_url'] }}" {{ $user_image['image_attr'] }} alt="{{ $user_details['display_name'] }}" title="{{ $user_details['display_name'] }}" /> {{ Lang::get('common.you') }}
                                        <span class="fa fa-caret-down text-muted"></span>
                                    </a>

                                    <ul id="menu1" class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="drop1">
                                        <li class="arrow">
                                        	<a href="{{ $user_details['profile_url'] }}">
                                        		<?php $user_thumb_image = CUtil::getUserPersonalImage($user_id, "thmub", false); ?>
                                                <img src="{{ $user_thumb_image['image_url'] }}" {{ $user_thumb_image['image_attr'] }} alt="{{ $user_details['display_name'] }}" title="{{ $user_details['display_name'] }}" />
                                                <div class="user-details">
                                                    <h3>
                                                        @if($user_details['user_name'])
                                                            {{isset($user_details['user_name'])?$user_details['user_name']:Lang::get('common.my_account')}}
                                                        @else
                                                            {{isset($user_details['display_name'])?$user_details['display_name']:Lang::get('common.my_account')}}
                                                        @endif
                                                    </h3>
                                                    <p class="btn btn-white btn-xs">{{ Lang::get('common.view_profile')}} <i class="fa fa-chevron-right"></i></p>
                                                </div>
                                            </a>
                                        </li>
                                        @if(CUtil::isUserAllowedToAddProduct() && $user_details['is_shop_owner'] == 'Yes')
											<li {{ (Request::is('myproducts') ? 'class="active"' : '') }}><a href="{{ URL::to('myproducts') }}">{{ Lang::get('common.my_products')}}</a></li>
											<li {{ ((Request::is('purchases/my-sales') || Request::is('purchases/sales-order-details/*')) ? 'class="active"' : '') }}>
												<a href="{{ URL::action('PurchasesController@getMySales') }}">{{ Lang::get('common.my_sales')}}</a>
											</li>
										@else
											@if(!CUtil::isUserAllowedToAddProduct())
												<li {{ (Request::is('users/request-seller*') ? 'class="active"' : '') }}>
													<a href="{{ URL::action('AccountController@getSellerRequest') }}">{{ Lang::get('common.want_to_become_seller')}}</a>
												</li>
											@endif
											<li {{ ((Request::is('purchases/*') && !Request::is('purchases/my-sales')) ? 'class="active"' : '') }}>
												<a href="{{ URL::action('PurchasesController@getIndex') }}">{{ Lang::get('common.my_purchases')}}</a>
	                                        </li>
	                                        <li {{ (Request::is('mycollections/*') ? 'class="active"' : '') }}>
												<a href="{{ URL::action('MyCollectionsController@getIndex') }}">{{ Lang::get('common.my_collections')}}</a>
	                                        </li>
										@endif
                                        <li {{ (Request::is('messages*') ? 'class="active"' : '') }}>
                                        	<a href="{{ URL::action('MessagingController@getIndex') }}">{{ Lang::get('common.mail_box') }}</a>
                                        </li>
                                        <li {{ (Request::is('users/myaccount') ? 'class="active"' : '') }}>
											<a href="{{ URL::to('users/myaccount') }}">{{ Lang::get('common.account_settings')}}</a>
										</li>
                                        <li {{ (Request::is('users/logout') ? 'class="active"' : '') }}><a href="{{ URL::to('users/logout') }}">{{ Lang::get('common.sign_out')}}</a></li>
                                	</ul>
                                </li>
							@else
								<li><a href="{{ URL::to('users/login') }}">{{trans('common.sign_in')}}</a></li>
							@endif
							<li {{ (Request::is('cart') ? 'class="active"' : '') }}>
								<?php
									$this->ShowCartService = new ShowCartService();
									$cart_total_count = $this->ShowCartService->getCartItemCount();
								?>
								<a href="{{ URL::to('cart') }}" class="ie-hdrcart">
                                    <img src="{{ URL::asset('/images/general/shopping-cart.png') }}" alt="shopping"> {{$cart_total_count}}
                                </a>
							</li>
						</ul>

						<?php
							$page_to_search = 'product';
							if(Request::is('shop') || Request::is('shop/')){
								$page_to_search = 'shop';
							}
							if(Request::is('users') || Request::is('users/')){
								$page_to_search = 'user';
							}
							$action = array('ProductController@showList');
							$input_name = 'tag_search';
							switch($page_to_search)
							{
								case 'product':
										$action = 'product';
										$input_name = 'tag_search';
										$text_disp = trans('common.all_items');
									break;

								case 'shop':
										$action = 'shop';//array('ListShopController');
										$input_name = 'shop_name';
										$text_disp = trans('common.shops');
									break;

								case 'user':
										$action = 'users';//array('AuthController@getIndex');
										$input_name = 'uname';
										$text_disp = trans('common.people');
									break;
							}
						 ?>

						<!-- BEGIN: DESK VIEW -->
						{{ Form::open(array('url' => $action, 'id'=>'searchHeaderFrm', 'method'=>'post')) }}
						<div class="search-block">
							<div class="input-group">
								<span class="input-group-addon dropdown" data-toggle="dropdown"><i class="fa fa-caret-down pull-right margin-top-3"></i>
								<span id="header_srch_text_to_disp" class="option-txt">{{$text_disp}}</span></span>
								<ul class="dropdown-menu all-option">
									<li data-action="product" class="js-action-selection"><a href="javascript:void(0);">{{trans('common.all_items')}}</a></li>
									<li data-action="user" class="js-action-selection"><a href="javascript:void(0);">{{trans('common.people')}}</a></li>
									<li data-action="shop" class="js-action-selection"><a href="javascript:void(0);">{{trans('common.shops')}}</a></li>
								</ul>
								{{Form::text($input_name, Input::get($input_name), array('id'=> 'header_tag_search', 'class' => 'form-control', 'placeholder' => trans('common.what_are_you_looking_for')) )}}
								<span class="input-group-addon search-option" id="js-search-header"><i class="fa fa-search"></i></span>
							</div>
						</div>
						{{Form::close()}}
						<!-- END: DESK VIEW -->

						<!-- BEGIN: ALL ITEMS DROPDOWN -->
						<div class="dropdown all-items">
							<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><span class="caret pull-right"></span>
							<span>{{trans('common.browse')}}</span></button>
							<ul class="dropdown-menu">
								<?php $browse_cat = Products::getTopCategories(); ?>
								@if(count($browse_cat) > 0)
									@foreach($browse_cat as $cat)
										<li><a href="{{ Url::to('browse/'.$cat->seo_category_name) }}">{{ $cat->category_name }}</a></li>
									@endforeach
								@else
									<li class="pad10"><p class="alert alert-info margin-0">{{trans('common.no_category')}}</p></li>
								@endif
							</ul>
						</div>
						<!-- END: ALL ITEMS DROPDOWN -->

						<!-- BEGIN: RESPONSIVE VIEW -->
                        {{ Form::open(array('url' => $action, 'id'=>'searchHeaderFrm', 'method'=>'post')) }}
						<div class="responsive-search pull-right">
							<a href="javascript:void(0);" class="btn btn-info"><i class="fa fa-search"></i></a>
							<div class="mobile-input">
								<div class="input-group">
									<span class="input-group-addon dropdown" data-toggle="dropdown"><i class="fa fa-caret-down pull-right margin-top-3"></i>
									<span id="header_srch_text_to_disp" class="option-txt">{{$text_disp}}</span></span>
									<ul class="dropdown-menu all-option">
										<li data-action="product" class="js-action-selection"><a href="javascript:void(0);">{{trans('common.all_items')}}</a></li>
										<li data-action="user" class="js-action-selection"><a href="javascript:void(0);">{{trans('common.people')}}</a></li>
										<li data-action="shop" class="js-action-selection"><a href="javascript:void(0);">{{trans('common.shops')}}</a></li>
									</ul>
									<input type="text" name="{{$input_name}}" value="{{ Input::get($input_name) }}" id="header_tag_search" class="form-control" placeholder="{{trans('common.what_are_you_looking_for')}}"/>
									<span class="input-group-addon resp-serach" id="js-search-header-resp"><i class="fa fa-search"></i></span>
								</div>
							</div>
						</div>
                        {{Form::close()}}
						<!-- END: RESPONSIVE VIEW -->
					</div>
				</div>
			</div>
        </div>
    </header>
<!-- END: HEADER -->