<aside class="col-md-3">
    @if($d_arr['error_msg'] == '')
        <?php
            $user_details = CUtil::getUserDetails($p_details['product_user_id']);
        ?>
        <div class="mb20">
        {{-- PRICE DETAILS STARTS --}}
            @if($p_details['is_free_product'] == 'No')
				<?php
                    $price_group = $service_obj->getPriceGroupsDetailsNew($p_details['id'], $logged_user_id);
                    $product_price = $product_price_arr = '';
                    $discount_price = 0;
                    $have_discount = false;
                    if(count($price_group) > 0 && isset($price_group['price']) && $price_group['price']!='') {
                        $purchase_price = CUtil::convertAmountToCurrency($p_details['purchase_price'], Config::get('generalConfig.site_default_currency'), '', true);
                        $product_price = CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true);
                        $product_price_arr = CUtil::getCurrencyBasedAmount($price_group['price'], $price_group['price_usd'], $price_group['currency'], true);
                        /*
                        $curr_time = strtotime(date('Y-m-d'));
                        if($p_details['product_discount_todate'] != '0000-00-00' || $p_details['product_discount_fromdate'] != '0000-00-00')
                        {
                            $discount_from_date = strtotime($p_details['product_discount_fromdate']);
                            $discount_end_date = strtotime($p_details['product_discount_todate']);
                            if($discount_end_date >= $curr_time && $discount_from_date <= $curr_time)
                            {
                                $have_discount = true;
                            }
                        }
                        else if($p_details['product_discount_fromdate'] != '0000-00-00')
                        {
                            $discount_from_time = strtotime($p_details['product_discount_fromdate']);
                            if($discount_from_time <= $curr_time)
                            {
                                $have_discount = true;
                            }
                        }
                        else if($p_details['product_discount_todate'] != '0000-00-00')
                        {
                            $discount_end_time = strtotime($p_details['product_discount_todate']);
                            if($discount_end_time >= $curr_time)
                            {
                                $have_discount = true;
                            }
                        }
                        else
                        {
                            if($p_details['product_discount_price'] > 0)
                            {
                                $have_discount = true;
                            }
                        }*/

                        if($price_group['discount'] > 0 && $price_group['price'] > $price_group['discount']) {
                            $have_discount = true;
                        }

                        if($have_discount) {
                            $discount_price = CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true);
                            $discount_price_arr = CUtil::getCurrencyBasedAmount($price_group['discount'], $price_group['discount_usd'], $price_group['currency'], true);
                        }
                    }
                ?>
            @endif
            <?php $enable_serivices = true; ?>
            @include('admin.displayProductPrice')
        {{-- PRICE DETAILS END --}}
        </div>

        {{-- BEGIN: ITEM DETAILS --}}
		<div class="well well-new">
            <h2>{{ trans('viewProduct.item_details') }}:</h2>
            <div class="dl-horizontal dl-horizontal-new">
                @if(Config::get('generalConfig.user_allow_to_add_product'))
                    {{-- BEGIN: SHOP DETAILS --}}
                    @if(count($d_arr['shop_details']) > 0)
                        <?php
                            $shop_url = '#';//$service_obj->getProductShopURL($d_arr['shop_details']['id'], $d_arr['shop_details']);
                        ?>
                        <dl>
                            <dt>{{ trans('viewProduct.shop_details') }}</dt>
                            <dd><span><a href="{{ $shop_url }}" title="{{{ $d_arr['shop_details']['shop_name'] }}}"class="light-link text-ellipsyfy">{{{ $d_arr['shop_details']['shop_name'] }}}</a>{{-- <i class="fa fa-heart"></i> --}}</span></dd>
                        </dl>
                    @endif
                    {{-- END: SHOP DETAILS --}}
                @endif
                <dl>
                    <dt>{{ trans('viewProduct.listed_on') }}</dt>
                    <dd><span>@if($p_details['date_activated'] == '0') {{ trans('viewProduct.not_available') }}  @else {{ date('M d, Y', $p_details['date_activated'])  }} @endif</span></dd>
                </dl>
                <dl>
                    <dt>{{ trans('viewProduct.listing_id') }} </dt>
                    <dd><span>#{{ $p_details['product_code'] }}</span></dd>
                </dl>
                <dl>
                    <dt>{{ Lang::choice('viewProduct.view_choice', $p_details['total_views']) }}</dt>
                    <dd><span>{{ $p_details['total_views'] }}</span></dd>
                </dl>
            </div>
        </div>
        {{-- END: ITEM DETAILS --}}

	    <!-- BEGIN: STOCK DETAILS -->
	    <?php
			$stock_details =  $service_obj->getProductStocks($p_details['id']);
		?>
		@if($stock_details)
			<div class="well well-new">
		    	<h2>{{ trans('viewProduct.stock_details') }}:</h2>
		    	<div class="dl-horizontal dl-horizontal-new">
					@if(isset($stock_details['stock_country_id_china']))
						<dl><dt>China</dt> <dd><span>{{ $stock_details['quantity_china'] }}</span></dd></dl>
					@endif
					@if(isset($stock_details['stock_country_id_pak']))
						<dl><dt>Pakistan</dt> <dd><span>{{ $stock_details['quantity_pak'] }}</span></dd></dl>
					@endif
				</div>
			</div>
		@endif
		<!-- END: STOCK DETAIL S -->

		<!-- BEGIN: SHIPPING DETAILS -->
	    <?php
			$shipping_template_name = CUtil::getSippingTemplateName($p_details['shipping_template']);
		?>
		<div class="well well-new">
	    	<h2>{{ trans('admin/productList.product_shipping_template') }}:</h2>
	    	<div>
                @if(count($shipping_template_name) > 0)
                    <p><strong>{{ $shipping_template_name }}</strong></p>
                @else
                    <p class="alert alert-danger mar0">{{ trans('admin/productList.template_not_select') }}</p>
                @endif
			</div>
		</div>
		<!-- END: SHIPPING DETAILS -->

		<!--
        @if(count($d_arr['shop_item_details']) > 0)
            <?php
                $total_shop_items = $prod_obj->getTotalProduct($p_details['product_user_id']);
            ?>
            <div class="well well-new">
                <h2>{{ trans('viewProduct.more_from_this_shop') }}</h2>
                <ul class="list-inline mt10 outer-img clearfix">
                    @foreach($d_arr['shop_item_details'] AS $prd)
                        <?php
                            $p_img_arr = $prod_obj->getProductImage($prd->id);
                            $p_thumb_img = $service_obj->getProductDefaultThumbImage($prd->id, 'thumb', $p_img_arr);
                            $view_url = $service_obj->getProductViewURLNew($prd->id, $prd);
                        ?>
                        <li>
                            <a href="{{ $view_url }}" class="img81x61"><img src="{{ $p_thumb_img['image_url'] }}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{ $p_thumb_img["thumbnail_width"] }}' height='{{ $p_thumb_img["thumbnail_height"] }}' @endif title="{{{ nl2br($prd->product_name)  }}}" alt="{{{ nl2br($prd->product_name)  }}}" /></a>
                    </li>
                    @endforeach
                    <li><a class="img81x61 imgalt81x61" href='{{ $shop_url }}'><strong>{{ $total_shop_items}}<span class="text-muted">{{Lang::choice('viewProduct.product_choice', $total_shop_items) }}</span></strong></a></li>
                </ul>
            </div>
        @endif
        -->

         <!-- BEGIN: ATTRIBUTE DETAILS -->
            @if(count($d_arr['product_attr']) > 0)
            	<div class="well well-new">
                    <h2>{{ trans('viewProduct.attributes_of_the_product') }}:</h2>
                    @foreach($d_arr['product_attr'] as $attr_det)
                    	<div class="mb10">
                            <div class="text-muted fonts14">{{$attr_det['attribute_label']}}:</div>
                            <div>{{$attr_det['attribute_value']}}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        <!-- END: ATTRIBUTE DETAIL -->

        {{-- BEGIN: PRICE DETAILS
        	<?php $enable_serivices = false; ?>
        	@include('admin.displayProductPrice')
        {{-- END: PRICE DETAILS --}}
    @endif
</aside>