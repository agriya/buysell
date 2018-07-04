<!-- BEGIN: SHOP SLOGAN ->
{{--<div class="well">
    <h4>{{{ $shop_details['shop_name'] }}}</h4>
    <div class="well-new margin-top-10">
        <p class="no-margin">{{{ $shop_details['shop_slogan'] }}}</p>
        (<strong>'.$default_section_details['section_count'].'</strong>)//total product count
    </div>
</div>--}}
<!-- END: SHOP SLOGAN -->

<!-- BEGIN: PRODUCT SECTIONS -->
@if(count($section_details) > 0)
	<div class="well">
        <h4>{{ trans("shop.shop_sections") }}</h4>
        <div class="well-new">
            <ul class="list-unstyled">
                @foreach($section_details AS $section)
                    <?php
                        $section_view_url = $shop_view_url."?section_id=".$section['id'];
                    ?>
                    <li><a href="{{$section_view_url.'#shop_products'}}" title="{{{ $section['section_name'] }}}">{{{ $section['section_name'] }}}: {{ $section['section_count'] }} {{ trans('common.items') }}</a></li>
                @endforeach
                <li class="text-right"><a href="{{$default_section_details['section_view_url'].'#shop_products'}}" title="{{ trans('common.view_all_product') }}">
                &raquo; {{ trans('common.view_all_product') }}</a></li>
            </ul>
        </div>
    </div>
@endif
<!-- END: PRODUCT SECTIONS -->

<!-- BEGIN: SHOP OWNER DETAILS -->
<div class="well">
    <h4>{{ trans("shop.shop_owner") }}</h4>
    <div class="well-new">
		<?php
            $user_details = CUtil::getUserDetails($shop_details['user_id']);
            $user_image = CUtil::getUserPersonalImage($shop_details['user_id'], "small", false);
            $seller_location = CUtil::getSellerCityCountry($shop_details);
        ?>
        <div class="shopuser-profile">
            <a href='{{$user_details['profile_url']}}' class="imguserborsm-56X56">
			<img  src="{{ $user_image['image_url'] }}" {{ $user_image['image_attr'] }} alt="{{ $user_details['display_name'] }}" title="{{ $user_details['display_name'] }}" /></a>
            <h3><a href='{{$user_details['profile_url']}}'>{{ $user_details['display_name'] }}</a></h3>
            <p class="no-margin"><span>{{ implode(', ', $seller_location) }}</span></p>
			<!-- BEGIN: ASK A QUEST DETAILS -->
	        @if(CUtil::isMember())
	            @if(!$viewShopServiceObj->current_user)
	            	<p class="margin-bottom-5 margin-top-20"><strong class="text-muted">{{ trans('shop.have_question') }}</strong></p>
	                <p class="show"><a href="javascript:void(0);" onclick="ContactShopOwner('{{ URL::to('shop/user/message/add/'.$user_details['user_code']) }}')" class="label label-primary pad7 border-r3">
					{{ trans('shop.contact_shop_owner') }}</a></p>
	            @endif
	        @else
	        	<p class="margin-bottom-5 margin-top-20"><strong class="text-muted">{{ trans('shop.have_question') }}</strong></p>
	            <?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
	            <p class="show"><a href="{{ $login_url }}" class="fn_signuppop label label-primary pad7 border-r3">{{ trans('shop.contact_shop_owner') }}</a></p>
	        @endif
	        <!-- END: ASK A QUEST DETAILS -->
        </div>
    </div>
</div>
<!-- END: SHOP OWNER DETAILS-->

