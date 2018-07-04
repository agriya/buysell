@extends('base')
@section('content')
	<!-- BEGIN: DEALS SUBMENU -->
	@include('deals::deal_submenu')
	<!-- END: DEALS SUBMENU -->

	<!-- BEGIN: DEAL DETAILS -->
	@if(isset($deal_details) && COUNT($deal_details) > 0)
		<?php
			$d_img_arr['deal_id'] = $deal_details->deal_id;
			$d_img_arr['deal_title'] = $deal_details->deal_title;
			$d_img_arr['img_name'] = $deal_details->img_name;
			$d_img_arr['img_ext'] = $deal_details->img_ext;
			$d_img_arr['img_width'] = $deal_details->img_width;
			$d_img_arr['img_height'] = $deal_details->img_height;
			$d_img_arr['l_width'] = $deal_details->l_width;
			$d_img_arr['l_height'] = $deal_details->l_height;
			$d_img_arr['t_width'] = $deal_details->t_width;
			$d_img_arr['t_height'] = $deal_details->t_height;
			$p_thumb_img = $deal_serviceobj->getDealDefaultThumbImage($deal_details->deal_id, 'thumb', $d_img_arr);
			$view_url = $deal_serviceobj->getDealViewUrl($deal_details);
			$shopDetails =	BasicCUtil::getShopDetails($deal_details->user_id);
			$expiry_details = $deal_serviceobj->dealExpiryDetails($deal_details->date_deal_from, $deal_details->date_deal_to);
		?>
		<div class="well">
			<div class="row">
				<div class="prodet-img col-md-3 col-sm-2">
					<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a>
				</div>
				<div class="col-md-6 col-sm-5">
					<p class="fonts18">{{ $deal_details->deal_title }}</p>
					<p class="min-hgt75">{{ $deal_details->deal_short_description }}</p>
					<ul class="list-inline">
						<li class="margin-right-20 margin-bottom-10">
							<em>{{ $expiry_details['label'] }}: <strong class="text-muted">{{ CUtil::FMTDate($deal_details->date_deal_to, 'Y-m-d', '') }}</strong></em>
						</li>
						@if(isset($shopDetails) && COUNT($shopDetails) > 0)
							<li>
								<em class="text-muted">{{ Lang::get('deals::deals.shop_head') }}:</em>
								<a href="{{ $shopDetails->shop_url }}" title="{{{ $shopDetails->shop_name  }}}"><strong>{{{ $shopDetails->shop_name  }}}</strong></a>
							</li>
						@endif
					</ul>
				</div>
				<div class="col-md-3 col-sm-3 text-center">
					<p><em>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}</em></p>
					<div class="deals-discount" title="{{ $deal_details->discount_percentage ."%"}}">{{ $deal_details->discount_percentage ."%"}}</div>
					<a href="{{ $view_url }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" class="btn green">
						<i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}
					</a>
				</div>
			</div>
		</div>
	@endif
	<!-- END: DEAL DETAILS -->

	<!-- BEGIN: PRODUCTS LISTS -->
	@if(count($product_details) > 0)
		<?php $shop_details = array(); ?>
		<div id="js-product-view">
			<ul class="row list-unstyled prolist-max768">
				@foreach($product_details as $productKey => $product)
					<li class="col-md-3 col-sm-4 feat-prod">
						<div class="bs-prodet product-gridview">
							<?php
								$seller_id = $product['product_user_id'];
								if (!isset($shop_details_array[$seller_id]))
								$shop_details_array[$seller_id] = $shop_obj->getShopDetails($seller_id);
								$shop_details = $shop_details_array[$seller_id];
								$seller_name = "";
								$products = Products::initialize();
								$p_img_arr = $products->getProductImage($product['id']);
								$p_thumb_img = $list_prod_serviceobj->getProductDefaultThumbImage($product['id'], 'thumb', $p_img_arr);
								$price = $list_prod_serviceobj->formatProductPriceNew($product);
								$view_url = $list_prod_serviceobj->getProductViewURL($product['id'], $product);
							?>
							<p class="prodet-img"><a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a></p>
							<div class="prodet-info dealitem-price">
								<h3><a href="{{$view_url}}" title="{{{ $product['product_name']  }}}">{{{ $product['product_name'] }}}</a></h3>
								@if($product['is_free_product'] == 'Yes')
									<span class="pull-right">
										<strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong>
									</span>
								@else
								@if($price['disp_price'] && $price['disp_discount'])
									<p>
										<span>{{ Lang::get("deals::deals.price_lbl") }}:</span>
										<span>{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span>
									</p>
									@if(isset($expiry_details['expired']) && !$expiry_details['expired'])
										<?php
											$item_deal_price = $price['product']['discount'] - ( $price['product']['discount'] * ($deal_details->discount_percentage / 100 ));
											$item_deal_price_usd = $price['product']['discount_usd'] - ( $price['product']['discount_usd'] * ($deal_details->discount_percentage / 100 ));
										?>
										<p>
											<span>{{ Lang::get("deals::deals.deal_price_lbl") }}:</span>
											<span>{{ CUtil::convertAmountToCurrency($item_deal_price, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
										</p>
									@endif
									@elseif($price['disp_price'])
										@if($price['product']['price'] > 0)
											<p>
												<span>{{ Lang::get("deals::deals.price_lbl") }}:</span>
												<span>{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span>
											</p>
											@if(isset($expiry_details['expired']) && !$expiry_details['expired'])
												<?php
													$item_deal_price = $price['product']['price'] - ( $price['product']['price'] * ($deal_details->discount_percentage / 100 ));
													$item_deal_price_usd = $price['product']['discount_usd'] - ( $price['product']['discount_usd'] * ($deal_details->discount_percentage / 100 ));
												?>
												<p>
													<span>{{ Lang::get("deals::deals.deal_price_lbl") }}:</span>
													<span>{{ CUtil::convertAmountToCurrency($item_deal_price, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
												</p>
											@endif
										@else
											<span class="pull-left"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></span>
										@endif
									@endif
								@endif
							</div>
						</div>
					</li>
				@endforeach
			</ul>
		</div>
	@else
		<p class="alert alert-info">{{ Lang::get('product.product_not_found') }}</p>
	@endif
	<!-- END: PRODUCTS LISTS -->
	@if(count($product_details) > 0)
		<div class="text-right">{{ $product_details->links() }}</div>
	@endif
@stop