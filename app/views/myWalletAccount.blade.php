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
				@if(CUtil::chkIsAllowedModule('sudopay'))
				<div class="">
						<a class="btn green-meadow btn-xs responsive-btn-block pull-right" href="{{URL::action('WalletAccountController@getAddAmount')}}"><i class="fa fa-plus margin-right-5"></i> {{ trans('walletAccount.add_amount_to_wallet') }}</a>
					</div>
				@endif
				<h1>{{ trans('walletAccount.my_wallet_account') }}</h1>
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
			<!-- ALERT BLOCK ENDS -->

			@if(count($withdrwal_request_details) > 0)
			<div class="well">
				{{ Form::open(array('id'=>'myWithdrawalsfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
					<h2 class="title-one">{{ trans('walletAccount.withdraw_request_list') }}</h2>
					<div class="table-responsive margin-bottom-30">
						<table class="table table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>{{ trans('walletAccount.id') }}</th>
									<th>{{ trans('walletAccount.date_added') }}</th>
									<th>{{ trans('walletAccount.amount') }}</th>
									<th>{{ trans('walletAccount.status') }}</th>
								</tr>
							</thead>
							<tbody>
								@if(count($withdrwal_request_details) > 0)
									@foreach($withdrwal_request_details as $reqKey =>  $req)
										<tr>
											<td>{{ $req['withdraw_id'] }}</td>
											<td class="text-muted">{{ CUtil::FMTDate($req['added_date'], "Y-m-d H:i:s", "") }}</td>
											<td>
												<div class="dl-horizontal dl-horizontal-new wallet-acc">
												<dl>
													<dt>{{ trans('walletAccount.requested_amount') }}</dt>
													<dd><span>
													@if($req['currency'] == "INR")
														<span class="clsWebRupe">Rs</span>
													@else
														<span class="text-muted">{{ $req['currency'] }}</span>
													@endif
													<strong>{{ $req['amount'] }}</strong>
														</span></dd>
												</dl>
												@if($req['fee'] != '-')
													<dl>
														<dt>{{ trans('walletAccount.fee_lbl') }}</dt>
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
														<dt>{{ trans('walletAccount.net_amount') }}</dt>
														<dd><span>
															@if($req['currency'] == "INR")
																<span class="clsWebRupe">Rs</span>
															@else
																<span class="text-muted">{{ $req['currency'] }}</span>
															@endif
															<strong>{{ $req['amount'] }}</strong>
														</span></dd>
													</dl>
												@endif
												</div>
											</td>
											<td>
												@if($req['status'] == "Paid")
													<span class="label label-success">{{ trans('common.paid') }}</span>
												@elseif($req['status'] == "Cancelled")
													<span class="label label-danger">{{ trans('common.cancelled') }}</span>
												@else
													<span class="label label-warning">{{ trans('common.pending') }}</span>
												@endif
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="4"><p class="alert alert-info">{{ trans('walletAccount.withdraw_request_none_msg') }}</p></td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
					@if(count($withdrwal_request_details) > 0)
						<div class="pull-right margin-top-10">
							{{ $withdrwal_request_details->links() }}
						</div>
					@endif
					{{ Form::hidden('req_id', "", array('id'=>'req_id'))}}
					{{ Form::hidden('act', "", array('id'=>'act'))}}
				{{ Form::close() }}
			</div>
			@endif
		</div>
	</div>
@stop