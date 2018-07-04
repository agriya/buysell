<div class="container recfav-list">
    <h1 class="margin-bottom-20">{{ ucwords(trans('featuredproducts::featuredproducts.featured_products')) }}</h1>
    @if(count($featured_products) > 0)
        <?php $shop_details = array(); ?>
        <div id="js-product-view">
            <ul class="row list-unstyled prolist-max768">
                @foreach($featured_products as $productKey => $product)
                    <li class="col-md-3 col-sm-3 feat-prod">
                        <div class="bs-prodet product-gridview">
                            <?php
                                $seller_id = $product['product_user_id'];
                                if (!isset($shop_details_array[$seller_id]))
                                    $shop_details_array[$seller_id] = $shop_obj->getShopDetails($seller_id);
                                $shop_details = $shop_details_array[$seller_id];
                                $seller_name = "";

                                $products = Products::initialize();
                                $p_img_arr = $products->getProductImage($product['id']);
                                $p_thumb_img = $product_service->getProductDefaultThumbImage($product['id'], 'thumb', $p_img_arr);
                                $price = $product_service->formatProductPriceNew($product, true);
                                $view_url = $product_service->getProductViewURL($product['id'], $product);
                            ?>
                            <p class="prodet-img"><a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a></p>
                            <div class="prodet-info">
                                <h3><a href="{{$view_url}}" title="{{{ $product['product_name']  }}}">{{{ $product['product_name'] }}}</a></h3>
                                @if($product['is_free_product'] == 'Yes')
                                    <span class="pull-right">
                                        <strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong>
                                    </span>
                                @else
                                    @if($price['disp_price'] && $price['disp_discount'])
                                        <p class="pull-right">{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>

                                    @elseif($price['disp_price'])
                                        @if($price['product']['price'] > 0)
                                            <p class="pull-right">{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                        @else
                                            <span class="pull-right"><strong class="label label-primary label-free no-margin">{{ Lang::get('common.free') }}</strong></span>
                                        @endif
                                    @endif
                                @endif
                                <a href='{{$shop_details['shop_url']}}' class="bs-title">{{ $shop_details['shop_name'] }}</a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="alert alert-info">{{ Lang::get('featuredproducts::featuredproducts.featured_product_not_found') }}</p>
    @endif
</div>