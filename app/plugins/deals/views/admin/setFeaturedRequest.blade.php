@extends('popup')
@section('content')
	<!-- BEGIN: PAGE TITLE -->
	<h1>{{ Lang::get('deals::deals.request_to_set_featured_head_lbl') }}</h1>
	<!-- END: PAGE TITLE -->
    
	<div class="pop-content">
    	<!-- BEGIN: INFO BLOCK -->
		@if (Session::has('error_message') && Session::get('error_message') != "")
			<div class="note note-danger">{{ Session::get('error_message') }}</div>
		@endif
	
		@if(isset($error_message) && $error_message != "")
			<div class="note note-danger">{{ $error_message }}</div>
			<a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
				<button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle margin-right-5"></i> Close</button>
			</a>
		@elseif(isset($success_message) && $success_message != "")
			<div class="note note-info">{{ $success_message }}</div>
			<a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
				<button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle margin-right-5"></i> Close</button>
			</a>
        <!-- END: INFO BLOCK -->
		@else
			@if(count($deal_details) > 0)
				{{ Form::open(array('id'=>'setFeaturedfrm', 'method'=>'post', 'class' => 'form-horizontal' )) }}
					<input type="hidden" name="pay_act" id="pay_act" value="" />
					<input type="hidden" name="user_id" id="user_id"  value="{{ $deal_details->user_id }}" />            
					<input type="hidden" name="deal_id" id="deal_id"  value="{{ $deal_details->deal_id }}" />  
					<input type="hidden" name="deal_featured_days" id="deal_featured_days"  value="{{ $deal_details->deal_featured_days }}" />                      
					<?php $view_url = $deal_serviceobj->getDealViewUrl($deal_details); ?>
					
					<!-- BEGIN: REQUEST DETAILS DEAL -->
					<h2 class="title-one">{{ Lang::get('deals::deals.request_details_head_lbl') }}</h2>
					<div class="dl-horizontal-new dl-horizontal dl-deals margin-bottom-30">
						<dl>
							<dt>{{ Lang::get('deals::deals.deal_id_head_lbl') }}</dt>
                            <dd>
								<span><strong class="text-muted">{{ $deal_details->deal_id }}</strong></span>
							</dd>
						</dl>
                        <dl>
							<dt>{{ Lang::get('deals::deals.deal_title_head') }}</dt>                            
							<dd>
								<span>
									<a target="_blank" href="{{$view_url}}" title="{{ $deal_details->deal_title }}">{{ $deal_details->deal_title }} </a>
								</span>
							</dd>
						</dl>
						
						<dl>
							<dt>{{ Lang::get('deals::deals.deal_featured_requested_days_lbl') }}</dt>
							<dd>
								<span>
									<strong class="text-muted margin-right-5">{{ Lang::get('deals::deals.search_deal_id_from') }}:</strong>
									{{ CUtil::FMTDate($deal_details->date_featured_from, 'Y-m-d', '') }} 
									<strong class="text-muted margin-right-5 margin-left-10">{{ Lang::get('deals::deals.search_deal_id_to') }}:</strong>
									{{ CUtil::FMTDate($deal_details->date_featured_to, 'Y-m-d', '') }}
								</span>
							</dd>
						</dl>
						
						<dl>
							<dt>{{ Lang::get('deals::deals.requested_days_lbl') }}</dt>
							<dd><span>{{ $deal_details->deal_featured_days }} {{ Lang::get('deals::deals.days_lbl') }}</span></dd>
						</dl>  
					</div>
					<!-- END: REQUEST DETAILS DEAL -->
					
					<!-- BEGIN: ADMIN APPROVAL DEAILS DEAL -->
					<h2 class="title-one">{{ Lang::get('deals::deals.admin_approval_deatils_lbl') }}</h2>
					<fieldset>
						<div class="form-group">
							{{ Form::label('date_featured_from', Lang::get('deals::deals.date_featured_from_head_lbl'), array('class' => 'col-sm-3 control-label required-icon')) }}
							<div class="col-sm-7">
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
							{{ Form::label('date_featured_to', Lang::get('deals::deals.date_featured_to_head_lbl'), array('class' => 'col-sm-3 control-label required-icon')) }}
							<div class="col-sm-7">
								<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
									{{ Form::text('date_featured_to', Input::get("date_featured_to"), array('id' => 'date_featured_to', 'class' => 'form-control valid end_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
									<span class="input-group-btn">
										<label class="btn default" for="date_featured_to"><i class="fa fa-calendar"></i></label>
									</span>
								</div>
								<label class="error" for="date_featured_to" generated="true">{{{ $errors->first('date_featured_to') }}}</label>
							</div>
						</div>                                         
						
						<div class="form-group">
							{{ Form::label('admin_comment',Lang::get('deals::deals.admin_comment_lbl'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-7">
								{{  Form::textarea('admin_comment', null, array('class' => 'form-control')); }}
								<label class="error">{{{ $errors->first('admin_comment') }}}</label>
							</div>
						</div>                                                          
						
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-5">
								<button type="submit" name="approve_featured" value="approve_featured" class="btn green">
									<i class="fa fa-check"></i> {{ Lang::get('deals::deals.approve_button_lbl') }}
								</button>
								<button type="submit" name="disapprove" value="disapprove" class="btn red cancel">
									<i class="fa fa-times"></i> {{ Lang::get('deals::deals.unapprove_button_lbl') }}
								</button>
							</div>
						</div>
					</fieldset>
					<!-- END: ADMIN APPROVAL DEAILS DEAL -->
				{{ Form::close() }}               
			@else
				<p class="alert alert-info">{{ Lang::get('deals::deals.no_deal_featured_request_lbl') }}</p>
			@endif
		@endif            
	</div>
	
    <script type="text/javascript">
		jQuery.validator.addMethod("greaterThan", function(value, element, params) {
			if (!/Invalid|NaN/.test(new Date(value))) {
		        return new Date(value) >= new Date($(params).val());
		    }
		    return isNaN(value) && isNaN($(params).val())
		        || (Number(value) > Number($(params).val()));
		},'Must be greater than or equal to "From Date".');

		jQuery.validator.addMethod("greaterThanToday", function(value, element, params) {			
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
		
	
	
   		var mes_required = '{{ Lang::get('auth/form.required') }}';
   		$("#setFeaturedfrm").validate({
			focusInvalid: false,
			rules: {				
				date_featured_from: {
					required: true,
					date: true,
					greaterThanToday: true
				},
				date_featured_to: {
					required: true,
					date:true
				},
				
			},
			messages: {				
				date_featured_from:{
					required: mes_required
				},
				date_featured_to:{
					required: mes_required
				}
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
			
			
		$(function() {			
			
			$('.start_date').datepicker({
				dateFormat: 'dd/mm/yy',
				todayHighlight: true,
				autoclose: true,
				"minDate": 0,
				onSelect: function(selected) {
				  $('.end_date').datepicker("option","minDate", selected)
				}
			});
			$('.end_date').datepicker({
				dateFormat: 'dd/mm/yy',
				todayHighlight: true,
				autoclose: true,
				onSelect: function(selected) {
				  $('.start_date').datepicker("option","maxDate", selected)
				}
			});
		});			
   </script>    
@stop    