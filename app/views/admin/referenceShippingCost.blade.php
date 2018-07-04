<div class="table-responsive margin-bottom-30">
    <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
        <thead>
            <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                <th class="col-lg-3">{{trans('product.shipping_company')}}</th>
                <th class="col-md-3">{{trans('product.shipping_type')}}</th>
                <th class="col-md-3">{{trans('product.cost')}}</th>
                <th class="col-lg-3">{{trans('product.shipping_time')}}</th>
            </tr>
        </thead>
        <tbody class="fn_formBuilderListBody" id="shippingfee_tbl_body">
            @if(count($d_arr['shipping_companies_list']) > 0)
            	@foreach($d_arr['shipping_companies_list'] as $companies_list)
	                <tr id="itemShippingRow_{{$companies_list['id']}}">
	                    <td>{{$companies_list['company_name']}}</td>
	                    <td>
                        	<?php
								if(count($companies_list) > 0)
								{
									if($companies_list['shipping_type'] == 'Standard') {
										$lbl_class = "badge-info";
									}
									elseif($companies_list['shipping_type'] == 'Free') {
										$lbl_class = "badge-primary";
									}
									elseif($companies_list['shipping_type'] == 'Custom') {
										$lbl_class = "badge-grey";
									}
								}
							?>
                        	<span class="badge {{ $lbl_class }}">{{$companies_list['shipping_type']}}</span>
                        </td>
	                    @if(isset($companies_list['error_message']) && $companies_list['error_message']!='')
                            <td colspan="2"><p class="text-danger">{{$companies_list['error_message']}}</p></td>
                        @else
                            <td>
								@if($companies_list['shipping_fee'] > 0)
									{{ CUtil::convertAmountToCurrency($companies_list['shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }}
								@else
									<span class="badge badge-primary">{{trans('common.free')}}</span>
								@endif
							</td>
                            <td>{{$companies_list['shipping_times']}}</td>
                        @endif
	                </tr>
                @endforeach
            @else
            <tr><td colspan="4"><p class="alert alert-info">{{trans('myPurchases.no_shipping_companies')}}</p></td></tr>
            @endif
        </tbody>
    </table>
</div>