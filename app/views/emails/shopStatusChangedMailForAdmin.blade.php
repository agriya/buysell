@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} Admin,</div>

    <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
        <a href="{{$shop_details['shop_url']}}">{{$shop_details['shop_name']}}</a> {{trans('mail.has_been')}} @if($action=='deactivateshop') {{trans('mail.deactivated')}} @else {{trans('mail.activated')}} @endif {{trans('mail.by')}} <a href="{{ URL::to('admin/users/user-details').'/'.$curr_user_details['id'] }}">{{$curr_user_details['display_name']}}</a>. 
    </p>
@stop
