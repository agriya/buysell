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
				<a href="{{ URL::to('deals') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('deals::deals.manage_deals') }}
				</a>

				@if($action=='add')
				   <h1>{{ Lang::get('deals::deals.add_deal') }}</h1>
				@else
					<h1>{{ Lang::get('deals::deals.update_deal') }}</h1>
				@endif
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: DEALS SUBMENU -->
			@include('deals::deal_submenu')
			<!-- END: DEALS SUBMENU -->

			<!-- BEGIN: ADD DEALS -->
			<div class="well">
            	<!-- BEGIN: INFO BLOCK -->
                @if ($success_message = Session::get('success'))
                    <div class="note note-success">{{ $success_message }}</div>
                @endif

                @if ($error_message = Session::get('error'))
                    <div class="note note-danger">{{ $error_message }}</div>
                @endif
                <!-- END: INFO BLOCK -->

                {{ Form::model($deal_details, [ 'url' => Url::to('deals/add-deal'),'method' => 'post', 'id' => 'add_deals_form', 'class' => 'form-horizontal', 'files' => true]) }}
					<div id="selSrchProducts">
						<fieldset>
							<div class="form-group">
								{{ Form::label('deal_title', Lang::get('deals::deals.deal_title'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
                                    {{  Form::text('deal_title', null, array('class' => 'form-control valid', 'onchange' => 'generateSlugUrl()', 'onblur' => 'generateSlugUrl()')); }}
									<label class="error">{{{ $errors->first('deal_title') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('url_slug', Lang::get('deals::deals.url_slug'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
                                    {{ Form::text('url_slug', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('url_slug') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('deal_short_description', Lang::get('deals::deals.short_description'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
									{{ Form::textArea('deal_short_description', null, array('class' => 'form-control valid', 'rows' => '10', 'cols' => '10', 'maxlength' => Config::get('plugin.deals.deal_highlighttext_max_chars'))) }}
                                    <div id="short_desc_count"></div>
									<label class="error">{{{ $errors->first('deal_short_description') }}}</label>
								</div>
							</div>

                            <div class="form-group">
								{{ Form::label('deal_description', Lang::get('deals::deals.deal_summary'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-7">
                                    {{  Form::textarea('deal_description', null, array('class' => 'form-control valid fn_editor', 'rows' => '7', 'id' => 'deal_description')) }}
									<label class="error">{{{ $errors->first('deal_description') }}}</label>
								</div>
							</div>

                            <div class="form-group">
								{{ Form::label('meta_title', Lang::get('deals::deals.meta_title'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-6">
                                    {{ Form::text('meta_title', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('meta_title') }}}</label>
								</div>
							</div>

                            <div class="form-group">
								{{ Form::label('meta_keyword', Lang::get('deals::deals.meta_keyword'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-6">
                                    {{ Form::textArea('meta_keyword', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('meta_keyword') }}}</label>
								</div>
							</div>

                            <div class="form-group">
								{{ Form::label('meta_description', Lang::get('deals::deals.meta_description'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-6">
                                    {{ Form::textArea('meta_description', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('meta_description') }}}</label>
								</div>
							</div>

                            <div class="form-group">
								{{ Form::label('deal_image', Lang::get('deals::deals.deal_image'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-5">
                                    {{ Form::file('deal_image',array('class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'), 'id' => 'deal_image', )) }}
                                    @if($action=='edit')
										<?php
											$d_img_arr['deal_id'] = $deal_details->deal_id;
											$d_img_arr['deal_title'] = $deal_details->deal_title;
											$d_img_arr['img_name'] = $deal_details->img_name;
											$d_img_arr['img_ext'] = $deal_details->img_ext;
											$d_img_arr['img_width'] = $deal_details->img_width;
											$d_img_arr['img_height'] = $deal_details->img_height;
											$d_img_arr['l_width'] = $deal_details->l_width;
											$d_img_arr['l_height'] = $deal_details->l_height;
											$d_img_arr['t_width'] = $deal_details->t_width;
											$d_img_arr['t_height'] = $deal_details->t_height;
											$p_thumb_img = $deal_serviceobj->getDealDefaultThumbImage($deal_details->deal_id, 'thumb', $d_img_arr);
										?>
										<p class="margin-top-10 margin-bottom-5 imgsize-95X95"><img src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></p>
                                    @endif
									<div>
										<small class="text-muted"> {{ $d_arr['file_noteinfo'] }}</small>
										<small class="text-muted"> {{ $d_arr['file_noteinfo1'] }}</small>
									</div>
									<label class="error" for="deal_image" generated="true">{{{ $errors->first('deal_image') }}}</label>
								</div>
							</div>

                            <div class="form-group {{{ $errors->has('applicable_for') ? 'error' : '' }}}">
								{{ Form::label('applicable_for', Lang::get('deals::deals.deal_for'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
									<?php
                                        $applicable_for_all_items = (isset($d_arr['applicable_for']) && $d_arr['applicable_for']=='all_items' ) ? true : false;
                                        $applicable_for_single_item = (isset($d_arr['applicable_for']) && $d_arr['applicable_for'] =='single_item') ? true : false;
										$applicable_for_selected_items =  false;
										$old_selection = Input::old('applicable_for', "all_items");

                                        if(isset($deal_details->applicable_for) || Input::old('applicable_for', '') != '')
                                        {
											$applicable_for_all_items = ((isset($deal_details->applicable_for) && $deal_details->applicable_for == "all_items") || $old_selection == "all_items") ? true : false;
											$applicable_for_single_item = ((isset($deal_details->applicable_for) && $deal_details->applicable_for == "single_item") || $old_selection == "single_item") ? true : false;
											$applicable_for_selected_items = ((isset($deal_details->applicable_for) && $deal_details->applicable_for == "selected_items") || $old_selection == "selected_items") ? true : false;
                                        }
                                    ?>
                                    <label class="radio-inline margin-right-10">
                                        {{Form::radio('applicable_for', 'all_items', Input::old('applicable_for', $applicable_for_all_items), array('class' => 'dealforoption', 'id' => 'applicable_for_all_items')) }}
                                        <label for="applicable_for_all_items">{{ Lang::get('deals::deals.deal_for_all_items') }}</label>
                                    </label>
									<label class="radio-inline">
                                        {{Form::radio('applicable_for', 'single_item', Input::old('applicable_for', $applicable_for_single_item) , array('class' => 'dealforoption', 'id' => 'applicable_for_single_item')) }}
                                        <label for="applicable_for_single_item">{{ Lang::get('deals::deals.deal_for_single_items') }}</label>
                                    </label>
                                    <label class="radio-inline">
                                        {{Form::radio('applicable_for', 'selected_items', Input::old('applicable_for', $applicable_for_selected_items) , array('class' => 'dealforoption', 'id' => 'applicable_for_selected_items')) }}
                                        <label for="applicable_for_selected_items">{{ Lang::get('deals::deals.deal_for_selected_items') }}</label>
                                    </label>
                                    <label class="error">{{{ $errors->first('applicable_for') }}}</label>
								</div>
							</div>

                            <?php
								$selItem = ($applicable_for_single_item && isset($d_arr['assigned_items'])) ? $d_arr['assigned_items'] : null;
								$selItemList = ($applicable_for_selected_items && isset($d_arr['assigned_items'])) ? $d_arr['assigned_items'] : array();
							?>

							<!-- BEGIN: SINGLE SELECTION BLOCK -->
                            <div id="itemsingleselection" @if(!$applicable_for_single_item) style="display:none" @endif>
                            	<div class="form-group {{{ $errors->has('applicable_for') ? 'error' : '' }}}">
                                    {{ Form::label('deal_item', Lang::get('deals::deals.deal_for_item'), array('class' => 'col-md-2 control-label')) }}
                                    <div class="col-md-6">
                                        {{ Form::select('deal_item', array("" => trans('common.select_option'))+$d_arr['shop_items'], $selItem, array('class' => 'form-control select2me input-medium valid')) }}
                                        <label class="error">{{{ $errors->first('deal_item') }}}</label>
                                    </div>
                                </div>

                                @if($d_arr['tipping_apply_single_item'] == 1)
                                    <div class="form-group {{{ $errors->has('tipping_qty_for_deal') ? 'error' : '' }}}">
                                        {{ Form::label('tipping_qty_for_deal', Lang::get('deals::deals.tipping_qty'), array('class' => 'col-md-2 control-label')) }}
                                        <div class="col-md-3">
                                            {{ Form::text('tipping_qty_for_deal', null, array('class' => 'form-control valid', 'maxlength'=> "5")) }}
                                            <small class="text-muted">{{{ Lang::get('deals::deals.min_qty_note_msg') }}} </small>
                                            <label class="error">{{{ $errors->first('tipping_qty_for_deal') }}}</label>
                                        </div>
                                    </div>
                                @endif
							</div>
                            <!-- END: SINGLE SELECTION BLOCK -->

                            <!-- BEGIN: MULTIPLE SELECTION BLOCK -->
                            <div id="itemlistselection" @if(!$applicable_for_selected_items) style="display:none" @endif>
                            	<div class="form-group {{{ $errors->has('applicable_for') ? 'error' : '' }}}">
                                    <div class="col-md-offset-2 col-md-9">
                                     	<div class="row adddeals-md">
											<div class="col-md-5 col-sm-5 col-xs-5">
												<label for="default_items_list">{{ Lang::get('deals::deals.items_added_by_me') }} </label>
												{{ Form::select('default_items_list[]', $d_arr['shop_items'], null, array('class' => 'form-control select2me valid', 'id' => 'default_items_list', 'multiple' => "multiple")) }}
											</div>

											<div class="col-md-2 text-center col-sm-2 col-xs-2">
												<button type="button" class="btn btn-xs bg-grey-cascade" name="assign_item_to_right" id="assign_item_to_right" onclick="moveOptions(this.form.default_items_list, this.form.selected_items_list);"><i class="fa fa-angle-double-right"></i></button>
												<button type="button" class="btn btn-xs bg-grey-cascade" name="assign_item_to_left" id="assign_item_to_left"  onclick="removeAssignee();">
													<i class="fa fa-angle-double-left"></i>
												</button>
											</div>

											<div class="col-md-5 col-sm-5 col-xs-5">
												<label for="selected_items">{{ Lang::get('deals::deals.selected_items') }}</label>
												{{ Form::select('selected_items_list[]', $selItemList, $selItemList, array('class' => 'form-control select2me valid', 'id' => 'selected_items_list', 'multiple' => "multiple")) }}
											</div>
										</div>
										<label class="error"  for="selected_items_list" generated="true">{{{ $errors->first('selected_items_list') }}}</label>
										<p class="margin-bottom-5"><small>{{ Lang::get('deals::deals.assign_item_note_msg') }}</small></p>
										<p class="margin-bottom-5"><small>{{ Lang::get('deals::deals.remove_item_assignment_note_msg') }}</small></p>
										<p class="margin-bottom-5"><small>{{ Lang::get('deals::deals.multiple_assignment_note_msg') }}</small></p>
									</div>
                                </div>
							</div>
                            <!-- FOR MULTIPLE SELECTION BLOCK ENDS  -->

                            <div class="form-group">
								{{ Form::label('discount_percentage', Lang::get('deals::deals.deal_discount_percentage'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
                                    {{ Form::text('discount_percentage', null, array('class' => 'form-control valid', 'maxlength' => 5)) }}
									<label class="error">{{{ $errors->first('discount_percentage') }}}</label>
								</div>
							</div>

							@if($d_arr['tipping_apply_single_item'] == 0)
                                <div class="form-group {{{ $errors->has('tipping_qty_for_deal') ? 'error' : '' }}}">
                                    {{ Form::label('tipping_qty_for_deal', Lang::get('deals::deals.tipping_qty'), array('class' => 'col-md-2 control-label', 'maxlength' => 5)) }}
                                    <div class="col-md-6">
                                        {{ Form::text('tipping_qty_for_deal', null, array('class' => 'form-control valid', 'maxlength'=> "5"  )) }}
                                        <small class="text-muted">{{{ Lang::get('deals::deals.min_qty_note_msg') }}} </small>
                                        <label class="error">{{{ $errors->first('tipping_qty_for_deal') }}}</label>
                                    </div>
                                </div>
							@endif

                            <div class="form-group">
								{{ Form::label('date_deal_from', Lang::get('deals::deals.deal_starting_date'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
                                    <div data-date-format="yyyy-mm-dd" class="input-group date date-picker">
										{{ Form::text('date_deal_from', Input::get("frodate_deal_from_date"), array('id' => 'date_deal_from', 'class' => 'form-control valid start_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
										<span class="input-group-btn">
											<label class="btn default" for="date_deal_from"><i class="fa fa-calendar"></i></label>
										</span>
									</div>
									<label class="error" for="date_deal_from" generated="true">{{{ $errors->first('date_deal_from') }}}</label>
								</div>
							</div>

                            <div class="form-group">
								{{ Form::label('date_deal_to', Lang::get('deals::deals.deal_ending_date'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
                                    <div data-date-format="yyyy-mm-dd" class="input-group date date-picker">
										{{ Form::text('date_deal_to', Input::get("date_deal_to"), array('id' => 'date_deal_to', 'class' => 'form-control valid end_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
										<span class="input-group-btn">
											<label class="btn default" for="date_deal_to"><i class="fa fa-calendar"></i></label>
										</span>
									</div>
									<label class="error" for="date_deal_to" generated="true">{{{ $errors->first('date_deal_to') }}}</label>
								</div>
							</div>

                            @if($action=='edit')
								<div class="form-group">
                                    {{ Form::label('deal_status', Lang::get('deals::deals.deal_status'), array('class' => 'col-md-2 control-label')) }}
                                    <div class="col-md-6 margin-top-8">
                                        <?php
											$status_lbl = (Lang::has('deals::deals.DEAL_STATUS_'.strtoupper($deal_details->deal_status))) ? Lang::get('deals::deals.DEAL_STATUS_'.strtoupper($deal_details->deal_status)): str_replace('_', ' ', $deal_details->deal_status);
										?>
                                        @if($deal_details->deal_status == "to_activate")
                                            <label class="label label-warning">{{ $status_lbl }}</label>
                                        @elseif($deal_details->deal_status == "active")
                                            <label class="label label-success">{{ $status_lbl }}</label>
                                        @elseif($deal_details->deal_status == "deactivated")
                                            <label class="label bg-red-pink">{{ $status_lbl }}</label>
                                        @elseif($deal_details->deal_status == "expired")
                                            <label class="label bg-red">{{ $status_lbl }}</label>
                                        @elseif($deal_details->deal_status == "closed")
                                            <label class="label label-default">{{ $status_lbl }}</label>
                                        @endif
                                    </div>
                                </div>
                            @endif

							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									{{ Form::hidden('logged_user_id', $logged_user_id) }}
									{{ Form::hidden('action', $action) }}
									{{ Form::hidden('deal_id', $deal_id, array('id' => 'deal_id')) }}
                                    @if($action=='add')
                                        <button type="submit" class="btn green" id="deal_submit" name="deal_submit">
                                            <i class="fa fa-plus"></i> {{Lang::get('deals::deals.add')}}
                                        </button>
                                    @else
                                        <button type="submit" class="btn blue-madison" id="deal_update" name="deal_update">
                                            <i class="fa fa-arrow-up"></i> {{Lang::get('deals::deals.update_deal')}}
                                        </button>
                                    @endif
									<button type="reset" name="addvariation_reset" value="addvariation_reset" class="btn default" onclick="javascript:location.href='{{ URL::to('deals/add-deal') }}'"><i class="fa fa-times"></i> {{ Lang::get('deals::deals.cancel') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
			</div>
			<!-- END: ADD DEALS -->
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var is_edit = 0;
		@if($action=='edit')
			is_edit = 1;
		@endif
        function alltrimhyphen(str) {
			return str.replace(/^\-+|\-+$/g, '');
		}

		tinymce.init({
			menubar: "tools",
			selector: "textarea.fn_editor",
			mode : "exact",
			elements: "deal_description",
			removed_menuitems: 'newdocument',
			apply_source_formatting : true,
			remove_linebreaks: false,
			height : 400,
			plugins: [
			"advlist autolink lists link image charmap print preview anchor",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu paste emoticons jbimages"
			],
			toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons",
			relative_urls: false,
			remove_script_host: false
		});


		function generateSlugUrl() {
			var title = $("#deal_title").val();
				if(title.trim() == "")
				$("#url_slug").val('');
				else if($("#url_slug").val().trim() == ''){
				var slug_url = title.replace(/[^a-z0-9]/gi, '-');
				slug_url = slug_url.replace(/(-)+/gi, '-');
				slug_url = slug_url.replace(/^(-)+|(-)+$/g, '');
				$("#url_slug").val(slug_url.toLowerCase());
			}
		}


		$('.dealforoption').click(function(){
			var selVal = $(this).val();
			if(selVal == 'selected_items')
			{
				$('#itemlistselection').show();
				$('#itemsingleselection').hide();
			}
			else if(selVal == 'single_item')
			{
				$('#itemsingleselection').show();
				$('#itemlistselection').hide();
			}
			else
			{
				$('#itemlistselection').hide();
				$('#itemsingleselection').hide();
			}
		});

		// Code to set selected items from the list by the time of  submit the form.
		$('#deal_submit, #deal_update').click(function() {
			$('#selected_items_list').find('option').each(function() {
				$(this).attr('selected', 'selected');
			});
		});


		//code to move selectd items from left to right if double clicked in leftside dropdwon
		$('#default_items_list').dblclick(function()
		{
			var theSelFrom = $('#default_items_list').get(0);
			var theSelTo =  $('#selected_items_list').get(0);
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

		// Code to check and remove duplicate in right side item dropdown
		function chkDuplicateAssignee()
		{
			var a = new Array();
			$("#selected_items_list").children("option").each(function(x){
				duplicate = false;
				b = a[x] = $(this).val();
				for (i=0;i<a.length-1;i++){
				   if (b ==a[i]) duplicate =true;
				}
				if (duplicate) $(this).remove();
			})
		}

		// Code to remove selected item from right drop down if left arrow button is clicked
		function removeAssignee()
		{
			$("#selected_items_list option:selected").remove();
		}

		// Code to remove selected item from right drop down if double clicked
		$('#selected_items_list').dblclick(function()
		{
			$("#selected_items_list option:selected").remove();
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
		//code to move selectd item from left to right if right arrow button is clicked
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
		//-->

		function validateNumber(evt) {
			/*
			var key = window.event ? event.keyCode : event.which;

			if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 46
			 || event.keyCode == 37 || event.keyCode == 39) {
				return true;
			}
			else if ( key < 48 || key > 57 ) {
				return false;
			}
			else return true;

			*/
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
				return false;
			return true;

		};


		jQuery.validator.addMethod("greaterThan", function(value, element, params) {
		if(is_edit == 1)
				return true;
			if (!/Invalid|NaN/.test(new Date(value))) {
			  //  return new Date(value) >= new Date($(params).val());
		    }
		    return isNaN(value) && isNaN($(params).val())
		        || (Number(value) > Number($(params).val()));
		},'Must be greater than or equal to "From Date".');

		/*
		jQuery.validator.addMethod("enddate", function(value, element){
           var startdatevalue = $('.startdate').val();
            return Date.parse(startdatevalue) <= Date.parse(value);
     	}, 'End Date should be greater than equal to Start Date.');
		*/

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

			$('#deal_submit, #deal_update').click(function() {
				 tinymce.triggerSave();
			});

			$('[id^=discount_percentage]').keypress(validateNumber);
			$('[id^=tipping_qty_for_deal]').keypress(validateNumber);

			$("#add_deals_form").validate({
                rules: {
	                deal_title: {
						required: true
					},
					url_slug:{
						required: true
					},
					deal_short_description:{
						required: true
					},
					deal_description:{
					     required: true
					},
					deal_image:{
						required: {
                            depends: function(element) {
                                return ($("#deal_id").val() > 0 ) ? false : true;
                            }
                        }
					},
					deal_item:{
						required: "#applicable_for_single_item:checked"
					},
					selected_items_list:{
						required: "#applicable_for_selected_items:checked"
					},
					discount_percentage:{
						required: true
					},
					date_deal_from: {
						required: true,
						date: true,
						greaterThanToday: true
					},
					date_deal_to: {
						required: true,
						date:true,
						greaterThan: '#date_deal_from'
					}
				},
	            messages: {
	                deal_title: {
						required: mes_required
					},
					url_slug:{
						required: mes_required
					},
					deal_short_description:{
						required: mes_required
					},
					deal_description:{
						required: mes_required
					},
					deal_image:{
						required: mes_required
					},
					deal_item:{
						required: mes_required
					},
					selected_items_list:{
						required: mes_required
					},
					discount_percentage:{
						required: mes_required
					},
					date_deal_from:{
						required: mes_required
					},
					date_deal_to:{
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
					autoclose: true,
					"minDate": 0,
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

			var highlight_max = "{{ Config::get('plugin.deal_highlighttext_max_chars') }}";
			$('#deal_short_description').keyup(function(e) {
				var text_length = $('#deal_short_description').val().length;
				var text_remaining = highlight_max - text_length;
				if(text_remaining >= 0)
				{
					$('#short_desc_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
				}
				else
				{
					 $('#deal_short_description').val($('#deal_short_description').val().substring(0, highlight_max));
				}
			});

        });

	</script>
@stop