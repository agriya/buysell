@extends('adminPopup')
@section('content')
	<!-- popup title starts -->
	<div class="popup-title">
        <h1>{{ trans('admin/massEmail.send_preview_mail') }}</h1>
    </div>
    <!-- popup title end -->

    <!-- mass mail preview starts -->
    <div class="popup-form">
    	<?php $show_form = true; ?>
        @if(Session::has('success_message') && Session::get('success_message') != "")
            <div class="alert alert-success">{{ Session::get('success_message') }}</div>
            <?php $show_form = false; ?>
        @endif
        @if(Session::has('error_message') && Session::get('error_message') != "")
            <div class="alert alert-danger">{{	Session::get('error_message') }}</div>
        @endif
        @if($show_form)
            {{ Form::open(array('id'=>'selPreviewMailForm', 'method'=>'post','class' => 'form-horizontal border-type1' )) }}
            {{ Form::hidden('preview_mass_mail', '', array('id' => 'preview_mass_mail')) }}
            {{ Form::hidden('from_name', '', array('id' => 'from_name')) }}
            {{ Form::hidden('from_email', '', array('id' => 'from_email')) }}
            {{ Form::hidden('subject', '', array('id' => 'subject')) }}
            <div class="form-group">
                {{ Form::label('preview_email', trans('admin/massEmail.composer.email'), array('class' => 'control-label col-xs-2 required-icon')) }}
                <div class="col-xs-8">
                    {{ Form::text('preview_email', null, array('id' => 'preview_email','class' => 'form-control')) }}
                    {{ $errors->first('preview_email') }}
                </div>
            </div>

            <div class="form-group">
            	<div class="col-xs-8 col-xs-offset-2 mb40">
                    <button type="submit" name="update_access" class="btn btn-success btn-sm" id="update_access" value="update_access">
                        <i class="fa fa-check bigger-110"></i> {{trans("common.submit")}}
                    </button>
                    <button type="reset" name="submit_cancel" class="btn btn-sm" onclick="javascript: cancelDialog();">
                        <i class="fa fa-rotate-left bigger-110"></i> {{trans("common.cancel")}}
                    </button>
            	</div>
            </div>
            {{Form::close() }}
        @endif
    </div>
    <!-- mass mail preview end -->
@stop
@section('includescripts')
<script type="text/javascript">
	$(document).ready(function() {
		var mes_required = "{{trans('auth/form.required')}}";
		var preview_mail_content = parent.tinyMCE.get('content').getContent();
		$("#from_name").val(parent.$("#from_name").val());
		$("#from_email").val(parent.$("#from_email").val());
		$("#subject").val(parent.$("#subject").val());
		$("#preview_mass_mail").val(preview_mail_content);
			$("#selPreviewMailForm").validate({
				rules: {
					preview_email: {
						required: true,
						email: true
					}
			},
			messages: {
				preview_email: {
					required: mes_required
				}
			},
			submitHandler: function(form) {
					form.submit();
			}
		});
	});
	function cancelDialog() {
		parent.$.fancybox.close();
	}
</script>
@stop