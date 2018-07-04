@extends('admin')
@section('content')
	@if(Input::get('m') == 'member')
        <a href="{{ Url::to('admin/users/user-details/').'/'.$invoice_details->user_id.'?status='.$s }}" class="btn default purple-stripe btn-xs pull-right mt5"><i class="fa fa-arrow-left"></i> {{ trans('admin/manageMembers.viewmember_user_details_title') }}</a>
  	@else
        <a href="{{ Url::to('admin/unpaid-invoice-list/index').'?tab='.$tab }}" class="btn default blue-stripe btn-xs pull-right mt5"><i class="fa fa-arrow-left"></i> {{trans('admin/unpaidInvoiceList.unpaid_invoices_list')}}</a>
   	@endif
    <h1 class="page-title">{{ Lang::get('admin/unpaidInvoiceList.invoice_details') }}</h1>
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    @if(count($invoice_details) <= 0)
    <div class="note note-info">
       {{ Lang::get('myInvoice.invalid_id') }}
    </div>
    @else
        @if(count($invoice_details) > 0)
            {{ Form::open(array('action' => array('AdminUnpaidInvoiceListController@getInvoiceDetails'), 'id'=>'unpaidinvoice', 'method'=>'get','class' => 'form-horizontal' )) }}
            <div class="row">
                <div class="col-md-6">
                   <div class="portlet green-meadow box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cogs"></i> Invoice Address
                            </div>
                        </div>
                        <div class="portlet-body form">
                        	<div class="form-body">
                                <h4 class="form-section">{{ trans('myInvoice.invoice_from') }}</h4>
								<?php
                                    $user_details = CUtil::getUserDetails($invoice_details->user_id);
                                    $user_url = URL::to('admin/users/user-details').'/'.$invoice_details->user_id;
                                    $user_name = $user_details['display_name'];
                                ?>
                                <p>
									@if($invoice_details->reference_type == 'Usercredits')
										<a href="{{ $user_url }}">{{ $user_name }}</a>
										(<a class="text-muted" href="{{ $user_url }}">{{ $user_details['user_code'] }}</a>)
									@else
										{{ trans('myInvoice.site_name') }}
									@endif
								</p>
                                <h4 class="form-section">{{ trans('myInvoice.invoice_to') }}</h4>
                                <p>
									<a href="{{ $user_url }}">{{ $user_name }}</a>
									(<a class="text-muted" href="{{ $user_url }}">{{ $user_details['user_code'] }}</a>)
								</p>
                            </div>
                    	</div>
                	</div>
                </div>
                <div class="col-md-6">
	                <div class="portlet purple-plum box address-details">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cogs"></i> Invoice Payment
                            </div>
                        </div>
                        <div class="portlet-body admin-dl">
							<?php
                                $lbl_class = '';
                                if($invoice_details->status == 'Paid') {
                                    $lbl_class = "label-success";
                                }
                                elseif($invoice_details->status == 'Unpaid') {
                                    $lbl_class = "label-danger";
                                }
                            ?>
                            <div class="dl-horizontal">
                                <dl><dt>Status</dt> <dd><span><span class="label {{ $lbl_class }}">{{ $invoice_details->status }}</span></span></dd></dl>
                                <dl>
                                    @if($invoice_details->date_added != '0000-00-00 00:00:00')
                                        <dt>{{ trans('myInvoice.invoice_added') }}</dt> <dd><span>{{ CUtil::FMTDate($invoice_details->date_added, "Y-m-d H:i:s", "") }}</span></dd>
                                    @else
                                        <dt>{{ trans('myInvoice.invoice_added') }}</dt> <dd><span>-</span></dd>
                                    @endif
                                </dl>
                                @if($invoice_details->status == 'Paid')
                                	<dl>
                                        @if($invoice_details->date_added != '0000-00-00 00:00:00')
                                            <dt>{{ trans('myInvoice.paid_date') }}</dt> <dd><span>{{ CUtil::FMTDate($invoice_details->date_paid, "Y-m-d H:i:s", "") }}</span></dd>
                                        @else
                                        <dt>{{ trans('myInvoice.paid_date') }}</dt> <dd><span>-</span></dd>
                                    @endif
                                    </dl>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="portlet box blue-hoki">
                    <!-- table title starts -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i> Details
                        </div>
                    </div>
			<div class="portlet-body">
                <div class="table-responsive">
                     <table class="table table-striped table-hover table-bordered">
                         <thead>
	                        <tr>
	                            <th class="col-md-2">{{ Lang::get('myInvoice.description') }}</th>
	                            <th class="col-md-2">{{ Lang::get('myInvoice.amount') }}</th>
	                        </tr>
	                    </thead>
						<tbody>
						@if(count($invoice_details) > 0)
                         	<tr>
								<td>{{ $invoice_details->user_notes }}</td>
								<td>
									{{ CUtil::convertAmountToCurrency($invoice_details->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
								</td>
							</tr>
							<tr>
								<td class="fonts18 text-right">{{ Lang::get('myInvoice.total_amount') }}</td>
								<td class="fonts18">
									{{ CUtil::convertAmountToCurrency($invoice_details->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
								</td>
							</tr>
						@else
                            <tr>
                                <td colspan="4"><p class="alert alert-info">{{ Lang::get('myInvoice.invalid_id') }}</p></td>
                            </tr>
                        @endif
						</tbody>
					</table>
				</div>
			</div>
           {{ Form::close() }}
		@endif
	@endif
@stop