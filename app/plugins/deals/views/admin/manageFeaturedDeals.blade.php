@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!-- BEGIN: SUCCESS INFO -->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- END: SUCCESS INFO -->

	<!-- BEGIN: PAGE TITLE -->	
    <h1 class="page-title">{{ Lang::get('deals::deals.manage_featured_deals_head') }}</h1>
    <!-- END: PAGE TITLE -->
    
    <!-- BEGIN: SEARCH FORM -->
	{{ Form::open(array('id'=>'setFeaturedfrm', 'method'=>'post', 'class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
        	<!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file-text"></i> {{ Lang::get('deals::deals.set_featured_deal_head_lbl') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->
			
        	<div class="portlet-body form">
                <div id="search_holder">
                    <div class="form-body">
						<fieldset>
							<div class="form-group">
								{{ Form::label('deal_id', Lang::get('deals::deals.deal_id_head_lbl'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-3">
									{{ Form::text('deal_id', Input::get("deal_id"), array('class' => 'form-control' )) }}
									<label class="error" for="deal_id" generated="true">{{$errors->first('deal_id_from')}}</label>
								</div>
							</div>
							
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
						</fieldset>
                    </div>
					<div class="form-actions fluid">
						<div class="col-md-offset-2 col-md-5">
							<button type="submit" name="set_featured" value="set_featured" class="btn green">
								<i class="fa fa-check"></i> {{ trans("common.submit") }}
							</button>
							<button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/deals/manage-featured-deals') }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}</button>
						</div>
					</div>                        
                </div>
            </div>
        </div>
    {{ Form::close() }}
	<!-- END: SEARCH FORM -->
    
    <div class="portlet box blue-hoki">
    	<!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-file-text new-icon"><sup class="fa fa-tag"></sup></i> {{ Lang::get('deals::deals.manage_featured_deals_head') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->
        
        <div class="portlet-body">
            @if(count($deal_list) > 0 )
            	<!-- BEGIN: MANAGE FEATURED LIST -->
                {{ Form::open(array('id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-scrollable">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('deals::deals.featured_deal_head') }}</th>
                                    <th>{{ Lang::get('deals::deals.date_featured_from_head_lbl') }}</th>
                                    <th>{{ Lang::get('deals::deals.date_featured_to_head_lbl') }}</th>
                                    <th>{{ Lang::get('deals::deals.actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($deal_list as $deal)
                                	<?php
                                        $view_url = $deal_serviceobj->getDealViewUrl($deal);		
                                	?>
                                    <tr>
                                        <td>
                                            {{Form::checkbox('ids[]',$deal->deal_featured_id, false, array('class' => 'checkboxes js-ids', 'id' => "id_{$deal->deal_featured_id}") )}}                                        </td>                                       
                                        <td>
											<a target="_blank" href="{{$view_url}}" title="{{ Lang::get('deals::deals.view') }}">{{$deal->deal_title }}</a>
										</td>
                                        <td>{{ CUtil::FMTDate($deal->date_featured_from, 'Y-m-d', '') }}</td>
                                        <td>{{ CUtil::FMTDate($deal->date_featured_to, 'Y-m-d', '') }}</td>                                        
                                        <td>  
                                            <a href="javascript:;" onclick="doAction('{{$deal->deal_featured_id}}', 'unfeature')" class="btn btn-xs bg-red-sunglo" title="{{ Lang::get('deals::deals.remove_featured') }}">{{ Lang::get('deals::deals.remove_featured') }}</a>
                                        </td>                                        
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5">
                                        <p class="pull-left mt10 mr10">
                                            {{Form::select('action', $actions,'',array('class'=>'form-control', 'id'=>'deal_action'))}}
                                        </p>
                                        <p class="pull-left mt10">
                                            <input type="submit" value="Submit" class="btn green" id="page_action" name="page_action">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!-- END: MANAGE FEATURED LIST -->

                <!-- BEGIN: PAGINATION -->
                <div class="text-right">
                    {{ $deal_list->appends(array('deal_id' => Input::get('deal_id'),  
                    	'date_featured_from' => Input::get('date_featured_from'), 'date_featured_to' => Input::get('date_featured_to')))->links() }}
                </div>
                <!-- END: PAGINATION -->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('deals::deals.no_deals_added_msg') }}</div>
            @endif
            <div id="dialog-confirm" title="" style="display:none;">
                <span class="ui-icon ui-icon-alert"></span>
                <span id="dialog-confirm-content" class="show ml15"></span>
            </div>
    	</div>
	</div>    
@stop

@section('script_content')
	<script type="text/javascript">     
		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.each(function(){
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				});
			}
			else
			{
				checkboxes.each(function(){
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				});
			}
		});
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
		
		function doAction(id, selected_action)
		{			
			if(selected_action == 'unfeature')
			{
				$('#dialog-confirm-content').html('{{ Lang::get("deals::deals.confirm_unfeatured") }}');
				
			}
			$("#dialog-confirm").dialog({ title: '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#id_'+id).attr('checked', true).parent().addClass("checked");
						$('#deal_action').val(selected_action);
						document.getElementById("listFrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.deal_item_null_err_msg') }}");
				error_found = true;
			}
			var selected_action = $('#deal_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.select_action') }}");
				error_found = true;
			}
			if(!error_found)
			{				
				if(selected_action == 'unfeature')
				{
					$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.confirm_unfeatured') }}");
				}
			}
			if(error_found)
			{
				$("#dialog-confirm").dialog({ title:  '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title:  '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#listFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		}) 
		
		
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
		$(document).ready(function(){		
			$('#deal_id').keypress(validateNumber);			
			$("#setFeaturedfrm").validate({
				focusInvalid: false,
				rules: {		
					deal_id:{
						required: true
					},							
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
	</script>
@stop         