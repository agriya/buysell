@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ trans('mail.hi') }} {{ $first_name }},</div>

    <div style="background:#f9f9f9; border:1px solid #eaeaea; border-radius:4px; margin-bottom:20px; line-height:18px; padding:10px 20px;">
    	<!-- BEGIN: INFO BLOCK -->
        <p style="background:#d9edf7; border-left:5px solid #91d9e8; color:#31708f; font:bold 15px Arial, Helvetica, sans-serif; margin:10px 0 18px 0; padding:10px 15px;">
            {{ trans('mail.your_account_details_changed') }}
        </p>
        <!-- END: INFO BLOCK -->

        <!-- BEGIN: USER CREATION -->
        <table width="98%" cellspacing="0" cellpadding="0" border="0">
        	@if(isset($new_email) && $new_email != '')
	            <tr>
	                <td width="100" valign="top" align="left">
	                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ trans('mail.old_email') }} :</p>
	                </td>
	                <td valign="top" align="left">
	                    <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $email }}</p>
	                </td>
	            </tr>

	            <tr>
	                <td width="100" valign="top" align="left">
	                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ trans('mail.new_email') }} :</p>
	                </td>
	                <td valign="top" align="left">
	                    <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $new_email }}</p>
	                </td>
	            </tr>
			@endif

			@if(isset($user_name) && $user_name != '')
	            <tr>
	                <td width="100" valign="top" align="left">
	                    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ trans('mail.username') }} :</p>
	                </td>
	                <td valign="top" align="left">
	                    <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $user_name }}</p>
	                </td>
	            </tr>
            @endif

			@if(isset($password) && $password != '')
	            <tr>
	                <td width="100" valign="top" align="left">
	                    <p style="padding:0; margin:0 0 8px 0; color:#8c8c8c; font:normal 13px Arial, Helvetica, sans-serif;">{{ trans('mail.password') }} :</p>
	                </td>
	                <td valign="top" align="left">
	                    <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $password }}</p>
	                </td>
	            </tr>
	        @endif
        </table>
        <!-- END: USER CREATION -->
    </div>

    <!-- BEGIN: ALERT BLOCK -->
    <p style="background:#fcf8e3; border:1px solid #faebcc; border-radius:4px; color:#8a6d3b; font:bold 14px Arial, Helvetica, sans-serif; margin:0; padding:10px 15px;">
        {{ trans('mail.change_your_password_myaccount_section') }}
    </p>
    <!-- END: ALERT BLOCK -->
@stop
