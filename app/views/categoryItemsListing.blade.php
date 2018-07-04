@extends('base')
@section('content')
	<?php $user_id = BasicCUtil::getLoggedUserId(); ?>
	<div class="bs-catgpg">
		<div id="error_msg_div"></div>
        <div class="bs-example bs-example-tabs">
            <div class="bs-tabmenu">
                <p class="btn default"><i class="fa fa-list margin-right-5"></i> View Menu <i class="fa fa-angle-down ml10"></i></p>
                <ul id="myTab" class="nav nav-tabs text-center bs-tabs" role="tablist">
                    <li @if($order_by == 'all')class="active"@endif><a href="{{ $product_service->urlLink('') }}?order_by=all">{{Lang::get('product.all_items')}}</a></li>
                    <li @if($order_by == 'recently_added')class="active"@endif><a href="{{ $product_service->urlLink('') }}?order_by=recently_added">{{Lang::get('product.product_listing_id')}}</a></li>
                    <li @if($order_by == 'most_sold')class="active"@endif><a href="{{ $product_service->urlLink('') }}?order_by=most_sold">{{Lang::get('product.product_listing_download')}}</a></li>
                    <li @if($order_by == 'price')class="active"@endif><a href="{{ $product_service->urlLink('') }}?order_by=price">{{Lang::get('product.price_tab')}}</a></li>
                    <li @if($order_by == 'free')class="active"@endif><a href="{{ $product_service->urlLink('') }}?order_by=free">{{Lang::get('common.free')}}</a></li>
                </ul>
            </div>
            <div id="myTabContent" class="tab-content bs-content catg-item">
                <div class="tab-pane fade active in">
                	@if(count($products) > 0)
                		<?php
							$display_items_records_per_row = 4;
							$count = count($products);
							$r = 0;
							$products_arr = $products->getItems();
						?>
	                    <ul class="row list-unstyled">
	                    	@for ($outer = 0; $outer < $display_items_records_per_row; $outer++)
		                    	<li class="col-md-3 col-sm-4">
		                    		<?php
		                    		$i = $outer;
									$temp = @(($count - $r) / ($display_items_records_per_row - $i));
									$limit = @(round( ($count-$r) / ($display_items_records_per_row - $i) ));
									if($temp > $limit) {
					                    $limit = $limit + 1;
					                }
					                ?>
					                @for ($inner = 0; $inner < $limit; $inner++)
					                	<?php
										$j = $inner;
										$arr_count = $j + $r;
										$product = $products_arr[$arr_count];
										$product_id = $product->id;
										$product_name = $product->product_name;
										$product_url = $product_service->getProductViewURL($product_id, $product);
										$price = $product_service->formatProductPriceNew($product);
										$is_free_product = $product->is_free_product;
										$p_img_arr = $prod_obj->getProductImage($product_id);
										$p_thumb_img = $product_service->getProductDefaultThumbImage($product_id, 'large', $p_img_arr);
										$shop_details = $shop_obj->getShopDetails($product->product_user_id);
										$shop_name = isset($shop_details['shop_name']) ? $shop_details['shop_name'] : '';
										$shop_url = isset($shop_details['shop_url']) ? $shop_details['shop_url'] : '#';
										?>
			                            <div class="bs-prodet">
			                            	{{ CUtil::showFeaturedProductIcon($product['id'], $product); }}
			                                <ul id="fav_block_{{ $product_id }}" class="list-unstyled favset-icon">
												<?php
													$prod_fav_service = new ProductFavoritesService();
													$is_favorite_prod = $prod_fav_service->isFavoriteProduct($product_id, $user_id);
													$login_url = URL::to('users/login?form_type=selLogin');
												?>
			                                    <li>
			                                        @if(CUtil::isMember())
								                        @if($is_favorite_prod)
								                    		<a href="javascript:void(0);" data-productid="{{$product_id}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-pink"></i></a>
								                    	@else
								                    		<a href="javascript:void(0);" data-productid="{{$product_id}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-muted"></i></a>
								                    	@endif
								                    @else
								    					<a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart"></i></a>
								                    @endif
							                    </li>
			                                    <li class="dropdown">
			                                    	@if(CUtil::isMember())
														<a href="javascript:void(0);" id="fav_list_{{$product_id}}" data-productid="{{$product_id}}" data-toggle="dropdown" class="js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
														<div id="fav_list_holder_{{$product_id}}" class="dropdown-menu custom-dropdown fnFavListHolder">
														<div>
													@else
								    					<a href="{{ $login_url }}" class="fn_signuppop"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
								                    @endif
			                                    </li>
			                                </ul>
			                                <div class="prodet-img">
			                                	<a href="{{ $product_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} alt="{{ htmlentities($product_name) }}" /></a>
			                                </div>
			                                <div class="prodet-info">
			                                    <h3><a href="{{ $product_url }}">{{CUtil::wordWrap(htmlentities($product_name), 30) }}</a></h3>
												<p class="pull-right">
				                                    @if($is_free_product == 'Yes')
			                                            {{ Lang::get('common.free') }}
			                                        @else
			                                            @if($price['disp_price'] && $price['disp_discount'])
				                                            {{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}
			                                            @elseif($price['disp_price'])
			                                                @if($price['product']['price'] > 0)
			                                                    {{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}
			                                                @else
			                                                    {{ Lang::get('common.free') }}
			                                                @endif
			                                            @endif
			                                        @endif
		                                        </p>
												<a href="{{ $shop_url }}" class="bs-title" title="{{ $shop_name }}">{{ $shop_name }}</a>
			                                </div>
			                            </div>
									@endfor
									<?php
									$r = $r + $j + 1;
									?>
								</li>
							@endfor
						</ul>
					@else
                    	<div class="alert alert-info no-margin">{{Lang::get('product.product_not_found')}}</div>
					@endif
                </div>
            </div>
        </div>
        <!-- Pagination Starts Here -->
        <!-- <ul class="pager">
          <li>
		  	<a href="#"><i class="fa fa-chevron-left"></i> <span class="disable-txt">Previous</span></a>
		  </li>
          <li class="active"><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li><a href="#">...</a></li>
          <li><a href="#">16</a></li>
          <li class="last-li">
		  	<a href="#"><span class="disable-txt">Next</span> <i class="fa fa-chevron-right"></i></a>
		  </li>
        </ul> -->
        @if(count($products) > 0)
			<div>
				{{ $products->appends(array('order_by' => Input::get('order_by')))->links() }}
			</div>
        @endif
        <!-- Pagination Ends Here -->
    </div>
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = {{$user_id}};
		var favorite_product_url = "{{URL::action('FavoritesController@postToggleFavorite')}}";
		var favorite_product_list_url = "{{URL::action('FavoritesController@postToggleFavoriteList')}}";

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
		$(document).ready(function() {
			$( ".js-addto-favorite-heart").click(function() {
				$this = $(this);
				if($(this).data('productid')!='')
				{
					postData = 'favorites=product&user_id=' + user_id + '&product_id=' + $(this).data('productid'),
					displayLoadingImage(true);
					$.post(favorite_product_url, postData,  function(response)
					{
						hideLoadingImage (false);

						data = eval( '(' +  response + ')');
						if(data.result == 'success')
						{
							removeErrorDialog();
			                var act = data.action_to_show;
			                var text_to_disp = '';
			                var favorite_text_msg = '';
			                if(act == "remove")
			                {
			                    text_to_disp = '<i class="fa fa-heart text-pink"></i>';
			                }else
			                {
			                    text_to_disp = '<i class="fa fa-heart text-muted"></i>';
			                }
			                $this.html(text_to_disp);
							//showSuccessDialog({status: 'success', success_message: data.success_msg});
						}
						else
						{
							showErrorDialog({status: 'error', error_message: data.error_msg});
						}
					}).error(function() {
						hideLoadingImage (false);
						showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
					});
				}
			});

			$( ".js-show-list").click(function() {
				$this = $(this);
				if($(this).data('productid')!='') {
					var product_id = $(this).data('productid');
					//alert($('#fav_list_holder_'+product_id).css('display'));
					if($('#fav_list_holder_'+product_id).css('display') == 'none')
					{
						//$('#fav_block_'+product_id).css('display', 'block');
						$('#fav_list_holder_'+product_id).show();
						$('#fav_list_holder_'+product_id).html('<img src="{{URL::asset('images/general/loading.gif')}}" alt="loading" />');
						postData = 'user_id=' + user_id + '&product_id=' + product_id,
						//displayLoadingImage(true);
						$.post(favorite_product_list_url, postData,  function(response)
						{
							//hideLoadingImage (false);

							data_arr = response.split('|~~|');

							if(data_arr[0] == 'success')
							{
								//removeErrorDialog();
								$('#fav_list_holder_'+product_id).html(data_arr[1]);
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data_arr[1]});
							}
						}).error(function() {
							//hideLoadingImage (false);
							showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
						});
					}
					else {
						$('#fav_list_holder_'+product_id).html("").hide();
					}
				}
			});

			$(document).mouseup(function (e) {
			    var container = $(".fnFavListHolder");
			    if (!container.is(e.target) // if the target of the click isn't the container...
			        && container.has(e.target).length === 0) {
			        container.hide();
			    }
			});
		});
	</script>
@stop