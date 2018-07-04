@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<a href="{{ URL::to('deals/add-deal') }}" class="pull-right btn btn-xs green-meadow responsive-btn-block">
					<i class="fa fa-plus"></i> {{ Lang::get("deals::deals.add_deal_link_lbl") }}
				</a>
				<h1>{{ $d_arr['page_title'] }}</h1>
			</div>
			<!-- END: PAGE TITLE -->
			
			<!-- BEGIN: DEALS SUBMENU -->
			@include('deals::deal_submenu')
			<!-- END: DEALS SUBMENU -->

			<!-- BEGIN: ALERT BLOCK -->
			@include('notifications')
			<!-- END: ALERT BLOCK -->
        	
            @if(count($deal_list) > 0)
				<!-- BEGIN: PRODUCTS LISTS -->
				<ul class="row list-unstyled deal-listblk prolist-max768">
					@foreach($deal_list as $dealKey => $deal)
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
								<p class="prodet-img">
									<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a>
								</p>
								<div class="prodet-info">
									<h3><a href="{{ $view_url }}" title="{{{ $deal->deal_title  }}}">{{{ $deal->deal_title  }}}</a></h3>
									@if(isset($shopDetails) && COUNT($shopDetails) > 0)
										<a href="{{ $shopDetails->shop_url }}" title="{{{ $shopDetails->shop_name  }}}" class="text-ellipsis">{{{ $shopDetails->shop_name  }}}</a>
									@endif
									<div class="margin-top-5 wid-">{{ Str::limit($deal->deal_short_description, 40) }}</div>
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
				<!-- END: PRODUCTS LISTS -->
            @else
                <p class="alert alert-info">{{ Lang::get('deals::deals.no_deals_list') }}</p>
            @endif
            
            @if(count($deal_list) > 0)
                <div class="text-right">{{ $deal_list->links() }}</div>
            @endif
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
	</script>
@stop    