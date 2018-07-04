@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('myaccount/form.account_menu_edit_profile') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			<!-- ALERT BLOCK STARTS -->
			@if (Session::has('success_message') && Session::get('success_message') != "")
				<div class="note note-success">{{	Session::get('success_message') }}</div>
			@endif
			@if (Session::has('error_message') && Session::get('error_message') != "")
				<div class="note note-danger">{{	Session::get('error_message') }}</div>
			@endif
			@if(Session::has('valid_user'))
				<div class="note note-danger">{{	Session::get('valid_user') }}</div>
			@endif
			<!-- ALERT BLOCK ENDS -->

			<!-- EDIT PROFILE STARTS -->
			<div class="edit-profilepg well bg-form">
				<div class="row">
					<div class="col-md-6">
						<!-- UPDATE PROFILE IMAGE STARTS -->
						{{ Form::model($udetails, ['url' => URL::to('users/myaccount'),'method' => 'post','id' => 'editimageaccount_frm', 'class' => 'form-horizontal margin-bottom-40', 'files' => true] ) }}
							<div class="portlet">
								<div class="portlet-title">
									<div class="caption">
										<i class="fa fa-photo"></i>
										{{ Lang::get('myaccount/form.edit-profile.profile_image_title') }}
									</div>
									<div class="tools">
										<a class="collapse" href="javascript:;"></a>
									</div>
								</div>
								<div class="portlet-body">
									<div class="media">
										<div class="pull-left custom-profimg">
											<a href="javascript:;" title="{{ Lang::get('myaccount/form.edit-profile.profile_image') }}" class="imgusertm-96X96">
												<img class="" src="{{$image_details['image_url']}}" {{$image_details['image_attr']}} alt="{{ Lang::get('myaccount/form.edit-profile.profile_image') }}" />
											</a>
										</div>
										<div class="media-body">
											<label for="user_image" class="disp-block required-icon">{{ Lang::get('myaccount/form.edit-profile.profile_image') }}</label>
											<div class="custom-upldimg margin-bottom-20">
												{{ Form::file('file',array('class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
												<small class="text-muted">({{Config::get('generalConfig.user_image_uploader_allowed_file_size')/1024}} MB) ({{Config::get('generalConfig.user_image_uploader_allowed_extensions')}})</small>
												<label for="filestyle-0" generated="true" class="error"></label>
											</div>
											<button name="edit_profile_image" type="submit" class="btn blue-madison margin-top-8 margin-bottom-15" value="{{Lang::get('common.upload')}}"><i class="fa fa-upload"></i> {{Lang::get('common.upload')}}</button>
										</div>
									</div>
								</div>
							</div>
						{{Form::close()}}
						<!-- UPDATE PROFILE IMAGE END -->

						<!-- BASIC DETAILS STARTS -->
						<div class="portlet">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-file-text"></i>
									{{ Lang::get('myaccount/form.edit-profile.basic_details_title') }}
								</div>
								<div class="tools">
									<a class="collapse" href="javascript:;"></a>
								</div>
							</div>

							<div class="portlet-body">
								{{ Form::model($udetails, ['url' => URL::to('users/myaccount'),'method' => 'post','id' => 'editaccount_frm', 'class' => 'form-horizontal']) }}
									<fieldset>
										<div class="form-group {{{ $errors->has('user_name') ? 'error' : '' }}}">
											{{ Form::label('user_name', Lang::get('myaccount/form.edit-profile.user_name'), array('class' => 'col-md-4 control-label')) }}
											<div class="col-md-7">
												@if($udetails['user_name'] == '')
													{{ Form::label('user_name', Str::lower($udetails['first_name']), array('class' => 'control-label text-bold')) }}
												@else
													{{ Form::label('user_name', $udetails['user_name'], array('class' => 'control-label text-bold')) }}
												@endif
											</div>
										</div>

										<div class="form-group {{{ $errors->has('current_email') ? 'error' : '' }}}">
											{{ Form::label('current_email', Lang::get('myaccount/form.edit-profile.current_email'), array('class' => 'col-md-4 control-label')) }}
											<div class="col-md-7">
												{{ Form::label('current_email', $udetails['email'], array('class' => 'control-label text-bold')) }}
											</div>
										</div>

										<div class="form-group {{{ $errors->has('Oldpassword') ? 'error' : '' }}}">
											{{ Form::label('Oldpassword', Lang::get('myaccount/form.edit-profile.current_password'), array('class' => 'col-md-4 control-label required-icon')) }}
											<div class="col-md-7">
												{{ Form::password('Oldpassword', array('class' => 'form-control')); }}
												<label class="error">{{{ $errors->first('Oldpassword') }}}</label>
											</div>
										</div>

										<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
											{{ Form::label('password',  Lang::get('myaccount/form.edit-profile.password'), array('class' => 'col-md-4 control-label')) }}
											<div class="col-md-7">
												{{  Form::password('password', array('class' => 'form-control', 'id' => 'password')); }}
												<label class="error">{{{ $errors->first('password') }}}</label>
											</div>
										</div>

										<div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
											{{ Form::label('password_confirmation', Lang::get('myaccount/form.edit-profile.confirm_password'), array('class' => 'col-md-4 control-label')) }}
											<div class="col-md-7">
												{{  Form::password('password_confirmation', array('class' => 'form-control')); }}
												<label class="error">{{{ $errors->first('password_confirmation') }}}</label>
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-offset-4 col-md-7">
												<button type="submit" name="edit_basic" class="btn green" id="edit_basic" value="edit_basic">
												<i class="fa fa-check"></i> {{ Lang::get('common.submit') }}</button>
											</div>
										</div>
									</fieldset>
								{{ Form::close() }}
							</div>
						</div>
						<!-- BASIC DETAILS ENDS -->
					</div>

					<div class="col-md-6">
						<!-- PERSONAL DETAILS STARTS -->
						<div class="portlet">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-file-text-o"></i>
									{{ Lang::get('myaccount/form.edit-profile.personal_details_title') }}
								</div>
								<div class="tools">
									<a class="collapse" href="javascript:;"></a>
								</div>
							</div>
							<div class="portlet-body">
								{{ Form::model($udetails, ['url' => URL::to('users/myaccount'),'method' => 'post','id' => 'editpersonal_details_frm', 'class' => 'form-horizontal', 'files' => 'true', 'enctype' => 'multipart/form-data']) }}
									<fieldset>
										<div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}">
											{{ Form::label('first_name', Lang::get('myaccount/form.edit-profile.first_name'), array('class' => 'col-md-4 control-label required-icon')) }}
											<div class="col-md-7">
												{{ Form::text('first_name', null, array ('class' => 'form-control')); }}
												<label class="error">{{{ $errors->first('first_name') }}}</label>
											</div>
										</div>

										<div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
											{{ Form::label('last_name', Lang::get('myaccount/form.edit-profile.last_name'), array('class' => 'col-md-4 control-label required-icon')) }}
											<div class="col-md-7">
												{{ Form::text('last_name', null, array ('class' => 'form-control')); }}
												<label class="error">{{{ $errors->first('last_name') }}}</label>
											</div>
										</div>

										<div class="form-group {{{ $errors->has('about_me') ? 'error' : '' }}}">
											{{ Form::label('about_me', Lang::get('myaccount/form.edit-profile.about_me'), array('class' => 'col-md-4 control-label')) }}
											<div class="col-md-7">
												{{ Form::textarea('about_me', null, array ('class' => 'form-control','rows' => '5')); }}
												<label class="error">{{{ $errors->first('about_me') }}}</label>
											</div>
										</div>

										<div class="form-group {{{ $errors->has('subscribe_newsletter') ? 'error' : '' }}}">
				                            {{ Form::label('subscribe_newsletter', trans("myaccount/form.edit-profile.subscribe_newsletter"), array('class' => 'col-md-4 control-label')) }}
				                            <div class="col-md-7">
				                               	<label class="margin-top-10">
													{{ Form::checkbox('subscribe_newsletter', null, array ('class' => '', 'id' => 'subscribe_newsletter')); }}
												</label>
												<label class="error">{{ $errors->first('subscribe_newsletter') }}</label>
				                            </div>
				                        </div>

										<div class="form-group">
											<div class="col-md-offset-4 col-md-7">
												<button type="submit" name="edit_personal" id="edit_personal" value="edit_personal" class="btn green">
												<i class="fa fa-check"></i> {{ Lang::get('common.submit') }}</button>
											</div>
										</div>
									</fieldset>
								{{ Form::close() }}
							</div>
						</div>
						<!-- PERSONAL DETAILS ENDS -->
					</div>
				</div>
			</div>
			<!-- EDIT PROFILE ENDS -->
		</div>
	</div>
	<script language="javascript" type="text/javascript">
		var format = "{{ str_replace('VAR_FORMAT', Config::get('generalConfig.user_image_uploader_allowed_extensions'), trans('myaccount/form.edit-profile.image_allowed_format')) }}";
		var err_msg = '';
		var messageFunc = function() { return err_msg; };
		var page_name 	= "edit_profile";
		var err_msg_message = "{{ Lang::get('auth/form.register.restricted_keyword') }}";
		var restrict_keywords_message = "{{ Config::get('webshopauthenticate.screen_name_restrict_keywords_like') }}";
		var restrict_keywords_exact_message= "{{ Config::get('webshopauthenticate.screen_name_restrict_keywords_exact') }}";
		var merchant_signup_specialchars = "{{ Lang::get('auth/form.edit-profile.merchant_signup_specialchars_not_allowed') }}";
		var merchant_signup_twice_not_allowed = '{{ Lang::get('auth/form.edit-profile.merchant_signup_twice_not_allowed') }}';
		var mes_required = "{{ Lang::get('auth/form.required') }}";
		var fieldlength_password_min = "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}";
		var fieldlength_password_max = "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}";
		var validation_password_length_low = "{{ Lang::get('auth/form.register.validation_password_length_low') }}";
		var validation_maxLength = "{{ Lang::get('auth/form.register.validation_maxLength') }}";
		var validation_password_mismatch = "{{ Lang::get('auth/form.register.validation_password_mismatch') }}";
		var fieldlength_name_min_length = "{{ Config::get('webshopauthenticate.fieldlength_name_min_length') }}";
		var fieldlength_name_max_length = "{{ Config::get('webshopauthenticate.fieldlength_name_max_length') }}";
    </script>
@stop

