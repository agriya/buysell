@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
	<a class="pull-right mt10 btn btn-success btn-xs" title="{{Lang::get('admin/currencies.add_currency')}}" data-toggle="modal" data-target="#myModal">
		<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/currencies.add_currency') }}
	</a>
	<h1 class="page-title">{{Lang::get('admin/currencies.manage_currency')}}</h1>
	<!-- END: PAGE TITLE -->

	<!-- BEGIN: CURRENCY ADDED -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span><span class="sr-only">{{trans('common.close')}}</span>
                    </button>
                    <h3 class="mar0" id="myModalLabel">{{ Lang::get('admin/currencies.add_currency') }}</h3>
                </div>
                <div class="modal-body">
					{{ Form::open(array('url' => Url::action('AdminExchangeRateController@postAdd'), 'id'=>'addCurrencyfrm', 'method'=>'post','class' => 'form-horizontal' )) }}
						<div class="form-body">
							<div class="form-group">
								{{ Form::label('currency_id', trans('admin/currencies.currency_code'), array('class' => 'col-sm-4 control-label required-icon')) }}
								<div class="col-sm-6">
									{{ Form::select('currency_id', $nonavail_currencies_list, Input::old("currency_id"), array('class' => 'form-control select2me input-medium')) }}
									<label class="error" for="status" generated="true">{{$errors->first('currency_id')}}</label>
								</div>
							</div>
                        </div>
					{{ Form::close()}}
                </div>
                <div class="modal-footer">
                	<button type="button" class="btn green js-submit"><i class="fa fa-plus"></i> {{ Lang::get('admin/currencies.add_currency') }} </button>
                    <button type="button" class="btn default" data-dismiss="modal"><i class="fa fa-times"></i> {{trans('common.close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: CURRENCY ADDED -->

	{{ Form::open(array('url' => Url::action('AdminExchangeRateController@getIndex'), 'id'=>'currenciesfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/currencies.search_currency') }}
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
							<div class="form-group">
								{{ Form::label('currency_code', trans('admin/currencies.currency_code'), array('class' => 'col-md-3 control-label')) }}
								<div class="col-md-4">
									{{ Form::text('currency_code', Input::get("status"), array('class' => 'form-control')) }}
									<label class="error" for="status" generated="true">{{$errors->first('currency_code')}}</label>
								</div>
							</div>
                        </div>
                        <div class="form-actions fluid">
							<div class="col-md-offset-3 col-md-5">
								<button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
								{{ trans("common.search") }} <i class="fa fa-search"></i></button>
								<button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminExchangeRateController@getIndex') }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}
								</button>
							</div>
						</div>
                    </div>
                </div>
            </div>
			<!-- END: SEARCH FORM -->
     	</div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-dollar"></i> {{ Lang::get('admin/currencies.currencies_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($currencies_list) > 0 )
            	<!--- BEGIN: CURRENCIES LIST --->
                {{ Form::open(array('url'=>URL::action('AdminExchangeRateController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-scrollable">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('admin/currencies.currency_code') }}</th>
                                    <th>{{ Lang::get('admin/currencies.currency_symbol') }}</th>
                                    <th>{{ Lang::get('admin/currencies.currency_amount') }}</th>
                                    <th>{{ Lang::get('admin/currencies.last_updated_on') }}</th>
                                    <th>{{ Lang::get('admin/currencies.status') }}</th>
                                    <th>{{ Lang::get('admin/currencies.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($currencies_list as $currency)
                                	<tr>
                                        <td>
                                        	@if($currency['currency_code']!='USD')
												{{Form::checkbox('ids[]',$currency['id'], false, array('class' => 'checkboxes js-ids') )}}
											@else
												{{Form::checkbox('ids[]',$currency['id'], false, array('class' => 'checkboxes js-ids', 'disabled' => 'disabled') )}}
											@endif
										</td>
                                        <td>{{ $currency['currency_code'] }}</td>
                                        <td><span class="text-muted">{{ $currency['currency_symbol'] }}</span></td>
                                        <td><span class="text-muted">{{ $currency['currency_symbol']}}</span> <strong>{{ CUtil::convertAmountToCurrency(1, Config::get('generalConfig.site_default_currency'), $currency['currency_code'], true) }}</strong></td>
                                        <td class="text-muted">{{ CUtil::FMTDate($currency['updated_at'], 'Y-m-d H:i:s', '') }}</td>
                                        <td>
											<?php
												$lbl_class = "label-default";
												if(count($currency) > 0) {
													if($currency['status'] == 'Active') {
														$lbl_class = "label-success";
														$status = trans('common.active');
													}
													elseif($currency['status'] == 'InActive') {
														$lbl_class = " label-danger";
														$status = trans('common.inactive');
													}
													else{ $lbl_class = "label-default"; }
												}
											?>
											<span class="label {{ $lbl_class }}">{{ $status }}</span>
										</td>
                                        <td class="status-btn">
                                        	@if($currency['currency_code']!='USD')
	                                        	@if($currency['status']=='Active')
	                                        		<a onclick="doAction({{$currency['id']}},'deactivate')"  class="btn btn-xs bg-red-pink" title="{{ trans('admin/currencies.deactivate') }}">
													<i class="fa fa-ban"></i></a>
	                                        	@else
	                                        		<a onclick="doAction({{$currency['id']}},'activate')"  class="btn btn-xs green" title="{{ trans('admin/currencies.activate') }}">
													<i class="fa fa-check"></i></a>
	                                        	@endif
	                                        	<a onclick="doAction({{$currency['id']}},'delete')"  class="btn btn-xs btn-info red" title="{{ trans('admin/currencies.delete') }}">
												<i class="fa fa-trash-o"></i></a>
	                                        @endif
										</td>
                                    </tr>
                                @endforeach
                            </tbody>
                         </table>
                    </div>
                    
                    <div class="clearfix">
                        <p class="pull-left mt10 mr10">{{Form::select('action',$actions,'',array('class'=>'form-control bs-select input-medium', 'id'=>'currencies_action'))}}</p>
                        <p class="pull-left mt10"><input type="submit" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action"></p>
                    </div>
                 {{Form::close()}}
                 <!--- END: CURRENCIES LIST --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/currencies.no_currency_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminExchangeRateController@postAction'))) }}
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
		var cfg_package_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.each(function(){
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				});
			}
			else
			{
				checkboxes.each(function(){
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				});
			}
		});
		function doAction(id, selected_action)
		{
			if(selected_action == 'activate')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/currencies.confirm_active_currency') }}');
			}
			if(selected_action == 'deactivate')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/currencies.confirm_inactive_currency') }}');
			}
			if(selected_action == 'delete')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/currencies.confirm_delete_currency') }}');
			}
			$("#dialog-confirm").dialog({ title: cfg_package_name, modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#list_action').val(selected_action);
						$('#list_id').val(id);
						document.getElementById("actionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html("{{ trans('admin/currencies.select_atleast_one_currency') }}");
				error_found = true;
			}
			var selected_action = $('#currencies_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/currencies.please_select_action') }}');
				error_found = true;
			}
			if(!error_found)
			{
				if(selected_action == 'Delete')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/currencies.confirm_delete_currency') }}');
				}
				if(selected_action == 'Active')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/currencies.confirm_active_currency') }}');
				}
				if(selected_action == 'InActive')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/currencies.confirm_inactive_currency') }}');
				}
			}
			if(error_found)
			{
				$("#dialog-confirm").dialog({ title: cfg_package_name, modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title: cfg_package_name, modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#listFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		})

		$('.js-submit').click(function(){
			$('#addCurrencyfrm').submit();
		})
	</script>
@stop