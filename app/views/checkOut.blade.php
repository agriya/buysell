@extends('base')
@section('content')
	@include('user_breadcrumb')
	<div class="row">
		<!--  BEGIN: SIDE BAR -->
		<div class="col-md-2">
			<div class="well aside-colectnguid cartchkout-sidebar">
				<h2 class="title-two">{{ trans('showCart.steps') }}</h2>
				<ul class="list-unstyled">
					<li><p><a href="{{ URL::to('cart') }}"><i class="fa fa-chevron-right text-muted"></i> {{ trans('showCart.cart_page_title') }}</a></p></li>
					<li class="active"><p><i class="fa fa-chevron-right text-muted"></i> {{ trans('checkOut.checkout_title') }}</p></li>
				</ul>
			</div>
		</div>
		<!-- END: SIDE BAR -->

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<a href="{{ URL::to('cart') }}" class="pull-right btn btn-xs blue-stripe default margin-top-5">
				<i class="fa fa-chevron-left"></i> {{ trans('common.back_to_cart') }}
			</a>
			<h1>{{ trans('checkOut.checkout_title') }}</h1>
			<?php $show_currency = CUtil::getCheckDefaultCurrencyActivate();	?>
		   <!-- END: PAGE TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if($CheckOutService->is_purchasing_own_item)
				<div class="note note-danger">{{ trans('checkOut.checkout_err_cant_buy_own_item') }}</div>
			@endif
			@if (Session::has('success_message') && Session::get('success_message') != "")
				<div class="note note-success">{{ Session::get('success_message') }}</div>
			@endif
			@if (Session::has('error_message') && Session::get('error_message') != "")
				<div class="note note-danger">{{  Session::get('error_message') }}</div>
			@endif
			<!-- END: ALERT BLOCK -->

			<!-- BEGIN: CHECKOUT FORM -->
			<div class="well">
				{{ Form::open(array('url' => URL::to('checkout/proceed'), 'class' => 'form-horizontal',  'id' => 'form_checkout', 'name' => 'form_checkout')) }}
					{{ Form::hidden('item_owner_id', $d_arr['item_owner_id'], array('id' => 'item_owner_id'))}}
					{{ Form::hidden('checkout_currency', $d_arr['checkout_currency'], array('id' => 'checkout_currency')) }}
					{{ Form::hidden('cookie_id', $CheckOutService->cookie_id, array('id' => 'cookie_id')) }}
					{{ Form::hidden('pid', $d_arr['pid'], array('id' => 'pid')) }}
					{{ Form::hidden('type', $d_arr['type'], array('id' => 'type')) }}
					{{ Form::hidden('is_shipping_needed', $CheckOutService->is_shipping_needed) }}
					{{ Form::hidden('country_id', $CheckOutService->shipping_country_id) }}
					{{ Form::hidden('shipping_address_id', $shipping_address_id, array('id' => 'js-shipping-address-id')) }}
					{{ Form::hidden('billing_address_id', $billing_address_id, array('id' => 'js-billing-address-id')) }}
					{{ Form::hidden('applied_coupon_code', '', array('id' => 'applied_coupon_code')) }}

					@if(count($CheckOutService->cart_item_details_arr) > 0)
						<div class="row">
							<div class="col-md-6">
								<div class="address-details @if(!$CheckOutService->is_shipping_needed) disabled @endif">
									<h2 class="title-one">{{trans('checkOut.shipping_address')}}</h2>
									<?php
										$address_obj = Webshopaddressing::Addressing();
										$shipping_address_details = $address_obj->getAddresses(array('id' => $shipping_address_id));
									?>
									@if($shipping_address_details && count($shipping_address_details) > 0)
										<div id="shipping_address">
											<?php
												$ship_details = $shipping_address_details[0];
											?>
											@if($ship_details->address_line1 != '')
												<p>{{ $ship_details->address_line1 }}</p>
											@endif
											@if($ship_details->address_line2 != '')
												<p>{{ $ship_details->address_line2 }}</p>
											@endif
											@if($ship_details->street != '')
												<p>{{ $ship_details->street }}</p>
											@endif
											<p>{{ $ship_details->city }}</p>
											<p>{{ $ship_details->state }}</p>
											<p>{{ $ship_details->country }}</p>
											<div class="margin-bottom-15">{{ $ship_details->zip_code }}</div>
										</div>
										<p><strong><a href="javascript:;" onclick="openCustomPopup('{{Url::to('checkout/shipping-address-popup')}}','shipping')"  class="fn_signuppop label label-info pad10">{{ trans('checkOut.change_shipping_address') }}</a></strong></p>
										<div class="checkbox-inline">
											{{ Form::checkbox('use_as_billing', 1, false, array('class' => 'js-use_as_billing', 'id' => 'js-use_as_billing')) }}
											{{ Form::label('js-use_as_billing', trans('checkOut.use_this_as_billing_addr'), array('class' => 'margin-top-3')) }}
										</div>
									@else
										<p><strong><a href="javascript:;" onclick="openCustomPopup('{{Url::to('checkout/shipping-address-popup')}}','shipping')" class="fn_signuppop label label-primary pad10">{{ trans('checkOut.enter_shipping_address') }}</a></strong></p>
									@endif
								</div>
							</div>

							<div class="col-md-6">
								<div class="address-details">
									<h2 class="title-one">{{ trans('checkOut.billing_address') }}</h2>
									<?php
										$address_obj = Webshopaddressing::Addressing();
										$billing_address_details = $address_obj->getAddresses(array('id' => $billing_address_id));
									?>
									@if($billing_address_details && count($billing_address_details) > 0)
										<div id="billing_address">
											<?php
												$billing_details = $billing_address_details[0];
											?>
											@if($billing_details->address_line1 != '')
												<p>{{ $billing_details->address_line1 }}</p>
											@endif
											@if($billing_details->address_line2 != '')
												<p>{{ $billing_details->address_line2 }}</p>
											@endif
											@if($billing_details->street != '')
												<p>{{ $billing_details->street }}</p>
											@endif
											<p>{{ $billing_details->city }}</p>
											<p>{{ $billing_details->state }}</p>
											<p>{{ $billing_details->country }}</p>
											<div class="margin-bottom-15">{{ $billing_details->zip_code }}</div>
										</div>
										<p><strong><a href="javascript:;" onclick="openCustomPopup('{{Url::to('checkout/shipping-address-popup')}}','billing')" class="label label-info pad10">{{ trans('checkOut.change_billing_address') }}</a></strong></p>
									@else
										<p><strong><a href="javascript:;" onclick="openCustomPopup('{{Url::to('checkout/shipping-address-popup')}}','billing')" class="label label-primary pad10">{{ trans('checkOut.enter_billing_address') }}</a></strong></p>
									@endif
								</div>
							</div>
						</div>
					@endif

					<div class="responsive-view margin-bottom-30">
						<div class="cart-list responsive-xscroll">
							<div class="blog-item">
								<div class="cart-list-title">
									<div class="row">
										<div class="col-md-3 portfolio-info"><strong>{{ trans('checkOut.item') }}</strong></div>
										<div class="portfolio-info col-md-1"><strong>{{ trans('checkOut.quantity') }}</strong></div>

										<div class="portfolio-info col-md-1 new-col-1"><strong>{{ trans('checkOut.price') }}</strong></div>
										<div class="portfolio-info col-md-2 new-col-2"><strong>{{ trans('checkOut.tax_fees') }}</strong></div>
										<div class="portfolio-info col-md-2"><strong>{{ trans('checkOut.shipping_fee') }}</strong></div>
										<div class="portfolio-info col-md-1"><strong>{{ trans('checkOut.checkout_sub_total') }}</strong></div>
										<div class="portfolio-info col-md-1 new-col-3"><strong>{{ trans('common.action') }}</strong></div>
									</div>
								</div>
								@if(count($CheckOutService->cart_item_details_arr) > 0)
									<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
                                    <?php $total_giftwrap_amount = 0;
									$total_shipping_fee_val = '';
									?>
									@foreach($CheckOutService->cart_item_details_arr as $item)
										<?php
											$is_downloadable_product = $CheckOutService->getItemData($item['item_id'], 'is_downloadable_product', $item['item_type']);
											$product_shipping_country_set = $CheckOutService->getItemData($item['item_id'], 'product_shipping_country_set', $item['item_type']);
											$product_shipping_country_match = $CheckOutService->getItemData($item['item_id'], 'product_shipping_country_match', $item['item_type']);
											$view_url = $ProductService->getProductViewURL($item['item_id']);
											$item_id = $item['item_id'];
											$marix_id = (isset($item['matrix_id']) )  ? $item['matrix_id'] : 0;
											$variation_shipping_price = $total_shipping_fee = 0;
											$allow_variation = (CUtil::chkIsAllowedModule('variations') && $marix_id > 0) ? 1 : 0;
											//echo"<pre>"; print_r($item);exit;
										?>
										<div class="portfolio-block margin-0">
											<div class="row">
												<div class="portfolio-info col-md-3">
													<div class="portfolio-text-info">
														<h4 class="no-top-space">
                                                        	<a href="{{ $view_url }}">{{{ $CheckOutService->getItemData($item['item_id'], 'product_name', $item['item_type']) }}}</a>
                                                        </h4>
														<p>{{trans('product.product_code')}}:
                                                        	<span class="text-muted">{{{ $CheckOutService->getItemData($item['item_id'], 'product_code', $item['item_type']) }}}</span>
                                                        </p>

                                                        @if($allow_variation)
                                                        	<div class="dl-horizontal deal-cartbk margin-top-10">
                                                        <!-- BEGIN: VARIATION BLOCK -->
                                                            @if(isset($item['variation_details']['attrirb_det']) && COUNT($item['variation_details']['attrirb_det']) > 0)
                                                                @foreach($item['variation_details']['attrirb_det'] AS $attr)
                                                                	<dl>
	                                                                    <dt>{{ $attr['name'] }}:</dt><dd><span> {{ $attr['label'] }}</span></dd>
                                                                    </dl>
                                                                @endforeach
                                                            @endif
                                                            {{Form::hidden('matrix_id_'.$d_arr['item_owner_id'].'_'.$item['item_id'], $item['matrix_id'], array('id' => 'matrix_id_'.$d_arr['item_owner_id'].'_'.$item['item_id']))}}
                                                            <?php $change_var_url = URL::action('CartController@getUpdateItemVariation') ."?item_id=".$item_id."&matrix_id=".$marix_id."&r_fnname=updVar"; ?>
															<p><strong><a href="javascript:;" onclick="openCustomVariationPopup('{{$change_var_url}}')"  class="fn_signuppop label label-info">{{ Lang::get('variations::variations.change_variation') }}</a></strong></p>
                                                            <!-- END: VARIATION BLOCK -->
                                                            </div>
														@endif

                                                        <?php
															$dealData = $CheckOutService->getItemData($item['item_id'], 'deal_details', $item['item_type']);
															$price = $CheckOutService->getItemData($item['item_id'], 'product_price', $item['item_type']);
															$product_price_currency = $CheckOutService->getItemData($item['item_id'], 'product_price_currency', $item['item_type']);
														?>
                                                        <!-- BEGIN: DEALS BLOCK -->
														@if(isset($dealData['item_deal_available']) && $dealData['item_deal_available'])
                                                            <div class="dl-horizontal deal-cartbk margin-top-20">
																<dl>
																	<dt title="{{ Lang::get('deals::deals.deal_discount') }}">{{ Lang::get('deals::deals.deal_discount') }}</dt>
																	<dd><span>{{ $dealData['deal_discount'] }}%</span></dd>
																</dl>

																<dl>
																	<dt title="{{ Lang::get('deals::deals.origional_price_label') }}">{{ Lang::get('deals::deals.origional_price_label') }}</dt>
																	<dd><span>{{ CUtil::convertAmountToCurrency($dealData['normal_price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
																</dl>
																<dl>
																	<dt title="{{ Lang::get('deals::deals.deal_price_lbl') }}">{{ Lang::get('deals::deals.deal_price_lbl') }}</dt>
																	<dd><span>{{ CUtil::convertAmountToCurrency($dealData['deal_price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
																</dl>
															</div>
															<a href="{{ $dealData['viewdeal_link'] }}" target="_blank" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" class="label label-primary"><i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}</a>
                                                        @endif
														<!-- END: DEALS BLOCK -->
													</div>
												</div>

												<div class="portfolio-info col-md-1">
													<strong>{{ $item['item_qty'] }}</strong>
												</div>


												<?php $product_price_currency = $CheckOutService->getItemData($item['item_id'], 'product_price_currency', $item['item_type']);?>
												<div class="portfolio-info col-md-1 new-col-1">
													@if($CheckOutService->getItemData($item['item_id'], 'is_free_product', $item['item_type']) == 'Yes')
														<p class="badge badge-primary">{{ trans('common.free') }}</p>
													@else
														<?php
															$price = $CheckOutService->getItemData($item['item_id'], 'product_price', $item['item_type']);
															$giftwrap_amount= 0;
														?>
														<p>{{ CUtil::convertAmountToCurrency($price * $item['item_qty'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
														@if($show_currency)
															<p>{{ CUtil::convertAmountToCurrency($price * $item['item_qty'], Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</p>
														@endif
													@endif

                                                    @if($allow_variation && isset($d_arr['show_giftwrap_column']) && $d_arr['show_giftwrap_column'])
                                                	<?php
														$giftwrap_type = $CheckOutService->getItemData($item['item_id'], 'giftwrap_type', $item['item_type']);
														$giftwrap_pricing = $CheckOutService->getItemData($item['item_id'], 'giftwrap_pricing', $item['item_type']);
														$accept_giftwrap = $CheckOutService->getItemData($item['item_id'], 'accept_giftwrap', $item['item_type']);
														$accept_giftwrap_message = $CheckOutService->getItemData($item['item_id'], 'accept_giftwrap_message', $item['item_type']);
														$item['show_giftwrap_message'] = 0;
														//if($accept_giftwrap == 1 && $item['use_giftwrap'] == 1 && $accept_giftwrap_message == 1)
														if($accept_giftwrap == 1 && $item['use_giftwrap'] == 1)
														{
															$item['show_giftwrap_message'] = 1;
															if($item['variation_details']['giftwrap_price_impact'] == 'increase' || $item['variation_details']['giftwrap_price_impact'] == 'decrease'){
																$giftwrap_amount = ($giftwrap_type == 'single') ? ($giftwrap_pricing + $item['variation_details']['giftwrap_price']) * $item['item_qty'] : $giftwrap_pricing;
																if($giftwrap_amount < 0){
																	$giftwrap_amount = 0;
																}
															}
															if($item['variation_details']['giftwrap_price'] == '' || $item['variation_details']['giftwrap_price'] == 0){
																$giftwrap_amount = ($giftwrap_type == 'single') ? $giftwrap_pricing  * $item['item_qty'] : $giftwrap_pricing;
															}
														}
														$total_giftwrap_amount += isset($giftwrap_amount) ? $giftwrap_amount : 0;
														$giftwrap_opt = (isset($item['use_giftwrap']) && $item['use_giftwrap']) ? true: false;
													?>
                                                    @if(isset($accept_giftwrap) && $accept_giftwrap == 1)
                                                        {{ Form::checkbox('use_giftwrap', 1, $giftwrap_opt, array('class' => 'js-use_as_billing giftwrapChk', 'id' => 'usegiftwrap_'.$d_arr['item_owner_id'].'_'.$item['item_id'])) }}
                                                            {{ Form::label('usegiftwrap_'.$d_arr['item_owner_id'].'_'.$item['item_id'], Lang::get('variations::variations.giftwrap_lbl'), array('class' => 'margin-top-3')) }}
                                                    @endif
                                                         @if($item['use_giftwrap'] == 1)
                                                            <p>{{ CUtil::convertAmountToCurrency($giftwrap_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</p>
															@if($show_currency)
																<p>{{ CUtil::convertAmountToCurrency($giftwrap_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</p>
															@endif

                                                            @if($item['show_giftwrap_message'] && $accept_giftwrap_message == 1)
                                                                <?php
                                                                    $giftwrapMsg = Lang::get('variations::variations.cart_giftwrap_msg_note');
                                                                    if(isset($item['giftwrap_msg']) && $item['giftwrap_msg']!= "")
                                                                        $giftwrapMsg = $item['giftwrap_msg'];
                                                                ?>
                                                                {{ Form::textarea('giftwrap_msg_'.$d_arr['item_owner_id'].'_'.$item['item_id'], $giftwrapMsg, array('class' => 'form-control valid fn_giftwrap_msg', 'id' => 'giftwrap_msg_'.$d_arr['item_owner_id'].'_'.$item['item_id'], 'rows' => 5)) }}
                                                            @endif
                                                        @endif
                                                    @endif

												</div>

												<div class="portfolio-info col-md-2 new-col-2">
													<?php $product_tax_details = $CheckOutService->getItemData($item['item_id'], 'product_tax_details', $item['item_type']);
														$product_tot_tax_amount = $CheckOutService->getItemData($item['item_id'], 'product_tot_tax_amount', $item['item_type']);
													?>
													@if($product_tot_tax_amount > 0)
														<p>{{ CUtil::convertAmountToCurrency($product_tot_tax_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
															<a href="javascript:void(0);" onclick="return showTaxDetails('{{$item['item_id']}}')" title="{{ trans('checkOut.tax_details') }}" class="text-muted"><i class="fa fa-question-circle"></i></a>
														</p>
														@if($show_currency)
															{{ CUtil::convertAmountToCurrency($product_tot_tax_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}
															<a href="javascript:void(0);" onclick="return showTaxDetails('{{$item['item_id']}}')" title="{{ trans('checkOut.tax_details') }}" class="text-muted"><i class="fa fa-question-circle"></i></a>
														@endif
														<div id="tax_fee_details_{{$item['item_id']}}" style="display:none">
															@if(!empty($product_tax_details))
																@foreach($product_tax_details as $tax_det)
																	<p>{{ $tax_det['tax_label'] }}</p>
																@endforeach
															@endif
														</div>
													@else
														<p class="badge badge-primary">{{ trans('common.free') }}</p>
													@endif
												</div>
												<div class="portfolio-info col-md-2 drop-position">

													@if($CheckOutService->is_shipping_needed)
														<?php
															$shipping_template = $CheckOutService->getItemData($item['item_id'], 'shipping_template', $item['item_type']);
														?>
														@if($shipping_template > 0)
															<?php
																$shipping_companies_details = $CheckOutService->getShippingTemplateDetails($item['cart_id'], $shipping_template, $item['item_id'], $item['item_qty']);
																$shipping_fee = $shipping_company_id_selected = 0;
																if(count($shipping_companies_details) > 0) {
																	$shipping_fee = $shipping_companies_details['shipping_company_fee_selected'];
																	$shipping_company_id_selected = $shipping_companies_details['shipping_company_id_selected'];
																}
																$total_shipping_fee = $shipping_fee;
																if($allow_variation) {
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
															@include('shippingCompanyPopupMin', array('shipping_companies_details' => $shipping_companies_details, 'shipping_template' => $shipping_template, 'cart_id' => $item['cart_id'], 'item_id' => $item['item_id'], 'variation_shipping_price' => $variation_shipping_price, 'allow_variation' => $allow_variation))
														@else
															<p class="badge badge-primary">{{ trans('common.free') }}</p>
														@endif
													@else
														-
													@endif
												</div>
												<div class="portfolio-info col-md-1">
													@if($CheckOutService->getItemData($item['item_id'], 'product_sub_total', $item['item_type']) == 'Yes')
														<strong class="badge badge-primary">{{ trans('common.free') }}</strong>
													@else
														<?php
															$new_sub_total = $CheckOutService->getItemData($item['item_id'], 'product_sub_total', $item['item_type']);
															$new_sub_total += (isset($giftwrap_amount) ) ? $giftwrap_amount : 0;
															$new_sub_total += (isset($variation_shipping_price) && $total_shipping_fee > 0) ? $variation_shipping_price : 0;
															$new_sub_total_currency = $CheckOutService->getItemData($item['item_id'], 'product_price_currency', $item['item_type']);
														?>
														<p>{{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), '', true) }}</p>
														@if($show_currency)
															<p>{{ CUtil::convertAmountToCurrency($new_sub_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</p>
														@endif
													@endif
												</div>

												<div class="portfolio-info col-md-1 new-col-3">
													<div class="action-btn">
														<a href="javascript:void(0);" onclick="return removeCartItem('{{$item['item_id']}}', '{{$item['item_type']}}')" title="{{ trans('common.remove_from_order') }}" class="btn red btn-xs"><i class="fa fa-trash-o"></i></a>
													</div>
												</div>
											</div>
											@if($is_downloadable_product == 'No')
												<?php
													$product_shipping_company_name = $CheckOutService->getItemData($item['item_id'], 'product_shipping_company_name', $item['item_type']);
												?>
												@if($product_shipping_company_name == '')
													<div class="pad10"><p class="text-danger margin-bottom-10">{{ trans('checkOut.item_cannot_be_shipped') }}</p></div>
												@endif
											@endif
										</div>
										<?php
											if(isset($total_shipping_fee) && $total_shipping_fee != '')
												$total_shipping_fee_val += $total_shipping_fee;
										?>
									@endforeach
									<?php
										$sub_total = $CheckOutService->calculateTotalAmount();
										$tax_total = $CheckOutService->calculateTotalTaxAmount();
										//$services_total = $CheckOutService->calculateSelectedServiceAmount();
									?>
								@else
								   <div class="portfolio-block pad10"><p class="alert alert-info no-margin">{{ $no_item_msg }}</p></div>
								@endif
							</div>
							<input id="reloadValue" type="hidden" name="reloadValue" value="" />
						</div>
					</div>
					@if(count($CheckOutService->cart_item_details_arr) > 0)
						<div class="text-right margin-bottom-20 cart-amount">
							<?php
								$total_amount = $CheckOutService->getTotalAmount();
								$total_amount += isset($total_giftwrap_amount) ? $total_giftwrap_amount : 0;
								if(isset($total_shipping_fee_val) && $total_shipping_fee_val != '')
									$total_amount += $total_shipping_fee_val;
								$total_amount_org = CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), '', true, false, true);
								$org_total_formatted = CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), '', true);
								$org_total_formatted_curr = CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), '', true);
							?>
							@if(CUtil::isMember() && $total_amount > 0)
								<span style="display:none" id="org_total_formatted_curr">{{$org_total_formatted_curr}}</span>

								<div class="fonts18 margin-bottom-20">
									<span class="margin-bottom-5">{{ trans('checkOut.order_total') }}:</span>
									<span id="selOrderTotalAmt">
										<span class="margin-bottom-5">{{ CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
										@if($show_currency)
                                        	<br />
											<span class="margin-bottom-5">{{ CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</span>
										@endif
									</span>
								</div>

								<div class="clearfix">
									<ul class="list-inline pull-right">
										<li class="margin-top-8 margin-bottom-5">{{ trans('checkOut.coupon_code_label') }}</li>
										<li class="coup-cod">{{  Form::text('coupon_code', null, array('id' => 'coupon_code', 'class' => 'form-control coupon_code', 'autocomplete' => 'off')); }}
										<label id="coupon_error" class="error text-left">{{{ $errors->first('coupon_code') }}}</label></li>
										<li id="apply_code">
											<a href="javascript:;" onClick="return applyCouponCode()" class="btn btn-sm btn-success">{{ trans('checkOut.checkout_apply') }}</a>
										</li>
										<li id="remove_code" style="display:none">
											<a href="javascript:;" onClick="return removeCouponCode()" class="btn btn-sm btn-danger">{{ trans('checkOut.checkout_remove') }}</a>
											<span id="selCouponCodeStatus" class="text-center"></span>
										</li>
									</ul>
								</div>

								<div class="fonts18" id="discount_price_div" style="display:none">
									<span class="margin-bottom-5">{{ trans('checkOut.discount_amount') }}:</span>
									<span id="dicount_price" class="margin-bottom-5">
										{{ CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
									</span>
									@if($show_currency)
                                        <br />
										<span id="dicount_price_with_currency" class="margin-bottom-5">
											{{ CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}
										</span>
									@endif
								</div>
							@endif

							<div class="fonts18">
								<span>{{ trans('checkOut.grand_total') }}:</span>
								<span id="subtotal_price" class="margin-bottom-5">
									{{ CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
								</span>
								@if($show_currency)
                                    <br />
									<span id="subtotal_price_with_currency" class="margin-bottom-5">
										{{ CUtil::convertAmountToCurrency($total_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}
									</span>
								@endif
							</div>
						</div>

						<div class="text-right">
							{{ Form::hidden('act', '', array('id' => 'act')) }}
							@if(!$CheckOutService->is_purchasing_own_item)
								@if(!$CheckOutService->disable_checkout)
									<button type="submit" name="do_checkout" value="do_checkout" class="btn green btn-sm">
									<i class="fa fa-check"></i> {{ trans('checkOut.proceed_to_pay') }}</button>
								@else
									<button type="submit" name="do_checkout" disabled value="do_checkout" class="btn green btn-sm">
									<i class="fa fa-check"></i> {{ trans('checkOut.proceed_to_pay') }}</button>
								@endif
								@if($CheckOutService->disable_checkout_err_msg != '')
									<p><small class="text-danger">{{ $CheckOutService->disable_checkout_err_msg }}</small></p>
								@endif
							@endif
						</div>
					@endif
				{{ Form::close() }}
			</div>
			<!-- END: CHECKOUT FORM -->
		</div>
	</div>
    <script language="javascript" type="text/javascript">
	    var page_name = "cart_checkout";

		var default_txt = '{{ Lang::get("variations::variations.cart_giftwrap_msg_note")  }}';
		var mes_required = "{{ trans('common.required') }}" ;
		var remove_item_title = '{{ trans('checkOut.remove_item_title') }}';
		var remove_item_confirm_msg = '{{ trans('checkOut.remove_item_confirm_msg') }}';
		var common_yes_lbl = '{{ trans('common.yes') }}';
		var common_cancel_lbl = '{{ trans('common.cancel') }}';
		var cookie_id = '{{ $CheckOutService->cookie_id }}';
		var check_out_url = '{{Url::to("checkout")}}';
		var BASE = "{{ Request::root() }}";
		var delete_order_url = "{{ Url::to('cart/delete-order') }}";
		var update_item_variation_link = "/cart/update-item-variation";
		var update_item_giftwrap = "/cart/update-item-giftwrap";
		var change_shipping_company_url = '{{ URL::action('CartController@getChangeShippingCompany') }}';
		var postCouponUrl = '{{ URL::action('CheckOutController@postCouponDetails') }}';
		@if(isset($total_amount_org['amt']) && $total_amount_org['amt']>0)
			var remove_coupon_total_amount = '{{ $org_total_formatted }}';
			var apply_coupon_total_amount = '{{ $total_amount_org['amt'] }}';
		@else
			var remove_coupon_total_amount = 0;
			var apply_coupon_total_amount = 0;
		@endif
	</script>
@stop

@section('script_content')
    <script language="javascript" type="text/javascript">
    	var item_owner_id = $('#item_owner_id').val();

	</script>
@stop