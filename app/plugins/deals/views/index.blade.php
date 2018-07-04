@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('deals::deals.deals') }}</h1>
			</div>

			<!-- BEGIN: DEALS SUBMENU -->
            <div class="dealindex-menu">
                @include('deals::deal_submenu')
            </div>
			<!-- END: DEALS SUBMENU -->

			 @if(isset($featured_deal) && COUNT($featured_deal) > 0)
				<?php
					$d_img_arr['deal_id'] = $featured_deal->deal_id;
					$d_img_arr['deal_title'] = $featured_deal->deal_title;
					$d_img_arr['img_name'] = $featured_deal->img_name;
					$d_img_arr['img_ext'] = $featured_deal->img_ext;
					$d_img_arr['img_width'] = $featured_deal->img_width;
					$d_img_arr['img_height'] = $featured_deal->img_height;
					$d_img_arr['l_width'] = $featured_deal->l_width;
					$d_img_arr['l_height'] = $featured_deal->l_height;
					$d_img_arr['t_width'] = $featured_deal->t_width;
					$d_img_arr['t_height'] = $featured_deal->t_height;
					$p_thumb_img = $deal_serviceobj->getDealDefaultThumbImage($featured_deal->deal_id, 'thumb', $d_img_arr);
					$view_url = $deal_serviceobj->getDealViewUrl($featured_deal);
					$shopDetails =	BasicCUtil::getShopDetails($featured_deal->user_id);
					$expiry_details = $deal_serviceobj->dealExpiryDetails($featured_deal->date_deal_from, $featured_deal->date_deal_to);
					$featured_disc = $deal_serviceobj->formatDiscountPercentage($featured_deal->discount_percentage);
				?>
				<!-- BEGIN: FEATURED DEAL -->

				<h2>{{ Lang::get('deals::deals.featured_deal_head') }}</h2>
				<div class="well margin-bottom-30">
					<div class="row">
						<div class="prodet-img col-md-3 col-sm-2">
							<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a>
						</div>
						<div class="col-md-6 col-sm-5">
							<p class="fonts18">{{ $featured_deal->deal_title }}</p>
							<p class="min-hgt75">{{ $featured_deal->deal_short_description }}</p>
							<ul class="list-inline">
								<li class="margin-right-20 margin-bottom-10">
									<em>{{ $expiry_details['label'] }}: <strong class="text-muted">{{ CUtil::FMTDate($featured_deal->date_featured_to, 'Y-m-d', '') }}</strong></em>
								</li>
								@if(isset($shopDetails) && COUNT($shopDetails) > 0)
									<li>
										<em class="text-muted">{{ Lang::get('deals::deals.shop_head') }}:</em>
										<a href="{{ $shopDetails->shop_url }}" title="{{{ $shopDetails->shop_name  }}}"><strong>{{{ $shopDetails->shop_name  }}}</strong></a>
									</li>
								@endif
							</ul>
						</div>

						<div class="col-md-3 col-sm-3 text-center">
							<p><em>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}</em></p>
							<div class="deals-discount" title="{{ $featured_deal->discount_percentage ."%"}}">{{ $featured_disc ."%"}}</div>
							<a href="{{ $view_url }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}" class="btn green">
								<i class="fa fa-eye"></i> {{ Lang::get('deals::deals.view_deal_link_label') }}
							</a>
						</div>
					</div>
				</div>
				<!-- END: FEATURED DEAL -->
            @endif

			<!-- BEGIN: ALERT BLOCK -->
			@include('notifications')
			<!-- END: ALERT BLOCK -->
			@if(count($recent_deals) > 0)
                <!-- BEGIN: PAGE TITLE -->
                <h2>
                    {{ Lang::get('deals::deals.recent_deals_head') }}
                    <a href="{{ URL::to('deals/list', array('list_type' => 'new')) }}" class="fonts12" title="{{ Lang::get('deals::deals.see_more_link_tip') }}">
                    <strong>{{ Lang::get('deals::deals.see_more_link_lbl') }}</strong></a>
                </h2>
                <!-- END: PAGE TITLE -->
            @endif

			<!-- BEGIN: RECENT DEALS PRODUCT -->
			@if(count($recent_deals) > 0)
				<ul class="row list-unstyled deal-listblk prolist-max768">
					@foreach($recent_deals as $dealKey => $deal)
						<?php
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
							$p_thumb_img = $deal_serviceobj->getDealDefaultThumbImage($deal->deal_id, 'thumb', $d_img_arr);
							$view_url = $deal_serviceobj->getDealViewUrl($deal);
							$shopDetails =	BasicCUtil::getShopDetails($deal->user_id);
							$expiry_details = $deal_serviceobj->dealExpiryDetails($deal->date_deal_from, $deal->date_deal_to);
							$deal_disc = $deal_serviceobj->formatDiscountPercentage($deal->discount_percentage);
						?>
						<li class="col-md-3 col-sm-4 feat-prod">
							<div class="bs-prodet product-gridview">
								<p class="deals-discount" title="{{ $deal->discount_percentage ."%"}}">{{ $deal_disc ."%"}}</p>
								<p class="prodet-img"><a href="{{ $view_url }}"><img src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a></p>
								<div class="prodet-info">
									<h3><a href="{{ $view_url }}" title="{{{ $deal->deal_title  }}}">{{{ $deal->deal_title  }}}</a></h3>
									<div class="margin-top-5 wid-">{{ Str::limit($deal->deal_short_description , 50) }}</div>
									@if(isset($shopDetails) && COUNT($shopDetails) > 0)
										<a href="{{ $shopDetails->shop_url }}" title="{{{ $shopDetails->shop_name  }}}" class="text-ellipsis">{{{ $shopDetails->shop_name  }}}</a>
									@endif
									@if(isset($expiry_details) && COUNT($expiry_details) > 0)
										<div class="fonts12 margin-top-5">
											<span class="text-muted">{{ $expiry_details['label'] }}</span> : {{ CUtil::FMTDate($deal->date_deal_to, 'Y-m-d', '') }}
										</div>
									@endif
								</div>
							</div>
						</li>
					@endforeach
				</ul>
			@else
				<p class="alert alert-info">{{ Lang::get('deals::deals.no_deals_list') }}</p>
			@endif
			<!-- END: RECENT DEALS PRODUCT -->
			<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
		</div>
	</div>
@stop