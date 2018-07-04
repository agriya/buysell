@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('common.hi')}} {{ $user->first_name }},</div>

    <!-- BEGIN: USER ACTIVATION -->
    <div style="margin-bottom:35px; font:normal 14px/18px Arial, Helvetica, sans-serif; color:#383838;">
        <p style="background:#eef7fb; border-left:5px solid #91d9e8; color:#000; margin:0 0 20px; padding:10px 15px; font-size:13px;">
		{{ Lang::get('users.please_click_link_to_activate')}} {{ Config::get('generalConfig.site_name') }}</p>
        <p style="margin:0; padding:10px; background:#f5f5f5; border:1px solid #eee; border-width:0 0 3px 0; white-space:normal; word-break:break-word;">
		<a href="{{ $activationUrl }}" style="font:bold 13px/22px Arial; color:#327cb7; text-decoration:none;">{{ $activationUrl }}</a></p>
    </div>
    <!-- END: USER ACTIVATION -->
@stop

