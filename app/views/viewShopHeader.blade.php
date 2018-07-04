@if(count($shop_details) > 0)
	<?php
        $shop_img = ShopService::getShopImage($shop_details['id'], 'thumb', $shop_details, true, $shop_obj);
    ?>
    
    <!-- BEGIN: SHOP BANNER IMAGE -->
    <div class="featrsel-shpimg">
        <div class="clearfix">
            {{ CUtil::showFeaturedSellersIcon($shop_details['user_id'], $shop_details) }}
        </div>
        <figure>
            @if($shop_img['image_url'] != "")
                <img src="{{$shop_img['image_url']}}" {{$shop_img['image_attr']}} title="{{{ $shop_details['shop_name'] }}}" alt="{{{ $shop_details['shop_name'] }}}" />
            @else
                <img src="{{ URL::asset('/images/no_image/shopnoimage-700x90.jpg') }}" alt="no-image" />
            @endif
        </figure>
    </div>
    <!-- END: SHOP BANNER IMAGE -->
    
    <div class="panel panel-default">
        <div class="panel-header">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="shop-profilename">
                        <h3><a href='javascript:void(0);'>{{{ $shop_details['shop_name'] }}}</a></h3>
                        <p><span>{{{ $shop_details['shop_slogan'] }}}</span></p>
                    </div>
                </div>
                
                <div class="col-md-6 col-sm-6">
                    <ul class="list-inline pad20Zero">
                        @if(Config::get('login.facebook_app_key') != '')
                            <li>
                                <div class="socialbtn-fb">
                                    <div id="fb-root"></div>
                                    <script>(function(d, s, id) {
                                      var js, fjs = d.getElementsByTagName(s)[0];
                                      if (d.getElementById(id)) return;
                                      js = d.createElement(s); js.id = id;
                                      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId="+{{ Config::get('login.facebook_app_key') }}+"&version=v2.0";
                                      fjs.parentNode.insertBefore(js, fjs);
                                    }(document, 'script', 'facebook-jssdk'));</script>
                                    <div class="fb-like" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                                </div>
                            </li>
                        @endif
                        
                        <li>
                            <div class="fav-btn">
                                @if(CUtil::isMember())
                                    <?php $isFavoriteShop = $shop_fav_service->isFavoriteShop($shop_details['id'], $logged_user_id);  ?>
                                    @if($isFavoriteShop)
                                        <a href="javascript:void(0);" data-shopid="{{$shop_details['id']}}" data-userid="{{$shop_details['user_id']}}"   class="btn btn-default js-addto-favorite-shop fnImg"><i class="fa fa-heart text-pink"></i> {{trans('shop.unfavorite')}}</a>
                                    @else
                                        <a href="javascript:void(0);" data-shopid="{{$shop_details['id']}}" data-userid="{{$shop_details['user_id']}}"  class="btn btn-default js-addto-favorite-shop fnImg"><i class="fa fa-heart text-muted"></i> {{trans('shop.favorite')}}</a>
                                    @endif
                                @else
                                    <?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
                                    <a href="{{ $login_url }}" class="fn_signuppop btn btn-default"><i class="fa fa-heart text-muted"></i> {{trans('shop.favorite')}}</a>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    
        <div class="panel-body">
            <!-- BEGIN: SHOP DESCRIPTION -->
            <div class="shop-description">
                {{-- <!--{{ nl2br(CUtil::makeClickableLinks(htmlspecialchars($shop_details['shop_desc']))) }}--> --}}
                <div id="shop_desc_more">
                    @if($shop_details['shop_desc'] != "")
                        {{ nl2br(CUtil::makeClickableLinks($shop_details['shop_desc'])) }}
                    @else
                        {{ trans('shop.no_description') }}
                    @endif
                </div>
            </div>
            <!-- END: SHOP DESCRIPTION -->
        </div>
    </div>
@endif