<!-- BEGIN: LISTING FAVORITE SHOP -->
<div class="well">
    @if(count($list_whos_fav_shop))
        <ul class="list-unstyled shop-list featured-icon">
            @foreach($list_whos_fav_shop as $key => $shop)
                <?php
                    $user_details = CUtil::getUserDetails($shop['user_id']);
                    $user_image = CUtil::getUserPersonalImage($shop['user_id'], "small", false);
                    $user_fav_prod_url = CUtil::userFavoritesUrl(BasicCUtil::setUserCode($shop['user_id']));

                    $total_fav_products = $prod_fav_service->totalFavorites($shop['user_id']);
                    $fav_prod_ids = $prod_fav_service->favoriteProductIds($shop['user_id'], 2);
                ?>
                <li>
                	<div class="row">
                        <div class="col-md-7">
							<a href="{{ $user_details['profile_url'] }}" class="pull-left margin-right-10 imgusersm-60X60">
							<img src="{{ $user_image['image_url'] }}" alt="{{ $user_details['display_name'] }}" /></a>

							<a href="{{ $user_details['profile_url'] }}" title="{{{ $user_details['display_name'] }}}" class="pull-left margin-top-5">
								<strong class="text-ellipsis">{{{ $user_details['display_name'] }}}</strong>
							</a>
                        </div>
                        <div class="col-md-5">
                            <ul class="list-inline text-right">
                                @foreach($fav_prod_ids AS $prod_id)
                                    <?php
                                    $fav_prod_obj = Products::initialize();
                                    $fav_prod_obj->setProductId($prod_id);
                                    $fav_prod_obj->setIncludeDeleted(true);
									$fav_prod_obj->setIncludeBlockedUserProducts(true);
									$product_details = $fav_prod_obj->getProductDetails();

                                    $p_img_arr = $fav_prod_obj->getProductImage($prod_id);
                                    $p_thumb_img = $product_service->getProductDefaultThumbImage($prod_id, 'thumb', $p_img_arr);
                                    $view_url = $product_service->getProductViewURL($prod_id, $product_details);
                                    ?>
                                    <li><a href="{{ $view_url }}" class="imgsize-75X75"><img src="{{ $p_thumb_img['image_url'] }}" @if(isset($p_thumb_img["thumbnail_width"])) @endif title="{{{ $product_details['product_name']  }}}" alt="{{{ $product_details['product_name']  }}}" /></a>
                                    </li>
                                @endforeach
                                <li><a href="{{ $user_fav_prod_url }}" title="{{ $user_fav_prod_url }}" class="imgsize-75X75 total-prod"><strong> {{ $total_fav_products }}</strong> <span class="text-muted">{{Lang::choice('shop.favorite_choice', $total_fav_products) }}</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="note note-info margin-0">{{ trans('shop.the_shop_not_yet_fav') }}</div>
    @endif

    <div class="text-center">
        @if(count($list_whos_fav_shop) > 0)
            {{ $list_whos_fav_shop->appends(array('favorites' => Input::get('favorites')))->links() }}
        @endif
    </div>
</div>
<!-- END: LISTING FAVORITE SHOP -->