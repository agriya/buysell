@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}},</div>
    
    <!-- BEGIN: COMMON EMAIL -->
    <div style="font:normal 14px/18px Arial, Helvetica, sans-serif; color:#383838;">
        {{ $content }}
    </div>
    <!-- END: COMMON EMAIL -->
@stop