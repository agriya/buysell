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
	<?php $back_url = Url::action('AdminSalesController@getIndex');?>
	@if(Input::has('page_from') && Input::get('page_from')=='member')
		<?php
			if(Input::has('member_id') && Input::get('member_id')!='')
				$back_url = Url::action('AdminSalesController@getMember', Input::get('member_id'));
			else
				$back_url = Url::action('AdminSalesController@getMemberWise');
		?>
	@endif
	<a class="btn default btn-xs purple-stripe pull-right" href="{{ $back_url }}" title="{{ Lang::get('admin/salesReport.back_to_sales_report') }}">
    	<i class="fa fa-chevron-left"></i> {{ Lang::get('admin/salesReport.back_to_sales_report') }}
    </a>
    <h1 class="page-title">{{Lang::get('admin/salesReport.sales_report_for_particular_product')}}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('url' => Url::action('AdminSalesController@getProduct',$product_id), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
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
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminSalesController@getProduct', $product_id) }}'"> <i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}</button>
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
				<i class="fa fa-line-chart"></i> {{ trans('admin/salesReport.product_details') }}
			</div>
		</div>
		<!-- END: PAGE TITLE -->

		<!-- BEGIN: PRODUCT DETAILS -->
		<div class="portlet-body admin-dl">
			<div class="dl-horizontal">
			<?php $user_det = CUtil::getUserDetails($product_details['product_user_id']);
					$p_service = new ProductService;
				 $product_view_url = $p_service->getProductViewURL($product_details['id'], $product_details); ?>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.product_name') }}</dt>
					<dd><span><a href="{{ $product_view_url }}">{{$product_details['product_name']}}</a></span></dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.product_code') }}</dt>
					<dd><span><a href="{{ $product_view_url }}">{{$product_details['product_code']}}</a></span></dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.owner') }}</dt>
					<dd><span><a href="{{ URL::to('admin/users/user-details').'/'.$product_details['product_user_id'] }}">{{ $user_det['display_name'] }}</a></span></dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.owner_earnings') }}</dt>
					<dd><span class="text-success"><strong>{{ CUtil::convertAmountToCurrency($earned_details['seller_earned'], Config::get('generalConfig.site_default_currency'), '', true) }}</strong></span></dd>
				</dl>
				<dl>
					<dt>{{ Lang::get('admin/salesReport.site_earnings') }}</dt>
					<dd><span class="text-warning"><strong>{{ CUtil::convertAmountToCurrency($earned_details['site_earned'], Config::get('generalConfig.site_default_currency'), '', true) }}</strong></span></dd>
				</dl>
			</div>
		</div>
		<!-- END: PRODUCT DETAILS-->
	</div>

	<div class="portlet box blue-hoki">
        <!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i> {{ Lang::get('admin/salesReport.sales_report_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(count($product_sales_report) > 0 )
            	<!-- BEGIN: SALES REPORT PRODUCT LIST -->
				<div class="table-responsive">
					<table class="table table-hover table-bordered table-striped">
						<thead>
							<tr>
								<th>{{ Lang::get('admin/salesReport.sales_date') }}</th>
								<th>{{ Lang::get('admin/salesReport.no_of_items') }}</th>
								<th>{{ Lang::get('admin/salesReport.buyer_details') }}</th>
								<th>{{ Lang::get('admin/salesReport.sales_amount') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($product_sales_report as $sales_report)
								<?php
									$buyer_details = CUtil::getUserDetails($sales_report['buyer_id']);
								?>
								<tr>
									<td>{{ CUtil::FMTDate($sales_report->date_added, 'Y-m-d H:i:s', '') }}</td>
									<td>{{ $sales_report->item_qty }}</td>
									<td>
										<p><strong><a href="{{ URL::to('admin/users/user-details').'/'.$sales_report['buyer_id'] }}">{{ $buyer_details['display_name'] }}</a></strong></p>
										<a href="{{ URL::to('admin/users/user-details').'/'.$sales_report['buyer_id'] }}" class="text-muted">({{ $buyer_details['user_code'] }})</a>
									</td>
									<td><strong>{{ CUtil::convertAmountToCurrency($sales_report->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}</strong></td>
								</tr>
							@endforeach
						</tbody>
					 </table>
				</div>
                 <!-- END: SALES REPORT PRODUCT LIST -->

                <!-- BEGIN: PAGINATION -->
                <div class="text-right">
					{{ $product_sales_report->appends(array('product_id_from' => Input::get('product_id_from'), 'product_id_to' => Input::get('product_id_to'),
					'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date')))->links() }}
                </div>
                <!-- END: PAGINATION -->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/salesReport.no_sales_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminStaticPageController@postAction'))) }}
    {{ Form::hidden('id', '', array('id' => 'list_id')) }}
    {{ Form::hidden('action', '', array('id' => 'list_action')) }}
    {{ Form::close() }}

	<div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
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