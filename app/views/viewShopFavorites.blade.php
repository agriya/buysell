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
    	<!-- BEGIN: SHOP PRODUCTS STARTS -->
        <div class="row">
            <div class="col-md-3 blog-sidebar sidebar-alt">
                <!-- BEGIN: SHOP OWNER DETAILS -->
				<div class="well">
				    <h4>{{ trans("shop.shop_owner") }}</h4>
				    <div class="well-new">
						<?php
				            $user_details = CUtil::getUserDetails($shop_details['user_id']);
				            $user_image = CUtil::getUserPersonalImage($shop_details['user_id'], "thumb", false);
				        ?>
				        <div class="shopuser-profile">
				        	<div class="clearfix">
								{{ CUtil::showFeaturedSellersIcon($shop_details['user_id'], $shop_details) }}
							</div>
				            <a href='{{ $shop_view_url }}' class="imguserborsm-56X56">
							<img  src="{{ $user_image['image_url'] }}" alt="{{ $shop_details['shop_name'] }}" title="{{ $shop_details['shop_name'] }}" /></a>
				            <h3><a href='{{ $shop_view_url }}'>{{ $shop_details['shop_name'] }}</a></h3>
				            <p class="no-margin"><span>{{{ $shop_details['shop_slogan'] }}}</span></p>
				        </div>
				    </div>
				</div>
				<!-- END: SHOP OWNER DETAILS -->

				<!-- BEGIN: SHOP FAVORITE DETAILS -->
				<div class="well">
					<div class="fav-btn well-new">
						@if(CUtil::isMember())
							<?php $isFavoriteShop = $shop_fav_service->isFavoriteShop($shop_details['id'], $logged_user_id);  ?>
							@if($isFavoriteShop)
								<a href="javascript:void(0);" data-shopid="{{$shop_details['id']}}" data-userid="{{$shop_details['user_id']}}"   class="btn btn-default btn-block js-addto-favorite-shop fnImg"><i class="fa fa-heart text-pink"></i> {{trans('shop.unfavorite')}}</a>
							@else
								<a href="javascript:void(0);" data-shopid="{{$shop_details['id']}}" data-userid="{{$shop_details['user_id']}}"  class="btn btn-default btn-block js-addto-favorite-shop fnImg"><i class="fa fa-heart text-muted"></i> {{trans('shop.favorite')}}</a>
							@endif
						@else
							<?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
							<a href="{{ $login_url }}" class="fn_signuppop btn btn-default btn-block"><i class="fa fa-heart text-muted"></i> {{trans('shop.favorite')}}</a>
						@endif
					</div>
				</div>
				<!-- END: SHOP FAVORITE DETAILS -->
            </div>
			<div class="col-md-9 shopprdct-info">
                @if(count($shop_details) > 0)
                    <!-- BEGIN: PRODUCT DETAILS -->
                    <div id="shop_products">
						@include('listWhoFavoriteThisShop')
                    </div>
                    <!-- END: PRODUCT DETAILS -->
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
							//removeErrorDialog();
			                var act = data.action_to_show;
			                var text_to_disp = '';
			                var favorite_text_msg = '';
			                if(act == "remove")
			                {
		                    	text_to_disp_img = '<i class="fa fa-heart text-pink"></i> {{ Lang::get('shop.unfavorite') }}';
			                }else
			                {
		                    	text_to_disp_img = '<i class="fa fa-heart text-muted"></i> {{ Lang::get('shop.favorite') }}';
			                }
		                	$( ".fnImg").html(text_to_disp_img);
							//showSuccessDialog({status: 'success', success_message: data.success_msg});
						}
						else
						{
							//showErrorDialog({status: 'error', error_message: data.error_msg});
						}
					}).error(function() {
						hideLoadingImage (false);
						//showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
					});
				}
			});
		});
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
	</script>
@stop