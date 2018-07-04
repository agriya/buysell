@extends('mail')
@section('email_content')
	<div style="margin:0 0 15px 0; font:normal 14px/20px Arial, Helvetica, sans-serif; color:#253131;">{{ $content }}</div>
	@if($unsubscribe_code != '')
		<div style="background:#d9edf7; border:1px solid #bce8f1; font:normal 14px Arial, Helvetica, sans-serif; color:#31708f; border-radius:4px; margin:0 0 20px 0; padding:15px;">
			{{trans('mail.to_unsubcribe_from_all')}},
			<a style="color:#0f68b4; font:bold 13px Arial; text-decoration:none;" href="{{ Url::to('unsubscribe/mail') }}/{{ $unsubscribe_code }}">{{trans('mail.click_here')}}</a>
		</div>
	@endif
@stop