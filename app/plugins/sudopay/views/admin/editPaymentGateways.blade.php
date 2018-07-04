@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
     <!-- BEGIN: INFO BLOCK -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{ Session::get('success_message') }}</div>
    @endif
    @if (Session::has('warning_message') && Session::get('warning_message') != "")
        <div class="note note-warning">{{ Session::get('warning_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
	<!-- END: INFO BLOCK -->

    {{ Form::model(Null, [
        'method' => 'post',
        'id' => 'payment_gateways_frm', 'class' => 'form-horizontal']) }}
    	{{ Form::hidden('type', $d_arr['type']) }}
        <div class="portlet blue-hoki box edit-paymt">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-dollar"><sup class="fa fa-pencil"></sup></i> {{ $d_arr['pageTitle'] }}</div>
            </div>
            <!-- END: PAGE TITLE -->

			@if($d_arr['type'] == 'sudopay')
				@if($sudopay_test == true || $sudopay_live == true)
                <div class="portlet-body fn_hide_block">
                    <div class="well">
                        <a href="javascript:void(0);" class="btn btn-sm btn-info mb20" id="sync_with_sudopay" onclick="syncWithSudopay()">{{ trans('sudopay::sudopay.sync_with_sudopay')}}</a>
                        <div class="dl-horizontal">
                        	<dl>
                                <dt>{{ trans('sudopay::sudopay.subscription_plan') }}</dt>
                                <dd>{{ $d_arr['plan_details']['subscription_plan'] }}</dd>
                            </dl>
                            <dl>
                                <dt>{{ trans('sudopay::sudopay.branding') }}</dt>
                                <dd>{{ $d_arr['plan_details']['branding'] }}</dd>
                            </dl>
                            <dl>
                                <dt>{{ trans('sudopay::sudopay.enabled_gateways')}}</dt>
                                <dd>
                                    @if(count($d_arr['enabled_gateways']) > 0)
                                        @foreach($d_arr['enabled_gateways'] as $gateway)
                                        	<dl class="dl-horizontal">
                                            	<dt>
                                                    <p>{{ $gateway['display_name'] }}</p>
                                                    <img alt="{{ $gateway['display_name'] }}" data-toggle="tooltip" title="{{ $gateway['display_name'] }}" src="{{ $gateway['thumb_url'] }}">
                                                </dt>
                                                <dd>
                                                    <p>{{ implode(', ',$gateway['supported_features'][0]['actions']) }}</p>
                                                    <p>{{ implode(', ',$gateway['supported_features'][0]['currencies']) }}</p>
                                                </dd>
                                            </dl>
                                        @endforeach
                                    @else
                                    	{{ trans('sudopay::sudopay.click_sync_with_sudopay')}}
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
				@endif
			@endif

            <!--  BEGIN: PAYMENT GATEWAYS TABLE -->
            <div class="portlet-body clearfix">
                @if($d_arr['type'] == 'sudopay')
                	<div class="chkbx-grp">
                        <div class="form-group {{{ $errors->has('sudopay_payment_test_mode') ? 'error' : '' }}}">
                            <label class="radio pull-left">
                                {{Form::checkbox('sudopay_payment_test_mode', '1', Config::get("plugin.sudopay_payment_test_mode"), array('class' => '', 'id' => 'sudopay_payment_test_mode')) }}
                            </label>
                            <div class="col-md-4">
                                {{ Form::label('sudopay_payment_test_mode', trans("sudopay::sudopay.test_mode"), array('class' => 'control-label')) }}
                                <label class="error">{{{ $errors->first('sudopay_payment_test_mode') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('sudopay_payment_used_addtowallet') ? 'error' : '' }}}">
                            <label class="radio pull-left">
                                {{Form::checkbox('sudopay_payment_used_addtowallet', '1', Config::get("plugin.sudopay_payment_used_addtowallet"), array('class' => '', 'id' => 'sudopay_payment_used_addtowallet')) }}
                            </label>
                            <div class="col-md-4">
                                {{ Form::label('sudopay_payment_used_addtowallet', trans("sudopay::sudopay.enable_for_add_wallet"), array('class' => 'control-label')) }}
                                <label class="error">{{{ $errors->first('sudopay_payment_used_addtowallet') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('sudopay_payment_used_product_purchase') ? 'error' : '' }}}">
                            <label class="radio pull-left">
                                {{Form::checkbox('sudopay_payment_used_product_purchase', '1', Config::get("plugin.sudopay_payment_used_product_purchase"), array('class' => '', 'id' => 'sudopay_payment_used_product_purchase')) }}
                            </label>
                            <div class="col-md-4">
                                {{ Form::label('sudopay_payment_used_product_purchase', trans("sudopay::sudopay.enable_for_product_purchase"), array('class' => 'control-label')) }}
                                <label class="error">{{{ $errors->first('sudopay_payment_used_product_purchase') }}}</label>
                            </div>
                        </div>
                    </div>

                    <table class="table api-log">
                        <thead>
                            <tr>
                              <th></th>
                              <th class="text-center">{{ trans('sudopay::sudopay.live_mode_credential')}}</th>
                              <th class="text-center">{{ trans('sudopay::sudopay.test_mode_credential')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>{{ trans('sudopay::sudopay.sudopay_merchant_id') }}</td>
                                <td>{{ Form::text('sudopay_live_merchant_id', Config::get("plugin.sudopay_live_merchant_id"), array ('class' => 'form-control')); }}</td>
                                <td>{{ Form::text('sudopay_test_merchant_id', Config::get("plugin.sudopay_test_merchant_id"), array ('class' => 'form-control')); }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('sudopay::sudopay.sudopay_website_id') }}</td>
                                <td>{{ Form::text('sudopay_live_website_id', Config::get("plugin.sudopay_live_website_id"), array ('class' => 'form-control')); }}</td>
                                <td>{{ Form::text('sudopay_test_website_id', Config::get("plugin.sudopay_test_website_id"), array ('class' => 'form-control')); }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('sudopay::sudopay.sudopay_secret_string') }}</td>
                                <td>{{ Form::text('sudopay_live_secret_string', Config::get("plugin.sudopay_live_secret_string"), array ('class' => 'form-control')); }}</td>
                                <td>{{ Form::text('sudopay_test_secret_string', Config::get("plugin.sudopay_test_secret_string"), array ('class' => 'form-control')); }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('sudopay::sudopay.sudopay_api_key') }}</td>
                                <td>{{ Form::text('sudopay_live_api_key', Config::get("plugin.sudopay_live_api_key"), array ('class' => 'form-control')); }}</td>
                                <td>{{ Form::text('sudopay_test_api_key', Config::get("plugin.sudopay_test_api_key"), array ('class' => 'form-control')); }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <button type="submit" name="edit_featured" class="btn btn-primary" id="" value="">
                                        <i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div class="form-group {{{ $errors->has('wallet_payment_used_product_purchase') ? 'error' : '' }}}">
                        {{ Form::label('wallet_payment_used_product_purchase', trans("sudopay::sudopay.enable_for_product_purchase"), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            <label class="radio">
                                {{Form::checkbox('wallet_payment_used_product_purchase', '1', Config::get("payment.wallet_payment_used_product_purchase"), array('class' => '')) }}
                            </label>
                            <label class="error">{{{ $errors->first('wallet_payment_used_product_purchase') }}}</label>
                        </div>
                    </div>

                    <button type="submit" name="edit_featured" class="btn btn-primary" id="" value="">
                        <i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                    </button>
                @endif
            </div>
            <!--  END: PAYMENT GATEWAYS TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        function syncWithSudopay() {
            postData = '';
            var actions_url = '{{ Url::to('admin/sudopay/synchronize-with-sudopay')}}';
            displayLoadingImage(true);
            $.post(actions_url, postData, function(data)
            {
	            if (data == 'success') {
					window.location.reload();
	            }
                hideLoadingImage (false);
            });
            return false;
        }

        $("#payment_gateways_frm").validate({
			rules: {
				sudopay_live_merchant_id: {required: {
						depends: function(element) {
						return (!$('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_live_website_id: {required: {
						depends: function(element) {
						return (!$('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_live_secret_string:{required: {
						depends: function(element) {
						return (!$('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_live_api_key: {required: {
						depends: function(element) {
						return (!$('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_test_merchant_id: {required: {
						depends: function(element) {
						return ($('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_test_website_id: {required: {
						depends: function(element) {
						return ($('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_test_secret_string:	{required: {
						depends: function(element) {
						return ($('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
				sudopay_test_api_key:{
						required: {
						depends: function(element) {
						return ($('#sudopay_payment_test_mode').is(':checked')) ? true : false;
						}
					}
				},
			},
			messages: {
				sudopay_live_merchant_id: {
					required: mes_required,
				},
				sudopay_live_website_id: {
					required: mes_required,
				},
				sudopay_live_secret_string: {
					required: mes_required,
				},
				sudopay_live_api_key: {
					required: mes_required,
				},
				sudopay_test_merchant_id: {
					required: mes_required,
				},
				sudopay_test_website_id: {
					required: mes_required,
				},
				sudopay_test_secret_string: {
					required: mes_required,
				},
				sudopay_test_api_key: {
					required: mes_required,
				},
			},
			/* For Contact info violation */
			submitHandler: function(form) {
				form.submit();
			}
		});

    </script>
@stop
