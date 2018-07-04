@extends('base')
@section('content')
	<?php $user_id = BasicCUtil::getLoggedUserId(); ?>
    <!--<h1>Products <span class="text-muted">({{$product_total_count}})</span></h1>-->
    <div class="row">
        <!-- BEGIN: SIDEBAR -->
        @include('productLeftMenu')
        <!-- END: SIDEBAR -->

		<div class="col-md-9">
            <div id="error_msg_div"></div>
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

			<div class="product-search">
                <span>
					{{Lang::get('product.we_found_items', array('total_count' => $product_total_count) )}}!
				</span>
				@if(isset($top_cat) && COUNT($top_cat) > 0)
            		- <a href="{{ $top_cat[0] }}">{{Lang::get('common.back') }}</a>
            	@endif
                <!-- BEGIN: TAB MENU -->
                    <ul class="pull-right list-unstyled">
                    	<li class="dropdown">
                            {{Lang::get('product.sort_by') }}:
							<?php
							$orderby_field = 'id';
							if (Input::has('orderby_field'))
								$orderby_field = Input::get('orderby_field');

							$sort_by_arr = $list_prod_serviceobj->populateOptionsArray();
							$first_val = $sort_by_arr[0]['innertext'];
							foreach($sort_by_arr as $sortKey => $sort) {
								if($orderby_field == $sort['innervalue'])
									$first_val = $sort['innertext'];
							}
							?>
                            <a href="#" data-toggle="dropdown">{{ $first_val }} <i class="fa fa-caret-down"></i></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                @foreach($sort_by_arr as $sortKey => $sort)
                                	<li @if($orderby_field == $sort['innervalue']) class="active" @endif><a href="{{ $sort['href'] }}">{{ $sort['innertext'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                <!-- END: TAB MENU -->
            </div>
            <!-- BEGIN: PRODUCTS LISTS -->
            @if(count($product_details) > 0)
                <?php $shop_details = array(); ?>
                <!-- BEGIN: LIST VIEW -->
                <div id="js-product-view">
                    <ul class="row list-unstyled list-response">
                        @foreach($product_details as $productKey => $product)
                            <li class="col-md-4 col-sm-4 col-xs-4">
                            	<div class="bs-prodet product-gridview">
                            		{{ CUtil::showFeaturedProductIcon($product['id'], $product); }}
                                    <ul id="fav_block_{{ $product['id'] }}" class="list-unstyled favset-icon">
										<?php
											$is_favorite_prod = $prod_fav_service->isFavoriteProduct($product['id'], $user_id);
											$login_url = URL::to('users/login?form_type=selLogin');
										?>
                                        <li>
	                                        @if(CUtil::isMember())
						                        @if($is_favorite_prod)
						                    		<a href="javascript:void(0);" data-productid="{{$product['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-pink"></i></a>
						                    	@else
						                    		<a href="javascript:void(0);" data-productid="{{$product['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-muted"></i></a>
						                    	@endif
						                    @else
						    					<a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart"></i></a>
						                    @endif
					                    </li>
                                        <li class="dropdown">
                                        	@if(CUtil::isMember())
												<a href="javascript:void(0);" id="fav_list_{{$product['id']}}" data-productid="{{$product['id']}}" data-toggle="dropdown" class="js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
												<div id="fav_list_holder_{{$product['id']}}" class="dropdown-menu custom-dropdown fnFavListHolder">
												<div>
											@else
						    					<a href="{{ $login_url }}" class="fn_signuppop"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
						                    @endif
                                        </li>
                                    </ul>
									<?php
                                        $seller_id = $product['product_user_id'];
                                        if (!isset($shop_details_array[$seller_id]))
                                            $shop_details_array[$seller_id] = $shop_obj->getShopDetails($seller_id);
                                        $shop_details = $shop_details_array[$seller_id];
                                        $seller_name = "";

                                        $products = Products::initialize();
                                        $p_img_arr = $products->getProductImage($product['id']);
                                        $p_thumb_img = $list_prod_serviceobj->getProductDefaultThumbImage($product['id'], 'thumb', $p_img_arr);
                                        //$price = $list_prod_serviceobj->formatProductPrice($product);
                                        $price = $list_prod_serviceobj->formatProductPriceNew($product);
                                        $view_url = $list_prod_serviceobj->getProductViewURL($product['id'], $product);
                                    ?>
                                    <p class="prodet-img">
                                    	{{ CUtil::showFeaturedProductIcon($product['id'], $product) }}
										<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a>
									</p>
                                    <div class="prodet-info">
                                        <h3><a href="{{$view_url}}" title="{{{ $product['product_name']  }}}">{{{ $product['product_name'] }}}</a></h3>
                                        <!--<p>{{{ $product['product_highlight_text'] }}}</p>-->
                                        @if($product['is_free_product'] == 'Yes')
                                            <span class="pull-right">
                                                <strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong>
                                            </span>
                                        @else
                                            @if($price['disp_price'] && $price['disp_discount'])
                                                <p class="pull-right">{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>

                                            @elseif($price['disp_price'])
                                                @if($price['product']['price'] > 0)
                                                    <p class="pull-right">{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                                @else
                                                    <span class="pull-right"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></span>
                                                @endif
                                            @endif
                                        @endif
										<a href='{{$shop_details['shop_url']}}' target="_blank" class="bs-title">{{ $shop_details['shop_name'] }}</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <!-- END: LIST VIEW -->
            @else
                <p class="alert alert-info">{{ Lang::get('product.product_not_found') }}</p>
            @endif
            <!-- END: PRODUCTS LISTS -->

            @if(count($product_details) > 0)
                <div class="text-right">{{ $product_details->appends(array('cat_search' => Input::get('cat_search'), 'author_search' => Input::get('author_search'), 'tag_search' => Input::get('tag_search'), 'shop_search' => Input::get('shop_search'), 'price_range_start' => Input::get('price_range_start'), 'price_range_end' => Input::get('price_range_end'), 'orderby_field' => Input::get('orderby_field'), 'search_products' => Input::get('search_products')))->links() }}</div>
            @endif
        </div>
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = {{$user_id}};
		var favorite_product_url = "{{URL::action('FavoritesController@postToggleFavorite')}}";
		var favorite_product_list_url = "{{URL::action('FavoritesController@postToggleFavoriteList')}}";
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

		function clearForm(oForm)
		{
			var elements = oForm.elements;
			oForm.reset();
			$('#cat_search').val();
			$('#cat_search').attr("checked", 'false');
			$('#cat_search').click();

			for(i=0; i<elements.length; i++)
			{
		 		field_type = elements[i].type.toLowerCase();

			   	switch(field_type)
			 	{
					case "text":
					case "textarea":
					  	elements[i].value = "";
					break;
					case "checkbox":
						if (elements[i].checked)
						{
							elements[i].checked = false;
						}
					break;
					case "select-one":
					case "select-multi":
					case "select-multiple":
						elements[i].selectedIndex = -1;
					break;
				}
		    }
			document.productSearchfrm.submit();
		}

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