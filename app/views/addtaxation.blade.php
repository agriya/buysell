@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<a href="{{ URL::action('TaxationsController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('taxation.taxations_list') }}
				</a>
				<h1>{{ Lang::get('taxation.add_taxation') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			<div class="well">
				<!-- ALERT BLOCK STARTS -->
				@if(Session::has('error_message') && Session::get('error_message') != '')
					<div class="note note-danger">{{ Session::get('error_message') }}</div>
					<?php Session::forget('error_message'); ?>
				@endif
				@if(Session::has('success_message') && Session::get('success_message') != '')
					<div class="note note-success">{{ Session::get('success_message') }}</div>
					<?php Session::forget('success_message'); ?>
				@endif
				<!-- ALERT BLOCK ENDS -->

				<!-- ADD TAXATION STARTS -->
				{{ Form::open(array('action' => array('TaxationsController@postAddTaxation'), 'id'=>'productFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
					<div id="selSrchProducts">
						<fieldset>
							<div class="form-group">
								{{ Form::label('tax_name', Lang::get('taxation.tax_name'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('tax_name', Input::get("tax_name"), array('class' => 'form-control valid')) }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('tax_description', Lang::get('taxation.tax_description'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-6">
									{{ Form::textarea('tax_description', Input::get("tax_description"), array('class' => 'form-control valid')) }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('tax_fee', Lang::get('taxation.tax_fee'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('tax_fee', Input::get("tax_fee"), array('class' => 'form-control valid')) }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('fee_type', Lang::get('taxation.fee_type'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-3">
									{{ Form::select('fee_type', array('percentage' => Lang::get('common.percentage'), 'flat' => Lang::get('common.flat')), Input::get("fee_type"), array('class' => 'form-control valid')) }}
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									{{ Form::hidden('user_id', $user_id) }}
									<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn green"><i class="fa fa-plus"></i> {{ Lang::get('taxation.add_taxation') }}</button>
									<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('TaxationsController@getIndex') }}'"><i class="fa fa-times"></i> {{ Lang::get('taxation.tax_cancel') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
				<!-- ADD TAXATION ENDS -->
			</div>
		</div>
	</div>
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
        var err_msg = '';
        var messageFunc = function() { return err_msg; };
        var mes_required = "{{ Lang::get('auth/form.required') }}";
        $("#productFrm").validate({
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
