@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{$user_name}},</div>

    <div style="line-height:18px;">
    	<!-- BEGIN: ALERT BLOCK -->
        @if($status == 'Paid')
            <p style="background:#dff0d8; border:1px solid #d6e9c6; border-radius:4px; color:#3c763d; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
                {{trans('mail.withdrawal_amount_approved')}}
            </p>
        @else
            <p style="background:#f2dede; border:1px solid #ebccd1; border-radius:4px; color:#a94442; font:bold 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
                {{trans('mail.withdrawal_amount_cancelled')}}
            </p>
        @endif
        <!-- END: ALERT BLOCK -->

        <!-- BEGIN: USER WITHDRAWAl DETAILS -->
        <div style="line-height:18px; padding:10px 20px; background:#fafafa;">
            <p style="margin:10px 0 20px 0; padding:0 0 5px  0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.withdrawal_details')}} :</p>
            <table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.withdrawal_id')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                        	<a target="_blank" href="{{Url::action('MyWithdrawalController@getIndex').'?request_id='.$withdraw_id}}">{{ $withdraw_id }}</a></p>
                    </td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.amount')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $currency }} {{ $amount }}</p>
                    </td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.status')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $status }}</p>
                    </td>
                </tr>

                @if($status == 'Paid')
                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.details')}} :</p>
                        </td>
                        <td valign="top" align="left">
                            <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ nl2br($paid_notes) }}</p>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.details')}} :</p>
                        </td>
                        <td valign="top" align="left">
                            <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ nl2br($cancelled_reason) }}</p>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
        <!-- END: USER WITHDRAWAl DETAILS -->
    </div>
@stop
