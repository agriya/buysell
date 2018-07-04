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
	<a class="btn default btn-xs purple-stripe pull-right" href="{{ Url::action('AdminSalesController@getMemberWise') }}" title="{{ Lang::get('admin/salesReport.back_to_sales_report') }}">
    	<i class="fa fa-chevron-left"></i> {{ Lang::get('admin/salesReport.back_to_sales_report') }}
    </a>
    <h1 class="page-title">{{Lang::get('admin/salesReport.owner_wise_sales_report')}}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('url' => Url::action('AdminSalesController@getMember', $user_id), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
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
                                </div>
                           	</div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-2 col-md-5">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                {{ trans("common.search") }} <i class="fa fa-search"></i></button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminSalesController@getMember', $user_id) }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}</button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
			<!-- END: SEARCH FORM -->
     	</div>
    {{ Form::close() }}


    <div class="portlet box blue-hoki mb40">
         <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
            <div class="caption">
                <i class="fa fa-bar-chart"></i> {{ trans('admin/salesReport.owner_details') }}
            </div>
        </div>
		 <!-- BEGIN: PAGE TITLE -->

		<!-- BEGIN: OWNER DETAILS -->
        <div class="portlet-body admin-dl">
			<div class="dl-horizontal">
				<dl>
					<dt>{{ Lang::get('admin/salesReport.owner_name') }}</dt>
					<dd><span>
						<a href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{$user_details['display_name']}}</a>
						(<a href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{$user_details['user_code']}}</a>)
					</span></dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.product_for_sale') }}</dt>
					<dd><span>{{$user_details['total_products']}}</span></dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.owner_earnings') }}</dt>
					<dd>{{ CUtil::convertAmountToCurrency($earned_details['seller_earned'], Config::get('generalConfig.site_default_currency'), '', true) }}</dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.site_earnings') }}</dt>
					<dd>{{ CUtil::convertAmountToCurrency($earned_details['site_earned'], Config::get('generalConfig.site_default_currency'), '', true) }}</dd>
				</dl>
			</div>
        </div>
		<!-- END: OWNER DETAILS -->
 	</div>

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
					<table class="table table-hover table-bordered">
						<thead>
							<tr>
								<th>{{ Lang::get('admin/salesReport.product_id') }}</th>
								<th>{{ Lang::get('admin/salesReport.total_sales') }}</th>
								<th>{{ Lang::get('admin/salesReport.owner_earning') }}</th>
								<th>{{ Lang::get('admin/salesReport.site_earning') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($owner_sales_report as $sales_report)
								<tr>
									<td><a href="{{Url::action('AdminSalesController@getProduct', $sales_report->item_id).'?page_from=member&member_id='.$user_id}}">{{ CUtil::getProductCodeUsingID($sales_report->item_id)}}</a></td>
									<td>
										<a href="{{Url::action('AdminSalesController@getProduct', $sales_report->item_id).'?page_from=member&member_id='.$user_id}}" class="pull-right">
											&raquo; <small>{{ Lang::get('common.details')}}</small>
										</a>
										{{ $sales_report->total_sales}}
									</td>
									<td>{{ CUtil::convertAmountToCurrency($sales_report->owners_earning, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
									<td>{{ CUtil::convertAmountToCurrency($sales_report->site_earning, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
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