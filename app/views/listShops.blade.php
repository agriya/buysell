@extends('base')
@section('content')
    <h1>{{ trans('shop.listShops.shoplist_title') }}</h1>
    <div class="row">
        <!-- BEGIN: SHOP ASIDE BLOCK -->
        <div class="col-md-3 blog-sidebar sidebar-alt">
            {{ Form::open(array('url' => Request::url(), 'id' => 'searchShopsfrm', 'class' => 'well', 'name' => 'searchShopsfrm', 'method' => 'get')) }}
                <h4>{{ trans('common.filter_your_search')}}</h4>
                <div class="well-new margin-top-10">
                    <div class="margin-bottom-15">
                        {{ Form::label('shop_name',Lang::get('shopDetails.shop_name'),array('class' => 'control-label')) }}
                        {{ Form::text('shop_name', Input::get('shop_name'),array('class' => 'form-control')) }}
                    </div>
                    <div class="sidebar-btn">
                        <button type="submit" class="btn purple-plum"><i class="fa fa-search"></i> {{ trans('common.search')}}</button>
                        <a href="{{ Request::url() }}" class="btn default"><i class="fa fa-undo"></i> {{ trans('common.reset') }}</a>
                    </div>
                </div>
             {{ Form::close() }}

             <!-- BEGIN: SIDE BANNER GOOGLE ADDS -->
             {{ getAdvertisement('side-banner') }}
             <!-- END: SIDE BANNER GOOGLE ADDS -->

        </div>
        <!-- END: SHOP ASIDE BLOCK -->


		<div class="col-md-9 mainbar">
            @if(CUtil::chkIsAllowedModule('featuredsellers'))
            <!-- BEGIN: TAB MENU -->
            <div class="btn-group margin-bottom-20 recently-btn pos-relative margin-top-10">
                <a href="{{ Url::to('shop?list_type=recently_added') }}" class="btn {{ ($list_type == 'recently_added') ? 'active' : '' }}">{{ trans('product.recently_added') }}</a>
                <a href="{{ Url::to('shop?list_type=featured') }}" class="btn {{ ($list_type == 'featured') ? 'active' : '' }}">{{ trans('product.product_listing_featured') }}</a>
            </div>
            <!-- END: TAB MENU -->
            @endif
            <!-- BEGIN: LISTING BLOCK -->
            <div class="well">
                {{ Form::open(array('id'=>'shopsListfrm', 'method'=>'get','class' => '' )) }}
                    @if(count($shops_list))
                        <ul class="list-unstyled shop-list">
                            @foreach($shops_list as $shop)
                                <?php
                                    $shop_details['url_slug'] = $shop['url_slug'];
                                    $prod_obj->setProductPagination(2);
                                    $prod_obj->setFilterProductStatus('Ok');
                                    $prod_obj->setFilterProductExpiry(true);
                                    $shop_items = $prod_obj->getProductsList($shop['user_id']);
                                    $total_products = $prod_obj->getTotalProducts($shop['user_id']);
                                    $shop_url = $service_obj->getProductShopURL($shop['id'], $shop_details);
                                    $user_details = CUtil::getUserDetails($shop['user_id']);//, 'all', $shop_user_details
                                ?>
                                <li>
                                    <div class="row">
                                        <div class="col-md-7">
                                            @if(isset($shop['shop_name']) && $shop['shop_name'] !='')
                                                <p class="fonts16 clearfix"><a href="{{ URL::to('shop/'.$shop['url_slug']) }}" title="{{{ $shop['shop_name'] }}}"><i class="fa fa-shopping-cart"></i> <strong class="text-ellipsis">{{{ $shop['shop_name'] }}}</strong>{{ CUtil::showFeaturedSellersIcon($shop['user_id'], $shop) }}</a></p>
                                            @endif

                                            <div class="margin-left-25">
                                                @if($shop['shop_city'] != '' && $shop['shop_state'] != '' && $shop['shop_country'] != '')
                                                    <p>{{{ $shop['shop_city'] }}}, {{{ $shop['shop_state'] }}}, {{{ $country_arr[$shop['shop_country']] }}}</p>
                                                @elseif($shop['shop_state'] != '' && $shop['shop_country'] != '')
                                                    <p>{{{ $shop['shop_state'] }}}, {{{ $shop['shop_country'] }}}</p>
                                                @elseif($shop['shop_country'] != '')
                                                    <p>{{{ $country_arr[$shop['shop_country']] }}}</p>
                                                @endif
                                                <p class="no-margin">{{ trans('shop.listShops.shop_owner') }}: <a href="{{$user_details['profile_url']}}" title="{{ $user_details['display_name'] }}"><strong class="text-success">{{ $user_details['display_name'] }}</strong></a></p>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <ul class="list-inline text-right featured-icon">
                                                @foreach($shop_items AS $prd)
                                                    <?php
                                                        $products = Products::initialize();
                                                        $p_img_arr = $products->getProductImage($prd['id']);
                                                        $p_thumb_img = $service_obj->getProductDefaultThumbImage($prd['id'], 'small', $p_img_arr);
                                                        //$price = $service_obj->formatProductPrice($product);
                                                        //$price = $service_obj->formatProductPriceNew($product);
                                                        $view_url = $service_obj->getProductViewURL($prd['id'], $prd);
                                                    ?>
                                                    <li>
                                                    	{{ CUtil::showFeaturedProductIcon($prd['id'], $prd) }}
                                                        <a href="{{ $view_url }}" class="imgsize-75X75"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{{ $prd['product_name']  }}}" alt="{{{ $prd['product_name']  }}}" /></a>
                                                    </li>
                                                @endforeach

                                                <?php
    //                                                $users_shops_details = $shop_obj->getUsersShopDetails($shop['user_id']);
    //                                                $total_products = 0;
    //                                                if($users_shops_details) {
    //                                                    $total_products = $users_shops_details['total_products'];
    //                                                }
                                                ?>
                                                <li><a href="{{ URL::to('shop/'.$shop['url_slug']) }}" title="{{ $shop_url }}" class="imgsize-75X75 total-prod"><strong> {{ $total_products }} </strong><span class="text-muted">{{Lang::choice('common.item_choice', $total_products) }}</span></a></li>
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
                        {{ $shops_list->appends(array('shop_name' => Input::get('shop_name')))->links() }}
                    </div>
                 {{ Form::close() }}
            </div>
            <!-- END: LISTING BLOCK -->
        </div>
    </div>
@stop