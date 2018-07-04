@extends('admin')
@section('content')
    {{ Form::open(array('url' => 'admin/unpaid-invoice-list/index?tab='.Input::get('tab'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    {{ Form::hidden('tab', Input::get('tab'),array('id' => 'tab')) }}
    	<div class="portlet box blue-madison mb40">
            <!-- SEARCH TITLE STARTS -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/unpaidInvoiceList.search_unpaid_invoices') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- SEARCH TITLE END -->

            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                        	<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('search_id_from', trans('admin/unpaidInvoiceList.search_id'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('search_id_from', Input::get("search_id_from"), array('class' => 'form-control', 'placeholder' => trans('admin/unpaidInvoiceList.search_id_from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('search_id_to', Input::get("search_id_to"), array('class' => 'form-control', 'placeholder' => trans('admin/unpaidInvoiceList.search_id_to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="search_product_id_from" generated="true">{{$errors->first('search_id_from')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('search_invoice_date_added', trans('admin/unpaidInvoiceList.search_invoice_date_added'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                        	{{ Form::text('search_invoice_date_added', Input::old('search_invoice_date_added', Input::get('search_invoice_date_added')), array('id'=>"search_invoice_date_added", 'class'=>'form-control', 'maxlength'=>'100')) }}
                                            <label class="error" for="search_invoice_date_added" generated="true">{{$errors->first('search_invoice_date_added')}}</label>
                                        </div>
                                    </div>
                               </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('search_user_code', trans('admin/unpaidInvoiceList.search_user_code'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_user_code', Input::get("search_user_code"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_user_code" generated="true">{{$errors->first('search_user_code')}}</label>
                                        </div>
                                    </div>
                                    <!--<div class="form-group">
                                        {{ Form::label('search_invoice_total_amount', trans('admin/unpaidInvoiceList.search_invoice_total_amount'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_invoice_total_amount', Input::get("search_invoice_total_amount"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('search_invoice_total_amount')}}</label>
                                        </div>
                                    </div>-->
                                </div>
                           	</div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-2 col-md-5">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                	{{ trans("common.search") }} <i class="fa fa-search"></i>
                                </button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'">
                                    <i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}
                                </button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
     	</div>
    {{ Form::close() }}

    <!-- TAB STARTS -->
    <div class="tabbable-custom tabbable-customnew">
        <ul class="nav nav-tabs">
            <li class="@if($tab == 'by_user' || $tab =='')active @endif"><a href="{{ URL::to('admin/unpaid-invoice-list/index?tab=by_user') }}">By User</a></li>
            <li class="@if($tab == 'by_admin') active @endif"><a href="{{ URL::to('admin/unpaid-invoice-list/index?tab=by_admin') }}">By Admin</a></li>
        </ul>
    </div>
    <!-- TAB END -->

    <div class="portlet box blue-hoki">
        <!-- TABLE TITLE STARTS -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/unpaidInvoiceList.my_invoice') }}
            </div>
        </div>
        <!-- TABLE TITLE END -->

        <div class="portlet-body">
            @if(count($invoice_details) <= 0)
                <!-- INFO STARTS -->
                <div class="note note-info mar0">
                   {{ Lang::get('admin/unpaidInvoiceList.no_result') }}
                </div>
                <!-- INFO END -->
            @else
                <div class="table-responsive">
                   <table class="table table-striped table-hover table-bordered">
                       <thead>
                            <tr>
                                <th width="40">{{ Lang::get('admin/unpaidInvoiceList.common_invoice_id') }}</th>
                                <th>{{ Lang::get('admin/unpaidInvoiceList.user_name') }}</th>
                                <th>{{ Lang::get('admin/unpaidInvoiceList.date_added') }}</th>
                                <th>{{ Lang::get('admin/unpaidInvoiceList.total_amount') }}</th>
                                <th>{{ Lang::get('admin/unpaidInvoiceList.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($invoice_details) > 0)
                                @foreach($invoice_details as $invoice)
                                    <tr>
                                       <td>{{ $invoice->common_invoice_id }}</td>
                                        <?php
                                            $user_details = CUtil::getUserDetails($invoice->user_id);
                                            $user_name = $user_details['display_name'];
                                            $u_name = $user_details['user_name'];
                                        ?>
                                        <td>
											<p><a class="" href="{{ URL::to('admin/users/user-details').'/'.$invoice->user_id.'?i=invoice' }}" title=""><strong>{{ $user_name }}</strong></a></p>
											<p>(<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$invoice->user_id.'?i=invoice' }}" title=""><strong>{{ $user_details['user_code'] }}</strong></a>)</p>
										</td>
                                        @if($invoice->reference_type == 'Credits' || $invoice->reference_type == 'Usercredits')
                                            <td>{{ CUtil::FMTDate($invoice->date_added, 'Y-m-d H:i:s', '')}}</td>
                                        @else
                                            <td>{{ CUtil::FMTDate($invoice->date_created, 'Y-m-d H:i:s', '')}}</td>
                                        @endif
                                        @if($invoice->reference_type == 'Credits' || $invoice->reference_type == 'Usercredits')
                                            <td>{{ CUtil::convertAmountToCurrency($invoice->amount, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
                                        @else
                                            <td>{{ CUtil::convertAmountToCurrency($invoice->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
                                        @endif
                                        @if($invoice->reference_type == 'Products')
                                            <td class="action-btn">
                                                <a href="{{ URL::action('AdminPurchasesController@getOrderDetails', $invoice->id).'?tab='.$tab.'&i=invoice' }}" class="btn btn-xs btn-info" title="{{ Lang::get('myPurchases.view')  }}">
												<i class="fa fa-eye"></i></a>
                                            </td>
                                        @else
                                            <td><a href="{{ URL::action('AdminUnpaidInvoiceListController@getInvoiceDetails', $invoice->common_invoice_id).'?tab='.$tab }}" class="btn btn-xs btn-info" title=""><i class="fa fa-eye"></i></a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5"><p class="alert alert-info">{{ Lang::get('unpaidInvoiceList.no_result') }}</p></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(count($invoice_details) > 0)
                    <div class="text-right">
                        {{ $invoice_details->appends( array('tab' => Input::get('tab'), 'search_user_code' => Input::get('search_user_code'), 'search_id_from' => Input::get('search_id_from'), 'search_id_to' => Input::get('search_id_to'), 'search_user_name' => Input::get('search_user_name'), 'search_invoice_date_added' => Input::get('search_invoice_date_added'), 'search_invoice_total_amount' => Input::get('search_invoice_total_amount') ))->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@stop
@section('script_content')
	<script type="text/javascript">
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(function() {
	        $('#search_invoice_date_added').datepicker({
	            format: 'yyyy-mm-dd',
                autoclose: true,
	            todayHighlight: true
	        });
	    });
	</script>
@stop