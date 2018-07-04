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
				<a href="{{ URL::action('PurchasesController@getMySales') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ trans('myPurchases.my_sales_list') }}
				</a>
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
                            <!-- BEGIN: TOP BLOCK -->
                            <div class="row">
                                <!-- BEGIN: ADDRESS -->
                                <div class="@if(!empty($shipping_address) || !empty($billing_address)) col-md-6 member-paymtstatus pull-right @endif">
                                    <div class="@if(!empty($shipping_address) || !empty($billing_address)) portlet bg-form @endif">
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
                                <!-- END: ADDRESS -->

                                <!-- BEGIN: STATUS BLOCK  -->
                                <div class="@if(!empty($shipping_address) || !empty($billing_address)) col-md-6 @else orderdetil-product @endif">
                                    <div class="member-paymtstatus">
                                        <div class="portlet bg-form">
                                            <div class="margin-bottom-30">
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
                                                                <span>{{ CUtil::convertAmountToCurrency($order_details[0]->sub_total, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
                                                            </dd>
                                                        </dl>

                                                        <dl>
                                                            <dt>{{ Lang::get('myPurchases.discount_amount') }}</dt>
                                                            <dd>
                                                                <span>{{ CUtil::convertAmountToCurrency($order_details[0]->discount_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
                                                            </dd>
                                                        </dl>
                                                    @endif

                                                    <dl>
                                                        <dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
                                                        <dd>
                                                            <span>{{ CUtil::convertAmountToCurrency($order_details[0]->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>
                                            <h2 class="title-one">{{ Lang::get('myPurchases.buyer_details') }}</h2>
                                            <div class="dl-horizontal">
                                                <dl>
                                                    <dt>{{ trans('myPurchases.buyer') }}</dt>
                                                    <dd>
                                                        <span>
                                                            <span class="label">
                                                                <?php $user_details = CUtil::getUserDetails($order_details[0]['buyer_id']); ?>
                                                                <a href="{{$user_details['profile_url']}}" title="{{$user_details['display_name']}}">{{$user_details['display_name']}}</a>
                                                            </span>
                                                        </span>
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="member-paymtstatus">
                                        <div class="portlet bg-form">
                                            <h2 class="title-one">{{trans('myPurchases.payment_status')}}</h2>
                                            <?php
                                                $order_status_paid = ucfirst($order_details[0]->order_status);
                                                $lbl_class = 'label-default';

                                                if($order_details[0]->order_status == 'payment_completed') {
                                                    $order_status_paid = trans('myPurchases.paid');
                                                    $lbl_class = "label-success";
                                                }
                                                elseif($order_details[0]->order_status == 'pending_payment' || $order_details[0]->order_status == 'not_paid') {
                                                    $order_status_paid = trans('myPurchases.unpaid');
                                                    $lbl_class = "label-warning";
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
                                                <dl>
                                                    <dt>{{ trans('myPurchases.status') }}</dt>
                                                    <dd><span><span class="label {{ $lbl_class }}">{{ $order_status_paid }}</span></span></dd>
                                                </dl>
                                                @if($order_details[0]->date_created != '0000-00-00 00:00:00')
                                                    <dl>
                                                        <dt>{{ trans('myPurchases.date_added') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($order_details[0]->date_created, "Y-m-d H:i:s", "") }}</span></dd>
                                                    </dl>
                                                @else
                                                    <dl><dt>{{ trans('myInvoice.date_added') }}</dt> <dd><span>-</span></dd></dl>
                                                @endif
                                                @if($order_status_paid == 'Paid')
                                                    @if($order_details[0]->date_created != '0000-00-00 00:00:00')
                                                        <dl>
                                                            <dt>{{ trans('myPurchases.paid_date') }}</dt>
                                                            <dd><span>{{ CUtil::FMTDate($order_details[0]->date_created, "Y-m-d H:i:s", "") }}</span></dd>
                                                        </dl>
                                                    @else
                                                        <dl>
                                                            <dt>{{ trans('myPurchases.paid_date') }}</dt>
                                                            <dd><span>-</span></dd>
                                                        </dl>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: STATUS BLOCK  -->
                            </div>
                            <!-- END: TOP BLOCK -->

                            <div class="table-responsive margin-bottom-30 responsive-xscroll">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-md-2">{{ Lang::get('myPurchases.product') }}</th>
                                            <th class="col-md-3">{{ Lang::get('myPurchases.product_details') }}</th>
                                            <th class="col-md-2">{{ Lang::get('myPurchases.shipping_details') }}</th>
                                            <th class="col-md-1">{{ Lang::get('myPurchases.payment_status') }}</th>
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
                                                            //$shipping_status_value = $order_obj->getOrderitems($invoice_detail['item_id'],$invoice_detail['order_id']);
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
                                                                                        <dd><span><a href="{{$view_url}}" title="{{ $product['product_code'] }}">{{ $product['product_code'] }}</a></span></dd>
                                                                                    </dl>
                                                                                @else
                                                                                    <figure>
                                                                                        <a href="#" class="imgsize-75X75">
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
                                                                    @if(isset($variations_det_arr['attrirb_det']) && COUNT($variations_det_arr['attrirb_det']) > 0)
                                                                    	<div class="dl-horizontal dl-horizontal-new">                                                                            @foreach($variations_det_arr['attrirb_det'] AS $attr)
                                                                            <dl>
                                                                                <dt>{{ $attr['name'] }}:</dt> <dd><span> {{ $attr['label'] }}</span></dd>
                                                                             </dl>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                	<!-- END: VARIATIONS BLOCK -->
																</div>
                                                            </td>
                                                            <td>
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

                                                                    @if($product['is_downloadable_product'] == 'Yes')
                                                                        <dl>
                                                                            <?php $download_url = URL::action('PurchasesController@getInvoiceAction'). '?action=download_file&invoice_id='.$invoice_detail['id']; ?>
                                                                            <dt>{{ trans('myPurchases.download_link') }}</dt>
                                                                            <dd><span><a href="{{$download_url}}" class="text-primary">{{ trans('myPurchases.download') }}</a></span></dd>
                                                                        </dl>
                                                                    @endif

                                                                    @if(isset($order->shop_details) && !empty($order->shop_details))
                                                                        <dl>
                                                                            <dt>{{ trans('myPurchases.shop_name') }}</dt>
                                                                            <dd>
                                                                                <span>
                                                                                    <a class="text-primary" href="{{$order->shop_details['shop_url']}}">{{ $order->shop_details['shop_name'] }}</a>
                                                                                </span>
                                                                            </dd>
                                                                        </dl>
                                                                    @endif

                                                                    <dl>
                                                                        <dt>{{ Lang::get('myPurchases.item_amount') }}</dt>
                                                                        <dd>
                                                                            <span>{{ CUtil::convertAmountToCurrency($invoice_detail['item_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span>
                                                                        </dd>
                                                                    </dl>

                                                                    <dl>
                                                                        <dt>{{ Lang::get('myPurchases.quantity') }}</dt>
                                                                        <dd><span>{{ $invoice_detail['item_qty'] }}</span></dd>
                                                                    </dl>

                                                                    @if($invoice_detail['shipping_company'])
                                                                        <dl>
                                                                            <dt>{{ Lang::get('myPurchases.shipping_company') }}</dt>
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
                                                                            <dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['item_total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                        @else
                                                                            <dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
                                                                            <dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
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

                                                                    @if(($invoice_detail['shipping_status'] == 'shipped' && $order->order_status != 'refund_completed') || (strtolower($invoice_detail['is_refund_approved_by_seller']) != "yes" && $invoice_detail['shipping_status'] == 'shipped'))
                                                                        <p>
                                                                            <a href="{{ URL::action('PurchasesController@getSetAsDelivered', $order->id).'?page=sale_order&item_id='.$invoice_detail['item_id'] }}" class="fn_dialog_confirm btn btn-sm btn-info" title="" action="Delivered"><i class="fa fa-check-square-o"></i> {{ Lang::get('admin/purchaseslist.set_as_delivered') }}</a>
                                                                        </p>
                                                                    @elseif($invoice_detail['shipping_status'] == 'not_shipping')
																		<?php
                                                                            $allow_deal_block = 0;
                                                                            if(CUtil::chkIsAllowedModule('deals'))
                                                                            {
                                                                                $allow_deal_block = 1;
                                                                                $deal_service = new DealsService();
                                                                            }
                                                                            $allow_shipping = true;
                                                                            if(($allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0 && !$deal_service->allowToDownloadDealItem($invoice_detail['deal_id'])) || ($invoice_detail['is_refund_requested'] == "Yes" && $invoice_detail['is_refund_approved_by_admin'] != 'Rejected'))
                                                                                $allow_shipping = false;
                                                                        ?>
                                                                        @if($allow_shipping)
                                                                    		@if(strtolower($invoice_detail['is_refund_approved_by_seller']) != "yes")
	                                                                        	@if(strtolower($invoice_detail['is_refund_approved_by_admin']) != "yes")
	                                                                            	<p><a href="{{ URL::action('PurchasesController@getSetAsShippingPopup').'?order_id='.$order->id.'&item_id='.$invoice_detail['item_id'] }}" class="fn_signuppop btn btn-xs blue-madison" title=""><i class="fa fa-plane"></i> {{ Lang::get('admin/purchaseslist.set_as_shipping') }}</a></p>
	                                                                        	@endif
	                                                                        @endif
                                                                        @else
                                                                            @if($allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0)
                                                                                <p title="{{Lang::get('deals::deals.shipping_not_allowed_not_tipped')}}">{{Lang::get('deals::deals.shipping_not_allowed_not_tipped')}}</p>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    <span title="{{ trans('myPurchases.not_applicable_for_download_prod') }}">{{ trans('common.not_applicable') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="wid-330">
                                                                    <p>
                                                                        @if(count($invoice_detail) > 0)
                                                                            <?php
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
                                                                            ?>
                                                                            <span class="label {{ $lbl_class }}">{{ trans('myPurchases.status_txt_'.$invoice_detail['invoice_status']) }}</span>
                                                                        @else
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
                                                                                        $lbl_class = "label-primary";
                                                                                    elseif($order_status == 'refund_rejected')
                                                                                        $lbl_class = "label-danger";
                                                                                    elseif($order_status == 'payment_cancelled')
                                                                                        $lbl_class = "label-danger";
                                                                                }
                                                                            ?>
                                                                            <span class="label {{ $lbl_class }}">{{ trans('myPurchases.status_txt_'.$order_status) }}</span>
                                                                        @endif
                                                                    </p>
                                                                    @if($invoice_detail['is_refund_requested'] == "Yes")
                                                                        <div class="invoice-detail order-detstas">
                                                                            <div class="portlet bg-form margin-bottom-15 pad7">
                                                                                <div class="text-muted margin-bottom-5"><strong>{{ trans('myPurchases.reason_for_cancellation') }}:</strong></div>
                                                                                @if(isset($invoice_detail['refund_reason']) && $invoice_detail['refund_reason'] != "")
                                                                                    <p>{{ $invoice_detail['refund_reason'] }}</p>
                                                                                @else
                                                                                    <p class="margin-bottom-5">{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                @endif
                                                                            </div>
                                                                            <div class="dl-horizontal dl-horizontal-new">
                                                                                @if(strtolower($invoice_detail['is_refund_approved_by_seller']) == 'no' && strtolower($invoice_detail['is_refund_approved_by_admin']) != 'yes')
                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.status') }}</dt>
                                                                                        <dd><span class="label label-warning">{{ trans('myPurchases.status_txt_pending') }}</span></dd>
                                                                                    </dl>
                                                                                    <p><a data-invoice="{{$invoice_detail['id']}}" class="js-refund-form btn blue btn-xs">Approve/DisApprove</a></p>
                                                                                    <div id="refundreasondiv_{{$invoice_detail['id']}}" style="display:none" class="margin-bottom-10">
                                                                                        <p class="margin-bottom-5">{{ Lang::get('myPurchases.action') }}</p>
                                                                                        <p class="margin-bottom-5">{{Form::select('refund_action_'.$invoice_detail['id'], array('' => 'Select Action', 'Yes' => 'Accept Refund', 'Rejected' => 'Reject the Request'), '', array('class' => 'form-control bs-select input-medium','id' => 'refund_action_'.$invoice_detail['id'])) }}</p>
                                                                                        <div id="error_{{$invoice_detail['id']}}" class="hide alert alert-success margin-bottom-20"></div>
                                                                                        <p class="margin-bottom-5">{{ Lang::get('myPurchases.notes') }}</p>
                                                                                        <p>{{Form::textarea('refund_response_'.$invoice_detail['id'],'',array('class' => 'form-control', 'rows' => '5', 'id' => 'refund_response_'.$invoice_detail['id'])) }}</p>
                                                                                        {{ Form::hidden('item_id', $invoice_detail['item_id'], array('id' => 'item_id_'.$invoice_detail['id'])) }}
                                                                                        <button data-invoice="{{$invoice_detail['id']}}" type="button" class="btn btn-success btn-xs js-response-refund margin-bottom-5"><i class="fa fa-check"></i> Submit</button>
                                                                                    </div>
                                                                                @else
                                                                                    <?php
                                                                                        if(strtolower($invoice_detail['is_refund_approved_by_seller']) == "yes")
                                                                                        {
                                                                                            $status = trans('myPurchases.accepted_by_you');
                                                                                            $notes = $invoice_detail['refund_response_by_seller'];
                                                                                            $lbl_class = "label-success";
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_seller']) == "no" && strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        {
                                                                                            $status = trans('myPurchases.accepted_by_admin');
                                                                                            $lbl_class = "label-success";
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_seller']) == "rejected")
                                                                                        {
                                                                                            $status = trans('myPurchases.rejected_by_you');
                                                                                            $notes = $invoice_detail['refund_response_by_seller'];
                                                                                            $lbl_class = "label-danger";
                                                                                        }
                                                                                            else
                                                                                        {
                                                                                            $status = trans('myPurchases.status_txt_pending');
                                                                                            $notes = $invoice_detail['refund_response_by_seller'];
                                                                                            $lbl_class = "label-warning";
                                                                                        }
                                                                                        $refunde_amount = $invoice_detail['refund_amount_by_seller']+$invoice_detail['refund_paypal_amount_by_seller'];
                                                                                    ?>
                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.status') }}</dt>
                                                                                        <dd><span class="label {{ $lbl_class }}">{{ $status }}</span></dd>
                                                                                    </dl>
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                         <dl>
                                                                                            <dt>{{ trans('myPurchases.amount_credited_to_buyer') }}</dt>
                                                                                            <dd><span>{{ CUtil::convertAmountToCurrency($refunde_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                                        </dl>
                                                                                    @endif
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_seller']) != "no")
                                                                                        <div class="portlet bg-form margin-bottom-15">
                                                                                            <div class="text-muted margin-bottom-5"><strong>{{ trans('myPurchases.notes') }}:</strong></div>
                                                                                            @if(isset($notes) && $notes != "")
                                                                                                <p>{{ $notes }}</p>
                                                                                            @else
                                                                                                <p class="margin-bottom-5">{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                            @endif
                                                                                        </div>
                                                                                   @endif
                                                                                @endif
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
                                                                                        <dt>{{ trans('myPurchases.commission_amount_credited_to_buyer') }}</dt>
                                                                                        <dd><span>{{ CUtil::convertAmountToCurrency($commission_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                                    </dl>
                                                                                @endif
                                                                                <div class="portlet bg-form margin-bottom-15 pad7">
                                                                                    <div class="text-muted margin-bottom-5"><strong>{{ trans('myPurchases.admin_notes') }}:</strong></div>
                                                                                    @if(isset($commission_notes) && $commission_notes != '')
                                                                                        <p>{{ $commission_notes }}</p>
                                                                                    @else
                                                                                        <p class="margin-bottom-5">{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
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
                                                <td colspan="4"><p class="alert alert-info">{{ Lang::get('myPurchases.no_result') }}</p></td>
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

	<script type="text/javascript">
		var page_name = "sales_order_details";
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;
		var show_search_filters = '{{ Lang::get('product.show_search_filters') }}';
		var hide_search_filters = '{{ Lang::get('product.hide_search_filters') }}';
		var product_confirm_delete = '{{ Lang::get('myProducts.product_confirm_delete') }}';
		var product_confirm_featured = '{{ Lang::get('myProducts.product_confirm_featured') }}';
		var product_confirm_unfeatured = '{{ Lang::get('myProducts.product_confirm_unfeatured') }}';
		var my_products_title = '{{ Lang::get('myProducts.my_products_title') }}';
		var response_cancel_url = "{{ Url::action('PurchasesController@postResponseCancel')}}";
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var product_confirm_delivered = '{{ Lang::get('myProducts.product_confirm_delivered') }}';
		var shipping_popup_url = '{{ URL::action('PurchasesController@getViewShippingPopup') }}';
	</script>
@stop