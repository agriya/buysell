@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} Admin,</div>

	<!-- BEGIN: ALERT BLOCK -->
    <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
        {{trans('mail.new_message_posted')}}.
    </p>
    <!-- END: ALERT BLOCK -->

    <!-- BEGIN: ADMIN DETAILS -->
    <div style="margin-bottom:35px; line-height:18px; padding:10px 20px; background:#f9f9f9;">
        <p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.details')}}</p>
        <table width="98%" cellspacing="0" cellpadding="0" border="0">
        	 <?php $view_from_user_url = Cutil::userProfileUrl($from_user_details['user_code']);
        	 	 $view_to_user_url = Cutil::userProfileUrl($to_user_details['user_code']);?>
            <tr>
                <td width="100" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.from')}}. :</p>
                </td>
                <td valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#1a1a1a;"><a style="color:#327cb7; font:normal 13px Arial; text-decoration:none;" target="_blank" href="{{ $view_from_user_url }}">{{ $from_user_details['display_name'] }}</a> ({{ $from_user_details['email'] }})</p>
                </td>
            </tr>
            <tr>
                <td width="100" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.to')}} :</p>
                </td>
                <td valign="top" align="left">
                    <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a;"><a style="color:#327cb7; font:noraml 13px Arial; text-decoration:none;" target="_blank" href="{{ $view_to_user_url }}">{{ $to_user_details['display_name'] }}</a> ({{ $to_user_details['email'] }})</p>
                </td>
            </tr>
            <tr>
                <td width="100" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.subject')}} :</p>
                </td>
                <td valign="top" align="left"><p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{{ $message_subject }}}</p></td>
            </tr>
            @if($message_text != "")
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.message')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $message_text }}</p>
                    </td>
                </tr>
            @endif
            <tr>
                <td width="100" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.date')}} :</p>
                </td>
                <td valign="top" align="left"><p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $date_posted }}</p></td>
            </tr>
        </table>
    </div>
    <!-- END: ADMIN DETAILS -->
@stop


