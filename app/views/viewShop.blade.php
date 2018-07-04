@extends('base')
@section('content')
	<!-- BEGIN: ALERT BLOCK -->
	@if(!$shop_status && $viewShopServiceObj->current_user)
		@if(isset($shop_err_msg) && $shop_err_msg != '')
			<div class="alert alert-danger">{{ $shop_err_msg }}</div>
		@else
			<div class="alert alert-danger">{{ trans("shop.shopstatus_access_owner") }}</div>
		@endif
	@endif

	@if(!$shop_status && !$viewShopServiceObj->current_user)
		@if(isset($shop_err_msg) && $shop_err_msg != '')
			<div class="alert alert-danger">{{ $shop_err_msg }}</div>
		@elseif(count($shop_details) > 0 && $shop_details['shop_message'] != "")
			<div class="alert alert-danger">{{ $shop_details['shop_message'] }}</div>
		@else
			<div class="alert alert-danger">{{ trans("shop.shopstatus_access") }}</div>
		@endif
    <!-- END: ALERT BLOCK -->
	@else
    	<!-- BEGIN: SHOP PRODUCTS -->
        <div class="row">
            <div class="col-md-3 blog-sidebar sidebar-alt">
            	<!-- BEGIN: RIGHTMENU -->
                @include("viewShopRightMenu")
                <!-- END: RIGHTMENU -->
            </div>

			<div class="col-md-9 shopprdct-info">
                @if(count($shop_details) > 0)
                    <!-- BEGIN: VIEW SHOP HEADER -->
					@include("viewShopHeader")
                    <!-- END: VIEW SHOP HEADER -->

					<div id="error_msg_div"></div>

                    <!-- BEGIN: SHOP PRODUCT -->
					<div id="shop_products">
						@include("shopProduct")
					</div>
                    <!-- END: SHOP PRODUCT -->
                @else
                    <p class="alert alert-danger">{{ trans('shop.product_not_found') }}</p>
                @endif
            </div>
        </div>
        <!-- END: SHOP PRODUCTS -->
	@endif
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = "{{$logged_user_id}}";
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

		function ContactShopOwner(url){
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

	    	$("input.rating").rating({
	            starCaptions: function(val) {
	                //if (val < 5) {
	                    return val;
	                //} else {
	                //    return 'high';
	                //}
	            },
	            starCaptionClasses: function(val) {
	                if (val < 3) {
	                    return 'label label-danger';
	                } else {
	                    return 'label label-success';
	                }
	            },
	            hoverOnClear: false,
	            readonly: true,
	            showClear: false,
	            showCaption: false
	        });

			$( ".js-addto-favorite-shop").click(function() {
				$this = $(this);
				shop_id = $(this).data('shopid');
				shop_user_id = $(this).data('userid');
				if(typeof shop_id != 'undefined' && shop_id!='' && typeof shop_user_id != 'undefined' && shop_user_id!='')
				{
					postData = 'favorites=shop&shop_user_id=' + shop_user_id + '&shop_id=' + shop_id + '&user_id=' + user_id,
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
		                		text_to_disp_txt = '<i class="fa fa-heart text-pink"></i> {{ Lang::get('shop.added_to_favorites') }}';
		                    	text_to_disp_img = '<i class="fa fa-heart text-pink"></i> {{ Lang::get('shop.unfavorite') }}';
			                }else
			                {
		                    	text_to_disp_txt = '<i class="fa fa-heart text-muted"></i> {{ Lang::get('shop.add_to_favorites') }}';
		                    	text_to_disp_img = '<i class="fa fa-heart text-muted"></i> {{ Lang::get('shop.favorite') }}';
			                }
		                	$( ".fnImg").html(text_to_disp_img);
		                	$( ".fnTxt").html(text_to_disp_txt);
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