@extends('popup')
@section('content')
    <h1>{{trans('myaccount/form.my-withdrawals.cancel_withdrawal_request')}}</h1>
    <div class="pop-content">
        @if(Session::has('success_msg') != '')
            <script type="text/javascript">
                parent.location.reload();
            </script>
            <div class="note note-success">{{	Session::get('success_msg') }}</div>
            <button type="reset" name="cancel_doctype" value="cancel_doctype" class="btn red margin-top-10" onclick="javascript:closeFancyPopUp();"><i class="fa fa-times-circle"></i> {{trans('common.close')}}</button>
        @elseif(isset($success_msg) && $success_msg !="")
            <script type="text/javascript">
                parent.location.reload();
            </script>
            <div class="note note-success">{{ $success_msg }}</div>
            <button type="reset" name="close_change_request" value="close_change_request" class="btn red margin-top-10" onclick="javascript:closeFancyPopUp();"><i class="fa fa-times-circle"></i> Close</button>
        @else

            {{ Form::open(array('id'=>'cancelRequestfrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                <input type="hidden" name="request_id" id="request_id" value="{{ $request_id }}"  />
                <fieldset>
                    <div class="form-group {{{ $errors->has('withdrawal_amount') ? 'error' : '' }}}">
                        {{ Form::label('withdrawal_amount', trans('myaccount/form.my-withdrawals.withdrawal_amout'), array('class' => 'control-label col-sm-3')) }}
                        <div class="col-sm-5">
                            <p class="form-control-static"><strong>{{ CUtil::convertBaseCurrencyToUSD($request_details['amount'], $request_details['currency'])}}</strong></p>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('cancel_reason') ? 'error' : '' }}}">
                        {{ Form::label('cancel_reason', trans('myaccount/form.my-withdrawals.reason_for_cancel'), array('class' => 'control-label col-sm-3 required-icon')) }}
                        <div class="col-sm-7">
                            {{ Form::textarea('cancel_reason', null, array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('cancel_reason') }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-8">
                            <button type="submit" name="cancel_request" class="btn green" id="set_paid" value="set_paid"><i class="fa fa-check"></i> {{trans("common.submit")}}</button>
                            <button type="reset" name="submit_cancel" class="btn default" onclick="javascript: closeFancyPopUp();"><i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
                        </div>
                    </div>
                </fieldset>
            {{ Form::close() }}

            <script language="javascript" type="text/javascript">
                var mes_required = "{{trans('auth/form.required')}}";
                $("#cancelRequestfrm").validate({
                    rules: {
                        cancel_reason: {
                            required: true
                        }
                    },
                    messages: {
                        cancel_reason:{
                            required: mes_required
                        }
                    }
                });
            </script>
        @endif
    </div>
@stop