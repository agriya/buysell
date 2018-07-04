@if(count($product_details) > 0 || $is_search_done)
	<div class="shop-prdctsearch">
		<div class="customnew-bg">
			<div class="row">
				<div class="col-md-7">
					{{ Form::open(array('url' => Request::url(), 'id'=>'productFrm', 'method'=>'get','class' => 'form-inline margin-bottom-5' )) }}
							{{ Form::text('search_product_name', Input::get("search_product_name"), array('class' => 'form-control input-sm margin-top-5', 'placeholder' => trans('shop.search_in_this_shop'))) }}
							<button class="btn blue btn-sm margin-top-5">{{ trans('common.search') }}</button>
					{{ Form::close() }}
				</div>
				<div class="col-md-5">
					<ul class="list-inline margin-top-10 margin-bottom-5">
						<!-- BEGIN: TAB MENU -->
						<li class="dropdown">
							{{trans('common.sort_by')}}:
							<?php
								$orderby_field = 'id';
								if (Input::has('orderby_field'))
									$orderby_field = Input::get('orderby_field');

								$sort_by_arr = $service_obj->populateOptionsArray();
								$first_val = $sort_by_arr[0]['innertext'];
								foreach($sort_by_arr as $sortKey => $sort) {
									if($orderby_field == $sort['innervalue'])
										$first_val = $sort['innertext'];
								}

							?>
							<a href="#" data-toggle="dropdown">{{ $first_val }} <i class="fa fa-caret-down"></i></a>
							<ul class="dropdown-menu dropdown-menu-right">
								@foreach($sort_by_arr as $sortKey => $sort)
										<li @if($orderby_field == $sort['innervalue']) class="active" @endif><a href="{{ $sort['href'] }}">{{ $sort['innertext'] }}</a></li>
								@endforeach
							</ul>
						</li>
						<!-- END: TAB MENU -->
						<li><a href="{{ BasicCUtil::getCurrentUrl(true, 'view_type=grid'); }}" title="{{trans('grid_view')}}"><i class="fa fa-th-large"></i></a></li>
						<li><a href="{{ BasicCUtil::getCurrentUrl(true, 'view_type=list'); }}" title="{{trans('list_view')}}"><i class="fa fa-list"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endif

