@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<a href="{{ URL::to('deals/add-deal') }}" class="pull-right btn btn-xs green-meadow responsive-btn-block">
					<i class="fa fa-plus"></i> {{ Lang::get("deals::deals.add_deal_link_lbl") }}
				</a>
				<h1>{{ Lang::get('deals::deals.my_deal_list') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->
			
			<!-- BEGIN: DEALS SUBMENU -->
			@include('deals::deal_submenu')
			<!-- END: DEALS SUBMENU -->

			<!-- BEGIN: ALERT BLOCK -->
			@include('notifications')
			<!-- END: ALERT BLOCK -->
            
            <!-- BEGIN: MY DEAL LIST -->
            <div class="well">
            	@if(count($deals) > 0)
                    <div id="fn_lists">
						{{ Form::model($deals, array('url' => 'deals/my-deals', 'method'=>'post', 'class' => 'form-horizontal pos-relative member-formbg mb40', 'role' => 'form', 'id'=>'my_deals_frm')) }}
						<div  class="table-responsive responsive-xscroll">
							<table class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th>{{ Lang::get('deals::deals.deals') }}</th>
										<th class="col-md-4">{{ Lang::get('deals::deals.deal_title') }}</th>
										<th>{{ Lang::get('deals::deals.deal_discount') }}</th>
										<th>{{ Lang::get('deals::deals.deal_bought') }}</th>
										<th>{{ Lang::get('deals::deals.deal_status') }}</th>
										<th width="180">{{ Lang::get('deals::deals.actions') }}</th>
									</tr>
								</thead>
								
								<tbody>
									@foreach($deals as $key => $val)
										<?php
											$d_img_arr['deal_id'] = $val->deal_id;
											$d_img_arr['deal_title'] = $val->deal_title;
											$d_img_arr['img_name'] = $val->img_name;
											$d_img_arr['img_ext'] = $val->img_ext;
											$d_img_arr['img_width'] = $val->img_width;
											$d_img_arr['img_height'] = $val->img_height;
											$d_img_arr['l_width'] = $val->l_width;
											$d_img_arr['l_height'] = $val->l_height;
											$d_img_arr['t_width'] = $val->t_width;
											$d_img_arr['t_height'] = $val->t_height;						
											$p_thumb_img = $deal_serviceobj->getDealDefaultThumbImage($val->deal_id, 'thumb', $d_img_arr);
											$view_url = $deal_serviceobj->getDealViewUrl($val);		
											$status_lbl = (Lang::has('deals::deals.DEAL_STATUS_'.strtoupper($val->deal_status))) ? Lang::get('deals::deals.DEAL_STATUS_'.strtoupper($val->deal_status)): str_replace('_', ' ',$val->deal_status);
											$expiry_details = $deal_serviceobj->dealExpiryDetails($val->date_deal_from, $val->date_deal_to);
											$set_featured_link = URL::to('deals/set-featured/'.$val->deal_id);
											$purchase_count = (isset($purchased_count[$val->deal_id])) ? $purchased_count[$val->deal_id] : 0; 										
											$allow_edit = ($val->deal_status != 'deactivated' && $val->deal_status != 'expired' && $val->deal_status != 'closed') ? 1 : 0;										
											//$allow_feature = ($val->deal_status == 'active' && isset($expiry_details['expired']) && $expiry_details['expired'] == 0) ? 1 : 0;	
											$allow_feature = ($val->deal_status == 'active') ? 1 : 0;	
											$allow_close = ($deal_serviceobj->chkIsAllowToCloseDeal($val, $purchase_count)) ? 1 : 0;		
											$deal_start_date = strtotime($val->date_deal_from);
											$curr_date = strtotime(date('Y-m-d'));
											$is_pre_start = ($deal_start_date > $curr_date ) ? 1 : 0;								
										?>                                
										<tr>
											<td>
												<figure class="margin-bottom-5 custom-image">
													<a href="{{ $view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}"  /></a>
												</figure>
											</td>
											<td>
												<div class="wid-330">
													<p class="margin-bottom-5">{{ Lang::get('deals::deals.deal_id_label') }}: <strong>{{ $val->deal_id }}</strong></p>
													<p class="margin-bottom-5">{{ $val->deal_title }}</p>
													<p class="margin-bottom-5">{{ $val->deal_short_description }}</p>
												</div>
											</td>
											<td><span class="font-green">{{ $val->discount_percentage }}</span></td>
											<td>{{ $purchase_count }}</td>
											<td>
												@if($val->deal_status == "to_activate")
													<label class="label label-warning">{{ $status_lbl }}</label>
												@elseif($val->deal_status == "active")
													<label class="label label-success">{{ $status_lbl }}</label>
												@elseif($val->deal_status == "deactivated")
													<label class="label bg-red-pink">{{ $status_lbl }}</label>
												@elseif($val->deal_status == "expired")
													<label class="label bg-red">{{ $status_lbl }}</label>
												@elseif($val->deal_status == "closed")
													<label class="label label-default">{{ $status_lbl }}</label>
												@endif
                                                <div class="dl-horizontal-new dl-horizontal">
													<dl>                                                	
														@if($is_pre_start)
															<dt> {{ Lang::get('deals::deals.starts_on_lbl') }}</dt> 
														@else
															<dt> {{ Lang::get('deals::deals.started_on_lbl') }}</dt>
														@endif
														<dd><span>{{ CUtil::FMTDate($val->date_deal_from, 'Y-m-d', '') }}</span></dd>
													</dl>	
													@if(isset($expiry_details) && COUNT($expiry_details) > 0)
														<dl>
															<dt>{{ $expiry_details['label'] }}</dt>
															<dd><span>{{ CUtil::FMTDate($val->date_deal_to, 'Y-m-d', '') }}</span></dd>
														</dl>	
													@endif  
													<dl>
														<dt>{{ Lang::get('deals::deals.tipping_lbl') }}</dt>
														<dd>
															@if($val->tipping_qty_for_deal > 0)
																@if($val->deal_tipping_status == '' )
																	<span class="font-red">{{ Lang::get('deals::deals.notstarted_tipping_lbl') }}</span>
																@elseif($val->deal_tipping_status == 'pending_tipping')
																	<span class="text-warning">{{ Lang::get('deals::deals.pending_tipping_lbl') }}</span>
																@elseif($val->deal_tipping_status == 'tipping_reached')
																	<span class="text-success">{{ Lang::get('deals::deals.tipping_reached_lbl') }}</span>
																@elseif($val->deal_tipping_status == 'tipping_failed')
																	<span class="text-danger">{{ Lang::get('deals::deals.tipping_failed_lbl') }}</span>
																@endif
															@else
																<span class="text-muted">{{ Lang::get('common.not_applicable') }}</span>
															@endif
														</dd>
													</dl>
													@if($val->tipping_qty_for_deal > 0)
														<dl>
															<dt>{{ Lang::get('deals::deals.tipping_qty_lbl') }}</dt>
															<dd><span>{{ $val->tipping_qty_for_deal }}</span></dd>
														</dl>
													@endif
												</div>
											</td>
											<td class="action-btn">
												<a href="{{ $view_url }}" class="btn btn-xs btn-info" title="{{ Lang::get('deals::deals.view_deal_link_label') }}">
												<i class="fa fa-eye"></i></a>
												@if($allow_edit)
													<a href="{{ URL::to('deals/update-deal/'.$val->deal_id) }}" class="btn blue btn-xs" title="{{ Lang::get('deals::deals.edit') }}">
													<i class="fa fa-edit"></i></a>
												@endif
												@if($allow_feature && $deal_serviceobj->chkIsValidFeaturedRequest($val->deal_id))
													<a href="{{ $set_featured_link }}" title="{{ Lang::get('deals::deals.request_to_set_featured_lbl') }}" class="btn bg-blue-steel btn-xs">
														<i class="fa fa-tags"></i>
													</a>
												@endif
												@if($allow_close)
														<a title="{{ Lang::get('deals::deals.close_deal') }}" href="javascript:void(0)" onclick="doAction('{{ $val->deal_id }}', 'close')" class="btn btn-danger btn-xs" title="{{ Lang::get('deals::deals.close_deal') }}"><i class="fa fa-ban"></i></a>
												@endif                                                
												<a href="{{Url::to('deals/purchased-details', array('deal_id' => $val->deal_id)) }}" class="fn_pop btn btn-success btn-xs" title="{{ Lang::get('deals::deals.deal_purchased_stats_link') }}"><i class="fa fa-shopping-cart"></i>
												</a>
                                                @if($deal_serviceobj->chkIsFeaturedDeal($val->deal_id))
	                                                <p><label class="label bg-purple-plum">{{ Lang::get('deals::deals.featured_deal_head') }}</label></p>
                                                @endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						{{ Form::close() }}
					</div>
                    
                	<!--- BEGIN: ACTIONS --->
			        <ul class="list-inline margin-top-20">
						<li class="margin-bottom-5">							
							{{ Form::hidden('page', Input::get('page'), array('id' => 'page')) }}
						</li>						
			        </ul>
			        <!--- END: ACTIONS --->
                @else
                	<div class="note note-info margin-0">{{ Lang::get('deals::deals.no_my_deals_list')}}</div>
                @endif
                
	           {{ Form::open(array('id'=>'dealsActionfrm', 'method'=>'post')) }}
                    {{ Form::hidden('deal_id', '', array('id' => 'deal_id')) }}
                    {{ Form::hidden('deal_action', '', array('id' => 'deal_action')) }}
                {{ Form::close() }}
                
                <div id="dialog-confirm" class="confirm-dialog-delete" title="" style="display:none;">
                    <span class="ui-icon ui-icon-alert"></span>
                    <span id="dialog-confirm-content" class="show"></span>
                </div>
			</div>            
            <!-- END: MY DEAL LIST -->
            @if(count($deals) > 0)
                <div class="text-right">{{ $deals->links() }}</div>
            @endif
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		function doAction(deal_id, selected_action)
		{			
			if(selected_action == 'close')
			{
				$('#dialog-confirm-content').html('{{ Lang::get("deals::deals.confirm_close_member") }}');
			}
			$("#dialog-confirm").dialog({ title: '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#deal_action').val(selected_action);
						$('#deal_id').val(deal_id);
						
						document.getElementById("dealsActionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}
		
		$(".fn_pop").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 630,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	</script>
@stop