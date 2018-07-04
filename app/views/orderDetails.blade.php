@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				@if(Input::has('i') && Input::get('i') == 'invoice')
					<?php $status = Input::get('s'); ?>
					<a href="{{ URL::to('invoice').'?status='.$status }}" class="pull-right btn btn-xs blue-stripe default">
						<i class="fa fa-chevron-left"></i> {{ trans('myInvoice.my_invoice_list') }}
					</a>
				@else
					<a href="{{ URL::action('PurchasesController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
						<i class="fa fa-chevron-left"></i> {{ trans('myPurchases.my_purchases_list') }}
					</a>
				@endif
				<h1>{{ Lang::get('myPurchases.order_detail') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: INFO BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: INFO BLOCK -->

			<!-- ALERT sudopay STARTS -->
			<div class="alert alert-danger" style="display:none;">
				<span id="buyer_fees_fourmula_disp"></span>
			</div>
			<!-- ALERT sudopay ENDS -->

			<!-- BEGIN: ORDER DETAILS -->
			<div class="well">
				@if(count($order_details) <= 0)
					<div class="note note-info margin-0">
					   {{ Lang::get('myPurchases.invalid_id') }}
					</div>
				@else
					@if(count($order_details) > 0)
						{{ Form::open(array('action' => array('PurchasesController@getIndex'), 'id'=>'purchaseFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
							<?php
								$shipping_address = (isset($order_details[0]->shipping_details[0]->shipping_address_arr) && !empty($order_details[0]->shipping_details[0]->shipping_address_arr))?$order_details[0]->shipping_details[0]->shipping_address_arr:(isset($order_details[0]->shipping_details[0]->shipping_address[0])?$order_details[0]->shipping_details[0]->shipping_address[0]->toArray():array());
								$billing_address = (isset($order_details[0]->shipping_details[0]->billing_address_arr) && !empty($order_details[0]->shipping_details[0]->billing_address_arr))?$order_details[0]->shipping_details[0]->billing_address_arr:(isset($order_details[0]->shipping_details[0]->billing_address[0])?$order_details[0]->shipping_details[0]->billing_address[0]->toArray():array());
								$allowed_keys=array('address_line1'=>'','address_line2'=>'','street'=>'','city'=>'','state'=>'','country'=>'','zip_code'=>'');

								$shipping_address = (is_array($shipping_address) && !empty($shipping_address))?((CUtil::isAssociativeArr($shipping_address))?array_intersect_key($shipping_address, $allowed_keys):$shipping_address):array();
								$billing_address = (is_array($billing_address) && !empty($billing_address))?((CUtil::isAssociativeArr($billing_address))?array_intersect_key($billing_address, $allowed_keys):$billing_address):array();
							?>
							<div class="row">
								<div class="@if(!empty($shipping_address)) || (!empty($billing_address)) col-md-6 member-paymtstatus pull-right @endif">
									<div class="@if(!empty($shipping_address)) || (!empty($billing_address)) portlet bg-form @endif">
										@if(is_array($shipping_address) && !empty($shipping_address))
											<div class="margin-bottom-30">
												<h2 class="title-one">{{trans('myPurchases.shipping_address')}}</h2>
												@foreach($shipping_address as $key=> $ship_addr)
													<p>{{ $ship_addr }}</p>
												@endforeach
											</div>
										@endif

										@if(is_array($billing_address) && !empty($shipping_address))
											<h2 class="title-one">{{ trans('checkOut.billing_address') }}</h2>
											@foreach($billing_address as $key=> $bill_addr)
												<p>{{ $bill_addr }}</p>
											@endforeach
										@endif
									</div>
								</div>

								<div class="@if(!empty($shipping_address)) || (!empty($billing_address)) col-md-6 @else orderdetil-product @endif">
									<div class="member-paymtstatus">
										<div class="portlet bg-form">
											<div class="margin-bottom-40">
												<h2 class="title-one">{{ Lang::get('myPurchases.order_detail') }}</h2>
												<div class="dl-horizontal">
													<dl>
														<dt>{{ trans('myPurchases.order_id') }}</dt>
														<dd><span>{{  CUtil::setOrderCode($order_details[0]->id); }}</span></dd>
													</dl>

													@if(!is_null($order_details[0]->coupon_code) && $order_details[0]->coupon_code!='')
														<dl>
															<dt>{{ trans('coupon.coupon_code') }}</dt>
															<dd><span>{{  $order_details[0]->coupon_code }}</span></dd>
														</dl>
													@endif

													@if(!is_null($order_details[0]->discount_amount) && $order_details[0]->discount_amount > 0)
														<dl>
															<dt>{{ Lang::get('myPurchases.sub_total') }}</dt>
															<dd>
																<span>
																	{{ CUtil::convertAmountToCurrency($order_details[0]->sub_total, Config::get('generalConfig.site_default_currency'), '', true) }}
																</span>
															</dd>
														</dl>
														<dl>
															<dt>{{ Lang::get('myPurchases.discount_amount') }}</dt>
															<dd>
																<span>
																	{{ CUtil::convertAmountToCurrency($order_details[0]->discount_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
																</span>
															</dd>
														</dl>
													@endif
													<dl>
														<dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
														<dd><span>{{ CUtil::convertAmountToCurrency($order_details[0]->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
													</dl>
												</div>
											</div>

                                            @if(isset($order_details[0]['seller_id']) && $order_details[0]['seller_id'] >0)
                                            	<div class="margin-top-30">
                                                    <h2 class="title-one">{{ Lang::get('myPurchases.shop_details') }}</h2>
                                                    <div class="dl-horizontal">
                                                        @if(isset($order_details[0]->shop_details) && !empty($order_details[0]->shop_details))
                                                            <dl>
                                                                <dt>{{ trans('myPurchases.shop_name') }}</dt>
                                                                <dd><span><a href="{{$order_details[0]->shop_details['shop_url']}}" class="text-primary">
                                                                {{ $order_details[0]->shop_details['shop_name'] }}</a></span></dd>
                                                            </dl>
                                                        @endif

                                                        <dl>
                                                            <dt>{{ trans('myPurchases.seller') }}</dt>
                                                            <dd>
                                                                <span>
                                                                    <?php $user_details = CUtil::getUserDetails($order_details[0]['seller_id']); ?>
                                                                    <a title="{{$user_details['display_name']}}" href="{{$user_details['profile_url']}}">{{$user_details['display_name']}}</a>
                                                                </span>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                            @endif
										</div>
									</div>

									<div class="member-paymtstatus">
										<div class="portlet bg-form">
											<h2 class="title-one">{{trans('myPurchases.payment_status')}}</h2>
											<?php
												$order_status_paid = ucfirst($order_details[0]->order_status);
												$lbl_class = 'label-default';

												if($order_details[0]->order_status == 'payment_completed') {
													$order_status_paid = 'Paid';
													$lbl_class = "label-success";
												}
												elseif($order_details[0]->order_status == 'pending_payment' || $order_details[0]->order_status == 'not_paid') {
													$order_status_paid = 'Unpaid';
													$lbl_class = "label-danger";
												}
												elseif($order_details[0]->order_status == 'payment_cancelled') {
													$order_status_paid = trans('myPurchases.status_txt_payment_cancelled');
													$lbl_class = "label-danger";
												}
												else
												{
													$order_status_paid = trans('myPurchases.status_txt_'.$order_details[0]->order_status);
												}
											?>

											<div class="dl-horizontal">
												<dl><dt>{{ trans('myPurchases.status') }}</dt> <dd><span><span class="label {{ $lbl_class }}">{{ $order_status_paid }}</span></span></dd></dl>
												@if($order_details[0]->date_created != '0000-00-00 00:00:00')
													<dl><dt>{{ trans('myPurchases.date_added') }}</dt>
													<dd><span>{{ CUtil::FMTDate($order_details[0]->date_created, "Y-m-d H:i:s", "") }}</span></dd></dl>
												@else
													<dl><dt>{{ trans('myInvoice.date_added') }}</dt> <dd><span>-</span></dd></dl>
												@endif

												@if($order_status_paid == 'Paid')
													@if($order_details[0]->date_created != '0000-00-00 00:00:00')
														<dl><dt>{{ trans('myPurchases.paid_date') }}</dt>
														<dd><span>{{ CUtil::FMTDate($order_details[0]->date_created, "Y-m-d H:i:s", "") }}</span></dd></dl>
													@else
														<dl><dt>{{ trans('myPurchases.paid_date') }}</dt> <dd><span>-</span></dd></dl>
													@endif
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>

							<?php
								foreach($order_details as $order){
										$product_status = false;
									foreach($order['order_invoices'] as $invoice_detail){
										if($invoice_detail['product_details']['product_status'] == 'Deleted'){
											$product_status = true;
											break;
										}
									}
								}
							?>
							@if($order_status_paid == 'Unpaid' && $product_status != true)
								<?php
									$user_account_balance = CUtil::getUserAccountBalance(BasicCUtil::getLoggedUserId());
									$amount = 0.00;
									$currency = 'USD';
									if(count($common_invoice_details) > 0) {
										$currency = $common_invoice_details["currency"];
										$amount = $common_invoice_details["amount"];
									}
								?>
								@if(CUtil::chkIsAllowedModule('sudopay') && Config::get('plugin.sudopay_payment') && Config::get('plugin.sudopay_payment_used_product_purchase'))
									<div class="portlet bg-form">
										<h2 class="title-one">{{ trans('payCheckOut.pay_through_label') }}</h2>
										<?php
											if($d_arr['sudopay_brand'] == 'SudoPay Branding') {
												echo $d_arr['sc']->displayJSBtn($d_arr['sudopay_fields_arr'], false, 'sudopaybtn', true);
											}
											else {
												//if($gateways_arr = $d_arr['sc']->checkGateways('Marketplace-Capture')) {
												if($gateways_arr = $d_arr['sc']->checkGateways('')) {
													//echo $d_arr['sc']->displayGatewaysNew('Marketplace-Capture', false, 'No', $gateways_arr);
													echo $d_arr['sc']->displayGatewaysNew('', false, 'No', $gateways_arr);
												}											
											}
											$discounted_amount_cal = CUtil::convertAmountToCurrency($amount, Config::get('generalConfig.site_default_currency'), '', false, false, true);
										?>
										{{ Form::hidden('sudopay_fees_payer', $d_arr['sudopay_fees_payer'], array('id' => 'sudopay_fees_payer')) }}
										{{ Form::hidden('parent_gateway_id', '', array('id' => 'parent_gateway_id')) }}
									</div>
								@endif
								<?php
									$total_discounted_amount = CUtil::convertAmountToCurrency($order_details[0]->total_amount, Config::get('generalConfig.site_default_currency'), '', false, false, true);
									$user_balance = CUtil::convertAmountToCurrency($user_account_balance['amount'], Config::get('generalConfig.site_default_currency'), '', false, false, true);
									$remaining_amount = $total_discounted_amount['amt'] - $user_balance['amt'];
								?>

								@if(Config::get('payment.wallet_payment') && Config::get('payment.wallet_payment_used_product_purchase'))
                                <div class="row">
									<div class="col-md-12">
										<div class="portlet bg-form">
											<h2 class="title-one">{{ trans('payCheckOut.wallet_payment_label') }} ({{trans('payCheckOut.account_balance')}} {{ $user_balance['currency_symbol']}} <strong>{{ $user_balance['amt'] }}</strong>)</h2>
											{{ Form::hidden('wallet_payment', $user_balance['amt'], array('id' => 'wallet_payment')) }}
											<!-- DUMMY PAYMENT START -->
											<div id="dummy" class="tab-pane">
												<dl class="dl-horizontal-new">
													<dt><div class="btn default payment-btn"><i class="fa fa-money"></i></div></dt>
													<dd class="note note-info">
														<p class="margin-bottom-5"><strong>{{ trans('payCheckOut.paypal_payment_instructions') }}:</strong></p>
														@if($user_account_balance['amount'] > $order_details[0]->total_amount)
															<p class="no-margin">{{ trans('payCheckOut.dummy_payment_instructions_msg') }}</p>
														@else
															<p class="no-margin">{{ trans('payCheckOut.wallet_payment_instructions_insufficient_amount') }}</p>
														@endif
													</dd>
												</dl>
												<div class="paypal-btn">
													@if($user_account_balance['amount'] > $order_details[0]->total_amount)
														<button type="button" name="" value="Pay via Wallet" class="btn green" onclick="proceedPayment('USD', 'wallet', 'No');">
														<i class="fa fa-shopping-cart"></i> {{ trans('payCheckOut.buy_now') }}
														</button>
													@elseif(CUtil::chkIsAllowedModule('sudopay') && $user_account_balance['amount'] != 0)
														<button type="button" name="" value="{{ trans('payCheckOut.pay_via_paypal') }}" class="btn green" onclick="proceedPayment('USD', 'sudopay', 'Yes');">
														<i class="fa fa-shopping-cart"></i> {{ Lang::get('common.pay')}} {{ $total_discounted_amount['currency_symbol'] }} <span id="wallet_amount_disp"></span> {{ Lang::get('common.using')}} <span id="wallet_gateway_name_disp"></span>
														</button>
													@else
														<button type="button" name="" value="Pay via Wallet" disabled="disabled" class="btn green">
														<i class="fa fa-shopping-cart"></i> {{ trans('payCheckOut.buy_now') }}
														</button>
													@endif
												</div>
											</div>
											<!-- DUMMY PAYMENT END -->
										</div>
									</div>
                                </div>
								@endif
							@endif

							<div class="table-responsive margin-bottom-30 responsive-xscroll">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th class="col-md-2">{{ Lang::get('myPurchases.product') }}</th>
											<th class="col-md-3">{{ Lang::get('myPurchases.product_details') }}</th>
											<th class="col-md-2">{{ Lang::get('myPurchases.shipping_details') }}</th>
											<th class="col-md-2">{{ Lang::get('myPurchases.payment_status') }}</th>
										</tr>
									</thead>
									<tbody>
										@if(count($order_details) > 0)
                                        	<?php
												$allow_deal_block = $allow_variations_block = 0;
                                        		if(CUtil::chkIsAllowedModule('deals'))
												{
													$deal_service = new DealsService();
													$allow_deal_block = 1;
												}
												if(CUtil::chkIsAllowedModule('variations'))
												{
													$variation_service = new VariationsService();
													$allow_variations_block = 1;
												}
											?>
											@foreach($order_details as $order)
												@if(isset($order['order_invoices']) && !empty($order['order_invoices']))
													@foreach($order['order_invoices'] as $orderKey => $invoice_detail)
														<?php
															$product = $invoice_detail['product_details'];
															if(!empty($product))
															{
																$p_img_arr = $product_obj->getProductImage($product['id']);
																$p_thumb_img = $productService->getProductDefaultThumbImage($product['id'], 'small', $p_img_arr);
																$view_url = $productService->getProductViewURLNew($product['id'], $product);
															}
															//$shipping_status_value = $order_obj->getOrderitems($invoice_detail['item_id'], $invoice_detail['order_id']);
															$deal_available = 0;
															if($allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0)
															{
																$deal_available = 1;
																$deal_details = $deal_service->fetchDealDetailsById($invoice_detail['deal_id']);
															}
															$matrix_det = isset($order['order_items'][$orderKey]) ? $order['order_items'][$orderKey] : array();
															$variations_det_arr = array();
															if($allow_variations_block && isset($matrix_det->matrix_id) && $matrix_det->matrix_id > 0)
															{
																$variations_det_arr = $variation_service->populateVariationAttributesByMatrixId($product['id'], $matrix_det->matrix_id, $invoice_detail['item_owner_id']);
																if(isset($variations_det_arr['swap_img_src']) && $variations_det_arr['swap_img_src'] != "")
																{
																	$p_thumb_img['image_url'] = $variations_det_arr['swap_img_src'];
																}
															}
															//$edit_url = URL::to('products/add?id='.$product->id);
															//print_r($invoice_detail);exit;
														?>
														<tr>
															<td>
                                                            	<div class="wid-220">
																	<!-- BEGIN: DEALS BLOCK -->
																	@if ($deal_available == 1 && isset($deal_details) && COUNT($deal_details) > 0)
																		<a href="{{ $deal_details['viewDealLink'] }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" target="_blank" class="label label-primary pull-right fonts12"><i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}</a>
																	@endif
																	<!-- END: DEALS BLOCK -->

																	<div class="custom-feature">
																		@if(!empty($product))
																		<div class="dl-horizontal dl-horizontal-new">
																			@if($product['product_status'] != 'Deleted')
																				<figure>
																					<a href="{{$view_url}}" class="imgsize-75X75">
																						<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ nl2br($product['product_name'])  }}}" alt="{{{nl2br($product['product_name'])}}}" />
																						{{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
																					</a>
																				</figure>
																				<dl>
																					<dt>{{ Lang::get('product.product_code') }}</dt>
																					<dd><span><a href="{{$view_url}}">{{ $product['product_code'] }}</a></span></dd>
																				</dl>
																			@else
																				<figure>
																					<a href="javascript:void(0);" class="imgsize-75X75">
																						<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ nl2br($product['product_name'])  }}}" alt="{{{nl2br($product['product_name'])}}}" />
																						{{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
																					</a>
																				</figure>
																				<dl>
																					<dt>{{ Lang::get('product.product_code') }}</dt>
																					<dd><span>{{ $product['product_code'] }}</span></dd>
																				</dl>
																				<dl>
																					<dt>{{ trans('productAdd.product_status') }}</dt>
																					<dd><span><strong>{{ $product['product_status'] }}</strong></span></dd>
																				</dl>
																			@endif
																		</div>
																		@else
																			<p class="label label-danger">{{ Lang::get('product.product_deleted') }}</p>
																		@endif
																	</div>

																	<!-- BEGIN: DEALS BLOCK -->
																	@if ($deal_available == 1 && isset($deal_details) && COUNT($deal_details) > 0)
																		<div class="dl-horizontal dl-horizontal-new">
																			<dl>
																				<dt>{{ Lang::get('deals::deals.deals_label') }}</dt>
																				<dd>
																					<span>
																						<a href="{{ $deal_details['viewDealLink'] }}" title="{{ $deal_details['deal_title'] }}">
																							{{ Str::limit($deal_details['deal_title'] , 13) }}
																						</a>
																					</span>
																				</dd>
																			</dl>

																			<dl>
																				<dt>{{ Lang::get('deals::deals.deal_discount') }}</dt>
																				<dd><span class="font-green">{{ $deal_details['discount_percentage'] }}%</span></dd>
																			</dl>

                                                                            @if(isset($invoice_detail['deal_tipping_status']) && $invoice_detail['deal_tipping_status'] != ""
                                                                            	&& isset($deal_details['tipping_qty']) && $deal_details['tipping_qty'] > 0)
																				<dl>
																					<dt>{{ Lang::get('deals::deals.tipping_status_lbl') }}</dt>
																					<dd>
																						<span>
																							@if($deal_details['deal_tipping_status'] == '')
																								<strong class="font-red">{{ Lang::get('deals::deals.notstarted_tipping_lbl') }}</strong>
																							@elseif($deal_details['deal_tipping_status'] == 'pending_tipping')
																								<strong class="text-warning">{{ Lang::get('deals::deals.pending_tipping_lbl') }}</strong>
																							@elseif($deal_details['deal_tipping_status'] == 'tipping_reached')
																								<strong class="text-success">{{ Lang::get('deals::deals.tipping_reached_lbl') }}</strong>
																							@elseif($deal_details['deal_tipping_status'] == 'tipping_failed')
																								<strong class="text-danger">{{ Lang::get('deals::deals.tipping_failed_lbl') }}</strong>
																							@endif
																						</span>
																				   </dd>
																			   </dl>
																			@endif
																		</div>
																	@endif
																<!-- END: DEALS BLOCK -->

                                                                	<!-- BEGIN: VARIATIONS BLOCK -->
                                                                    <div class="dl-horizontal dl-horizontal-new">                                                                        @if(isset($variations_det_arr['attrirb_det']) && COUNT($variations_det_arr['attrirb_det']) > 0)
                                                                            @foreach($variations_det_arr['attrirb_det'] AS $attr)
                                                                            <dl>
                                                                                <dt>{{ $attr['name'] }}:</dt> <dd><span> {{ $attr['label'] }}</span></dd>
                                                                             </dl>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                	<!-- END: VARIATIONS BLOCK -->
																</div>
															</td>

															<td>
																<?php
																	$allow_to_download = 0;
																	$allow_to_download = (!empty($product) && $product['is_downloadable_product'] == 'Yes')?1:0;
																	if($allow_to_download && $allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0)
																	{
																		$allow_to_download = $deal_service->allowToDownloadDealItem($invoice_detail['deal_id'], $product['id']);
																	}
																 ?>
																<div class="dl-horizontal dl-horizontal-new wid-330">
																	<dl>
																		@if(!empty($product))
																			<dt>{{ trans('myPurchases.product') }}</dt>
																			@if($product['product_status'] != 'Deleted')
																				<dd><span><a href="{{$view_url}}" class="text-primary">{{{ nl2br($product['product_name'])}}}</a></span></dd>
																			@else
																				<dd><span>{{{ nl2br($product['product_name']) }}}</span></dd>
																			@endif
																		@else
																			<dt>{{ trans('myPurchases.product') }}</dt>
																			<dd><span>{{"Product deleted"}}</span></dd>
																		@endif
																	</dl>

																	@if($allow_to_download)
																		<dl>
																			<?php $download_url = URL::action('PurchasesController@getInvoiceAction'). '?action=download_file&invoice_id='.$invoice_detail['id']; ?>
																			<dt>{{ trans('myPurchases.download_link') }}</dt>
																			<dd><span><a href="{{$download_url}}" class="text-primary">{{ trans('myPurchases.download') }}</a></span></dd>
																		</dl>
																	@endif

																	<dl>
																		<dt>{{ Lang::get('myPurchases.item_amount') }}</dt>
																		<dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['item_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
																	</dl>

																	<dl>
																		<dt>{{ Lang::get('myPurchases.quantity') }}</dt>
																		<dd><span>{{ $invoice_detail['item_qty'] }}</span></dd>
																	</dl>

																	@if($invoice_detail['shipping_company'])
																		<dl>
																			<dt title="{{ Lang::get('myPurchases.shipping_company') }}">{{ Lang::get('myPurchases.shipping_company') }}</dt>
																			<dd><span>{{ CUtil::getSippingCompanyName($invoice_detail['shipping_company']); }}</span></dd>
																		</dl>
																	@endif

																	<dl>
																		<dt>{{ Lang::get('myPurchases.shipping_fee') }}</dt>
																		<dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
																	</dl>

																	@if(isset($invoice_detail['tax_ids']) && $invoice_detail['tax_ids'])
																		<?php
																		   $order_item_tax_split_arr = array();
																				$order_item_tax_split_arr = explode(",", $invoice_detail['tax_ids']);
																				$order_item_amounts_split_arr = explode(",", $invoice_detail['tax_amounts']);
																				foreach($order_item_tax_split_arr as $inc => $tax_id) {
																					$amount = isset($order_item_amounts_split_arr[$inc]) ? $order_item_amounts_split_arr[$inc] : 0;
																					$tax_info = Webshoptaxation::Taxations()->getTaxations(array('id' => $tax_id), 'first', array('include_deleted' => true));
																					echo '<dl><dt>'.(isset($tax_info['tax_name']) ? $tax_info['tax_name'] : '').': </dt><dd><span>'.CUtil::convertAmountToCurrency($amount, Config::get('generalConfig.site_default_currency'), '', true).'</span></dd></dl>';
																				}
																		?>
																	@endif

                                                                    @if($allow_variations_block && isset($invoice_detail['giftwrap_price']) && $invoice_detail['giftwrap_price'])
                                                                    	<dl>
                                                                            <dt>{{ Lang::get('variations::variations.giftwrap_fee_lbl') }}</dt>
                                                                            <dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['giftwrap_price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                        </dl>
                                                                    @endif

																	<dl>
																		@if($order->has_invoice)
																			<dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
																			<dd>
																				<span>
																					{{ CUtil::convertAmountToCurrency($invoice_detail['item_total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}
																				</span>
																			</dd>
																		@else
																			<dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
																			<dd>
																				<span>{{ CUtil::convertAmountToCurrency($invoice_detail['total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span>
																			</dd>
																		@endif
																	</dl>

																	@if(isset($invoice_detail['discount_ratio_amount']) && $invoice_detail['discount_ratio_amount']>0)
																		<dl>
																			<dt>{{ Lang::get('myPurchases.discount_ratio') }}</dt>
																			<dd>
																				<span>
																					{{ CUtil::convertAmountToCurrency($invoice_detail['discount_ratio_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}
																				</span>
																			</dd>
																		</dl>
																	@endif
																</div>
															</td>

															<td class="sales-odetails">
																@if($product['is_downloadable_product'] == 'No')
																	<?php
																		$s_status = '';

																		if($invoice_detail['shipping_status'] == 'shipped') {
                                                                            $lbl_class = "label-success";
                                                                            $s_status = trans('myPurchases.shipped');
                                                                        }
                                                                        elseif($invoice_detail['shipping_status'] == 'delivered') {
                                                                            $lbl_class = "label-success";
                                                                            $s_status = trans('myPurchases.shipped');
                                                                        }
                                                                        elseif($invoice_detail['shipping_status'] == 'not_shipping') {
                                                                            $lbl_class = "label-warning";
                                                                            $s_status = trans('myPurchases.not_shipped');
                                                                        }
																	?>

																	<p class="margin-bottom-10"><span class="label {{ $lbl_class }}">{{ $s_status }}</span></p>

																	@if($invoice_detail['shipping_status'] != 'not_shipping')
                                                                        <p class="text-muted">{{ Lang::get('myPurchases.tracking_id') }}</p>
                                                                        <p class="margin-bottom-15">{{ $invoice_detail['shipping_tracking_id'] }}</p>
                                                                        <?php
                                                                            $serial_number = explode("\r\n", $invoice_detail['shipping_serial_number']);
                                                                            $shipping_serial_number = implode(",", $serial_number);
                                                                        ?>
                                                                        @if($shipping_serial_number != '')
                                                                            <p class="text-muted">{{ Lang::get('myPurchases.serial_numbers') }}</p>
                                                                            <p class="margin-bottom-15">{{ $shipping_serial_number }}</p>
                                                                        @endif
                                                                        <p class="text-muted">{{ Lang::get('myPurchases.shipping_date') }}</p>
                                                                        <p>{{ CUtil::FMTDate($invoice_detail['shipping_date'], "Y-m-d H:i:s", "") }}</p>
                                                                        @if($invoice_detail['shipping_status'] == 'delivered')
                                                                            <p class="text-muted margin-top-10">{{trans('myPurchases.delivered_date')}}</p>
                                                                            <p>{{ CUtil::FMTDate($invoice_detail['delivered_date'], "Y-m-d H:i:s", "") }}</p>
                                                                        @endif
                                                                    @endif
																@else
																	<span title="{{ trans('myPurchases.not_applicable_for_download_prod') }}">{{ trans('common.not_applicable') }}</span>
																@endif
															</td>

															@if($order->has_invoice)
                                                            	<td>
                                                                <div class="wid-330">
                                                                    <?php
                                                                        if(count($invoice_detail) > 0)
                                                                        {
                                                                            if($invoice_detail['invoice_status'] == 'completed') {
                                                                                $lbl_class = "label-success";
                                                                            }
                                                                            elseif($invoice_detail['invoice_status'] == 'pending') {
                                                                                $lbl_class = "label-warning";
                                                                            }
                                                                            elseif($invoice_detail['invoice_status'] == 'refund_requested') {
                                                                                $lbl_class = "label-warning";
                                                                            }
                                                                            elseif($invoice_detail['invoice_status'] == 'refunded') {
                                                                                $lbl_class = "label-success";
                                                                            }
                                                                            elseif($invoice_detail['invoice_status'] == 'refund_rejected') {
                                                                                $lbl_class = "label-danger";
                                                                            }
                                                                        }
                                                                    ?>
                                                            
                                                                    <p>
                                                                        <span class="label {{ $lbl_class }}">
                                                                        {{ trans('myPurchases.status_txt_'.$invoice_detail['invoice_status']) }}</span>
                                                                    </p>
                                                            
                                                                    @if($invoice_detail['item_total_amount'] > 0 && ($invoice_detail['item_total_amount'] - $invoice_detail['discount_ratio_amount']) > 0){{--strtolower($order['payment_gateway_type'])!='paypal' && --}}
                                                                        @if($invoice_detail['is_refund_requested'] == "Yes")
                                                                            <?php $refund_status = 'Pending';?>
                                                                            @if($invoice_detail['is_refund_approved_by_admin'] == 'Yes')
                                                                                <?php $refund_status = 'Accepted'; $lbl_class = "label-success";
                                                                                        $refunde_amount = $invoice_detail['refunded_amount_by_admin'] + $invoice_detail['refund_amount_by_seller'];
                                                                                 ?>
                                                                            @elseif($invoice_detail['is_refund_approved_by_seller'] == 'Rejected')
                                                                                <?php $refund_status = 'Rejected'; $refund_status_by = 'rejected_by_seller'; $lbl_class = "label-danger"; ?>
                                                                            @elseif($invoice_detail['is_refund_approved_by_admin'] == 'Rejected')   
                                                                                <?php $refund_status = 'Rejected'; $refund_status_by = 'rejected_by_admin'; $lbl_class = "label-danger"; ?>
                                                                            @elseif($invoice_detail['is_refund_approved_by_admin'] == 'No' && $invoice_detail['is_refund_approved_by_seller'] == 'No')
                                                                                <?php $refund_status = 'Pending'; $lbl_class = "label-warning";?>
                                                                            @endif
                                                            
                                                                            <div class="invoice-detail payment-status">
                                                                                <div class="portlet bg-form margin-bottom-15 pad7">
                                                                                    <div class="text-muted margin-bottom-5">
                                                                                        <strong>{{ trans('myPurchases.reason_for_cancellation') }}:</strong>
                                                                                    </div>
                                                                                    @if(isset($invoice_detail['refund_reason']) && $invoice_detail['refund_reason'] != '')
                                                                                        <p class="margin-bottom-5">{{ $invoice_detail['refund_reason'] }}</p>
                                                                                    @else
                                                                                        <p class="margin-bottom-5">{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                    @endif
                                                                                </div>
                                                            
                                                                                <div class="dl-horizontal dl-horizontal-new">
                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.status') }}</dt>
                                                                                        <dd>
                                                                                            <span class="label {{ $lbl_class }}">
                                                                                                {{ trans('myPurchases.'.strtolower($refund_status)) }}
                                                                                            </span>
                                                                                        </dd>
                                                                                    </dl>
                                                                                    <?php
                                                                                        if(strtolower($invoice_detail['is_refund_approved_by_seller']) == "yes")
                                                                                        {
                                                                                            $seller_status = trans('myPurchases.accepted_by_seller');
                                                                                            $seller_notes = $invoice_detail['refund_response_by_seller'];
                                                                                            $lbl_class = "label-success";
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_seller']) == "rejected")
                                                                                        {
                                                                                            $seller_status = trans('myPurchases.rejected_by_seller');
                                                                                            $seller_notes = $invoice_detail['refund_response_by_seller'];
                                                                                            $lbl_class = "label-danger";
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $seller_status = trans('myPurchases.status_txt_pending');
                                                                                            $seller_notes = $invoice_detail['refund_response_by_seller'];
                                                                                            $lbl_class = "label-warning";
                                                                                        }
                                                                                        $seller_refunde_amount = $invoice_detail['refund_amount_by_seller']+$invoice_detail['refund_paypal_amount_by_seller'];
                                                                                    ?>
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_seller']) != "no")
                                                                                        <dl>
                                                                                            <dt>{{ trans('myPurchases.seller_refund_status') }}</dt>
                                                                                            <dd><span class="label {{ $lbl_class }}">{{ $seller_status }}</span></dd>
                                                                                        </dl>
                                                                                    @endif
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        <dl>
                                                                                            <dt>{{ trans('myPurchases.amount_credited_to_your_account') }}</dt>
                                                                                            <dd>
                                                                                                <span>
                                                                                                    {{ CUtil::convertAmountToCurrency($seller_refunde_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
                                                                                                </span>
                                                                                            </dd>
                                                                                        </dl>
                                                                                    @endif
                                                            
                                                                                    <div class="portlet bg-form margin-bottom-15 pad7">
                                                                                        <div class="text-muted margin-bottom-5">
                                                                                            <strong>{{ trans('myPurchases.seller_notes') }}:</strong>
                                                                                        </div>
                                                                                        @if(isset($seller_notes) && $seller_notes != '')
                                                                                            <p class="margin-bottom-5">{{ $seller_notes }}</p>
                                                                                        @else
                                                                                            <p class="margin-bottom-5">{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                        @endif
                                                                                    </div>
                                                            
                                                                                    <?php
                                                                                        if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        {
                                                                                            $commission_status = trans('myPurchases.accepted_by_admin');
                                                                                            $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                            $lbl_class = "label-success";
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_seller']) == "rejected")
                                                                                        {
                                                                                            if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes"){
                                                                                                $commission_status = trans('myPurchases.rejected_by_admin');
                                                                                                $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                                $lbl_class = "label-success";
                                                                                            }else{
                                                                                                $commission_status = trans('myPurchases.rejected_by_seller');
                                                                                                $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                                $lbl_class = "label-danger";
                                                                                            }
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $commission_status = trans('myPurchases.status_txt_pending');
                                                                                            $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                            $lbl_class = "label-warning";
                                                                                        }
                                                                                        $commission_amount = $invoice_detail['refunded_amount_by_admin'];
                                                                                    ?>
                                                            
                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.site_commission_status') }}</dt>
                                                                                        <dd><span class="label {{ $lbl_class }}">{{ $commission_status }}</span></dd>
                                                                                    </dl>
                                                            
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        <dl>
                                                                                            <dt>{{ trans('myPurchases.amount_credited_to_your_account') }}</dt>
                                                                                            <dd>
                                                                                                <span>
                                                                                                    {{ CUtil::convertAmountToCurrency($commission_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
                                                                                                </span>
                                                                                            </dd>
                                                                                        </dl>
                                                                                    @endif
                                                            
                                                                                    <div class="portlet bg-form margin-bottom-15 pad7">
                                                                                        <div class="text-muted margin-bottom-5">
                                                                                            <strong>{{ trans('myPurchases.admin_notes') }}:</strong>
                                                                                        </div>
                                                                                        @if(isset($commission_notes) && $commission_notes != '')
                                                                                            <p class="margin-bottom-5">{{ $commission_notes }}</p>
                                                                                        @else
                                                                                            <p class="margin-bottom-5">{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                        @endif
                                                                                    </div>
                                                            
                                                                                    @if(strtolower($refund_status) != "pending")
                                                                                        <?php
                                                                                            $notes = $invoice_detail['refund_response_to_user_by_admin'];
                                                                                            $notes_label = (strtolower($refund_status) == "accepted") ? 'Transaction Details' : 'Notes';
                                                                                        ?>
                                                            
                                                                                        @if(strtolower($refund_status) == "accepted")
                                                                                            <ul class="list-unstyled">
                                                                                                <li class="text-muted">{{ trans('myPurchases.refund_details') }}:</li>
                                                                                                <li>{{ trans('myPurchases.amount_transfer_via_paypal') }}: {{ CUtil::convertAmountToCurrency($invoice_detail['refund_paypal_amount_by_seller'], Config::get('generalConfig.site_default_currency'), '', true) }}</li>
                                                                                                <li>Amount credited to Wallet: {{ CUtil::convertAmountToCurrency($invoice_detail['refund_amount_by_seller'], Config::get('generalConfig.site_default_currency'), '', true) }}</li>
                                                                                            </ul>
                                                                                        @endif
                                                            
                                                                                        @if(mb_strlen($notes))
                                                                                        <div class="text-muted">{{ $notes_label }}:</div>
                                                                                        @endif
                                                            
                                                                                        <div id="selSubLessNotes_{{ $invoice_detail['id'] }}" >
                                                                                            {{ mb_substr($notes, 0, 90) }}
                                                                                            @if(mb_strlen($notes) > 90)
                                                                                                <p class="text-right">
                                                                                                <a onclick="return callShowMoreNotes('more', '{{ $invoice_detail['id'] }}')">
                                                                                                {{ Lang::get('common.more')}}...</a></p>
                                                                                            @endif
                                                                                        </div>
                                                            
                                                                                        <div id="selSubMoreNotes_{{ $invoice_detail['id'] }}" style="display:none">
                                                                                            {{ $notes }}
                                                                                            <p class="text-right">
                                                                                            <a onclick="return callShowMoreNotes('less', '{{ $invoice_detail['id'] }}')">
                                                                                            ... {{ Lang::get('common.less')}}</a></p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <?php
                                                                                $allow_cancellation = 0;
                                                                                $allow_cancellation = (!empty($product) && $product['use_cancellation_policy'] == 'Yes')?1:0;
                                                                                if($allow_cancellation && $allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0)
                                                                                {
                                                                                    $deal_available = 1;
                                                                                    $deal_details = $deal_service->fetchDealDetailsById($invoice_detail['deal_id']);
                                                                                    $allow_deal_product_cancel = $deal_service->allowedToCancelPurchasedItem($invoice_detail['deal_id'], $invoice_detail['item_qty']);
                                                                                    $allow_cancellation = ($allow_deal_product_cancel) ? 1 : 0;
                                                                                }
                                                                            ?>
                                                            
                                                                            @if($allow_cancellation)
                                                                                <p><a data-invoice="{{$invoice_detail['id']}}" class="js-refund-form btn btn-danger btn-xs">{{trans('myPurchases.cancel_product')}}</a></p>
                                                                                @if(isset($product['cancellation_policy_filename']) && $product['cancellation_policy_filename']!='')
                                                                                    <?php $filePath = $product['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
                                                                                    $filename = $product['cancellation_policy_filename'].'.'.$product['cancellation_policy_filetype'];?>
                                                                                    <p><a target="_blank" href="{{$filePath.'/'.$filename}}" class="btn btn-xs btn-info">{{trans('myPurchases.click_to_view_cancellation_policy')}}</a></p>
                                                                                @elseif(isset($product['cancellation_policy_text']) && $product['cancellation_policy_text']!='')
                                                                                    <p><a href="javascript:;" class="popover-dismiss btn btn-xs btn-info" data-toggle="modal" data-target="#cancelPolicy_{{$product['id']}}">{{trans('myPurchases.click_to_view_cancellation_policy')}}</a></p>
                                                                                    <div class="modal fade" id="cancelPolicy_{{$product['id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                                        <div class="modal-dialog">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                                                                                                    </span><span class="sr-only">{{trans('common.close')}}</span></button>
                                                                                                    <h4 class="modal-title" id="myModalLabel">
                                                                                                    {{Config::get('generalConfig.site_name')}}</h4>
                                                                                                </div>
                                                                                                <div class="modal-body">
                                                                                                    <p>{{nl2br($product['cancellation_policy_text'])}}</p>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <div class="form-group">
                                                                                                        <div class="col-sm-12">
                                                                                                            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">
                                                                                                            <i class="fa fa-times"></i> {{trans('common.close')}}</button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                            
                                                                                <div id="refundreasondiv_{{$invoice_detail['id']}}" style="display:none" class="col-md-12 padding-left-0">
                                                                                    {{Form::textarea('refund_reason_'.$invoice_detail['id'],'',array('rows' => '5', 'id' => 'refund_reason_'.$invoice_detail['id'], 'class' => 'form-control'))}}
                                                                                    <label class="">[{{ trans('common.optional') }}]</label>
                                                                                    <p class="margin-top-10">
                                                                                        <button data-invoice="{{$invoice_detail['id']}}" type="button" class="btn green btn-xs js-request-refund"><i class="fa fa-check"></i> {{trans('common.submit')}}</button>
                                                                                        <button data-invoice="{{$invoice_detail['id']}}" type="button" class="btn default btn-xs js-refund-form"><i class="fa fa-times"></i> {{trans('common.cancel')}}</button>
                                                                                    </p>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </td>
															@else
																<td>
																	<p>
																		<?php
																			$lbl_class = '';
																			$order_status = 'draft';
																			if(isset($order['order_status']) && $order['order_status'] != '')
																			{
																				$order_status = $order['order_status'];
																				if($order_status == 'payment_completed' || $order_status == 'refund_completed')
																					$lbl_class = "label-success";
																				elseif($order_status == 'draft' || $order_status == 'pending_payment' || $order_status == 'not_paid')
																					$lbl_class = "label-warning";
																				elseif($order_status == 'refund_requested')
																					$lbl_class = "label-info";
																				elseif($order_status == 'refund_rejected')
																					$lbl_class = "label-danger";
																				elseif($order_status == 'payment_cancelled')
																					$lbl_class = "label-danger";
																			}
																		?>
																		<span class="label {{ $lbl_class }}">{{ trans('myPurchases.status_txt_'.$order_status) }}</span>
																	</p>
																</td>
															@endif
														</tr>
                                                        @if(isset($invoice_detail['is_use_giftwrap']) && $invoice_detail['is_use_giftwrap'] > 0 && $invoice_detail['giftwrap_msg'] != '')
                                                            <tr>
                                                                <td>{{ Lang::get('variations::variations.giftwrap_msg') }}:</td>
                                                                <td colspan="3">{{ nl2br($invoice_detail['giftwrap_msg']) }}</td>
                                                            </tr>
                                                        @endif
													@endforeach
												@endif
											@endforeach
										@else
											<tr>
												<td colspan="@if(Config::get('generalConfig.user_allow_to_add_product'))4 @else 3 @endif">
													<p class="alert alert-info">{{ Lang::get('myPurchases.no_result') }}</p>
												</td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
						{{ Form::close() }}

						{{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('myproducts/deleteproduct'))) }}
							{{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
							{{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
						{{ Form::close() }}

						<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
							<span class="ui-icon ui-icon-alert"></span>
							<span id="dialog-product-confirm-content" class="show ml15"></span>
						</div>
					@else
						<div class="alert alert-info">
						   {{ Lang::get('product.list_empty') }}
						</div>
					@endif
				@endif
			</div>
			<!-- END: ORDER DETAILS -->
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var pay_via_paypal = "{{ trans('payCheckOut.pay_via_paypal') }}";
		var common_invoice_id_val = "{{ $common_invoice_details["common_invoice_id"] }}";
		var proceedpayment = "{{ Url::to('proceedpayment')}}";
		var payment_gateway_revised_amount_txt = "{{ trans('sudopay::sudopay.payment_gateway_revised_amount') }}";

		var discounted_amount_bf_revise = 0;
		var discounted_currency_bf_revise = '';
		var amount = 0;
		@if(isset($order_status_paid) && $order_status_paid == 'Unpaid' && $product_status != true)
			var discounted_amount_bf_revise = "{{ $discounted_amount_cal['amt'] }}";
			var discounted_currency_bf_revise = "{{ $discounted_amount_cal['currency_symbol'] }}";
			var amount = "{{ $amount }}";
		@endif

        var ajax_proceed = 0;
	      $('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.show_search_filters') }} <i class="fa fa-caret-down"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.hide_search_filters') }} <i class="fa fa-caret-up"></i>');
	        }
	        return false;
	    });

	    function doAction(p_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myProducts.product_confirm_delete') }}');
			}
			else if(selected_action == 'feature')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myProducts.product_confirm_featured') }}');
			}
			else if(selected_action == 'unfeature')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myProducts.product_confirm_unfeatured') }}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('myProducts.my_products_title') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#product_action').val(selected_action);
						$('#p_id').val(p_id);
						document.getElementById("productsActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}

        $(".js-refund-form").click(function(){
            var invoice_id = $(this).data('invoice');
            var div_id = 'refundreasondiv_'+invoice_id;
            $('#'+div_id).toggle(500);
        });

        $(".js-request-refund").click(function(){

            var invoice_id = $(this).data('invoice');
            var refund_reason = $('#refund_reason_'+invoice_id).val();

            var div_id = 'refundreasondiv_'+invoice_id;

            if(ajax_proceed)
                ajax_proceed.abort();

            var params = {"invoice_id": invoice_id, "refund_reason": refund_reason };
            ajax_proceed = $.post("{{ Url::action('PurchasesController@postRequestCancel')}}", params, function(data) {
                if(data) {
                    var data_arr = data.split("|~~|");
                    if(data_arr.length > 1) {
                        if(data_arr[0] == "success") {
                            $('#'+div_id).append('<p class="alert alert-success">'+data_arr[1]+'</p>');
                            window.location.reload();
                        }
                        else if(data_arr[0] == "error")
                        {
                            $('#'+div_id).append('<p class="alert alert-danger">'+data_arr[1]+'</p>');
                        }
                        else
                        {
                            window.location.reload();
                        }
                    }
                    else {
                        window.location.reload();
                    }
                }
            })

        });

        $(document).ready(function() {
			$("#purchaseFrm").validate({
				rules: {
					credit_card_number: {
						required: true
					},
					credit_card_expire: {
						required: true
					},
					credit_card_name_on_card: {
						required: true
					},
					credit_card_code: {
						required: true
					},
				},
				messages: {
					credit_card_number: {
						required: mes_required,
					},
					credit_card_expire: {
						required: mes_required,
					},
					credit_card_name_on_card: {
						required: mes_required,
					},
					credit_card_code: {
						required: mes_required,
					},
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
			});
		});

		$(".fn_changeStatus").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });

	$('.popover-dismiss').popover({
		  trigger: 'focus'
		})

		function proceedPayment(payment_mode, payment_gateway_chosen, is_credit)
		{
			var parent_gateway_id = $('#parent_gateway_id').val();
			var valid = true;
			var wallet_payment = $('#wallet_payment').val();
			var total_discounted_amount = $('#total_discounted_amount').val();

			if(parent_gateway_id == ''){
				parent_gateway_id = 4922;
			}

			if(parent_gateway_id == 4922 && payment_gateway_chosen != 'wallet') {
				var valid = $('#purchaseFrm').valid();
			}

			if(valid == false) {
				return false;
			} else {
				if(parent_gateway_id == 4922 && payment_gateway_chosen != 'wallet') {
					var response = cardValidation();
				}
				if(response == false) {
					return false;
				} else {
					var gateway_id = 0;
					if(parent_gateway_id == 4922 || parent_gateway_id == 5333) {
						if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0)
							gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();
					}

					if(ajax_proceed)
					{
						ajax_proceed.abort();
						displayLoadingImage(false);
					}
					var currency_code = payment_mode;

					var d_arr = [];
			        sudopay_arr = {};
			        sudopay_arr['sudopay_fees_payer'] = $("#sudopay_fees_payer").val();
			        sudopay_arr['credit_card_number'] = $("#credit_card_number").val();
			        sudopay_arr['credit_card_expire'] = $("#credit_card_expire").val();
			        sudopay_arr['parent_gateway_id'] = parent_gateway_id;
			        sudopay_arr['credit_card_name_on_card'] = $("#credit_card_name_on_card").val();
			        sudopay_arr['credit_card_code'] = $("#credit_card_code").val();
			        sudopay_arr['gateway_id'] = gateway_id;
			        d_arr.push(JSON.stringify(sudopay_arr));
					console.log(d_arr);

					var params = {"common_invoice_id": common_invoice_id_val, "payment_gateway_chosen": payment_gateway_chosen, "currency_code": currency_code, "is_credit": is_credit, "d_arr[]": d_arr };
					displayLoadingImage(true);
					ajax_proceed = $.post(proceedpayment, params, function(data) {
						if(data) {
							var data_arr = data.split("|~~|");
							if(data_arr.length > 1) {
								window.location.href = data_arr[0];
							}
							else {
								displayLoadingImage(false);
								$("#paypal_form").html(data);
								document.getElementById("frmTransaction").submit();
							}
						}
					});
				}
			}
		}

	$(document).ready(function () {
		$("#credit_card_number").attr('maxlength','16');
	    $("#credit_card_expire").attr('maxlength','7');
	    $("#credit_card_code").attr('maxlength','4');

		$("#credit_card_number").keypress(function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				$("#card_error").html("Digits Only").show().fadeOut("slow");
				return false;
			}
		});

		$("#credit_card_code").keypress(function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				$("#card_error").html("Numbers Only").show().fadeOut("slow");
				return false;
			}
		});

		defaultGatewaySetter();
		calcBuyerFeesFormula();
	});

	$("#card_error").hide();

	function defaultGatewaySetter()
	{
		$('.fnGatwayFinder').each(function(){
			if($(this).hasClass("active")) {
				var element_id = $(this).attr("id");
				var data_arr = element_id.split("-");
				if(data_arr.length > 1) {
					$('#parent_gateway_id').val(data_arr[1]);
				}
			}
		});
	}

	function hideCreditCard(id){
		if(id == 5333){
			$('.js-form-tpl-credit_card').hide();
			$('#parent_gateway_id').val(id);

		}else{
			$('.js-form-tpl-credit_card').show();
			$('#parent_gateway_id').val(id);
		}
		calcBuyerFeesFormula();
	}

	function calcBuyerFeesFormula() {
		//4922 = credit card, 5333 = electronics gateways
		var buyer_fees_formula = '';
		var gateway_id = 0;
		var parent_gateway_id = $('#parent_gateway_id').val();
		if(parent_gateway_id == '') {
			parent_gateway_id = 4922;
		}
		if(parent_gateway_id == 4922 || parent_gateway_id == 5333) {
			if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0) {
				gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();
			}
		}

		if(gateway_id > 0) {
			if($('#buyer_fees_formula_' + parent_gateway_id + '_' + gateway_id).length > 0) {
				buyer_fees_formula = $('#buyer_fees_formula_' + parent_gateway_id + '_' + gateway_id).val();
			}
			if($('#wallet_gateway_name_disp').length > 0) {
				if($('#sudopay_gateway_' + parent_gateway_id + '_' +gateway_id).length > 0)
					gateway_name = $('#sudopay_gateway_' + parent_gateway_id + '_' +gateway_id).data("gateway-name");
				$('#wallet_gateway_name_disp').html(gateway_name);
			}
		}

		if(buyer_fees_formula != '') {
		    var wallet_payment = $('#wallet_payment').val();
			var amount = eval(discounted_amount_bf_revise).toFixed(2);
			var revised_amount = eval(buyer_fees_formula).toFixed(2);
			var wallet_amount_disp = eval(eval(buyer_fees_formula) - wallet_payment);
			$('#wallet_amount_disp').html(wallet_amount_disp.toFixed(2));
			//var formula = buyer_fees_formula;
			//$('#buyer_fees_fourmula_disp').html(discounted_currency_bf_revise + ' ' + eval(buyer_fees_formula));
			if(amount != revised_amount) {
				var revised_txt = payment_gateway_revised_amount_txt.replace(/VAR_CURRENCY/g, discounted_currency_bf_revise);
				var revised_txt = revised_txt.replace(/VAR_AMOUNT/g, amount);
				var revised_txt = revised_txt.replace(/VAR_REVISED_AMOUNT/g, eval(buyer_fees_formula).toFixed(2));
				$('#buyer_fees_fourmula_disp').text(revised_txt);
				$('#buyer_fees_fourmula_disp').parent('.alert').show();
			}
			else {
				$('#buyer_fees_fourmula_disp').parent('.alert').hide();
			}
		}
	}

	</script>
@stop