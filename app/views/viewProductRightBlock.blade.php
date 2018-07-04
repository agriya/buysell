@if($d_arr['error_msg'] == '')
    <?php
        $user_details = CUtil::getUserDetails($p_details['product_user_id']);
        $shipping_total_price = 0;
    ?>
    <!-- BEGIN: STOCK AVAILABILITY -->
    <!--<div class="well">
    	@if($stock_count !=0)
    	<b>{{ trans('admin/productList.product_stock_count') }}</b> :
		<span>{{ $stock_count }}</span>
		@else
		<b>{{ trans('admin/productList.product_stock_count') }}</b> :
		<span>0</span>
		@endif
    </div> -->
    <!-- END: STOCK AVAILABILITY -->

    @if($d_arr['alert_msg'] == '')
	    <!-- BEGIN: ITEM DEALS PAGE -->
		@if(CUtil::chkIsAllowedModule('deals'))
			<?php
				$input_det_arr = array();
				$input_det_arr['item_id'] = $p_details['id'];
				$input_det_arr['item_owner_id'] = $p_details['product_user_id'];
				$deal_details =  $product_this_obj->deal_service->fetchItemDealDetails($input_det_arr);
			?>
			@include('deals::itemDealsBlock')
	   	@endif
		<!-- END: ITEM DEALS PAGE -->
	@endif

	<div class="well">
        <!-- BEGIN: PRICE DETAILS -->
        @if($p_details['is_free_product'] == 'No')
            <?php
            	$price_group = $service_obj->getPriceGroupsDetailsNew($p_details['id'], $logged_user_id, 1, 1, false);
            	$product_price = $product_price_arr = '';
            	$discount_price = 0;
	            $have_discount = false;
				if(count($price_group) > 0 && isset($price_group['price']) && $price_group['price']!='') {
					$shipping_total_price = $price_group['price'];
	                $product_price = CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true);
	                $product_price_arr = CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true, false, true);
	                /*$curr_time = strtotime(date('Y-m-d'));
	                if($p_details['product_discount_todate'] != '0000-00-00' && $p_details['product_discount_fromdate'] != '0000-00-00')
	                {
	                    $discount_from_date = strtotime($p_details['product_discount_fromdate']);
	                    $discount_end_date = strtotime($p_details['product_discount_todate']);
	                    if($discount_end_date >= $curr_time && $discount_from_date <= $curr_time)
	                    {
	                    	$have_discount = true;
	                    }
	                }
	                else if($p_details['product_discount_fromdate'] != '0000-00-00')
					{
						$discount_from_time = strtotime($p_details['product_discount_fromdate']);
						if($discount_from_time <= $curr_time)
						{
							$have_discount = true;
						}
					}
					else if($p_details['product_discount_todate'] != '0000-00-00')
					{
						$discount_end_time = strtotime($p_details['product_discount_todate']);
						if($discount_end_time >= $curr_time)
						{
							$have_discount = true;
						}
					}
					else
	                {
	                    if($p_details['product_discount_price'] > 0) {
	                    	$have_discount = true;
	                    }
	                }*/
	                if($price_group['discount'] > 0 && $price_group['price'] > $price_group['discount']) {
	                	$have_discount = true;
	                }
					if($have_discount) {
						$shipping_total_price = $price_group['discount'];
						$discount_price = CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true);
	                    $discount_price_arr = CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true, false, true);
					}
				}
            ?>
        @endif
        <!-- END: PRICE DETAILS -->

        <?php $enable_serivices = true; ?>

        <!-- BEGIN: DISPLAY PRODUCT PRICE -->
        @include('displayProductPrice')
        <!-- END: DISPLAY PRODUCT PRICE -->

		<!-- BEGIN: REPORT LISTING MODAL BOX -->
		@if(CUtil::isMember())
			<div class="text-right">
				<a data-toggle="modal" data-target="#reportItem" class="btn blue btn-xs">
					{{Lang::get('viewProduct.report_item_to')}} {{Config::get('generalConfig.site_name')}}<i class="fa fa-angle-double-right margin-left-5"></i>
				</a>
			</div>
			<!-- BEGIN: INCLUDE LISTING MODAL BOX -->
			@include('reportThisItemBlock')
			<!-- BEGIN: INCLUDE  REPORT LISTING MODAL BOX -->
		@endif
		<!-- END: REPORT LISTING MODAL BOX -->
    </div>

    <!-- BEGIN: SOCIAL LINK -->
    <div class="well">
        <div class="soc-link clearfix">
            <ul class="list-unstyled">
                <li>
                    <div class="fav-btn pull-left">
                    	@if(CUtil::isMember())
	                        @if($d_arr['is_favorite_product'])
	                    		<a href="javascript:void(0);" data-productid="{{$p_details['id']}}" class="btn btn-default js-addto-favorite">
								<i class="fa fa-heart text-pink"></i> {{Lang::get('viewProduct.unfavorite')}}</a>
	                    	@else
	                    		<a href="javascript:void(0);" data-productid="{{$p_details['id']}}" class="btn btn-default js-addto-favorite">
								<i class="fa fa-heart text-muted"></i> {{Lang::get('viewProduct.favorite')}}</a>
	                    	@endif
	                    @else
                        	<?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
        					<a href="{{ $login_url }}" class="fn_signuppop btn btn-default"><i class="fa fa-heart text-muted"></i> {{Lang::get('viewProduct.favorite')}}</a>
                        @endif
                    </div>
                    <span class="fav-list"><a id="js-fav-count" href="javascript:void(0);">{{ $d_arr['prod_fav_cnt'] }}</a></span>
                </li>
                <li class="dropdown fav-btn">
                    {{--<div class="fav-btn">
                        <a href="javascript:void(0);" class="btn btn-default">
                            <span class="fa fa-list"> <i class="fa fa-caret-down"></i></span>{{ Lang::get('common.add_to')}}
                        </a>
                    </div>
                    --}}
                    @if(CUtil::isMember())
						<a href="javascript:void(0);" id="view_item_{{$p_details['id']}}" data-block="view_item" data-productid="{{$p_details['id']}}" data-toggle="dropdown" class="btn btn-default js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i>{{ Lang::get('common.add_to')}}</a>
						<div id="view_item_holder_{{$p_details['id']}}" class="dropdown-menu custom-dropdown fnFavListHolder">
						<div>
					@else
    					<a href="{{ $login_url }}" class="fn_signuppop btn btn-default"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i>{{ Lang::get('common.add_to')}}</a>
                    @endif
                </li>
            </ul>
        </div>
        @if($d_arr['alert_msg'] == '')
	        <ul class="list-inline viewitem-share clearfix">
	        	<!-- BEGIN: TWITTER TWEET BUTTON -->
	            <li>
                    <a class='twitter-share-button' data-count='horizontal' expr:tw:counturl='data:blog.url' expr:data-counturl='data:post.url' expr:data-text='data:post.title' expr:data-url='data:post.url' data-via='' data-related='' href='http://twitter.com/share'>Tweet</a>
                    <script type="text/javascript">
                        window.twttr=(function(d,s,id){var t,js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);return window.twttr||(t={_e:[],ready:function(f){t._e.push(f)}})}(document,"script","twitter-wjs"));
                    </script>
	            </li>
		        <!-- END: TWITTER TWEET BUTTON -->


				<!-- BEGIN: FACEBOOK LIKE BUTTON -->
				@if(Config::get('login.facebook_app_key') != '')
                    <li>
                        <div id="fb-root"></div>
                        <script>(function(d, s, id) {
                          var js, fjs = d.getElementsByTagName(s)[0];
                          if (d.getElementById(id)) return;
                          js = d.createElement(s); js.id = id;
                          js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId="+{{ Config::get('login.facebook_app_key') }}+"&version=v2.0";
                          fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));</script>
                        <div class="fb-like" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                    </li>
	            @endif
				<!-- END: FACEBOOK LIKE BUTTON ->

				<!-- BEGIN: GOOGLE PLUS BUTTON -->
	            <li>
					<!-- PLACE THIS TAG WHERE YOU WANT THE +1 BUTTON TO RENDER -->
					<g:plusone size="medium"></g:plusone>
					<!-- Place this render call where appropriate -->
					<script type="text/javascript">
					(function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					})();
					</script>
	            </li>
		       <!-- END: GOOGLE PLUS BUTTON -->
			</ul>
		@endif
    </div>
    <!-- END: SOCIAL LINK -->

    <!-- BEGIN: ITEM DETAILS -->
    <!--
	<div class="well">
        <h2 class="no-top-space">{{ trans('viewProduct.item_details') }}:</h2>
        <div class="dl-horizontal dl-horizontal-new margin-top-20">
        	@if(Config::get('generalConfig.user_allow_to_add_product'))
            	<dl>
                    <dt>{{ trans('viewProduct.shop_owner_name') }}</dt>
                    <dd><span>{{ $user_details['display_name'] }}</span></dd>
                </dl>
                @if(count($d_arr['shop_details']) > 0)
                    <?php
                        //$shop_url = URL::to('shop/'.$d_arr['shop_details']['url_slug']);
                    ?>
                    <dl>
                        <dt>{{ trans('viewProduct.shop_details') }}</dt>
                        <dd><span><a href="{{ $shop_url }}" title="{{{ $d_arr['shop_details']['shop_name'] }}}"class="light-link text-ellipsyfy">{{{ $d_arr['shop_details']['shop_name'] }}}</a></span></dd>
                    </dl>
                @endif
            @endif
            <dl>
                <dt>{{ trans('viewProduct.listed_on') }}</dt>
                <dd><span>@if($p_details['date_activated'] == '0') {{ trans('viewProduct.not_available') }}  @else {{ date('M d, Y', $p_details['date_activated'])  }} @endif</span></dd>
            </dl>
            <dl>
                <dt>{{ trans('viewProduct.listing_id') }}</dt>
                <dd><span>#{{ $p_details['product_code'] }}</span></dd>
            </dl>
            <dl>
                <dt>{{ Lang::choice('viewProduct.view_choice', $p_details['total_views']) }}</dt>
                <dd><span>{{ $p_details['total_views'] }}</span></dd>
            </dl>
        </div>
    </div>
    -->
    <!-- END: ITEM DETAILS -->

    <!-- BEGIN: ATTRIBUTE DETAILS  -->
    @if(count($d_arr['product_attr']) > 0)
        <div class="well">
            <h2 class="no-top-space">{{ trans('viewProduct.attributes_of_the_product') }}:</h2>
            <div class="dl-horizontal dl-horizontal-new margin-top-20">
                @foreach($d_arr['product_attr'] as $attr_det)
                    <dl>
                        <dt>{{$attr_det['attribute_label']}}</dt>
                        <dd><span>{{$attr_det['attribute_value']}}</span></dd>
                    </dl>
                @endforeach
            </div>
        </div>
    @endif
	<!-- END: ATTRIBUTE DETAILS -->

    <!-- BEGIN: ITEM LIST -->
    <div class="well clearfix vwpgrgt-featrsell">
    	@if(isset($d_arr['shop_details']))
            <div class="media text-center">
                <?php
                    $user_image_details = CUtil::getUserPersonalImage($d_arr['shop_details']['user_id'], 'thumb');
                    $seller_location = CUtil::getSellerCityCountry($d_arr['shop_details']);
                ?>
                <a href="{{ $d_arr['shop_details']['shop_url'] }}" class="bs-imgrounded">
                <img class="img-circle" src="{{ $user_image_details['image_url'] }}" {{ $user_image_details['image_attr'] }} alt="image" /></a>
                <div class="media-body">
                    <h2><a href="{{ $d_arr['shop_details']['shop_url'] }}" title="{{{ $d_arr['shop_details']['shop_name'] }}}">{{{ $d_arr['shop_details']['shop_name'] }}}</a>
                    {{ CUtil::showFeaturedSellersIcon($d_arr['shop_details']['user_id'], $d_arr['shop_details']) }}</h2>
                    @if(!empty($seller_location))<p>in {{ implode(', ', $seller_location) }}</p>@endif
                </div>
            </div>
		@endif

		@if(count($d_arr['shop_item_details']) > 0)
			<ul class="list-unstyled viewpg-item">
				@foreach($d_arr['shop_item_details'] AS $prod)
					<?php
                        $shop_product = Products::initialize();
                        $p_img_arr = $shop_product->getProductImage($prod['id']);
                        $p_thumb_img = $service_obj->getProductDefaultThumbImage($prod['id'], 'thumb', $p_img_arr);
                        $view_url = $service_obj->getProductViewURLNew($prod['id'], $prod);
                        $price = $service_obj->formatProductPriceNew($prod);
                        $is_free_product = $prod['is_free_product'];
                    ?>
                    <li>
                        <div class="bs-prodet">
                            {{ CUtil::showFeaturedProductIcon($prod['id'], $prod); }}
                            <ul id="shop_item_holder_main_{{ $prod['id'] }}" class="list-unstyled favset-icon">
                                <?php
                                    $is_favorite_prod = $productFavoritesService->isFavoriteProduct($prod['id'], $logged_user_id);
                                    $login_url = URL::to('users/login?form_type=selLogin');
                                ?>
                                <li>
                                    @if(CUtil::isMember())
                                        @if($is_favorite_prod)
                                            <a href="javascript:void(0);" data-productid="{{$prod['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-pink"></i></a>
                                        @else
                                            <a href="javascript:void(0);" data-productid="{{$prod['id']}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-muted"></i></a>
                                        @endif
                                    @else
                                        <a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart"></i></a>
                                    @endif
                                </li>
                                <li class="dropdown">
                                    @if(CUtil::isMember())
                                        <a href="javascript:void(0);" id="shop_item_{{$prod['id']}}" data-productid="{{$prod['id']}}" data-block="shop_item" data-toggle="dropdown" class="js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
                                        <div id="shop_item_holder_{{$prod['id']}}" class="dropdown-menu custom-dropdown fnFavListHolder">
                                        <div>
                                    @else
                                        <a href="{{ $login_url }}" class="fn_signuppop"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
                                    @endif
                                </li>
                            </ul>

                            <div class="prodet-img">
                                <a href="{{ $view_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} alt="{{{ nl2br($prod->product_name)  }}}" /></a>
                            </div>
                            <div class="prodet-info">
                                <h3><a href="{{ $view_url }}">{{CUtil::wordWrap(htmlentities($prod->product_name), 20) }}</a></h3>

                                @if($is_free_product == 'Yes')
                                    <label class="label label-primary label-free no-margin">{{ Lang::get('common.free') }}</label>
                                @else
                                    @if($price['disp_price'] && $price['disp_discount'])
                                        <p>
                                            {{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}
                                        </p>
                                    @elseif($price['disp_price'])
                                        @if($price['product']['price'] > 0)
                                            <p>{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                        @else
                                            <strong class="label label-primary label-free no-margin">{{ Lang::get('common.free') }}</strong>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </li>
				@endforeach
			</ul>
        @endif
    </div>
    <!-- END: ITEM LIST -->

    {{-- BEGIN: PRICE DETAILS
		<?php $enable_serivices = false; ?>
        @include('displayProductPrice')
    END: PRICE DETAILS --}}
@endif