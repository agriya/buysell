@extends('adminPopup')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
	<div class="popup-title">
    	<h1>{{ trans("admin/massEmail.composer.pick_user")}}</h1>
    </div>
    <div class="popup-form">
    	<!-- BEGIN: ALERT BLOCK -->
		@if (Session::has('success_message') && Session::get('success_message') != "")
			<div class="alert alert-success">{{	Session::get('success_message') }}</div>
		@endif

		@if (Session::has('error_message') && Session::get('error_message') != "")
			<div class="alert alert-danger">{{	Session::get('error_message') }}</div>
		@endif
        <!-- END: ALERT BLOCK -->

		<!-- BEGIN: PAGE TITLE -->
		<h4>{{ trans('admin/manageMembers.memberlist_search_members') }}</h4>
		<!-- END: PAGE TITLE -->

		<!-- BEGIN: SEARCH BAR -->
		{{ Form::hidden('page', Input::get('page'),array('id' => 'page')) }}
		{{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal well mb30' )) }}
			<div class="row">
				<div class="clearfix">
					<fieldset class="col-sm-6">
						<div class="form-group {{{ $errors->has('user_code') ? 'error' : '' }}}">
							{{ Form::label('user_code', trans('admin/manageMembers.memberlist_user_code'), array('class' => 'control-label col-sm-4')) }}
							<div class="col-sm-7">
								{{ Form::text('user_code', Input::get("user_code"), array("placeholder"=> trans('admin/manageMembers.memberlist_user_code_id'),'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group {{{ $errors->has('user_name') ? 'error' : '' }}}">
							{{ Form::label('user_name', trans('admin/manageMembers.memberlist_user_name'), array('class' => 'control-label col-sm-4')) }}
							<div class="col-sm-7">
								{{ Form::text('user_name', Input::get("user_name"), array('class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group {{{ $errors->has('user_email') ? 'error' : '' }}}">
							{{ Form::label('user_email', trans('admin/manageMembers.memberlist_user_email'), array('class' => 'control-label col-sm-4')) }}
							<div class="col-sm-7">
								{{ Form::text('user_email', Input::get("user_email"), array('class' => 'form-control')) }}
							</div>
						</div>
					</fieldset>

					<fieldset class="col-sm-6">
						<div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
							{{ Form::label('status', trans('admin/manageMembers.memberlist_user_status'), array('class' => 'control-label col-sm-4')) }}
							<div class="col-sm-7">
								{{ Form::select('status', array('' => trans("common.all"), 'active' => trans("common.active"), 'inactive' => trans("common.inactive"), 'blocked' => trans("common.blocked")), Input::get("status"), array('class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group {{{ $errors->has('user_group') ? 'error' : '' }}}">
							{{ Form::label('user_group', trans('admin/manageMembers.memberlist_group_label'), array('class' => 'control-label col-sm-4')) }}
							<div class="col-sm-7">
								{{ Form::select('user_group', array('' => trans("common.choose")) + $user_groups, Input::get("user_group"), array('class' => 'form-control')) }}
							</div>
						</div>
					</fieldset>
				</div>

				<div class="col-sm-offset-2 col-sm-5">
					<button type="submit" name="search_members" value="search_members" class="btn purple-plum">
					<i class="fa fa-search"></i> {{ trans("common.search") }}</button>
					<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminUserController@SearchMembers') }}'"><i class="fa fa-undo"></i> {{ trans("common.reset")}}</button>
				</div>
			</div>
		{{ Form::close() }}
		<!-- BEGIN: SEARCH BAR -->

		<!-- BEGIN: TABLE -->
		{{ Form::open(array('id'=>'memberListfrm', 'method'=>'get','class' => 'form-horizontal form-request overflw-auto' )) }}
			<table class="table table-striped table-bordered table-hover" id="search-user">
				<thead>
					<tr>
						<th><input  type="checkbox" id="selectall" value="" />{{ trans('common.select_all') }}</th>
						<th>{{ trans('admin/manageMembers.memberlist_user_id') }}</th>
						<th>{{ trans('admin/manageMembers.memberlist_user_name') }}</th>
						<th>{{ trans('admin/manageMembers.memberlist_user_email') }}</th>
						<th></th>
					</tr>
				</thead>

				<tbody>
					@if(count($user_list) > 0)
						@foreach($user_list AS $user)
							<tr>
								<td >
									<?php
										$checked_checkbox = '';
										$admin_profile_url = CUtil::getUserDetails($user->id);
										$mass_email_ids = MassMailUsers::where('mass_email_id', $send_id)->select('user_id')->get();
										foreach($mass_email_ids as $mass_id){
											if($mass_id->user_id == $user->id){
												$checked_checkbox = 'checked';
											}
										}
										?>
									<input type="checkbox" {{ $checked_checkbox }} class="sel_users" value="{{$user->id}}" title="{{ $user->first_name }} {{ $user->last_name }}" id="seletall" />
								</td>
								<td>{{ $user->id }}  </td>
								<td>
									<a target="_blank" href="{{ Url::to('admin/users/user-details/'.$user->id) }}">{{ $user->first_name }} {{ $user->last_name }}</a>
									<p>({{BasicCUtil::setUserCode($user->id)}})</p>
								</td>
								<td>{{ $user->email }}</td>
								<td class="btnxs-size"> <button type="button" name="select_member"  value="{{ $user->id }}" title="{{ $user->first_name }} {{ $user->last_name }}" class="btn btn-info btn-xs select_member">{{ trans("common.select_option") }}</button></td>
							</tr>
						@endforeach
					@else
						<tr><td colspan="7"><p class="alert alert-info">{{ trans('admin/manageMembers.memberlist_none_err_msg') }}</p></td></tr>
					@endif
				</tbody>
			</table>

			<button type="button" name="submit_member" id="submit_member" value="search_members" class="btn btn-success btn-sm">
			{{ trans("common.submit") }} <i class="fa fa-check"></i></button>

			@if(count($user_list) > 0)
				<div class="pull-right">
					{{ $user_list->appends(array('send_id' => $send_id, 'page' => Input::get('page'), 'user_code' => Input::get('user_code'), 'user_name' => Input::get('user_name'), 'user_email' => Input::get('user_email'), 'user_type' => Input::get('user_type'), 'destination' => Input::get('destination'), 'status' => Input::get('status'), 'search_members' => Input::get('search_members')))->links() }}
				</div>
			@endif
		{{ Form::close() }}
		<!-- END: TABLE -->
	</div>
@stop

@section('includescripts')
	<script language="javascript">
        $(document).ready(function () {
            $("#selectall").click(function () {
                  $('.sel_users').attr('checked', this.checked);
            });

            $(".case").click(function(){

                if($(".sel_users").length == $(".sel_users:checked").length) {
                    $("#selectall").attr("checked", "checked");
                } else {
                    $("#selectall").removeAttr("checked");
                }

            });

            $("#submit_member").click(function(){
                var useridarr= new Array();
                var usernamearr= new Array();
                $( ".sel_users" ).each(function() {
                if($( this ).is(':checked') )
                {
                useridarr.push($(this).val())
                usernamearr.push($(this).attr('title'))
                }
                });

                parent.getusers(useridarr,usernamearr);
            })
            $(".select_member").click(function() {
                var useridarr= new Array();
                var usernamearr= new Array();
                useridarr.push($(this).val())
                usernamearr.push($(this).attr('title'))
                parent.getusers(useridarr,usernamearr);
            })
            });
    </script>
@stop
