@extends('popup')
@section('content')
	<!-- BEGIN: PAGE TITLE -->
	<h1>{{ Lang::get('deals::deals.request_to_set_featured_head_lbl') }}</h1>
    <!-- BEGIN: PAGE TITLE -->
	
	<div class="pop-content">
		@if(isset($error_message) && $error_message != "")
        	<!-- BEGIN: INFO BLOCK -->
			<div class="note note-danger">{{ $error_message }}</div>
			<a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
				<button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle margin-right-5"></i> Close</button>
			</a>
            <!-- END: INFO BLOCK -->
		@else
			<!-- BEGIN: VIEW FEATURED REQUEST DEAL -->
			@if(count($deal_details) > 0)
				<?php
					$view_url = $deal_serviceobj->getDealViewUrl($deal_details);					
					$admin_comment = str_replace('#@@#', '<br>', $deal_details->admin_comment);
					$admin_comment = (trim($admin_comment) != '') ? trim($admin_comment) : "N/A";
					$req_status = strtolower($deal_details->request_status);
					$req_status_lbl = Lang::has('deals::deals.manage_deal_featured_req_'.$req_status) ? Lang::get('deals::deals.manage_deal_featured_req_'.$req_status) : str_replace("_", " ", ucwords($req_status));
					$approval_Details = array();
					if($deal_details->request_status == 'approved')
					{
						$approval_Details = $deal_serviceobj->fetchFeaturedApprovalDetailsByID($deal_details->request_id);
					}
					
				?>
				<div class="dl-horizontal-new dl-horizontal dl-deals">
					<dl>
						<dt>{{ Lang::get('deals::deals.deal_id_head_lbl') }}</dt>
						<dd>
							<span>{{ $deal_details->deal_id }}</span>
						</dd>
					</dl>
                    
                    <dl>
						<dt>{{ Lang::get('deals::deals.deal_title_head') }}</dt>
						<dd>
							<span>
								<a target="_blank" href="{{$view_url}}" title="{{ $deal_details->deal_title }}">{{ $deal_details->deal_title }}</a>
							</span>
						</dd>
					</dl>
					
					<dl>
						<dt>{{ Lang::get('deals::deals.deal_featured_from_lbl') }}</dt>
						<dd><span>{{ CUtil::FMTDate($deal_details->date_featured_from, 'Y-m-d', '') }}</span></dd>
					</dl>
					
					<dl>
						<dt>{{ Lang::get('deals::deals.deal_featured_to_lbl') }}</dt>
						<dd><span>{{ CUtil::FMTDate($deal_details->date_featured_to, 'Y-m-d', '') }}</span></dd>
					</dl>
					
					<dl>
						<dt>{{ Lang::get('deals::deals.deal_featured_requested_days_lbl') }}</dt>
						<dd><span>{{ $deal_details->deal_featured_days }}</span></dd>
					</dl>
					
					@if(trim($admin_comment) != "")
						<dl>
							<dt>{{ Lang::get('deals::deals.admin_comment_lbl') }}</dt>
							<dd><span>{{ nl2br($admin_comment) }}</span></dd>
						</dl>
					@endif
					
					<dl>
						<dt>{{ Lang::get('deals::deals.request_lbl') }}</dt>
						<dd>
							@if($deal_details->request_status == "approved")
								<span class="text-success"><strong>{{ $req_status_lbl }}</strong></span>
							@elseif($deal_details->request_status == "pending_for_approval")
								<span class="text-warning"><strong>{{ $req_status_lbl }}</strong></span>
							@elseif($deal_details->request_status == "un_approved")
								<span class="text-danger"><strong>{{ $req_status_lbl }}</strong></span>
							@endif
						</dd>
					</dl>
                    @if($deal_details->request_status == "approved" && isset($approval_Details) && COUNT($approval_Details) > 0)
                    	<div class="form"><h2 class="form-section">Approved Details</h2></div>
                    	<dl>
                        	<dt>{{ Lang::get('deals::deals.date_featured_from_head_lbl') }}</dt>
                            <dd><span>{{ CUtil::FMTDate($approval_Details->date_featured_from, 'Y-m-d', '') }}</span></dd>
                        </dl>
                        <dl>
                        	<dt>{{ Lang::get('deals::deals.date_featured_to_head_lbl') }}</dt>
                            <dd><span>{{ CUtil::FMTDate($approval_Details->date_featured_to, 'Y-m-d', '') }}</span></dd>
                        </dl>
                        <?php
							$from	= date('d-m-Y', strtotime($approval_Details->date_featured_from));
							$to		= date('d-m-Y', strtotime($approval_Details->date_featured_to));
							$approved_featured_days =((strtotime($to) - strtotime($from))/ (60 * 60 * 24)) + 1; //it will count no. of days
						?>
                        
                        <dl>
                        	<dt>{{ Lang::get('deals::deals.deal_featured_days_lbl') }}</dt>
                            <dd><span>{{ $approved_featured_days }}</span></dd>
                        </dl>
                    @endif
                    
				</div>
				
				<div class="col-sm-offset-3 margin-top-20">	
					<a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();" class="btn red">
						<i class="fa fa-times-circle"></i> Close
					</a>
				</div>
			@else
				<p class="alert alert-info">{{ Lang::get('deals::deals.no_deal_featured_request_lbl') }}</p>
			@endif
			<!-- END: VIEW FEATURED REQUEST DEAL -->
		@endif            
	</div>
@stop    