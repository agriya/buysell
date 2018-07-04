<?php
	$route_action = Route::currentRouteAction();
?>

<h1>{{ str_replace('VAR_USERNAME', $user_details['display_name'], trans('myaccount/viewProfile.profile_title')) }}</h1>

<!-- BEGIN: PROFILE INFO -->
<div class="well about-user margin-bottom-20">
    <h3>{{ trans('myaccount/viewProfile.about') }}</h3>
     @if($user_arr['about_me'] != '')
       <p>{{ $user_arr['about_me'] }}</p>
    @endif
    <p class="text-muted">{{ str_replace('VAR_DATE_OF_JOIN', CUtil::FMTDate($user_arr['created_at'], 'Y-m-d H:i:s', ''), trans('myaccount/viewProfile.joined_text')) }}</p>
</div>
<!-- END: PROFILE INFO -->

<div class="row">
<div class="col-md-7 row">
    <!-- BEGIN: FAVORITE SHOP BLOCK -->
    <div class="col-md-12">
        <div id="usershop-list">
        	{{CUtil::getFavoriteShopDetailsView($user_arr['id'], true)}}
        </div>
    </div>
    <!-- END: FAVORITE SHOP BLOCK -->

	<!-- BEGIN: FAVORITE ITEMS BLOCK -->
	<div class="col-md-12">
		<div class="well clearfix">
			{{ CUtil::getFavoriteItemsDetailsView($user_arr['id'], true, 2, 3) }}
		</div>
	</div>
    <!-- END: FAVORITE ITEMS BLOCK -->
</div>
<div class="col-md-5 row">
	<!-- BEGIN: SHOP BLOCK -->
    @if($user_arr['is_shop_owner'])
        <div class="col-md-12">
            <div id="usershop-product" class="well clearfix">
                {{CUtil::getShopDetailsView($user_arr['id'], true, $shop_obj)}}
            </div>
        </div>
    @endif
    <!-- END: SHOP BLOCK -->

    <!-- BEGIN: TREASURY LISTS BLOCK -->
	<div class="col-md-12">
		<div id="usercollection-list">
			{{CUtil::getCollectionDetailsView($user_arr['id'], true)}}
		</div>
	</div>
    <!-- END: TREASURY LISTS BLOCK -->
</div>
</div>
