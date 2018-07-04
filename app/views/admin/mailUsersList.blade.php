@extends('adminPopup')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
	<div class="popup-title">
        <h1>{{ $d_arr['pageTitle'] }}</h1>
    </div>
    @if(isset($d_arr['error_msg']) && $d_arr['error_msg']!= "")
		<div class="note note-danger">{{ $d_arr['error_msg'] }}</div>
	@else
	    <div class="pop-content">
			<!-- BEGIN TABLE -->
			{{ Form::open(array('id'=>'memberListfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
				{{ Form::hidden('page', Input::get('page'),array('id' =>'page')) }}
				{{ Form::hidden('mail_id', Input::get('mail_id'),array('id' =>'mail_id')) }}
				{{ Form::hidden('action', Input::get('action'),array('id' =>'action')) }}
	        	<div class="table-responsive">
	                <table class="table table-striped table-bordered table-hover" id="search-user">
	                    <thead>
	                        <tr>
	                            <th class="col-xs-5">{{ trans('admin/manageMembers.memberlist_user_name') }}</th>
	                            <th class="col-xs-7">{{ trans('admin/manageMembers.memberlist_user_email') }}</th>
	                        </tr>
	                    </thead>

	                    <tbody>
	                        @if(count($user_details) > 0)
	                            @foreach($user_details as $reqKey => $user)
	                                <tr>
	                                    <td>{{ $user['first_name'] }} {{ $user['last_name'] }}</td>
	                                    <td>{{ $user['email'] }}</td>
	                                </tr>
	                            @endforeach
	                        @else
	                            <tr><td colspan="2"><p class="alert alert-info">{{ trans('admin/manageMembers.memberlist_none_err_msg') }}</p></td></tr>
	                        @endif
	                    </tbody>
	                </table>
	            </div>
				@if(count($user_details) > 0)
	                {{ $user_list->appends(array('page' => Input::get('page'), 'mail_id' => Input::get('mail_id'), 'action' => Input::get('action')))->links() }}
				@endif
			{{ Form::close() }}
			<!-- END TABLE -->
		</div>
	@endif
@stop