@extends('admin')
@section('content')
	<-- SUCCESS INFO STARTS -->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{ Session::get('success_message') }}</div>
    @endif

    @if (Session::has('error_message') && Session::get('error_message') != "")
    	<div class="note note-danger">{{	Session::get('error_message') }}</div>
    @endif
    <!-- SUCCESS INFO END -->

    {{ Form::model($news_letter_details, ['method' => 'post', 'id' => 'addnewsletterfrm', 'class' => 'form-horizontal']) }}
        <div class="portlet box blue-hoki">
            <!-- TITLE STARTS -->
            <div class="portlet-title">
                <div class="caption">
					<i class="fa fa-plus-circle"></i> {{Lang::get('admin/newsletter.add_newsletter')}}
                </div>
                <a href="{{ URL::action('AdminNewsletterController@getIndex') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe pull-right">
                    <i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}
                </a>
            </div>
            <!-- TITLE END -->

            <div class="portlet-body form">
            	<!-- ADD MEMBER BASIC DETAILS STARTS -->
                <div class="form-body">

                	<p class="note note-info">Variable you can use in subject and content : <strong>VAR_SITE_NAME</strong>, <strong>VAR_USERNAME</strong>, <strong>VAR_EMAIL</strong></p>
                    <div class="form-group">
                        {{ Form::label('username', Lang::get('admin/newsletter.username'), array('class' => 'control-label col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('username', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('username') }}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}" >
                        {{ Form::label('first_name', Lang::get('admin/newsletter.first_name'), array('class' => 'control-label col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('first_name', null, array('class' => 'form-control', 'rows' => '7')) }}
                            <label class="error">{{ $errors->first('first_name') }}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
                        {{ Form::label('last_name', Lang::get('admin/newsletter.last_name'), array('class' => 'control-label col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('last_name', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('last_name') }}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
                        {{ Form::label('email', Lang::get('admin/newsletter.email'), array('class' => 'control-label col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('email', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('email') }}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('from_date') ? 'error' : '' }}}">
                        {{ Form::label('doj', trans("admin/newsletter.doj"), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-8">
                            <div class="input-group input-medium date date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                {{ Form::text('doj_from_date', Input::old('doj_from_date', Input::get('doj_from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
								<label for="date_added_to" class="input-group-addon">To</label>
                                {{ Form::text('doj_to_date', Input::old('doj_to_date', Input::get('doj_to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
                            </div>
                            <label class="error">{{{ $errors->first('doj_from_date') }}}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('last_login') ? 'error' : '' }}}">
                        {{ Form::label('last_login', Lang::get('admin/newsletter.last_login'), array('class' => 'control-label col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('last_login', null, array('class' => 'form-control', 'id'=>'last_login')) }}
                            <label class="error">{{ $errors->first('last_login') }}</label>
                        </div>
                    </div>

					<div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                        {{ Form::label('status', Lang::get('admin/newsletter.status'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-8">
                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                	{{Form::checkbox('status[]', 'Active', false) }}
                                    {{ Form::label('status_active', trans('common.active')) }}
                                </label>
                                <label class="checkbox-inline">
                                	{{Form::checkbox('status[]', 'InActive', false) }}
                                    {{ Form::label('status_active', trans('common.inactive')) }}
                                </label>
                                <label class="checkbox-inline">
                                	{{Form::checkbox('status[]', 'Blocked', false) }}
                                    {{ Form::label('status_active', trans('common.blocked')) }}
                                </label>
                                <label class="error">{{{ $errors->first('status') }}}</label>
                            </div>
                        </div>
                    </div>


                    <div class="form-group {{{ $errors->has('subject') ? 'error' : '' }}}" >
                        {{ Form::label('subject', Lang::get('admin/newsletter.subject'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('subject', null, array('class' => 'form-control', 'rows' => '7')) }}
                            <label class="error">{{ $errors->first('subject') }}</label>
                        </div>
                    </div>


                    <div class="form-group {{{ $errors->has('message') ? 'error' : '' }}}" >
                        {{ Form::label('message', Lang::get('admin/newsletter.message'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-7">
                            {{ Form::textarea('message', null, array('class' => 'form-control', 'rows' => '7')) }}
                            <label class="error">{{ $errors->first('message') }}</label>
                        </div>
                    </div>

                </div>
                <!-- ADD MEMBER BASIC DETAILS END -->

                <!-- ACTIONS STARTS -->
                <div class="form-actions fluid">
                    <div class="col-md-offset-3 col-md-5">
                        <button type="submit" name="add_tax" value="add_tax" class="btn green">
                         	<i class="fa fa-check"></i> {{ Lang::get('common.submit') }}
                        </button>
                        <button type="reset" name="reset_members" value="reset_members" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminNewsletterController@getIndex') }}'">
                        	<i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}
                        </button>
                    </div>
                </div>
                <!-- ACTIONS END -->
            </div>
       </div>
    {{ Form::close() }}
    <div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var err_msg = '';
        var messageFunc = function() { return err_msg; };
        var mes_required = "{{ Lang::get('auth/form.required') }}";
        $("#addnewsletterfrm").validate({
            rules: {
                subject: {
                    required: true,
                },
                message: {
                    required: true,
                },
            },
            messages: {
                subject: {
                    required: mes_required
                },
                message: {
                    required: mes_required
                },
            },
            submitHandler: function(form)
			{
				$('#dialog-confirm-content').html('{{ trans('admin/newsletter.confirm_add_newsletter') }}');
                $("#dialog-confirm").dialog({ title: '{{ trans('admin/newsletter.newsletter_head') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$(this).dialog("close");
							form.submit();
						}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
					}
				});
            },

            highlight: function (element) { // hightlight error inputs
               $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            }
        });

        $(function() {
            $('#from_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#to_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });
            $('#last_login').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });
        });
    </script>
@stop
