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
        'method' => 'get',
        'id' => 'payment_gateways_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box ecomm-paymtgtwy">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-dollar sudo-icon"></i> {{ trans('sudopay::sudopay.payment_gateways') }}</div>
            </div>
            <!-- END: PAGE TITLE -->
            
			<div class="portlet-body clearfix">
				<p class="alert alert-info">{{ trans('sudopay::sudopay.payment_info')}}</p>
                <p class="alert alert-info">{{ trans('sudopay::sudopay.read_warning_carefully')}}</p>
                
                <!-- BEGIN: PAYMENT GATEWAYS TABLE -->
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <thead>
                                <tr>
                                  <th rowspan="3" width="50" class="text-center">{{ trans('sudopay::sudopay.actions')}}</th>
                                  <th rowspan="3" class="text-center">{{ trans('sudopay::sudopay.display_name')}}</th>
                                  <th colspan="3" class="text-center">{{ trans('sudopay::sudopay.settings') }}</th>
                                </tr>
                                <tr>
                                  <th rowspan="2" class="text-center">{{ trans('sudopay::sudopay.active')}}</th>
                                  <th colspan="2" class="text-center">{{ trans('sudopay::sudopay.where_to_use')}}</th>
                                </tr>
                                <tr>
                                  <th class="text-center">{{ trans('sudopay::sudopay.add_to_wallet')}}</th>
                                  <th class="text-center">{{ trans('sudopay::sudopay.product_order')}}</th>
                                </tr>
                            </thead>
                        </thead>
    
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <div class="dropdown pull-center">
                                        <a data-toggle="dropdown" class="dropdown-toggle" type="button">
                                            <i class="fa fa-gear"></i>
                                        </a>
                                        <ul role="menu" class="dropdown-menu dropdown-menu-left">
                                            <li>
                                                <a href="{{ URL::to('admin/sudopay/edit-payment-gateways').'?type=sudopay' }}"><i class="fa fa-angle-right"></i> {{ trans('sudopay::sudopay.edit')}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <p>{{ trans('sudopay::sudopay.sudopay')}}</p>
                                    <p class="paymt-disp"><i class="fa fa-exclamation-circle mr5"></i> {{ trans('sudopay::sudopay.payment_through_sudopay')}}</p>
                                </td>
                                <td class="status-btn text-center">
                                    @if(Config::get('plugin.sudopay_payment') == '1')
                                        <a href="{{ URL::to('admin/sudopay/change-status').'?action=Inactive&config_var=sudopay_payment' }}" class="fn_dialog_confirm btn red btn-xs" action="Inactive" title="{{ Lang::get('sudopay::sudopay.deactivate') }}"><i class="fa fa-ban"></i></a>
                                    @else
                                        <a href="{{ URL::to('admin/sudopay/change-status').'?action=Active&config_var=sudopay_payment' }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Active" title="{{ Lang::get('sudopay::sudopay.activate') }}"><i class="fa fa-check"></i></a>
                                    @endif
                                </td>
                                <td class="status-btn text-center">
                                    @if(Config::get('plugin.sudopay_payment_used_addtowallet') == '1')
                                        <a href="{{ URL::to('admin/sudopay/change-status').'?action=Inactive&config_var=sudopay_payment_used_addtowallet' }}" class="fn_dialog_confirm btn red btn-xs" action="Inactive" title="{{ Lang::get('sudopay::sudopay.deactivate') }}"><i class="fa fa-ban"></i></a>
                                    @else
                                        <a href="{{ URL::to('admin/sudopay/change-status').'?action=Active&config_var=sudopay_payment_used_addtowallet' }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Active" title="{{ Lang::get('sudopay::sudopay.activate') }}"><i class="fa fa-check"></i></a>
                                    @endif
                                </td>
                                <td class="status-btn text-center">
                                    @if(Config::get('plugin.sudopay_payment_used_product_purchase') == '1')
                                        <a href="{{ URL::to('admin/sudopay/change-status').'?action=Inactive&config_var=sudopay_payment_used_product_purchase' }}" class="fn_dialog_confirm btn red btn-xs" action="Inactive" title="{{ Lang::get('sudopay::sudopay.deactivate') }}"><i class="fa fa-ban"></i></a>
                                    @else
                                        <a href="{{ URL::to('admin/sudopay/change-status').'?action=Active&config_var=sudopay_payment_used_product_purchase' }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Active" title="{{ Lang::get('sudopay::sudopay.activate') }}"><i class="fa fa-check"></i></a>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="dropdown pull-center">
                                        <a data-toggle="dropdown" class="dropdown-toggle" type="button">
                                            <i class="fa fa-gear"></i>
                                        </a>
                                        <ul role="menu" class="dropdown-menu dropdown-menu-left">
                                            <li>
                                                <a href="{{ URL::to('admin/sudopay/edit-payment-gateways').'?type=wallet' }}"><i class="fa fa-angle-right"></i> {{ trans('sudopay::sudopay.edit')}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    {{ trans('sudopay::sudopay.wallet')}}
                                    <p class="paymt-disp"><i class="fa fa-exclamation-circle mr5"></i> {{ trans('sudopay::sudopay.wallet_option_for_purchase')}}</p>
                                </td>
                                <td class="status-btn text-center">
                                    @if(Config::get('payment.wallet_payment') == '1')
                                        <a href="{{ URL::to('admin/sudopay/change-status-payment').'?action=Inactive&config_var=wallet_payment' }}" class="fn_dialog_confirm btn red btn-xs" action="Inactive" title="{{ Lang::get('sudopay::sudopay.deactivate') }}"><i class="fa fa-ban"></i></a>
                                    @else
                                        <a href="{{ URL::to('admin/sudopay/change-status-payment').'?action=Active&config_var=wallet_payment' }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Active" title="{{ Lang::get('sudopay::sudopay.activate') }}"><i class="fa fa-check"></i></a>
                                    @endif
                                </td>
                                <td class="status-btn text-center">
                                    -
                                </td>
                                <td class="status-btn text-center">
                                    @if(Config::get('payment.wallet_payment_used_product_purchase') == '1')
                                        <a href="{{ URL::to('admin/sudopay/change-status-payment').'?action=Inactive&config_var=wallet_payment_used_product_purchase' }}" class="fn_dialog_confirm btn red btn-xs" action="Inactive" title="{{ Lang::get('sudopay::sudopay.deactivate') }}"><i class="fa fa-ban"></i></a>
                                    @else
                                        <a href="{{ URL::to('admin/sudopay/change-status-payment').'?action=Active&config_var=wallet_payment_used_product_purchase' }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Active" title="{{ Lang::get('sudopay::sudopay.activate') }}"><i class="fa fa-check"></i></a>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- END: PAYMENT GATEWAYS TABLE -->
            </div>
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(".fn_dialog_confirm").click(function(){
			var atag_href = $(this).attr("href");
			var action = $(this).attr("action");
			var cmsg = "";
			switch(action){
				case "Active":
					cmsg = "{{ Lang::get('sudopay::sudopay.activate_confirm') }}";
					break;

				case "Inactive":
					cmsg = "{{ Lang::get('sudopay::sudopay.deactivate_confirm') }}";
					break;
			}
			bootbox.dialog({
				message: cmsg,
				title: cfg_site_name,
				buttons: {
					danger: {
						label: "{{ trans('common.ok')}}",
						className: "btn-danger",
						callback: function() {
							Redirect2URL(atag_href);
							bootbox.hideAll();
						}
					},
					success: {
						label: "Cancel",
						className: "btn-default",
					}
				}
			});
			return false;
		});
    </script>
@stop