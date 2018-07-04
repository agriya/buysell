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
    <h1 class="page-title">{{Lang::get('admin/salesReport.product_wise_sales_report')}}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('url' => Url::action('AdminSalesController@getIndex'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
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
										{{ Form::label('product_code', trans('admin/productList.product_product_code'), array('class' => 'col-md-4 control-label')) }}
										<div class="col-md-6">
											{{ Form::text('product_code', Input::get("product_code"), array('class' => 'form-control')) }}
											<label class="error" for="product_code" generated="true">{{$errors->first('product_code')}}</label>
										</div>
									</div>
								</div>
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
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminSalesController@getIndex') }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}</button>
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
                <i class="fa fa-line-chart"></i> {{ Lang::get('admin/salesReport.sales_report_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(count($product_sales_report) > 0 )
            	<!-- BEGIN: SALES REPORT ALL PRODUCT LIST -->
				<div class="table-responsive">
					<table class="table table-hover table-bordered table-striped">
						<thead>
							<tr>
								<th>{{ Lang::get('admin/salesReport.product_details') }}</th>
								<th>{{ Lang::get('admin/salesReport.owner') }}</th>
                                <th>{{ Lang::get('common.date') }}</th>
								<th>{{ Lang::get('admin/salesReport.total_sales') }}</th>
								<th>{{ Lang::get('admin/salesReport.owner_earning') }}</th>
								<th>{{ Lang::get('admin/salesReport.site_earning') }}</th>
								<th>{{ Lang::get('admin/salesReport.action') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($product_sales_report as $sales_report)
								<?php
                                    $product_details = array();
									$user_details = CUtil::getUserDetails($sales_report->item_owner_id);

									$prod_obj = Products::initialize($sales_report->item_id);
									$prod_obj->setIncludeBlockedUserProducts(true);
									$prod_obj->setIncludeDeleted(true);
									$product_details = $prod_obj->getProductDetails();
									$p_service = new ProductService;
									$product_view_url = $p_service->getProductViewURL($sales_report->item_id, $product_details);
									$site_default_currency = Config::get('generalConfig.site_default_currency');
								?>
								<tr>
									<td>
										<div class="dl-horizontal dl-horizontal-new">
											<dl>
												<dt>{{{ trans('admin/productList.product_product_name') }}}</dt>
												<dd>
													<span>
														<a href="{{ $product_view_url }}" title="{{{ $product_details['product_name'] }}}">
															{{ Str::limit($product_details['product_name'] , 25) }}
														</a>
													</span>
												</dd>
											</dl>
											<dl>
												<dt>{{ trans('admin/productList.product_product_code') }}</dt>
												<dd><span><a href="{{ $product_view_url }}">{{ $product_details['product_code'] }}</a></span></dd>
											</dl>
										</div>
									</td>
									<td>
										<p><strong><a href="{{ URL::to('admin/users/user-details').'/'.$sales_report->item_owner_id }}">{{$user_details['display_name']}}</a></strong></p>	
                                        <a href="{{ URL::to('admin/users/user-details').'/'.$sales_report->item_owner_id }}" class="text-muted">({{ $user_details['user_code'] }})</a>
									</td>
                                    <td class="text-muted">{{{ CUtil::FMTDate($sales_report->date_added, 'Y-m-d H:i:s', '') }}}</td>
									<td><strong>{{ $sales_report->item_qty}}</strong></td>
									<td><strong class="text-success">{{ CUtil::convertAmountToCurrency($sales_report->seller_amount, $site_default_currency, '', true) }}</strong></td>
									<td><strong class="text-warning">{{ CUtil::convertAmountToCurrency($sales_report->site_commission, $site_default_currency, '', true) }}</strong></td>
									<td>
										<a href="{{Url::action('AdminSalesController@getProduct',$sales_report->item_id)}}" title="{{ trans('common.view_details') }}" class="btn btn-xs btn-info">
										<i class="fa fa-eye"></i></a>
									</td>
								</tr>
							@endforeach
						</tbody>
					 </table>
				</div>
                 <!-- END: SALES REPORT ALL PRODUCT LIST -->

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