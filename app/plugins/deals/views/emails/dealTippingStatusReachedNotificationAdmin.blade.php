@extends('mail')
@section('email_content')
	<div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('deals::deals.hi') }} Admin,</div>

	<div style="line-height:18px;">
		<p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
			{{ $tip_message }}
		</p>

		<!-- BEGIN: DEAL TIPPING REACHED NOTIFICATION -->
		<div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
			<p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{ Lang::get('deals::deals.details_of_the_deal') }}</p>
			<table width="98%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.deal_name') }} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a href="{{ $view_deal_link }}" title="{{ $deal_title }}" style="color:#15aadb; text-decoration:none;">{{ $deal_title }}</a>
						</p>
					</td>
				</tr>

				@if(isset($mail_for) && $mail_for != 'Buyer')
					<tr>
						<td width="150" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.order_details') }} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								<a href="{{ $view_sales_link }}" title="{{ Lang::get('deals::deals.view_sale') }}" style="color:#15aadb; text-decoration:none;">{{ Lang::get('deals::deals.click_here') }}</a>
							</p>
						</td>
					</tr>
				@endif

				@if(isset($mail_for) && isset($view_order_link) && $view_order_link != "")
					<tr>
						<td width="150" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.order_details') }} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								<a href="{{ $view_order_link }}" title="view sale" style="color:#15aadb; text-decoration:none;">{{ Lang::get('deals::deals.click_here') }}</a>
							</p>
						</td>
					</tr>
				@endif
			</table>

			<p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 13px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
				{{ Lang::get('deals::deals.for_more_details_about_this_deal') }}: <a href="{{ $view_deal_link }}" title="{{ Lang::get('deals::deals.view_deal') }}" style="color:#15aadb; text-decoration:none; font-weight:bold;">{{ Lang::get('deals::deals.view_deal') }}</a>
			</p>
		</div>

		@if(isset($order_txn_details) && COUNT($order_txn_details) > 0)
			<div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
				<p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{ Lang::get('deals::deals.details_of_transaction') }}</p>
				<table width="98%" cellspacing="0" cellpadding="0" style="border:1px solid #ddd; border-collapse:collapse;">
					<tr>
						<th width="150" valign="top" align="left" style="background-color:#efefef; border:1px solid #ddd; padding:5px; font:bold 13px Arial, Helvetica, sans-serif; color:#707070;">{{ Lang::get('deals::deals.reference_id') }}</th>
						<th width="150" valign="top" align="left" style="background-color:#efefef; border:1px solid #ddd; padding:5px; font:bold 13px Arial, Helvetica, sans-serif; color:#707070;">{{ Lang::get('deals::deals.amount') }}</th>
						<th width="150" valign="top" align="left" style="background-color:#efefef; border:1px solid #ddd; padding:5px; font:bold 13px Arial, Helvetica, sans-serif; color:#707070;">{{ Lang::get('deals::deals.transaction_method') }}</th>
						<th width="150" valign="top" align="left" style="background-color:#efefef; border:1px solid #ddd; padding:5px; font:bold 13px Arial, Helvetica, sans-serif; color:#707070;">{{ Lang::get('deals::deals.transaction_status') }}</th>
					</tr>
					@foreach($order_txn_details as $order)
						<tr>
							<td valign="top" align="left" style="border:1px solid #ddd; padding:5px; font:normal 13px Arial, Helvetica, sans-serif; color:#1a1a1a;">
								{{ $order['reference_id'] }}
							</td>
							<td valign="top" align="left" style="border:1px solid #ddd; padding:5px; font:normal 13px Arial, Helvetica, sans-serif; color:#1a1a1a;">
								{{ $order['txn_amount'] }}
							</td>
							<td valign="top" align="left" style="border:1px solid #ddd; padding:5px; font:normal 13px Arial, Helvetica, sans-serif; color:#1a1a1a;">
								{{ $order['txn_method'] }}
							</td>
							<td valign="top" align="left" style="border:1px solid #ddd; padding:5px; font:normal 13px Arial, Helvetica, sans-serif; color:#1a1a1a;">
								{{ $order['txn_status'] }}
							</td>
						</tr>
					@endforeach
				</table>
			</div>
		@endif
		<!-- END: DEAL TIPPING REACHED NOTIFICATION -->
	</div>
@stop