@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: ACCOUNT MENU -->
			@include('myaccount.myAccountMenu')
			<!-- END: ACCOUNT MENU -->
		</div>

	   <div class="col-md-10">
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('myPurchases.my_sales') }}</h1>
			</div>

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

			<div class="well">
				{{ Form::open(array('action' => array('PurchasesController@getMySales'), 'id'=>'purchaseFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
					<!-- BEGIN: SEARCH BLOCK -->
					<div id="search_holder" class="portlet bg-form">
						<div class="portlet-title">
							<div class="caption">
								{{ Lang::get('myPurchases.search_order') }}
							</div>
							<div class="tools">
								<a class="collapse" href="javascript:;"></a>
							</div>
						</div>

						<div id="selSrchProducts" class="portlet-body">
							<div class="row">
								<fieldset class="col-md-6">
									<div class="form-group">
										{{ Form::label('order_id', Lang::get('admin/purchaseslist.order_id'), array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::text('order_id', Input::get("order_id"), array('class' => 'form-control')) }}
										</div>
									</div>
									<div class="form-group fn_clsPriceFields {{{ $errors->has('from_date') ? 'error' : '' }}}">
										{{ Form::label('from_date', trans("admin/purchaseslist.order_date"), array('class' => 'col-md-4 control-label')) }}
										<div class="col-md-6">
											<div class="input-group date date-picker input-daterange col-xs-12" data-date-format="dd-mm-yyyy">
												{{ Form::text('from_date', Input::old('from_date', Input::get('from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
												<label for="date_added_to" class="input-group-addon">{{ trans('common.to') }}</label>
												{{ Form::text('to_date', Input::old('to_date', Input::get('to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
											</div>
											<label class="error">{{{ $errors->first('from_date') }}}</label>
										</div>
									</div>
								</fieldset>

								<fieldset class="col-md-6">
									<div class="form-group">
										{{ Form::label('order_status', Lang::get('admin/purchaseslist.order_status'), array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::select('order_status', $search_order_statuses,  Input::get("order_status"), array('class' => 'form-control bs-select')) }}
										</div>
									</div>
									<div class="form-group">
										{{ Form::label('buyer_user_code', Lang::get('admin/purchaseslist.user_code_of_buyer'), array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::text('buyer_user_code', Input::old('buyer_user_code', Input::get('buyer_user_code')), array('class' => 'form-control')) }}
										</div>
									</div>
								</fieldset>
							</div>

							<div class="form-group">
								<div class="col-md-offset-2 col-md-5">
									<button type="submit" name="search_tax" value="search_tax" class="btn purple-plum">
										{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i>
									</button>
									<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('PurchasesController@getMySales') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
								</div>
							</div>
						</div>
					</div>
					<!-- END: SEARCH BLOCK -->

                    @if(count($order_details) > 0)
						<?php
							$allow_deal_block = 0;
							if(CUtil::chkIsAllowedModule('deals'))
							{
								$deal_service = new DealsService();
								$allow_deal_block = 1;
							}
						?>
                        <div class="table-responsive margin-bottom-30">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ Lang::get('myPurchases.order') }}</th>
                                        <th>{{ Lang::get('myPurchases.buyer') }}</th>
                                        <th>{{ Lang::get('myPurchases.item_details') }}</th>
                                        <th>{{ Lang::get('myPurchases.total_amount') }}</th>
                                        <th>{{ Lang::get('myPurchases.status') }}</th>
                                        <th width="100">{{ Lang::get('common.action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if(count($order_details) > 0)
                                        @foreach($order_details as $order)
                                            <?php
												$is_all_not_shipped = $is_all_delivered = true;
												foreach($order['order_items'] as $o_items){
													if(!isset($o_items->product_details) || (isset($o_items->product_details['is_downloadable_product']) && $o_items->product_details['is_downloadable_product'] == 'Yes' ))
														continue;

													if($o_items->shipping_status != 'delivered')
														$is_all_delivered = false;
													if($o_items->shipping_status != 'not_shipping')
														$is_all_not_shipped = false;
												}
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="dl-horizontal dl-horizontal-new wid-220">
                                                        <dl>
                                                            <dt>{{ Lang::get('myPurchases.order_id') }}</dt>
                                                            <dd><span><strong>{{ CUtil::setOrderCode($order->id); }}</strong></span></dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>{{ Lang::get('myPurchases.date_ordered') }}</dt>
                                                            <dd><span>{{ CUtil::FMTDate($order->date_created, 'Y-m-d H:i:s', ''); }}</span></dd>
                                                        </dl>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $user_det = CUtil::getUserDetails($order['buyer_id']);?>
                                                    <p>
															<strong><a href="{{$user_det['profile_url']}}">{{$user_det['display_name']}}</a></strong>
															<span><a class="text-muted" href="{{$user_det['profile_url']}}">({{ $user_det['user_code'] }})</a></span>
														</p>
                                                </td>
                                                <td>
                                                    @if($show_ship = false)@endif

                                                    @if($is_product_deleted = false)@endif
                                                    <?php $inc=0;
                                                    	$tot = count($order['order_items']);
                                                    ?>

                                                    @foreach($order['order_items'] as $o_items)
														<?php
															$product_id = $o_items->item_id;
															$products = Products::initialize();
															$products->setProductId($product_id);
															$products->setIncludeBlockedUserProducts(true);
															$products->setIncludeDeleted(true);
															$product_details = $products->getProductDetails();
															$prod_name = $prod_code = '';
															$prod_url = '#';
															if(count($product_details) > 0) {
															$prod_name = $product_details['product_name'];
															$prod_url = $productService->getProductViewURL($product_id, $product_details);
															$prod_code = $product_details['product_code'];
															if($product_details['is_downloadable_product'] == 'No')
															$show_ship = true;
															if($product_details['product_status'] == 'Deleted')
																$is_product_deleted = true;
															}
															$deal_available = 0;
															if($allow_deal_block && isset($o_items->deal_id) && $o_items->deal_id > 0)
															{
																$deal_available = 1;
																$deal_details = $deal_service->fetchDealDetailsById($o_items->deal_id);
															}
															$inc++;
														?>
                                                        <div class="dl-horizontal dl-horizontal-new @if($tot > 1 && ($inc!=$tot)) margin-bottom-20 @endif">
                                                            <dl>
                                                                <dt>{{ trans('myPurchases.name') }}</dt>
                                                                <dd>
																	<span>
																		<a href="{{ $prod_url }}" title="{{{ $prod_name }}}">{{{ Str::limit($prod_name , 20) }}}</a>
																	</span>
																</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>{{ trans('myPurchases.code') }}</dt>
                                                                <dd><span><a href="{{ $prod_url }}" title="{{{ $prod_code }}}">{{{ $prod_code }}}</a></span></dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>{{ trans('myPurchases.qty') }}</dt>
                                                                <dd><span><strong>{{ $o_items->item_qty }}</strong></span></dd>
                                                            </dl>
                                                            @if($product_details['product_status'] == 'Deleted')
                                                                <dl>
                                                                    <dt>{{ trans('productAdd.product_status') }}</dt>
                                                                    <dd><span>{{ $product_details['product_status'] }}</span></dd>
                                                                </dl>
                                                            @endif

                                                            <!-- BEGIN: DEALS BLOCK -->
                                                            @if ($deal_available == 1 && isset($deal_details) && COUNT($deal_details) > 0)
                                                                <div class="dl-horizontal dl-horizontal-new">
																	<dl>
																		<dt>{{ Lang::get('deals::deals.deals_label') }}</dt>
																		<dd>
																			<span>
																				<a href="{{ $deal_details['viewDealLink'] }}" title="{{ $deal_details['deal_title'] }}">
																					{{ Str::limit($deal_details['deal_title'] , 15) }}
																				</a>
																			</span>
																		</dd>
																	</dl>
																	<dl>
																		<dt>{{ Lang::get('deals::deals.deal_discount') }}</dt>
																		<dd><span class="font-green">{{ $deal_details['discount_percentage'] }}%</span></dd>
																	</dl>
																	@if(isset($o_items->deal_tipping_status) && $o_items->deal_tipping_status != "")
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
                                                                <a href="{{ $deal_details['viewDealLink'] }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" target="_blank" class="label label-primary"><i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}</a>
                                                            @endif
                                                            <!-- END: DEALS BLOCK -->
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <p>
                                                        <span class="text-muted">{{ Lang::get('myPurchases.amount') }}:</span>
                                                        {{ CUtil::convertAmountToCurrency($order->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <?php
                                                        $lbl_class = "";
                                                        if(strtolower ($order['order_status']) == "draft")
                                                            $lbl_class = "label-default";
                                                        elseif(strtolower ($order['order_status']) == "pending_payment")
                                                            $lbl_class = "label-warning";
                                                        elseif(strtolower ($order['order_status']) == "payment_completed")
                                                            $lbl_class = "label-success";
                                                        elseif(strtolower ($order['order_status']) == "refund_requested")
                                                            $lbl_class = "label-warning";
                                                        elseif(strtolower ($order['order_status']) == "refund_completed")
                                                            $lbl_class = "label-info";
                                                        elseif(strtolower ($order['order_status']) == "refund_rejected")
                                                            $lbl_class = "label-danger";
                                                        elseif(strtolower ($order['order_status']) == "not_paid")
                                                            $lbl_class = "label-warning";
                                                        elseif(strtolower ($order['order_status']) == "payment_cancelled")
                                                            $lbl_class = "label-danger";
                                                    ?>
                                                    @if(strtolower ($order['order_status']) == "not_paid")
                                                        <p class="label {{ $lbl_class }}">{{ Lang::get('myPurchases.status_unpaid') }}</p>
                                                    @else
                                                        <p class="label {{ $lbl_class }}">{{ Lang::get('myPurchases.status_txt_'.$order->order_status) }}</p>
                                                    @endif
                                                </td>
												<td class="action-btn">
                                                    <a href="{{ URL::action('PurchasesController@getSalesOrderDetails', $order->id).'?page=my_sale' }}" title="{{ Lang::get('myPurchases.view') }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7"><p class="alert alert-info">{{ Lang::get('myPurchases.no_result') }}</p></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if(count($order_details) > 0)
                            <div class="text-right">
                                {{ $order_details->appends(array('order_id' => Input::get('order_id'), 'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date'), 'order_status' => Input::get('order_status'), 'buyer_user_code' => Input::get('buyer_user_code')))->links() }}
                            </div>
                        @endif
                    {{ Form::close() }}

                        <div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
                            <span class="ui-icon ui-icon-alert"></span>
                            <span id="dialog-product-confirm-content" class="show"></span>
                        </div>
                    @else
                        <div class="note note-info">
                           {{ Lang::get('product.list_empty') }}
                        </div>
                    @endif
				{{ Form::close() }}

				{{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('myproducts/deleteproduct'))) }}
					{{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
					{{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var page_name = "my_sales_list";
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;
		var show_search_filters = '{{ Lang::get('product.show_search_filters') }}';
		var hide_search_filters = '{{ Lang::get('product.hide_search_filters') }}';
		var product_confirm_delete = '{{ Lang::get('myProducts.product_confirm_delete') }}';
		var product_confirm_featured = '{{ Lang::get('myProducts.product_confirm_featured') }}';
		var product_confirm_unfeatured = '{{ Lang::get('myProducts.product_confirm_unfeatured') }}';
		var my_products_title = '{{ Lang::get('myProducts.my_products_title') }}';
		var actions_url_popup = '{{ URL::action('PurchasesController@getViewShippingPopup') }}';
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var product_confirm_delivered = '{{ Lang::get('myProducts.product_confirm_delivered') }}';
	</script>
@stop