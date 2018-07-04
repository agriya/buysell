@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('deals::deals.hi') }} Admin,</div>

	<div style="line-height:18px;">
		<!-- BEGIN: ALERT BLOCK -->
		<p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
			{{ $admin_msg }}
		</p>
		<!-- END: ALERT BLOCK -->

		<!-- BEGIN: DEAL DETAILS -->
		<div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
			<p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{ Lang::get('deals::deals.details_of_the_deal') }}</p>
			 <table width="98%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.deal_id_label') }} :</p>
					</td>

					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $deal_id }}</p>
					</td>
				</tr>

				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.deal_name') }} :</p>
					</td>

					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a style="color:#15aadb; text-decoration:none;" href="{{ $viewDealLink }}" title="{{ $deal_title }}">{{ $deal_title }}</a>
						</p>
					</td>
				</tr>

				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.discount_percentage') }} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;"><strong>{{ $deal_discount_percentage }}</strong>%</p>
					</td>
				</tr>

				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.added_by') }} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a href="{{ $deal_user_profile_link }}" title="{{ $deal_added_by['display_name'] }}" style="color:#15aadb; text-decoration:none;">
								{{ $deal_added_by['display_name'] }}
							</a>
						</p>
					</td>
				</tr>

				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.added_on') }} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ CUtil::FMTDate($deal_added_date, "Y-m-d H:i:s", '') }}
						</p>
					</td>
				</tr>

				<tr>
					<td width="150" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.deal_status') }} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							{{ $status_msg }}
						</p>
					</td>
				</tr>
			</table>
			<p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 13px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
				{{ Lang::get('deals::deals.for_more_details_about_this_deal') }}: <a href="{{ $viewDealLink }}" title="{{ Lang::get('deals::deals.view_deal') }}" style="color:#15aadb; text-decoration:none; font-weight:bold;">{{ Lang::get('deals::deals.click_here') }}</a>
			</p>
		</div>
		<!-- END: DEAL DETAILS -->
	</div>
@stop