<?php
	$shipping_companies_list = array();
	$shipping_company_id = $shipping_fee = 0;
	$shipping_company_name = '';
	if(count($shipping_companies_details) > 0) {
		$shipping_companies_list = $shipping_companies_details['shipping_companies_list'];
		$shipping_company_id = $shipping_companies_details['shipping_company_id_selected'];
		$shipping_fee = $shipping_companies_details['shipping_company_fee_selected'];
		$shipping_company_name = $shipping_companies_details['shipping_company_name_selected'];
	}
?>
@if(count($shipping_companies_details) > 0)
	<?php $submit_disabled = 'disabled=disable';?>

    <div class="margin-bottom-10 table-new table-scrollable">
        <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
            <tbody class="fn_formBuilderListBody" id="shippingfee_tbl_body">
                @if(count($shipping_companies_list) > 0)
                    @foreach($shipping_companies_list as $companies_list)
                        <?php
                            $shipping_company_id = isset($shipping_company_id) ? $shipping_company_id : 0;
                            $checked = ($companies_list['company_id'] == $shipping_company_id) ? 'checked = "checked"' : '';
                            $disabled = (isset($companies_list['error_message']) && $companies_list['error_message']!='')?'disabled=disable':'';
                            if($disabled == '')
                                $submit_disabled = '';
                        ?>
                        <tr>
                            <td>
                                <label class="radio">
                                    <input type="radio" name="shipping_company_{{ $cart_id }}" id="shipping_company_{{ $item_id }}_{{ $companies_list['company_id'] }}" value="" {{ $checked }} {{$disabled}} >
                                    <label for="shipping_company_{{ $cart_id }}_{{ $companies_list['company_id'] }}">{{$companies_list['company_name']}}</label>
                                </label>
                            </td>
                            @if(isset($companies_list['error_message']) && $companies_list['error_message']!='')
                                <td colspan="2"><p class="alert alert-danger no-margin"><small>{{$companies_list['error_message']}}</small></p></td>
                            @else
                                <td>
                                    @if($companies_list['shipping_fee'] > 0)
                                        {{ CUtil::convertAmountToCurrency($companies_list['shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }}
                                    @else
                                        <p class="badge badge-primary margin-bottom-5">Free </p>
                                    @endif
                                </td>
                                <td>{{$companies_list['shipping_times']}}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="3"><p class="alert alert-danger no-margin">{{ trans('common.do_not_ship_to_country') }}</p></td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="clearfix">
        @if(count($shipping_companies_list) > 0)
            <button type="button" class="btn green btn-xs fnchangeShippingCompany" id="change_shipping_company_{{ $item_id }}_{{ $cart_id }}" {{$submit_disabled}}><i class="fa fa-check"></i> {{trans('common.ok')}}</button>
        @endif
        <a href="javascript:;" itemprop="url" class="fnShippingCompaniesClose">
            <button type="button" class="btn default btn-xs"><i class="fa fa-times"></i> {{trans('common.cancel')}}</button>
        </a>
    </div>
@else
	<p class="alert alert-danger no-margin">{{ trans('common.do_not_ship_to_country') }}</p>
@endif