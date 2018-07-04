@extends('base')
@section('content')
    <div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT MENU -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT MENU -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<h1>MailBox</h1>
			</div>
			<!-- END: PAGE TITLE -->
            
			<div class="well">
				<div class="row">
					<div class="col-md-2 margin-bottom-30">
						<ul class="list-unstyled ver-inline-menu">
							<li><a href="{{URL::action('MessagingController@getIndex','inbox')}}"><i class="fa fa-inbox"></i>{{trans('mailbox.inbox')}} ({{$inbox_unread_count}})</a></li>
							<li><a href="{{URL::action('MessagingController@getIndex','sent')}}"><i class="fa fa-share"></i>{{trans('mailbox.sent')}}</a></li>
							<li><a href="{{URL::action('MessagingController@getIndex','saved')}}"><i class="fa fa-save"></i>{{trans('mailbox.saved')}}</a></li>
							<li><a href="{{URL::action('MessagingController@getIndex','trash')}}"><i class="fa fa-trash-o"></i>{{trans('mailbox.trash')}}</a></li>
							<li class="active"><a href="{{ URL::action('MessagingController@getCompose') }}"><i class="fa fa-edit"></i>{{trans('mailbox.compose')}}</a></li>
						</ul>
					</div>

					<div class="col-md-10">
						<h2 class="title-one">{{trans('mailbox.compose_mail')}}</h2>
						<!-- BEGIN: INFO BLOCK -->
						@if(Session::has('error_message') && Session::get('error_message') != '')
                            <div class="note note-danger">{{ Session::get('error_message') }}</div>
                            <?php Session::forget('error_message'); ?>
						@endif
                        
						@if(Session::has('success_message') && Session::get('success_message') != '')
                            <div class="note note-success">{{ Session::get('success_message') }}</div>
                            <?php Session::forget('success_message'); ?>
						@endif
						<!-- END: INFO BLOCK -->

						<!-- BEGIN: COMPOSE MAIL -->
						{{ Form::open(array('action' => array('MessagingController@postCompose'), 'id'=>'messagesFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
							<?php
								$user_names= isset($action_details['user_names'])?$action_details['user_names']:'';
								$subject= isset($action_details['subject'])?$action_details['subject']:'';
								$message_text= isset($action_details['message_text'])?$action_details['message_text']:'';
							?>
                            
							<div class="form-group {{{ $errors->has('user_names') ? 'error' : '' }}}">
								{{ Form::label('user_names', trans('mailbox.to')." (".trans('mailbox.username').")", array('class' => 'col-md-3 control-label required-icon')) }}
								<div class="col-md-6">
									{{ Form::select('user_names[]', $user_details, Input::old('user_names', $user_names), array('multiple', 'class' => "input-large mySel")); }}
									{{--  Form::textarea('user_names', Input::old('user_names',$user_names), array('class' => 'form-control', 'rows' => '2', 'cols' => 40)); --}}
									<label class="error">{{{ $errors->first('user_names') }}}</label>
								</div>
							</div>

							<div class="form-group {{{ $errors->has('subject') ? 'error' : '' }}}">
								{{ Form::label('subject', trans('mailbox.subject'), array('class' => 'col-md-3 control-label required-icon')) }}
								<div class="col-md-6">
									{{  Form::text('subject', Input::old('subject',$subject), array('class' => 'form-control valid fn_editor', 'rows' => '7')); }}
									<label class="error">{{{ $errors->first('subject') }}}</label>
								</div>
							</div>

							<div class="form-group {{{ $errors->has('message_text') ? 'error' : '' }}}">
								{{ Form::label('message_text', trans('mailbox.message_text'), array('class' => 'col-md-3 control-label required-icon')) }}
								<div class="col-md-8">
									{{  Form::textarea('message_text', Input::old('message_text',$message_text), array('class' => 'form-control valid fn_editor', 'rows' => '7')); }}
									<label class="error">{{{ $errors->first('message_text') }}}</label>
								</div>
							</div>

							<div class="form-group {{{ $errors->has('after_goto') ? 'error' : '' }}}">
								{{ Form::label('after_goto', trans('mailbox.after_goto'), array('class' => 'col-md-3 control-label ')) }}
								<div class="col-md-8">
									<?php
										$after_goto_compose = false;
										$after_goto_inbox = true;
										$after_goto_sent = false;
									?>
									<label class="radio-inline">
										{{Form::radio('after_goto','compose', Input::old('after_goto',$after_goto_compose), array('class' => '')) }}
										{{trans('mailbox.compose')}}
									</label>
									<label class="radio-inline">
										{{Form::radio('after_goto','inbox', Input::old('after_goto',$after_goto_inbox) , array('class' => '')) }}
										{{trans('mailbox.inbox')}}
									</label>
									<label class="radio-inline">
										{{Form::radio('after_goto','sent', Input::old('after_goto',$after_goto_sent) , array('class' => '')) }}
										{{trans('mailbox.sent')}}
									</label>
									<label class="error">{{{ $errors->first('after_goto') }}}</label>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-3 col-md-5">
									<label class="checkbox-inline">
										{{Form::checkbox('open_alert_needed','Yes',false)}}{{trans('mailbox.notify_when_open_message')}}
									</label>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-3 col-md-8">
									<!--<input type="submit" value="Send Mail" class="btn green" id="mailcompose_submit" name="mailcompose_submit">-->
									<button type="submit" class="btn green" id="mailcompose_submit" name="mailcompose_submit"><i class="fa fa-sign-in"></i> {{trans('mailbox.send_mail')}}</button>
								</div>
							</div>
						{{Form::close()}}
						<!-- END: COMPOSE MAIL -->
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		 var BASE = "{{ Request::root() }}";
		tinymce.init({
                menubar: "tools",
                selector: "textarea.fn_editor",
                mode : "exact",
                elements: "message_text",
                removed_menuitems: 'newdocument',
                apply_source_formatting : true,
                remove_linebreaks: false,
                height : 400,
                plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste emoticons jbimages"
                ],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons",
                relative_urls: false,
                remove_script_host: false
            });

		$(".mySel").select2({
		    allowClear:true
		 });
	</script>
@stop