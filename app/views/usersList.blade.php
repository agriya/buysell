@extends('base')
@section('content')
	<h1>{{ trans('users.users_list') }}</h1>
	<div id="error_msg_div"></div>

    <!-- BEGIN: USERS LIST BLOCK -->
	<div class="row">
    	<div class="col-md-3 blog-sidebar">
			<div id="advanced_search">
				{{ Form::open(array('url' => Request::url(), 'id' => 'searchShopsfrm', 'class' => 'well', 'name' => 'searchShopsfrm', 'method' => 'get')) }}
                    <h4>{{ trans('common.filter_your_search')}}</h4>
                    <ul class="list-unstyled">
                        <li><a title="Everyone" href="{{URL::action('AuthController@getIndex')}}">{{ trans('users.everyone')}}</a></li>
                        <li><a title="Shop Owners" href="{{URL::action('AuthController@getIndex').'?filter_by=shop_owner' }}">{{ trans('users.shop_onwers')}}</a></li>
                        <li class="clsLiLast"><a title="Non-Shop Owners" href="{{URL::action('AuthController@getIndex').'?filter_by=nonshop_owner' }}">{{ trans('users.non_shop_owners')}}</a></li>
                    </ul>
                    <div class="margin-bottom-10">
                        {{Form::text('uname',Input::get('uname'), array('class' => 'form-control')) }}
                    </div>
                    <div clas="sidebar-btn">
                    	<button type="submit" id="post_comment" name="post_comment" class="btn green btn-sm"><i class="fa fa-check"></i> {{ trans('common.submit')}}</button>
                        <!--<a onclick="javascript:callSearch();" href="javascript:void(0);" class="btn btn-success btn-sm mt10">Search</a>-->
                    </div>
				{{ Form::close() }}
			</div>
		</div>

		<div class="col-md-9 mainbar">
        	<div class="well">
				<div id="searchresult_text">
					<h2 class="title-one margin-bottom-10"><strong>{{$users_list->getTotal()}}</strong> <span class="text-muted"> {{ trans('users.peeples_found') }}</span></h2>
				</div>
				@if(count($users_list) > 0)
					<div class="text-right clearfix margin-bottom-15">
						{{ $users_list->appends(array('uname' => Input::get('uname')))->links() }}
					</div>

                    <ul class="list-unstyled shop-list">
                        @foreach($users_list as $user)
                            <?php
                                //echo "<pre>";print_r($user);echo "</pre>";
                                $profile_url = CUtil::userProfileUrl($user->id);
                                $display_name = ucfirst($user->first_name).' '.ucfirst($user->last_name);
                             ?>
                            <li>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="media">
                                            <div class="pull-left margin-top-3">
                                                <a title="{{$display_name}}" href="{{$profile_url}}" class="imguserborsm-56X56">
                                                    <img width="30" title="{{$display_name}}" alt="{{$user->user_name}}" src="{{$user->profile_image['image_url']}}">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <a title="{{$display_name}}" href="{{$profile_url}}" class="text-ellipsis text-success">{{$user->user_name}}</a>
                                                <ul class="list-inline clearfix margin-top-8">
                                                    <li>Favorites (<strong>{{$user->total_favorites}}</strong>)</li>
                                                    <li class="text-muted">|</li>
                                                    <li><span>Circles:</span> <strong>{{$user->members_in_circle}}</strong></li>
                                                </ul>
                                                <div class="beforelogin-circle margin-top-8">
                                                    @if(CUtil::isMember())
                                                        @if($logged_user_id != $user->id )
                                                            <?php $isUserInCircle =  $userCircleService->isUserInCircle($user->id, $logged_user_id); ?>
                                                            @if($isUserInCircle)
                                                                <a class="js-addto-circle" data-userid="{{$user->id}}" href="javascript:;">In your circle <i class="fa fa-times-circle text-danger"></i></a>
                                                            @else
                                                                <a class="js-addto-circle" data-userid="{{$user->id}}" href="javascript:;">Add to circle <i class="fa fa-plus-square text-success"></i></a>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <?php $login_url = URL('users/login?form_type=selLogin'); ?>
                                                        <i class="fa fa-hand-o-right"></i> <a href="{{$login_url}}" class="fn_signuppop"><small>You need to login to add to circle</small></a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
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
                                                            <img width="75" title="{{{$prd['product_name']}}}" alt="{{{$prd['product_name']}}}" src="{{$p_thumb_img['image_url']}}">
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

					<div class="text-right">
						{{ $users_list->appends(array('uname' => Input::get('uname'), 'filter_by' => Input::get('filter_by')))->links() }}
					</div>
				@else
					<div class="alert alert-info margin-0" id="selMsgError">{{ trans('users.no_members_found')}}.</div>
				@endif
			</div>
		</div>
	</div>
    <!-- END: USERS LIST BLOCK -->
@stop


@section('script_content')
	<script language="javascript" type="text/javascript">
		var user_id = {{$logged_user_id}};
		var user_circle_url = '{{URL::action('UsersCircleController@postToggleUserCircle')}}';

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
                            text_to_disp = 'In your circle <i class="fa fa-times-circle text-danger"></i>';
                        else
                            text_to_disp = 'Add to circle <i class="fa fa-plus-square text-success"></i>';
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