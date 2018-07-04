@extends('base')
@section('content')
    @if ($success_message != "")
        <div class="alert alert-success">
        	{{	$success_message }}
			@if (isset($is_free) && $is_free)
				<a href="{{ URL::action('PurchasesController@getIndex') }}"><strong>{{ trans('payCheckOut.click_here') }}</strong></a>
			@else
				<a href="{{ URL::action('InvoiceController@getIndex') }}"><strong>{{ trans('payCheckOut.click_here') }}</strong></a>
			@endif
			{{ trans('payCheckOut.to_view_the_status') }}.
        </div>
    @endif

    @if ($cancel_message != "")
        <div class="alert alert-danger">
        	{{	$cancel_message }}<a href="{{ URL::action('InvoiceController@getIndex') }}"><strong>{{ trans('payCheckOut.click_here') }}</strong></a>
        	{{ trans('payCheckOut.to_view_the_status') }}.
        </div>
    @endif
@stop