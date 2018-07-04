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
				<a href="{{ URL::action('AddressesController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('myAddresses.my_addresses') }}
				</a>

				@if($action=='add')
				   <h1>{{ Lang::get('myAddresses.add_address') }}</h1>
				@else
					<h1>{{ Lang::get('myAddresses.edit_address') }}</h1>
				@endif
			</div>
			<!-- END: PAGE TITLE -->

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

			<!-- BEGIN: ADD TAXATION -->
			<div class="well">
				{{-- Form::open(array('action' => array('TaxationsController@postAddTaxation'), 'id'=>'addressFrm', 'method'=>'post','class' => 'form-horizontal' )) --}}
				{{ Form::model($address_details, ['method' => 'post','class' => 'form-horizontal', 'id' => 'addAddressfrm']) }}
					<div id="selSrchProducts">
						<fieldset>
							<div class="form-group">
								{{ Form::label('address_line1', Lang::get('myAddresses.address_line1'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('address_line1', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('address_line1') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('address_line2', Lang::get('myAddresses.address_line2'), array('class' => 'col-md-2 control-label ')) }}
								<div class="col-md-3">
									{{ Form::text('address_line2', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('address_line2') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('street', Lang::get('myAddresses.street'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-3">
									{{ Form::text('street', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('street') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('city', Lang::get('myAddresses.city'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('city', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('city') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('state', Lang::get('myAddresses.state'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('state', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('state') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('country_id', Lang::get('myAddresses.country'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::select('country_id', $countries_list, null , array("id" => "country_id", "class" => "form-control")) }}
									<label class="error">{{{ $errors->first('country_id') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('zip_code', Lang::get('myAddresses.zip_code'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('zip_code', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('zip_code') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('phone_no', Lang::get('myAddresses.phone_no'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('phone_no', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('phone_no') }}}</label>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-sm-9">
									<label class="checkbox-inline">
										{{ Form::checkbox('make_as_default', 1, false, array('class' => 'js-make_as_default', 'id' => 'js-make_as_default')) }}
										{{ Form::label('make_as_default', trans('myAddresses.make_as_primary_address')) }}
									</label>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									{{ Form::hidden('logged_user_id', $logged_user_id) }}
									{{ Form::hidden('action',$action) }}
									{{ Form::hidden('addr_id',$addr_id) }}
                                    @if($action=='add')
                                        <button type="submit" class="btn green">
                                            <i class="fa fa-plus"></i> {{Lang::get('myAddresses.add_address')}}
                                        </button>
                                    @else
                                        <button type="submit" class="btn blue-madison">
                                            <i class="fa fa-save"></i> {{Lang::get('myAddresses.save_address')}}
                                        </button>
                                    @endif
									<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AddressesController@getIndex') }}'"><i class="fa fa-times"></i> {{ Lang::get('myAddresses.cancel') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
			</div>
			<!-- END: ADD TAXATION -->
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var mes_required = '{{ Lang::get('auth/form.required') }}';
		$(document).ready(function() {
			$("#addAddressfrm").validate({
                rules: {
                	address_line1: {
						required: true
					},
	                city: {
						required: true
					},
					state: {
						required: true
					},
					country_id: {
						required: true
					},
					zip_code: {
						required: true
					},
					phone_no: {
						required: true
					}
				},
	            messages: {
	            	address_line1: {
						required: mes_required
					},
	                city: {
						required: mes_required
					},
					state: {
						required: mes_required
					},
					country_id: {
						required: mes_required
					},
					zip_code: {
						required: mes_required
					},
					phone_no: {
						required: mes_required
					}
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
            });
        });
	</script>
@stop