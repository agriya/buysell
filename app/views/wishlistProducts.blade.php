@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('wishlistProduct.my_wishlist') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			<!-- ALERT BLOCK STARTS -->
			<div id="error_msg_div"></div>
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif

			<div class="well">
				@if(count($product_list) <= 0)
					<div class="note note-info margin-0">
					   {{ Lang::get('wishlistProduct.list_empty') }}
					</div>
				<!-- ALERT BLOCK ENDS -->
				@else
					<!-- MY WISHLIST DETAILS STARTS -->
					@if(count($product_list) > 0)
						{{ Form::open(array('action' => array('ProductController@productList'), 'id'=>'productFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
							<div class="table-responsive margin-bottom-30">
								<table class="table table-bordered table-hover table-striped" id="js-wishlist-table">
									<thead>
										<tr>
											<th class="col-md-2">{{ Lang::get('wishlistProduct.product_code') }}</th>
											<th>{{ Lang::get('wishlistProduct.product_name') }}</th>
											<th>{{ Lang::get('wishlistProduct.product_price') }}</th>
											<th>{{ Lang::get('wishlistProduct.action') }}</th>
										</tr>
									</thead>

									<tbody>
										@if(count($product_list) > 0)
											@foreach($product_list as $product)
												<?php //print_r($product);exit; ?>
												<tr id="wishlist_tr_{{$product['id']}}">
													<?php
														$p_img_arr = $prod_obj->getProductImage($product['id']);
														$p_thumb_img = $productService->getProductDefaultThumbImage($product['id'], 'thumb', $p_img_arr);
														$view_url = $productService->getProductViewURLNew($product['id'], $product);
														//$edit_url = URL::to('products/add?id='.$product->id);
													?>
													<td>
														<figure class="margin-bottom-5 custom-image">
																<a href="{{$view_url}}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif title="{{{ nl2br($product['product_name'])  }}}" alt="{{{nl2br($product['product_name'])}}}" /></a>
														</figure>
														<p><span class="text-muted">{{ Lang::get('wishlistProduct.product_code') }}:</span> {{ $product['product_code'] }}</p>
													</td>
													<td><span><a class="text-primary" href="{{$view_url}}">{{{ nl2br($product['product_name']) }}}</a></span></td>
													<?php
														$price_group = $productService->getPriceGroupsDetailsNew($product['id'], $user_id);

														$product_price = $product_price_arr = '';
														$discount_price = 0;
														$have_discount = false;
														if(count($price_group) > 0 && isset($price_group['price']) && $price_group['price']!='' ) {
															$shipping_total_price = $price_group['price'];
															$product_price = CUtil::getCurrencyBasedAmount($price_group['price'], $price_group['price_usd'], $price_group['currency']);
															$product_price_arr = CUtil::getCurrencyBasedAmount($price_group['price'], $price_group['price_usd'], $price_group['currency'], true);
															if($price_group['discount'] > 0 && $price_group['price'] > $price_group['discount']) {
																$have_discount = true;
															}
															if($have_discount) {
																$shipping_total_price = $price_group['discount'];
																$discount_price = CUtil::getCurrencyBasedAmount($price_group['discount'], $price_group['discount_usd'], $price_group['currency']);
																$discount_price_arr = CUtil::getCurrencyBasedAmount($price_group['discount'], $price_group['discount_usd'], $price_group['currency'], true);
															}
														}
													?>
													<td>
														@if($product['is_free_product'] == 'Yes')
															<div class="text-success">{{ trans('viewProduct.free') }}</div>
														@else
															<div class="pricelist-product">
																@if($discount_price)
																	<?php $subtotal_price_arr = $discount_price_arr;?>
																	<p class="strike-out" id="finalprice_org">{{ $product_price }}</p>
																	<p class="text-success" id="finalprice">{{ $discount_price }}</p>
																@else
																	<?php $subtotal_price_arr = $product_price_arr;?>
																	<p id="finalprice">{{ $product_price }}</p>
																@endif
															</div>
														@endif
													</td>
													<td class="action-btn">
														<a href="{{ $view_url }}" title="{{ Lang::get('wishlistProduct.product_view') }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
														<a href="javascript:;" title="{{ trans("viewProduct.remove_from_wishlist") }}" data-productid="{{$product['id']}}" class="js-addto-wishlist btn btn-xs red " id="fn_dialog_confirm" action="Delete"><i class="fa fa-trash-o"></i></a>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td colspan="6"><p class="alert alert-info">sdfs{{ Lang::get('wishlistProduct.products_not_found_msg') }}</p></td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
							@if(count($product_list) > 0)
								<div class="text-right">
									{{ $product_list->appends(array('search_product_code' => Input::get('search_product_code'), 'search_product_name' => Input::get('search_product_name'), 'search_product_category' => Input::get('search_product_category'), 'search_product_status' => Input::get('search_product_status'), 'srchproduct_submit' => Input::get('srchproduct_submit')))->links() }}
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
						<div class="note note-info">
						   {{ Lang::get('wishlistProduct.list_empty') }}
						</div>
					@endif
					<!-- MY WISHLIST DETAILS ENDS -->
				@endif
			</div>

			<div id="dialog-product-confirm" title="" style="display:none;">
				<span class="ui-icon ui-icon-alert"></span>
				<span id="dialog-product-confirm-content" class="show">
				Are you sure want to delete wishlist?</span>
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var user_id = {{BasicCUtil::getLoggedUserId()}};
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var wishlist_actions_url = '{{ URL::action('WishlistController@postToggleWishlist')}}';
		function showErrorDialog(err_data)
		{
			var err_msg ='<div class="alert alert-danger">'+err_data.error_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}
		function showSuccessDialog(err_data)
		{
			var err_msg ='<div class="alert alert-success">'+err_data.success_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}
		function removeErrorDialog()
		{
			$('#error_msg_div').html('');
		}

	    $( ".js-addto-wishlist").click(function() {
	    var product_id = $(this).data('productid');
	    $('#dialog-product-confirm').html("Are you sure want to delete wishlist?");
	    	$("#dialog-product-confirm").dialog({ title: cfg_site_name, modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$(this).dialog("close");
					    	var rowCount = $('#js-wishlist-table tbody tr').length - 1;
							$this = $(this);
							if(product_id!='')
							{
								//var product_id = $(this).data('productid');
								postData = 'action=toggle&user_id=' + user_id + '&product_id='+product_id,
								displayLoadingImage(true);
								$.post(wishlist_actions_url, postData,  function(response)
								{
									hideLoadingImage (false);

									data = eval( '(' +  response + ')');

									if(data.result == 'success')
									{
										removeErrorDialog();
										$('#wishlist_tr_'+product_id).remove();

										var rowCount = $('#js-wishlist-table tbody tr').length;
										if(rowCount <=0)
										{
											location.reload();
										}
										showSuccessDialog({status: 'success', success_message: data.success_msg});
									}
									else
									{
										showErrorDialog({status: 'error', error_message: data.error_msg});
									}
								}).error(function() {
									hideLoadingImage (false);
									showErrorDialog({status: 'error', error_message: 'There are some problem. Please try againg later.'});
								});
							}
					},"{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
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
	</script>
@stop