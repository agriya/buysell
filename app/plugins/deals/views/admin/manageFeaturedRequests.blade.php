@extends('admin')
@section('content')
	<!-- BEGIN: INCLUDE NOTIFICATIONS -->
    @include('notifications')
    <!-- END: INCLUDE NOTIFICATIONS -->

    <!--- BEGIN: INFO BLOCK --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-info">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: INFO BLOCK --->
	
	<!-- BEGIN: PAGE TITLE -->	
    <h1 class="page-title">{{ Lang::get('deals::deals.manage_featured_deals_head') }}</h1>
    <!-- END: PAGE TITLE -->
	
    <div class="tabbable-custom tabbable-customnew">
		<div class="mobilemenu mobmenu-only">
			<!-- BEGIN: MOBILE TOGGLER -->
			<button class="btn btn-primary btn-sm mobilemenu-bar mb10"><i class="fa fa-chevron-down"></i> Menu</button>
			<!-- END: MOBILE TOGGLER -->
			<!--<span>{{ Lang::get('deals::deals.show_lbl') }}:</span>  -->  
			<ul class="nav nav-tabs mbldropdown-menu ac-custom-tabs">
				<li {{ ((Request::is('admin/deals/manage-featured-requests/all')) ? 'class="active"' : '') }}>
					<a role="presentation" href="{{ Url::to('admin/deals/manage-featured-requests', array('request_type' => 'all')) }}">
						{{ Lang::get('deals::deals.all_lbl') }}
					</a>
				</li>
				<li {{ ((Request::is('admin/deals/manage-featured-requests/pending')) ? 'class="active"' : '') }}>
					<a href="{{ Url::to('admin/deals/manage-featured-requests', array('request_type' => 'pending')) }}">
						{{ Lang::get('deals::deals.pending_for_approval_lbl') }}
					</a>
				</li>
				<li {{ ((Request::is('admin/deals/manage-featured-requests/approved')) ? 'class="active"' : '') }}>
					<a href="{{ Url::to('admin/deals/manage-featured-requests', array('request_type' => 'approved')) }}">
						{{ Lang::get('deals::deals.approved_lbl') }}
					</a>
				</li>
				<li {{ ((Request::is('admin/deals/manage-featured-requests/unapproved')) ? 'class="active"' : '') }}>
					<a href="{{ Url::to('admin/deals/manage-featured-requests', array('request_type' => 'unapproved')) }}">
						{{ Lang::get('deals::deals.unapproved_lbl') }}
					</a>
				</li>
			</ul>
		</div>  
		
		<div class="portlet box blue-hoki">
			<!--- BEGIN: TABLE TITLE --->
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-file new-icon"><sup class="fa fa-tag"></sup></i> {{ Lang::get('deals::deals.manage_featured_deals_head') }}
				</div>
			</div>
			<!--- END: TABLE TITLE --->
			
			<div class="portlet-body">
				@if(count($deal_list) > 0 )
					<!--- BEGIN: MANGE REQUEST DEAL --->
					{{ Form::open(array('id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
						<div class="table-scrollable">
							<table class="table table-hover table-bordered">
								<thead>
									<tr>                                   
										<th>{{ Lang::get('deals::deals.deal_id_head_lbl') }}</th>
										<th>{{ Lang::get('deals::deals.search_deal_title') }}</th>
										<th>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}</th>
                                        <th>{{ Lang::get('deals::deals.request_details_head_lbl') }}</th>
										<th>{{ Lang::get('deals::deals.deal_status') }}</th>
										<th>{{ Lang::get('deals::deals.request_status_head') }}</th>
										<th width="80">{{ Lang::get('deals::deals.actions') }}</th>
									</tr>
								</thead>
	
								<tbody>
									@foreach($deal_list as $deal)
										<?php
											$user_profile_url = CUtil::userProfileUrl($deal->user_id);
											$expiry_details = $deal_serviceobj->dealExpiryDetails($deal->date_deal_from, $deal->date_deal_to);                                      
											$view_url = $deal_serviceobj->getDealViewUrl($deal);		
											$status_lbl = (Lang::has('deals::deals.DEAL_STATUS_'.strtoupper($deal->deal_status))) ? Lang::get('deals::deals.DEAL_STATUS_'.strtoupper($deal->deal_status)): str_replace('_', ' ', $deal->deal_status);
											$req_status = strtolower($deal->request_status);
											$req_status_lbl = Lang::has('deals::deals.manage_deal_featured_req_'.$req_status) ? Lang::get('deals::deals.manage_deal_featured_req_'.$req_status) : str_replace("_", " ", ucwords($req_status));	
											$deal_owner_details = CUtil::getUserDetails($deal->user_id);	
										?>
										<tr>
											<td>{{ $deal->deal_id }}</td> 
                                                                                  
											<td>
												<div class="wid-220">
													<p><a target="_blank" href="{{$view_url}}" class="" title="{{ Lang::get('deals::deals.view') }}">{{$deal->deal_title }}</a></p>
													<p>
														<span class="text-muted">{{ Lang::get('deals::deals.by_lbl') }}:</span>
														<a href="{{ URL::to('admin/users/user-details').'/'.$deal->user_id }}" title="{{ $deal_owner_details['display_name'] }}" class="ml5">
															{{ $deal_owner_details['display_name'] }}
														</a>
													</p>
													<p><span class="text-muted">{{ Lang::get('deals::deals.search_slug_url') }}:</span> <span class="ml5">{{ $deal->url_slug }}</span></p>
												</div>
											</td>
                                            
											<td>
												<div class="dl-horizontal-new dl-horizontal wid-220">
													<dl>
														<dt>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}</dt>
														<dd><span><strong>{{ $deal->discount_percentage }}</strong>%</span></dd>
													</dl>
													<dl>
														<dt>{{ Lang::get('deals::deals.deal_from_lbl') }}</dt>
														<dd><span>{{ CUtil::FMTDate($deal->date_deal_from, 'Y-m-d', '') }}</span></dd>
													</dl>
													<dl>
														<dt>{{ Lang::get('deals::deals.deal_to_lbl') }}</dt>
														<dd><span>{{ CUtil::FMTDate($deal->date_deal_to, 'Y-m-d', '') }}</span></dd>
													</dl>                                                    
												</div>
											</td>
                                            
                                            <td>
                                            	<div class="dl-horizontal-new dl-horizontal wid-220">
                                            		<dl>
                                                        <dt>{{ Lang::get('deals::deals.deal_featured_from_lbl') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($deal->date_featured_from, 'Y-m-d', '') }}</span></dd>
                                                    </dl>
                                                    
                                                    <dl>
                                                        <dt>{{ Lang::get('deals::deals.deal_featured_to_lbl') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($deal->date_featured_to, 'Y-m-d', '') }}</span></dd>
                                                    </dl>
                                                    
                                                    <dl>
                                                        <dt>{{ Lang::get('deals::deals.deal_featured_requested_days_lbl') }}</dt>
                                                        <dd><span>{{ $deal->deal_featured_days }}</span></dd>
                                                    </dl>
												</div>                                                    
                                            </td>                                           
                                            
											<td>
												@if($deal->deal_status == "to_activate")
													<label class="label label-warning">{{ $status_lbl }}</label>
												@elseif($deal->deal_status == "active")
													<label class="label label-success">{{ $status_lbl }}</label>
												@elseif($deal->deal_status == "deactivated")
													<label class="label bg-red-pink">{{ $status_lbl }}</label>
												@elseif($deal->deal_status == "expired")
													<label class="label bg-red">{{ $status_lbl }}</label>
												@elseif($deal->deal_status == "closed")
													<label class="label label-default">{{ $status_lbl }}</label>
												@endif
											</td>
                                            
											<td>
												@if($deal->request_status == "pending_for_approval")
													<label class="label label-warning">{{ $req_status_lbl }}</label>
												@elseif($deal->request_status == "approved")
													<label class="label label-success">{{ $req_status_lbl }}</label>
												@elseif($deal->request_status == "un_approved")
													<label class="label label-danger">{{ $req_status_lbl }}</label>
												@endif
											</td>
                                            
											<td class="status-btn">
												<a href="{{ URL::to('admin/deals/view-featured-request', array('deal_id' => $deal->deal_id)) }}" class="btn btn-xs btn-info fn_pop" title="{{ Lang::get('deals::deals.view_request_lbl') }}"><i class="fa fa-eye"></i></a>
												@if($deal->request_status == 'pending_for_approval' && $deal->deal_status == "active")
													<a href="{{ URL::to('admin/deals/approve-featured-request', array('deal_id' => $deal->deal_id)) }}" class="btn blue btn-xs fn_pop" title="{{ Lang::get('deals::deals.set_featured') }}"><i class="fa fa-edit"></i></a>
												@endif
											</td>                                        
										</tr>
									@endforeach                                
								</tbody>
							 </table>
						</div>
					 {{Form::close()}}
					 <!--- END: MANGE REQUEST DEAL --->
	
					<!--- BEGIN: PAGINATION --->
					<div class="text-right">
						{{ $deal_list->links() }}
					</div>
					<!--- END: PAGINATION --->
				@else
					<div class="alert alert-info mar0">{{ Lang::get('deals::deals.no_deal_featured_request_lbl') }}</div>
				@endif
				<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">    
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
	        closeEffect : 'none',
            afterClose : function() {
                location.reload();
                return;
            }
	    });  
	</script>
@stop      
    