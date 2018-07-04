@extends('admin')
@section('content')
	<?php
        $logged_user_id = (Sentry::getUser())? Sentry::getUser()->id : 0;
        $shop_url = '';
        $url_str = '';
        $no_url = 'javascript:void(0)';
    ?>
    @if($d_arr['error_msg'] != '')
        <div class="note note-danger">{{ trans($d_arr['error_msg']) }}</div>
    @else
        <div class="mb30">
            <a href="{{ Url::to('admin/product/list') }}" class="btn btn-success btn-xs pull-right mt5"><i class="fa fa-arrow-left"></i> {{trans('admin/productList.product_list_title')}}</a>

            <?php $edit_url =  URL::to('admin/product/add?id='.$p_details['id']); ?>
            <a href="{{ $edit_url }}" class="btn btn-primary btn-xs pull-right mt5 mr10"><i class="fa fa-edit"></i> {{trans('admin/productList.product_edit')}}</a>
            <h1 class="page-title">{{{ $product_title }}}</h1>
        </div>
        @if($d_arr['alert_msg'] != '')
            <div class="note note-info">{{ trans($d_arr['alert_msg']) }}</div>
        @endif
        <div class="row viewpdct-responsive">
            <div class="col-md-9">
                <!-- SLIDER BLOCK STARTS -->
                <div id="myCarousel" class="carousel slide custom-carousel mb40">
                    <div class="carousel-inner">
                        <div class="item active">
                            @if(count($d_arr['slider_default_img']) > 0)
                                @if($d_arr['slider_default_img']['image_exits'])
                                    <a class="fn_fancyboxview" rel="screenshots_group" href="{{ $d_arr['slider_default_img']['orig_img_path'] }}" title="{{{ $d_arr['slider_default_img']['title'] }}}" ><img src="{{ $d_arr['slider_default_img']['large_img_path'] }}" {{ $d_arr['slider_default_img']['large_img_attr'] }} title="{{{ $d_arr['slider_default_img']['title'] }}}" alt="{{{ $d_arr['slider_default_img']['title'] }}}" class="img-responsive" /></a>
                                @else
                                    <a href="javascript:void(0);" class="no-cursor">
                                    	<img src="{{ $d_arr['slider_default_img']['large_img_path'] }}" {{ $d_arr['slider_default_img']['large_img_attr'] }} title="{{{ $d_arr['slider_default_img']['title'] }}}" alt="{{{ $d_arr['slider_default_img']['title'] }}}" class="img-responsive" />
                                    </a>
                                @endif
                            @endif
                        </div>

                        @if(count($d_arr['slider_preview_img']) > 0)
                            @foreach($d_arr['slider_preview_img'] AS $img)
                                <div class="item"><a class="fn_fancyboxview" rel="screenshots_group" href="{{ $img['orig_img_path'] }}" title="{{{ $img['title'] }}}" ><img src="{{ $img['large_img_path'] }}" {{ $img['large_img_attr'] }} title="{{{ $img['title'] }}}" alt="{{{ $img['title'] }}}" class="img-responsive" /></a></div>
                            @endforeach
                        @endif
                    </div>
                    <a class="carousel-control left" href="#myCarousel" data-slide="prev">
                        <i class="fa fa-angle-left"></i>
                    </a>
                    <a class="carousel-control right" href="#myCarousel" data-slide="next">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
                <!-- SLIDER BLOCK END -->

                <!-- PRODUCT DESCRIPTION STARTS  -->
                @if($d_arr['error_msg'] == '')
                    <div class="tabbable-custom tabbable-customnew">
                        <div class="mobilemenu">
                            <!-- MOBILE TOGGLER STARTS -->
                            <button class="btn btn-primary btn-sm mobilemenu-bar"><i class="fa fa-chevron-down"></i> Menu</button>
                            <!-- MOBILE TOGGLER END -->

                            <!-- TABS STARTS -->
                            <ul class="product_tabs nav nav-tabs mbldropdown-menu ac-custom-tabs">
                            	@if(isset($p_details['product_description']) && $p_details['product_description'] != '')
                                	<li class="active"><a href="#description" data-toggle="tab">{{ trans('viewProduct.description') }}</a></li>
                                @endif
                                @if(isset($p_details['product_support_content']) && $p_details['product_support_content'] != '')
                                	<li><a href="#support" data-toggle="tab">{{ trans('viewProduct.support_content') }}</a></li>
                                @endif
                                @if(isset($p_details['demo_details']) && $p_details['demo_details'] != '')
                                    <li><a href="#demo" data-toggle="tab">{{ trans('viewProduct.demo_details') }}</a></li>
                                @endif
                                @if(isset($p_details['use_cancellation_policy']) && $p_details['use_cancellation_policy'] == 'Yes' &&
                                	((isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename']) ||
                                    (isset($p_details['cancellation_policy_text']) && $p_details['cancellation_policy_text'])
                                    ))

                                	<li><a href="#cancellation" data-toggle="tab">{{ trans('viewProduct.cancellation_policy') }}</a></li>
        						@endif
                            </ul>
                            <!-- TABS END -->
                        </div>

                        <div class="tab-content line-hgt24">
                        	@if(isset($p_details['product_description']) && $p_details['product_description'] != '')
	                            <div id="description" class="tab-pane active custom-description">
	                                {{nl2br($p_details['product_description'])}}
	                            </div>
                            @endif
                            @if(isset($p_details['product_support_content']) && $p_details['product_support_content'] != '')
	                            <div id="support" class="tab-pane">
	                                {{nl2br($p_details['product_support_content'])}}
	                            </div>
                            @endif
                            @if(isset($p_details['demo_details']) && $p_details['demo_details'] != '')
                                <div id="demo" class="tab-pane">
                                    <p><span class="label label-success">{{trans('viewProduct.demo_url')}}:</span> <a href='{{ $p_details['demo_url'] }}' class="light-link" target="_blank"><strong>{{ $p_details['demo_url'] }}</strong></a></p>
                                    <div class="margin-bottom-20">{{nl2br($p_details['demo_details'])}}</div>
                                </div>
                            @endif
                            @if(isset($p_details['use_cancellation_policy']) && $p_details['use_cancellation_policy'] == 'Yes' &&
                                	((isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename']!='') ||
                                     (isset($p_details['cancellation_policy_text']) && $p_details['cancellation_policy_text']!='')
                                    ))
                            	<div id="cancellation" class="tab-pane">
                                	@if(isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename']!='')
                                    	<?php $filePath = $p_details['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
                                              $filename = $p_details['cancellation_policy_filename'].'.'.$p_details['cancellation_policy_filetype'];?>
										<a target="_blank" href="{{$filePath.'/'.$filename}}"><i class="fa fa-share"></i> Click here to view cancellation policy</a>
                                    @elseif(isset($p_details['cancellation_policy_text']) && $p_details['cancellation_policy_text']!='')
                                    	{{nl2br($p_details['cancellation_policy_text'])}}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <!-- PRODUCT DESCRIPTION END  -->
             </div>

            <!-- RIGHT BLOCK STARTS -->
                @include('admin.viewProductRightBlock')
            <!-- RIGHT BLOCK END -->
        </div>
    @endif
@stop

@section('script_content')
    @if($d_arr['error_msg'] == '')
        <script src="{{ URL::asset('/js/lib/jcarousel-0.3.0/js/jquery.jcarousel.min.js') }}"></script>
        <script src="{{ URL::asset('/js/lib/jcarousel-0.3.0/js/jcarousel.connected-carousels.js') }}"></script>
        <script language="javascript" type="text/javascript">
			$('.carousel').carousel({
			  interval: 0
			});
            @if(!$preview_mode)

                $(".fn_ChangeStatus").fancybox({
                    maxWidth    : 772,
                    maxHeight   : 432,
                    fitToView   : false,
                    width       : '70%',
                    height      : '432',
                    autoSize    : true,
                    closeClick  : true,
                    type        : 'iframe',
                    openEffect  : 'none',
                    closeEffect : 'none'
                });
            @endif

            $(document).ready(function() {
                $(".fn_fancybox").fancybox({
                    openEffect	: 'none',
                    closeEffect	: 'none'
                });
            });

            $(".fn_fancyboxview").fancybox({
				beforeShow: function() {
					$(".fancybox-wrap").addClass('view-proprevw');
				},
				maxWidth    : 772,
				maxHeight   : 432,
				fitToView   : false,
				autoSize    : true,
				closeClick  : true,
				openEffect  : 'none',
				closeEffect : 'none'
			});
        </script>
    @endif
	<script language="javascript" type="text/javascript">
		var like_ajax = 0;
		$(document).ready(function() {
			$(".product_tabs li:first").addClass('active');
			$(".tab-content div:first").addClass('active');
			$(".js_showAddCartBtn").hover(function() {
				$(this).children('#addCartButton').slideToggle('fast');
		    });
		});

		$('.js-service-checkbox').click(function() {
			var final_price = $('#orgamount').val();
			final_price = parseFloat(final_price);
			var services_price = 0;
			var service_ids = [];
			$.each($("input[name='productservices[]']:checked"), function() {
				service_ids.push($(this).val());
				services_price = services_price + $(this).data('price');
			});
			service_ids.join(',');
			var subtotal_price = final_price+services_price;
			$('#subtotal_price').html(subtotal_price);
			$('#product_services').val(service_ids);
		});

		$('.fn_clsDescMore').click(function() {
			$(this).parent().hide();
			$(this).parent().next().show();
		});
		$('.fn_clsDescLess').click(function() {
			$(this).parent().hide();
			$(this).parent().prev().show();
		});
	</script>
@stop