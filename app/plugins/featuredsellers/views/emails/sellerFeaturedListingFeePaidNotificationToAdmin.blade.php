@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('deals::deals.hi') }} Admin,</div>

    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.listing_fee_has_been_paid_for_set_as_featured_seller') }}.</p>
@stop