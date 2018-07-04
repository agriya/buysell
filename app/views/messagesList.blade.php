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
				<h1>{{trans('mailbox.mail_box')}}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<div class="well">
				<div class="row">
					<div class="col-md-2 margin-bottom-30">
						<ul class="list-unstyled ver-inline-menu">
							<li class="@if($message_type == 'inbox')active@endif">
								<a href="{{URL::action('MessagingController@getIndex','inbox')}}"><i class="fa fa-inbox"></i>{{trans('mailbox.inbox')}} ({{$inbox_unread_count}})</a>
							</li>
							<li class="@if($message_type == 'sent') active @endif">
								<a href="{{URL::action('MessagingController@getIndex','sent')}}"><i class="fa fa-share"></i>{{trans('mailbox.sent')}}</a>
							</li>
							<li class="@if($message_type == 'saved') active @endif">
								<a href="{{URL::action('MessagingController@getIndex','saved')}}"><i class="fa fa-save"></i>{{trans('mailbox.saved')}}</a>
							</li>
							<li class="@if($message_type == 'trash') active @endif">
								<a href="{{URL::action('MessagingController@getIndex','trash')}}"><i class="fa fa-trash-o"></i>{{trans('mailbox.trash')}}</a>
							</li>
							<li><a href="{{ URL::action('MessagingController@getCompose') }}" class=""><i class="fa fa-edit"></i>{{trans('mailbox.compose')}}</a></li>
						</ul>
					</div>

					<div class="col-md-10">
						<h2 class="title-one">{{ trans('mailbox.'.$message_type) }}</h2>
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

						<!-- BEGIN: MAIL LISTS -->
						{{ Form::open(array('action' => array('MessagingController@postBulkMessageAction'), 'id'=>'messagesFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
							<div class="table-responsive margin-bottom-30">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th width="40"><input type="checkbox" name="select_all" id="select_all"></th>
											@if($message_type !='sent')
												<th class="clsFromTitle">{{trans('mailbox.from')}}</th>
											@endif
											@if($message_type !='inbox')
												<th class="clsFromTitle">{{trans('mailbox.sent_to')}}</th>
											@endif
											<th>{{trans('mailbox.status')}}</th>
											<th>{{trans('mailbox.subject')}}</th>
											<th>{{trans('mailbox.date')}}</th>
										</tr>
									</thead>

									<tbody>
										@if($messages && count($messages) > 0)
											<?php
											if($message_type =='saved'){
												$valid_actions = array(''=> trans('common.select_action'),'delete'=> trans('common.delete'));
												if($message_type=='delete')
												unset($valid_actions['delete']);
											}else{
												$valid_actions = array(''=> trans('common.select_action'),'save'=> trans('common.save'), 'delete'=> trans('common.delete'));
												if($message_type=='delete')
												unset($valid_actions['delete']);
											}
											?>
											@foreach($messages as $message)
												<?php
													if(!isset($all_user_details[$message->from_user_id]))
													$all_user_details[$message->from_user_id] = CUtil::getUserDetails($message->from_user_id);
													$from_user_details = $all_user_details[$message->from_user_id];

													if(!isset($all_user_image_details[$message->from_user_id]))
													$all_user_image_details[$message->from_user_id] = CUtil::getUserPersonalImage($message->from_user_id, 'small');
													$from_image_details = $all_user_image_details[$message->from_user_id];


													if(!isset($all_user_details[$message->to_user_id]))
													$all_user_details[$message->to_user_id] = CUtil::getUserDetails($message->to_user_id);
													$to_user_details = $all_user_details[$message->to_user_id];

													if(!isset($all_user_image_details[$message->to_user_id]))
													$all_user_image_details[$message->to_user_id] = CUtil::getUserPersonalImage($message->to_user_id, 'small');
													$to_image_details = $all_user_image_details[$message->to_user_id];
												?>
												<tr>
													<td>{{Form::checkbox('message_ids[]',$message->id, false, array('class' => 'checkbox_value'))}}</td>
													@if($message_type !='sent')
														<td>
															<a href="{{$from_user_details['profile_url']}}" class="imguserborsm-56X56">
																<img alt="{{$from_user_details['display_name']}}" src="{{$from_image_details['image_url']}}" {{$from_image_details['image_attr']}}>
															</a>
															<p><a href="{{$from_user_details['profile_url']}}">{{$from_user_details['user_name']}}</a></p>
														</td>
													@endif
													@if($message_type !='inbox')
														<td>
															<a href="{{$to_user_details['profile_url']}}" class="imguserborsm-56X56">
																<img alt="{{$to_user_details['display_name']}}" src="{{$to_image_details['image_url']}}" {{$to_image_details['image_attr']}}>
															</a>
															<p><a href="{{$to_user_details['profile_url']}}">{{$to_user_details['user_name']}}</a></p>
														</td>
													@endif
													<td>
														<span class="selReadMail">
															<?php $status = 'read'; ?>
															@if($message_type == 'inbox')
																<?php $status = $message->to_message_status; ?>
															@elseif($message_type == 'sent')
																<?php $status = $message->from_message_status; ?>
															@else
																@if($message->to_user_id == $logged_user_id)
																	<?php $status = $message->to_message_status; ?>
																@else
																	<?php $status = $message->from_message_status; ?>
																@endif
															@endif
															{{trans('mailbox.'.strtolower($status))}}
														</span>
													</td>
													<td>
														<a href="{{URL::action('MessagingController@getViewMessage',$message->id).'?message_type='.$message_type}}">
															@if(strtolower($status)=='unread')
																<strong>{{$message->subject}}</strong>
															@else
																{{$message->subject}}
															@endif
														</a>
													</td>
													<td>
														<p class="margin-bottom-5">
															<i class="fa fa-calendar text-muted"></i> {{CUtil::FMTDate($message->date_added, "Y-m-d H:i:s", "")}}
														</p>
														<p><i class="fa fa-clock-o text-muted"></i> {{CUtil::FMTDate($message->date_added, "Y-m-d H:i:s", "h:i A")}}</p>
													</td>
												</tr>
											@endforeach
											<tr>
												<td colspan="6">
													<p class="pull-left margin-top-10 margin-right-10">
														{{Form::select('action',$valid_actions,'',array('class'=>'form-control', 'id'=>'message_action')) }}
														{{Form::hidden('message_type',$message_type)}}
													</p>
													<p class="pull-left margin-top-10">
														<input type="button" value="{{ Lang::get('mailbox.submit') }}" class="btn green" id="mail_action" name="mail_action" onclick="doAction(this)">
													</p>
												</td>
											</tr>
										@else
											<tr>
												<td colspan="6"><p class="alert alert-info margin-0">{{ Lang::get('mailbox.no_message_in_folder')}}</p></td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
						{{Form::close()}}

						@if($messages && count($messages) > 0)
							<div class="text-right">
								{{ $messages->links() }}
							</div>
						@endif
						<!-- END: MAIL LISTS -->

						<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
							<span class="ui-icon ui-icon-alert"></span>
							<span id="dialog-product-confirm-content" class="show"></span>
						</div>

						<div id="address_confirm_primary" class="confirm-dialog-delete" title="" style="display:none;">
							<span class="ui-icon ui-icon-alert"></span>
							<span id="dialog-product-confirm-address" class="show"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.attr('checked', 'checked');
			}
			else
			{
				checkboxes.removeAttr('checked');
			}
		});

	    $('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.show_search_filters') }} <i class="fa fa-caret-down"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.hide_search_filters') }} <i class="fa fa-caret-up"></i>');
	        }
	        return false;
	    });

	   function doAction(message_id)
		{
			if ($('.checkbox_value:checked').length <= 0) {
				bootbox.alert('{{ Lang::get('mailbox.select_the_checkbox') }}');
				return false;
			}
			selected_action = $("#message_action").val();

			if(selected_action == 'save')
			{
				$('#dialog-product-confirm-address').html('{{ Lang::get('mailbox.are_you_sure_want_to_save_this_mail') }}');
			}
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-address').html('{{ Lang::get('mailbox.are_you_sure_want_to_delete_this_mail') }}');
			}
			if(selected_action != 'save' && selected_action != 'delete')
			{
				bootbox.alert('{{ Lang::get('mailbox.please_select_an_action') }}');
				return false;
			}

			$("#address_confirm_primary").dialog({ title: '{{ Lang::get('mailbox.mailbox_name') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
					$(this).dialog("close");
					$('#message_action').val(selected_action);
					document.getElementById("messagesFrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		$(".fn_changeStatus").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	</script>
@stop