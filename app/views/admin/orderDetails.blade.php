@extends('admin')
@section('content')
	@if(Input::get('m') == 'member')
		<a href="{{ Url::to('admin/users/user-details/').'/'.$order_details[0]['buyer_id'].'?status='.Input::get('s') }}" class="btn default purple-stripe btn-xs pull-right mt5">
		<i class="fa fa-arrow-left"></i> {{ trans('admin/manageMembers.viewmember_user_details_title') }}</a>
	@else
		@if(Input::has('i') && Input::get('i') == 'invoice')
			<?php $user_det = CUtil::getUserDetails($order_details[0]['buyer_id']);?>
	    	<div class="pull-right responsive-pull-none">
                <ul class="list-inline orderdet-list mt5">
                    <li>{{ Lang::get('admin/purchaseslist.buyer') }}: <strong><a href="{{ URL::to('admin/users/user-details').'/'.$order_details[0]['buyer_id'] }}">{{$user_det['display_name']}}</a></strong> <span class="text-muted">({{ $user_det['user_name'] }})</span></li>
                    <li class="text-muted">|</li>
                    <li>
                        @if($user_det['user_group_name'] != '')
                        	{{ Lang::get('admin/unpaidInvoiceList.group_name') }}: <span class="text-muted"> {{$user_det['user_group_name']}}</span>
                        @endif
                    </li>
                    <li>
                        <a href="{{ Url::to('admin/unpaid-invoice-list/index').'?tab='.Input::get('tab') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe responsive-btn-block">
                            <i class="fa fa-chevron-left"></i> {{ Lang::get('admin/unpaidInvoiceList.unpaid_invoices_list') }}
                        </a>
                    </li>
                </ul>
	        </div>
		@else
		    <?php $user_det = CUtil::getUserDetails($order_details[0]['buyer_id']);?>
	    	<div class="pull-right responsive-pull-none">
                <ul class="list-inline orderdet-list mt5">
                    <li>{{ Lang::get('admin/purchaseslist.buyer') }}: <strong><a href="{{ URL::to('admin/users/user-details').'/'.$order_details[0]['buyer_id'] }}">{{$user_det['display_name']}}</a></strong>  <span class="text-muted">({{ $user_det['user_name'] }})</span></li>
                    <li class="text-muted">|</li>
                    <li>@if($user_det['user_group_name'] != '') {{ Lang::get('admin/unpaidInvoiceList.group_name') }}:
					<span class="text-muted"> {{$user_det['user_group_name']}}</span>@endif</li>
                    <li><a href="{{ URL::action('AdminPurchasesController@getIndex') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe responsive-btn-block"><i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}</a></li>
                </ul>
            </div>
		@endif
	@endif

	<h1 class="page-title">{{ Lang::get('admin/purchaseslist.order_detail') }}</h1>

    <!-- BEGIN: INFO BLOCK -->
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif

    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(count($order_details) <= 0)
        <div class="note note-info">
           {{ Lang::get('admin/purchaseslist.no_result') }}
        </div>
    <!-- END: INFO BLOCK -->
    @else
        @if(count($order_details) > 0)
            {{ Form::open(array('action' => array('PurchasesController@getIndex'), 'id'=>'purchaseFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
                <!-- BEGIN: ORDER DETAILS -->
				@if(count($order_details) > 0)
                    <div class="row address-details">
                        <?php
                            $shipping_address = isset($order_details[0]->shipping_details[0]->shipping_address)?$order_details[0]->shipping_details[0]->shipping_address:'';
                            $billing_address = isset($order_details[0]->shipping_details[0]->billing_address)?$order_details[0]->shipping_details[0]->billing_address:'';
                        ?>
                        @if($shipping_address!='')
                            <div class="col-md-6">
                                <div class="portlet green-meadow box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i> {{ Lang::get('admin/purchaseslist.address') }}
                                        </div>
                                    </div>
                                    <div class="portlet-body form">
                                    	<div class="form-body">
                                        	<div class="mb30">
                                                <h4 class="form-section">{{trans('myPurchases.shipping_address')}}</h4>
                                                <?php $ship_details = $shipping_address[0];?>
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
                                                <p>{{ $ship_details->zip_code }}</p>
                                            </div>

                                            @if($billing_address!='')
                                                <h4 class="form-section">{{ trans('checkOut.billing_address') }}</h4>
                                                <?php $billing_details = $billing_address[0];?>
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
                                                <p>{{ $billing_details->zip_code }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

						<div class="col-md-6">
                            <div class="portlet purple-plum box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-file-text-o"><sub class="fa fa-clock-o font11"></sub></i>{{ Lang::get('myPurchases.item_details') }}
                                    </div>
                                </div>
                                <div class="portlet-body form admin-dl">
                                    <div class="form-body">
										<div class="mb20">
											<h4 class="form-section">{{ Lang::get('myPurchases.order_detail') }}</h4>
											<div class="dl-horizontal">
												<dl>
													<dt>{{ trans('myPurchases.order_id') }}</dt>
													<dd><span>
														{{  CUtil::setOrderCode($order_details[0]->id); }}
														</span>
													</dd>
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
													<dd>
														<span>
															{{ CUtil::convertAmountToCurrency($order_details[0]->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
														</span>
													</dd>
												</dl>
											</div>
										</div>
										<h4 class="form-section">{{ Lang::get('myPurchases.shop_details') }}</h4>
										<div class="dl-horizontal">
											@if(isset($order_details[0]->shop_details) && !empty($order_details[0]->shop_details))
												<dl>
													<dt>{{ trans('myPurchases.shop') }}</dt>
													<dd><span><a href="{{$order_details[0]->shop_details['shop_url']}}" class="text-primary">
													{{ $order_details[0]->shop_details['shop_name'] }}</a></span></dd>
												</dl>
											@endif

											<dl>
												<dt>{{ trans('myPurchases.seller') }}</dt>
												<dd>
													<span>
														<?php $user_details = CUtil::getUserDetails($order_details[0]['seller_id']); ?>
														<a title="{{$user_details['display_name']}}" href="{{ URL::to('admin/users/user-details').'/'.$order_details[0]['seller_id'] }}">{{$user_details['display_name']}}</a>
													</span>
												</dd>
											</dl>
										</div>
									</div>
								</div>
							</div>
						</div>
                        <div class="col-md-6 pull-right">
                            <div class="portlet purple-plum box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-file-text-o"><sub class="fa fa-clock-o font11"></sub></i> {{ Lang::get('common.status') }}
                                    </div>
                                </div>
                                <div class="portlet-body form admin-dl">
                                    <div class="form-body">
										<?php
                                            $order_status_paid = str_replace('_', ' ', ucfirst($order_details[0]->order_status));
                                            $lbl_class = 'label-default';

                                            if($order_details[0]->order_status == 'payment_completed') {
                                                $order_status_paid = Lang::get('admin/purchaseslist.status_txt_paid');
                                                $lbl_class = "label-success";
                                            }
                                            elseif($order_details[0]->order_status == 'refund_requested') {
                                                $order_status_paid = Lang::get('admin/purchaseslist.status_txt_refund_requested');//"Cancellation Requested";
                                                $lbl_class = "label-warning";
                                            }
                                            elseif($order_details[0]->order_status == 'refund_rejected') {
                                                $order_status_paid = Lang::get('admin/purchaseslist.status_txt_refund_rejected');//"Cancellation Rejected";
                                                $lbl_class = "label-default";
                                            }
                                            elseif($order_details[0]->order_status == 'refund_completed') {
                                                $order_status_paid = Lang::get('admin/purchaseslist.status_txt_refund_completed');//"Cancellation Completed";
                                                $lbl_class = "label-success";
                                            }
                                            elseif($order_details[0]->order_status == 'pending_payment' || $order_details[0]->order_status == 'not_paid') {
                                                $order_status_paid =Lang::get('admin/purchaseslist.status_txt_unpaid');
                                                $lbl_class = "label-danger";
                                            }
                                            elseif($order_details[0]->order_status == 'payment_cancelled') {
                                                $order_status_paid = trans('myPurchases.status_txt_'.$order_details[0]->order_status);
                                                $lbl_class = "label-danger";
                                            }
                                        ?>
                                        <div class="dl-horizontal">
                                            <dl><dt>{{ Lang::get('common.status') }}</dt><dd><span><span class="label {{ $lbl_class }}">{{ $order_status_paid }}</span></span></dd></dl>

                                            <dl>
                                                @if($order_details[0]->date_created != '0000-00-00 00:00:00')
                                                    <dt>{{ trans('myPurchases.date_added') }}</dt>
													<dd><span>{{ CUtil::FMTDate($order_details[0]->date_created, "Y-m-d H:i:s", "") }}</span></dd>
                                                @else
                                                    <dt>{{ trans('myInvoice.date_added') }}</dt><dd><span>-</span></dd>
                                                @endif
                                            </dl>

                                            @if($order_status_paid == 'Paid')
                                                <dl>
                                                    @if($order_details[0]->date_created != '0000-00-00 00:00:00')
                                                        <dt>{{ trans('myPurchases.paid_date') }}</dt>
														<dd><span>{{ CUtil::FMTDate($order_details[0]->date_created, "Y-m-d H:i:s", "") }}</span></dd>
                                                    @else
                                                        <dt>{{ trans('myPurchases.paid_date') }}</dt> <dd><span>-</span></dd>
                                                    @endif
                                                </dl>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
				 <!-- END: ORDER DETAILS -->

                <div class="portlet box blue-hoki">
                    <!-- BEGIN: TABLE TITLE -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i> {{ trans('admin/purchaseslist.payment_list') }}
                        </div>
                    </div>
                    <!-- END: TABLE TITLE -->

                    <!-- BEGIN: SHOP LIST -->
                    <div class="portlet-body">
                        <div class="table-responsive responsive-xscroll">
                            <table class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="col-md-1">{{ Lang::get('admin/purchaseslist.product') }}</th>
                                        <th class="col-md-3">{{ Lang::get('admin/purchaseslist.price') }}</th>
                                        <th class="col-md-2">{{ Lang::get('myPurchases.shipping_details') }}</th>
										<th class="col-md-4">{{ Lang::get('admin/purchaseslist.payment_status') }}</th>
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
                                                        $shipping_status_value = $order_obj->getOrderitems($invoice_detail['item_id'],$invoice_detail['order_id']);
                                                        //$edit_url = URL::to('products/add?id='.$product->id);
														$deal_available = 0;
														if($allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0)
														{
															$deal_available = 1;
															$deal_details = $deal_service->fetchDealDetailsById($invoice_detail['deal_id']);
														}
														$variation_available = 0;
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
    															@if(!empty($product))
    																<!-- BEGIN: DEALS BLOCK -->
    																@if ($deal_available == 1 && isset($deal_details) && COUNT($deal_details) > 0)
    																	<a href="{{ $deal_details['viewDealLink'] }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" target="_blank" class="label label-primary pull-right fonts12"><i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}</a>
    																@endif
    																<!-- END: DEALS BLOCK -->
    																<div class="custom-feature">
    																	@if($product['product_status'] != 'Deleted')
    																		<figure class="margin-bottom-5 custom-image">
    																			<a href="{{$view_url}}" class="img81x61">
    																				<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ nl2br($product['product_name'])  }}}" alt="{{{nl2br($product['product_name'])}}}" />
                                                                                    {{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
    																			</a>
    																		</figure>
                                                                            <div class="dl-horizontal dl-horizontal-new mt10">
                                                                                <dl>
                                                                                    <dt>{{ Lang::get('product.product_code') }}</dt>
                                                                                    <dd><span><a href="{{$view_url}}">{{ $product['product_code'] }}</a></span></dd>
                                                                                </dl>
                                                                            </div>
    																	@else
    																		<figure>
    																			<a href="javascript:void(0);" class="img81x61">
    																				<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ nl2br($product['product_name'])  }}}" alt="{{{nl2br($product['product_name'])}}}" />
                                                                                    {{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
    																			</a>
    																		</figure>
    																	@endif
    																</div>

    																<!-- BEGIN: DEALS BLOCK -->
    																@if ($deal_available == 1 && isset($deal_details) && COUNT($deal_details) > 0)
    																	<div class="dl-horizontal dl-horizontal-new mt10">
    																		<dl>
    																			<dt>{{ Lang::get('deals::deals.deal_id_label') }}</dt>
    																			<dd>
    																				<span><a href="{{ $deal_details['viewDealLink'] }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}">{{ $invoice_detail['deal_id'] }}</a></span>
    																			</dd>
    																		</dl>
    																		<dl>
    																			<dt>{{ Lang::get('deals::deals.deal_discount') }}</dt>
    																			<dd><span class="font-green">{{ $deal_details['discount_percentage'] }}%</span></dd>
    																		</dl>
    																		@if(isset($invoice_detail['deal_tipping_status']) && $invoice_detail['deal_tipping_status'] != "")
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
    															@else
    																<span class="text-danger">{{"product deleted"}}</span>
    															@endif
    														</div>
                                                        </td>

                                                        <td class="address-details">
                                                            <div class="dl-horizontal dl-horizontal-new wid-330">
                                                                <?php
																	$id_text = ($order->has_invoice) ? trans('myPurchases.invoice_id') : trans('myPurchases.order_id');
                                                                ?>
                                                                <dl>
                                                                    <dt>{{ $id_text }}</dt>
                                                                    <dd><span><strong>{{ $invoice_detail['id'] }}</strong></span></dd>
                                                                </dl>

                                                                <dl>
                                                                    @if(!empty($product))
                                                                        <dt>{{ trans('myPurchases.product') }}</dt>
                                                                        @if($product['product_status'] != 'Deleted')
                                                                            <dd>
                                                                                <span><a href="{{$view_url}}" class="text-primary">
                                                                                <strong>{{{ nl2br($product['product_name']) }}}</strong></a></span>
                                                                            </dd>
                                                                        @else
                                                                            <dd>
                                                                                <span><strong>{{{ nl2br($product['product_name']) }}}</strong></span>
                                                                                <span class="mt5"><span class="label label-danger">Deleted</span></span>
                                                                            </dd>
                                                                        @endif
                                                                    @else
                                                                        <dt>{{ trans('myPurchases.product') }}</dt>
                                                                        <dd><span>{{trans('common.deleted')}}</span></dd>
                                                                    @endif
                                                                </dl>

                                                                {{--<dl>
                                                                    <dt>{{ trans('myPurchases.shop') }}</dt>
                                                                    <dd><span><a href="{{$shop_url}}">{{ $invoice_detail['shop_name'] }}</a></span></dd>
                                                                </dl>--}}

                                                                <dl>
                                                                    <dt>{{ Lang::get('myPurchases.quantity') }}</dt>
                                                                    <dd><span>{{ $invoice_detail['item_qty'] }}</span></dd>
                                                                </dl>

                                                                <dl>
                                                                    <dt>{{ Lang::get('myPurchases.item_amount') }}</dt>
                                                                    <dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['item_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                </dl>

                                                                @if($invoice_detail['shipping_company'])
																	<dl>
																		<dt><span title="{{ Lang::get('myPurchases.shipping_company') }}">{{ Lang::get('myPurchases.shipping_company') }}</span></dt>
																		<dd><span>{{ CUtil::getSippingCompanyName($invoice_detail['shipping_company']); }}</span></dd>
																	</dl>
                                                                @else
																	<dl>
																		<dt>{{ Lang::get('myPurchases.shipping_company') }}</dt>
																		<dd><span>--</span></dd>
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
                                                                                echo '<dl><dt>'.(isset($tax_info['tax_name']) ? $tax_info['tax_name'] : '').'</dt><dd><span>'.CUtil::convertAmountToCurrency($amount, Config::get('generalConfig.site_default_currency'), '', true).'</span></dd></dl>';
                                                                     }
                                                                    ?>
                                                                @endif
																@if($allow_variations_block && isset($invoice_detail['giftwrap_price']) && $invoice_detail['giftwrap_price'])
                                                                    <dl>
                                                                        <dt>{{ Lang::get('variations::variations.giftwrap_fee_lbl') }}</dt>
                                                                        <dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['giftwrap_price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                    </dl>
                                                                @endif
                                                                @if($order->has_invoice)
                                                                    <dl>
                                                                        <dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
                                                                        <dd>
																			<span>{{ CUtil::convertAmountToCurrency($invoice_detail['item_total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span>
																		</dd>
                                                                    </dl>
                                                                @else
                                                                    <dl>
                                                                        <dt>{{ Lang::get('myPurchases.total_amount') }}</dt>
                                                                        <dd><span>{{ CUtil::convertAmountToCurrency($invoice_detail['total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                                    </dl>
                                                                @endif
                                                             </div>
                                                        </td>

														<td class="sales-odetails">
                                                            @if(isset($product['is_downloadable_product']) && $product['is_downloadable_product'] == 'No')
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

                                                                @if($invoice_detail['shipping_status'] == 'shipped' && $order->order_status != 'refund_completed')
                                                                    <p>
                                                                        <a href="{{ URL::action('AdminPurchasesController@getSetAsDelivered', $order->id).'?page=sale_order&item_id='.$invoice_detail['item_id'] }}" class="fn_dialog_confirm btn btn-sm btn-info" title="" action="Delivered"><i class="fa fa-check-square-o"></i> {{ Lang::get('admin/purchaseslist.set_as_delivered') }}</a>
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
                                                                            if(($allow_deal_block && isset($invoice_detail['deal_id']) && $invoice_detail['deal_id'] > 0 && !$deal_service->allowToDownloadDealItem($invoice_detail['deal_id'])) || ( $invoice_detail['is_refund_requested'] == "Yes" && $invoice_detail['is_refund_approved_by_admin'] != 'Rejected'))
                                                                                $allow_shipping = false;
                                                                        ?>
                                                                        @if($allow_shipping)
                                                                        	@if(strtolower($invoice_detail['is_refund_approved_by_seller']) != "yes")
	                                                                        	@if(strtolower($invoice_detail['is_refund_approved_by_admin']) != "yes")
	                                                                            	<p><a href="javascript:;" onclick="openSetAsShippingPopup('{{$order->id}}', '{{$invoice_detail['item_id']}}')" href="" class="fn_signuppop btn btn-xs blue-madison" title=""><i class="fa fa-plane mr5"></i> {{ Lang::get('admin/purchaseslist.set_as_shipping') }}</a></p>
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

														@if($order->has_invoice)
                                                            <td>
                                                                <div class="clearfix">
                                                                    <p>
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
                                                                        <span class="label {{ $lbl_class }}">{{ trans('myPurchases.status_txt_'.$invoice_detail['invoice_status']) }}</span>
                                                                    </p>
                                                                    @if($invoice_detail['is_refund_requested'] == "Yes")
                                                                        <div class="invoice-detail admin-dl">
                                                                            <div class="dl-horizontal dl-horizontal-new">
                                                                                <dl>
                                                                                    <dt>{{ trans('myPurchases.reason_for_cancellation') }}</dt>
                                                                                    @if(isset($invoice_detail['refund_reason']) && $invoice_detail['refund_reason'] != '')
                                                                                        <dd><span>{{ $invoice_detail['refund_reason'] }}</span></dd>
                                                                                    @else
                                                                                        <dd><span>{{ trans('myPurchases.empty_notes')  }}</span></dd>
                                                                                    @endif
                                                                                </dl>

                                                                                @if(strtolower($invoice_detail['is_refund_approved_by_admin']) == 'no')
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_admin']) != "rejected")
                                                                                        <dl>
                                                                                            <dt>{{ trans('myPurchases.site_commission_status') }}</dt>
                                                                                            <dd><span class="label {{ $lbl_class }}">{{ trans('myPurchases.status_txt_pending') }}</span></dd>
                                                                                        </dl>
                                                                                    @else
                                                                                        <dl>
                                                                                            <dt>{{ trans('myPurchases.site_commission_status') }}</dt>
                                                                                            <dd><span class="label {{ $lbl_class }}">{{ trans('myPurchases.rejected_by_admin') }}</span></dd>
                                                                                        </dl>
                                                                                    @endif
                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_admin']) != "rejected")
                                                                                        <button class="btn btn-success btn-xs js-refund-form mb5" type="button" data-invoice="{{$invoice_detail['id']}}" data-action="accept"><i class="fa fa-check"></i> {{ Lang::get('admin/purchaseslist.accept') }}</button>
                                                                                        <button class="btn red btn-xs js-refund-form mb5" type="button" data-invoice="{{$invoice_detail['id']}}" data-action="rejected"><i class="fa fa-times"></i> {{ Lang::get('admin/purchaseslist.reject') }}</button>
                                                                                    @endif
                                                                                @else
                                                                                    <?php
                                                                                        if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        {
                                                                                            $lbl_class_admin = "label-success";
                                                                                            if(!isset($invoice_detail['refund_responded_by_admin_id']) || $invoice_detail['refund_responded_by_admin_id'] <= 0)
                                                                                            {
                                                                                                $commission_status = trans('myPurchases.accepted_by_admin');
                                                                                            }
                                                                                            elseif(isset($invoice_detail['refund_responded_by_admin_id']) && $invoice_detail['refund_responded_by_admin_id'] > 0 && $invoice_detail['refund_responded_by_admin_id']!=$logged_user_id )
                                                                                            {
                                                                                                $approved_user_det = CUtil::getUserDetails($invoice_detail['refund_responded_by_admin_id']);
                                                                                                if(isset($approved_user_det['display_name']))
                                                                                                    $commission_status = trans('myPurchases.accepted_by').' '.'<a href="'.URL::to('admin/users/user-details').'/'.$approved_user_det['id'].'" target="_blank">'.$approved_user_det['display_name'].'</a>';
                                                                                                else
                                                                                                    $commission_status = trans('myPurchases.accepted_by_admin');
                                                                                            }
                                                                                            else
                                                                                                $commission_status = trans('myPurchases.accepted_by_you');
                                                                                            $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_admin']) == "rejected")
                                                                                        {
                                                                                            $lbl_class_admin = "label-warning";
                                                                                            if(!isset($invoice_detail['refund_responded_by_admin_id']) || $invoice_detail['refund_responded_by_admin_id'] <= 0)
                                                                                            {

                                                                                                $commission_status = trans('myPurchases.rejected_by_admin');
                                                                                            }
                                                                                            elseif(isset($invoice_detail['refund_responded_by_admin_id']) && $invoice_detail['refund_responded_by_admin_id'] > 0 && $invoice_detail['refund_responded_by_admin_id']!=$logged_user_id )
                                                                                            {

                                                                                                $approved_user_det = CUtil::getUserDetails($invoice_detail['refund_responded_by_admin_id']);
                                                                                                if(isset($approved_user_det['display_name']))
                                                                                                    $commission_status = trans('myPurchases.rejected_by').' '.'<a href="'.URL::to('admin/users/user-details').'/'.$approved_user_det['id'].'" target="_blank">'.$approved_user_det['display_name'].'</a>';
                                                                                                else
                                                                                                    $commission_status = trans('myPurchases.rejected_by_admin');
                                                                                            }
                                                                                            else
                                                                                                $commission_status = trans('myPurchases.rejected_by_you');
                                                                                            $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $lbl_class_admin = "label-warning";
                                                                                            $commission_status = trans('myPurchases.status_txt_pending');
                                                                                            $commission_notes = $invoice_detail['refund_response_by_admin'];
                                                                                        }
                                                                                        $commission_amount = $invoice_detail['refunded_amount_by_admin'];
                                                                                        $seller_refunde_amount = $invoice_detail['refund_amount_by_seller']+$invoice_detail['refund_paypal_amount_by_seller'];
                                                                                    ?>

                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.status') }}</dt>
                                                                                        <dd><span class="label {{ $lbl_class_admin }}">{{ $commission_status }}</span></dd>
                                                                                    </dl>

                                                                                    @if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        <dl>
                                                                                            <dt>{{ trans('myPurchases.commission_amount_credited_to_buyer') }}</dt>
                                                                                            <dd>
                                                                                                <span>
                                                                                                    {{ CUtil::convertAmountToCurrency($commission_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
                                                                                                </span>
                                                                                            </dd>
                                                                                        </dl>
                                                                                    @endif

                                                                                    <div class="approval-notes">
                                                                                        <strong>{{ trans('myPurchases.admin_notes') }}</strong>
                                                                                        @if(isset($commission_notes) && $commission_notes != '')
                                                                                            <p>{{ $commission_notes }}</p>
                                                                                        @else
                                                                                            <p>{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                        @endif
                                                                                    </div>
                                                                                @endif
                                                                                 <?php
                                                                                        if(strtolower($invoice_detail['is_refund_approved_by_seller']) == "yes")
                                                                                        {
                                                                                            $lbl_class_seller = "label-success";
                                                                                            $seller_status = trans('myPurchases.accepted_by_seller');
                                                                                            $seller_notes = $invoice_detail['refund_response_by_seller'];
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_seller']) == "no" && strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                        {
                                                                                            $lbl_class_seller = "label-success";
                                                                                            $seller_status = trans('myPurchases.accepted_by_admin');
                                                                                        }
                                                                                        elseif(strtolower($invoice_detail['is_refund_approved_by_seller']) == "rejected")
                                                                                        {
                                                                                            $lbl_class_seller = "label-warning";
                                                                                            $seller_status = trans('myPurchases.rejected_by_seller');
                                                                                            $seller_notes = $invoice_detail['refund_response_by_seller'];
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $lbl_class_seller = "label-warning";
                                                                                            $seller_status = trans('myPurchases.status_txt_pending');
                                                                                            $seller_notes = $invoice_detail['refund_response_by_seller'];
                                                                                        }
                                                                                        $seller_refunde_amount = $invoice_detail['refund_amount_by_seller']+$invoice_detail['refund_paypal_amount_by_seller'];
                                                                                    ?>

                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.seller_status') }}</dt>
                                                                                        <dd><span class="label {{ $lbl_class_seller }}">{{ $seller_status }}</span></dd>
                                                                                    </dl>

                                                                                @if(strtolower($invoice_detail['is_refund_approved_by_admin']) == "yes")
                                                                                    <dl>
                                                                                        <dt>{{ trans('myPurchases.amount_credited_to_buyer') }}</dt>
                                                                                        <dd>
                                                                                            <span>
                                                                                                {{ CUtil::convertAmountToCurrency($seller_refunde_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
                                                                                            </span>
                                                                                        </dd>
                                                                                    </dl>
                                                                                @endif

                                                                                @if(strtolower($invoice_detail['is_refund_approved_by_seller']) != "no")
                                                                                    <div class="approval-notes">
                                                                                        <strong>{{ trans('myPurchases.seller_notes') }}</strong>
                                                                                        @if(isset($seller_notes) && $seller_notes != '')
                                                                                            <p>{{ $seller_notes }}</p>
                                                                                        @else
                                                                                            <p>{{ trans('myPurchases.empty_notes')  }}</p>
                                                                                        @endif
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
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
																				$lbl_class = "label-warning";
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
                                                    @if(isset($invoice_detail['is_use_giftwrap']) && $invoice_detail['is_use_giftwrap'] > 0
                                                    	&& $invoice_detail['giftwrap_msg'] != '')
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
                                            <td colspan="5"><p class="alert alert-info">{{ Lang::get('admin/purchaseslist.no_result') }}</p></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- EN: SHOP LIST -->
                </div>
            {{ Form::close() }}

            {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('myproducts/deleteproduct'))) }}
                {{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
                {{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
            {{ Form::close() }}

            <div id="dialog-product-confirm" title="" style="display:none;">
            	<span class="ui-icon ui-icon-alert"></span>
				<span id="dialog-product-confirm-content" class="show ml15"></span>
            </div>
        @else
            <div class="note note-info">
               {{ Lang::get('product.list_empty') }}
            </div>
        @endif
    @endif
@stop

@section('script_content')
	<script type="text/javascript">
		function callShowMoreNotes(act, ident)	{
			$("#selSubLessNotes_"+ident).toggle('slow');
			$("#selSubMoreNotes_"+ident).toggle('slow');
		}

		function callShowMoreAdminNotes(act, ident)	{
			$("#selSubLessAdminNotes_"+ident).toggle('slow');
			$("#selSubMoreAdminNotes_"+ident).toggle('slow');
		}

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
            var refund_action = $(this).data('action');
            var div_id = 'refundreasondiv_'+invoice_id;

            var actions_url = '{{ URL::action('AdminPurchasesController@getResponseCancel') }}';
            var postData = 'invoice_id='+ invoice_id + '&refund_action=' + refund_action;
            fancybox_url = actions_url + '?' + postData;

            $.fancybox({
                maxWidth    : 800,
                maxHeight   : 432,
                fitToView   : false,
                width       : '70%',
                height      : '550',
                autoSize    : false,
                closeClick  : false,
                type        : 'iframe',
                href        : fancybox_url,
                openEffect  : 'none',
                closeEffect : 'none',
                afterClose  : function() {
                     window.location.reload();
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

	    function openSetAsShippingPopup(order_id, item_id) {
                var actions_url = '{{ URL::action('AdminPurchasesController@getSetAsShippingPopup') }}';
                var postData = 'order_id='+order_id+'&item_id='+item_id;
                //alert(postData);
                fancybox_url = actions_url + '?' + postData;
                $.fancybox({
                    maxWidth    : 800,
                    maxHeight   : 432,
                    fitToView   : false,
                    autoSize    : false,
                    closeClick  : false,
                    type        : 'iframe',
                    href        : fancybox_url,
                    openEffect  : 'none',
                    closeEffect : 'none',
                    /*afterClose  : function() {
                        window.location.reload();
                    }*/
                });
            };
		/**
		 *
		 * @access public
		 * @return void
		 **/
		/*function updateSetAsPaidValues(data)(){
			$('#set_as_paid_info').html(data);

		}*/
		function openViewShippingPopup(order_id) {
            var actions_url = '{{ URL::action('AdminPurchasesController@getViewShippingPopup') }}';
            var postData = 'order_id='+order_id;
            fancybox_url = actions_url + '?' + postData;
            $.fancybox({
                maxWidth    : 800,
                maxHeight   : 432,
                fitToView   : false,
                width       : '70%',
                height      : '432',
                autoSize    : false,
                closeClick  : false,
                type        : 'iframe',
                href        : fancybox_url,
                openEffect  : 'none',
                closeEffect : 'none',
                /*afterClose  : function() {
                     window.location.reload();
                }*/
            });
        };
		var cfg_site_name = '{{ Config::get('generalConfig.site_name') }}' ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					//alert(action); return false;
					switch(action){
						case "Delivered":
							cmsg = '{{ Lang::get('myProducts.product_confirm_delivered') }}';
							break;
					}
					bootbox.dialog({
						message: cmsg,
					  	title: cfg_site_name,
					  	buttons: {
							danger: {
					      		label: "Ok",
					      		className: "btn-danger",
					      		callback: function() {
					      			Redirect2URL(atag_href);
					      			bootbox.hideAll();
					      		}
					    	},
					    	success: {
					      		label: "Cancel",
					      		className: "btn-default",
					    	}
					  	}
					});
					return false;
				});
			});
	</script>
@stop