@extends('base')
@section('content')
    <!-- BEGIN: BOTTOM BANNER GOOGLE ADDS -->
    {{ getAdvertisement('top-banner') }}
    <!-- END: BOTTOM BANNER GOOGLE ADDS -->

    <ul class="index-list list-unstyled">
    	@if(CUtil::chkIsAllowedModule('featuredproducts'))
            <li>
                {{ Products::getFeaturedProductsIndex() }}
            </li>
		@endif

        @if(CUtil::chkIsAllowedModule('featuredsellers'))
            <li>
                {{ HomeCUtil::getFeaturedSellersIndex() }}
            </li>
		@endif

        <li>
            <!-- BEGIN: RECENT FAVORITE ITEM -->
            <div class="container recfav-list">
                <h1>{{trans('home.recent_favorites')}}</h1>
                <h2>{{trans('home.discover_find_from_marketplace')}}</h2>
                @if(isset($recent_fav) && count($recent_fav) > 0)
                    <ul class="list-unstyled row">
                        @foreach($recent_fav as $key => $fav)
                            <li class="col-md-4 col-sm-6 col-xs-12">
                                <div class="recfav-item">
                                    @if(isset($fav['seller_details']) && isset($fav['seller_image']) && isset($fav['seller_shop_details']))
                                        <?php
                                            $fav_seller_location = CUtil::getSellerCityCountry($fav['seller_shop_details']);
                                        ?>
                                        <div class="media">
                                            <a href="{{ $fav['seller_details']['profile_url'] }}" class="pull-left imgusersm-60X60">
                                                <img src="{{ $fav['seller_image']['image_url'] }}" {{ $fav['seller_image']['image_attr'] }} alt="{{ $fav['seller_details']['display_name'] }}" />
                                            </a>
                                            <div class="media-body">
                                                <p>{{trans('common.from')}} <a href="{{ $fav['seller_shop_details']['shop_url'] }}">{{ $fav['seller_shop_details']['shop_name'] }}</a></p>
                                                <p>{{trans('common.in')}} <strong>{{ implode(', ', $fav_seller_location) }}</strong></p>
                                                <p class="margin-top-10">
                                                    {{ Form::input('number','rating',$fav['feed_back_rate'], array('class' => 'rating', 'id' => 'input-21f', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
                                                    <strong>{{ $fav['feed_back_cnt'] }}</strong> {{ Lang::choice('home.review_choice', $fav['feed_back_cnt']) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    <figure>
                                        <a href="{{ $fav['fav_product']['product_url'] }}">
                                            <img src="{{ $fav['fav_product']['p_thumb_img']['image_url'] }}" alt="{{ htmlentities($fav['fav_product']['product_name']) }}" />
                                        </a>
                                        <figcaption>
                                            <h3><a href="{{ $fav['fav_product']['product_url'] }}">{{CUtil::wordWrap(htmlentities($fav['fav_product']['product_name']), 30) }}.</a></h3>
                                            @if($fav['fav_product']['is_free_product'] == 'Yes')
                                                <label class="label label-primary label-free no-margin">{{ Lang::get('common.free') }}</label>
                                            @else
                                                <?php $price = $fav['fav_product']['product_price']; ?>
                                                @if($price['disp_price'] && $price['disp_discount'])
                                                    <p>{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                                @elseif($price['disp_price'])
                                                    @if($price['product']['price'] > 0)
                                                        <p>{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                                    @else
                                                        <strong class="label label-primary label-free no-margin">{{ Lang::get('common.free') }}</strong>
                                                    @endif
                                                @endif
                                            @endif
                                        </figcaption>
                                    </figure>
                                    <ul class="list-unstyled clearfix">
                                        @if(isset($fav['seller_products']) && count($fav['seller_products']) > 0)
                                            @foreach($fav['seller_products'] as $key => $val)
                                                <li>
                                                    <a href="{{ $val['product_url'] }}">
                                                        <img src="{{ $val['p_thumb_img']['image_url'] }}" {{ $val['p_thumb_img']['image_attr'] }} title="{{ nl2br($val['product_name']) }}" alt="{{ nl2br($val['product_name']) }}" />
                                                    </a>
                                                </li>
                                            @endforeach
                                            @if($fav['seller_products_total'] > 3)
                                                <li><a href="{{ $fav['seller_shop_details']['shop_url'] }}" class="tot-prdct"><span><strong>{{ $fav['seller_products_total'] }}</strong> {{ Lang::choice('common.item_choice', $fav['seller_products_total']) }}</span></a></li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-info no-margin">{{trans('home.no_recent_favorites')}}.</div>
                @endif
            </div>
            <!-- END: RECENT FAVORITE ITEM -->
        </li>
        <li>
            <!-- BEGIN: TOP PICK'S FAVORITE ITEM -->
            <div class="container toppicks-list">
                <h1>{{trans('home.community_tastemakers')}}</h1>
                <h2>{{ Lang::get('home.get_inspiration_from_member_top_picks', array('site_name' => Config::get('generalConfig.site_name'))) }}</h2>
                @if(isset($top_picks) && count($top_picks) > 0)
                    <div class="margin-top-m15">
                        <ul class="list-unstyled row">
                            @foreach($top_picks as $key => $pick)
                                <li class="col-md-3 col-sm-4 col-xs-4">
                                    <div class="tooltip-hover">
                                        <!-- BEGIN: TOOLTIP -->
                                        <div class="tooltip-new" role="tooltip">
                                            <div class="tooltip-newarrow"><i class="fa fa-caret-down"></i></div>
                                            <div class="tooltip-newinner">
                                                <a href="{{ $pick['user_details']['favorites_url'] }}" class="imgusersm-32X32"><img src="{{ $pick['user_image']['image_url'] }}"  {{ $pick['user_image']['image_attr'] }} title="{{ $pick['user_details']['display_name'] }}" alt="{{ $pick['user_details']['display_name'] }}" /></a>
                                                <h3>{{ str_limit($pick['user_details']['display_name'], $limit = 6, $end = '...') }} <span>{{trans('home.favorites')}}</span></h3>
                                            </div>
                                        </div>
                                        <!-- END: TOOLTIP -->

                                        <div class="recfav-item">
                                            <div class="toppick-item clearfix">
                                                <a href="{{ $pick['user_details']['favorites_url'] }}" class="arrow-icon"><span></span></a>
                                                <ul class="list-unstyled clearfix">
                                                    <?php $remain_prod_cnt = 0; ?>
                                                    @if(isset($pick['fav_product']) && count($pick['fav_product']) > 0)
                                                        <?php $remain_prod_cnt = 4 - count($pick['fav_product']); ?>
                                                        @foreach($pick['fav_product'] as $key => $val)
                                                            <li>
                                                                <a href="{{ $pick['user_details']['favorites_url'] }}">
                                                                    <img src="{{ $pick['fav_product'][$key]['p_thumb_img']['image_url'] }}" {{ $pick['fav_product'][$key]['p_thumb_img']['image_attr'] }} alt="image" />
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                    @while($remain_prod_cnt > 0)
                                                        <li>
                                                            <a href="{{ $pick['user_details']['favorites_url'] }}">
                                                                <img src="{{ URL::asset('images/no_image/prodnoimage-215x170.jpg') }}" alt="image" />
                                                            </a>
                                                        </li>
                                                        @if($remain_prod_cnt = $remain_prod_cnt - 1)@endif
                                                    @endwhile
                                                </ul>
                                            </div>
                                            <figcaption>
                                                <h3><a href="{{ $pick['user_details']['favorites_url'] }}">{{Lang::get('home.users_favorites', array('user_name' => $pick['user_details']['display_name'])) }}</a></h3>
                                                <p>{{ $pick['user_total_fav_produts'] }} {{ Lang::choice('common.item_choice', $pick['user_total_fav_produts']) }}</p>
                                            </figcaption>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="alert alert-info no-margin margin-top-10">{{trans('home.no_community_tastmakers')}}.</div>
                @endif
            </div>
            <!-- END: TOP PICK'S FAVORITE ITEM -->
        </li>
        <li>
            <!-- BEGIN: FEATURED LIST -->
            <div class="container feature-list">
                <ul class="row list-unstyled text-center">
                    <li class="col-md-4 col-sm-6 col-xs-12">
                        <figure><img src="{{ URL::asset('images/general/customer.png') }}" alt="customer passionate" /></figure>
                        <h4>{{trans('home.satisfied_customers')}}</h4>
                        <p>{{trans('home.get_to_shops_item_review_from_community')}}.</p>
                    </li>
                    <li class="col-md-4 col-sm-6 col-xs-12">
                        <figure><img src="{{ URL::asset('images/general/passionate_seller.png') }}" alt="customer passionate" /></figure>
                        <h4>{{trans('home.passionate_sellers')}}</h4>
                        <p>{{trans('home.buy_from_creative_people')}}.</p>
                    </li>
                    <li class="col-md-4 col-sm-6 col-xs-12">
                        <figure><img src="{{ URL::asset('images/general/ido-percent.png') }}" alt="customer passionate" /></figure>
                        <h4>{{trans('home.secure_transactions')}}</h4>
                        <p>{{trans('home.feel_confident_knowing_our_trust')}}.</p>
                    </li>
                </ul>

                <!-- BEGIN: NEWSLETTER -->
                {{ Form::open(array('url' => '', 'class' => 'form-horizontal setas-shipping',  'id' => 'subscribe_frm', 'name' => 'subscribe_frm')) }}
                    <div class="news-letter">
                        <p>{{Lang::get('home.get_top_friends_and_editors', array('site_name' => Config::get('generalConfig.site_name'))) }}.</p>
                        <div class="col-md-8 col-sm-8 col-xs-8 padding-left-0">
                            {{ Form::text('email', '', array('class' => 'form-control', 'id' => 'email', 'placeholder' => trans('home.enter_your_email'))) }}
                            <label class="error">{{{ $errors->first('email') }}}</label>
                        </div>
                        <button type="submit" class="btn btn-green green">{{trans('common.subscribe')}}</button>
                    </div>
                {{ Form::close() }}
                <!-- END: NEWSLETTER -->
            </div>
            <!-- END: FEATURED LIST -->
        </li>
    </ul>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('common.required')}}";
        var valid_email = '{{trans('common.enter_valid_email')}}';

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
        });

        $("#subscribe_frm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: mes_required,
                    email: valid_email
                }
            },
            submitHandler: function(form) {
                displayLoadingImage(true);

                var actions_url = '{{ URL::action('HomeController@postSubscribeNewsletter')}}';
                var email = $('#email').val();
                postData = 'email=' + email,
                $.post(actions_url, postData,  function(response)
                {
                    data = eval( '(' +  response + ')');
                    if(data.status == 'success') {
                        $('#email').next('label.error').css("display", "inline");
                        $('#email').next('label.error').text(data.status_msg);
                    }
                    else {
                        var errorDiv = '#error';
                        $('#email').next('label.error').css("display", "inline");
                        $('#email').next('label.error').text(data.status_msg);
                    }
                    hideLoadingImage (false);
                });
            }
        });
    </script>
@stop