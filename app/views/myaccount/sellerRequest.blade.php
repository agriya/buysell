@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('myaccount/form.seller_request.request_to_become_seller', array('site_name' => Config::get('generalConfig.site_name'))) }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="alert alert-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="alert alert-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<!-- BEGIN: SELLER REQUEST -->
			<div class="well captcha-code">
				@if(!$is_already_requested)
					<div class="note note-info">
					{{ Lang::get('myaccount/form.seller_request.request_seller_info_message', array('site_name' => Config::get('generalConfig.site_name'))) }}
					</div>
					{{ Form::open(array('action' => array('AccountController@getSellerRequest'), 'id'=>'messagesFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
						<div class="form-group {{{ $errors->has('subject') ? 'error' : '' }}}">
							{{ Form::label('subject', Lang::get('myaccount/form.seller_request.subject'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::text('subject', Lang::get('myaccount/form.seller_request.request_to_become_seller', array('site_name' => Config::get('generalConfig.site_name'))), array('class' => 'form-control valid ', 'rows' => '7','readonly' => 'readonly')); }}
								<label class="error">{{{ $errors->first('subject') }}}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('message_text') ? 'error' : '' }}}">
							{{ Form::label('request_message',Lang::get('myaccount/form.seller_request.message_text'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-8">
								{{  Form::textarea('request_message', Input::old('request_message'), array('class' => 'form-control valid fn_editor', 'rows' => '7')); }}
								<label class="error">{{{ $errors->first('request_message') }}}</label>
							</div>
						</div>

						@if(Config::get('generalConfig.seller_request_captcha_display'))
							@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
                            	<div class="form-group {{{ $errors->has('captcha') ? 'error' : '' }}}">
                                    {{ Form::label('captcha', Lang::get('auth/form.register.security_code'), array('class' => 'col-md-3 col-xs-12 pull-left control-label required-icon')) }}
                                    <div class="col-md-8 captcha-bk">
                                        <div class="clearfix">
                                            {{ Form::text('captcha', null, array('class' => 'form-control')) }}
                                            {{HTML::image(Captcha::img(), Lang::get('auth/form.register.captcha_image'), array('id' => 'src_captcha')) }}
                                            <a href="javascript:void(0)" id="reload_captcha"><span class="fa fa-refresh"></span></a>
                                        </div>
                                        <label for="captcha" generated="true" class="error">{{{ $errors->first('captcha') }}}</label>
                                    </div>
                                </div>
							@else
								<div class="form-group {{{ $errors->has('adcopy_response') ? 'error' : '' }}}">
									{{ Form::label('adcopy_response', Lang::get('auth/form.register.security_code'), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-8">
										{{ solvemedia_get_html(Config::get('generalConfig.challenge_key')) }}
										<label class="error">{{{ $errors->first('adcopy_response') }}}</label>
									</div>
								</div>
							@endif
						@endif

						<div class="form-group">
							<div class="col-md-offset-3 col-md-8">
								<input type="submit" value="{{Lang::get('common.submit')}}" class="btn green" id="seller_request_submit" name="seller_request_submit">
							</div>
						</div>
					{{Form::close()}}
				@else
					<div class="note note-success">{{ Lang::get('myaccount/form.seller_request.we_have_received_request') }}</div>
					@if(count($request_posted_details))
						<div class="dl-horizontal-new dl-horizontal">
							<dl>
								<dt>{{ Lang::get('myaccount/form.seller_request.your_request') }}</dt>
								<dd><span>{{$request_posted_details->request_message}}</span></dd>
							</dl>
							<dl>
								<dt>{{ Lang::get('common.status') }}</dt>
								<dd>
									<?php
										if($request_posted_details->request_status == 'NewRequest') {
											$lbl_class = " text-info";
										}
										elseif($request_posted_details->request_status == 'Allowed') {
											$lbl_class = "text-success";
										}
										elseif($request_posted_details->request_status == 'Rejected') {
											$lbl_class = "text-danger";
										}
										elseif($request_posted_details->request_status == 'ConsiderLater') {
											$lbl_class = "text-primary";
										}
										else {
											$lbl_class = "text-default";
										}
									?>
									<span class="{{ $lbl_class }}">
										{{($request_posted_details->request_status == 'NewRequest')?Lang::get('myaccount/form.seller_request.not_processed_yet'):Lang::get('myaccount/form.seller_request.'.strtolower($request_posted_details->request_status))}}
									</span>
								</dd>
							</dl>
							@if($request_posted_details->reply_sent == 'Yes')
								<dl>
									<dt>{{ Lang::get('myaccount/form.seller_request.response') }}</dt>
									<dd><span>{{$request_posted_details->reply_message}}</span></dd>
								</dl>
							@endif
						</div>
					@endif
				@endif
			</div>
			<!-- END: SELLER REQUEST -->
		</div>
	</div>
@stop

@section('script_content')

<script type="text/javascript">
		var mes_required = '{{ Lang::get('auth/form.required') }}';
		$(document).ready(function() {
			$("#messagesFrm").validate({
                rules: {
	                subject: {
						required: true
					},
					request_message: {
						required: true
					}
					@if(Config::get('generalConfig.seller_request_captcha_display'))
						,
						@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
			                captcha: {
				                required: true
				            }
				        @else
				        	adcopy_response: {
				                required: true
				            }
			            @endif
			        @endif
				},
	            messages: {
	                subject: {
						required: mes_required
					},
					request_message: {
						required: mes_required
					}
					@if(Config::get('generalConfig.seller_request_captcha_display'))
		                ,
		                @if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
			                captcha: {
				                required: mes_required
				            }
				        @else
				        	adcopy_response: {
				                required: mes_required
				            }
			            @endif
			        @endif
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
            });
        });

        @if(Config::get('generalConfig.seller_request_captcha_display') && Config::get('generalConfig.captcha_type') == 'Recaptcha')
			$('#reload_captcha').bind('click', function() {
	      		$('#src_captcha').attr('src', "{{Captcha::img()}}?r="+ Math.random())
	    	});
		@endif
	</script>
@stop