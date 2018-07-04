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
				<a href="{{ URL::to('variations/groups') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('variations::variations.manage_variations_group') }}
				</a>

				@if($action=='add')
				   <h1>{{ Lang::get('variations::variations.add_group') }}</h1>
				@else
					<h1>{{ Lang::get('variations::variations.update_group') }}</h1>
				@endif
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: INCLUDE NOTIFICATIONS -->
			@include('notifications')
			<!-- END: INCLUDE NOTIFICATIONS -->

			<!-- BEGIN: ADD VARIATIONS GROUP -->
			<?php
				//echo '<pre>';print_r($variation_group_details);
			?>

			<div class="well">
				{{ Form::model($variation_group_details, ['method' => 'post','class' => 'form-horizontal', 'id' => 'add_variations_group_form']) }}
					<div id="selSrchProducts">
						<fieldset>
							<div class="form-group">
								{{ Form::label('variation_group_name', Lang::get('variations::variations.group_name'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('variation_group_name', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('variation_group_name') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('short_description', Lang::get('variations::variations.short_description'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-5">
									{{ Form::textarea('short_description', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('short_description') }}}</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-2 control-label">{{ Lang::get('variations::variations.options_in_group') }}</label>
								<div class="col-md-9 row addvar-grp">
									<div class="col-md-5 col-sm-5 col-xs-5">
										<p class="margin-top-8 text-muted"><strong>{{ Lang::get('variations::variations.assigned_variations') }}</strong></p>
										<div class="tooltip-viewtop">
											<span data-toggle="tooltip" data-original-title="{{ Lang::get('variations::variations.assigned_variations_for_this_group') }}">
											{{ Form::select('assigned_variation[]', $assigned_variation_arr, NULL, array('id' => 'assigned_variation', 'multiple', 'class' => 'form-control min-hig180')) }}						
                                            </span>
                                            <label class="error">{{{ $errors->first('assigned_variation') }}}</label>
										</div>
									</div>
									<div class="col-md-1 col-sm-1 col-xs-1 text-center margin-top-80 no-padding">
										<p><button type="button"  name="assignment_variation_to_right" id="assignment_variation_to_right" class="btn bg-grey-gallery" value="" onclick="moveOptions(this.form.avail_variation, this.form.assigned_variation);" title="{{ Lang::get('variations::variations.assign_available_variations') }}"><i class="fa fa-angle-double-left"></i></button></p>
										<!--<input type="button"  name="assignment_variation_to_right" id="assignment_variation_to_right" value="<<" onclick="moveOptions(this.form.avail_variation, this.form.assigned_variation);" title="Assign available variations"/>-->

										<p><button type="button" name="assignment_variation_to_left" id="assignment_variation_to_left" class="btn bg-grey-gallery" value="" onclick="removeAssignee();" title="{{ Lang::get('variations::variations.remove_assignments') }}"><i class="fa fa-angle-double-right"></i></button></p>
										<!--<input type="button" name="assignment_variation_to_left" id="assignment_variation_to_left" value=">>" onclick="removeAssignee();" title="Remove assignments" />-->
									</div>
									<div class="col-md-5 col-sm-5 col-xs-5">
										<p class="margin-top-8 text-muted">
											<strong>{{ Lang::get('variations::variations.available_variations') }}</strong>
											<a href="{{ URL::to('variations/add-variation') }}" class="margin-left-5 badge badge-primary">{{ Lang::get('variations::variations.add_variations') }}</a>
										</p>
										<div class="tooltip-viewtop">
											<span data-toggle="tooltip" data-original-title="{{ Lang::get('variations::variations.available_variations') }}">
                                                {{ Form::select('avail_variation[]', $avail_variation_arr, NULL, array('id' => 'avail_variation', 'multiple', 'class' => 'form-control min-hig180')) }}
											</span>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									{{ Form::hidden('logged_user_id', $logged_user_id) }}
									{{ Form::hidden('action', $action) }}
									{{ Form::hidden('variation_group_id', $variation_group_id) }}
									@if($action=='add')
										<button type="submit" class="btn green">
											<i class="fa fa-plus"></i> {{Lang::get('variations::variations.add')}}
										</button>
									@else
										<button type="submit" class="btn blue-madison">
											<i class="fa fa-undo"></i> {{Lang::get('variations::variations.update')}}
										</button>
									@endif

									<button type="reset" name="addvariationgroup_reset" value="addvariationgroup_reset" class="btn default" onclick="javascript:location.href='{{ URL::to('variations/groups') }}'">
                                    	<i class="fa fa-times"></i> {{ Lang::get('variations::variations.cancel') }}
                                    </button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
			</div>
			<!-- END: ADD VARIATIONS GROUP -->
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var mes_required = '{{ Config::get('common.required') }}';
		$(document).ready(function() {
			$("#add_variations_group_form").validate({
                rules: {
	                variation_group_name: {
						required: true
					}
				},
	            messages: {
	                variation_group_name: {
						required: mes_required
					}
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					// Submit variation options add form
					$('#assigned_variation').find('option').each(function() {
			            $(this).attr('selected', 'selected');
			        });
					form.submit();
				}
            });
        });

        //code to move selectd assignees from left to right if double clicked in leftside dropdwon
		$('#avail_variation').dblclick(function()
		{
			var theSelFrom = $('#avail_variation').get(0);
			var theSelTo =  $('#assigned_variation').get(0);
			var selLength = theSelFrom.length;
			var selectedText = new Array();
			var selectedValues = new Array();
			var selectedCount = 0;
			var i;
			for(i=selLength-1; i>=0; i--)
			{
				if(theSelFrom.options[i].selected)
			    {
			    	selectedText[selectedCount] = theSelFrom.options[i].text;
			      	selectedValues[selectedCount] = theSelFrom.options[i].value;
			      	selectedCount++;
			    }
			}
			for(i=selectedCount-1; i>=0; i--)
			{
				addOption(theSelTo, selectedText[i], selectedValues[i]);
			}
			chkDuplicateAssignee();
		});

		// Code to check and remove duplicate in right side assignees dropdown
		function chkDuplicateAssignee()
		{
			var a = new Array();
	        $("#assigned_variation").children("option").each(function(x){
	            duplicate = false;
	            b = a[x] = $(this).val();
	            for (i=0;i<a.length-1;i++){
	                    if (b ==a[i]) duplicate =true;
	            }
	            if (duplicate) $(this).remove();
	        })
		}

		// Code to remove selected assignee from right drop down if left arrow button is clicked
		function removeAssignee()
		{
			$("#assigned_variation option:selected").remove();
		}

		// Code to remove selected assignee from right drop down if double clicked
		$('#assigned_variation').dblclick(function()
		{
			$("#assigned_variation option:selected").remove();
		});

		<!--
		var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
		function addOption(theSel, theText, theValue)
		{
		  var newOpt = new Option(theText, theValue);
		  var selLength = theSel.length;
		  theSel.options[selLength] = newOpt;
		  chkDuplicateAssignee();
		}
		//code to move selectd assignees from left to right if right arrow button is clicked
		function moveOptions(theSelFrom, theSelTo)
		{

		  var selLength = theSelFrom.length;
		  var selectedText = new Array();
		  var selectedValues = new Array();
		  var selectedCount = 0;
		  var i;
		  for(i=selLength-1; i>=0; i--)
		  {
		    if(theSelFrom.options[i].selected)
		    {
		      selectedText[selectedCount] = theSelFrom.options[i].text;
		      selectedValues[selectedCount] = theSelFrom.options[i].value;
		      selectedCount++;
		    }
		  }

		  for(i=selectedCount-1; i>=0; i--)
		  {
		    addOption(theSelTo, selectedText[i], selectedValues[i]);
		  }

		  if(NS4) history.go(0);
		}
	</script>
@stop