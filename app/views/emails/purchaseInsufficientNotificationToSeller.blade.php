@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{ $receiver_details['seller_details']['display_name']}},</div>

    <div style="line-height:18px;">
        @if(isset($invoices) && count($invoices) > 0)
        	<!-- BEGIN: ALERT BLOCK -->
            <p style="background:#f2dede; border:1px solid #ebccd1; border-radius:4px; color:#a94442; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
                {{ $receiver_details['buyer_details']['display_name']}} {{trans('mail.dont_have_wallet_bal_credit_payment')}}
                <br />
                {{trans('mail.amount_credited_buyer_wallet')}}
				<br>
				{{trans('mail.order_details_below')}}
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
						<td align="left" valign="top"><p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a target="_blank" href="{{ URL::action('PurchasesController@getOrderDetails', $invoices['reference_id']) }}" title="{{ Lang::get('myPurchases.view')  }}">
							{{ CUtil::setOrderCode($invoices['reference_id']) }}</a></p>
						</td>
					</tr>
				
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.status')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.transacion_canelled')}}</p>
						</td>
					</tr>
				
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.paid_by')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								<a target="_blank" href="{{ $receiver_details['buyer_details']['profile_url']}}">{{ $receiver_details['buyer_details']['display_name']}}</a>
							</p>
						</td>
					</tr>
				
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.total_amount_for_purchase')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoices['currency'] }} {{ $invoices['amount'] }}</p>
						</td>
					</tr>
				
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.seller_amount')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $invoices['currency'] }} {{ $receiver_details['seller_amount'] }}</p>
						</td>
					</tr>
				
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.site_commission')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $invoices['currency'] }} {{ $receiver_details['admin_amount'] }}</p>
						</td>
					</tr>
				
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.wallet_credits_to_pay')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoices['currency'] }} {{ $invoices['amount'] }}</p>
						</td>
					</tr>
				</table>
            </div>
            <!-- END: SELLER INVOICE DETAILS -->
        @endif
    </div>
@stop
