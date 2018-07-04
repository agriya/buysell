@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('common.hi')}} {{ $user->first_name }},</div>

    <!-- BEGIN: WELCOME MAIL FOR USER -->
    <div style="font:normal 14px/18px Arial, Helvetica, sans-serif; color:#383838;">
        <p style="background:#dff0d8; border:1px solid #d6e9c6; border-radius:4px; color:#3c763d; font:normal 14px Arial, Helvetica, sans-serif; margin:0; padding:10px 15px;">
            {{ Lang::get('users.you_account_ready_thanks_for_joining')}} {{ Config::get('generalConfig.site_name') }}.
        </p>
    </div>
    <!-- END: WELCOME MAIL FOR USER -->
@stop