@extends('base')
@section('content')
	{{-- Breadcrums start
	{{ Breadcrumbs::render('view-profile', $breadcrumb_arr) }}
	Breadcrums end --}}
	<div class="row">
		<div class="col-md-12">
			<div id="error_msg_div"></div>
			@if($d_arr['error_msg'] != '')
				<div class="note note-danger">{{ $d_arr['error_msg'] }}</div>
			@else
                <!-- USER PROFILE STARTS -->
                <div class="row user-profile">
                    <!-- LEFT USER PROFILE BLOCK STARTS -->
                    <div class="col-md-3 blog-sidebar sidebar-alt">
                    	<div class="well">
                            <div class="well-new">
                            	<div class="shopuser-profile">
                            		@if($user_arr['is_shop_owner'])
                                        <div class="clearfix">
                                            {{ CUtil::showFeaturedSellersIcon($user_arr['id'], $user_arr) }}
                                        </div>
								    @endif
                                    <a href='{{$user_details['profile_url']}}' class="imguserborsm-56X56">
                                    	<img src="{{ $user_image_details['image_url'] }}" {{ $user_image_details['image_attr'] }} alt="{{ $user_details['display_name'] }}" title="{{ $user_details['display_name'] }}" />
									</a>
                                    <h3>
										<a href='{{$user_details['profile_url']}}'>{{ $user_details['display_name'] }}</a>
										<span title="User Name" class="text-muted fonts12">({{ $user_details['user_name'] }})</span>
									</h3>
                                    @if($user_arr['is_shop_owner'])
										<?php
                                            $shop_obj->setIncludeBlockedUserShop(true);
                                            $shop_details = $shop_obj->getShopDetails($user_id);
                                        ?>
                                        <?php $seller_location = CUtil::getSellerCityCountry($shop_details); ?>
                                        <p><span>{{ implode(', ', $seller_location) }}</span></p>
                                    @endif
                                </div>
                                {{--URL::asset('/images/no_image/no-maleprofile_T.jpg')--}}
                                <!-- THE IMAGE URL IS DUMMY
                                <a href='{{$user_details['profile_url']}}' class="imgsize-75X75"><img  src="{{ $user_image_details['image_url'] }}" alt="user" title="user profile" /></a>
                                <h3>{{$user_details['display_name']}}</h3>-->

                                <ul class="list-unstyled">
                                    @if($logged_user_id == $user_id )
                                        <li>
                                            <!--{{ HTML::link(URL::to('users/myaccount'), trans('myaccount/viewProfile.edit'), array()) }}-->
                                            <a href="{{URL::to('users/myaccount')}}">{{ trans('myaccount/viewProfile.edit') }}</a>
                                        </li>
                                    @endif

                                    <li class="active"><a href="{{$user_details['profile_url']}}">{{ trans('myaccount/viewProfile.profile') }}</a></li>

                                    <li><a href="{{URL::action('FavoritesController@viewFavorites', $user_details['user_code'])}}">{{ trans('myaccount/viewProfile.favorites') }}</a></li>

                                    <?php $currmembersincircle = $userCircleService->numberOfMembersInCircle($user_id); ?>
                                    <li>
                                        <a href="{{URL::action('UsersCircleController@viewCircle', $user_details['user_code'])}}">
                                        {{ trans('myaccount/viewProfile.circles') }} <span>{{$currmembersincircle}}</span></a>
                                    </li>

                                    @if(CUtil::isMember())
                                        @if($logged_user_id != $user_id )
                                            <?php $isUserInCircle =  $userCircleService->isUserInCircle($user_id, $logged_user_id); ?>
                                            @if($isUserInCircle)
                                                <li><a class="js-addto-circle" href="javascript:;"><i class="fa fa-times-circle text-danger"></i>
												{{ trans('myaccount/viewProfile.in_your_circle') }} </a></li>
                                            @else
                                                <li><a class="js-addto-circle" href="javascript:;"><i class="fa fa-plus-circle text-success"></i>
												{{ trans('myaccount/viewProfile.add_to_circle') }} </a></li>
                                            @endif
                                        @endif
                                    @else
                                        <?php $login_url = URL('users/login?form_type=selLogin'); ?>
                                        <li><a href="{{$login_url}}" class="fn_signuppop">{{ trans('myaccount/viewProfile.add_to_circle') }}</a></li>
                                    @endif

                                    @if(CUtil::isMember())
                                        @if($logged_user_id != $user_id)
                                            <li><a href="{{Url::to('shop/user/message/add/'.$user_details['user_code']) }}" class="fn_signuppop">
											{{ trans('myaccount/viewProfile.contact') }}</a></li>
                                        @endif
                                    @else
                                        <?php $login_url = URL('users/login?form_type=selLogin'); ?>
                                        <li><a href="{{$login_url}}" class="fn_signuppop">{{ trans('myaccount/viewProfile.contact') }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

						@if($user_arr['is_shop_owner'])
							<div class="userprfl-shop">
								<h2>{{ trans('myaccount/viewProfile.shop') }}</h2>
								<h3><a href="{{ $shop_details['shop_url'] }}" title="{{ $shop_details['shop_name'] }}">{{ $shop_details['shop_name'] }}</a></h3>
								@if($shop_details['shop_slogan'] != '')
									<p>{{ $shop_details['shop_slogan'] }}</p>
								@endif
							</div>
                        @endif
                    </div>
                    <!-- LEFT USER PROFILE BLOCK END -->

                    <!-- RIGHT USER PROFILE BLOCK STARTS -->
                    <div class="col-md-9 user-profileblk featured-icon">
                        @include('myaccount/leftUserProfileBlock')
                    </div>
                    <!-- RIGHT USER PROFILE BLOCK END -->
                </div>
                <!-- USER PROFILE END -->
			@endif
		</div>
	</div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = {{$logged_user_id}};
		var user_circle_url = '{{URL::action('UsersCircleController@postToggleUserCircle')}}';
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
			//if($(this).data('productid')!='')
			//{
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
			//}
		});
	</script>
@stop