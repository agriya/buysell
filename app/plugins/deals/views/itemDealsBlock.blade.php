<!-- BEGIN: ITEM DEALS BLOCK -->
@if(isset($deal_details['deal_available']) && COUNT($deal_details) > 0 && $deal_details['deal_available'])
	<?php
		$deal_purchase_count = 0;
		if(isset($deal_details['deal_available']) && COUNT($deal_details) > 0 && $deal_details['deal_available'])
		{
			$deal_purchase_count = $product_this_obj->deal_service->getDealPurchasedCountById($deal_details['deal_id']);
		}	
	?>
	<div class="deals-itemblk">
		<h2 class="title-one" title="{{{ $deal_details['deal_title'] }}}">{{ Lang::get('deals::deals.deal_applicalble_this_item_lbl') }}:</h2>
		<div class="row">
			<div class="col-md-8 col-sm-8">
				<div class="media">
					<a href="{{  $deal_details['view_deal_url'] }}" class="imgsize-75X75 pull-left"><img src="{{$deal_details['deal_image_details']['image_url']}}" {{$deal_details['deal_image_details']['image_attr']}} title="{{{ $deal_details['deal_title']  }}}" alt="{{{ $deal_details['deal_title']  }}}"  /></a>
					<div class="media-body margin-bottom-10 wid-">
						<h3 class="title-three">{{ $deal_details['deal_title'] }}</h3>
						<p id="shop_desc_more">{{ $deal_details['short_desc'] }}</p>                        
					</div>
				</div>
			</div>
			
			<div class="col-md-4 col-sm-4 text-center">
				<p><em>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}</em></p>
				<div class="deals-discount" title="{{ $deal_details['discount_percentage'] }}%">{{ $deal_details['discount_percentage'] }}%</div>
				<a href="{{  $deal_details['view_deal_url'] }}" title="{{ Lang::get('deals::deals.read_more_link_lbl') }}" class="fonts12">
					<i class="fa fa-angle-double-right"></i> {{ Lang::get('deals::deals.read_more_link_lbl') }}
				</a>
			</div>
		</div>
        @if($deal_details['tipping_qty_for_deal'] > 0)
            <div class="clearfix margin-top-10">
                <span class="pull-right responsive-pull-none">{{ Lang::get('deals::deals.tipping_lbl') }}:
                    @if($deal_details['deal_tipping_status'] == '')
                        <span class="label bg-red-flamingo">{{ Lang::get('deals::deals.notstarted_tipping_lbl') }}</span>
                    @elseif($deal_details['deal_tipping_status'] == 'pending_tipping')
                        <span class="label label-warning">{{ Lang::get('deals::deals.pending_tipping_lbl') }}</span>
                    @elseif($deal_details['deal_tipping_status'] == 'tipping_reached')
                        <span class="label label-success">{{ Lang::get('deals::deals.tipping_reached_lbl') }}</span>
                    @elseif($deal_details['deal_tipping_status'] == 'tipping_failed')
                        <span class="label label-danger">{{ Lang::get('deals::deals.tipping_failed_lbl') }}</span>
                    @endif
                </span>
				@if(isset($deal_purchase_count))
                    <p class="title-three clearfix ">{{ Lang::get('deals::deals.deal_bought') }}: <span>{{ $deal_purchase_count }}</span></p>
                @endif 
                <p class="title-three clearfix margin-top-5">{{ Lang::get('deals::deals.tipping_qty') }}: <span class="text-danger">{{ $deal_details['tipping_qty_for_deal'] }}</span></p>                               
            </div>    
            <p class="note note-info margin-0">{{ nl2br(Lang::get('deals::deals.deal_tipping_info')) }}</p>
        @endif
	</div>                  
@endif
<!-- END: ITEM DEALS BLOCK -->