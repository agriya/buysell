@extends('admin')
@section('content')
	<!--- SUCCESS INFO STARTS --->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{ Session::get('success_message') }}</div>
    @endif

    @if (Session::has('error_message') && Session::get('error_message') != "")
    	<div class="note note-danger">{{	Session::get('error_message') }}</div>
    @endif
    <!--- SUCCESS INFO END --->

    {{ Form::model($tax_details, ['method' => 'post', 'id' => 'addupdatetaxfrm', 'class' => 'form-horizontal']) }}
    {{ Form::hidden('mode', $d_arr['mode'], array("id" => "mode")) }}
    {{ Form::hidden('user_id', $d_arr['user_id'], array("id" => "user_id")) }}
        <div class="portlet box blue-hoki">
            <!--- TITLE STARTS --->
            <div class="portlet-title">
                <div class="caption">
                    @if($d_arr['mode'] == 'edit')<i class="fa fa-edit"></i>@else<i class="fa fa-plus-circle"></i>@endif {{ $d_arr['pageTitle'] }}
                </div>
                <a href="{{ URL::action('AdminTaxationsController@getIndex') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe pull-right">
                    <i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}
                </a>
            </div>
            <!--- TITLE END --->

            <div class="portlet-body form">
            	<!--- ADD MEMBER BASIC DETAILS STARTS --->
                <div class="form-body">
                    <div class="form-group">
                        {{ Form::label('tax_name', Lang::get('admin/taxation.tax_name'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('tax_name', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('tax_name') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('tax_description', Lang::get('admin/taxation.tax_description'), array('class' => 'control-label col-md-3')) }}
                        <div class="col-md-6">
                            {{ Form::textarea('tax_description', null, array('class' => 'form-control', 'rows' => '7')) }}
                            <label class="error">{{ $errors->first('tax_description') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('tax_fee', Lang::get('admin/taxation.tax_fee'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('tax_fee', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('tax_fee') }}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('fee_type', Lang::get('admin/taxation.fee_type'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::select('fee_type', array('percentage' => 'Percentage', 'flat' => 'Flat'), null, array('class' => 'form-control bs-select input-medium valid')) }}
                            <label class="error">{{ $errors->first('fee_type') }}</label>
                        </div>
                    </div>
                </div>
                <!--- ADD MEMBER BASIC DETAILS END --->

                <!--- ACTIONS STARTS --->
                <div class="form-actions fluid">
                    <div class="col-md-offset-3 col-md-5">
                        @if($d_arr['mode'] == 'edit')
                            <button type="submit" name="add_tax" value="add_tax" class="btn green">
                                <i class="fa fa-arrow-up"></i> {{ Lang::get('common.update') }}
                            </button>
                        @else
                            <button type="submit" name="add_tax" value="add_tax" class="btn green">
                            	<i class="fa fa-check"></i> {{ Lang::get('common.submit') }}
                            </button>
                        @endif
                        <button type="reset" name="reset_members" value="reset_members" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/taxations') }}'">
                        	<i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}
                        </button>
                    </div>
                </div>
                <!--- ACTIONS END --->
            </div>
       </div>
    {{ Form::close() }}
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var err_msg = '';
        var messageFunc = function() { return err_msg; };
        var mes_required = "{{ Lang::get('auth/form.required') }}";
        $("#addupdatetaxfrm").validate({
            rules: {
                tax_name: {
                    required: true,
                },
                tax_fee: {
                    required: true,
                    number: true
                },
                fee_type: {
                    required:true,
                }
            },
            messages: {
                tax_name: {
                    required: mes_required
                },
                tax_fee: {
                    required: mes_required,
                    number: jQuery.validator.format("{{trans('common.number_validation')}}")
                },
                fee_type: {
                    required: mes_required
                }
            },
            submitHandler: function(form) {
                    form.submit();
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
    </script>
@stop
