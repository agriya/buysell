@extends('mail')
@section('email_content')
	<div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('deals::deals.dear') }} {{ $user_name }}, </div>
	<div style="line-height:18px;">
		<p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
			{{ Lang::get('deals::deals.your_request_for_set_as_featured_deal_has_been_rejected_by_admin') }}
		</p>

		<div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
			<p style="margin:10px 0 20px 0; padding:0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{ Lang::get('deals::deals.admin_comment_lbl') }}</p>
			<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ $comment }}</p>
		</div>

		<!-- BEGIN: DEAL FEATURED REQUEST DISAPPROVED -->
		<div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
			<p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{ Lang::get('deals::deals.details_of_the_deal') }}</p>
			<table width="98%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.deal_id_label') }} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a  href="{{ $viewDealLink }}" title="{{ $deal_id }}" style="color:#15aadb; text-decoration:none;">{{ $deal_id }}</a>
						</p>
					</td>
				</tr>

				@if(isset($deal_title))
					<tr>
						<td width="150" valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.deal_title') }} :</p>
						</td>
						<td valign="top" align="left">
							<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
								<a  href="{{ $viewDealLink }}" title="{{ $deal_title }}">{{ $deal_title }}</a>
							</p>
						</td>
					</tr>
				@endif
			</table>

			<p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 13px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
				{{ Lang::get('deals::deals.for_more_details_about_this_deal') }}: <a href="{{ $viewDealLink }}" title="{{ Lang::get('deals::deals.view_deal') }}" style="color:#15aadb; text-decoration:none; font-weight:bold;">{{ Lang::get('deals::deals.view_deal') }}</a>
			</p>
		</div>
		<!-- END: DEAL FEATURED REQUEST DISAPPROVED -->
	</div>
@stop