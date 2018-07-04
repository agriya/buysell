@if($cart_item_details[$cart['item_owner_id']]['product']['free_item'])
    <div class="blog-item">
       <div class="cart-list-title">
            <div class="row">
                <div class="col-md-3 portfolio-info"><strong>{{ trans('showCart.item') }}</strong></div>
                <!--<div class="portfolio-info col-md-4"><strong>{{ trans('showCart.product_price') }}</strong></div>-->
                <div class="portfolio-info col-md-1"><strong>{{ trans('showCart.quantity') }}</strong></div>
                <div class="portfolio-info col-md-1 new-col-1"><strong>{{ trans('showCart.product_price') }}</strong></div>
                <div class="portfolio-info col-md-2 new-col-2"><strong>{{ trans('showCart.tax_details') }}</strong></div>
                <div class="portfolio-info col-md-2"><strong>{{ trans('showCart.shipping_details') }}</strong></div>
                <div class="portfolio-info col-md-2"><strong>Sub total</strong></div>
                <div class="portfolio-info col-md-1 new-col-3"><strong>{{ trans('common.action') }}</strong></div>
            </div>
        </div>
       <div class="portfolio-block">
            @foreach($cart_item_details[$cart['item_owner_id']]['product']['free_item'] as $item)
                <?php
					$p_img_arr = $prod_obj->getProductImage($item['id']);
					/*echo '<pre>';
					print_r($item);die;*/
					$p_thumb_img = $ProductService->getProductDefaultThumbImage($item['id'], 'thumb', $p_img_arr);
					$view_url = $ProductService->getProductViewURL($item['id']);

					$item_id = $item['id'];
					$qty = $item['qty'];
					$price_with_qty = $item['product_price'] * $qty;
					$new_sub_total = $product_tot_tax_amount = $total_shipping_fee = 0;
				?>
                <div class="portfolio-block">
                    <div class="row">
                        <div class="portfolio-info col-md-3">
                            <div class="portfolio-text">
                                <a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{{ $item['product_name']  }}}" alt="{{{ $item['product_name']  }}}"  class="media-object"/></a>
                                <div class="portfolio-text-info">
                                    <h4><a href="{{ $view_url }}">{{{ $item['product_name'] }}}</a></h4>
                                    <p>{{ trans('showCart.product_code') }}: <span class="text-muted">{{ $item['product_code'] }}</span></p>
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-4 col-sm-4 col-xs-4 portfolio-info">{{ CUtil::getBaseAmountToDisplay($item['product_price'], $item['product_price_currency']) }}</div>-->
                        <?php

                        ?>
                        <div class="portfolio-info col-md-1">
                            <!--{{ Form::select('product_qty', $numbers_arr, $qty, array('id'=>"product_qty_".$cart['item_owner_id']."_".$item_id, 'class'=>'form-control input-small', 'onchange'=>'updateProductQuantity(this)')) }}-->
                            {{ Form::input('number', 'product_qty', $qty, array ('id'=>"product_qty_".$cart['item_owner_id']."_".$item_id, 'class' => 'form-control input-small fnQuantity', 'autocomplete' => 'off', 'maxlength' => '4', 'min' => '1', 'max' => '1000')); }}
                        </div>
                        <div class="portfolio-info col-md-1 new-col-1">
                            <strong class="badge badge-primary">{{ trans('common.free') }}</strong>
                        </div>
                        <?php
                            $this->show_cart_service = new ShowCartService();
                            $product_taxes = $this->show_cart_service->getTaxDetails($item['id'], $item['product_price'], $qty);
                            if($product_taxes['product_tot_tax_amount'] > 0) {
                                $product_tot_tax_amount = $product_taxes['product_tot_tax_amount'];
                            }
                        ?>
                        <div class="portfolio-info col-md-2 new-col-2">
                            @if($product_tot_tax_amount > 0)
                                <p><span id="product_tax_total_{{$item_id}}">{{ CUtil::convertAmountToCurrency($product_tot_tax_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span> <span><a href="javascript:void(0);" onclick="return showTaxDetails('{{$item_id}}')" title="{{ trans('checkOut.tax_details') }}" class="text-muted"><i class="fa fa-question-circle"></i></a></span></p>
                                <div id="tax_fee_details_{{$item_id}}" style="display:none">
                                    @if(!empty($product_taxes['product_tax_details']))
                                        @foreach($product_taxes['product_tax_details'] as $tax_det)
                                            <p class="margin-bottom-5">{{$tax_det['tax_label']}}</p>
                                        @endforeach
                                    @endif
                                </div>
                            @else
                                <strong class="badge badge-primary">{{ trans('common.free') }}</strong>
                            @endif
                        </div>
                        <div class="portfolio-info col-md-2 drop-position">
                            @if($item['shipping_template'] > 0)
                                <?php
                                    $shipping_companies_details = $this->show_cart_service->getShippingTemplateDetails($item['cart_id'], $item['shipping_template'], $item_id, $qty);
                                    $shipping_fee = $shipping_company_id_selected = 0;
                                    if(count($shipping_companies_details) > 0) {
                                        $shipping_fee = $shipping_companies_details['shipping_company_fee_selected'];
                                        $shipping_company_id_selected = $shipping_companies_details['shipping_company_id_selected'];
                                    }
                                    $total_shipping_fee = $shipping_fee;
                                ?>
                                {{Form::hidden('cart_id_'.$cart['item_owner_id'].'_'.$item_id, $item['cart_id'], array('id' => 'cart_id_'.$cart['item_owner_id'].'_'.$item_id))}}
                                {{Form::hidden('shipping_template_'.$cart['item_owner_id'].'_'.$item_id, $item['shipping_template'], array('id'=>'shipping_template_'.$cart['item_owner_id'].'_'.$item_id))}}
                                {{Form::hidden('shipping_company_id_selected_'.$cart['item_owner_id'].'_'.$item_id, $shipping_company_id_selected, array('id'=>'shipping_company_id_selected_'.$cart['item_owner_id'].'_'.$item_id))}}
                                @include('shippingCompanyPopupMin', array('shipping_companies_details' => $shipping_companies_details, 'shipping_template' => $item['shipping_template'], 'cart_id' => $item['cart_id'], 'item_id' => $item_id))
                            @else
                                <div class="alert alert-danger no-margin"><small>{{ trans('common.do_not_ship_to_country') }}</small></div>
                            @endif
                        </div>
                        <?php
                            if($product_tot_tax_amount > 0 || $total_shipping_fee > 0)
                                $currency_cart_items++;

                            $new_sub_total = $price_with_qty + $product_tot_tax_amount + $total_shipping_fee;
                            $new_checkout_total += $new_sub_total;
                        ?>
                        <div class="portfolio-info col-md-2">
                            <span id="new_product_subtotal_{{$item_id}}">
                                {{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), '', true) }}
                            </span>
                        </div>
                        <div class="portfolio-info col-md-1 new-col-3">
                            <div class="action-btn">
                                <a href="javascript:void(0);" onclick="return removeCartItem('{{$item['id']}}', 'Product')" title="Remove from cart" class="btn red btn-xs"><i class="fa fa-trash-o"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!--
		<div class="margin-bottom-20 clearfix">
            <div class="col-md-4 col-sm-4"></div>
            <div class="col-md-8 col-sm-8">
                <div class="col-md-5 col-sm-5"></div>
                <div class="col-md-5 col-sm-5 fonts18">
                	{{ trans('showCart.cart_sub_total') }}:
            		{{ CUtil::getBaseAmountToDisplay("0", "") }}
                </div>
                <div class="col-md-2 col-sm-2"></div>
            </div>
        </div>
        -->
    </div>
@endif

@if($cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item'])
    @if(count($cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item']) == 1)
        <div class="alert alert-danger">{{ trans('showCart.product_cannot_purchase') }}</div>
    @else
        <div class="alert alert-danger">{{ trans('showCart.products_cannot_purchase') }}</div>
    @endif
    <div class="blog-item">
    	<div class="row cart-list-title">
            <div class="portfolio-info col-md-3"><strong>{{ trans('showCart.product_details') }}</strong></div>
            <div class="portfolio-info col-md-8"><strong>{{ trans('showCart.product_price') }}</strong></div>
            <div class="portfolio-info col-md-1 new-col-3"><strong>{{ trans('common.action') }}</strong></div>
        </div>
        <div class="portfolio-block">
            @foreach($cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item'] as $item)
                <?php
                    $p_img_arr = $prod_obj->getProductImage($item['id']);
                    $p_thumb_img = $ProductService->getProductDefaultThumbImage($item['id'], 'thumb', $p_img_arr);
                    $view_url = $ProductService->getProductViewURL($item['id']);
                ?>
                <div class="row">
                    <div class="portfolio-info col-md-3">
                        <div class="portfolio-text">
                            <a href="{{ $view_url }}" class="pull-left"><img class="media-object" id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{{ $item['product_name']  }}}" alt="{{{ $item['product_name']  }}}" /></a>
                            <div class="portfolio-text-info">
                                <h4>{{{ $item['product_name'] }}}</h4>
                                <p>{{ trans('showCart.product_code') }}: <span class="text-muted">{{ $item['product_code'] }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio-info col-md-8">
                        <strong class="badge badge-primary">{{ trans('common.free') }}</strong>
                    </div>
                    <div class="portfolio-info col-md-1 new-col-3">
                    	<div class="action-btn">
                            <a class="btn btn-xs red" href="javascript:void(0);" onclick="return removeCartItem('{{$item['id']}}', 'Product')" title="{{ trans('common.remove') }}"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif