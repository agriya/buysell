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
				<div class="@if(count($product_list) > 0 || $is_search_done) @else responsive-text-center @endif">
					<a href="{{ URL::to('product/add') }}" class="btn green-meadow btn-xs responsive-btn-block pull-right">
					<i class="fa fa-plus margin-right-5"></i> {{ Lang::get('product.product_add')  }}</a>
				</div>
				<h1>{{ Lang::get('product.list_product') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<div class="well">
				<!-- BEGIN: ALERT BLOCK -->
				@if(Session::has('error_message') && Session::get('error_message') != '')
					<div class="note note-danger">{{ Session::get('error_message') }}</div>
					<?php Session::forget('error_message'); ?>
				@endif

				@if(Session::has('success_message') && Session::get('success_message') != '')
					<div class="note note-success">{{ Session::get('success_message') }}</div>
					<?php Session::forget('success_message'); ?>
				@endif

				@if(count($product_list) <= 0 && !$is_search_done)
					<div class="note note-info margin-0">
						{{ Lang::get('product.producs_not_added') }}
					</div>
					<!-- END: ALERT BLOCK -->
				@else
					@if(count($product_list) > 0 || $is_search_done)
						{{ Form::open(array('action' => array('ProductController@productList'), 'id'=>'productFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
							<div id="search_holder" class="portlet bg-form">
								<div class="portlet-title">
									<div class="caption">
										{{ Lang::get('product.search_products') }}
									</div>
									<div class="tools">
										<a class="collapse" href="javascript:;"></a>
									</div>
								</div>

								<div id="selSrchProducts" class="portlet-body">
									<div class="row">
										<fieldset class="col-md-6">
											<div class="form-group">
												{{ Form::label('search_product_code', Lang::get('product.product_code'), array('class' => 'col-md-4 control-label')) }}
												<div class="col-md-7">
													{{ Form::text('search_product_code', Input::get("search_product_code"), array('class' => 'form-control valid')) }}
												</div>
											</div>

											<div class="form-group">
												{{ Form::label('search_product_name', Lang::get('product.search_products'), array('class' => 'col-md-4 control-label')) }}
												<div class="col-md-7">
													{{ Form::text('search_product_name', Input::get("search_product_name"), array('class' => 'form-control valid')) }}
												</div>
											</div>

											@if(CUtil::chkIsAllowedModule('featuredproducts'))
                                                <div class="form-group">
                                                    {{ Form::label('search_featured_product', Lang::get('product.product_listing_featured'), array('class' => 'col-md-4 control-label')) }}
                                                    <div class="col-md-6">
                                                        {{ Form::select('search_featured_product', array('' => Lang::get('product.select'), 'Yes' => Lang::get('product.yes'), 'No' => Lang::get('product.no')), Input::get("search_featured_product"), array('class' => 'form-control bs-select input-medium')) }}
                                                    </div>
                                                </div>
			                                @endif
										</fieldset>

										<fieldset class="col-md-6">
											<div class="form-group">
												{{ Form::label('search_product_category', Lang::get('product.search_category'), array('class' => 'col-md-4 control-label')) }}
												<div class="col-md-7">
													{{ Form::select('search_product_category', $category_list, Input::get("search_product_category"), array('class' => 'form-control valid')) }}
												</div>
											</div>

											<div class="form-group">
												{{ Form::label('search_product_status', Lang::get('product.status'), array('class' => 'col-md-4 control-label')) }}
												<div class="col-md-7">
													{{ Form::select('search_product_status', $status_list, Input::get("search_product_status"), array('class' => 'form-control valid')) }}
												</div>
											</div>
										</fieldset>
									</div>

									<div class="form-group">
										<div class="col-md-offset-2 col-md-5">
											<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn purple-plum">
											<i class="fa fa-search"></i> {{ Lang::get('product.search') }}</button>
											<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'"><i class="fa fa-undo"></i> {{ Lang::get('product.reset') }}</button>
										</div>
									</div>
								</div>
							</div>

							<!--BEGIN: PRODUCT LIST TABLE-->
							<div class="table-responsive margin-bottom-30 responsive-xscroll">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>{{ Lang::get('product.product_code') }}</th>
											<th class="col-md-3">{{ Lang::get('product.product_name') }}</th>
											<th>{{ Lang::get('product.product_price') }}</th>
											<th class="col-md-1">{{ Lang::get('product.product_sales') }}</th>
											<th class="col-md-1">{{ Lang::get('product.status') }}</th>
											<th class="col-md-1">{{ trans('admin/productList.product_stocks') }}</th>
											<th width="100">{{ Lang::get('product.action') }}</th>
										</tr>
									</thead>

									<tbody>
										@if(count($product_list) > 0)
                                        	<?php
												$allow_variation = (CUtil::chkIsAllowedModule('variations')) ? 1 : 0;
											?>
											@foreach($product_list as $product)
												<tr>
													<?php
														$p_img_arr = $prod_obj->getProductImage($product->id, false);
														$p_thumb_img = $productService->getProductDefaultThumbImage($product->id, 'small', $p_img_arr);
														$view_url = $productService->getProductViewURLNew($product->id, $product);
														//$edit_url = URL::to('products/add?id='.$product->id);
														$price_group = $productService->getPriceGroupsDetailsNew($product->id, 0, 1, 0, false);
													?>
													<td>
														<div class="custom-feature">
															<figure class="margin-bottom-5 custom-image">
																<a href="{{$view_url}}">
																	<img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ nl2br($product['product_name'])  }}}" alt="{{{nl2br($product['product_name'])}}}" />
                                                                    {{ CUtil::showFeaturedProductIcon($product->id, $product) }}
																</a>
															</figure>
															<span class="text-muted">{{ Lang::get('product.product_code') }}:</span> <a href="{{$view_url}}">{{ $product['product_code'] }}</a>
														</div>
													</td>
													<td>
                                                    	<p><a href="{{$view_url}}">{{{ nl2br($product['product_name']) }}}</a></p>
                                                    	@if($allow_variation && $product->use_variation > 0 && $product->variation_group_id > 0)
                                                            <p class="label label-success" title="{{ Lang::get('variations::variations.variation_available_tip') }}"> {{ Lang::get("variations::variations.variation_available") }}</p>
                                                        @endif
                                                    </td>
													<td>
														@if($product['is_free_product'] == 'Yes')
															<strong class="text-success">{{ Lang::get('common.free') }}</strong>
														@else
															<div class="dl-horizontal-new dl-horizontal">
				                                            	<!--- THIS BLOCK SPAN GAVE FOR COLON --->
				                                                <dl>
				                                                    <dt>{{ trans('admin/productList.product_purchase_price') }}</dt>
				                                                    <dd><span class="fnt10">{{ CUtil::convertAmountToCurrency($product->purchase_price, Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
				                                                </dl>
				                                                @if(count($price_group) > 0)
					                                                <dl>
					                                                    <dt>{{ trans('admin/productList.product_product_price') }}</dt>
					                                                    <dd><span class="fnt10">{{ CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
					                                                </dl>
					                                                @if($price_group['price'] > $price_group['discount'])
					                                                    <dl>
					                                                        <dt><span title="{{ trans('admin/productList.product_discount_price') }}">{{ trans('admin/productList.product_discount_price') }}</span></dt>
					                                                        <dd>
																				<span class="fnt10">{{ CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span>
																			</dd>
					                                                    </dl>
					                                                @endif
					                                            @endif
				                                            </div>
														@endif
													</td>
													<td>
                                                    @if($allow_variation && $product->use_variation > 0 && $product->variation_group_id > 0)
                                                        <a href="{{ Request::root().'/variations/vartion-stock/'.$product['id'] }}" class="view-stock-sales">{{ Lang::get("variations::variations.click_here") }}</span>
                                                    @else
                                                    	{{ $product['product_sold'] }}
                                                    @endif
                                                    </td>
													<td>
														<?php
															$display_product_status = "";
															$lbl_class = "";
															if($product['product_status'] == "ToActivate") {
																$display_product_status =  Lang::get('product.pending_approval_label');
																$lbl_class ="label-warning";
															}
															elseif($product['product_status'] == "NotApproved") {
																$display_product_status =  Lang::get('product.rejected_label');
																$lbl_class ="label-danger";
															}
															elseif($product['product_status'] == "Deleted") {
																$display_product_status =  Lang::get('common.deleted');
																$lbl_class ="label-danger";
															}
															elseif($product['product_status'] == "Ok" && $product['date_expires'] != "9999-12-31 00:00:00" && date('Y-m-d', strtotime($product['date_expires'])) < date('Y-m-d')) {
																$display_product_status =  Lang::get('common.expired');
																$lbl_class ="label-warning";
															}
															elseif($product['product_status'] == "Ok") {
																$display_product_status =  Lang::get('product.active');
																$lbl_class ="label-success";
															}
															else {
																$display_product_status = Lang::get('product.status_in_'.strtolower($product['product_status']));
																$lbl_class ="label-info";
															}
														?>
														<span class="label {{ $lbl_class }}">{{ $display_product_status }}</span>
													</td>
													<td>
				                                    	@if($allow_variation && $product->use_variation > 0 && $product->variation_group_id > 0)
                                                        	<a href="{{ Request::root().'/variations/vartion-stock/'.$product['id'] }}" class="view-stock-sales">{{ Lang::get("variations::variations.click_here") }}</span>
                                                        @else
															<?php
                                                                $stock_details =  $productService->getProductStocks($product['id']);
                                                            ?>
                                                            @if($stock_details)
                                                                @if(isset($stock_details['quantity']))
                                                                    <p>{{ trans('admin/productList.product_stocks') }}:
																		<strong>
																		{{ ($stock_details['quantity'] - $product->product_sold) > 0 ? $stock_details['quantity'] - $product->product_sold : 0; }}
																		</strong>
																	</p>
                                                                @endif
                                                            @else
                                                                --
                                                            @endif
                                                       @endif
				                                    </td>
													<td class="action-btn">
														@if(strtolower($product['product_status']) != 'deleted')
															<a href="{{ URL:: to('product/add?id='.$product->id) }}" title="{{ Lang::get('product.product_edit') }}" class="btn btn-xs blue">
																<i class="fa fa-edit"></i></a>
														@endif
														<a href="{{ $view_url }}" class="btn btn-xs btn-info" title="{{ Lang::get('product.product_view') }}">
														<i class="fa fa-eye"></i></a>
														@if(strtolower($product['product_status']) != 'deleted')
															<a href="javascript:void(0)" title="{{ Lang::get('product.product_delete') }}" onclick="doActionProduct('{{ $product['id'] }}', 'delete')" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
														@endif
														@if(CUtil::chkIsAllowedModule('featuredproducts'))
				                                            @if($product['is_featured_product'] == "Yes" && (strtotime($product['featured_product_expires']) >= strtotime(date('Y-m-d'))))
				                                                <p class="margintb3 label label-info"><i class="fa fa-check"></i> {{ Lang::get('featuredproducts::featuredproducts.featured') }}</p>
				                                            @elseif($product['product_status'] == "Ok")
				                                                <p class="margintb3"><a href="{{ URL::to('featuredproducts/set-as-featured?id='.$product['id']) }}" class="btn btn-xs green fn_changeStatus">{{ trans('featuredproducts::featuredproducts.set_as_featured') }}</a></p>
				                                            @endif
				                                        @endif

														@if(CUtil::chkIsAllowedModule('deals') && $product['is_free_product'] == 'No' && $product['is_downloadable_product'] == 'No' && $product['product_status'] == "Ok")
															<?php $add_deal_link = URL::to('deals/add-deal').'?setDealItem='.$product['id']; ?>
															<p class="margintb3">
																<a href="{{ $add_deal_link }}" title="{{ Lang::get('deals::deals.add_deal_link_lbl') }}" class="label label-primary">
																	{{ Lang::get('deals::deals.add_deal_to_this_link_lbl') }}
																</a>
															</p>
														@endif
													</td>
												</tr>
											@endforeach
										@else
											<tr><td colspan="6"><p class="alert alert-info">{{ Lang::get('product.products_not_found_msg') }}</p></td></tr>
										@endif
									</tbody>
								</table>
							</div>

							<!--END: PRODUCT LIST TABLE-->
							@if(count($product_list) > 0)
								<div class="text-right">
									@if(CUtil::chkIsAllowedModule('featuredproducts'))
									{{ $product_list->appends(array('search_product_code' => Input::get('search_product_code'), 'search_product_name' => Input::get('search_product_name'), 'search_product_category' => Input::get('search_product_category'), 'search_product_status' => Input::get('search_product_status'), 'srchproduct_submit' => Input::get('srchproduct_submit'), 'search_featured_product' => Input::get('search_featured_product')))->links() }}
									@else
									{{ $product_list->appends(array('search_product_code' => Input::get('search_product_code'), 'search_product_name' => Input::get('search_product_name'), 'search_product_category' => Input::get('search_product_category'), 'search_product_status' => Input::get('search_product_status'), 'srchproduct_submit' => Input::get('srchproduct_submit')))->links() }}
									@endif
								</div>
							@endif
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
						<div class="alert alert-info margin-0">
							{{ Lang::get('product.list_empty') }}
						</div>
					@endif
				@endif
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var page_name = "product_list";
		var show_search_filters = '{{ Lang::get('product.show_search_filters') }}';
		var hide_search_filters = '{{ Lang::get('product.hide_search_filters') }}';
		var product_confirm_delete = '{{ Lang::get('myProducts.product_confirm_delete') }}';
		var product_confirm_featured = '{{ Lang::get('myProducts.product_confirm_featured') }}';
		var product_confirm_unfeatured = '{{ Lang::get('myProducts.product_confirm_unfeatured') }}';
		var my_products_title = '{{ Lang::get('myProducts.my_products_title') }}';
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;
	</script>
@stop