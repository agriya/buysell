@extends('base')
@section('content')
	<div class="responsive-pull-none">
		<h1>
			{{ $deal_details->deal_title }}
			@if($deal_serviceobj->chkIsFeaturedDeal($deal_details->deal_id))
				<span class="label bg-purple-plum fonts12">{{ Lang::get('deals::deals.featured_deal_head') }}</span>
			@endif
		</h1>
	</div>

	<!-- BEGIN: INCLUDE NOTIFICATIONS -->
	@include('notifications')
	<!-- END: INCLUDE NOTIFICATIONS -->

	<?php
		$d_thumb_img = $d_arr['deal_thumb_image'];
		$deal_thumb_share_img = $d_arr['deal_share_image'];
		$view_url = $deal_serviceobj->getDealViewUrl($deal_details);
		$shopDetails =	$d_arr['shop_details'];
		$shop_url= (isset($shopDetails->url_slug)) ? URL::to('shop/'.$shopDetails->url_slug) : "";
		$shop_img = ShopService::getShopImage($shopDetails['id'], 'small', $shopDetails, true, $shop_obj);
		$expiry_details = $deal_serviceobj->dealExpiryDetails($deal_details->date_deal_from, $deal_details->date_deal_to);
		$deal_owner_details = CUtil::getUserDetails($deal_details->user_id);
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$show_status = 0;
		if($logged_user_id > 0 && (($logged_user_id == $deal_details->user_id) || CUtil::isAdmin()))
		{
			$show_status =1;
		}
		$status_lbl = (Lang::has('deals::deals.DEAL_STATUS_'.strtoupper($deal_details->deal_status))) ? Lang::get('deals::deals.DEAL_STATUS_'.strtoupper($deal_details->deal_status)): str_replace('_', ' ', $deal_details->deal_status);

		$deal_start_date = strtotime($deal_details->date_deal_from);
		$curr_date = strtotime(date('Y-m-d'));
		$is_pre_start = ($deal_start_date > $curr_date ) ? 1 : 0;
	?>

	<div class="row">
		<div class="col-md-7 viewdeal-lft">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="text-center viewbg-deal">
						@if($deal_details->deal_status == "to_activate")
                            <span class="label label-warning">{{ $status_lbl }}</span>
						@elseif($deal_details->deal_status == "active")
						@elseif($deal_details->deal_status == "deactivated")
                            <span class="label bg-red-pink">{{ $status_lbl }}</span>
						@elseif($deal_details->deal_status == "expired")
                            <span class="label bg-red">{{ $status_lbl }}</span>
						@elseif($deal_details->deal_status == "closed")
                            <span class="label label-default">{{ $status_lbl }}</span>
						@endif
						<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$d_thumb_img['image_url']}}" {{$d_thumb_img['image_attr']}} title="{{{ $d_thumb_img['title']  }}}" alt="{{{ $d_thumb_img['title']  }}}" /></a>
					</div>

                    <?php /* ?>

                    <!-- AddThis Button BEGIN -->
                    <div class="mt5 addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="{{ $view_url }}" addthis:title="{{{ $d_thumb_img['title']  }}}" addthis:description="{{ $deal_details->deal_description }}">
                        <a class="addthis_button_twitter"></a>
                        <!--<a class="addthis_button_facebook"></a>-->
                        <a class="addthis_button_facebook btn btn-xs btn-primary" href="#" onclick="return addthis_sendto('facebook');" alt="facebook share" title="facebook share">
                        </a>
                        <a class="addthis_button_compact"></a>
                    </div>
                    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-522053281afdebad"></script>
                    <script>
                       var addthis_share = {
					   		url: '{{ $view_url }}',
							//title: encodeURIComponent('{{ ($deal_details->deal_title) }}'),
							title: $('#fbsharetitle').html(),
							//description: ('{{ ($deal_details->deal_description) }}'),
							description: $('#fbsharedescription').html(),
							screenshot: '{{$deal_thumb_share_img["image_url"]}}',
                            templates : {
                                twitter : "{{ $deal_details->deal_title }} {{ $view_url }} (via @ {{Config::get('generalConfig.site_name')}})"
                            }
                        }
                    </script>
                    <!-- AddThis Button END -->
                    <?php */ ?>

					<!-- BEGIN: SHARING BLOCK -->
                    @if($deal_details->deal_status == "active")
                    <div class="mt5 addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="{{ $view_url }}" addthis:title="{{{ $d_thumb_img['title']  }}}" addthis:description="{{{ $deal_details->deal_description }}}">
                        <ul class="list-unstyled margin-top-20 deals-share">
                            <li>{{ Lang::get('deals::deals.share_this_lbl') }}:</li>
                            <li>
                                <a class="btn btn-xs btn-primary" href="#" onclick="return addthis_sendto('facebook');" alt="facebook share" title="facebook share">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a class="addthis_button_twitter btn btn-xs btn-info" href="#" onclick="return addthis_sendto('twitthis');" alt="twitthis" title="twitthis">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button btn btn-xs btn-success">
                                    <i class="fa fa-angle-down"><sub class="fa fa-angle-up"></sub></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
					<!-- END: SHARING BLOCK -->
					<div style="display:none" id="fbsharetitle" class="title-three margin-top-20">{{ $deal_details->deal_title }}</div>
					<div style="display:none" id="fbsharedescription">{{ $deal_details->deal_description }}</div>
				</div>

				<div class="panel-footer clearfix panel-grey">
					<h3 class="title-three">{{ Lang::get('deals::deals.summary_lbl') }}</h3>
					<p>{{ $deal_details->deal_description }}</p>
				</div>
			</div>
		</div>

		<div class="col-md-5 bs-viewpage">
			<div class="row">
				<div class="col-md-8">
					<h2 class="title-two">
						<span class="show fonts12 margin-top-5">
							{{ Lang::get('deals::deals.by_lbl') }}
							<a href="{{ $deal_owner_details['profile_url'] }}" title="{{ $deal_owner_details['display_name'] }}" class="text-success">
								<strong>{{ $deal_owner_details['display_name'] }}</strong>
							</a>
						</span>
					</h2>

                    <div class="dl-horizontal-new dl-horizontal">
                        <dl>
                            @if($is_pre_start)
                                <dt> {{ Lang::get('deals::deals.starts_on_lbl') }}</dt>
                            @else
                                <dt> {{ Lang::get('deals::deals.started_on_lbl') }}</dt>
                            @endif
                            <dd><span>{{ CUtil::FMTDate($deal_details->date_deal_from, 'Y-m-d', '') }}</span></dd>
                        </dl>
                        @if(isset($expiry_details) && COUNT($expiry_details) > 0)
                            <dl>
                                <dt>{{ $expiry_details['label'] }}</dt>
                                <dd><span>{{ CUtil::FMTDate($deal_details->date_deal_to, 'Y-m-d', '') }}</span></dd>
                            </dl>
                        @endif
					</div>
					<div class="viewdeal-para wid-">
						<p id="shop_desc_more" class="margin-0">{{ nl2br($deal_details->deal_short_description) }}</p>
					</div>
				</div>
				<div class="col-md-4 text-center margin-top-20">
					<p><em>{{ Lang::get('deals::deals.meta_manage_discount') }}</em></p>
                    <?php $deal_discount_percentage = $deal_serviceobj->formatDiscountPercentage($deal_details->discount_percentage); ?>
                    <div class="deals-discount" title="{{ $deal_discount_percentage }}%">{{ $deal_discount_percentage }}%</div>
				</div>
			</div>

			@if(isset($shopDetails) && COUNT($shopDetails) > 0)
				<ul class="list-unstyled listview-deals clearfix margin-bottom-20">
					@if(isset($expiry_details['expired']) && $expiry_details['expired'] == 0)
						<li><em>{{ $expiry_details['label'] }}:</em> <strong class="text-muted">{{ CUtil::FMTDate($deal_details->date_deal_to, 'Y-m-d', '') }}</strong></li>
					@endif

					@if(isset($shopDetails) && COUNT($shopDetails) > 0)
						<li>
							{{ Lang::get('deals::deals.shop_head') }}:
							<a href="{{ $shop_url}}" title="{{{ $shopDetails->shop_name  }}}">{{{ $shopDetails->shop_name  }}}</a>
						</li>
					@endif

					@if($d_arr['purchase_count'] > 0)
						<li>
							{{ Lang::get('deals::deals.deal_bought') }}: <strong title="{{ $d_arr['purchase_count'] ."&nbsp;". Lang::get('deals::deals.deal_bought') }}">
							{{ $d_arr['purchase_count'] }}</strong>
						</li>
					@endif
				</ul>
			@endif

			@if($deal_details->tipping_qty_for_deal > 0)
				<div class="well clearfix">
					<span class="pull-right responsive-pull-none">{{ Lang::get('deals::deals.tipping_lbl') }}:
                        @if($deal_details->deal_tipping_status == '')
                            <span class="label bg-red-flamingo">{{ Lang::get('deals::deals.notstarted_tipping_lbl') }}</span>
                        @elseif($deal_details->deal_tipping_status == 'pending_tipping')
                            <span class="label label-warning">{{ Lang::get('deals::deals.pending_tipping_lbl') }}</span>
                        @elseif($deal_details->deal_tipping_status == 'tipping_reached')
                            <span class="label label-success">{{ Lang::get('deals::deals.tipping_reached_lbl') }}</span>
                        @elseif($deal_details->deal_tipping_status == 'tipping_failed')
                            <span class="label label-danger">{{ Lang::get('deals::deals.tipping_failed_lbl') }}</span>
                        @endif
					</span>
					<p class="title-three clearfix margin-top-5">{{ Lang::get('deals::deals.tipping_qty') }}: <span class="text-danger">{{ $deal_details->tipping_qty_for_deal }}</span></p>
                    <p class="note note-info margin-0">{{ nl2br(Lang::get('deals::deals.deal_tipping_info')) }}</p>
				</div>
			@endif

			<!-- BEGIN: SHOP DETAILS -->
			<div class="well clearfix">
				<h2 class="title-three">{{ Lang::get('deals::deals.shop_details_lbl') }}</h2>
				<span class="pull-left margin-right-10">
					<a class="imgsize-32X32">
						@if($shop_img['image_url'] != "")
							<img src="{{$shop_img['image_url']}}" {{$shop_img['image_attr']}} title="{{{ $shopDetails['shop_name'] }}}" alt="{{{ $shopDetails['shop_name'] }}}" />
						@else
							<img src="{{ URL::asset('/images/no_image/shopnoimage-700x90.jpg') }}" alt="no-image" />
						@endif
					</a>
				</span>
				<span class="margin-top-10 pull-left">
					@if(isset($shopDetails) && COUNT($shopDetails) > 0)
						<a href="{{ $shop_url}}" title="{{{ $shopDetails->shop_name  }}}">{{{ $shopDetails->shop_name  }}}</a>
						<span class="text-muted pad7">|</span>
					@endif
					{{ Lang::get('deals::deals.owner_lbl') }}:
					<a href="{{ $deal_owner_details['profile_url'] }}" title="{{ $deal_owner_details['display_name'] }}">{{ $deal_owner_details['display_name'] }}</a>
				</span>
			</div>
			<!-- END: SHOP DETAILS -->

			<!-- BEGIN: REALTED DEALS LISTS -->
			@if(count($d_arr['related_deals']) > 0)
				<div class="well">
					<h2 class="title-three">{{ Lang::get('deals::deals.more_deal_in_shop_head_lbl') }}</h2>
					<ul class="list-unstyled clearfix viewpg-item">
						@foreach($d_arr['related_deals'] as $dealKey => $deal)
							<?php
								$d_img_arr = array();
								$d_img_arr['deal_id'] = $deal->deal_id;
								$d_img_arr['deal_title'] = $deal->deal_title;
								$d_img_arr['img_name'] = $deal->img_name;
								$d_img_arr['img_ext'] = $deal->img_ext;
								$d_img_arr['img_width'] = $deal->img_width;
								$d_img_arr['img_height'] = $deal->img_height;
								$d_img_arr['l_width'] = $deal->l_width;
								$d_img_arr['l_height'] = $deal->l_height;
								$d_img_arr['t_width'] = $deal->t_width;
								$d_img_arr['t_height'] = $deal->t_height;
								$rel_thumb_img = $deal_serviceobj->getDealDefaultThumbImage($deal->deal_id, 'thumb', $d_img_arr);
								$rel_view_url = $deal_serviceobj->getDealViewUrl($deal);
								$disc = $deal_serviceobj->formatDiscountPercentage($deal->discount_percentage);
							?>
							<li>
								<div class="bs-prodet">
									<div class="deals-discount" title="{{ $disc ."%"}}">{{ $disc ."%"}}</div>
									<p class="prodet-img"><a href="{{ $rel_view_url }}"><img id="item_thumb_image_id" src="{{$rel_thumb_img['image_url']}}" {{$rel_thumb_img['image_attr']}} title="{{{ $rel_thumb_img['title']  }}}" alt="{{{ $rel_thumb_img['title']  }}}" /></a></p>
								</div>
							</li>
						@endforeach
					</ul>
				</div>
			@endif
			<!-- END: REALTED DEALS LISTS -->
		</div>
	</div>
@stop

@section('script_content')
	<!--<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-549260c2199730e3" async="async"></script>-->

	<script type="text/javascript">
		var addthis_config = {
			 username: "agriyadev",
			 data_track_clickback:true
			 //services_exclude: 'facebook, twitter'
			 //services_exclude: 'twitter'
		}
		var addthis_share =
		{
			url: '{{ $view_url }}',
			//title: encodeURIComponent('{{ ($deal_details->deal_title) }}'),
			title: $('#fbsharetitle').html(),
		    description: $('#fbsharedescription').html(),
			screenshot: '{{$deal_thumb_share_img["image_url"]}}',
			templates : {
		                    twitter : "{{ ($deal_details->deal_title) }} {{ $view_url }} (via @ {{Config::get('generalConfig.site_name')}} )"
		                }
		}
	</script>
@stop