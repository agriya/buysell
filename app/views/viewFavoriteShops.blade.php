<!-- BEGIN: LISTING BLOCK -->
<div class="well">
    @if(count($favorite_details))
        <ul class="list-unstyled shop-list">
            @foreach($favorite_details as $shop)
                <?php
                    $shop_details['url_slug'] = $shop['url_slug'];
                    $prod_obj->setProductPagination(2);
                    $prod_obj->setFilterProductStatus('Ok');
                    $prod_obj->setFilterProductExpiry(true);
                    $shop_items = $prod_obj->getProductsList($shop['user_id']);
                    $total_products = $prod_obj->getTotalProducts($shop['user_id']);
                    $shop_url = $productService->getProductShopURL($shop['id'], $shop_details);
                    $user_details = CUtil::getUserDetails($shop['user_id']);//, 'all', $shop_user_details
                ?>
                <li>
                	<div class="row">
                        <div class="col-md-7">
                            <p>
                                <a href="{{ URL::to('shop/'.$shop['url_slug']) }}" title="{{{ $shop['shop_name'] }}}">
                                <i class="fa fa-shopping-cart pull-left margin-right-5 text-primary"></i> <strong class="text-ellipsis">{{{ $shop['shop_name'] }}}</strong></a>
                            </p>
                            <div class="margin-left-22">
                                <p>{{ trans('shop.listShops.shop_owner') }}: <a href="{{$user_details['profile_url']}}" title="{{ $user_details['display_name'] }}">
                                <strong class="text-success">{{ $user_details['display_name'] }}</strong></a>{{ CUtil::showFeaturedSellersIcon($shop['user_id'], $shop) }}</p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <ul class="list-inline text-right">
                                @foreach($shop_items AS $prd)
                                    <?php
										$products = Products::initialize();
										$p_img_arr = $products->getProductImage($prd['id']);
										$p_thumb_img = $productService->getProductDefaultThumbImage($prd['id'], 'thumb', $p_img_arr);
										$view_url = $productService->getProductViewURL($prd['id'], $prd);
                                    ?>
                                    <li><a href="{{ $view_url }}" class="imgsize-75X75"><img src="{{ $p_thumb_img['image_url'] }}" @if(isset($p_thumb_img["thumbnail_width"])) @endif title="{{{ $prd['product_name']  }}}" alt="{{{ $prd['product_name']  }}}" /></a>
                                    </li>
                                @endforeach
                                <li><a href="{{ URL::to('shop/'.$shop['url_slug']) }}" class="imgsize-75X75 total-prod"><strong> {{ $total_products }}</strong> <span class="text-muted">{{Lang::choice('viewProduct.product_choice', $total_products) }}</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="note note-info margin-0">{{ trans('shop.listShops.no_shops_found') }}</div>
    @endif

    <div class="text-center">
        @if(count($favorite_details) > 0)
            {{ $favorite_details->appends(array('favorites' => Input::get('favorites')))->links() }}
        @endif
    </div>
</div>
<!-- END: LISTING BLOCK -->