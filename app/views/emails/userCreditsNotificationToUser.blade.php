@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{$user_name}},</div>

    <div style="line-height:18px;">
        @if(isset($invoices) && count($invoices) > 0)
        	<!-- BEGIN: ALERT BLOCK -->
            <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
                {{trans('mail.credits_added_to_your_account')}}
            </p>
            <!-- END: ALERT BLOCK -->

            <!-- BEGIN: USER INVOICE DETAILS -->
            <div style="line-height:18px; padding:10px 20px; background:#fafafa;">
                <p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.invoice_details')}} :</p>
                <table width="98%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.invoice_id')}} :</p>
                        </td>
                        <td valign="top" align="left">
                        	<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
	                        	<a href="{{ URL::action('InvoiceController@getInvoiceDetails', $invoices['common_invoice_id']) }}">{{ $invoices['common_invoice_id'] }}</a>
	                        </p>
                        </td>
                    </tr>

                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.status')}} :</p>
                        </td>
                        <td valign="top" align="left">
                            <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoices['status'] }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.paid_by')}} :</p>
                        </td>
                        <td valign="top" align="left"><p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $user_name }}</p></td>
                    </tr>

                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount')}} :</p>
                        </td>
                        <td valign="top" align="left">
							<p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $invoices['currency'] }} {{ $invoices['amount'] }}</p>
						</td>
                    </tr>
                </table>
            </div>
            <!-- END: USER INVOICE DETAILS -->
        @endif
    </div>
@stop
