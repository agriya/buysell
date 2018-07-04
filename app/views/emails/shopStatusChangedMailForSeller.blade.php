@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{$user_details['display_name']}},</div>
    
    <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 14px Arial, Helvetica, sans-serif; margin:10px 0 20px; padding:10px 15px;">
        {{trans('mail.your_shop_has_been')}} @if($action=='deactivateshop') {{trans('mail.deactivated')}} @else {{trans('mail.activated')}} @endif {{trans('mail.by')}} {{trans('mail.site_admin')}}. @if($action=='deactivateshop'){{trans('mail.contact_admin_to_activate_shop')}}.@endif
    </p>
@stop
