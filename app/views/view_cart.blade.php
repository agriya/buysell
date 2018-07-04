@extends('base')
@section('content')
@include('user_breadcrumb')
    <div class="row">
		<!-- BEGIN: SIDE BAR -->
		<div class="col-md-2">
			<div class="well aside-colectnguid cartchkout-sidebar">
				<h2 class="title-two">{{ trans('showCart.steps') }}</h2>
				<ul class="list-unstyled">
					<li class="active"><p><i class="fa fa-chevron-right text-muted"></i> {{ trans('showCart.cart_page_title') }}</p></li>
					<li class="disabled"><p><i class="fa fa-chevron-right text-muted"></i> {{ trans('checkOut.checkout_title') }}</p></li>
				</ul>
			</div>
		</div>
		<!-- END: SIDE BAR -->
		<div class="col-md-10">
			<div class="text-right margin-bottom-20">
				<button type="button" name="keep_shopping" value="Keep Shopping" class="btn bg-green-jungle" onclick="javascript:location.href='{{ URL::to('product') }}'">
				<i class="fa fa-shopping-cart"></i> {{ trans('showCart.cart_keep_shopping') }}</button>
			</div>

			@if($cart_total_count > 0)
				<div class="alert alert-info">
					<h3 class="no-margin media-320">
						<strong>{{$cart_total_count}}</strong>
						<span>@if($cart_total_count == 1) {{ trans('showCart.cart_your_item') }} @else {{ trans('showCart.cart_your_items') }} @endif </span>
						<button type="button" name="remove_all" value="remove_all" class="btn red-sunglo btn-xs margin-top-5 pull-right" onclick="return emptyCart();">
						<i class="fa fa-trash-o bigger-110"></i> {{ trans('showCart.empty_cart') }}</button>
					</h3>
				</div>
			@endif

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="alert alert-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="alert alert-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<!-- BEGIN: CART ITEMS LIST -->
			<div class="well">
				@if(count($cart_details) > 0)
					<?php $show_currency = CUtil::getCheckDefaultCurrencyActivate();	?>
					<div class="clearfix form-horizontal">
						<div class="form-group fn_clsPriceFields ctrl-labelleft margin-bottom-30 {{{ $errors->has('shipping_country') ? 'error' : '' }}}">
							{{ Form::label('shipping_country', trans('showCart.ship_orders_to'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-4">
								{{  Form::select('shipping_country', $d_arr['countries_list'], Input::old('shipping_country', $d_arr['shipping_country_id']), array('class' => 'form-control select2me input-medium fnShippingCostEstimate', 'id' => 'shipping_country')); }}
								<label class="error">{{{ $errors->first('shipping_country') }}}</label>
							</div>
						</div>
					</div>
					<small id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></small>
					@foreach($cart_details as $cartKey => $cart)
						<h1 class="title-one">{{ trans('showCart.order_from') }} <a href="{{ url('shop/'.$cart['url_slug']) }}" >{{{$cart['shop_name']}}}</a></h1>
						<div class="responsive-view margin-bottom-30">
							<div class="responsive-xscroll">
								<?php $allow_to_process = false;
								 $checkout_total = $new_checkout_total = 0; ?>
								{{ Form::open(array('id'=>'itemFrm_'.$cart['item_owner_id'], 'method'=>'post', 'class' => 'form-horizontal cart-list' )) }}
									{{ Form::hidden('itemdetails_list_owner_'.$cart['item_owner_id'], $cart['item_owner_id'], array('id' => 'itemdetails_list_owner_'.$cart['item_owner_id'])) }}
									<?php $currency_cart_items = 0; ?>
									<div class="blog-item">
										<div class="cart-list-title">
											<div class="row">
												<div class="col-md-4 portfolio-info"><strong>{{ trans('showCart.item') }}</strong></div>
												<div class="portfolio-info col-md-1"><strong>{{ trans('showCart.quantity') }}</strong></div>
												<div class="portfolio-info col-md-1 new-col-1"><strong>{{ trans('showCart.product_price') }}</strong></div>
												<div class="portfolio-info col-md-2 new-col-2"><strong>{{ trans('showCart.tax_details') }}</strong></div>
												<div class="portfolio-info col-md-2"><strong>{{ trans('showCart.shipping_details') }}</strong></div>
												<div class="portfolio-info col-md-2"><strong>{{ trans('showCart.cart_sub_total') }}</strong></div>
												<div class="portfolio-info col-md-1 new-col-3"><strong>{{ trans('common.action') }}</strong></div>
											</div>
										</div>
										@if($cart_item_details[$cart['item_owner_id']]['product']['free_item'])
											@foreach($cart_item_details[$cart['item_owner_id']]['product']['free_item'] as $item)
												<?php
													$allow_to_process = true;
													$p_img_arr = $prod_obj->getProductImage($item['id']);
													/*echo '<pre>';
													print_r($item);die;*/
													$p_thumb_img = $ProductService->getProductDefaultThumbImage($item['id'], 'small', $p_img_arr);
													$view_url = $ProductService->getProductViewURL($item['id']);

													$item_id = $item['id'];

													$qty = $item['qty'];
													$price_with_qty = $item['product_price'] * $qty;
													$new_sub_total = $product_tot_tax_amount = $total_shipping_fee = 0;
													//echo "<pre>";print_r();echo "</pre>";
												?>
												<div class="portfolio-block">
													<div class="row">
														<div class="portfolio-info col-md-4">
															<div class="portfolio-text">
																<a href="{{ $view_url }}" class="imgsize-75X75 pull-left margin-right-10">
																	<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{{ $item['product_name']  }}}" alt="{{{ $item['product_name']  }}}"/>
                                                                </a>
																<div class="portfolio-text-info pull-left">
																	<h4><a href="{{ $view_url }}">{{{ $item['product_name'] }}}</a></h4>
																	<p>{{ trans('showCart.product_code') }}: <span class="text-muted">{{ $item['product_code'] }}</span></p>
																</div>
															</div>
														</div>
														<div class="portfolio-info col-md-1">
															@if($item['is_downloadable_product'] == 'Yes')
																{{ Form::hidden('product_qty', $qty, array ('id'=>"product_qty_".$cart['item_owner_id']."_".$item_id )); }}
																<strong>1</strong>
															@else
																{{ Form::input('number', 'product_qty', $qty, array ('id'=>"product_qty_".$cart['item_owner_id']."_".$item_id, 'class' => 'form-control input-small fnQuantity', 'autocomplete' => 'off', 'maxlength' => '5', 'min' => '1', 'max' => '1000')); }}
                                                                <p class="text-muted margin-top-5"><small>Max <strong>1000 qty</strong></small></p>
															@endif
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
																@if($show_currency)
																	<p><span id="product_tax_total_{{$item_id}}">{{ CUtil::convertAmountToCurrency($product_tot_tax_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</span> <span><a href="javascript:void(0);" onclick="return showTaxDetails('{{$item_id}}')" title="{{ trans('checkOut.tax_details') }}" class="text-muted"><i class="fa fa-question-circle"></i></a></span></p>
																@else
																	<p><span id="product_tax_total_with_currency_{{$item_id}}">{{ CUtil::convertAmountToCurrency($product_tot_tax_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span> <span><a href="javascript:void(0);" onclick="return showTaxDetails('{{$item_id}}')" title="{{ trans('checkOut.tax_details') }}" class="text-muted"><i class="fa fa-question-circle"></i></a></span></p>
																@endif
																<div id="tax_fee_details_{{$item_id}}" style="display:none">
																	@if(!empty($product_taxes['product_tax_details']))
																		@foreach($product_taxes['product_tax_details'] as $tax_det)
																			<p>{{$tax_det['tax_label']}}</p>
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
																@if(strtolower($item['is_downloadable_product']) == 'yes')
																	<p class="badge badge-primary">{{ trans('common.free') }}</p>
																@else
																	<div class="alert alert-danger no-margin"><small>{{ trans('common.do_not_ship_to_country') }}</small></div>
																@endif
															@endif
														</div>
														<?php
															$currency_cart_items++;
															$new_sub_total = $price_with_qty + $product_tot_tax_amount + $total_shipping_fee;
															$new_checkout_total += $new_sub_total;
														?>
														<div class="portfolio-info col-md-2">
															<p><span id="new_product_subtotal_{{$item_id}}">
																{{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), '', true) }}
															</span></p>
															@if($show_currency)
																<p><span id="new_product_subtotal_with_currency_{{$item_id}}">
																	{{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}
																</span></p>
															@endif
														</div>
														<div class="portfolio-info col-md-1 new-col-3">
															<div class="action-btn">
																<a href="javascript:void(0);" onclick="return removeCartItem('{{$item['id']}}', 'Product')" title="{{trans('showCart.remove_from_cart')}}" class="btn red btn-xs"><i class="fa fa-trash-o"></i></a>
															</div>
														</div>
													</div>
												</div>
											@endforeach
										@endif
									</div>

									<?php //echo "<pre>"; print_r($cart_item_details);echo "</pre>"; ?>
									@foreach($currency_arr as $currency)
										@if($cart_item_details[$cart['item_owner_id']]['product'][$currency])
											<div class="blog-item">
												<?php $currency_cart_items++; ?>
                                                <?php $total_giftwrap_amount = 0; ?>
												@foreach($cart_item_details[$cart['item_owner_id']]['product'][$currency] as $item)
													<?php
														$p_img_arr = $prod_obj->getProductImage($item['id']);
													//	echo '<pre>';
													//	print_r($item);die;
														$allow_to_process = true;
														$p_thumb_img = $ProductService->getProductDefaultThumbImage($item['id'], 'small', $p_img_arr);
														$view_url = $ProductService->getProductViewURL($item['id']);

														$item_id = $item['id'];
														$marix_id = isset($item['matrix_id']) ? $item['matrix_id'] : 0;
														$qty = $item['qty'];
														$price_with_qty = $item['product_price'] * $qty;
														$new_sub_total = $product_tot_tax_amount = $total_shipping_fee = $total_giftwrap_amount = 0;
														$deal_details = array();
														$deal_available = $allow_variation = 0;

														if(CUtil::chkIsAllowedModule('deals') && isset($item['deal_details']) && COUNT($item['deal_details']) >0 )
														{
															$deal_available = 1;
														}
														$allow_variation = (CUtil::chkIsAllowedModule('variations') && $marix_id > 0) ? 1 : 0;
													?>
													<div class="portfolio-block">
														<div class="row">
															<div class="portfolio-info col-md-4">
																<div class="portfolio-text">
																	<a href="{{ $view_url }}" class="imgsize-75X75 pull-left margin-right-10">
																		<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{{ $item['product_name']  }}}" alt="{{{ $item['product_name']  }}}"  class=""/>
                                                                    </a>
																	<div class="pull-left">
																		<div class="portfolio-text-info">
																			<h4><a href="{{ $view_url }}">{{{ $item['product_name'] }}}</a></h4>
																			<p>{{ trans('showCart.product_code') }}: <span class="text-muted">{{ $item['product_code'] }}</span></p>
																		</div>


																		<!-- BEGIN: VARIATION BLOCK -->
                                                                        @if(isset($allow_variation) && $allow_variation > 0)

                                                                            @if(isset($item['variation_details']['attrirb_det']) && COUNT($item['variation_details']['attrirb_det']) > 0)
	                                                                            <div class="dl-horizontal deal-cartbk margin-top-5">
	                                                                                @foreach($item['variation_details']['attrirb_det'] AS $attr)
	                                                                                	<dl>
	                                                                                        <dt>{{ $attr['name'] }}: </dt>
	                                                                                        <dd><span>{{ $attr['label'] }}</span></dd>
	                                                                                    </dl>
	                                                                                @endforeach
	                                                                            </div>
                                                                            @endif
                                                                            {{Form::hidden('matrix_id_'.$cart['item_owner_id'].'_'.$item_id, $item['matrix_id'], array('id' => 'matrix_id_'.$cart['item_owner_id'].'_'.$item_id))}}
                                                                             <a href="{{ URL::action('CartController@getUpdateItemVariation') ."?item_id=".$item_id."&matrix_id=".$marix_id."&r_fnname=updVar" }}" class="fn_signuppop green label label-info">{{ Lang::get('variations::variations.change_variation') }}</a>
                                                                         @endif
                                                                        <!-- END: VARIATION BLOCK -->

                                                                        <!-- BEGIN: DEAL BLOCK -->
																		@if($deal_available)
																			<div class="dl-horizontal deal-cartbk margin-top-20">
																				<dl>
																					<dt title="{{ Lang::get('deals::deals.deal_discount') }}">{{ Lang::get('deals::deals.deal_discount') }}</dt>
																					<dd><span> {{ $item['deal_details']['deal_discount'] }}%</span></dd>
																				</dl>

																				<dl>
																					<dt title="{{ Lang::get('deals::deals.origional_price_label') }}">{{ Lang::get('deals::deals.origional_price_label') }}</dt>
																					<dd><span>
                                                                                    {{ CUtil::convertAmountToCurrency($item['deal_details']['normal_price'], Config::get('generalConfig.site_default_currency'), '', true) }}
                                                                                   </span></dd>
																				 </dl>

																				<dl>
																					<dt title="{{ Lang::get('deals::deals.deal_price_lbl') }}">{{ Lang::get('deals::deals.deal_price_lbl') }}</dt>
																					<dd><span>
                                                                                     {{ CUtil::convertAmountToCurrency($item['deal_details']['deal_price'], Config::get('generalConfig.site_default_currency'), '', true) }}
                                                                                    </span></dd>
																				</dl>
																			</div>
																		   <a href="{{ $item['deal_details']['viewdeal_link'] }}" target="_blank" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" class="label label-primary"><i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}</a>
																		@endif
																		<!-- END: DEAL BLOCK -->
																	</div>
																</div>
															</div>
															<div class="portfolio-info col-md-1">
																{{ Form::input('number', 'product_qty', $qty, array ('id'=>"product_qty_".$cart['item_owner_id']."_".$item_id, 'class' => 'form-control input-small fnQuantity', 'autocomplete' => 'off', 'maxlength' => '5', 'min' => '1', 'max' => '1000')); }}
                                                                <p class="text-muted margin-top-5"><small>Max <strong>1000 qty</strong></small></p>
															</div>


															<div class="portfolio-info col-md-1 new-col-1">
																<p><span id="product_total_{{$item_id}}">
																	{{ CUtil::convertAmountToCurrency($price_with_qty, Config::get('generalConfig.site_default_currency'), '', true) }}
																</span></p>
																@if($show_currency)
																	<p><span id="product_total_with_currency_{{$item_id}}">
																		{{ CUtil::convertAmountToCurrency($price_with_qty, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}
																	</span></p>
																@endif

                                                                @if(isset($allow_variation) && isset($d_arr['show_giftwrap_column']) && $d_arr['show_giftwrap_column'] && isset($item['use_giftwrap']) && $item['use_giftwrap'])
                                                                    <div class="checkbox-inline">
                                                                    <?php
                                                                        $giftwrap_opt = (isset($item['use_giftwrap']) && $item['use_giftwrap']) ? true: false;
                                                                    ?>
                                                                    {{ Form::checkbox('use_giftwrap_'.$cart['item_owner_id'].'_'.$item_id, 1, $giftwrap_opt, array('class' => 'js-use_as_billing giftwrapChk', 'name' => 'use_giftwrap', 'id' => 'use_giftwrap_'.$cart['item_owner_id'].'_'.$item_id)) }}
                                                                    {{ Form::label('use_giftwrap_'.$cart['item_owner_id'].'_'.$item_id, Lang::get('variations::variations.giftwrap_lbl'), array('class' => 'margin-top-3')) }}
                                                                    </div>
                                                                    @if(isset($item['use_giftwrap']) && $item['use_giftwrap'])
                                                                    <?php
																		$giftwrap_type = '';
																		$giftwrap_pricing = '';
																		if(isset($item['giftwrap_type']))
																			$giftwrap_type = $item['giftwrap_type'];
																		if(isset($item['giftwrap_pricing']))
																			$giftwrap_pricing = $item['giftwrap_pricing'];

                                                                    	if(isset($item['variation_details']['price_impact']) && ($item['variation_details']['price_impact'] == 'increase' || $item['variation_details']['price_impact'] == 'decrease')){
                                                                            $giftwrap_amount = ($giftwrap_type == 'single') ? ($giftwrap_pricing + $item['variation_details']['giftwrap_price']) * $item['qty'] : $giftwrap_pricing;
                                                                            if($giftwrap_amount < 0){
                                                                                $giftwrap_amount = 0;
                                                                            }
                                                                        }

																		if((isset($item['variation_details']['giftwrap_price'])) && ($item['variation_details']['giftwrap_price'] == '' || $item['variation_details']['giftwrap_price'] == 0)){
                                                                            $giftwrap_amount = ($giftwrap_type == 'single') ? $giftwrap_pricing  * $item['qty'] : $giftwrap_pricing;
                                                                        }
																		$total_giftwrap_amount += isset($giftwrap_amount) ? $giftwrap_amount : 0;
                                                                    ?>

                                                                    @if(isset($giftwrap_amount))
                                                                        <p>{{ CUtil::convertAmountToCurrency($giftwrap_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                                                        @if($show_currency)
                                                                            <p>{{ CUtil::convertAmountToCurrency($giftwrap_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</p>
                                                                        @endif
																	@endif
                                                                   @endif
                                                                @endif

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
																	@if($show_currency)
																		<p><span id="product_tax_total_with_currency_{{$item_id}}">{{ CUtil::convertAmountToCurrency($product_tot_tax_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</span> <span><a href="javascript:void(0);" onclick="return showTaxDetails('{{$item_id}}')" title="{{ trans('checkOut.tax_details') }}" class="text-muted"><i class="fa fa-question-circle"></i></a></span></p>
																	@endif
																	<div id="tax_fee_details_{{$item_id}}" style="display:none">
																		@if(!empty($product_taxes['product_tax_details']))
																			@foreach($product_taxes['product_tax_details'] as $tax_det)
																				<p>{{$tax_det['tax_label']}}</p>
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
																		$variation_shipping_price = 0;

																		$total_shipping_fee = $shipping_fee;

																		if(isset($allow_variation) && $allow_variation) {
																			if(isset($shipping_fee)) {
																				if(isset($item["variation_details"]["shipping_price"]) && $item["variation_details"]["shipping_price"] != '') {
																						$variation_shipping_price = $item["variation_details"]["shipping_price"];
																				}
																				if(isset($item["variation_details"]["shipping_price_impact"])) {
																					if($item["variation_details"]["shipping_price_impact"] == 'increase' || $item["variation_details"]["shipping_price_impact"] == 'decrease') {
																						$total_shipping_fee = $shipping_fee + $variation_shipping_price;
																						if(!isset($total_shipping_fee) || $total_shipping_fee < 0) {
			                                                                                $total_shipping_fee = 0;
			                                                                            }
			                                                                        }
			                                                                    }
			                                                                }
			                                                            }
																	?>
																	{{Form::hidden('cart_id_'.$cart['item_owner_id'].'_'.$item_id, $item['cart_id'], array('id' => 'cart_id_'.$cart['item_owner_id'].'_'.$item_id))}}
																	{{Form::hidden('shipping_template_'.$cart['item_owner_id'].'_'.$item_id, $item['shipping_template'], array('id'=>'shipping_template_'.$cart['item_owner_id'].'_'.$item_id))}}
																	{{Form::hidden('shipping_company_id_selected_'.$cart['item_owner_id'].'_'.$item_id, $shipping_company_id_selected, array('id'=>'shipping_company_id_selected_'.$cart['item_owner_id'].'_'.$item_id))}}
																	@include('shippingCompanyPopupMin', array('shipping_companies_details' => $shipping_companies_details, 'shipping_template' => $item['shipping_template'], 'cart_id' => $item['cart_id'], 'item_id' => $item_id, 'variation_shipping_price' => $variation_shipping_price, 'allow_variation' => $allow_variation))
																@else
																	@if(strtolower($item['is_downloadable_product']) == 'yes')
																		<p class="badge badge-primary">{{ trans('common.free') }}</p>
																	@else
																		<div class="alert alert-danger no-margin"><small>{{ trans('common.do_not_ship_to_country') }}</small></div>
																	@endif
																@endif
															</div>
															<?php																
																$new_sub_total = $price_with_qty + CUtil::formatAmount($product_tot_tax_amount) + CUtil::formatAmount($total_shipping_fee) + CUtil::formatAmount($total_giftwrap_amount);
																$new_checkout_total += $new_sub_total;
															?>
															<div class="portfolio-info col-md-2">
																<p><span id="new_product_subtotal_{{$item_id}}">
																	{{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), '', true) }}
																</span></p>
																@if($show_currency)
																	<p><span id="new_product_subtotal_with_currency_{{$item_id}}">
																		{{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}
																	</span></p>
																@endif
															</div>
															<div class="portfolio-info col-md-1 new-col-3">
																<div class="action-btn">
																	<a href="javascript:void(0);" onclick="return removeCartItem('{{$item['id']}}', 'Product')" title="{{trans('showCart.remove_from_cart')}}" class="btn red btn-xs"><i class="fa fa-trash-o"></i></a>
																</div>
															</div>
														</div>
													</div>
												@endforeach
											 </div>
										@endif
									@endforeach

									@if($cart_item_details[$cart['item_owner_id']]['not_available_product']['item'] || $cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item'])
										@if(count($cart_item_details[$cart['item_owner_id']]['not_available_product']) == 1)
											<div class="alert alert-danger">{{ trans('showCart.product_cannot_purchase') }}</div>
										@else
											<div class="alert alert-danger">{{ trans('showCart.products_cannot_purchase') }}</div>
										@endif
										<div class="blog-item">
											<div class="row cart-list-title">
												<div class="portfolio-info col-md-4"><strong>{{ trans('showCart.product_details') }}</strong></div>
												<div class="portfolio-info col-md-4"><strong>{{ trans('showCart.product_price') }}</strong></div>
												<div class="portfolio-info col-md-4 new-col-3"><strong>{{ trans('common.action') }}</strong></div>
											</div>
											<div class="portfolio-block">
												@if($cart_item_details[$cart['item_owner_id']]['not_available_product']['item'])
													@foreach($cart_item_details[$cart['item_owner_id']]['not_available_product']['item'] as $key =>  $item)
														<?php
															//echo "<pre>"; print_r($item); echo "</pre>";exit;
															$p_img_arr = $prod_obj->getProductImage($item['id']);
															$p_thumb_img = $ProductService->getProductDefaultThumbImage($item['id'], 'small', $p_img_arr);
															$view_url = $ProductService->getProductViewURL($item['id']);

														?>
														<div class="row">
															<div class="portfolio-info col-md-4">
																<div class="portfolio-text">
																	<a href="{{$view_url}}" class="imgsize-75X75"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{ $item['product_name']  }}" alt="{{{ $item['product_name']  }}}" /></a>
																	<div class="portfolio-text-info">
																		<h4>{{{ $item['product_name'] }}}</h4>
																		<p>{{ trans('showCart.product_code') }}: <span class="text-muted">{{ $item['product_code'] }}</span></p>
																	</div>
																</div>
															</div>
															<div class="portfolio-info col-md-4">
																@if($item['product_price'] > 0)
																	{{ CUtil::convertAmountToCurrency($item['product_price'], Config::get('generalConfig.site_default_currency'), '', true) }}
																@else
																	<strong class="badge badge-primary">{{ trans('common.free') }}</strong>
																@endif
																<div class="portfolio-text-info">
																	@if(isset($item['not_available_reason']) == 'product_stock')
																		<p class="badge badge-danger margin-top-5">{{ $item['not_available_reason_msg'] }}</p>
																	@else if(isset($item['not_available_reason']) == 'product_expired' || isset($item['not_available_reason']) == 'product_status')
																		<p class="badge badge-danger margin-top-5">{{ $item['not_available_reason_msg'] }}</p>
																	@endif
																</div>
															</div>
															<div class="portfolio-info col-md-4 new-col-3">
																<div class="action-btn">
																	<a class="btn btn-xs red" href="javascript:void(0);" onclick="return removeCartItem('{{$item['id']}}', 'Product')" title="{{trans('showCart.remove_from_cart')}}"><i class="fa fa-trash-o"></i></a>
																</div>
															</div>
														</div>
													@endforeach
												@endif
												@if($cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item'])
													@foreach($cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item'] as $item)
														<?php
															$p_img_arr = $prod_obj->getProductImage($item['id']);
															$p_thumb_img = $ProductService->getProductDefaultThumbImage($item['id'], 'small', $p_img_arr);
															$view_url = $ProductService->getProductViewURL($item['id']);
														?>
														<div class="row">
															<div class="portfolio-info col-md-4">
																<div class="portfolio-text">
																	<a href="{{ $view_url }}" class="imgsize-75X75"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{{ $item['product_name']  }}}" alt="{{{ $item['product_name']  }}}" /></a>
																	<div class="portfolio-text-info">
																		<h4>{{{ $item['product_name'] }}}</h4>
																		<p>{{ trans('showCart.product_code') }}: <span class="text-muted">{{ $item['product_code'] }}</span></p>
																	</div>
																</div>
															</div>
															<div class="portfolio-info col-md-4">
																<strong class="badge badge-primary">{{ trans('common.free') }}</strong>
															</div>
															<div class="portfolio-info col-md-4 new-col-3">
																<a class="btn btn-xs red" href="javascript:void(0);" onclick="return removeCartItem('{{$item['id']}}', 'Product')" title="{{trans('common.remove') }}"><i class="fa fa-trash-o"></i></a>
															</div>
														</div>
													@endforeach
												@endif
											</div>
										</div>
									@endif

									@if($currency_cart_items == 0)
										@include("showCartFreeItems")
									@endif
								{{ Form::close() }}
							</div>
						</div>

						@if($allow_to_process)
							<div class="clearfix margin-bottom-10">
								<div class="fonts18 pull-right cart-amount">
									<div class="margin-bottom-5">{{ trans('showCart.estimated_amount') }}:
										<span id="checkout_total">{{ CUtil::convertAmountToCurrency($new_checkout_total, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
									</div>
									@if($show_currency)
										<div class="margin-bottom-5">{{ trans('showCart.estimated_amount') }}:
											<span id="checkout_total_with_currency">{{ CUtil::convertAmountToCurrency($new_checkout_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</span>
										</div>
									@endif
								</div>
							</div>

							@if(CUtil::isMember())
								<div class="clearfix margin-bottom-20">
									<button type="button" name="checkout" value="checkout" class="btn green btn-sm pull-right" onclick="purchaseItem('{{ $cart['item_owner_id'] }}', '{{ Config::get('generalConfig.site_default_currency') }}');">
									<i class="fa fa-check bigger-110"></i> {{ trans('showCart.place_order') }}</button>
								</div>
							@else
								<?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
								<div class="clearfix margin-bottom-20">
									<a href="javascript:void(0);" onclick="signupPopup('{{ $login_url }}')" class="btn green btn-sm pull-right"><i class="fa fa-check bigger-110"></i> {{ trans('showCart.place_order') }}</a>
								</div>
							@endif
						@endif
					@endforeach
				@else
					<p class="note note-info margin-0">{{ trans('showCart.items_not_found_cart') }}</p>
				@endif

				{{ Form::open(array('url' => 'checkout', 'method'=>'post', 'class' => 'form-horizontal',  'id' => 'checkoutFrm', 'name' => 'checkoutFrm')) }}
					{{ Form::hidden('item_owner_id') }}
					{{ Form::hidden('checkout_currency') }}
				{{ Form::close() }}
			</div>
			<!-- END: CART ITEMS LIST -->
		</div>
	</div>
    <script language="javascript" type="text/javascript">
	    var page_name = "view_cart";
	</script>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		var BASE = "{{ Request::root() }}";
		$(".fn_signuppop").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 630,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });

	    function signupPopup(url){
		    jQuery.fancybox.open([
		        {
		            maxWidth    : 800,
			        maxHeight   : 630,
			        fitToView   : false,
			        width       : '70%',
			        height      : '430',
			        autoSize    : false,
			        closeClick  : false,
			        type        : 'iframe',
			        openEffect  : 'none',
			        closeEffect : 'none',
		            href: url,
		        }
		    ]);
		}

		function removeCartItem(item_id, item_type) {
			var cmsg = '{{ trans('showCart.remove_item_confirm_msg') }}';
			var txtYes = '{{ trans('common.yes') }}';
			var txtNo = '{{ trans('common.cancel') }}';
			var buttonText = {};
			buttonText[txtYes] = function(){
										window.location.href="{{ Url::to('cart/delete') }}?item_id="+item_id+"&item_type="+item_type;
									};
			buttonText[txtNo] = function(){
										$(this).dialog('close');
										return false;
									};
			$("#fn_dialog_confirm_msg").html(cmsg);
			$("#fn_dialog_confirm_msg").dialog({
				resizable: false,
				height: 180,
				modal: true,
				title: '{{ trans('showCart.remove_item_title') }}',
				buttons: buttonText
			});
		}
		function emptyCart() {
			var cmsg = '{{ trans('showCart.empty_cart_confirm_msg') }}';
			var txtYes = '{{ trans('common.yes') }}';
			var txtNo = '{{ trans('common.cancel') }}';
			var buttonText = {};
			buttonText[txtYes] = function(){
										window.location.href="{{ Url::to('cart/empty') }}";
									};
			buttonText[txtNo] = function(){
										$(this).dialog('close');
										return false;
									};
			$("#fn_dialog_confirm_msg").html(cmsg);
			$("#fn_dialog_confirm_msg").dialog({
				resizable: false,
				height: 180,
				modal: true,
				title: '{{ trans('showCart.empty_cart_title') }}',
				buttons: buttonText
			});
		}


		var purchaseItem = function()
		{
			var item_owner_id = arguments[0];
			var checkout_currency = arguments[1];
			var checkout_url = '{{Url::to("checkout")}}'+'/'+item_owner_id;
			var addr_id= '{{$shipping_address_id}}';
			var country_id= '{{$last_shipping_country_id}}';
			displayLoadingImage(true);
			$.post("{{ URL::action('CheckOutController@postUpdateShippingAddress') }}", {"address_id": addr_id, "country_id": country_id}, function(data) {
				$("#checkoutFrm input[name=item_owner_id]").val(item_owner_id);
				$("#checkoutFrm input[name=checkout_currency]").val(checkout_currency);
				$("#checkoutFrm").attr('action',checkout_url)
				$("#checkoutFrm").submit();
				//hideLoadingImage();
			});
			/*$("#checkoutFrm input[name=item_owner_id]").val(item_owner_id);
			$("#checkoutFrm input[name=checkout_currency]").val(checkout_currency);
			$("#checkoutFrm").attr('action',checkout_url)
			$("#checkoutFrm").submit();
			*/
		};

		$(document).ready(function() {
		    $(".fnQuantity").live("keypress", function (e) {
		    	var specialKeysPrice = new Array();
				specialKeysPrice.push(8); //Backspace
				specialKeysPrice.push(9); //tab
				specialKeysPrice.push(13); //Enter
		        var keyCode = e.which ? e.which : e.keyCode
		        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
		        var limit = 4;
				if (keyCode == 8 || e.keyCode == 46) limit = 5;
				ret = (ret && this.value.length < limit);
		        return ret;
		    });
		    $(".fnQuantity").live("keyup", function (e) {
		    	var elem_id = $(this).attr('id');
				var elem_id_str = elem_id.split('_');
				var quantity = $("#"+elem_id).val();
				if (quantity == '' || quantity <= 0) {
					$("#"+elem_id).val(1);
					$("#"+elem_id).change();
				}
				if (quantity > 1000) {
					$(this).val(1000);
					$(".fnQuantity").change();
				}
		    });

			$(".fnQuantity" ).change(function() {
				var elem_id = $(this).attr('id');
				var elem_id_str = elem_id.split('_');
				var quantity = $("#"+elem_id).val();
				/*var regx = /^[0-9]+$/;
				if (!regx.test(quantity)) {
					$("#"+elem_id).val(1);
					return false;
				}*/
				if (quantity == '' || quantity <= 0) {
					$("#"+elem_id).val(1);
				}
				var item_owner_id = elem_id_str[2];
				var item_id = elem_id_str[3];

				var total = $("#product_total_"+item_id+ " strong").html();

				//Set shipping company in cooike
				var cart_id = $('#cart_id_'+item_owner_id+'_'+item_id).val();
				var shipping_company_id = 0;
				if($('#shipping_company_id_selected_'+item_owner_id+'_'+item_id).length > 0)
					shipping_company_id = $('#shipping_company_id_selected_'+item_owner_id+'_'+item_id).val();

				/*$.post("{{ Url::to('updateShippingCompany')}}", {"cart_id": cart_id, "shipping_company_id": shipping_company_id}, function(data) {
					//parent.hideLoadingImage();
				})*/

				var tax_total = 0;
				if($("#product_tax_total_"+item_id).length > 0)
					tax_total = $("#product_tax_total_"+item_id+ " strong").html();
				var ship_price = 0;
				if($("#shipping_price_"+item_id).length > 0)
					ship_price = $("#shipping_price_"+item_id+ " strong").html();
				var subtotal = $("#new_product_subtotal_"+item_id+ " strong").html();
				var checkout_total = $("#checkout_total"+ " strong").html();
	       		displayLoadingImage(true);
	       		$.ajax({
	       			type:'POST',
	       			url: BASE + "/cart/update-product-quantity",
	       			data: {"item_id": item_id, "item_owner_id" : item_owner_id, "quantity": quantity, "total" : total, "tax_total" : tax_total, "ship_price" : ship_price, "subtotal" : subtotal, "checkout_total" : checkout_total, "shipping_company_id": shipping_company_id},
	       			success: function(data){

						data_arr = eval( '(' +  data + ')');
						if(!data_arr.error_exists)
						{
							$("#product_total_"+item_id).html(data_arr.total);
							if($("#product_tax_total_"+item_id).length > 0)
								$("#product_tax_total_"+item_id).html(data_arr.tax_total);
							$("#new_product_subtotal_"+item_id).html(data_arr.sub_total);
							$("#checkout_total").html(data_arr.checkout_total);
							if($("#shipping_price_"+item_id).length > 0)
								$("#shipping_price_"+item_id).html(data_arr.ship_total);

							//if multiple currency supported
							if($("#product_total_with_currency_"+item_id).length > 0)
								$("#product_total_with_currency_"+item_id).html(data_arr.total_curr);
							if($("#product_tax_total_with_currency_"+item_id).length > 0)
								$("#product_tax_total_with_currency_"+item_id).html(data_arr.tax_total_curr);
							if($("#new_product_subtotal_with_currency_"+item_id).length > 0)
								$("#new_product_subtotal_with_currency_"+item_id).html(data_arr.sub_total_curr);
							if($("#checkout_total_with_currency").length > 0)
								$("#checkout_total_with_currency").html(data_arr.checkout_total_curr);
							if($("#shipping_price_with_currency_"+item_id).length > 0)
								$("#shipping_price_with_currency_"+item_id).html(data_arr.ship_total_curr);
						}

						//Update the shipping template details
						var shipping_template = $('#shipping_template_'+item_owner_id+'_'+item_id).val();
						$.post(BASE + "/cart/cart-companies-list", {"cart_id": cart_id, "item_id": item_id, "quantity": quantity, "shipping_template": shipping_template, "item_owner_id": item_owner_id} , function(data){
							$('#shipping_companies_'+cart_id).html(data);
							hideLoadingImage();
						})
					},
				});
			});

			$("#shipping_country").change(function() {
				var actions_url = '{{ URL::action('CartController@getChangeShippingCountry')}}';
				var shipping_country_id =  $(this).val();
				var qry_str = 'shipping_country=' + shipping_country_id + '&redirect_to=cart';
				window.location.href = actions_url + "?" + qry_str;
			});

			$(".fnchangeShippingCompany").live("click", function (e) {
				var elem_id = $(this).attr('id');
				var cart_id = elem_id.split('_')[4];
				var shipping_company = $('input[name=shipping_company_'+ cart_id +']:checked').attr('id');
				var shipping_company_id = shipping_company.split('_')[3];
				displayLoadingImage(true);
	        	var actions_url = '{{ URL::action('CartController@getChangeShippingCompany')}}';
				var qry_str = 'cart_id=' + cart_id + '&shipping_company=' + shipping_company_id + '&redirect_to=cart';
				window.location.href = actions_url + "?" + qry_str;
			});

			$(document).mouseup(function (e) {
			    var container = $(".fnShippingCompanies");
			    if (!container.is(e.target) // if the target of the click isn't the container...
			        && container.has(e.target).length === 0) {
			        container.hide();
			    }
			});

			$(".fnShippingCompaniesOpener").click(function() {
				var elem_id = $(this).attr('id');
				var cart_id = elem_id.split('_')[3];
				if($("#shipping_companies_" + cart_id).is(":hidden")) {
					$(".fnShippingCompanies").hide();
					$("#shipping_companies_" + cart_id).show();
				}
				else
					$("#shipping_companies_" + cart_id).hide();
			});

			$(".fnShippingCompaniesClose").live("click", function (e) {
				$(".fnShippingCompanies").hide();
			});
		});

        function showTaxDetails(item_id)
        {
            var div_id = 'tax_fee_details_'+item_id;
            $('#'+div_id).toggle('slow');
        }

		$('.giftwrapChk').click(function(event) {
			if($(this).attr('checked'))
				var use_giftwrap = 1;
			else
				var use_giftwrap = 0;

			var elem_id = $(this).attr('id');
			var elem_id_str = elem_id.split('_');
			var item_owner_id = elem_id_str[2];
			var item_id = elem_id_str[3];
			displayLoadingImage(true);
			var BASE = "{{ Request::root() }}";
			$.ajax({
				type:'POST',
				url: BASE + "/cart/update-item-giftwrap",
				data: {"item_id": item_id, "item_owner_id" : item_owner_id, "use_giftwrap": use_giftwrap},
				success: function(data){
					data_arr = eval( '(' +  data + ')');
					if(!data_arr.error_exists)
					{
						//alert("No errors found");
						window.location.href = '{{Url::to("cart")}}';
					}
				},
			});
		});


		function updVar(matrix_id, item_id, item_owner_id)
		{
			var total = $("#product_total_"+item_id+ " strong").html();
			var quantity = $('#product_qty_'+item_owner_id+'_'+item_id).val();

			//Set shipping company in cooike
			var cart_id = $('#cart_id_'+item_owner_id+'_'+item_id).val();
			var shipping_company_id = 0;
			if($('#shipping_company_id_selected_'+item_owner_id+'_'+item_id).length > 0)
				shipping_company_id = $('#shipping_company_id_selected_'+item_owner_id+'_'+item_id).val();

			var tax_total = 0;
			if($("#product_tax_total_"+item_id).length > 0)
				tax_total = $("#product_tax_total_"+item_id+ " strong").html();
			var ship_price = 0;
			if($("#shipping_price_"+item_id).length > 0)
				ship_price = $("#shipping_price_"+item_id+ " strong").html();
			var subtotal = $("#new_product_subtotal_"+item_id+ " strong").html();
			var checkout_total = $("#checkout_total"+ " strong").html();

			displayLoadingImage(true);

			$.ajax({
	       			type:'POST',
	       			url: BASE + "/cart/update-product-variation",
	       			data: {"item_id": item_id, "item_owner_id" : item_owner_id, "quantity": quantity, "total" : total, "tax_total" : tax_total, "ship_price" : ship_price, "subtotal" : subtotal, "checkout_total" : checkout_total, "shipping_company_id": shipping_company_id, "matrix_id": matrix_id},
	       			success: function(data){
						data_arr = eval( '(' +  data + ')');
						if(!data_arr.error_exists)
						{
							window.location.href =  '{{Url::to("cart")}}';
						}
					},
				});
		}

	</script>
@stop