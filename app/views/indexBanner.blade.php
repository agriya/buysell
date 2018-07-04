<!-- BEGIN: INDEX BANNER -->
    <?php
		$banner_details = HomeCUtil::getIndexBannerImage();
		$featured_seller_res = HomeCUtil::getIndexFeaturedSellers();
	?>
	<div class="index-banner">
        <figure>
			<img src="{{ $banner_details['image_src'] }}" {{ $banner_details['image_attr'] }} title="{{{ nl2br($banner_details['image_title'])  }}}" alt="{{{nl2br($banner_details['image_title'])}}}" />
		</figure>
		<div class="banner-info">
			@if(isset($featured_seller_res['seller_details']) && isset($featured_seller_res['seller_image']) && isset($featured_seller_res['seller_shop_details']))
				<?php
                    $seller_name_shop = $featured_seller_res['seller_details']['display_name'].' of '.$featured_seller_res['seller_shop_details']['shop_name'];
                    $seller_location = CUtil::getSellerCityCountry($featured_seller_res['seller_shop_details']);
                ?>
                <div class="container">
                    <div class="banner-item">
                        <div class="media">
                            <a href="{{ $featured_seller_res['seller_shop_details']['shop_url'] }}" class="pull-left imgusersm-54X54">
                                <img src="{{ $featured_seller_res['seller_image']['image_url'] }}" {{ $featured_seller_res['seller_image']['image_attr'] }} alt="{{ $seller_name_shop }}" />
                            </a>
                            <div class="media-body">
                                <p>{{ $featured_seller_res['seller_details']['display_name'] }} of <a href="{{ $featured_seller_res['seller_shop_details']['shop_url'] }}">{{ $featured_seller_res['seller_shop_details']['shop_name'] }}</a></p>
                                <p>{{ implode(', ', $seller_location) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
			<div class="shop-det">
                <div class="container">
                    <div class="item-list">
                        <ul class="list-inline clearfix">
                        	@if(isset($featured_seller_res['seller_products']) && count($featured_seller_res['seller_products']) > 0)
                        		@foreach($featured_seller_res['seller_products'] as $key => $val)
                        			<li>
										<a href="{{ $val['product_url'] }}">
											<img src="{{ $val['p_thumb_img']['image_url'] }}" title="{{{ nl2br($val['product_name'])  }}}" alt="{{{nl2br($val['product_name'])}}}" />
										</a>
									</li>
	                            @endforeach
	                            @if($featured_seller_res['seller_products_total'] > 4)
	                            	<li><a href="{{ $featured_seller_res['seller_shop_details']['shop_url'] }}" class="tot-prdct"><span><strong>{{ $featured_seller_res['seller_products_total'] }}</strong> {{ Lang::choice('common.item_choice', $featured_seller_res['seller_products_total']) }}</span></a></li>
	                        	@endif
	                        @endif
                        </ul>
                    </div>
                    <h1>{{trans('home.shop_directly_from_people_around_world')}}</h1>
                </div>
            </div>
        </div>
    </div>
<!-- END: INDEX BANNER -->