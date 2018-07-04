<?php
	$shipping_companies_list = array();
	$shipping_company_id = $shipping_fee = 0;
	$shipping_company_name = '';
	$selected_company_has_err = '';
	if(count($shipping_companies_details) > 0) {
		$shipping_companies_list = $shipping_companies_details['shipping_companies_list'];
		$shipping_company_id = $shipping_companies_details['shipping_company_id_selected'];
		$shipping_fee = $shipping_companies_details['shipping_company_fee_selected'];
		if(isset($allow_variation) && $allow_variation) {
			if(isset($shipping_companies_details['shipping_company_fee_selected']) && $shipping_companies_details['shipping_company_fee_selected'] !== '' && isset($variation_shipping_price)) {
				$shipping_fee = $shipping_companies_details['shipping_company_fee_selected'] + $variation_shipping_price; 
				if(!isset($shipping_fee)) { 
	                $shipping_fee = 0;
	            }
			}
		}
		$shipping_company_name = $shipping_companies_details['shipping_company_name_selected'];
	}
?>
<?php $show_currency = CUtil::getCheckDefaultCurrencyActivate();	?>
@if(count($shipping_companies_details) > 0)
	<div class="margin-bottom-10 pos-relative shipping-dropdown">
		@if($shipping_company_name != '')
			<a class="fnShippingCompaniesOpener btn purple-plum btn-xs" id="shipping_companies_opener_{{ $item_id }}">
				<i class="fa fa-chevron-down"></i>
				<span>{{ $shipping_company_name }}</span>
			</a>
		@else
			<p class="alert alert-danger no-margin">{{ trans('common.do_not_ship_to_country') }}</p>
		@endif
		<?php $submit_disabled = 'disabled=disable';?>
        <div class="fnShippingCompanies shipping-submenu clearfix" id="shipping_companies_{{ $item_id }}" style="display:none;">
        	<div class="margin-bottom-10 table-new">
                <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
                    <tbody class="fn_formBuilderListBody" id="shippingfee_tbl_body">
                        @if(count($shipping_companies_list) > 0)
                            @foreach($shipping_companies_list as $companies_list)
                                <?php
                                	$error_msg = (isset($companies_list['error_message']) && $companies_list['error_message']!='') ? $companies_list['error_message'] : '';
                                    $shipping_company_id = isset($shipping_company_id) ? $shipping_company_id : 0;
                                    $checked = ($companies_list['company_id'] == $shipping_company_id) ? 'checked = "checked"' : '';
                                    $disabled = ($error_msg != '') ? 'disabled=disable' : '';
									if(isset($allow_variation) && $allow_variation) {
										if($companies_list['shipping_fee'] > 0 && isset($variation_shipping_price) && $variation_shipping_price > 0) {
											$companies_list['shipping_fee'] = $companies_list['shipping_fee'] + $variation_shipping_price;
											if(!isset($companies_list['shipping_fee'])) {
								                $companies_list['shipping_fee'] = 0;
								            }
										}
									}
                                    if($disabled == '')
										$submit_disabled = '';
									if($checked != '' && $disabled != '') {//current company_id eq selected company id
										$selected_company_has_err = $error_msg;
									}
                                ?>
                                <tr>
                                    <td>
                                        <label class="radio-inline">
                                            <input type="radio" name="shipping_company_{{ $cart_id }}" id="shipping_company_{{ $item_id }}_{{ $companies_list['company_id'] }}" value="" {{ $checked }} {{$disabled}} >
                                            <label for="shipping_company_{{ $cart_id }}_{{ $companies_list['company_id'] }}">{{$companies_list['company_name']}}</label>
                                        </label>
                                    </td>
                                    @if(isset($companies_list['error_message']) && $companies_list['error_message']!='')
										<td colspan="2"><p class="alert alert-danger"><small>{{$companies_list['error_message']}}</small></p></td>
									@else
                                    	<td>
											@if($companies_list['shipping_fee'] > 0)
												{{ CUtil::convertAmountToCurrency($companies_list['shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }}
												@if($show_currency)
													<p>{{ CUtil::convertAmountToCurrency($companies_list['shipping_fee'], Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</p>
												@endif
											@else
												<p class="badge badge-primary">Free</p>
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
                    <button type="button" class="btn default btn-xs"><i class="fa fa-times"></i>  {{trans('common.cancel')}}</button>
                </a>
            </div>
        </div>
	</div>
	@if(count($shipping_companies_list) > 0 && $shipping_company_name != '')
	    @if(isset($shipping_fee) && $shipping_fee != '0')
	        <p><span id="shipping_price_{{ $item_id }}">{{ CUtil::convertAmountToCurrency($shipping_fee, Config::get('generalConfig.site_default_currency'), '', true) }}</span></p>
	        @if($show_currency)
	        	<p><span id="shipping_price_with_currency_{{ $item_id }}">{{ CUtil::convertAmountToCurrency($shipping_fee, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }}</span></p>
	        @endif
	    @elseif($selected_company_has_err != '')
	    	<div class="alert alert-danger no-margin">{{ $selected_company_has_err }}</div>
		@else
	        <strong class="badge badge-primary">{{ trans('common.free') }}</strong>
	    @endif
    @endif
@else
	<p class="alert alert-danger no-margin">{{ trans('common.do_not_ship_to_country') }}</p>
@endif