@extends('base')
@section('content')
	<!--BEGIN: UNSUBSCRIBE PAGE -->
	<div class="index-features">
    	<h1>Unsubscribe</h1>
        @if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
           <div class="alert alert-danger">{{ $d_arr['error_msg'] }}</div>
        @elseif(isset($d_arr['success_msg']) && $d_arr['success_msg'] != '')
        	<div class="alert alert-success">{{ $d_arr['success_msg'] }}</div>
        @else
            {{ Form::open(array('id'=>'selUnsubscribeForm', 'method'=>'post','class' => 'form-horizontal border-type1 well' )) }}
                {{ Form::hidden('code', $code) }}
                <div class="note note-info">
					<p class="margin-bottom-5"><span>Email: </span><strong>{{ $unsubscribe_email }}</strong></p>
					{{ trans('unsubscribe.unsubscribe_msg') }}
				</div>
				<button type="submit" name="edit_basic" class="btn green" id="edit_basic" value="edit_basic"><i class="fa fa-check"></i> {{trans("common.submit")}}</button>
				<button type="reset" name="edit_cancel" class="btn default" onclick="window.location = '{{ Url::to('/') }}'"><i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
            {{ Form::close() }}
        @endif
    </div>
	<!-- END: UNSUBSCRIBE PAGE -->
@stop