@extends('adminPopup')
@section('content')
    @if($request_action == 'set_as_paid')
        <h1>{{ trans('admin/manageWithdrawals.withdrawallist_approve_request_title') }}</h1>
    @elseif($request_action == 'decline')
        <h1>{{ trans('admin/manageWithdrawals.withdrawallist_decline_request_title') }}</h1>
    @endif

    <div class="pop-content">
        @if(isset($success_msg) && $success_msg != "")
            <div class="note note-success">{{ $success_msg }}</div>
            <button type="reset" name="close_change_request" value="close_change_request" class="btn red btn-sm mt10" onclick="javascript:closeDialog();">
                <i class="fa fa-times-circle"></i> {{ trans('admin/manageWithdrawals.withdrawallist_close') }}
             </button>
        @elseif(isset($error_msg) && $error_msg != "")
            <div class="note note-danger">{{ $error_msg }}</div>
            <button type="reset" name="close_change_request" value="close_change_request" class="btn red btn-sm mt10" onclick="javascript:cancelDialog();">
                <i class="fa fa-times-circle"></i> {{ trans('admin/manageWithdrawals.withdrawallist_close') }}
            </button>
        @else

            @if($request_action == 'set_as_paid')
                {{ Form::open(array('id'=>'setAsPaidfrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <input type="hidden" name="request_id" id="request_id" value="{{ $request_id }}"  />
                    <input type="hidden" name="request_action" id="request_action" value="{{ $request_action }}"  />
                    <fieldset>
                    	<?php $user_wallet_detail = CUtil::getWalletAccountDetails($request_details['user_id']);?>
                    	<div class="form-group {{{ $errors->has('withdrawal_amount') ? 'error' : '' }}}">
                            {{ Form::label('user_wallet_amount', trans('admin/manageWithdrawals.withdrawallist_user_wallet_amount') , array('class' => 'col-sm-3 control-label')) }}
                            <div class="col-sm-5 form-control-static">
                            	@foreach($user_wallet_detail as $other_bal)
                            		<p>
                                        {{ CUtil::convertAmountToCurrency($other_bal['amount'], Config::get('generalConfig.site_default_currency'), '', true) }}
                            		</p>
						        @endforeach
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('withdrawal_amount') ? 'error' : '' }}}">
                            {{ Form::label('withdrawal_amount', trans('admin/manageWithdrawals.withdrawallist_withdrawal_amount') , array('class' => 'col-sm-3 control-label')) }}
                            <div class="col-sm-5 form-control-static">
                                <?php
                                    $amt = ($request_details['amount'] - $request_details['fee']);
                                ?>
                                {{ CUtil::convertAmountToCurrency($amt, Config::get('generalConfig.site_default_currency'), '', true)}}
                                @if($request_details['fee'] > 0)
                                	(Requested amount: <strong>{{ $request_details['amount'] }}</strong>, Fees: <strong>{{ $request_details['fee']}}</strong>)
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('paid_notes') ? 'error' : '' }}}">
                            {{ Form::label('paid_notes', trans('admin/manageWithdrawals.withdrawallist_paid_notes'), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-7">
                                {{ Form::textarea('paid_notes', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('paid_notes') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('admin_notes') ? 'error' : '' }}}">
                            {{ Form::label('admin_notes', trans('admin/manageWithdrawals.withdrawallist_admin_notes'), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-7">
                                {{ Form::textarea('admin_notes', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('admin_notes') }}}</label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="form-group label-none">
                    	<label class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-8">
                            <button type="submit" name="set_paid" class="btn btn-success" id="set_paid" value="set_paid"><i class="fa fa-check"></i> {{trans("common.submit")}}</button>
                           <button type="reset" name="submit_cancel" class="btn default" onclick="javascript: cancelDialog();"><i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
                        </div>
                    </div>
                {{ Form::close() }}

            @elseif($request_action == 'decline')
                {{ Form::open(array('id'=>'declaineRequestfrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <input type="hidden" name="request_id" id="request_id" value="{{ $request_id }}"  />
                    <input type="hidden" name="request_action" id="request_action" value="{{ $request_action }}"  />
                    <fieldset>
                        <div class="form-group {{{ $errors->has('withdrawal_amount') ? 'error' : '' }}}">
                            {{ Form::label('withdrawal_amount', trans('admin/manageWithdrawals.withdrawallist_withdrawal_amount'), array('class' => 'col-sm-3 control-label')) }}
                            <div class="col-sm-5 form-control-static">
                                {{ CUtil::convertAmountToCurrency($request_details['amount'], Config::get('generalConfig.site_default_currency'), '', true)}}
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('cancelled_reason') ? 'error' : '' }}}">
                            {{ Form::label('cancelled_reason', trans('admin/manageWithdrawals.user_notes'), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-7">
                                {{ Form::textarea('cancelled_reason', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('cancelled_reason') }}}</label>
                            </div>
                        </div>
                          <div class="form-group {{{ $errors->has('admin_notes') ? 'error' : '' }}}">
                            {{ Form::label('admin_notes', trans('admin/manageWithdrawals.withdrawallist_admin_notes'), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-7">
                                {{ Form::textarea('admin_notes', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('admin_notes') }}}</label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="form-group label-none">
                    	<label class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-8">
                            <button type="submit" name="decline_request" class="btn btn-success" id="set_paid" value="decline"><i class="fa fa-check"></i> {{trans("common.submit")}}</button>
                           <button type="reset" name="submit_cancel" class="btn default" onclick="javascript: cancelDialog();"><i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
        @endif
    </div>

	<script type="text/javascript">
        var mes_required = '{{ Lang::get('auth/form.required') }}';
        $(document).ready(function() {
            $("#declaineRequestfrm").validate({
                rules: {
                    admin_notes : {
                        required: true
                    },
                    cancelled_reason:{
                        required: true
                    }
                },
                messages: {
                    admin_notes : {
                        required: mes_required
                    },
                    cancelled_reason : {
                        required: mes_required
                    }
                }
            });

            $("#setAsPaidfrm").validate({
                rules: {
                    paid_notes: {
                        required: true
                    },
                    admin_notes : {
                        required: true
                    }
                },
                messages: {
                    paid_notes: {
                        required: mes_required
                    },
                    admin_notes : {
                        required: mes_required
                    }
                }
            });
        });
        function closeDialog()
        {
            parent.window.location.href = "{{ Url::to('admin/withdrawals') }}";
        }
        function cancelDialog() {
            parent.$.fancybox.close();
        }
    </script>
@stop