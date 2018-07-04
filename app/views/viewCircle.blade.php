@extends('base')
@section('content')
	{{-- Breadcrumbs start
        {{ Breadcrumbs::render('view-profile', $breadcrumb_arr) }}
	Breadcrumbs end --}}
	<div id="error_msg_div"></div>

    <div class="row">
        <!-- BEGIN: USER PROFILE CIRCLE -->
        <div class="col-md-3 blog-sidebar sidebar-alt user-profile">
            <div class="well">
                <div class="well-new">
                    <div class="shopuser-profile">
                        <a href='{{$user_details['profile_url']}}' class="imguserborsm-56X56">
                            <img  src="{{ URL::asset('images/no_image/usernoimage-50x50.jpg') }}" alt="user" title="user profile" />
                        </a>
                        <h3><a href='{{$user_details['profile_url']}}'>{{ $user_details['display_name'] }}</a></h3>
                        <p><span>Harrisburg, PA, United States</span></p>
                    </div>
                    {{--URL::asset('/images/no_image/no-maleprofile_T.jpg')--}}
                    <!--<a href='{{$user_details['profile_url']}}' class="imgsize-75X75"><img  src="{{ $user_image_details['image_url'] }}" alt="user" title="user profile" /></a>-->

                    <ul class="list-unstyled">
                        <li>
                            @if($logged_user_id == $user_id )
                                <!-- {{ HTML::link(URL::to('users/myaccount'), trans('myaccount/viewProfile.edit'), array()) }}-->
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

                        <li><a href="{{URL::action('FavoritesController@viewFavorites', $user_details['user_code'])}}">{{ trans('myaccount/viewProfile.favorites') }}</a></li>

                        <?php $currmembersincircle = $userCircleService->numberOfMembersInCircle($user_id); ?>
                        <li class="active"><a href="{{URL::action('UsersCircleController@viewCircle', $user_details['user_code'])}}">
                        {{ trans('myaccount/viewProfile.circles') }} <span>{{$currmembersincircle}}</span></a></li>

                        @if($logged_user_id != $user_id)
                            <?php $isUserInCircle =  $userCircleService->isUserInCircle($user_id, $logged_user_id); ?>
                            @if($isUserInCircle)
                                <li><a class="js-addto-circle" data-userid="{{$user_id}}" href="javascript:;"><i class="fa fa-times-circle text-danger"></i> {{ trans('myaccount/viewProfile.in_your_circle') }} </a></li>
                            @else
                                <li><a class="js-addto-circle" data-userid="{{$user_id}}" href="javascript:;"><i class="fa fa-plus-circle text-success"></i> {{ trans('myaccount/viewProfile.add_to_circle') }} </a></li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>

            <?php
				$shopService = new ShopService();
				$shop_details = $shopService->getShopDetails($user_id);
			?>

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
        <!-- END: USER PROFILE CIRCLE -->

        <!-- BEGIN: RIGHT USER PROFILE CIRCLE BLOCK -->
        <div class="col-md-9 mainbar">
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

            <h1>{{ str_replace('VAR_USERNAME', $user_details['display_name'], trans('myaccount/viewProfile.profile_title')) }}</h1>
            <div class="tabbable-custom portlet">
                <div class="customview-navtab mobviewmenu-480">
                    <button class="btn bg-blue-steel btn-sm"><i class="fa fa-chevron-down"></i>View Menu</button>
                    <ul class="nav nav-tabs margin-bottom-30">
                        <?php
                            $membersincircle = $userCircleService->numberOfMembersInCircle($user_id);
                            $sort_by_arr = CUtil::populateCirclesHeaderArray($membersincircle,$user_details['display_name']);
                            $orderby_field = 'id';
                            $circle_type = 'followers';
                            if (Input::has('circle_type'))
                            $circle_type = Input::get('circle_type');
                        ?>
                        @foreach($sort_by_arr as $sortKey => $sort)
                            <li @if($circle_type == $sort['innervalue']) class="active" @endif><a href="{{ $sort['href'] }}">{{ $sort['innertext'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                @if(count($users_list) > 0)
                    <div class="text-right">
                        {{ $users_list->appends(array('uname' => Input::get('uname')))->links() }}
                    </div>

                    <div class="well">
                        <ul class="list-unstyled shop-list">
                            @foreach($users_list as $user)
                                <?php
                                    //echo "<pre>";print_r($user);echo "</pre>";
                                    $profile_url = CUtil::userProfileUrl($user->id);
                                    $display_name = ucfirst($user->first_name).' '.ucfirst($user->last_name);
                                ?>
                                <li>
                                	<div class="row">
                                        <div class="col-md-5">
                                            <div class="media">
                                                <div class="pull-left">
                                                    <a title="{{$display_name}}" href="{{$profile_url}}" class="imguserborsm-56X56">
                                                        <img title="{{$display_name}}" alt="{{$user->user_name}}" src="{{$user->profile_image['image_url']}}">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <p><a title="{{$display_name}}" href="{{$profile_url}}" class="text-ellipsis "><strong>{{$user->user_name}}</strong></a></p>
                                                    <ul class="list-inline margin-bottom-5">
                                                        <li>{{ trans('myaccount/viewProfile.favorites') }} (<strong>{{$user->total_favorites}}</strong>)</li>
                                                        <li class="text-muted">|</li>
                                                        <li><span>{{ trans('myaccount/viewProfile.circles') }}:</span> <strong>{{$user->members_in_circle}}</strong></li>
                                                    </ul>
                                                    <div class="beforelogin-circle">
                                                        @if(CUtil::isMember())
                                                            @if($logged_user_id != $user->id )
                                                                <?php $isUserInCircle =  $userCircleService->isUserInCircle($user->id, $logged_user_id); ?>
                                                                <i class="fa fa-hand-o-right text-muted"></i>
                                                                @if($isUserInCircle)
                                                                    <a class="js-addto-circle" data-userid="{{$user->id}}" href="javascript:;">
                                                                    <i class="fa fa-times-circle text-danger"></i> {{ trans('myaccount/viewProfile.in_your_circle') }} </a>
                                                                @else
                                                                    <a class="js-addto-circle" data-userid="{{$user->id}}" href="javascript:;">
                                                                    <i class="fa fa-plus-circle text-success"></i> {{ trans('myaccount/viewProfile.add_to_circle') }}</a>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <?php $login_url = URL('users/login?form_type=selLogin'); ?>
                                                            <i class="fa fa-hand-o-right text-muted"></i>
                                                            <a href="{{$login_url}}" class="fn_signuppop fonts12">{{ trans('myaccount/viewProfile.need_login_to_add_circle') }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <ul class="list-inline text-right">
                                                @if(isset($user->favorite_products) && count($user->favorite_products) > 0)
                                                    @foreach($user->favorite_products as $product_id)
														<?php
                                                            $products = Products::initialize($product_id);
                                                            $products->setIncludeDeleted(true);
                                                            $products->setIncludeBlockedUserProducts(true);
                                                            $prd = $products->getProductDetails();
                                                            $p_img_arr = $products->getProductImage($prd['id']);
                                                            $p_thumb_img = $productService->getProductDefaultThumbImage($prd['id'], 'thumb', $p_img_arr);
                                                            //$price = $service_obj->formatProductPriceNew($product);
                                                            $view_url = $productService->getProductViewURL($prd['id'], $prd);
                                                        ?>
                                                        <li>
                                                            <a title="{{{$prd['product_name']}}}" href="{{$view_url}}" class="imgsize-75X75">
                                                                <img title="{{{$prd['product_name']}}}" alt="{{{$prd['product_name']}}}" src="{{$p_thumb_img['image_url']}}">
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="text-right">
                        {{ $users_list->appends(array('uname' => Input::get('uname'), 'filter_by' => Input::get('filter_by')))->links() }}
                    </div>
                @else
                	<!-- BEGIN: ALERT BLOCK -->
                	@if($logged_user_id != $user_id)
                    	<div class="alert alert-info" id="selMsgError">{{ $user_details['display_name'].'\'s '.trans('myaccount/viewProfile.have_not_in_any_circle') }}</div>
                    @else
                    	<div class="alert alert-info" id="selMsgError">{{ trans('myaccount/viewProfile.you_have_not_in_any_circle') }}</div>
                    @endif
                    <!-- END: ALERT BLOCK -->
                @endif
            </div>
        </div>
        <!-- END: RIGHT USER PROFILE CIRCLE BLOCK -->
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = {{$logged_user_id}};
		var user_circle_url = '{{URL::action('UsersCircleController@postToggleUserCircle')}}';
		//var circle_user_id = {{$user_id}};
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

	    $( ".js-addto-circle").click(function() {
			$this = $(this);
			circle_user_id = $(this).data('userid');
			if(typeof circle_user_id != 'undefined' && circle_user_id!='')
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
                            text_to_disp = '<i class="fa fa-times-circle text-danger"></i> In your circle';
                        else
                            text_to_disp = '<i class="fa fa-plus-circle text-success"></i> Add to circle';
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
	</script>
@stop