@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!-- BEGIN: SUCCESS INFO -->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- END: SUCCESS INFO -->

	<!-- BEGIN: PAGE TITLE -->
	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <h1 class="page-title">{{Lang::get('admin/salesReport.owner_wise_sales_report')}}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('url' => Url::action('AdminSalesController@getMemberWise'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/salesReport.search_sales_report') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->

			<!-- BEGIN: SEARCH FORM -->
            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                        	<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('owner_id', trans('common.seller_code'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                 							{{ Form::text('owner_id', Input::get("owner_id"), array('class' => 'form-control')) }}
                                            <label class="error" for="owner_id" generated="true">{{$errors->first('owner_id')}}</label>
                                        </div>
                                    </div>
                               </div>

                               <!--<div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('transaction_date', trans('admin/salesReport.transaction_date'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('from_date', Input::get("from_date"), array('class' => 'form-control', 'id'=>'from_date', 'placeholder' => trans('admin/salesReport.from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('to_date', Input::get("to_date"), array('class' => 'form-control', 'id'=>'to_date', 'placeholder' => trans('admin/salesReport.to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="search_date" generated="true">{{$errors->first('from_date')}}</label>
                                        </div>
                                    </div>
                                </div>-->
                           	</div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-2 col-md-5">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                {{ trans("common.search") }} <i class="fa fa-search"></i></button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminSalesController@getMemberWise') }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}</button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
			<!-- END: SEARCH FORM -->
     	</div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-bar-chart"></i> {{ Lang::get('admin/salesReport.sales_report_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(count($owner_sales_report) > 0 )
            	<!-- BEGIN: SALES REPORT OWNER LIST -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ Lang::get('common.seller')}}</th>
                                    <th>{{ Lang::get('admin/salesReport.total_sales') }}</th>
                                    <th>{{ Lang::get('admin/salesReport.owner_earning') }}</th>
                                    <th>{{ Lang::get('admin/salesReport.site_earning') }}</th>
                                    <th>{{ Lang::get('admin/salesReport.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($owner_sales_report as $sales_report)
                                	<?php
										$product_details = array();
										$user_details = CUtil::getUserDetails($sales_report->item_owner_id);
										$site_default_currency = Config::get('generalConfig.site_default_currency');
									 ?>
                                    <tr>
                                    	<td>
											<a href="{{ URL::to('admin/users/user-details').'/'.$sales_report->item_owner_id }}">{{$user_details['display_name']}}</a>
											(<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$sales_report->item_owner_id }}">{{$user_details['user_code']}}</a>)
										</td>
                                        <td><strong>{{ $sales_report->total_sales}}</strong></td>
                                        <td>{{ CUtil::convertAmountToCurrency($sales_report->owners_earning, $site_default_currency, '', true) }}</td>
                                        <td>{{ CUtil::convertAmountToCurrency($sales_report->site_earning, $site_default_currency, '', true) }}</td>
                                        <td>
											<a href="{{Url::action('AdminSalesController@getMember',$sales_report->item_owner_id)}}" title="{{ trans('common.view_details') }}" class="btn btn-info btn-xs">
											<i class="fa fa-eye"></i></a>
										</td>
                                    </tr>
                                @endforeach
                            </tbody>
                         </table>
                    </div>

                 <!-- END: SALES REPORT OWNER LIST -->

                <!-- BEGIN: PAGINATION -->
                <div class="text-right">
                    {{ $owner_sales_report->appends(array('owner_id_from' => Input::get('owner_id_from'), 'owner_id_to' => Input::get('owner_id_to'),
						'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date')))->links() }}
                </div>
                <!-- END: PAGINATION -->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/salesReport.no_sales_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>

    </div>
@stop

@section('script_content')
	<script type="text/javascript">

		$(function() {
            $('#from_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#to_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });

	</script>
@stop