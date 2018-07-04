@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{ $first_name }},</div>
	<div style="line-height:18px;">
		@if(isset($shop_order_details) && count($shop_order_details) > 0)
			<!-- BEGIN: ALERT BLOCK -->
			<p style="background:#f2dede; border:1px solid #ebccd1; border-radius:4px; color:#a94442; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
				{{trans('mail.product_shipping_details')}}
			</p>
			<!-- END: ALERT BLOCK -->

			<!-- BEGIN: SELLER INVOICE DETAILS -->
			<div style="line-height:18px; padding:10px 20px; background:#fafafa;">
				<p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.order_details')}} :</p>
				<table width="98%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_id')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a target="blank" href="{{ $order_view_url }}" title="View Order Details">{{ CUtil::setOrderCode($order_id) }}</a></p>
						</td>
					</tr>
				</table>

				<p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.shipped_details')}} :</p>
				<table width="98%" cellspacing="0" cellpadding="0" border="0">
					@foreach($shop_order_details as $shop_order)
						<?php
							$prod_obj = Products::initialize($shop_order->item_id);
							$prod_obj->setIncludeBlockedUserProducts(true);
							$prod_obj->setIncludeDeleted(true);
							$product_details = $prod_obj->getProductDetails();
							$p_service = new ProductService;
							$product_view_url = $p_service->getProductViewURL($shop_order->item_id, $product_details);
						?>
						@if(isset($product_details['is_downloadable_product']) && $product_details['is_downloadable_product']=='No')
							<tr>
								<td width="200" valign="top" align="left">
									<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_name')}} :</p>
								</td>
								<td valign="top" align="left">
									<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									<a target="blank" href="{{ $product_view_url }}" title="View Details">{{ $product_details['product_name'] }}</a></p>
								</td>
							</tr>

							<tr>
								<td width="200" valign="top" align="left">
								<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.tracking_id')}}</p>
								</td>
								<td valign="top" align="left">
									<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $shop_order->shipping_tracking_id }}</p>
								</td>
							</tr>
                            
							@if($shop_order->shipping_serial_number!='')
								<tr>
									<td width="200" valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.serial_number')}}:</p>
									</td>
									<td valign="top" align="left">
										<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $shop_order->shipping_serial_number }}</p>
									</td>
								</tr>
							@endif

							<?php
								$shipping_temp = new ShippingTemplateService;
								$company_name = $shipping_temp->getShippingTemplateCompanyName($shop_order->shipping_company_name);
							?>

							<tr>
								<td width="200" valign="top" align="left">
									<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.shipping_company')}}:</p>
								</td>
								<td valign="top" align="left">
									<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $company_name }}</p>
								</td>
							</tr>

							<tr>
								<td width="200" valign="top" align="left">
									<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.shipping_qty')}}:</p>
								</td>
								<td valign="top" align="left">
									<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $shop_order->item_qty }}</p>
								</td>
							</tr>
                            
							<tr>
		                        <td width="200" valign="top" align="left">
		                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.shipping_date')}}:</p>
		                    	</td>
		                    	<td valign="top" align="left">
									<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
										{{ CUtil::FMTDate($shop_order->shipping_date, "Y-m-d H:i:s", "") }}
									</p>
								</td>
		                    </tr>
                            
		                    <tr>
			                	<td colspan="2">&nbsp;</td>
			                </tr>
	                    @endif
					@endforeach
				</table>
			</div>
			<!-- END: SELLER INVOICE DETAILS -->
		@endif
	</div>
@stop
