@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<a href="{{ URL::action('CouponsController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('coupon.coupons_list') }}
				</a>
				@if($is_edit)
					<h1>{{ Lang::get('coupon.edit_coupon') }}</h1>
				@else
					<h1>{{ Lang::get('coupon.add_coupon') }}</h1>
				@endif
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<!-- BEGIN: ADD TAXATION -->
			<div class="well">
				@if($is_edit)
					{{ Form::model($coupon_det, ['url' => URL::action('CouponsController@postUpdate', $coupon_det->id), 'method' => 'post', 'id' => 'couponsFrm', 'class' => 'form-horizontal']) }}
				@else
					{{ Form::open(array('action' => array('CouponsController@postAdd'), 'id'=>'couponsFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
				@endif
					<div id="selSrchProducts">
						<fieldset>
							<div class="fn_clsPriceFields">
                                <span class="note note-info btn-block">
                                    {{Lang::get('coupon.coupon_create_notes', array('site_name' => Config::get('generalConfig.site_name')))}}
                                </span>
							</div>

							<div class="form-group">
								{{ Form::label('coupon_code', Lang::get('coupon.coupon_code'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('coupon_code', Input::get("coupon_code"), array('class' => 'form-control valid coupon_code')) }}
									<label class="error">{{{ $errors->first('coupon_code') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('from_date', Lang::get('coupon.from_date'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
										{{ Form::text('from_date', Input::get("from_date"), array('id' => 'from_date', 'class' => 'form-control valid start_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
										<span class="input-group-btn">
											<label class="btn default" for="from_date"><i class="fa fa-calendar"></i></label>
										</span>
									</div>
									<label class="error" for="from_date" generated="true">{{{ $errors->first('from_date') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('to_date', Lang::get('coupon.to_date'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
										{{ Form::text('to_date', Input::get("to_date"), array('id' => 'to_date', 'class' => 'form-control valid end_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
										<span class="input-group-btn">
											<label class="btn default" for="to_date"><i class="fa fa-calendar"></i></label>
										</span>
									</div>
									<label class="error" for="to_date" generated="true">{{{ $errors->first('to_date') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('price_restriction', Lang::get('coupon.price'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-8 row">
									<div class="col-md-4 col-xs-4">
									{{ Form::select('price_restriction', array('none' => Lang::get('coupon.price_restriction_none'), 'less_than' => Lang::get('coupon.price_restriction_less_than'), 'greater_than' => Lang::get('coupon.price_restriction_greater_than'), 'equal_to' => Lang::get('coupon.price_restriction_equal_to'), 'between'=> Lang::get('coupon.price_restriction_between')), null, array('class' => 'form-control valid', 'id' => 'jsPriceRestriction')) }}
									<label class="error">{{{ $errors->first('price_restriction') }}}</label>
									</div>
									<div id="price_restriction_div" class="col-md-8 col-xs-8 row">
										<?php $between_div = 'style="display:none"'; $single_price_div = 'style="display:none"';?>
										@if(isset($coupon_det->price_restriction) && $coupon_det->price_restriction=='between')
											<?php $between_div = ''; $single_price_div = 'style="display:none"'; ?>
										@elseif(isset($coupon_det->price_restriction) && $coupon_det->price_restriction!='between' && $coupon_det->price_restriction!='none')
											<?php $between_div = 'style="display:none"'; $single_price_div = ''; ?>
										@endif

										<div class="col-md-8 col-xs-12 row" id="between_div" {{$between_div}}>
											<div class="col-md-6 col-xs-6">
												{{ Form::text('price_from', Input::get("price_from"), array('id' => 'price_from', 'class' => 'form-control valid price', 'placeholder' => 'From')) }}
												<label class="error">{{{ $errors->first('price_from') }}}</label>
											</div>

											<div class="col-md-6 col-xs-6">
												{{ Form::text('price_to', Input::get("price_to"), array('id'=> 'price_to', 'class' => 'form-control valid price', 'placeholder' => 'To')) }}
												<label class="error">{{{ $errors->first('price_to') }}}</label>
											</div>
										</div>

										<div class="col-md-4 col-xs-6" id="single_price_div" {{$single_price_div}}>
											{{ Form::text('price', Input::get("price"), array('class' => 'form-control valid price', 'placeholder' => 'price')) }}
											<label class="error">{{{ $errors->first('price') }}}</label>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('offer_type', Lang::get('coupon.offer'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-5 row">
									<div class="col-md-6 col-xs-6 w-p-54">
										{{ Form::select('offer_type', array('Percentage' => 'In Percentage', 'Flat' => 'In Flat'), null, array('class' => 'form-control valid', 'id' => 	'offer_type')) }}
										<label class="error">{{{ $errors->first('offer_type') }}}</label>
									</div>
									<div class="col-md-4 col-xs-4">
										{{ Form::text('offer_amount', Input::get("offer_amount"), array('class' => 'form-control valid price', 'id' => 'offer_amount', 'placeholder' => 'Amount')) }}
										<label class="error">{{{ $errors->first('offer_amount') }}}</label>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									{{ Form::hidden('user_id', $user_id) }}
									@if($is_edit)
										<button type="submit" name="srchproduct_submit" value="srchproduct_submit" id="submit" class="btn blue-madison">
											<i class="fa fa-arrow-up"></i> {{ Lang::get('coupon.update_coupon')  }}
										</button>
									@else
										<button type="submit" name="srchproduct_submit" value="srchproduct_submit" id="submit" class="btn green">
											<i class="fa fa-plus"></i> {{ Lang::get('coupon.add_coupon') }}
										</button>
									@endif
									<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('CouponsController@getIndex') }}'"><i class="fa fa-times"></i> {{ Lang::get('coupon.cancel') }}</button>
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
		var is_edit = {{$is_edit}};
		jQuery.validator.addMethod("greaterThan", function(value, element, params) {
			if (!/Invalid|NaN/.test(new Date(value))) {
		        return new Date(value) >= new Date($(params).val());
		    }
		    return isNaN(value) && isNaN($(params).val())
		        || (Number(value) > Number($(params).val()));
		},'Must be greater than or equal to "From Date".');

		jQuery.validator.addMethod("greaterThanToday", function(value, element, params) {
			if(is_edit == 1)
				return true;
			date_from = value.split("/");
			var yesterday = new Date();
			yesterday.setDate(yesterday.getDate() - 1);
			if (!/Invalid|NaN/.test(new Date(value))) {
		        return new Date(value) >= yesterday;
		    }
		    return isNaN(value) && isNaN($(params).val())
		        || (Number(value) > Number($(params).val()));
		},'Must be greater than or equal to today.');

		jQuery.validator.addMethod("titleAlphaNum", function(value, element, param)
		{
			return value.match(new RegExp("^" + param + "$"));
		});

		$('#jsPriceRestriction').change(function(){

			var price_restriction = $(this).val();
			if(price_restriction == 'between')
			{
				$('#between_div').show();
				$('#single_price_div').hide();
			}
			else if(price_restriction == 'none')
			{
				$('#between_div').hide();
				$('#single_price_div').hide();
			}
			else
			{
				$('#between_div').hide();
				$('#single_price_div').show();
			}
		})

		var mes_required = '{{ Lang::get('auth/form.required') }}';

		$('#offer_type').change(function(){
		    if($('#offer_type').val() == "Percentage"){
		         $('#offer_amount').rules("add",{max:100});
		    }
		    else if($('#offer_type').val() == "Flat"){
		         $('#offer_amount').rules("remove","max");
		    }
		});

		$('#jsPriceRestriction').change(function(){
		    if($('#jsPriceRestriction').val() == "between"){
		         $('#price_to').rules("add",{min:function(){ return $('#price_from').val(); } });
		    }
		    else{
		         $('#price_to').rules("remove","min");
		    }
		});

		$(document).ready(function() {
			$("#couponsFrm").validate({
				focusInvalid: false,
                rules: {
	                coupon_code: {
						required: true
					},
					from_date: {
						required: true,
						//date: true,
						greaterThanToday: function(element){
							if(is_edit)
								return false;
							else
								return true;
						}
					},
					to_date: {
						required: true,
						//date:true,
						greaterThan: '#from_date'
					},
					offer_type: {
						required: true
					},
					offer_amount: {
						required: true,
						number: true,
						min: 0,
						max: 100
					},
					price : {
						required: function(element) {
							if($('#jsPriceRestriction').val() != 'none' && $('#jsPriceRestriction').val() != 'between')
								return true;
							else
								return false
						},
						number: true,
						min: 0
					},
					price_from : {
						required: function(element) {
							if($('#jsPriceRestriction').val() == 'between')
								return true;
							else
								return false;
						},
						number: true,
						min: 0
					},
					price_to : {
						required: function(element) {
							if($('#jsPriceRestriction').val() == 'between')
								return true;
							else
								return false;
						},
						number: true,
						min: function(){ return $('#price_from').val(); }
					}
				},
	            messages: {
	                coupon_code: {
						required: mes_required,
					},
					from_date: {
						required: mes_required
					},
					to_date: {
						required: mes_required
					},
					offer_type: {
						required: mes_required
					},
					offer_amount: {
						required: mes_required
					},
					price: {
						required: mes_required
					},
					price_from: {
						required: mes_required
					},
					price_to: {
						required: mes_required
					},
				}
            });

			//Add or remove max validation based on the offer type
            if($('#offer_type').val() == "Percentage"){
		         $('#offer_amount').rules("add",{max:100});
		    }
		    else if($('#offer_type').val() == "Flat"){
		         $('#offer_amount').rules("remove","max");
		    }


			var price_restriction = $('#jsPriceRestriction').val();
			if(price_restriction == 'between')
			{
				$('#between_div').show();
				$('#single_price_div').hide();

				$('#price_to').rules("remove","min");
				$('#price_to').rules("add",{min:function(){ return $('#price_from').val(); } });
			}
			else if(price_restriction == 'none')
			{
				$('#between_div').hide();
				$('#single_price_div').hide();
			}
			else
			{
				$('#between_div').hide();
				$('#single_price_div').show();
			}


        });

		if(!is_edit)
		{
	        $('.start_date').datepicker({
	            format: 'yyyy-mm-dd',
	            todayHighlight: true,
	            autoclose: true,
	            "minDate": 0,
				onSelect: function(selected) {
				  $('.end_date').datepicker("option","minDate", selected)
				}
	        })
		}
		else
		{
			$('.start_date').datepicker({
	            format: 'yyyy-mm-dd',
	            todayHighlight: true,
	            autoclose: true
	        })
		}

        $('.end_date').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            onSelect: function(selected) {
			  //$('.start_date').datepicker("option","maxDate", selected)
			}
        })

       // $('#start_date').bind('onSelect', function() { $('.end_date').datepicker("option","minDate", selected) });
       // $('#end_date').bind('onSelect', function() { $('.start_date').datepicker("option","maxDate", selected) });
       $("#submit").click(function(){
		   $('#offer_amount').rules("remove","max");
		   if($('#offer_type').val() == "Percentage"){
			 	$('#offer_amount').rules("add", {
			     	max: 100
			   	});
		    }

			$('#price_to').rules("remove","min");
		  	if($('#jsPriceRestriction').val() == "between"){
		         $('#price_to').rules("add",{min:function(){ return $('#price_from').val(); } });
		    }


		   $("#couponsFrm").submit();
		});

	</script>
@stop