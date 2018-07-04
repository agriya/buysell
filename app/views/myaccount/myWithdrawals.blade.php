@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<h1>{{ trans('myaccount/form.my-withdrawals.account_menu_withdrawal') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			@include('walletAccount')

			<!-- ALERT BLOCK STARTS -->
			@if (Session::has('success_message') && Session::get('success_message') != "")
				<div class="note note-success">{{	Session::get('success_message') }}</div>
			@endif
			@if (Session::has('error_message') && Session::get('error_message') != "")
				<div class="note note-danger">{{	Session::get('error_message') }}</div>
			@endif
			<!-- ALERT BLOCK END -->


			<!-- WITHDRAW LIST START -->
			@if(isset($d_arr['allow_withdrawal']) && $d_arr['allow_withdrawal'])
				<div class="well">
					<h2 class="title-one">{{ trans('myaccount/form.my-withdrawals.withdraw_request') }} <small class="text-muted">({{ trans('myaccount/form.my-withdrawals.choose_withdraw_method') }})</small></h2>
					<div class="table-responsive margin-bottom-30">
						<table class="table table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th class="col-md-2">{{ trans('myaccount/form.my-withdrawals.method_lbl') }}</th>
									<th class="col-md-6">{{ trans('myaccount/form.my-withdrawals.description_lbl') }}</th>
									<th>{{ trans('myaccount/form.my-withdrawals.fee_lbl') }}</th>
									<th>{{ trans('myaccount/form.my-withdrawals.action') }}</th>
								</tr>
							</thead>

							<tbody class="pay-method">
								@if(count($d_arr['allowed_withdrawals_arr']) > 0)
									@foreach($d_arr['allowed_withdrawals_arr'] as $wd)
										@if($wd['method'] != 'NEFT')
											<tr>
												<td><a href="{{ URL::to('users/my-withdrawals/withdrawals', array('transfer_thru' => strtolower(str_replace(" ", "_", $wd['method']))))}}" class="text-info">{{ $wd['method']}}</a></td>
												<td><div class="wid-">{{ $wd['description']}}</div></td>
												<td>
													@if(round($wd['fee']) == 0)
														<span class="text-danger">NIL</span>
													@else
														<span>{{ $wd['fee_currency']}}</span> <strong>{{ $wd['fee']}}</strong>
													@endif
												</td>
												<td>
													<a class="btn blue btn-xs" href="{{ URL::to('users/my-withdrawals/withdrawals', array('transfer_thru' => strtolower(str_replace(" ", "_", $wd['method']))))}}" class="text-info"><i class="fa fa-cloud-download"></i> {{ trans('myaccount/form.my-withdrawals.withdraw') }}</a>
												</td>
											</tr>
										@endif
									@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div>
			@endif

			{{-- Desk view Code Starts --}}
			{{ Form::open(array('id'=>'myWithdrawalsfrm', 'method'=>'get','class' => 'form-horizontal clearfix' )) }}
				<div class="well">
					<h2 class="title-one">{{ trans('myaccount/form.my-withdrawals.withdraw_request_list') }}:</h2>
					<div class="table-responsive margin-bottom-30">
						<table class="table table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th width="40">{{ trans('myaccount/form.my-withdrawals.id') }}</th>
									<th>{{ trans('myaccount/form.my-withdrawals.date_added') }}</th>
									<th>{{ trans('myaccount/form.my-withdrawals.amount') }}</th>
									<th class="col-md-2">{{ trans('myaccount/form.my-withdrawals.paid_to') }}</th>
									<th>{{ trans('myaccount/form.my-withdrawals.payment_method') }}</th>
									<th class="col-md-2">{{ trans('myaccount/form.my-withdrawals.paid_notes') }}</th>
									<th class="col-md-2">{{ trans('myaccount/form.my-withdrawals.status') }}</th>
								</tr>
							</thead>
							<tbody>
								@if(count($request_details) > 0)
									@foreach($request_details_arr as $reqKey =>  $req)
										<tr>
											<td>{{ $req['id'] }}</td>
											<td>{{ CUtil::FMTDate($req['date_added'], "Y-m-d H:i:s", "") }}</td>
											<td>
												<div class="dl-horizontal dl-horizontal-new">
													<dl>
														<dt>{{ trans('myPurchases.total_amount') }}</dt>
														<dd><span>
															@if($req['currency'] == "INR")
																<span class="clsWebRupe text-muted">Rs</span>
															@else
																<span class="text-muted">{{ $req['currency'] }}</span>
															@endif
															<strong>{{ $req['amount'] }}</strong>
														</span></dd>
													</dl>
													@if($req['fee'] != '-')
														<dl>
															<dt>{{ trans('myaccount/form.my-withdrawals.fee_lbl') }}</dt>
															<dd><span>
																@if($req['currency'] == "INR")
																	<span class="clsWebRupe">Rs</span>
																@else
																	<span class="text-muted">{{ $req['currency'] }}</span>
																@endif
																<strong>{{ $req['fee']}}</strong>
															</span></dd>
														</dl>
														<dl>
															<dt>{{ trans('myaccount/form.my-withdrawals.net_amount') }}</dt>
															<dd><span>
																@if($req['currency'] == "INR")
																	<span class="clsWebRupe text-muted">Rs</span>
																@else
																	<span class="text-muted">{{ $req['currency'] }}</span>
																@endif
																	<strong>{{ $req['net_amount'] }}</strong>
															</span></dd>
														</dl>
													@endif
												</div>
											</td>
											<td><div class="wid-200">{{ nl2br($req['pay_to_user_account']) }}</div></td>
											<td>{{ ($req['payment_type']) }}</td>
											<td>
												<div class="wid-200">
													@if($req['status'] == "Paid")
														{{ nl2br($req['paid_notes']) }}
													@elseif($req['status'] == "Cancelled")
														{{ nl2br($req['cancelled_reason']) }}
													@endif
												</div>
											</td>
											<td>
												@if($req['status'] == "Active")
													<p class="label label-warning">{{trans('common.pending')}}</p>
													<p><a href="{{ URL::to('users/my-withdrawals/cancel-request', array('request_id' => $req['id'] ))}}" title="{{ trans('common.cancel') }}" class="fn_quoteHistoryPop btn btn-xs red"><i class="fa fa-times"></i></a></p>
												@else
													@if($req['status'] == "Cancelled")
														<p class="label label-danger">{{ ucwords(str_replace("_", " ", $req['status'])) }}</p>
														<p><span class="text-muted">{{trans('common.on')}}:</span> {{ CUtil::FMTDate($req['date_cancelled'], "Y-m-d H:i:s", "") }}</p>
													@elseif($req['status'] == "Paid" )
														<p class="label label-success">{{ ucwords(str_replace("_", " ", $req['status'])) }}</p>
														<p><span class="text-muted">{{trans('common.on')}}:</span> {{ CUtil::FMTDate($req['date_paid'], "Y-m-d H:i:s", "") }}</p>
													@endif
												@endif
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="7"><p class="alert alert-info">{{ trans('myaccount/form.my-withdrawals.withdraw_request_none_msg') }}</p></td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
					{{ Form::hidden('req_id', "", array('id'=>'req_id'))}}
					{{ Form::hidden('act', "", array('id'=>'act'))}}
					@if(count($request_details) > 0)
						<div class="text-center">
							{{ $request_details->links() }}
						</div>
					@endif
				</div>
			{{ Form::close() }}
			<!-- WITHDRAW LIST END -->
            
			<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
		</div>
	</div>
    
	<script type="text/javascript">
		var page_name = "my_withdrawals";
		var mes_required = "{{trans('auth/form.required')}}";
    </script>
@stop
