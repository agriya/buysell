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
        <h1>{{$shop_details['shop_name']}}'s shop policies</h2>
        <div class="row">
            <div class="col-md-3 blog-sidebar sidebar-alt">
                @include("viewShopRightMenu")
            </div>

            <div class="col-md-9">
                <div class="shopprdct-info">
                    @include("viewShopHeader")
                </div>

                <div id="error_msg_div"></div>
                <?php
                $rating_det = $feedback_service->getAvgRatingForSeller($shop_details['user_id']	); ?>
                @if(isset($rating_det['avg_rating']))
                    <div class="margin-bottom-10 well">
                        <strong class="text-muted">{{ Lang::get('shop.avg_item_review')}}</strong>
                        {{ Form::input('number','rating',$rating_det['avg_rating'], array('class' => 'rating', 'id' => 'input-21f', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
                        ({{$rating_det['rating_count']}})
                    </div>
                @endif

                @if(isset($feed_back_list) && count($feed_back_list) > 0)
                    <div class="list-unstyled review-list panel panel-default featured-icon">
                        @foreach($feed_back_list as $key => $rev)
                            <?php
                                $rev = $rev->toArray();
                                $user_image_details = CUtil::getUserPersonalImage($rev['feedback_user_id'], 'small');
                                $user_details = CUtil::getUserDetails($rev['feedback_user_id']);
                                $rev_product_obj = Products::initialize($rev['product_id']);
                                $rev_product_obj->setIncludeDeleted(true);
                                $rev_product_obj->setIncludeBlockedUserProducts(true);
                                $rev_product_details = $rev_product_obj->getProductDetails();
                                $product_url = $productService->getProductViewURL($rev['product_id'], $rev_product_details);
                                $p_img_arr = $rev_product_obj->getProductImage($rev['product_id']);
                                $p_thumb_img = $productService->getProductDefaultThumbImage($rev['product_id'], 'thumb', $p_img_arr);
                                $feed_back_rate = 0;

                                if($rev['feedback_remarks'] == 'Positive' || $rev['feedback_remarks'] == 'Neutral')
                                    $feed_back_rate = 100;
                                if($rev['feedback_remarks'] == 'Neutral') {
                                    $lbl_class = "text-primary";
                                }
                                elseif($rev['feedback_remarks'] == 'Positive') {
                                    $lbl_class = " text-success";
                                }
                                elseif($rev['feedback_remarks'] == 'Negative') {
                                    $lbl_class = " text-danger";
                                }
                            ?>
                            <div class="panel-heading clearfix">
                                <a href="{{ $user_details['profile_url'] }}" class="imgusersm-32X32 pull-left margin-right-10">
                                <img class="" src="{{ $user_image_details['image_url'] }}" {{ $user_image_details['image_attr'] }} alt="{{ $user_details['display_name'] }}" /></a>
                                <div class="margin-top-8 pull-left">
                                    {{trans('viewProduct.reviewed_by')}} <a href="{{ $user_details['profile_url'] }}" class="">{{ $user_details['display_name'] }}</a>
                                    <span class="fonts12 text-muted">{{ CUtil::FMTDate($rev['created_at'], 'Y-m-d H:i:s', '') }}</span>
                                </div>
                            </div>

                            <div class="panel-body row">
                                <div class="clearfix col-md-2 col-sm-2 margin-bottom-10">
                                    {{ CUtil::showFeaturedProductIcon($rev['product_id'], $rev_product_details) }}
                                    <a href="{{ $product_url }}" class="imgsize-95X95 pull-left">
                                        <img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} alt="{{ htmlentities($rev_product_details['product_name']) }}" />
                                    </a>
                                </div>

                                <div class="col-md-10 col-sm-10">
                                    <p class="clearfix"><a href="{{ $product_url }}" class="pull-left">{{ $rev_product_details['product_name'] }}</a></p>
                                    {{ Form::input('number','rating',$rev['rating'], array('class' => 'rating', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
                                    <!--<p class="review-rating"><strong><span style="width:{{ $feed_back_rate }}%;">
                                    {{trans('viewProduct.reviewe_rating')}}</span></strong></p>-->
                                    <p class="margin-top-10">
                                        <span class="text-muted">{{ Lang::get('feedback.feedback') }}:</span>
                                        <span class="{{ $lbl_class }}"><strong>{{$rev['feedback_remarks']}}</strong></span>
                                    </p>
                                    <div class="line-hgt24 margin-bottom-10">{{ $rev['feedback_comment'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info no-margin">{{trans('viewProduct.no_review_found')}}</div>
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
	</script>
@stop