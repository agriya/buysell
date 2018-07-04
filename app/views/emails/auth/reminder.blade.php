@extends('mail')
@section('email_content')
	<div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ trans('mail.hi') }} {{ $user->first_name }},</div>
    <h2 style="font:bold 14px Arial, Helvetica, sans-serif; margin:0;">{{ trans('mail.password_reset') }}</h2>
    <p style="margin:10px 0; font-size:14px; color:#253131; line-height:20px; overflow:hidden; outline:none; max-width:620px;">
        {{ trans('mail.reset_password_complete_form') }}:
        <span style="color:#3c763d; font:normal 14px Arial, Helvetica, sans-serif;"><a href="{{ URL::to('users/reset-password/'.$token) }}">{{ URL::to('users/reset-password/'.$token) }}</a></span>
    </p>
@stop