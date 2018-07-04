@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{$seller_details['display_name']}},</div>

	<div style="line-height:18px;">
		<!-- BEGIN: BUYER ORDER DETAILS -->
		@if(isset($order_details) && count($order_details) > 0)
			<p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.order_details')}} :</p>
			<div style="margin-bottom:35px; line-height:18px; padding:10px 20px; background:#f9f9f9;">
				<table width="98%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_id')}} :</p>
						</td>
						<td align="left" valign="top">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								<a target="_blank" style="text-decoration:none; color:#15aadb;" href="{{ URL::action('PurchasesController@getSalesOrderDetails', $order_details->id) }}" title="{{ Lang::get('myPurchases.view')  }}">
									{{ CUtil::setOrderCode($order_details->id); }}
								</a>
							</p>
						</td>
					</tr>

                    @if(isset($order_details) && $order_details->coupon_code!='')
						<tr>
                            <td width="200" valign="top" align="left">
                                <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-{{trans('mail.order_details')}}serif;">{{trans('mail.coupon_code')}} :</p>
                            </td>
                            <td valign="top" align="left">
                                <p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $order_details->coupon_code }}</p>
                            </td>
                        </tr>
                    @endif

					@if(isset($order_details) && $order_details->discount_amount > 0)

                        <tr>
                            <td width="200" valign="top" align="left">
                                <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-{{trans('mail.order_details')}}serif;">{{trans('mail.sub_total')}} :</p>
                            </td>
                            <td valign="top" align="left">
                                <p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									{{ $order_details->currency.' '. $order_details->sub_total }}
								</p>
                            </td>
                        </tr>

                        <tr>
                            <td width="200" valign="top" align="left">
                                <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_details')}}{{trans('mail.discount')}} :</p>
                            </td>
                            <td valign="top" align="left">
                                <p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									{{ $order_details->currency.' '. $order_details->discount_amount }}
								</p>
                            </td>
                        </tr>
                    @endif

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.total_amount')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $order_details->currency.' '. $invoices['amount'] }}</p>
						</td>
					</tr>

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.your_amount')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $order_details->currency}} {{$order_details->total_amount-$order_details->site_commission}}</p>
						</td>
					</tr>

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.site_commission')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $order_details->currency.' '. $order_details->site_commission }}</p>
						</td>
					</tr>

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_status')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								@if(isset($order_details) && isset($order_details->order_status) && $order_details->order_status!='')
									@if(Lang::has('myPurchases.status_txt_'.$order_details->order_status))
										{{ trans('myPurchases.status_txt_'.$order_details->order_status) }}
									@else
										{{$invoices['status']}}
									@endif
								@else
									{{$invoices['status']}}
								@endif
							</p>
						</td>
					</tr>

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.payment_gateway')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{$invoices['payment_gateway_text']}}</p>
						</td>
					</tr>

					@if($invoices['payment_gateway'] == 'paypal')
						<tr>
							<td width="200" valign="top" align="left">
								<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount_paid_in_paypal')}} :</p>
							</td>
							<td valign="top" align="left">
								<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									{{ $invoices['paypal_amount'] }}
								</p>
							</td>
						</tr>
					@elseif ($invoices['payment_gateway'] == 'wallet')
						<tr>
							<td width="200" valign="top" align="left">
								<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount_paid_from_wallet')}} :</p>
							</td>
							<td valign="top" align="left">
								<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									{{ $invoices['paypal_amount'] }}
								</p>
							</td>
						</tr>
					@else
						<tr>
							<td width="200" valign="top" align="left">
								<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount_paid_in_paypal')}} :</p>
							</td>
							<td valign="top" align="left">
								<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									{{ $invoices['paypal_amount'] }}
								</p>
							</td>
						</tr>

						<tr>
							<td width="200" valign="top" align="left">
								<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount_paid_from_wallet')}} :</p>
							</td>
							<td valign="top" align="left">
								<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
									{{ $invoices['wallet_credit_used']}}
								</p>
							</td>
						</tr>
					@endif

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.buyer')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								<a target="_blank" href="{{ $buyer_details['profile_url']}}" style="text-decoration:none; color:#15aadb;">{{ $buyer_details['display_name']}}</a>
							</p>
						</td>
					</tr>

					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_date')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ CUtil::FMTDate($invoices['date_added'], "Y-m-d H:i:s", "") }}</p>
						</td>
					</tr>
				</table>
			</div>
		@endif
		<!-- END: BUYER ORDER DETAILS -->

		<!-- BEGIN: BUYER INVOICE DETAILS -->
			<p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.invoice_details')}} :</p>
			<table width="98%" cellspacing="0" cellpadding="0" border="0"><tr><td>
				@if(count($order_details) > 0)
					@if($order_details->has_invoice)
						@if(count($order_details['order_invoices']) > 0)
							@foreach($order_details['order_invoices'] as $invoice_detail)
							<div style="margin-bottom:15px; line-height:18px; padding:10px 20px; background:#f9f9f9;">
                            <table width="98%" cellspacing="0" cellpadding="0" border="0">
								<?php $product = $invoice_detail['product_details'];?>
								<tr>
									<td width="200" valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_name')}} :</p>
									</td>
									<td valign="top" align="left">
										<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
											@if(!empty($product))
												@if($product['product_status'] != 'Deleted')
													<a href="{{$product['view_url']}}" style="text-decoration:none; color:#15aadb;" >{{{ nl2br($product['product_name'])}}}</a>
												@else
													{{{ nl2br($product['product_name']) }}}
												@endif
											@else
												{{"Product deleted"}}
											@endif
										</p>
									</td>
								</tr>
								@if($product['is_downloadable_product'] == 'Yes')
									<?php
										if(isset($product['resource_arr'][0]['download_filename'])  && $product['resource_arr'][0]['download_filename']!='')
											$download_filename = $product['resource_arr'][0]['download_filename'];
										else
											$download_filename = "Download";
									?>
									<tr>
										<td width="200" valign="top" align="left">
											<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.download_file')}} :</p>
										</td>
										<td valign="top" align="left">
											<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
												<a href="{{$product['download_url']}}" style="text-decoration:none; color:#15aadb;" >{{ $download_filename }}</a>
											</p>
										</td>
									</tr>
								@endif
								<tr>
									<td width="200" valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_amount')}} :</p>
									</td>
									<td valign="top" align="left">
										<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
											{{ CUtil::convertAmountToCurrency($invoice_detail['item_amount'], Config::get('generalConfig.site_default_currency'), $order_details->currency, true) }}
										</p>
									</td>
								</tr>

								<tr>
									<td width="200" valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.quantity')}} :</p>
									</td>
									<td valign="top" align="left">
										<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
											{{ $invoice_detail['item_qty'] }}
										</p>
									</td>
								</tr>

								<tr>
									<td width="200" valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.shipping_fee')}} :</p>
									</td>
									<td valign="top" align="left">
										<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
											{{ CUtil::convertAmountToCurrency($invoice_detail['shipping_fee'], Config::get('generalConfig.site_default_currency'), $order_details->currency, true) }}
										</p>
									</td>
								</tr>

								@if(isset($invoice_detail['total_tax_amount']) && $invoice_detail['total_tax_amount'] >0)
									<tr>
										<td width="200" valign="top" align="left">
											<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.tax_fee')}} :</p>
										</td>
										<td valign="top" align="left">
											<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
												{{ CUtil::convertAmountToCurrency($invoice_detail['total_tax_amount'], Config::get('generalConfig.site_default_currency'), $order_details->currency, true) }}
											</p>
										</td>
									</tr>
								@endif

                                @if(isset($invoice_detail['variation_data']) && COUNT($invoice_detail['variation_data']) > 0)
									 @if(isset($invoice_detail['giftwrap_price']) && $invoice_detail['giftwrap_price'] !=0)
		                                	<tr>
		                                        <td width="200" valign="top" align="left">
		                                        	<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ "GiftWarp Price" }}:</p>
		                                         </td>
		                                        <td valign="top" align="left">
		                                        	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoice_detail['giftwrap_price'] }}</p>
		                                        </td>
		                                     </tr>
										 @endif
		                                 @if(isset($invoice_detail['giftwrap_msg']) && trim($invoice_detail['giftwrap_msg']) !='')
		                                     <tr>
		                                        <td width="200" valign="top" align="left">
		                                        	<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ "GiftWarp Msg" }}:</p>
		                                         </td>
		                                        <td valign="top" align="left">
		                                        	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoice_detail['giftwrap_msg'] }}</p>
		                                        </td>
		                                     </tr>
		                                 @endif
                                    @if(isset($invoice_detail['variation_data']['attrirb_det']) && COUNT($invoice_detail['variation_data']['attrirb_det']) > 0)
                                        @foreach($invoice_detail['variation_data']['attrirb_det'] AS $attr)
                                        <tr>
                                            <td width="200" valign="top" align="left">
                                            	<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ $attr['name'] }}:</p>
                                             </td>
                                            <td valign="top" align="left">
                                            	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $attr['label'] }}</p>
                                            </td>
                                         </tr>
                                        @endforeach
                                    @endif

                                @endif

								@if(isset($invoice_detail['deal_data']) && COUNT($invoice_detail['deal_data']) > 0 && isset($invoice_detail['deal_data']['discount_percentage']))
									 <tr>
										<td width="200" valign="top" align="left">
											<p style="padding:0; margin:0 0 4px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.deal_disoucnt')}} :</p>
										</td>
										<td valign="top" align="left">
											<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
												<strong>{{ $invoice_detail['deal_data']['discount_percentage'] }}</strong>%
												<span style="color:#999;">(</span><a style="font:bold 12px 'trebuchet MS'; color:#15aadb; text-decoration:none;" href="{{ $invoice_detail['deal_data']['viewDealLink'] }}">View deal</a><span style="color:#999;">)</span>
											</p>
										</td>
									</tr>
                                    @if($invoice_detail['deal_data']['deal_tipping_status_lbl'] != '')
                                        <tr>
                                            <td width="200" valign="top" align="left">
                                                <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.tipping_status')}} :</p>
                                            </td>
                                            <td valign="top" align="left">
                                            	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                                                	{{ $invoice_detail['deal_data']['deal_tipping_status_lbl'] }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($invoice_detail['deal_data']['tipping_note_msg'] != '')
                                        <tr>
                                            <td width="200" valign="top" align="left">
                                                <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;"></p>
                                            </td>
                                            <td valign="top" align="left">
                                            	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                                                	({{ $invoice_detail['deal_data']['tipping_note_msg'] }})
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
								@endif

								<tr>
									<td width="200" valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.total_amount')}} :</p>
									</td>
									<td valign="top" align="left">
										<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
											@if($order_details->has_invoice)
												{{ CUtil::convertAmountToCurrency($invoice_detail['item_total_amount'], Config::get('generalConfig.site_default_currency'), $order_details->currency, true) }}
											@else
												{{ CUtil::convertAmountToCurrency($invoice_detail['total_amount'], Config::get('generalConfig.site_default_currency'), $order_details->currency, true) }}
											@endif
										</p>
									</td>
								</tr>
                            </table>
							</div>
							@endforeach
						@endif
					@endif
				@endif
			</td></tr></table>
		<!-- BEGIN: BUYER INVOICE DETAILS -->
	</div>
@stop