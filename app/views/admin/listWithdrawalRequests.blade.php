@extends('admin')
@section('content')
  	<!-- BEGIN: ALERT BLOCK -->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
    @endif
    <!-- END: ALERT BLOCK -->

	<!-- BEGIN: SEARCH BLOCK -->
	{{ Form::open(array('id'=>'withdrawalSearchfrm', 'method'=>'get','class' => 'form-horizontal search-form' )) }}
		<div class="portlet box blue-madison">
			<!--- BEGIN: SEARCH TITLE --->
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-search"></i> {{ trans('admin/manageWithdrawals.withdrawallist_search_requests') }}</div>
				<div class="tools"><a class="collapse" href="javascript:;"></a></div>
			</div>
			<!--- END: SEARCH TITLE --->
			<div class="portlet-body form">
				<div class="form-body" id="search_holder">
					<div class="form-group">
						{{ Form::label('request_status', trans('admin/manageWithdrawals.withdrawallist_status'), array('class' => 'control-label col-md-3')) }}
						<div class="col-md-4">
							{{ Form::select('request_status', $d_arr['request_status_arr'], Input::get("request_status"), array('class' => 'bs-select form-control')) }}
						</div>
					</div>
					<div class="form-group">
						{{ Form::label('request_id', trans('admin/manageWithdrawals.withdrawallist_request_id'), array('class' => 'control-label col-md-3')) }}
						<div class="col-md-4">
							{{ Form::text('request_id', Input::get("request_id"), array("placeholder"=> trans('admin/manageWithdrawals.code_id'), 'class' => 'form-control' )) }}
						</div>
					</div>
				</div>
				<!-- BEGIN: SEARCH ACTIONS STARTS -->
				<div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-8">
						<button type="submit" name="search_request" value="search_request" class="btn purple-plum">
							{{ trans("common.search") }} <i class="fa fa-search bigger-110"></i>
						</button>
						<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ url('admin/withdrawals/index') }}'">
							<i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}
						</button>
					</div>
				</div>
				<!-- END: SEARCH ACTIONS -->
			</div>
		</div>
	{{ Form::close() }}
	<!-- BEGIN: SEARCH BLOCK -->

	<!-- BEGIN: WITHDRAW REQUEST LIST -->
	{{ Form::open(array('id'=>'requestListfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
		<div class="portlet box blue-hoki">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-list"></i> {{ trans('admin/manageWithdrawals.withdrawallist_request_list') }}
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-responsive responsive-xscroll">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="40">{{ trans('admin/manageWithdrawals.withdrawallist_id') }}</th>
								<th width="100">{{ trans('admin/manageWithdrawals.withdrawallist_by') }}</th>
								<th width="100">{{ trans('admin/manageWithdrawals.withdrawallist_requested_on') }}</th>
								<th width="120">{{ trans('admin/manageWithdrawals.withdrawallist_requested_amount') }}</th>
								<th width="150">{{ trans('admin/manageWithdrawals.withdrawallist_payment_type') }}</th>
								<th>{{ trans('admin/manageWithdrawals.withdrawallist_pay_to_details') }}</th>
								<th>{{ trans('admin/manageWithdrawals.withdrawallist_stats') }}</th>
								<th>{{ trans('admin/manageWithdrawals.withdrawallist_status') }}</th>
								<th>{{ trans('common.action') }}</th>
							</tr>
						</thead>
						<tbody>
                            @if(count($request_details) > 0)
                                @foreach($request_details as $r_key => $det)
                                    <?php
                                        /*$user_arr = array('first_name'=>$det['user_firstname'], 'last_name' => $det['user_lastname'], 'user_code' => $det['user_usercode'] );
                                        $user_name = CUtil::getUserDetails($det['requester_id'], 'display_name', $user_arr);
                                        $user_url = CUtil::getUserDetails($det['requester_id'], 'admin_profile_url', $user_arr);

                                        $admin_arr = array('first_name'=>$det['admin_firstname'], 'last_name' => $det['admin_lastname'], 'user_code' => $det['admin_usercode'] );
                                        $admin_name = CUtil::getUserDetails($det['admin_id'], 'display_name', $admin_arr);
                                        $admin_url = CUtil::getUserDetails($det['admin_id'], 'admin_profile_url', $admin_arr);*/

                                        $user_details = CUtil::getUserDetails($det['user_id']);
                                        $user_name = $user_details['display_name'];
                                        $user_url = URL::to('admin/users/user-details').'/'.$det['user_id'];

                                        $admin_details = CUtil::getUserDetails($det['set_as_paid_by']);
                                        $admin_name = $admin_details['display_name'];
                                        $admin_url = $admin_details['profile_url'];
                                    ?>
                                    <tr>
                                        <td>{{ $det['withdraw_id'] }}</td>
                                        <td>
                                            <p>
												<strong><a href="{{ $user_url }}">{{ $user_name }}</a></strong>
												(<a class="text-muted" href="{{ $user_url }}">{{ $user_details['user_code'] }}</a>)
											</p>
                                        </td>
                                        <td>{{ CUtil::FMTDate($det['added_date'], "Y-m-d H:i:s", "") }}</td>
                                        <td>
                                            <p>
												{{ CUtil::convertAmountToCurrency(($det['amount'] - $det['fee']), Config::get('generalConfig.site_default_currency'), '', true) }}
											</p>
                                            <p>
                                                @if($det['status'] == "Active")
                                                    <?php
                                                        $class = "";
                                                        if(round($det['amount'], 2) >= round($det['amount'], 2))
                                                        {
                                                            $class = "text-error";
                                                        }
                                                    ?>
                                                    <span class="{{ $class }} text-muted"> {{ trans('admin/manageWithdrawals.bal') }}:</span> {{ CUtil::convertAmountToCurrency($det['amount'], Config::get('generalConfig.site_default_currency'), '', true) }}
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            {{ $det['payment_type'] }}
                                            @if(round($det['fee']) > 0)<p><span class="text-muted">{{ trans('admin/manageWithdrawals.fee') }}:</span> {{ CUtil::convertAmountToCurrency($det['fee'], Config::get('generalConfig.site_default_currency'), '', true) }} </p> @endif
                                        </td>
                                        <td>{{ nl2br($det['pay_to_user_account']) }}</td>
                                        <td>
                                            <div class="wid-280">
												@if($det['status'] == "Active")
													--
												@elseif($det['status'] == "Paid")
													<p><strong>{{ trans('admin/manageWithdrawals.withdrawallist_paid_notes') }}:</strong></p>
													<div class="mb10">{{ nl2br($det['paid_notes']) }}</div>
													<p><strong>{{ trans('admin/manageWithdrawals.withdrawallist_admin_notes') }}:</strong></p>
													<div class="mb10">{{ nl2br($det['admin_notes']) }}</div>
												@elseif($det['status'] == "Cancelled")
													@if($det['admin_notes'] != "")
														<p><strong>{{ trans('admin/manageWithdrawals.withdrawallist_admin_notes') }}:</strong></p>
														<div class="mb10">{{ nl2br($det['admin_notes']) }}</div>
													@endif
													@if($det['cancelled_reason'] != "")
														<p><strong>{{ trans('admin/manageWithdrawals.withdrawallist_cancelled_reason') }}:</strong></p>
														<div class="mb10">{{ nl2br($det['cancelled_reason']) }}</div>
													@endif
												@endif
											</div>
                                        </td>
                                        <td>
                                            <?php
                                                $lbl_class = "";
                                                $status = "";
                                                if(strtolower ($det['status']) == "active") {
                                                    $lbl_class = "label-success";
                                                    $status = trans('common.active');
                                                } elseif(strtolower ($det['status']) == "paid") {
                                                    $lbl_class = "label-primary";
                                                    $status = trans('common.paid');
                                                } elseif(strtolower ($det['status']) == "cancelled") {
                                                    $lbl_class = "label-danger";
                                                    $status = trans('common.cancelled');
                                                }
                                            ?>
                                            <p><span class="label {{ $lbl_class }}">{{ $status }}</span></p>

                                            @if($det['status'] == "Cancelled")
                                                @if($det['cancelled_by'] == $det['user_id'])
                                                    <p><span class="text-muted">{{ trans('common.by') }}:</span> {{ trans('admin/manageWithdrawals.user') }}</p>
                                                @else
                                                    <?php  $by_user_arr = CUtil::getUserDetails($det['cancelled_by']); ?>
                                                      <p><span class="text-muted">{{ trans('common.by') }}:</span> {{ $by_user_arr['display_name'] }}</p>
                                                      <p><span class="text-muted">{{ trans('common.on') }}:</span> {{ CUtil::FMTDate($det['date_cancelled'], "Y-m-d H:i:s", "") }}</p>
                                                @endif
                                            @elseif($det['status'] == "Paid" AND $det['admin_id'] != 0 )
                                                <p><span class="text-muted">{{ trans('common.by') }}:</span> {{ $admin_name }}</p>
                                                <p><span class="text-muted">{{ trans('common.on') }}:</span> {{ CUtil::FMTDate($det['date_paid'], "Y-m-d H:i:s", "") }}</p>
                                            @endif
                                        </td>
                                        <td class="status-btn">
                                            @if($det['status'] == "Active" && $d_arr['allow_update_request'])
                                                <a href="{{ URL::to('admin/withdrawals/update-request', array('request_id' => $det['withdraw_id'], 'request_action' => 'set_as_paid' ))}}" title="{{ trans('admin/manageWithdrawals.withdrawallist_set_paid') }}" class="fn_changeStatusPop btn green btn-xs"><i class="fa fa-check-circle"></i></a>
                                                <a href="{{ URL::to('admin/withdrawals/update-request', array('request_id' => $det['withdraw_id'], 'request_action' => 'decline' ))}}" title="{{ trans('admin/manageWithdrawals.withdrawallist_decline') }}" class="fn_changeStatusPop btn red btn-xs"><i class="fa fa-times"></i></a>
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                           @else
                                <tr><td colspan="9"><p class="alert alert-info">{{ trans('admin/manageWithdrawals.withdrawallist_none_err_msg') }}</p></td></tr>
                           @endif
						</tbody>
					</table>
				</div>
				@if(count($request_details) > 0)
					<div class="text-right">
						{{ $request_details->appends(array('request_by' => Input::get('request_by'), 'date_added_from' => Input::get('date_added_from'), 'date_added_to' => Input::get('date_added_to'), 'request_id' => Input::get('request_id'), 'request_status' => Input::get('request_status'), 'search_request' => Input::get('search_request')))->links() }}
					</div>
				@endif
			</div>
		</div>
	{{ Form::close() }}
	<!-- END: WITHDRAW REQUEST LIST -->
@stop

@section('script_content')
	<script type="text/javascript">
		setAddMessageFancyBox();

		$(".fn_changeStatusPop").click(function(){
			var linkurl = $(this).attr("href")+"?popup=true";
			$(".fn_changeStatusPop").fancybox({
				href 		: linkurl,
				maxWidth    : 800,
				maxHeight   : 430,
				fitToView   : false,
				width       : '70%',
				height      : '430',
				autoSize    : false,
				closeClick  : false,
				type        : 'iframe',
				openEffect  : 'none',
				closeEffect : 'none'
			});
		});

		function setAddMessageFancyBox(){
			$(window).load(function(){
				 $(".fn_add_message").fancybox({
						maxWidth    : 800,
						maxHeight   : 600,
						fitToView   : false,
						width       : '70%',
						height      : '430',
						autoSize    : false,
						closeClick  : false,
						type        : 'iframe',
						openEffect  : 'none',
						closeEffect : 'none'
					});
			});
		}
	</script>
@stop