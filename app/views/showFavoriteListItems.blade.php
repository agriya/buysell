<!-- BEGIN: PAGE TITLE -->
<h2>{{ trans('myaccount/viewProfile.favorite_items') }}
	@if(count($d_arr['fav_items']) > 0)
		<a href="{{ $d_arr['fav_items_url'] }}"><small class="text-primary">{{ trans('common.see_more') }}</small></a>
	@endif
</h2>
<!-- END: PAGE TITLE -->

@if(!empty($d_arr['fav_items']))
	<!-- BEGIN: SHOW FAVORITE ITEM -->
	@foreach($d_arr['fav_items'] AS $key => $list)
		<div class="margin-bottom-20">
			<h3><a href="{{ $d_arr['fav_items_url'] }}">{{{ $list['list_name'] }}}</a></h3>
			<ul class="list-inline shp-lst clearfix">
				@if(count($list['list_fav_prod_ids']) > 0)
					@foreach($list['list_fav_prod_ids'] AS $prd)
						<?php
							$fav_products = Products::initialize();
							$fav_products->setProductId($prd);
							$fav_products->setIncludeDeleted(true);
							$fav_products->setIncludeBlockedUserProducts(true);
							//$fav_products->setFilterProductStatus('Ok');
							$product_details = $fav_products->getProductDetails();
							$valid_prod = false;
							if(count($product_details) > 0) {
								$valid_prod = true;
								$p_img_arr = $fav_products->getProductImage($prd);
								$p_thumb_img = $product_service->getProductDefaultThumbImage($prd, 'small', $p_img_arr);
								$view_url = $product_service->getProductViewURL($prd, $product_details);
							}
						?>
                        
						@if($valid_prod)
                            <li>
                                {{ CUtil::showFeaturedProductIcon($prd, $product_details) }}
                                <a href="{{ $view_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{{ $product_details['product_name']  }}}" alt="{{{ $product_details['product_name']  }}}" /></a>
                            </li>
						@endif
					@endforeach
                    
					<li><a href="{{ $d_arr['fav_items_url'] }}" title="{{ trans('myaccount/viewProfile.favorite_shop') }}" class="total-prod"><strong> {{ $list['list_fav_total'] }}</strong> <span class="text-muted">{{ Lang::choice('common.item_choice', $list['list_fav_total']) }}</span></a></li>
				@endif
			</ul>
		</div>
	@endforeach
    <!-- END: SHOW FAVORITE ITEM -->
@else
	<!-- BEGIN: INFO BLOCK -->
	<p class="note note-info no-margin">{{ trans('myaccount/viewProfile.shop_not_favorited_yet') }}</p>
    <!-- END: INFO BLOCK -->
@endif