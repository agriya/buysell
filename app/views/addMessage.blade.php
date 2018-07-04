@extends('popup')
@section('content')
    @if($d_arr['type'] == 'offer')
        <h1>{{trans("messaging.addMessage.make_an_offer")}}</h1>
    @else
        <h1>{{trans("messaging.addMessage.contact_member")}}</h1>
    @endif

    <div class="pop-content">
		@if(isset($d_arr['error_msg']))
			<div class="note note-danger">
				{{ $d_arr['error_msg'] }}
			</div>
            <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle"></i> Close</button>
            </a>
		@elseif(Session::has('success_message'))
			<div class="note note-success">
				{{ Session::get('success_message') }}
			</div>
            <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle"></i> {{ trans('common.close') }}</button>
            </a>
    	@else
        {{ Form::open(array('url' => 'shop/user/message/add/'.$d_arr['user_code'], 'class' => 'form-horizontal',  'id' => 'addmessage_frm')) }}
                {{ Form::hidden("user_code", $d_arr['user_code']) }}
                {{ Form::hidden("type", $d_arr['type']) }}
                <fieldset>
                    <div class="form-group {{{ $errors->has('subject') ? 'error' : '' }}}">
                        {{ Form::label('subject', trans('messaging.addMessage.subject_label'), array('class' => 'col-sm-3 control-label required-icon')) }}
                        <div class="col-sm-5">
                            {{  Form::text('subject', null, array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('subject') }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('message_text', trans('messaging.addMessage.description_label'), array('class' => 'col-sm-3 control-label required-icon')) }}
                        <div class="col-sm-7">
                            {{  Form::textarea('message_text', null, array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('message_text') }}}</label>
                        	<p class="text-muted">{{ trans('messaging.addMessage.code_note') }}</p>
						</div>
                    </div>

                    <div class="form-group label-none">
                        <label class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-8">
                            <button type="submit" class="btn btn-success">{{ trans('common.submit') }}</button>
                            <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                                <button type="reset" class="btn default">{{ trans('common.cancel') }}</button>
                            </a>
                        </div>
                    </div>
                </fieldset>
        {{ Form::close() }}
	</div>
   <script type="text/javascript">
   		var mes_required = '{{ Lang::get('auth/form.required') }}';
   		$("#addmessage_frm").validate({
			rules: {
				 subject: {
					required: true
				 },
				 message_text: {
						required: true
				 },
		  },
		messages: {
			subject: {
					required: mes_required
				},
			message_text: {
					required: mes_required
				},
		},
		/* For Contact info violation */
		submitHandler: function(form) {
			form.submit();
		}
	});
   </script>
  @endif
@stop