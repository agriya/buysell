@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT MENU -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT MENU -->
		</div>

		<div class="col-md-10 msg-view">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<h1>MailBox</h1>
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
							<li class=""><a href="{{ URL::action('MessagingController@getCompose') }}"><i class="fa fa-edit"></i>{{trans('mailbox.compose')}}</a></li>
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

						@if($message_details && count($message_details))
							<?php
								$common_msg_type='inbox';
								if($message_details->from_user_id == $logged_user_id)
								{
									$common_msg_type='sent';
									$user_details = CUtil::getUserDetails($message_details->to_user_id);
									$user_image_details = CUtil::getUserPersonalImage($message_details->to_user_id, 'thumb');
								}
								else
								{
									$user_details = CUtil::getUserDetails($message_details->from_user_id);
									$user_image_details = CUtil::getUserPersonalImage($message_details->from_user_id, 'thumb');
								}
							?>
							<!-- BEGIN: INBOX LIST MAIL -->
							<div class="clearfix" id="selShowMail">
								<div class="pull-left" id="selMisNavLinks">
									<ul class="list-unstyled list-inline">
										<li><a class="btn btn-sm btn-default" onclick="return true" href="{{URL::action('MessagingController@getCompose').'?action=reply&message_id='.$message_details->id}}">{{trans('mailbox.reply')}}</a></li>
                                        
										<li><a class="btn btn-sm btn-default" onclick="return true" href="{{URL::action('MessagingController@getCompose').'?action=forward&message_id='.$message_details->id}}">{{trans('mailbox.forward')}}</a></li>
                                        
										@if($message_type!='saved' && $message_status!='Saved')
                                            <li><a class="btn btn-sm btn-default" href="javascript:;" onclick="doAction('{{ $message_details->id }}', 'save')">{{trans('mailbox.save')}}</a></li>
										@endif
                                        
										<li><a class="btn btn-sm btn-default" href="javascript:;" onclick="doAction('{{ $message_details->id }}', 'delete')">{{trans('mailbox.delete')}}</a></li>
									</ul>
								</div>

								<div class="pull-right">
									<ul class="list-unstyled list-inline margin-top-10">
										@if(isset($previous_next_message_ids['prev_id']) && ($previous_next_message_ids['prev_id']!='' || $previous_next_message_ids['prev_id']>0))
											<li><a href="{{URL::action('MessagingController@getViewMessage',$previous_next_message_ids['prev_id']).'?message_type='.$message_type}}">
											<span class="badge bg-blue-madison">{{trans('mailbox.previous')}}</span></a></li>
										@else
											<li><span class="badge bg-grey-cascade">{{trans('mailbox.previous')}}</span></li>
										@endif

										@if(isset($previous_next_message_ids['next_id']) && ($previous_next_message_ids['next_id']!='' || $previous_next_message_ids['next_id']>0))
											<li><a href="{{URL::action('MessagingController@getViewMessage',$previous_next_message_ids['next_id']).'?message_type='.$message_type}}">
											<span class="badge bg-blue-madison">{{trans('mailbox.next')}}</span></a></li>
										@else
											<li><span class="badge bg-grey-cascade">{{trans('mailbox.next')}}</span></li>
										@endif
									</ul>
								</div>
							</div>
                            
							<div class="table-responsive">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>@if($common_msg_type=='inbox'){{trans('mailbox.from').":"}} @else {{trans('mailbox.sent_to').":"}} @endif </th>
											<th>{{trans('mailbox.message')}}</th>
										</tr>
									</thead>

									<tbody>
										<tr>
											<td id="selPhotoGallery">
												<a href="{{$user_details['profile_url']}}" class="imguserborsm-56X56">
													<img title="{{$user_details['display_name']}}" alt="{{$user_details['user_name']}}" src="{{$user_image_details['image_url']}}">
												</a>
												<p><a href="{{$user_details['profile_url']}}">{{$user_details['user_name']}}</a></p>
											</td>
											<td>
												<p class="text-muted">
                                                    {{CUtil::FMTDate($message_details->date_added, "Y-m-d H:i:s", "")}} {{CUtil::FMTDate($message_details->date_added, "Y-m-d H:i:s", "h:i A")}}
												</p>
												<p><strong>{{trans('mailbox.subject')}}: </strong>  {{$message_details->subject}}</p>
												<p></p>
												<p>{{$message_details->message_text}}</p>
												<p></p>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
                            
							<div class="clearfix margin-top-10" id="selShowMail">
								<div class="pull-left" id="selMisNavLinks">
									<ul class="list-unstyled list-inline">
										<li><a class="btn btn-sm btn-default" onclick="return true" href="{{URL::action('MessagingController@getCompose').'?action=reply&message_id='.$message_details->id}}">{{trans('mailbox.reply')}}</a></li>
										<li><a class="btn btn-sm btn-default" onclick="return true" href="{{URL::action('MessagingController@getCompose').'?action=forward&message_id='.$message_details->id}}">{{trans('mailbox.forward')}}</a></li>
										@if($message_type!='saved' && $message_status!='Saved')
											<li><a class="btn btn-sm btn-default" href="javascript:;" onclick="doAction('{{ $message_details->id }}', 'save')">{{trans('mailbox.save')}}</a></li>
										@endif
										<li><a class="btn btn-sm btn-default" href="javascript:;" onclick="doAction('{{ $message_details->id }}', 'delete')">{{trans('mailbox.delete')}}</a></li>
									</ul>
								</div>

								<div class="pull-right">
									<ul class="list-unstyled list-inline margin-top-10">
										@if(isset($previous_next_message_ids['prev_id']) && ($previous_next_message_ids['prev_id']!='' || $previous_next_message_ids['prev_id']>0))
											<li><a href="{{URL::action('MessagingController@getViewMessage',$previous_next_message_ids['prev_id']).'?message_type='.$message_type}}">
											<span class="badge bg-blue-madison">{{trans('mailbox.previous')}}</span></a></li>
										@else
											<li><span class="badge bg-grey-cascade">{{trans('mailbox.previous')}}</span></li>
										@endif

										@if(isset($previous_next_message_ids['next_id']) && ($previous_next_message_ids['next_id']!='' || $previous_next_message_ids['next_id']>0))
											<li><a href="{{URL::action('MessagingController@getViewMessage',$previous_next_message_ids['next_id']).'?message_type='.$message_type}}">
											<span class="badge bg-blue-madison">{{trans('mailbox.next')}}</span></a></li>
										@else
											<li><span class="badge bg-grey-cascade">{{trans('mailbox.next')}}</span></li>
										@endif
									</ul>
								</div>
							</div>
							<!-- END: INBOX LIST MAIL -->
						@endif
					</div>
				</div>
                
				<div id="address_confirm_primary" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog-product-confirm-address" class="show"></span>
				</div>
                
				{{ Form::open(array('action' => array('MessagingController@postBulkMessageAction'), 'id'=>'messagesFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
					{{Form::hidden('message_ids[]',$message_id)}}
					{{Form::hidden('action','',array('id'=>'message_action'))}}
				{{ Form::close()}}
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

		function doAction(message_id, selected_action)
		{

			if(selected_action == 'save')
			{
				$('#dialog-product-confirm-address').html('{{ "Are you sure want to save this mail?" }}');
			}
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-address').html('{{ "Are you sure want to delete this mail?" }}');
			}

			$("#address_confirm_primary").dialog({ title: '{{ "Mailbox" }}', modal: true,
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
	</script>
@stop