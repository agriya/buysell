@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} Admin,</div>
    
    <!-- BEGIN: ADMIN CANCELLATION DETAILS -->
    <div style="line-height:18px;">
        <div style="background:#fafafa; line-height:18px; padding:10px 20px;">
            <p style="margin:5px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.cancellation_details')}} :</p>
            <table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.order_id')}} :</p>
                    </td>
                    <td valign="top" align="left"><p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $order_id }}</p></td>
                </tr>
    
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.status')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                            {{ $order_status }}
                        </p>
                    </td>
                </tr>
    
                @if($refund_action == 'yes' && $mail_from_send == 'admin')
                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount_details')}} :</p>
                        </td>
                        <td valign="top" align="left">
                           <!-- <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                                <span>{{trans('mail.paypal_amount')}}:</span> {{ $currency }} {{ $seller_refund_paypal_amount }}</span>
                            </p>-->
                            <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                                <span>{{trans('mail.credit_amount')}}:</span> {{ $currency }} {{ $seller_refund_amount }}
                            </p>
                        </td>
                    </tr>
                @endif
    
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.notes')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $admin_notes }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- END: ADMIN CANCELLATION DETAILS -->
@stop
