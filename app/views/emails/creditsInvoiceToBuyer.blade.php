@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{ $display_name }},</div>

    <div style="line-height:18px;">
    	<!-- BEGIN: ALERT BLOCK -->
        <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0; padding:10px 15px;">
            {{ $msg }}
        </p>
        <!-- END: ALERT BLOCK -->

        <!-- BEGIN: BUYER CREDIT DETAILS -->
        <div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
            <p style="margin:10px 0 20px 0; padding:0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.credit_details')}}:</p>
            <table width="98%" cellspacing="0" cellpadding="0" border="0">
            	<tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.invoice_id')}} :</p>
                    </td>
                    <td valign="top" align="left">
                    	@if($invoice_details->status == 'Paid')
                        	<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a href="{{ url('invoice/invoice-details/'.$invoice_details->common_invoice_id.'?s=Paid') }}">{{ $invoice_details->common_invoice_id }}</a></p>
                        @endif
                        @if($invoice_details->status == 'Unpaid')
                        	<p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
							<a href="{{ url('invoice/invoice-details/'.$invoice_details->common_invoice_id.'?s=Unpaid') }}">{{ $invoice_details->common_invoice_id }}</a></p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $currency.' '. $amount }}</p>
                    </td>
                </tr>

                @if($date_paid != '0000-00-00 00:00:00')
                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.credited_on')}}:</p>
                        </td>
                        <td valign="top" align="left">
                            @if($invoice_details->status == 'Paid')
	                        	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $date_paid  }}</p>
	                        @endif
	                        @if($invoice_details->status == 'Unpaid')
	                        	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;"> - </p>
	                        @endif
                        </td>
                    </tr>
                @endif

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.notes')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ nl2br($user_notes)  }}</p>
                    </td>
                </tr>
            </table>
        </div>
        <!-- END: BUYER CREDIT DETAILS -->
    </div>
@stop
