@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} Admin,</div>

	<div>
		<!-- BEGIN: INFO BLOCK -->
        <p style="background:#d9edf7; border-left:5px solid #91d9e8; color:#31708f; font:bold 15px Arial, Helvetica, sans-serif; margin:10px 0 18px 0; padding:10px 15px;">
            {{trans('mail.a_new_user_registered')}}
        </p>
        <!-- END: INFO BLOCK -->
    </div>
    <div>
		<p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">
            <a href="{{ url('admin/users/user-details/'.$user_details->id) }}">{{trans('mail.view_users_profile')}}</a>
        </p>
	</div>
    <div style="background:#f9f9f9; border:1px solid #eaeaea; border-radius:4px; margin-bottom:20px; line-height:18px; padding:10px 20px;">

        <!-- BEGIN: USER CREATION -->
        <table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="100" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.name')}} :</p>
                </td>
                <td valign="top" align="left">
                    <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $user_details->first_name }} {{ $user_details->last_name }}</p>
                </td>
            </tr>

            <tr>
                <td width="100" valign="top" align="left">
                    <p style="padding:0; margin:0 0 8px 0; color:#8c8c8c; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.email')}} :</p>
                </td>
                <td valign="top" align="left">
                    <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $user_details->email }}</p>
                </td>
            </tr>
        </table>
        <!-- END: USER CREATION -->
    </div>
@stop
