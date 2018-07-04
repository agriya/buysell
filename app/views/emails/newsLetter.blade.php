@extends('mail')
@section('email_content')    
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{$newsletter['user_name']}},</div>
    
    <!-- BEGIN: ALERT BLOCK - NEWSLETTER MESSAGE -->
    <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; font:normal 14px Arial, Helvetica, sans-serif; color:#31708f; margin:0; padding:10px 15px;">
        {{$newsletter['message']}}
    </p>
    <!-- END: ALERT BLOCK - NEWSLETTER MESSAGE -->
@stop


