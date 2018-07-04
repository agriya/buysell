@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{ $to_user_details['display_name'] }},</div>

    <!-- BEGIN: ALERT BLOCK - INFO FOR USER -->
     <?php $view_from_user_url = Cutil::userProfileUrl($from_user_details['user_code']); ?>
    <div style="margin-bottom:35px; font:normal 14px/18px Arial, Helvetica, sans-serif; color:#383838;">
        <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; font:normal 14px Arial, Helvetica, sans-serif; color:#31708f; margin:0 0 15px; padding:10px 15px;">
            A message has been posted to you from
            <a style="color:#31708f; font:bold 13px Arial; text-decoration:none;" href="{{ $view_from_user_url }}">{{ $from_user_details['display_name'] }}</a>.
        </p>

        <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; font:normal 14px Arial, Helvetica, sans-serif; color:#31708f; margin:0; padding:10px 15px;">
            <a style="color:#31708f; font:bold 13px Arial; text-decoration:none;" href="{{$message_view_link}}">Click Here</a> to view message.
        </p>
    </div>
    <!-- END: ALERT BLOCK - INFO FOR USER -->
@stop


