<div class="well clearfix">
    <!-- BEGIN: PAGE TITLE -->
	<h2>{{ trans('myaccount/viewProfile.favorite_shop') }}
    	@if(count($d_arr['fav_shop_ids']) > 0)
			<a href="{{ $d_arr['fav_shop_url'] }}"><small class="text-primary">{{ trans('common.see_more') }}</small></a>
		@endif
	</h2>
	<!-- END: PAGE TITLE -->
	
    @if(!empty($d_arr['fav_shop_ids']))
    	<!-- BEGIN: SHOW FAVORITE SHOP -->
    	@foreach($d_arr['fav_shop_ids'] AS $shop_id)
			<?php
				$shop_loop_obj = Products::initializeShops();
				$shop_loop_obj->setIncludeBlockedUserShop(true);
				$shop_loop_obj->setFilterShopId($shop_id);
				$shop_details = $shop_loop_obj->getShopDetailsWithFilter();
				$prod_obj = Products::initialize();
				$prod_obj->setProductsLimit(3);
				$prod_obj->setFilterProductStatus('Ok');
				$prod_obj->setFilterProductExpiry(true);
				if(count($shop_details) > 0)
				{
					$prod_obj->setFilterProductExpiry(true);
					$shop_items = $prod_obj->getProductsList($shop_details['user_id']);
					$total_products = $prod_obj->getTotalProducts($shop_details['user_id']);
				}
				else
				{
					$shop_items = array();
					$total_products = array();
				}
			?>
			@if(count($shop_details) > 0)
				<div class="margin-bottom-20">
					<h3><a href="{{ $shop_details['shop_url'] }}">{{{ $shop_details['shop_name'] }}}</a></h3>
					<ul class="list-inline shp-lst clearfix">
						@if(isset($shop_items) && count($shop_items) > 0)
							@foreach($shop_items AS $prd)
								<?php
									$products = Products::initialize();
									$p_img_arr = $products->getProductImage($prd['id']);
									$p_thumb_img = $product_service->getProductDefaultThumbImage($prd['id'], 'small', $p_img_arr);
									$view_url = $product_service->getProductViewURL($prd['id'], array());
								?>
								<li>
									{{ CUtil::showFeaturedProductIcon($prd['id'], array()) }}
									<a href="{{ $view_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{{ $prd['product_name']  }}}" alt="{{{ $prd['product_name']  }}}" />
								</a>
								</li>
							@endforeach
						@endif
                        
						<li><a href="{{ $shop_details['shop_url'] }}" title="{{ $shop_details['shop_name'] }}" class="total-prod">
						<strong> {{ $total_products }}</strong> <span class="text-muted">{{ Lang::choice('common.item_choice', $total_products) }}</span></a></li>
					</ul>
				</div>
			@endif
        @endforeach
        <!-- END: SHOW FAVORITE SHOP -->
    @else
    	<!-- BEGIN: INFO BLOCK -->
        <p class="note note-info no-margin">{{ trans('myaccount/viewProfile.shop_not_favorited_yet') }}</p>
        <!-- END: INFO BLOCK -->
    @endif
</div>