<!-- BEGIN: SHOP INFO DETAILS -->
<div class="well">
    <h4>{{ trans("shop.shop_info") }}</h4>
    <div class="well-new">
    	<div class="margin-top-10 margin-left-4"><a href="{{ $shop_view_url }}"><i class="fa fa-home text-muted"></i> {{{ $shop_details['shop_name'] }}}</a></div>
    	@if($shop_created_date = CUtil::FMTDate($shop_details['created_at'], 'Y-m-d H:i:s', ''))@endif
        <p class="margin-top-5 margin-left-4"><span>{{ trans('shop.opened_on') }} @if($shop_created_date != ''){{ $shop_created_date }}@else -- @endif</span></p>

        <ul class="list-unstyled">
            <?php
				$feed_back = $feedback_service->getFeedbackCountBySellerId($shop_details['user_id']);
				$feed_back_cnt = array_sum($feed_back);
				$feed_back_rate = CUtil::calculateFeedbackRate($feed_back);
				$admirers_cnt = $shop_fav_service->totalFavoritesForShop($shop_details['id']);
				$rating_det = $feedback_service->getAvgRatingForSeller($shop_details['user_id']	);
			?>

			<li><a href="{{$shop_view_url.'/shop-policy'}}">{{ trans("shop.policies") }}</a></li>
            @if(isset($viewShopServiceObj->total_products) && $viewShopServiceObj->total_products > 0)
                <li><a href="{{$shop_view_url}}">{{ $viewShopServiceObj->total_products}} {{trans("shop.products_for_sale") }}</a></li>
            @endif

			<li>
				<a href="{{$shop_view_url.'/shop-reviews'}}">{{ trans('common.reviews') }}</a>
				@if(isset($rating_det['avg_rating']) && $rating_det['avg_rating'] >=0)
					{{ Form::input('number','rating',$rating_det['avg_rating'], array('class' => 'rating', 'id' => 'input-21f', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
					({{$rating_det['rating_count']}})
				@endif
				<!--
				<p class="review-rating margin-0"><a href="{{$shop_view_url.'/shop-reviews'}}">
				<strong><span style="width:{{ $feed_back_rate }}%;">review rating</span></strong> ({{ $feed_back_cnt }})</a></p>
				-->
			</li>

            @if(isset($viewShopServiceObj->total_sales) && $viewShopServiceObj->total_sales > 0)
                <li><a href="{{$shop_view_url.'?orderby_field=product_sold'}}">{{ $viewShopServiceObj->total_sales}} {{trans("shop.sales") }}</a></li>
            @endif
			@if($admirers_cnt > 0)
				<li><a href="{{$shop_fav_view_url}}">{{ $admirers_cnt }} admirers</a></li>
			@endif
			<!--<li><a data-toggle="modal" data-target="#reportItem" class="">{{trans("shop.contact_info") }}</a></li>-->
	    </ul>
		<!-- START: SHOP CONTACT -->
		<!--
		<div class="modal fade" id="reportItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<div class="margin-top-10 pull-right">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">{{trans('common.close')}}</span></button>
						</div>
						<div id="selSharehedding"><h1 class="margin-0">{{Lang::get('shop.contact_info')}}</h1></div>
					</div>

					<div class="modal-body">
						<div id="report_message_div">{{ $shop_details['shop_contactinfo'] }}</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn red" data-dismiss="modal"><i class="fa fa-times"></i> {{Lang::get('common.close')}}</button>
					</div>
				</div>
			</div>
		</div>-->
		<!-- END: SHOP CONTACT -->
    </div>
</div>
<!-- END: SHOP INFO DETAILS -->

<!-- BEGIN: ACTIONS -->
<div class="well">
    <h4>{{ trans('common.actions') }}</h4>
    <div class="well-new">
    	<ul class="list-unstyled">
    		<li>
				@if(CUtil::isMember())
                    <?php $isFavoriteShop = $shop_fav_service->isFavoriteShop($shop_details['id'], $logged_user_id);  ?>
					@if($isFavoriteShop)
						<a href="javascript:void(0);" data-shopid="{{$shop_details['id']}}" data-userid="{{$shop_details['user_id']}}" class="js-addto-favorite-shop fnTxt">
						<i class="fa fa-heart text-pink"></i> {{trans('shop.added_to_favorites')}}</a>
					@else
						<a href="javascript:void(0);" data-shopid="{{$shop_details['id']}}" data-userid="{{$shop_details['user_id']}}" class="js-addto-favorite-shop fnTxt">
						<i class="fa fa-heart text-muted"></i> {{ trans('shop.add_to_favorites') }}</a>
					@endif
                @else
                    <?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
                    <a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart text-muted"></i> {{trans('shop.add_to_favorites')}}</a>
                @endif
			</li>
			<li><a href="{{ $shop_fav_view_url }}"><i class="fa fa-heart-o text-pink"></i> {{ trans("shop.see_who_favorites_this") }}</a></li>
    		<li>
                <a class='twitter-share-button' data-count='horizontal' expr:data-counturl='data:post.url' expr:data-text='data:post.title' expr:data-url='data:post.url' data-via='' data-related='' href='http://twitter.com/share'>Tweet</a>
                <script type="text/javascript">
                    window.twttr=(function(d,s,id){var t,js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);return window.twttr||(t={_e:[],ready:function(f){t._e.push(f)}})}(document,"script","twitter-wjs"));
                </script>
            </li>
    		{{--<li>{{ trans('shop.report_this_shop_to_buysell') }}</li>--}}
    	</ul>
    </div>
</div>
<!-- END: ACTIONS -->
