@extends('admin')
{{ $header->setMetaTitle(trans('admin/manageMembers.admin_manage_credits')) }}
@section('content')
	<!-- NOTIFICATION STARTS -->
	@include('notifications')
  	<!-- ALERT BLOCK STARTS -->
    @if(isset($success_msg) && $success_msg != "")
	    <div class="note note-success">{{ $success_msg }}</div>
	@elseif(isset($error_msg) && $error_msg != "")
	    <div class="note note-danger">{{ $error_msg }}</div>
	@endif
    <!-- ALERT BLOCK END -->

	<!-- ADD/EDIT CREDIT BLOCK STARTS -->
	{{ Form::model($credit_details, ['url' => $d_arr['post_url'],'method' => 'post','id' => 'add_credits_frm', 'class' => 'form-horizontal']) }}
		{{ Form::hidden('is_invoice_generated', $d_arr['is_invoice_generated'], array("id" => "is_invoice_generated")) }}
		<div class="portlet box blue-madison">
			<!--- SEARCH TITLE STARTS --->
			<div class="portlet-title">
				<div class="caption">
					@if($d_arr['mode'] == 'edit')
						<i class="fa fa-credit-card"><sup class="fa fa-pencil font11"></sup></i> {{ trans('admin/manageMembers.edit_credits') }} {{ Lang::get('common.for')}} "{{ $user_details['display_name'] }}"
					@else
						<i class="fa fa-credit-card"><sup class="fa fa-plus font11"></sup></i> {{ trans('admin/manageMembers.add_credits') }}  {{ Lang::get('common.for')}} "{{ $user_details['display_name'] }}"
					@endif
				</div>
				<a href="{{ URL::to('admin/users') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe pull-right">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}
				</a>
			</div>
			<!--- SEARCH TITLE END --->
			<div class="portlet-body form">
				<div class="form-body">
					@if($d_arr['mode'] == 'edit')
						<?php $amount_class_arr = array("class" => "form-control", "id" => "amount", "disabled" => "disabled"); ?>
					@else
						<?php $amount_class_arr = array("class" => "form-control", "id" => "amount"); ?>
					@endif
					<input type="hidden" name="user_id" id="user_id" value="{{ Input::get('user_id') }}"  />
					<input type="hidden" name="credit_id" id="credit_id" value="{{ $credit_id }}"  />
					@if($d_arr['mode'] == 'edit')
						<input type="hidden" name="amount" id="amount" value="{{ $credit_details['amount'] }}"  />
					@endif

					<div class="form-group {{{ $errors->has('amount') ? 'error' : '' }}}">
						{{ Form::label('amount', trans('admin/manageMembers.amount_to_credit'), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							<div class="input-group">
								<span class="input-group-addon">{{ Config::get('generalConfig.site_default_currency') }}</span>
								{{ Form::text('amount', null, $amount_class_arr) }}
							</div>
							<label for="amount" generated="true" class="error">{{{ $errors->first('amount') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('user_notes') ? 'error' : '' }}}">
						{{ Form::label('user_notes', trans('admin/manageMembers.user_notes'), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-7">
							{{ Form::textarea('user_notes', null, array('class' => 'form-control')); }}
							<label class="error">{{{ $errors->first('user_notes') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('admin_notes') ? 'error' : '' }}}">
						{{ Form::label('admin_notes', trans('admin/manageMembers.admin_notes'), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-7">
							{{ Form::textarea('admin_notes', null, array('class' => 'form-control')); }}
							<label class="error">{{{ $errors->first('admin_notes') }}}</label>
						</div>
					</div>

					<!--
					<div class="form-group {{{ $errors->has('paid') ? 'error' : '' }}}">
						<div class="col-md-7 col-md-offset-3">
							<div class="checkbox-list">
								<?php
									$paid = ( (isset($credit_details['paid']) && $credit_details['paid'] == 'Yes') || (Input::old('paid')) ) ? 'checked="checked"' : '';
									$generate_invoice = ( (isset($credit_details['generate_invoice']) && $credit_details['generate_invoice'] == 'Yes') || (Input::old('generate_invoice')) ) ? 'checked="checked"' : '';
									$generate_invoice_disabled = ( isset($credit_details['generate_invoice']) && $credit_details['generate_invoice'] == 'Yes') ? 'disabled="disabled"' : '';
								?>
								<label class="checkbox-inline">
									<input name="paid" {{$paid}} type="checkbox" value="Yes" id="paid">
									{{ Form::label('paid', trans('admin/manageMembers.paid'), array('class' => 'no-margin')) }}
								</label>
								<label class="checkbox-inline">
									<input name="generate_invoice" {{$generate_invoice}} {{$generate_invoice_disabled}} type="checkbox" value="Yes" id="generate_invoice">
									{{ Form::label('generate_invoice', trans('admin/manageMembers.generate_invoice'), array('class' => 'no-margin')) }}
								</label>
								<label class="error">{{{ $errors->first('paid') }}}</label>
								<label class="error">{{{ $errors->first('generate_invoice') }}}</label>
							</div>
						</div>
					</div>
					-->

					<div class="form-group {{{ $errors->has('notify_user') ? 'error' : '' }}}">
						<div class="col-md-7 col-md-offset-3">
							<?php
								$notify_user = true;
								$notify_user = ($notify_user) ? 'checked="checked"' : '';
							?>
							<label class="checkbox">
								<input name="notify_user" {{$notify_user}} type="checkbox" value="Yes" id="notify_user">
								{{ Form::label('notify_user', trans('admin/manageMembers.notify_user_by_mail')) }}
							</label>
							<label class="error">{{{ $errors->first('notify_user') }}}</label>
						</div>
					</div>
				</div>
				<div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-8">
						<button type="submit" name="add_credits" class="btn green" id="add_credits" value="add_credits"><i class="fa fa-check"></i> {{trans("common.submit")}}</button>
						<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/users/manage-credits?user_id='.Input::get('user_id') ) }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}</button>
					</div>
				</div>
			</div>
		</div>
	{{ Form::close() }}
	<!-- ADD/EDIT CREDIT BLOCK END -->

	<!-- WITHDRAW REQUEST LIST STARTS -->
	{{ Form::open(array('id'=>'creditsListfrm', 'method'=>'get', 'class' => 'form-horizontal' )) }}
		<div class="portlet box blue-hoki">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-list"><sup class="fa fa-credit-card font11"></sup></i>{{ trans('admin/manageMembers.credit_list') }}
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="115">{{ trans('admin/manageMembers.credit_id') }}</th>
								<th width="130">{{ trans('admin/manageMembers.amount') }}</th>
								<th class="col-md-3">{{ trans('admin/manageMembers.user_notes') }}</th>
								<th class="col-md-3">{{ trans('admin/manageMembers.admin_notes') }}</th>
								<th class="col-md-2">{{ trans('common.date_added') }}</th>
							</tr>
						</thead>
						<tbody>
							@if(count($credit_list) > 0)
								@foreach($credit_list as $r_key => $det)
									<tr>
										<td>{{ $det['credit_id'] }}</td>
										<td>{{ CUtil::convertAmountToCurrency($det['amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</td>
										<td>
											<div id="selMsgLessUser_{{ $det['credit_id'] }}">
												{{ nl2br(e(mb_substr($det['user_notes'], 0, 120))) }}
												@if(mb_strlen($det['user_notes']) > 120)
													<p class="text-right">&raquo; <a onclick="return callShowMoreUser('more', '{{ $det['credit_id'] }}')">{{trans('common.more')}}...</a></p>
												@endif
											</div>
											<div id="selMsgMoreUser_{{ $det['credit_id'] }}" style="display:none">
												{{ nl2br(e($det['user_notes'])) }}
												<p class="text-right">&laquo; <a onclick="return callShowMoreUser('less', '{{ $det['credit_id'] }}')">... {{trans('common.less')}}</a></p>
											</div>
										</td>
										<td>
											<div id="selMsgLess_{{ $det['credit_id'] }}">
												{{ nl2br(e(mb_substr($det['admin_notes'], 0, 120))) }}
												@if(mb_strlen($det['admin_notes']) > 120)
													<p class="text-right">&raquo; <a onclick="return callShowMore('more', '{{ $det['credit_id'] }}')">{{trans('common.more')}}...</a></p>
												@endif
											</div>
											<div id="selMsgMore_{{ $det['credit_id'] }}" style="display:none">
												{{ nl2br(e($det['admin_notes'])) }}
												<p class="text-right">&laquo; <a onclick="return callShowMore('less', '{{ $det['credit_id'] }}')">... {{trans('common.less')}}</a></p>
											</div>
										</td>
										<td>
											@if($det['date_paid'] != '0000-00-00 00:00:00')
												<p><span class="text-muted">{{ CUtil::FMTDate($det['date_paid'], "Y-m-d H:i:s", "") }}</span></p>
											@endif
										</td>
									</tr>
								@endforeach
						   @else
								<tr><td colspan="5"><p class="alert alert-info">{{ trans('admin/manageMembers.no_credits_found') }}</p></td></tr>
						   @endif
						</tbody>
					</table>
				</div>
				@if(count($credit_list) > 0)
					<div class="text-right">
						{{ $credit_list->appends(array('user_id' => Input::get('user_id')))->links() }}
					</div>
				@endif
			</div>
		</div>
	{{ Form::close() }}
	<!-- WITHDRAW REQUEST LIST END -->
@stop

@section('script_content')
	<script type="text/javascript">
		var mes_required = '{{ Lang::get('auth/form.required') }}';
        $(document).ready(function() {
        	jQuery.validator.addMethod("decimallimit", function (value, element) {
		        return this.optional(element) || /^[0-9]*(\.\d{0,2})?$/i.test(value);
		    }, "Only two decimals allowed");

            $("#add_credits_frm").validate({
                rules: {
                	amount: {
		                required: true,
		                number: true,
		                decimallimit: true
		            },
                    user_notes: {
                        required: true
                    },
                    admin_notes : {
                        required: true
                    }
                },
                messages: {
                	amount: {
                		required: mes_required,
                		number: jQuery.format("{{trans('common.number_validation')}}"),
                		decimallimit: jQuery.format("{{trans('common.decimal_validation')}}"),
                    },
                    user_notes: {
                        required: mes_required
                    },
                    admin_notes : {
                        required: mes_required
                    }
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
        });

		function callShowMore(act, ident) {
			$("#selMsgLess_"+ident).toggle('slow');
			$("#selMsgMore_"+ident).toggle('slow');
		}
		function callShowMoreUser(act, ident) {
			$("#selMsgLessUser_"+ident).toggle('slow');
			$("#selMsgMoreUser_"+ident).toggle('slow');
		}
	</script>
@stop