@if(count($product_details) > 0 || !$is_search_done)
	{{ Form::open(array('url' => URL::action('ViewShopController@getIndex'), 'id'=>'productFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
		@if($view_type == 'list')
			<div class="product-list">
				<!-- BEGIN: LIST VIEW -->
				<ul class="list-unstyled">
					@foreach($product_details as $productKey => $product)
						<?php
							$p_img_arr = $product_obj->getProductImage($product['id']);
							$p_thumb_img = $service_obj->getProductDefaultThumbImage($product['id'], 'thumb', $p_img_arr);
							$price = $service_obj->formatProductPriceNew($product);
							$view_url = $service_obj->getProductViewURLNew($product['id'], $product);
						?>
						<li>
							<!-- BEGIN: VIEW SHOP PRODUCTS -->
							<div class="shoplist-product">
								{{ CUtil::showFeaturedProductIcon($product['id'], $product); }}
								<ul id="fav_block_{{ $product['id'] }}" class="list-unstyled favset-icon">
									<?php
										$is_favorite_prod = $prod_fav_service->isFavoriteProduct($product['id'], $logged_user_id);
										$login_url = URL::to('users/login?form_type=selLogin');
									?>
                                    <li>
                                        @if(CUtil::isMember())
					                        @if($is_favorite_prod)
					                    		<a href="javascript:void(0);" data-productid="{{$product['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-pink"></i></a>
					                    	@else
					                    		<a href="javascript:void(0);" data-productid="{{$product['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-muted"></i></a>
					                    	@endif
					                    @else
					    					<a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart"></i></a>
					                    @endif
				                    </li>
                                    <li class="dropdown">
                                    	@if(CUtil::isMember())
											<a href="javascript:void(0);" id="fav_list_{{$product['id']}}" data-productid="{{$product['id']}}" data-toggle="dropdown" class="js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
											<div id="fav_list_holder_{{$product['id']}}" class="dropdown-menu custom-dropdown fnFavListHolder">
											<div>
										@else
					    					<a href="{{ $login_url }}" class="fn_signuppop"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
					                    @endif
                                    </li>
                                </ul>
								<div class="row">
									<div class="col-md-2 col-sm-2 col-xs-2">
										{{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
										<figure class="text-center">
											<a href="{{ $view_url }}" class="imgsize-95X95"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $product['product_name']  }}}" alt="{{{ $product['product_name']  }}}" /></a>
										</figure>
									</div>

									<div class="col-md-4 col-sm-4 col-xs-4 wid-">
										<p class="margin-bottom-5"><a href="{{ $view_url }}" title="{{{ $product['product_name']  }}}">{{{ $product['product_name'] }}}</a></p>
										<p><a href='javascript:void(0);' class="bs-title fonts13 text-muted">{{{ $shop_details['shop_name'] }}}</a></p>
									</div>

									<div class="col-md-3 fonts13 col-sm-3 col-xs-3">
										<p class="margin-bottom-5">
											@if($product['date_activated'] == '0') {{ trans('viewProduct.not_available') }}  @else {{ date('M d, Y', $product['date_activated']) }} @endif
										</p>
										<p>{{ $product['total_views'] }} <span class="text-muted">{{ trans('common.views') }}</span></p>
									</div>

									<div class="col-md-3 col-sm-3 col-xs-3">
										@if($product['is_free_product'] == 'Yes')
											<p class="no-padding text-right"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></p>
										@else
											@if($price['disp_price'] && $price['disp_discount'])
												<p class="text-success text-right">
													{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}
												</p>
											@elseif($price['disp_price'])
												@if($price['product']['price'] > 0)
													<p class="text-success text-right">
														{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}
													</p>
												@else
													<p class="no-padding"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></p>
												@endif
											@endif
										@endif
									</div>
								</div>
							</div>
							<!-- END: VIEW SHOP PRODUCTS -->
						</li>
					@endforeach
				</ul>
				<!-- END: LIST VIEW -->
			</div>
		@else
			<!-- BEGIN: GRID VIEW -->
			<div class="shop-prdctsearch">
				<ul class="row list-unstyled">
					@foreach($product_details as $productKey => $product)
						<?php
							$p_img_arr = $product_obj->getProductImage($product['id']);
							$p_thumb_img = $service_obj->getProductDefaultThumbImage($product['id'], 'thumb', $p_img_arr);
							$price = $service_obj->formatProductPriceNew($product);
							$view_url = $service_obj->getProductViewURLNew($product['id'], $product);
						?>
						<li class="col-md-4 col-sm-4 col-xs-4">
							<div class="bs-prodet product-gridview">
								{{ CUtil::showFeaturedProductIcon($product['id'], $product); }}
								<ul id="fav_block_{{ $product['id'] }}" class="list-unstyled favset-icon">
									<?php
										$is_favorite_prod = $prod_fav_service->isFavoriteProduct($product['id'], $logged_user_id);
										$login_url = URL::to('users/login?form_type=selLogin');
									?>
                                    <li>
                                        @if(CUtil::isMember())
					                        @if($is_favorite_prod)
					                    		<a href="javascript:void(0);" data-productid="{{$product['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-pink"></i></a>
					                    	@else
					                    		<a href="javascript:void(0);" data-productid="{{$product['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-muted"></i></a>
					                    	@endif
					                    @else
					    					<a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart"></i></a>
					                    @endif
				                    </li>
                                    <li class="dropdown">
                                    	@if(CUtil::isMember())
											<a href="javascript:void(0);" id="fav_list_{{$product['id']}}" data-productid="{{$product['id']}}" data-toggle="dropdown" class="js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
											<div id="fav_list_holder_{{$product['id']}}" class="dropdown-menu custom-dropdown fnFavListHolder">
											<div>
										@else
					    					<a href="{{ $login_url }}" class="fn_signuppop"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
					                    @endif
                                    </li>
                                </ul>

								<!-- BEGIN: VIEW SHOP PRODUCTS -->
								<p class="prodet-img">
									{{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
									<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $product['product_name']  }}}" alt="{{{ $product['product_name']  }}}" /></a>
								</p>
								<div class="prodet-info">
									<h3><a href="{{ $view_url }}" title="{{{ $product['product_name']  }}}">{{{ $product['product_name'] }}}</a></h3>
									@if($product['is_free_product'] == 'Yes')
										<span class="pull-right">
											<strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong>
										</span>
									@else
										@if($price['disp_price'] && $price['disp_discount'])
											<p class="pull-right">
												{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}
											</p>
										@elseif($price['disp_price'])
											@if($price['product']['price'] > 0)
												<p class="pull-right">
													{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}
												</p>
											@else
												<span class="pull-right"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></span>
											@endif
										@endif
									@endif
									<a href='javascript:void(0);' class="bs-title">{{{ $shop_details['shop_name'] }}}</a>
								</div>
								<!-- END: VIEW SHOP PRODUCTS -->
							</div>
						</li>
					@endforeach
				</ul>
			</div>
			<!-- END: GRID VIEW -->
		@endif

		@if(count($product_details) > 0)
			<div class="text-right">
				{{ $product_details->appends(array('search_product_name' => Input::get('search_product_name'), 'orderby_field' => Input::get('orderby_field'), 'view_type' => Input::get('view_type')))->links() }}
			</div>
		@endif
	{{ Form::close() }}
@else
	<!-- BEGIN:ALERT BLOCK -->
	<p class="alert alert-info">{{ trans("shop.product_not_found") }}</p>
    <!-- END:ALERT BLOCK -->
@endif

