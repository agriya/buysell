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
				<h1>{{ Lang::get('deals::deals.my_featured_request_lbl') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->
			
			<!-- BEGIN: DEALS SUBMENU -->
			@include('deals::deal_submenu')
			<!-- END: DEALS SUBMENU -->

			<!-- BEGIN: INCLUDE NOTIFICATIONS -->
			@include('notifications')
			<!-- END: INCLUDE NOTIFICATIONS -->
            
             <!-- BEGIN: MY DEAL LIST -->
             <div class="well">
            	@if(count($deals) > 0)
                    <div id="fn_lists">
						{{ Form::model($deals, array('method'=>'post', 'class' => 'form-horizontal pos-relative member-formbg mb40', 'role' => 'form', 'id'=>'my_deals_frm')) }}
                        	<div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th width="80">{{ Lang::get('deals::deals.deals') }}</th>
                                            <th>{{ Lang::get('deals::deals.deal_title') }}</th>
                                            <th>{{ Lang::get('deals::deals.request_details_head_lbl') }}</th>
                                            <th>{{ Lang::get('deals::deals.request_status_head') }}</th>
                                            <th>{{ Lang::get('deals::deals.admin_comment_lbl') }}</th>
                                        </tr>
                                    </thead>
                                
                                    <tbody>
                                        @foreach($deals as $key => $val)
                                            <?php
                                                $view_url = $deal_serviceobj->getDealViewUrl($val);											
                                                $req_status = strtolower($val->request_status);
                                                $req_status_lbl = Lang::has('deals::deals.manage_deal_featured_req_'.$req_status) ? Lang::get('deals::deals.manage_deal_featured_req_'.$req_status) : str_replace("_", " ", ucwords($req_status));										
                                                $comment = (trim($val->admin_comment) != '') ? trim(str_replace('#@@#', '<br>', $val->admin_comment)) : "N/A";
                                            ?>
                                            <tr>
                                                <td><a href="{{ $view_url }}">{{ $val->deal_id }}</a></td>
                                                <td>{{ $val->deal_title }}</td>
                                                <td>
                                                    <dl class="dl-horizontal dl-featuredreq">
                                                        <dt>{{ Lang::get('deals::deals.from_date_lbl') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($val->date_featured_from, 'Y-m-d', '') }}</span></dd>
                                                        
                                                        <dt>{{ Lang::get('deals::deals.to_date_lbl') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($val->date_featured_to, 'Y-m-d', '') }}</span></dd>
                                                        
                                                        <dt>{{ Lang::get('deals::deals.number_of_days_to_featured_lbl') }}</dt>
                                                        <dd><span>{{ $val->deal_featured_days }}</span></dd>
                                                        
                                                        <dt>{{ Lang::get('deals::deals.date_requested_lbl') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($val->date_added, 'Y-m-d H:i:s', '') }}</span></dd>
                                                    </dl>
                                                </td>
                                                <td>
                                                    @if($val->request_status == "approved")
                                                        <label class="label label-success">{{ $req_status_lbl }}</label>
                                                    @elseif($val->request_status == "pending_for_approval")
                                                        <label class="label label-warning">{{ $req_status_lbl }}</label>
                                                    @elseif($val->request_status == "un_approved")
                                                        <label class="label label-danger">{{ $req_status_lbl }}</label>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($val->request_status != "pending_for_approval")
                                                        <div class="wid-400">{{ nl2br($comment) }}</div>
                                                    @else
                                                        -
                                                    @endif    
                                                </td>  
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
						{{ Form::close() }}
					</div>
					<div class="text-right">{{ Form::hidden('page', Input::get('page'), array('id' => 'page')) }}</div>
                @else
                	<div class="note note-info margin-0">{{ Lang::get('deals::deals.no_deal_featured_request_lbl')}}</div>
                @endif	
                @if(count($deals) > 0)
                    <div class="text-right">{{ $deals->links() }}</div>
                @endif
	            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
			</div>
            <!-- END: MY DEAL LIST -->            
		</div>
	</div>
@stop