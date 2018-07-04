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

            	<div class="col-md-9 shopprdct-info">
					@include("viewShopHeader")

					<div id="error_msg_div"></div>

					<div class="well">
                        <ul class="list-unstyled shop-list shop-policy">
                        	<?php $policy_found = false; ?>
							@if(isset($shop_details['policy_welcome']) && $shop_details['policy_welcome']!='')
								<?php $policy_found = true; ?>
                                <li>
									 <h1 class="title-two">Welcome</h1>
									<div class="shoppolicy-para">{{$shop_details['policy_welcome']}}</div>
                                </li>
                            @endif

                            @if(isset($shop_details['policy_payment']) && $shop_details['policy_payment']!='')
								<?php $policy_found = true; ?>
                                <li>
									<h1 class="title-two">Payment</h1>
									<div class="shoppolicy-para">
										{{$shop_details['policy_payment']}}
									</div>
                                </li>
                            @endif

                            @if(isset($shop_details['policy_shipping']) && $shop_details['policy_shipping']!='')
								<?php $policy_found = true; ?>
								<li>
									<h1 class="title-two">Shipping</h1>
									<div class="shoppolicy-para">
										{{$shop_details['policy_shipping']}}
									</div>
								</li>
                            @endif

                            @if(isset($shop_details['policy_refund_exchange']) && $shop_details['policy_refund_exchange']!='')
								<?php $policy_found = true; ?>
                                <li>
									<h1 class="title-two">Refund and Exchange</h1>
									<div class="shoppolicy-para">
										{{$shop_details['policy_refund_exchange']}}
									</div>
                                </li>
                            @endif

                            @if(isset($shop_details['policy_faq']) && $shop_details['policy_faq']!='')
								<?php $policy_found = true; ?>
                                <li>
									<h1 class="title-two">Additional policies and FAQs</h1>
									<div class="shoppolicy-para">
										{{$shop_details['policy_faq']}}
									</div>
                                </li>
                            @endif
                            @if(!$policy_found)
                            	<li><p class="note note-info margin-0">{{ trans('shop.since_the_shop_owner_has_not_set_the_shop_policy')}}</p></li>
                            @endif
                        </ul>
	                </div>
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