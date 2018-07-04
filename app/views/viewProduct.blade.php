@extends('base')
@section('content')
	<?php
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$shop_url = '';
		$url_str = '';
		$no_url = 'javascript:void(0)';
	?>

	@if($d_arr['error_msg'] != '')
		<div class="alert alert-danger">{{ trans($d_arr['error_msg']) }}</div>
    @else
	    <!-- BreadCrumb Starts
	    <ul class="breadcrumb">
		    <li><a href="{{ URL::to('product') }}">Products</a> <span class="divider"></span></li>
		    <?php $Category_url = $productService->getProductCategoryArr($p_details['product_category_id']); ?>
		    @foreach($Category_url as $Cat_url)
		    	<li>{{ $Cat_url }}</li>
		    @endforeach
		    <li class="active">{{{ $product_title }}}</li>
	    </ul>
	    BreadCrumb Ends -->

		{{-- <!--<h1>{{{ $product_title }}}</h1>--> --}}

		<div id="error_msg_div"></div>

        <!-- BEGIN: ALERT BLOCK -->
	    @if($d_arr['alert_msg'] != '')
			<div class="alert alert-info">{{ trans($d_arr['alert_msg']) }}</div>
	    @endif
		{{--Remove as it is not needed
		@if($stock_count == 0)
	    	<div class="alert alert-info">{{ trans("viewProduct.stock_country_info") }}</div>
		@endif--}}
        <!-- END: ALERT BLOCK -->

        <!-- Tab Menu Starts -->
	    {{-- <!--<ul class="nav nav-tabs margin-bottom-30">
	        <li @if(Route::currentRouteAction()== 'ViewProductController@getIndex') class="active" @endif>
	            <a href="{{ $d_arr['view_url'] }}" itemprop="url"><strong>Item Details</strong></a>
	        </li>
	    </ul>--> --}}
        <!-- Tab Menu Ends -->

        <!-- BEGIN: VIEW PRODUCT -->
		<div class="row bs-viewpage">
			<!-- BEGIN: LEFT BLOCK -->
			<div class="col-md-7">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="fav-block clearfix">
							<div class="fav-btn pull-left">
								@if(CUtil::isMember())
									@if($d_arr['is_favorite_product'])
										<a href="javascript:void(0);" data-productid="{{$p_details['id']}}" class="btn btn-default js-addto-favorite">
										<i class="fa fa-heart text-pink"></i> {{ Lang::get('viewProduct.unfavorite') }}</a>
									@else
										<a href="javascript:void(0);" data-productid="{{$p_details['id']}}" class="btn btn-default js-addto-favorite">
										<i class="fa fa-heart text-muted"></i> {{ Lang::get('viewProduct.favorite') }}</a>
									@endif
								@else
									<?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
									<a href="{{ $login_url }}" class="fn_signuppop btn btn-default"><i class="fa fa-heart text-muted"></i> {{ Lang::get('viewProduct.favorite') }}</a>
								@endif
							</div>

							@if(!$d_arr['is_favorite_product'])
								<p class="pull-right" id="js-favorite-msg"><strong>
								{{ Lang::get('viewProduct.like_this_item') }}</strong> {{ Lang::get('viewProduct.add_to_favorite_and_revisit') }}</p>
							@else
								<p class="pull-right" id="js-favorite-msg"><strong>{{ Lang::get('viewProduct.favorited_item') }}</strong>
								{{ Lang::get('viewProduct.revisit_from_favorite') }}</p>
							@endif
						</div>

						<!-- BEGIN: SLIDER HERE -->
						@if($p_details['product_preview_type'] == 'image')
							<div class="carousel-slider">
								<div id="myCarousel" class="carousel slide">
									<div class="carousel-inner">
										@if(count($d_arr['slider_default_img']) > 0)
											<div class="item active fn_main-image">
                                            	<img src="{{ $d_arr['slider_default_img']['large_img_path'] }}" {{ $d_arr['slider_default_img']['large_img_attr'] }} title="{{{ $d_arr['slider_default_img']['title'] }}}" alt="{{{ $d_arr['slider_default_img']['title'] }}}" />
                                                {{ CUtil::showFeaturedProductIcon($p_details['id'], $p_details) }}
                                            </div>
										@endif

										@if(count($d_arr['slider_preview_img']) > 0)
											@foreach($d_arr['slider_preview_img'] AS $img)
												<div class="item">
                                                	<a class="fn_fancyboxview" rel="screenshots_group" href="{{ $img['large_img_path'] }}" title="{{{ $img['title'] }}}" ><img src="{{ $img['large_img_path'] }}" {{ $img['large_img_attr'] }} title="{{{ $img['title'] }}}" alt="{{{ $img['title'] }}}" /></a>
                                                    {{ CUtil::showFeaturedProductIcon($p_details['id'], $p_details) }}
                                                </div>
											@endforeach
										@endif
									</div>

									@if(count($d_arr['slider_preview_img']) >= 1)
										<a class="carousel-control left" href="#myCarousel" data-slide="prev"></a>
										<a class="carousel-control right" href="#myCarousel" data-slide="next"></a>
									@endif
								</div>
								<?php $cnt  = 1; ?>
								<ul class="list-inline">
									<li class="item active fn_slide-image" data-slide-to="0" data-target="#myCarousel">
										<a rel="screenshots_group" href="{{ $d_arr['slider_default_img']['small_img_path'] }}" title="{{{ $d_arr['slider_default_img']['title'] }}}" ><img src="{{ $d_arr['slider_default_img']['small_img_path'] }}" {{ $d_arr['slider_default_img']['small_img_attr'] }} title="{{{ $d_arr['slider_default_img']['title'] }}}" alt="{{{ $d_arr['slider_default_img']['title'] }}}" /></a>
									</li>
									@foreach($d_arr['slider_preview_img'] AS $img)
										@if($cnt > 0)
											<li class="item" data-slide-to="{{$cnt}}" data-target="#myCarousel">
												<a class="" rel="screenshots_group" href="{{ $img['small_img_path'] }}" title="{{{ $img['title'] }}}" >
												<img src="{{ $img['small_img_path'] }}" {{ $img['small_img_attr'] }} title="{{{ $img['title'] }}}" alt="{{{ $img['title'] }}}" /></a>
											</li>
										@endif
										<?php $cnt++; ?>
									@endforeach
								</ul>
							</div>
						@endif
						<!-- END: SLIDER HERE -->
						<div class="slider-info">
							<!-- BEGIN: TAB HERE -->
							<div class="bs-example bs-example-tabs">
								<div class="bs-tabmenu">
									<p class="btn btn-default"><i class="fa fa-list margin-right-5"></i> View Menu <i class="fa fa-angle-down margin-left-10"></i></p>
									<ul id="myTab" class="nav nav-tabs bs-tabs text-center" role="tablist">
										<li class="active"><a href="#itemdet" data-toggle="tab" role="tab">{{Lang::get('viewProduct.item_details')}}</a></li>
										<li>
											<a href="#revw" data-toggle="tab" role="tab" title="{{ Lang::get('common.reviews')}}">
												@if (isset($d_arr['rating_avg']['rating_count']) && $d_arr['rating_avg']['rating_count'] > 0)
													{{ Form::input('number','rating',$d_arr['rating_avg']['avg_rating'], array('class' => 'rating', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
													<span>({{ $d_arr['rating_avg']['rating_count'] }})</span>
												@else
													{{ Form::input('number','rating',0, array('class' => 'rating', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
												@endif
											</a>
										</li>

										<li><a href="#shipol" data-toggle="tab" role="tab">{{Lang::get('viewProduct.cancellation_policies')}}</a></li>

										@if((isset($p_details['product_support_content']) && $p_details['product_support_content'] != '') || (isset($p_details['demo_details']) && $p_details['demo_details'] != ''))
                                            <li><a class="dropdown-toggle" data-toggle="dropdown" href="#">	{{ Lang::get('common.more')}} <b class="caret"></b> </a>
                                                <ul class="dropdown-menu dropdown-menu-right support_demo" id="myTab1">
                                                    @if(isset($p_details['product_support_content']) && $p_details['product_support_content'] != '')
                                                        <li><a href="#support" data-toggle="tab" role="tab">{{ trans('viewProduct.support_content') }}</a></li>
                                                    @endif
                                                    @if(isset($p_details['demo_details']) && $p_details['demo_details'] != '')
                                                        <li><a href="#demo" data-toggle="tab" role="tab">{{ trans('viewProduct.demo_details') }}</a></li>
                                                    @endif
                                                </ul>
                                            </li>
										@endif
									</ul>
								</div>
								<div id="myTabContent" class="tab-content bs-content">
									<div id="itemdet" class="tab-pane fade active in" style="word-wrap: break-word;" >
										@if($p_details['product_description'] != '')
											{{ nl2br(CUtil::makeClickableLinks($p_details['product_description'])) }}
										@else
											<div class="alert alert-info no-margin">{{ Lang::get('viewProduct.item_details_not_added') }}</div>
										@endif
									</div>

									<div id="revw" class="tab-pane fade">
										@if(count($d_arr['feed_back_list']) > 0)
											<ul class="list-unstyled review-list featured-icon">
												@foreach($d_arr['feed_back_list'] as $key => $rev)
													<?php
														$user_image_details = CUtil::getUserPersonalImage($rev['feedback_user_id'], 'small');
														$user_details = CUtil::getUserDetails($rev['feedback_user_id']);
														$rev_product_obj = Products::initialize($rev['product_id']);
														$rev_product_obj->setIncludeDeleted(true);
														$rev_product_obj->setIncludeBlockedUserProducts(true);
														$rev_product_details = $rev_product_obj->getProductDetails();
														$product_url = $productService->getProductViewURL($rev['product_id'], $rev_product_details);
														$p_img_arr = $rev_product_obj->getProductImage($rev['product_id']);
														$p_thumb_img = $productService->getProductDefaultThumbImage($rev['product_id'], 'small', $p_img_arr);
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

													<li>
														<div class="row">
															<div class="col-md-3 text-center">
																<a href="{{ $user_details['profile_url'] }}" class="img-45X45">
																	<img class="img-circle" src="{{ $user_image_details['image_url'] }}" {{ $user_image_details['image_attr'] }} alt="{{ $user_details['display_name'] }}" />
																</a>
																<p>{{trans('viewProduct.reviewed_by')}} <a href="{{ $user_details['profile_url'] }}" class="show">
																{{ $user_details['display_name'] }}</a></p>
															</div>
															<div class="col-md-8 col-md-offset-1">
																<div class="margin-bottom-10">
																	<span class="pull-right fonts12 text-muted">{{ CUtil::FMTDate($rev['created_at'], 'Y-m-d H:i:s', '') }}</span>
																	{{ Form::input('number','rating',$rev['rating'], array('class' => 'rating', 'id' => 'input-21f', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
																	<!--<p class="review-rating"><strong><span style="width:{{ $feed_back_rate }}%;">
																	{{trans('viewProduct.reviewe_rating')}}</span></strong></p>-->
																	<p class="margin-top-10">
																		<span class="text-muted">{{ Lang::get('feedback.feedback') }}:</span>
																		<span class="{{ $lbl_class }}"><strong>{{$rev['feedback_remarks']}}</strong></span>
																	</p>
																</div>
																<div class="line-hgt24 margin-bottom-10">{{ $rev['feedback_comment'] }}</div>
																<div class="clearfix">
																	<a href="{{ $product_url }}" class="img-45X45 imgalt-45X45 pull-left">
																		<img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} alt="{{ htmlentities($rev_product_details['product_name']) }}" />
																	{{ CUtil::showFeaturedProductIcon($rev['product_id'], $rev_product_details) }}
																	</a>
																	<a href="{{ $product_url }}" class="pull-left">{{ $rev_product_details['product_name'] }}</a>
																</div>
															</div>
														</div>
													</li>
												@endforeach
											</ul>
										@else
											<div class="alert alert-info no-margin">{{trans('viewProduct.no_review_found')}}</div>
										@endif
									</div>

									<div id="support" class="tab-pane fade">
										@if(isset($p_details['product_support_content']) && $p_details['product_support_content'] != '')
			                                {{nl2br($p_details['product_support_content'])}}
		                        		@endif
		                            </div>

									<div id="demo" class="tab-pane fade">
			                            @if(isset($p_details['demo_details']) && $p_details['demo_details'] != '')
		                                    <p><span class="label label-success">{{trans('viewProduct.demo_url')}}:</span> <a href='{{ $p_details['demo_url'] }}' class="light-link" target="_blank"><strong>{{ $p_details['demo_url'] }}</strong></a></p>
		                                    <div class="margin-bottom-20">{{nl2br($p_details['demo_details'])}}</div>
		                            	@endif
		                            </div>

									<div id="shipol" class="tab-pane fade">
										@if(isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename']!='')
											<?php $filePath = $p_details['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
												  $filename = $p_details['cancellation_policy_filename'].'.'.$p_details['cancellation_policy_filetype'];?>
											<a target="_blank" href="{{$filePath.'/'.$filename}}"><i class="fa fa-share"></i> {{trans('product.click_to_view_cancellation_policy')}} </a>
										@elseif(isset($p_details['cancellation_policy_text']) && $p_details['cancellation_policy_text']!='')
											{{nl2br($p_details['cancellation_policy_text'])}}
										@else
											<div class="alert alert-info no-margin">{{trans('product.no_cancellation_policy_added')}}</div>
										@endif
									</div>
								</div>
							</div>
							<!-- END: TAB HERE -->
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="media">
							<?php
								$user_image_details = CUtil::getUserPersonalImage($d_arr['shop_details']['user_id'], 'thumb');
								$user_details = CUtil::getUserDetails($p_details['product_user_id']);
							?>
							<div class="pull-left text-center">
								<div class="bs-imgrounded">
									<img class="img-circle" src="{{ $user_image_details['image_url'] }}"  {{ $user_image_details['image_attr'] }} alt="image" />
								</div>
								<p><a href="{{ $user_details['profile_url'] }}" title="{{ $user_details['display_name'] }}">{{ $user_details['display_name'] }}</a></p>
							</div>
							<div class="media-body text-right">
								<div class="text-center">
									<p>{{trans('viewProduct.meet_the_owner_of')}} <strong>{{{ $d_arr['shop_details']['shop_name'] }}}.</strong></p>
									<p><strong>{{trans('viewProduct.learn_more_about_shop_process')}}.</strong></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END: LEFT BLOCK -->

			<!-- BEGIN: SIDEBAR -->
			<div class="col-md-5 viewpg-rgtpart">
				@include('viewProductRightBlock')
			</div>
			<!-- END: SIDEBAR -->
			@if(isset($d_arr["default_shipping_fee"]))
				{{ Form::hidden('shipping_company_name', $d_arr['default_shipping_fee'], array("id" => "shipping_company_name"))}}
			@endif
		</div>
        <!-- END: VIEW PRODUCT -->
        @if(count($d_arr['tag_arr']) > 0)
            <div class="bs-itemlist">
                <h2>{{Lang::get('viewProduct.related_tag_to_item')}}</h2>
                <ul class="list-unstyled clearfix">
                    @foreach($d_arr['tag_arr'] as $key => $val)
                        <li><a href="{{{ Url::to('product?tag_search='.trim($val)) }}}">{{{ ucfirst(trim($val)) }}}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
	@endif

    <script language="javascript" type="text/javascript">
	@if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
		var page_name = "";
	@else
    	var page_name = "view_product";
    	var show_add_cart_button = 1;
    	@if(CUtil::isAdmin() || $logged_user_id == isset($p_details['product_user_id']))
    		var show_add_cart_button = 0;
    	@endif
		var actions_url = '{{ URL::action('CartController@getUpdateShippingCountryAndCost') }}';
		@if(COUNT($p_details) > 0 && isset($p_details['shipping_template']))
		   var ship_template_id = '{{ $p_details['shipping_template'] }}';
		@else
     	   var ship_template_id = 0;
		@endif
        var actions_url_country = '{{ URL::action('CartController@postUpdateShippingCountryAndCost')}}';
        var user_id = "{{$logged_user_id}}";
		var favorite_product_url = "{{URL::action('FavoritesController@postToggleFavorite')}}";
		var favorite_product_list_url = "{{URL::action('FavoritesController@postToggleFavoriteList')}}";
		var select_atleast_one_thread = '{{ Lang::get('viewProduct.select_atleast_one_thread') }}';
		var report_item_url = '{{Url::action('ProductController@postReportItem')}}';
		var some_problem_try_later = '{{ Lang::get('viewProduct.some_problem_try_later') }}';
		var unfavorite = ' {{ Lang::get('viewProduct.unfavorite') }}';
		var favorited_item = '{{ Lang::get('viewProduct.favorited_item') }}';
		var revisit_from_favorite = '{{ Lang::get('viewProduct.revisit_from_favorite') }}';
		var favorite = '{{ Lang::get('viewProduct.favorite') }}';
		var like_this_item = '{{ Lang::get('viewProduct.like_this_item') }}';
		var add_to_favorite_and_revisit = '{{ Lang::get('viewProduct.add_to_favorite_and_revisit') }}';
		var unfavorite_shop = ' {{ Lang::get('viewProduct.unfavorite_shop') }}';
		var favorite_shop = '{{ Lang::get('viewProduct.favorite_shop') }}';
		var loading_image_url = '{{URL::asset('images/general/loading.gif')}}';
		var preview_mode = '{{ $preview_mode }}';
		var currSymbol = '{{ Config::get('generalConfig.site_default_currency') }}';
		var currCode  = '{{ Config::get('generalConfig.site_default_currency') }}';
		var has_discount = 0;
		var stockAvailLbl = '{{ Lang::get('viewProduct.product_stock_available_label'); }}'
		var viewitem_no_stock_msg = ' {{ Lang::get('viewProduct.stock_not_avalible') }}';
		var no_swap_img_msg = ' {{ Lang::get('viewProduct.no_swap_img_msg') }}';
		var allow_variation = 0;
		var matrix_details_arr = new Array();
		@if(isset($d_arr['show_variation']) && $d_arr['show_variation'] > 0 && isset($d_arr["default_shipping_fee"]))
			var allow_variation = 1;
			@if(isset($p_details['shipping_template']) && $p_details['shipping_template'] > 0)
				var matrix_details_arr = '{{ $product_this_obj->variation_service->getItemMatrixDetailsArr($p_details["id"], $d_arr["default_shipping_fee"]); }}';
			@else
				var matrix_details_arr = '{{ $product_this_obj->variation_service->getItemMatrixDetailsArr($p_details["id"]); }}';
			@endif
		@endif
		var default_img = '';
		var default_img_img = '';
		@if(isset($d_arr['slider_default_img']) && count($d_arr['slider_default_img']) > 0)
			var default_img = '{{ $d_arr['slider_default_img']['large_img_path'] }}';
		@endif
		@if(isset($d_arr['slider_default_img']['small_img_path']) && count($d_arr['slider_default_img']['small_img_path']) > 0)
			var default_img_img = '{{ $d_arr['slider_default_img']['small_img_path'] }}';
		@endif
	@endif
		var deal_price_lbl = '{{ Lang::get("deals::deals.deal_price_lbl") }}';
	</script>
@stop