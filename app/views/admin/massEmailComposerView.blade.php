@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
	<!-- BEGIN: INFO BLOCK -->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif
    
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
    @endif
    <!-- END: INFO BLOCK -->

	<div class="portlet box blue-madison">
		<!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-eye"></i> {{trans('admin/massEmail.title')}} {{trans('admin/massEmail.composer.title_view')}}
			</div>
            <button type="reset" name="cancel_mail" value="cancel_mail" class="btn default btn-xs green-stripe pull-right" onclick="javascript:location.href='{{ URL::to('admin/mass-email/list') }}'"> <i class="fa fa-chevron-left"></i> {{ trans("common.back")}}</button>
		</div>
		<!-- END: PAGE TITLE -->

		<!-- BEGIN: MAIL DETAIL -->
		<div class="portlet-body form" style="display: block;">
			@if(isset($mail_details[0]))
				{{ Form::model($mail_details[0], [
				'method' => 'post',
				'id' => 'massEmailComposerfrm', 'class' => 'form-horizontal'
				]) }}
				{{ Form::hidden('getusers', $mail_details[1],array('id' => 'getusers')) }}
				{{ Form::hidden('getusersname', $mail_details[2],array('id' => 'getusersname')) }}
			@else
				{{ Form::model($mail_details, [
				'method' => 'post',
				'id' => 'massEmailComposerfrm', 'class' => 'form-horizontal'
				]) }}
				{{ Form::hidden('getusers', null,array('id' => 'getusers')) }}
				{{ Form::hidden('getusersname', null,array('id' => 'getusersname')) }}
			@endif
				{{ Form::hidden('id',Input::get('id')) }}
				<fieldset>
					<div class="form-body admin-dl">
                    	<div class="dl-horizontal">
                            <dl>
                                <dt>{{ Form::label('tousers', trans('admin/massEmail.composer.to_users'), array('class' => "")) }}</dt>
                                <dd>
                                	<span>
                                        @if($mail_details[0]['send_to'] == 'all')
                                        	{{ trans('admin/massEmail.composer.all_users') }}
                                        @elseif($mail_details[0]['send_to'] == 'newsletter')
                                            {{ trans('admin/massEmail.composer.newsletter_subscriber') }}
                                    	@elseif($mail_details[0]['send_to'] == 'picked_user')
                                            {{ trans('admin/massEmail.composer.to_picked_users') }}
                                        @else
                                            <div id="userdiv"></div>
                                        @endif
                                    </span>
                                </dd>
                            </dl>
                            <dl>
                                <dt>{{ Form::label('send_on', trans('admin/massEmail.composer.send_on'), array('class' => '') ) }}</dt>
                                <dd>
                                    <span>{{ Form::label('send_on', CUtil::FMTDate($mail_details[0]['send_on'], 'Y-m-d', ''), array('class' => '') ) }}</span>
                                </dd>
                            </dl>
                            <dl>
                                <dt>{{ Form::label('subject', trans('admin/massEmail.composer.subject'), array('class' => '') ) }}</dt>
                                <dd>
                                    <span>{{ Form::label('subject', $mail_details[0]['subject'], array('class' => '') ) }}</span>
                                </dd>
                            </dl>
                            <dl>
                                <dt>{{ Form::label('from_name', trans('admin/massEmail.composer.from_name_label'), array('class' => '') ) }}</dt>
                                <dd>
                                    <span>{{ Form::label('from_name', $mail_details[0]['from_name'], array('class' => '') ) }}</span>
                                </dd>
                            </dl>
                            <dl>
                                <dt>{{ Form::label('from_email', trans('admin/massEmail.composer.from_email_label'), array('class' => '') ) }}</dt>
                                <dd>
                                    <span>{{ Form::label('from_email', $mail_details[0]['from_email'], array('class' => '') ) }}</span>
                                </dd>
                            </dl>
                            <dl>
                                <dt>{{ Form::label('content',trans('admin/massEmail.composer.content'), array('class' => '') ) }}</dt>
                                <dd><span>{{$mail_details[0]['content']}}</span></dd>
                            </dl>
                        </div>
					</div>
				</fieldset>
			{{ Form::close() }}
			<!-- END: MAIL DETAIL -->
		</div>
	</div>
@stop

@section('script_content')
<script type="text/javascript">
	window.onload = function(e){

		if($("#getusers").val()!='')
		{
			globaluserarray 		= $("#getusers").val().split(',');
			globalusernamearray 	= $("#getusersname").val().split(',');
		}
		else
		{
			globaluserarray 		= new Array();
			globalusernamearray		= new Array();
		}

		for(var i=0;i<globaluserarray.length;i++)
		{
				$("#userdiv").html($("#userdiv").html()+'<div class="userdivname" id="userdiv_'+globaluserarray[i]+'" >'+globalusernamearray[i]+'  </div>');
				$(".userdivname").click( function() {
				removeuser($(this).attr('id'))
				})
		}
	}
</script>
@stop
