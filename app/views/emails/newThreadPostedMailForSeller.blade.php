@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} Admin,</div>
    
    <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
        {{trans('mail.new_report_posted_for_product')}}
    </p>

    <div style="line-height:18px;">
    	<!-- BEGIN: BUYER ORDER DETAILS -->
		<div style="margin-bottom:35px; line-height:18px; padding:10px 20px; background:#f9f9f9;">
			<p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.reported_details')}} :</p>
			<table width="98%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="100" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.reported_product')}} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a href="{{$product_view_url}}">{{$product_details['product_name']}}</a>
						</p>
					</td>
				</tr>

				<tr>
					<td width="100" valign="top" align="left">
						<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.reported_by')}} :</p>
					</td>
					<td valign="top" align="left">
						<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
						   <a href="{{$reported_by['profile_url']}}">{{$reported_by['display_name']}}</a>
						</p>
					</td>
				</tr>
			</table>
		</div>
        <!-- END: BUYER ORDER DETAILS -->

		@if(count($reported_threads) > 0)
			<!-- BEGIN: BUYER INVOICE DETAILS -->
			<div style="line-height:18px; padding:10px 20px; background:#fafafa;">
				<p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.reported_threads')}} : </p>
				<table width="98%" cellspacing="0" cellpadding="0" border="0">
					<?php $inc = 1; ?>
					@foreach($reported_threads as $thread)
						<tr>
							<td valign="top" align="left"><p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{$inc}}. {{ $thread }}</p></td>
						</tr>
						<?php $inc++; ?>
					@endforeach
				</table>
			</div>
			<!-- BEGIN: BUYER INVOICE DETAILS -->
        @endif
    </div>
@stop
