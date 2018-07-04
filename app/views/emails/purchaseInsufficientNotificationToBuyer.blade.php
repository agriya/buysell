@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{ $receiver_details['buyer_details']['display_name']}},</div>
	<div style="line-height:18px;">
		@if(isset($invoices) && count($invoices) > 0)
			<!-- BEGIN: ALERT BLOCK -->
			<div style="background:#f2dede; border:1px solid #ebccd1; border-radius:4px; color:#a94442; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
				<p  style="margin:0 0 8px 0;">{{trans('mail.you_does_not_have_sufficient_balance')}} </p>
				<p style="margin:0 0 8px 0;">{{trans('mail.paid_amount_credited_to_wallet')}}</p>
				<p style="margin:0 0 8px 0;">{{trans('mail.you_cane')}} <a href="{{ URL::to('users/my-withdrawals/withdrawals', array('transfer_thru' => 'paypal')).'?pay_to_details='.$receiver_details['buyer_paypal_email'] }}">{{trans('mail.withdraw')}}</a> {{trans('mail.your_amount_from_site')}} .</p>
				<p style="margin:0 0 8px 0;">{{trans('mail.order_details_below')}}</p>
			</div>
			<!-- END: ALERT BLOCK -->
			
			<!-- BEGIN: BUYER INVOICE DETAILS -->
			<div style="line-height:18px; padding:10px 20px; background:#fafafa;">
				<p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.order_details')}} :</p>
				<table width="98%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_id')}} :</p>
						</td>
							<td align="left" valign="top">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
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
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.total_amount_for_purchase')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoices['currency'] }} {{ $invoices['amount'] }}</p>
						</td>
					</tr>
					
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.credits_added_to_wallet')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $invoices['currency'] }} {{ $invoices['paypal_amount'] }}</p>
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
					
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.credits_avail_while_payment')}} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $invoices['currency'] }} {{ $receiver_details['buyer_available_balance'] }}</p>
						</td>
					</tr>
					
					<tr>
						<td width="200" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount_paid_from_paypal_account')}}  :</p>
						</td>
						<td valign="top" align="left"><p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;"></p></td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="80%">
								<tr>
									<td width="50%">{{trans('mail.paypal_email')}}</th>
									<td width="50%">{{trans('mail.amount')}}</th>
								</tr>
								<tr>
									<td>{{ $receiver_details['buyer_paypal_email'] }}</td>
									<td>{{ $invoices['currency'] }} {{ $invoices['amount'] - $invoices['wallet_credit_used']}}</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table>
			</div>
			<!-- END: BUYER INVOICE DETAILS -->
		@endif
	</div>
@stop
