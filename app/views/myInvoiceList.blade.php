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
				<h1>{{ Lang::get('myInvoice.my_invoice') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: INFO BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: INFO BLOCK -->

			<!-- BEGIN: INVOICE LIST -->
			{{ Form::open(array('action' => array('InvoiceController@getIndex'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
				<div class="tabbable tabbable-custom">
					<div class="customview-navtab margin-top-10">
						<ul class="nav nav-tabs margin-bottom-20">
							<li class="@if($status == 'Paid' || $status =='')active @endif"><a href="{{ URL::to('invoice?status=Paid') }}">{{trans('myInvoice.paid')}}</a></li>
							<li class="@if($status == 'Unpaid') active @endif"><a href="{{ URL::to('invoice?status=Unpaid') }}">{{trans('myInvoice.unpaid')}}</a></li>
						</ul>
					</div>
					<div class="well">
						<div id="current_table_id">
							@if(count($invoice_details) <= 0)
								<div class="note note-info margin-0">{{ Lang::get('myInvoice.no_result') }}</div>
							@else
								<div class="table-responsive margin-bottom-30">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th width="100">{{ Lang::get('myInvoice.common_invoice_id') }}</th>
												<th class="col-md-2">{{ Lang::get('myInvoice.date_added') }}</th>
												<th class="col-md-3">{{ Lang::get('myInvoice.user_notes') }}</th>
												<th class="col-md-2">{{ Lang::get('myInvoice.paid_date') }}</th>
												<th class="col-md-2">{{ Lang::get('myInvoice.amount') }}</th>
												<th class="col-md-1">{{ Lang::get('myInvoice.status') }}</th>
												<th width="70">{{ Lang::get('myInvoice.action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(count($invoice_details) > 0)
												@foreach($invoice_details as $invoice)
													<tr>
														<td>{{ $invoice->common_invoice_id }}</td>
														@if($invoice->reference_type == 'Products')
															<td class="text-muted">{{ CUtil::FMTDate($invoice->date_created, "Y-m-d H:i:s", "") }}</td>
														@else
															@if($invoice->date_added != '0000-00-00 00:00:00')
																<td class="text-muted">{{ CUtil::FMTDate($invoice->date_added, "Y-m-d H:i:s", "") }}</td>
															@else
																<td>-</td>
															@endif
														@endif

														@if($invoice->reference_type == 'Products')
															<td>{{ Lang::get('myInvoice.created_for_product_purchase')}}</td>
														@else
															<td><div class="wid-200">{{ $invoice->user_notes }}</div></td>
														@endif

														@if($invoice->status == 'Paid' && $invoice->date_paid != '0000-00-00 00:00:00')
															<td class="text-muted">{{ CUtil::FMTDate($invoice->date_paid, "Y-m-d H:i:s", "") }}</td>
														@else
															<td>--</td>
														@endif

														@if($invoice->reference_type == 'Products')
															<td>{{ CUtil::convertAmountToCurrency($invoice->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
														@else
															<td>{{ CUtil::convertAmountToCurrency($invoice->amount, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
														@endif
														<td>
															<?php
																$lbl_class = "";
																if(strtolower ($invoice->status) == "unpaid")
																	$lbl_class = "label-danger";
																else
																	$lbl_class = "label-success";
															?>
															<span class="label {{ $lbl_class }}">{{ Lang::get('myInvoice.'.strtolower($invoice->status)) }}</span>
														</td>
														@if($invoice->reference_type == 'Products')
															<td class="action-btn">
																<a href="{{ URL::action('PurchasesController@getOrderDetails', $invoice->id).'?s='.$invoice->status.'&i=invoice' }}" class="btn btn-xs btn-info" title="{{ Lang::get('myPurchases.view') }}"><i class="fa fa-eye"></i></a>
															</td>
														@else
															<td class="action-btn"><a href="{{ URL::action('InvoiceController@getInvoiceDetails', $invoice->common_invoice_id).'?s='.$invoice->status }}" class="btn btn-xs btn-info" title="{{ Lang::get('myPurchases.view') }}"><i class="fa fa-eye"></i></a></td>
														@endif
													</tr>
												@endforeach
											@else
												<tr>
													<td colspan="7"><p class="alert alert-info">{{ Lang::get('myInvoice.no_result') }}</p></td>
												</tr>
											@endif
										</tbody>
									</table>
								</div>
								@if(count($invoice_details) > 0)
									<div class="text-right">{{ $invoice_details->appends( array('status' => Input::get('status')))->links() }}</div>
								@endif
							@endif
						</div>
					</div>
				</div>
			{{ Form::close() }}
			<!-- END: INVOICE LIST -->
		</div>
	</div>
@stop