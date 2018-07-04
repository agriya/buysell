@extends('popup')
@section('includescripts')
	<script type="text/javascript">
		@if($is_inserted)
			parent.displayLoadingImage(true);
			parent.location.href = '{{ URL::to('checkout/'.$item_owner_id) }}';
		@endif
	</script>
@stop

@section('content')
    <!-- BEGIN: PAGE TITLE -->
	<h1 class="margin-bottom-0">{{trans('checkOut.address_details')}}</h1>
   <!-- END: PAGE TITLE -->

	<!-- BEGIN: SHIPPING ADDRESS DETAIL -->
    <div class="pop-content">
        {{ Form::open(array('url' => URL::action('CheckOutController@getShippingAddressPopup'), 'class' => 'form-horizontal',  'id' => 'shipping_address_frm', 'name' => 'form_checkout')) }}
        	{{Form::hidden('item_owner_id',$item_owner_id)}}
        	<?php $cookie_country = CUtil::getShippingCountry(); ?>
            <div class="row addrdet-popup">
                <fieldset class="col-sm-6">
                	<h2 class="title-one">
						@if($address_type=='shipping')
							{{trans('checkOut.select_your_shipping_address')}}
						@else
							{{trans('checkOut.select_your_billing_address')}}
						@endif
					</h2>
					@if(isset($user_addresses) && $user_addresses && count($user_addresses) > 0)
						@foreach($user_addresses as $address)
							<div class="js-addrdiv_{{$address->id}} js-addrdiv addr-div clearfix">
								<meta id="address_details_{{$address->id}}"
                                data-addrline1="{{$address->address_line1}}"
                                data-addrline2="{{$address->address_line2}}"
                                data-street="{{$address->street}}"
                                data-city="{{$address->city}}"
                                data-state="{{$address->state}}"
                                data-country="{{$address->country_id}}"
                                data-zipcode="{{$address->zip_code}}"
                                data-phone_no="{{$address->phone_no}}">
                                <div class="col-sm-10 col-xs-10">
                                    <div onclick="selectAddr(this)" data-addrid="{{$address->id}}" class="pull-left showonhover @if($address->id == $selected_address_id) selected @endif">
									<i class="fa fa-check text-primary"></i></div>
                                    <div onclick="selectAddr(this)" data-addrid="{{$address->id}}" id="address_det_{{$address->id}}" class="margin-left-25">
                                        {{Form::hidden('address_country_'.$address->id, $address->country_id, array('id' => 'address_country_'.$address->id))}}
                                        @if($address->address_line1!='')
                                            <p>{{$address->address_line1}}</p>
                                        @endif
                                        @if($address->address_line2!='')
                                            <p>{{$address->address_line2}}</p>
                                        @endif
                                        @if($address->street!='')
                                            <p>{{$address->street}}</p>
                                        @endif
                                        @if($address->city!='')
                                            <p>{{$address->city}}</p>
                                        @endif
                                        @if($address->state!='')
                                            <p>{{$address->state}}</p>
                                        @endif
                                        @if($address->country!='')
                                            <p>{{$address->country}}</p>
                                        @endif
                                        @if($address->zip_code!='')
                                            <p>{{$address->zip_code}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-2">
                                    <div id="edit_div" class="showonhover">
                                        <a href="javascript:;" data-addrid="{{$address->id}}" onclick="editAddr(this)" class="btn btn-xs blue pull-right"><i class="fa fa-edit"></i></a>
                                    </div>
                                </div>
							</div>
						@endforeach
					@else
						<div class="alert alert-info">{{trans('checkOut.no_address_added_yet')}}</div>
					@endif
				</fieldset>

                <fieldset class="col-sm-6">
					<h2 class="title-one" id="js-shipping-title">
						{{trans('checkOut.add_new_address')}}
					</h2>
                	<div class="form-group {{{ $errors->has('address_line1') ? 'error' : '' }}}">
                        {{ Form::label('address_line1', trans('checkOut.address_line1'), array('class' => 'col-sm-4 control-label required-icon')) }}
                        <div class="col-sm-8">
                             {{ Form::text('address_line1', Input::old('address_line1'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('address_line1') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('address_line2') ? 'error' : '' }}}">
                        {{ Form::label('address_line2', trans('checkOut.address_line2'), array('class' => 'col-sm-4 control-label ')) }}
                        <div class="col-sm-8">
                             {{ Form::text('address_line2', Input::old('address_line2'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('address_line2') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('street') ? 'error' : '' }}}">
                        {{ Form::label('street', trans('checkOut.street'), array('class' => 'col-sm-4 control-label')) }}
                        <div class="col-sm-8">
                             {{ Form::text('street', Input::old('street'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('street') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('city') ? 'error' : '' }}}">
                        {{ Form::label('city', trans('checkOut.city'), array('class' => 'col-sm-4 control-label required-icon')) }}
                        <div class="col-sm-8">
                             {{ Form::text('city', Input::old('city'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('city') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('state') ? 'error' : '' }}}">
                        {{ Form::label('state', trans('checkOut.state'), array('class' => 'col-sm-4 control-label required-icon')) }}
                        <div class="col-sm-8">
                             {{ Form::text('state', Input::old('state'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('state') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('country_id') ? 'error' : '' }}}">
                        {{ Form::label('country_id', trans('checkOut.country'), array('class' => 'col-sm-4 control-label required-icon')) }}
                        <div class="col-sm-8">
                             {{ Form::select('country_id', $d_arr['countries_list'], Input::old('country_id', $cookie_country) , array("id" => "country_id", "class" => "form-control js-copy-text")) }}<!--"onchange" => "UpdateShippingCountry(this.value)"-->
                            <label class="error">{{{ $errors->first('country_id') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('zip_code') ? 'error' : '' }}}">
                        {{ Form::label('zip_code', trans('checkOut.zip_code'), array('class' => 'col-sm-4 control-label required-icon')) }}
                        <div class="col-sm-8">
                             {{ Form::text('zip_code', Input::old('zip_code'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('zip_code') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('phone_no') ? 'error' : '' }}}">
                        {{ Form::label('phone_no', trans('checkOut.phone_no'), array('class' => 'col-sm-4 control-label required-icon')) }}
                        <div class="col-sm-8">
                             {{ Form::text('phone_no', Input::old('phone_no'), array("class" => "form-control js-copy-text")) }}
                            <label class="error">{{{ $errors->first('phone_no') }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="checkbox padding-left-0">
								{{ Form::checkbox('make_as_default', 1, false, array('class' => 'js-make_as_default', 'id' => 'js-make_as_default')) }}
								{{ Form::label('js-make_as_default', trans('checkOut.make_as_default_address'), array('class' => 'fonts12')) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
		                <div class="col-sm-offset-4 col-sm-8">
		                	{{Form::hidden('address_type',$address_type, array('id' => 'address_type'))}}
		                	{{Form::hidden('address_id',0, array('id' => 'address_id'))}}
		                    <button type="submit" id="js-submit-button" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('checkOut.add_address')}}</button>
		                    <!--<a href="javascript:;" itemprop="url" onclick="closeFancyBox()"><button type="reset" class="btn default">Cancel</button></a>-->
		                </div>
		            </div>
                </fieldset>
            </div>
	    {{ Form::close() }}
    </div>
	<!-- END: SHIPPING ADDRESS DETAIL -->

    <script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";

		function selectAddr(element)
		{
			var addr_id = $(element).data('addrid');
			var address_det = $('#address_det_'+addr_id).html();
			var country_id = $('#address_country_'+addr_id).val();
			var address_type = $('#address_type').val();
			parent.displayLoadingImage(true);
			$.post("{{ URL::action('CheckOutController@postUpdateShippingAddress') }}", {"address_id": addr_id, "address_type":address_type, "country_id":country_id}, function(data) {
				parent.location.reload(true);
				//parent.hideLoadingImage();
			});
		}
		function editAddr(element)
		{
			var addr_id = $(element).data('addrid');
			$('#js-shipping-title').text('{{trans('checkOut.edit_address')}}');
			var meta_element=$('#address_details_'+addr_id);

			$('#address_line1').val($(meta_element).data('addrline1'));
			$('#address_line2').val($(meta_element).data('addrline2'));
			$('#street').val($(meta_element).data('street'));
			$('#city').val($(meta_element).data('city'));
			$('#state').val($(meta_element).data('state'));
			$('#country_id').val($(meta_element).data('country'));
			$('#zip_code').val($(meta_element).data('zipcode'));
			$('#phone_no').val($(meta_element).data('phone_no'));
			$('#address_id').val(addr_id);

			$('.js-addrdiv').removeClass('active');
			$('.js-addrdiv_'+addr_id).addClass('active');
			$('#js-submit-button').text('{{trans('checkOut.submit_changes')}}');

			/*$.post("{{ URL::action('CheckOutController@postUpdateShippingAddress') }}", {"address_id": addr_id, "address_type":address_type, }, function(data) {
				parent.location.reload(true);
				//parent.hideLoadingImage();
			});*/
		}

		function closeFancyBox() {
			parent.$.fancybox.close();
		}
		/*function closeFancyBox()
		{

			$('.fancybox-close').trigger('click');
		}*/
		function fancyPopupParentUrlRedirect(url)
		{
			parent.location.href = url;//'{{ URL::to('checkout') }}';
		}
		function checkisrequired()
		{
			if($('.js-use_as_billing').is(':checked'))
            {
				return false;
			}
			return true;
		}

        $("#shipping_address_frm").validate({
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
				/*billing_city: {
					required: checkisrequired()
				},
				billing_state: {
					required: checkisrequired()
				},
				billing_country_id: {
					required: checkisrequired()
				},
				billing_zip_code: {
					required: checkisrequired()
				}*/
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
				/*billing_city: {
					required: mes_required
				},
				billing_state: {
					required: mes_required
				},
				billing_country_id: {
					required: mes_required
				},
				billing_zip_code: {
					required: mes_required
				}*/
            },
			submitHandler: function(form) {
				parent.displayLoadingImage(true);
                form.submit();
        	}
        });



		$('.js-use_as_billing').click(function() {
            if ($(this).is(':checked')) {

                $('.js-copy-text').each(function(){
                    $('#billing_'+$(this).attr('name')).val($(this).val());
                    $('#billing_'+$(this).attr('name')).prop('disabled', true);
                });
            }
            else
            {
                $('.js-copy-text').each(function(){
                    //$('#billing_'+$(this).attr('name')).val($(this).val());
                    $('#billing_'+$(this).attr('name')).prop('disabled', false);
                });
            }
        });

        $('.js-copy-text').change(function() {
            if($('.js-use_as_billing').is(':checked'))
            {
                $('#billing_'+$(this).attr('name')).val($(this).val());
            }
        });

		function UpdateShippingCountry(country_id) {
			parent.displayLoadingImage(true);
			$.post("{{ Url::to('updateShippingCountry')}}", {"country_id": country_id}, function(data) {
				parent.hideLoadingImage();
			})
		}

        /*$("#country_id").change(function() {
			var actions_url = '{{ URL::action('CartController@getChangeShippingCountry')}}';
			var shipping_country_id =  $(this).val();
			var qry_str = 'shipping_country=' + shipping_country_id + '&redirect_to=checkout';
			parent.window.location.href = actions_url + "?" + qry_str;
		});*/
    </script>
@stop