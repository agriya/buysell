@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ trans('mail.hi') }} {{ $first_name }},</div>

    <div style="background:#f9f9f9; border:1px solid #eaeaea; border-radius:4px; margin-bottom:35px; line-height:18px; padding:10px 20px;">
        <!-- BEGIN: INFO BLOCK -->
        <p style="background:#d9edf7; border-left:5px solid #91d9e8; color:#31708f; font:normal 14px/20px Arial, Helvetica, sans-serif; margin:0 0 20px 0; padding:10px 15px;">
            {{ trans('mail.processed_your_request_become_seller') }}
        </p>
        <!-- BEGIN: INFO BLOCK -->

        <!-- BEGIN: PROCESSED REQUEST TO BECOME SELLER -->
        <table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="150" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ trans('mail.status') }} :</p>
                </td>
                <td valign="top" align="left">
                    <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $status_txt }}</p>
                </td>
            </tr>

            <tr>
                <td width="150" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ trans('mail.comments_by_admin') }} :</p>
                </td>
                <td valign="top" align="left">
                    <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $reply_message }}</p>
                </td>
            </tr>
        </table>
        <!-- END: PROCESSED REQUEST TO BECOME SELLER -->
    </div>
@stop
