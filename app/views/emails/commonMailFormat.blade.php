@extends('mail')
@section('email_content')
    <!-- BEGIN: COMMON EMAIL -->
    <div style="font:normal 14px/18px Arial, Helvetica, sans-serif; color:#383838;">
        {{ nl2br($content) }}
    </div>
    <!-- END: COMMON EMAIL -->
@stop