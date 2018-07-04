@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{$user_details['display_name']}},</div>
    
    <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
        {{trans('mail.your')}} @if(isset($section)) "{{$section}}" {{trans('mail.section_of')}} @endif {{trans('mail.shop_details_changed_by_admin')}}. {{trans('mail.you_can')}} <a href="{{URL::to('users/login')}}">{{trans('mail.login')}}</a> {{trans('mail.and_checked_update_shop_details')}} <a href="{{ URL::to('shop/users/shop-details') }}">{{trans('mail.here')}}</a>
    </p>
@stop
