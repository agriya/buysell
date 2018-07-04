@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('deals::deals.request_to_set_featured_head_lbl') }}</h1>
			</div>
			
			<!-- BEGIN: DEALS SUBMENU -->
			@include('deals::deal_submenu')
			<!-- END: DEALS SUBMENU -->
            
            <!-- BEGIN: ALERT BLOCK -->
			@include('notifications')
			<!-- BEGIN: ALERT BLOCK -->
			
            @if(isset($error_message) && $error_message != "")
                <div class="note note-danger">{{ $error_message }}</div>
            @else
                @if(isset($d_arr['confirm']) && $d_arr['confirm'] == 'Yes')
                    <div class="well">
					    {{ Form::open(array('id'=>'dealFeaturedFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                            {{ Form::hidden('deal_id', $d_arr['deal_id'], array('id' => 'deal_id')) }}
                            {{ Form::hidden('pay_act', '', array('id' => 'pay_act')) }}
                            <?php
                                $view_url = $deal_serviceobj->getDealViewUrl($deal_details);
                            ?>	
                            <div class="form-group">
                                {{ Form::label('deal_title', Lang::get('deals::deals.deal_details_lbl'), array('class' => 'col-md-2 control-label')) }}
                                <div class="col-md-3 margin-top-8">
									<a href="{{ $view_url }}" title="{{ $deal_details->deal_title }}">
										{{ $deal_details->deal_title }} {{-- ({{ $deal_details->deal_id }}) --}}
									</a>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('date_featured_from', Lang::get('deals::deals.date_featured_from_head_lbl'), array('class' => 'col-md-2 control-label required-icon')) }}
                                <div class="col-md-3 margin-top-8">
                                    <input type="hidden" name="date_featured_from" id="date_featured_from" value="{{ $d_arr['date_featured_from_lbl'] }}"/>
                                    {{ CUtil::FMTDate($d_arr['date_featured_from'], 'Y-m-d', '') }}
                                    <label class="error" for="date_featured_from" generated="true">{{{ $errors->first('date_featured_from') }}}</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('date_featured_to', Lang::get('deals::deals.date_featured_to_head_lbl'), array('class' => 'col-md-2 control-label')) }}
                                <div class="col-md-3 margin-top-8">
                                    <input type="hidden" name="date_featured_to" id="date_featured_to" value="{{ $d_arr['date_featured_to_lbl'] }}"/>
                                    {{ CUtil::FMTDate($d_arr['date_featured_to'], 'Y-m-d', '') }}
                                	<label class="error" for="date_featured_from" generated="true">{{{ $errors->first('date_featured_to') }}}</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('deal_featured_days', Lang::get('deals::deals.number_of_days_to_featured_lbl'), array('class' => 'col-md-2 control-label')) }}
                                <div class="col-md-3 margin-top-8">
									<?php /* ?>
                                    <input type="hidden" name="deal_featured_days" id="deal_featured_days" value="{{ $d_arr['deal_featured_days'] }}"/>
									<?php */ ?>
									{{ $d_arr['deal_featured_days'] }}
								</div>
                            </div>
                        
                            @if($d_arr['deal_listing_fee'] != 0)
                                <div class="form-group">
                                    {{ Form::label('deal_listing_fee', Lang::get('deals::deals.listing_fee_lbl'), array('class' => 'col-md-2 control-label')) }}
                                    <div class="col-md-3 margin-top-8">
										<span class="text-muted">{{ Config::get('generalConfig.site_default_currency') }}</span><strong> {{ $d_arr['deal_listing_fee'] }}</strong>
									</div>
                                </div>
                            @endif
                            
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-9">
                                    <button type="submit" name="set_featured" class="btn green" id="set_featured" value="submit_request">
                                    	<i class="fa fa-check"></i> {{ Lang::get('deals::deals.submit') }}
									</button>
                                    <button type="submit" name="edit_request" class="btn default" id="edit_request" value="edit_request">
                                    	<i class="fa fa-arrow-left"></i> {{trans("common.back")}}
									</button>
                                </div>
                            </div>
                        {{Form::close()}}
                    </div>                    
                @else
                    <div class="well">
                        {{ Form::open(array('id'=>'dealFeaturedFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                            {{ Form::hidden('deal_id', $d_arr['deal_id'], array('id' => 'deal_id')) }}
                            <?php
                                $view_url = $deal_serviceobj->getDealViewUrl($deal_details);
                            ?>			
                            <div class="form-group">
                                {{ Form::label('deal_title', Lang::get('deals::deals.deal_details_lbl'), array('class' => 'col-md-2 control-label required-icon fonts18')) }}
                                <div class="col-md-3 margin-top-10">
                                    <label for="deal_title">
										<a href="{{ $view_url }}" title="{{ $deal_details->deal_title }}" class="fonts18">
											{{ $deal_details->deal_title }} {{-- ({{ $deal_details->deal_id }}) --}}
										</a>
									</label>									
                                </div>
                            </div>
                            <p class="note note-info">{{ Lang::get('deals::deals.featured_request_date_range_note') .": ". Lang::get('deals::deals.featured_request_date_range_note_msg')}}</p>
                            <div class="form-group">
                                {{ Form::label('date_featured_from', Lang::get('deals::deals.date_featured_from_head_lbl'), array('class' => 'col-md-2 control-label required-icon')) }}
                                <div class="col-md-6">
                                    <div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
                                        {{ Form::text('date_featured_from', Input::get("date_featured_from"), array('id' => 'date_featured_from', 'class' => 'form-control valid start_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
                                        <span class="input-group-btn">
                                            <label class="btn default" for="date_featured_from"><i class="fa fa-calendar"></i></label>
                                        </span>
                                    </div>
                                    <label class="error" for="date_featured_from" generated="true">{{{ $errors->first('date_featured_from') }}}</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('date_featured_to', Lang::get('deals::deals.date_featured_to_head_lbl'), array('class' => 'col-md-2 control-label required-icon')) }}
                                <div class="col-md-6">
                                    <div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
                                        {{ Form::text('date_featured_to', Input::get("date_featured_to"), array('id' => 'date_featured_to', 'class' => 'form-control valid end_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
                                        <span class="input-group-btn">
                                            <label class="btn default" for="date_featured_to"><i class="fa fa-calendar"></i></label>
                                        </span>
                                    </div>
                                    <label class="error" for="date_featured_to" generated="true">{{{ $errors->first('date_featured_to') }}}</label>
                                </div>
                            </div>
                            <?php /* ?>
                            <div class="form-group">
                                {{ Form::label('deal_featured_days', Lang::get('deals::deals.number_of_days_to_featured_lbl'), array('class' => 'col-md-2 control-label required-icon')) }}
                                <div class="col-md-3">
                                    {{ Form::text('deal_featured_days', null, array('class' => 'form-control valid')) }}
                                    <label class="error">{{{ $errors->first('deal_featured_days') }}}</label>
                                </div>
                            </div>
                            <?php */ ?>
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-9">
                                    <button type="submit" value="{{trans('common.confirm')}}" class="btn green" id="pay_confirm" name="pay_confirm">
										{{trans('common.confirm')}} <i class="fa fa-arrow-right"></i>
									</button>
                                </div>
                            </div>
                        {{ Form::close() }}
	                   </div>
                @endif
            @endif    
            <div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
                <span class="ui-icon ui-icon-alert"></span>
                <span id="dialog-product-confirm-content" class="show"></span>
            </div>         
		</div>
	</div>
@stop   

@section('script_content')
	<script type="text/javascript">
		var is_edit = 0;
		@if(isset($d_arr['act']) && $d_arr['act'] == 'edit')
			is_edit = 1;		
		@endif
		function validateNumber(event) {
			var key = window.event ? event.keyCode : event.which;
			if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 46
			 || event.keyCode == 37 || event.keyCode == 39) {
				return true;
			}
			else if ( key < 48 || key > 57 ) {
				return false;
			}
			else return true;
		};
	
		jQuery.validator.addMethod("greaterThan", function(value, element, params) {
			if(is_edit == 1)
				return true;
			
			date_from = $(params).val().split("/");
			date_to = value.split("/");
			/*
			if (!/Invalid|NaN/.test(new Date(value))) {
		       return new Date(value) >= new Date($(params).val());
		    }	
			*/
			if (!/Invalid|NaN/.test(new Date(date_from[2], date_from[1]-1, date_from[0]))) {
		        return new Date(date_from[2], date_from[1]-1, date_from[0]) <= new Date(date_to[2], date_to[1]-1, date_to[0]);
		    }		
		    return isNaN(value) && isNaN($(params).val())    || (Number(value) > Number($(params).val()));
				
		},'Must be greater than or equal to "From Date".');

		jQuery.validator.addMethod("greaterThanToday", function(value, element, params) {
			if(is_edit == 1)
				return true;
			date_from = value.split("/");
			var yesterday = new Date();
			yesterday.setDate(yesterday.getDate() - 1);
			if (!/Invalid|NaN/.test(new Date(date_from[2], date_from[1]-1, date_from[0]))) {
		        return new Date(date_from[2], date_from[1]-1, date_from[0]) > yesterday;
		    }
		    return isNaN(value) && isNaN($(params).val())
		        || (Number(value) > Number($(params).val()));
		},'Must be greater than or equal to today.');
				
		function getDateFormat(formatString) {	 
			var separator = formatString.match(/[.\/\-\s].*?/),
				parts = formatString.split(/\W+/);
			if (!separator || !parts || parts.length === 0) {
				throw new Error("Invalid date format.");
			}
			return { separator: separator, parts: parts };
		}

		function MyParseDate(date, format) {
			var parts = date.split(format.separator),
				date = new Date(),
				val;
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);
			date.setMilliseconds(0);
			if (parts.length === format.parts.length) {

				for (var i = 0, cnt = format.parts.length; i < cnt; i++) {
					val = parseInt(parts[i], 10) || 1;
		 
					switch (format.parts[i]) {
						case 'dd':
						case 'd':
							date.setDate(val);
							break;
						case 'mm':
						case 'm':
							date.setMonth(val);
							break;
						case 'yy':
							date.setFullYear(2000 + val);
							break;
						case 'yyyy':
							date.setFullYear(val);
							break;
					}
				}
			}		 
			return date;
		}
		
		jQuery.validator.addMethod('date', function (value, element, params) {                   
		   if (this.optional(element)) {
			   return true;
		   }
		   var result = false;
		   try {                       
			   var format = getDateFormat('dd/mm/yyyy');
			   MyParseDate(value, format);                       
			   result = true;
		   } catch(err) {

			   result = false;
		   }
		   return result;
	   });
		   	
		var mes_required = 'Required';		
		$(document).ready(function(){		
			$('[id^=deal_featured_days]').keypress(validateNumber);		
			$("#dealFeaturedFrm").validate({
				focusInvalid: false,
                rules: {	                
					date_featured_from: {
						required: true,
						date: true,
						greaterThanToday: true
					},
					date_featured_to: {
						required: true,
						date:true,
						greaterThan: '#date_featured_from'
					},
					deal_featured_days:{
						required: true					
					}					
				},
	            messages: {	               
					date_featured_from:{
						required: mes_required
					},
					date_featured_to:{
						required: mes_required
					},
					deal_featured_days:{
						required: mes_required
					}
				},
				submitHandler: function(form) {
					form.submit();
				}
            });
			
			$(function() {
				$('.start_date').datepicker({
					format: 'dd/mm/yyyy',
					todayHighlight: true,
					"minDate": 0,
					autoclose: true,
					onSelect: function(selected) {
					  $('.end_date').datepicker("option","minDate", selected)
					}
				});
				$('.end_date').datepicker({
					format: 'dd/mm/yyyy',
					todayHighlight: true,
					autoclose: true,
					onSelect: function(selected) {
					  //$('.start_date').datepicker("option","maxDate", selected)
					}
				});
			});
			
			$('#set_featured').click(function(){				
				@if(Config::has('plugin.deal_listing_fee') && Config::get('plugin.deal_listing_fee') > 0)
					$('#dialog-product-confirm-content').html('{{ Lang::get("deals::deals.payby_accountbalance_confirm_msg") }}');
					$("#dialog-product-confirm").dialog({ title: '{{ Lang::get("deals::deals.payby_accountbalance_confirm_head") }}', modal: true,
						buttons: {
							"{{ Lang::get('importer::importer.yes') }}": function() {
								$(this).dialog("close");
								$('#pay_act').val('act_bal');						
								$('#dealFeaturedFrm').submit();						
							}, "{{ Lang::get('deals::deals.cancel') }}": function() { $(this).dialog("close"); }
						}
					});		
					return false;
				@else
					$('#pay_act').val('act_bal');
					$('#dealFeaturedFrm').submit();
				@endif			
			});
			
		});		
	</script>
@stop                         