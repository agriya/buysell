@extends('base')
@section('content')
    {{-- Breadcrums start
        {{ Breadcrumbs::render('view-profile', $breadcrumb_arr) }}
    Breadcrums end --}}
	<div class="row">
		<div class="col-md-12">
			<div id="error_msg_div"></div>
            <!-- BEGIN: FAVORITE BLOCK -->
            <div class="row user-profile">
            	@if(CUtil::isShopOwner($user_id))
					<?php
						$shop_details = $shop_obj->getShopDetails($user_id);
						if(count($shop_details) > 0) {
							$seller_location = CUtil::getSellerCityCountry($shop_details);
						}
					?>
				@endif
                <!-- BEGIN: LEFT USER PROFILE  FAVORITE BLOCK -->
                <div class="col-md-3 blog-sidebar sidebar-alt">
                	<div class="well">
                        <div class="well-new">
                            <div class="shopuser-profile">
                            	@if(isset($shop_details) && count($shop_details) > 0)
                        		<div class="clearfix">
							        {{ CUtil::showFeaturedSellersIcon($shop_details['user_id'], $shop_details) }}
							    </div>
							    @endif
                                <a href='{{$user_details['profile_url']}}' class="imguserborsm-56X56">
									<img  src="{{ $user_image_details['image_url'] }}" {{ $user_image_details['image_attr'] }} alt="{{ $user_details['display_name'] }}" title="{{ $user_details['display_name'] }}" />
								</a>
                                <h3><a href='{{$user_details['profile_url']}}'>{{ $user_details['display_name'] }}</a></h3>
                                @if(isset($seller_location) && count($seller_location) > 0)
                                	<p><span>{{ implode(', ', $seller_location) }}</span></p>
                                @endif
                            </div>
                            <ul class="list-unstyled no-margin">
                                <li>
                                    @if($logged_user_id == $user_id )
                                        <!--{{ HTML::link(URL::to('users/myaccount'), trans('myaccount/viewProfile.edit'), array()) }}-->
                                        <a href="{{URL::to('users/myaccount')}}">{{ trans('myaccount/viewProfile.edit') }}</a>
                                    @endif

                                    @if(BasicCUtil::sentryCheck())
                                        @if($logged_user_id != $user_id )
                                            <a href="{{Url::to('shop/user/message/add/'.$user_details['user_code']) }}" class="fn_signuppop">
                                            {{ trans('myaccount/viewProfile.contact') }}</a>
                                        @endif
                                    @else
                                        <?php $login_url = URL('users/login?form_type=selLogin'); ?>
                                        <a href="{{$login_url}}" class="fn_signuppop">{{ trans('myaccount/viewProfile.contact') }}</a>
                                    @endif
                                </li>
                                <li><a href="{{$user_details['profile_url']}}">{{ trans('myaccount/viewProfile.profile') }}</a></li>
                                <li class="active"><a href="{{URL::action('FavoritesController@viewFavorites', $user_details['user_code'])}}">
								{{ trans('myaccount/viewProfile.favorites') }}</a></li>
                                <?php $currmembersincircle = $userCircleService->numberOfMembersInCircle($user_id); ?>
                                <li>
                                    <a href="{{URL::action('UsersCircleController@viewCircle', $user_details['user_code'])}}">
                                    {{ trans('myaccount/viewProfile.circles') }} <span>{{$currmembersincircle}}</span></a>
                                </li>

                                @if($logged_user_id != $user_id )
                                    <?php $isUserInCircle =  $userCircleService->isUserInCircle($user_id, $logged_user_id); ?>
                                    @if($isUserInCircle)
                                        <li><a class="js-addto-circle" href="javascript:;">
										<i class="fa fa-times-circle text-danger"></i> {{ trans('myaccount/viewProfile.in_your_circle') }}</a></li>
                                    @else
                                        <li><a class="js-addto-circle" href="javascript:;">
										<i class="fa fa-plus-circle text-success"></i> {{ trans('myaccount/viewProfile.add_to_circle') }}</a></li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if(CUtil::isShopOwner($user_id))
	                    @if(isset($shop_details) && count($shop_details) > 0)
	                    <div class="userprfl-shop">
	                        <h2>{{ trans('myaccount/viewProfile.shop') }}</h2>
	                        <h3><a href="{{ $shop_details['shop_url'] }}" title="{{ $shop_details['shop_name'] }}">{{ $shop_details['shop_name'] }}</a></h3>
	                        @if($shop_details['shop_slogan'] != '')
	                        	<p>{{ $shop_details['shop_slogan'] }}</p>
	                        @endif
	                    </div>
	                    @endif
                    @endif
                </div>
                <!-- END: LEFT USER PROFILE  FAVORITE BLOCK -->

                <!-- BEGIN: RIGHT USER PROFILE FAVORITE BLOCK -->
                <div class="col-md-9 mainbar featured-icon">
                    @include('leftViewFavoriteBlock')
                </div>
                <!-- END: RIGHT USER PROFILE FAVORITE BLOCK -->
            </div>
            <!-- END: FAVORITE BLOCK -->
		</div>
	</div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = {{$logged_user_id}};
		var user_circle_url = '{{URL::action('UsersCircleController@postToggleUserCircle')}}';
		var wishlist_actions_url = '{{ URL::action('WishlistController@postToggleWishlist')}}';
		var circle_user_id = {{$user_id}};
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

	    function showErrorDialog(err_data)
		{
			var err_msg ='<div class="note note-danger">'+err_data.error_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}
		function showSuccessDialog(err_data)
		{
			var err_msg ='<div class="note note-success">'+err_data.success_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}

		function removeErrorDialog()
		{
			$('#error_msg_div').html('');
		}

	    $( ".js-addto-circle").click(function() {
			$this = $(this);
			if($(this).data('productid')!='')
			{
				postData = 'action=toggle&user_id=' + user_id + '&circle_user_id=' + circle_user_id,
				displayLoadingImage(true);
				$.post(user_circle_url, postData,  function(response)
				{
					hideLoadingImage (false);

					data = eval( '(' +  response + ')');

					if(data.result == 'success')
					{
						removeErrorDialog();
                        var act = data.action_to_show;
                        var text_to_disp = '';
                        if(act == "remove")
                            text_to_disp = '<i class="fa fa-times-circle text-danger"></i> In your circle ';
                        else
                            text_to_disp = '<i class="fa fa-plus-circle text-success"></i> Add to circle ';
						$this.html(text_to_disp);
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
		});

		$( ".js-addto-wishlist").click(function() {
			$this = $(this);
			if($(this).data('productid')!='')
			{
				postData = 'action=toggle&user_id=' + user_id + '&product_id='+$(this).data('productid'),
				displayLoadingImage(true);
				$.post(wishlist_actions_url, postData,  function(response)
				{
					hideLoadingImage (false);

					data = eval( '(' +  response + ')');

					if(data.result == 'success')
					{
						removeErrorDialog();
                        var act = data.action_to_show;
                        var text_to_disp = '';
                        if(act == "remove")
                            text_to_disp = '<i class="fa fa-minus-square"></i>{{Lang::get("viewProduct.remove_from_wishlist")}}';
                        else
                            text_to_disp = '<i class="fa fa-plus-square"></i>{{Lang::get("viewProduct.add_to_wishlist")}}';
						$this.html(text_to_disp);
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
		});

		var add_list_url = "{{URL::action('FavoritesController@postAddFavoriteList')}}";
		$(document).ready(function() {
			$('#new_list_opener').click(function(){
				$('#new_list_create').show();
				$(this).hide();
			});

			$('#new_list_close').click(function(){
				$('#new_list_create').hide();
				$('#new_list_opener').show();
			});

			$('.clsAddList').click(function(){
				var list_name = $.trim($('#list_name').val());
				if(list_name == ''){
					bootbox.alert("{{ Lang::get('common.please_enter_fav_name') }}");
					return false;
				}
				postData = 'user_id=' + user_id + '&list_name=' + urlencode(list_name),
				displayLoadingImage(true);
				$.post(add_list_url, postData,  function(response)
				{
					hideLoadingImage (false);

					data = eval( '(' +  response + ')');
					if(data.result == 'success')
					{
						window.location.reload(true);
						//showSuccessDialog({status: 'success', success_message: data.success_msg});
					}
					else
					{
						showErrorDialog({status: 'error', error_message: data.error_msg});
						var body = $("html, body");
			            body.animate({scrollTop:0}, '500', 'swing', function() {
			            });
					}
				}).error(function() {
					hideLoadingImage (false);
					showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
				});
			});

		});
	</script>
@stop