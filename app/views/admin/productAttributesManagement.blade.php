@extends('admin')
@section('content')
	<div id="catalogLoadingImageDialog" title="" style="display:none;">
		<p style="align:center;text-align:center;">
	        <img src="{{ URL::asset('images/general/loader.gif') }}" alt="loading" />
		</p>
	</div>

	<div id="dialog-delete-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span class="show ml15">{{ trans('admin/manageCategory.delete-attribute.delete_attribute_confirm') }}</span>
	</div>
	<div id="dialog-err-msg" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-err-msg-content" class="show ml15"></span>
	</div>
	{{ Form::open(array('id'=>'attributeListfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-hoki">
            <!--- TABLE TITLE STARTS --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list"></i> {{ trans('admin/manageCategory.product_attribute_title') }}
                </div>
                <a href="{{ URL::to('admin/manage-product-catalog') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe pull-right responsive-pull-none">
                    <i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}
                </a>
            </div>
            <!--- TABLE TITLE END --->

            <div class="portlet-body">
                <!--- INFO STARTS --->
                <div class="note note-info">
                    <strong>{{ trans('common.note') }}:</strong> {{ trans('admin/manageCategory.list-attribute.list_attribute_note') }}
                </div>
                <!--- INFO END --->

                <div class="table-responsive">
                    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="col-md-2">{{ trans('admin/manageCategory.list-attribute.attribute_label') }}</th>
                                <th class="col-md-3">{{ trans('admin/manageCategory.list-attribute.attribute_type') }}</th>
                                <th class="col-md-2">{{ trans('admin/manageCategory.list-attribute.attribute_validation') }}</th>
                                <th class="col-md-2">{{ trans('admin/manageCategory.list-attribute.attribute_default_value') }}</th>
                                <th class="col-md-1">{{ trans("common.status") }}</th>
                                <th class="col-md-1">{{ trans("common.action") }}</th>
                            </tr>
                        </thead>
                        <tbody class="formBuilderListBody product-attribute">
                            @if(count($attribute_details) > 0)
                                @foreach($attribute_details as $attributeKey => $attribute)
                                    <tr id="formBuilderRow_{{ $attribute['id'] }}" class="formBuilderRow">
                                        <td>{{ $attribute['attribute_label'] }}</td>
                                            <?php
                                                $attr_option_arr = Products::getAttributeOptionDetails($attribute);
                                            ?>
                                        <td>
                                            {{ $prod_attr_service_obj->getHTMLElement($attribute['attribute_question_type'], $attr_option_arr['attr_options'], $attribute['default_value']) }}
                                        </td>
                                        <td><div class="wid-">{{ $attribute['validation_rules'] }}</div></td>
                                        <td>{{ $attr_option_arr['default_value'] }}</td>
                                        <td>
                                            <?php
                                                $lbl_class = "";
                                                if(strtolower ($attribute['status']) == "active")
                                                    $lbl_class = "label-success";
                                                elseif(strtolower ($attribute['status']) == "inactive")
                                                    $lbl_class = "label-danger";
                                            ?>
                                            <span class="label {{ $lbl_class }}">{{ $attribute['status'] }}</span>
                                        </td>
                                        <td class="status-btn">
                                            <a class="btn blue btn-xs" onclick="javascript:formBuilderEditListRow({{$attribute['id']}});" href="javascript: void(0);" title="{{ trans('admin/manageCategory.edit_attribute') }}"><i class="fa fa-edit"></i> </a>
                                            <a class="btn btn-danger btn-xs" onclick="javascript:formBuilderRemoveListRow({{$attribute['id']}});" href="javascript: void(0);" title="{{ trans('admin/manageCategory.remove_attribute') }}"><i class="fa fa-trash-o"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach
                           @else
                                <tr><td colspan="9"><p class="alert alert-info">{{ trans('admin/manageCategory.list-attribute.attribute_not_found') }}</p></td></tr>
                           @endif
                        </tbody>
                    </table>
                </div>

                @if(count($attribute_details) > 0)
                    <div class="text-right">{{ $attribute_details->links() }}</div>
                @endif
            </div>
        </div>
    {{ Form::close() }}

    <div class="portlet box blue-madison">
        <!--- SEARCH TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-plus-circle"></i> {{ trans('admin/manageCategory.add_attribute') }} <!--<i class="fa fa-edit"></i> {{ trans('admin/manageCategory.edit_attribute') }}-->
            </div>
            <div class="tools">
                <a class="collapse" href="javascript:;"></a>
            </div>
        </div>
        <!--- SEARCH TITLE END --->

        <div class="portlet-body form">
            <div id="add_attributes">
                {{ Form::open(array('class' => 'form-horizontal',  'id' => 'addAttributefrm', 'name' => 'addAttributefrm')) }}
                    <div class="form-body">
                    	<!--- INFO STARTS --->
                         <div id="ajaxMsgs" class="note note-danger" style="display:none;"></div>
                         <div id="ajaxMsgSuccess" class="note note-success" style="display:none;"></div>
                         <!--- INFO END --->

                         {{ Form::hidden('attribute_action', 'add_attribute', array("id" => "attribute_action")) }}
                         {{ Form::hidden('attribute_id', '', array("id" => "attribute_id")) }}
                         {{ Form::hidden('attribute_options_count', '', array("id" => "attribute_options_count")) }}

                        <div class="form-group {{{ $errors->has('attribute_label') ? 'error' : '' }}}">
                            {{ Form::label('attribute_label', trans('admin/manageCategory.list-attribute.attribute_label'), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-4">
                                {{  Form::text('attribute_label', null, array('class' => 'form-control valid')); }}
                                <label class="error">{{{ $errors->first('attribute_label') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('description') ? 'error' : '' }}}">
                            {{ Form::label('description', trans('admin/manageCategory.add-attribute.attribute_description'), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                                {{  Form::text('description', null, array('class' => 'form-control valid')); }}
                                <label class="error">{{{ $errors->first('description') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('attribute_question_type') ? 'error' : '' }}}">
                            {{ Form::label('attribute_question_type', trans('admin/manageCategory.list-attribute.attribute_type'), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-4">
                                <div id="attribute_options_already_used" style="display:none;" class="cannotAccessMsg">
                                {{ trans('admin/manageCategory.add-attribute.attribute_option_in_use') }}</div>
                                {{ Form::select('attribute_question_type',  $ui_elements_all, '', array('class' => 'valid form-control select2me', 'onchange' => 'showOptions()')) }}
                                <label class="error">{{{ $errors->first('attribute_question_type') }}}</label>
                                <div id="options_ctrls" style="display:none;">
                                    <p class="text-muted">{{ trans('admin/manageCategory.add-attribute.attribute_options_title') }}</p>
                                    <ul id="attribute_options_group_ul" class="list-unstyled">
                                        <li id="attribute_options_group_ul_li" class="mb10">
                                        	<div class="row">
                                            	<div class="col-md-8">
                                                	{{  Form::text('attribute_options[]', null, array('class' => 'form-control')); }}
                                                </div>
                                                <div class="col-md-4 custom-pad1">
                                                    <a href="javascript: void(0);" onclick="addClone();" title="{{ trans('admin/manageCategory.add_option') }}" class="btn btn-xs btn-info mt5">
                                                    	<i class="fa fa-plus"></i>
                                                    </a>
                                                    <a href="javascript: void(0);" onclick="removeClone(this);" title="{{ trans('admin/manageCategory.remove_option') }}" class="btn btn-xs red mt5">
                                                    	<i class="fa fa-times"></i>
                                                    </a>
                                                    <a class="setAttributeOption btn btn-xs green mt5" href="javascript: void(0);" onclick="setSelected(this);" title="{{ trans('admin/manageCategory.select_by_default') }}" ><i class="fa fa-check"></i></a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('attribute_default_value') ? 'error' : '' }}}" id="default_value_row">
                            {{ Form::label('attribute_default_value', trans('admin/manageCategory.list-attribute.attribute_default_value'), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                                {{  Form::text('attribute_default_value', null, array('class' => 'form-control valid')); }}
                                <label class="error">{{{ $errors->first('attribute_default_value') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('validation_rules') ? 'error' : '' }}}">
                            {{ Form::label('validation_rules', trans('admin/manageCategory.list-attribute.attribute_validation'), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-7">
                                @if(count(Config::get('webshoppack.validation_rules')) > 0)
                                    <div class="checkbox-list custom-checklist">
                                        @foreach(Config::get('webshoppack.validation_rules') as $key => $val)
                                            <label class="checkbox-inline">
                                                {{ Form::checkbox('validation_rules[]', $val['name'], false, array("id" => $val['name'], "class" => "clsValidationRules ace")) }}
                                                {{ Form::label($val['name'], $val['caption']) }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <ul class="clsValidationRulesInputBoxesList list-unstyled">
                                        @foreach(Config::get('webshoppack.validation_rules') as $key => $val)
                                            @if($val['input_box'])
                                                <li class="{{ $val['name'] }}ListItem clsValidationRulesInputBoxesListItem mt10 clearfix" style="display:none;">
                                                	<div class="input-group col-md-7">
                                                        {{  Form::text($val['name'].'_input', null, array('id' => $val['name'].'_input', 'class' => 'form-control valid clsValidationRulesInputBox')); }}
                                                        <span class="input-group-addon">{{ Form::label($val['name'].'_input', $val['caption'], array('class' => 'no-margin')) }}</span>
                                                    </div>
                                                    <label for="{{ $val['name'].'_input' }}" generated="true" class="error"></label>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                                <label class="error">{{{ $errors->first('validation_rules') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('attribute_is_searchable') ? 'error' : '' }}}">
                            {{ Form::label('attribute_is_searchable', trans('admin/manageCategory.add-attribute.attribute_is_searchable'), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-5">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {{ Form::radio('attribute_is_searchable', 'yes', ($d_arr['attribute_is_searchable'] == 'yes') ? true : false, array('id' => 'attribute_is_searchable_yes', 'name' => 'attribute_is_searchable', 'class' => 'ace')) }}
                                        {{ Form::label('attribute_is_searchable_yes', trans('common.yes'))}}
                                    </label>
                                    <label class="radio-inline">
                                        {{ Form::radio('attribute_is_searchable', 'no', ($d_arr['attribute_is_searchable'] == 'no') ? true : false, array('id' => 'attribute_is_searchable_no', 'name' => 'attribute_is_searchable', 'class' => 'ace')) }}
                                        {{ Form::label('attribute_is_searchable_no', trans('common.no'))}}
                                    </label>
                                </div>
                            </div>
                            <label class="error">{{{ $errors->first('attribute_is_searchable') }}}</label>
                        </div>

                        <div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                            {{ Form::label('status', trans('common.status'), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-5">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {{ Form::radio('status', 'active', ($d_arr['status'] == 'active') ? true : false, array('id' => 'status_active', 'name' => 'status', 'class' => 'ace')) }}
                                        <span class="lbl">{{ Form::label('status_active', trans('common.active'))}}</span>
                                    </label>
                                    <label class="radio-inline">
                                        {{ Form::radio('status', 'inactive', ($d_arr['status'] == 'inactive') ? true : false, array('id' => 'status_inactive', 'name' => 'status', 'class' => 'ace')) }}
                                        <span class="lbl">{{ Form::label('status_inactive', trans('common.inactive'))}}</span>
                                    </label>
                                </div>
                                <label class="error">{{{ $errors->first('status') }}}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions fluid">
                        <div class="col-md-offset-3 col-md-5">
                            <button type="submit" name="attributes_add_submit" class="btn green">
                            	<i class="fa fa-save"></i> {{ trans('admin/manageCategory.save_attribute') }}
                            </button>
                            <button type="button" name="attributes_cancel" value="cancel_script" class="btn default" onclick="javascript:location.href='{{ Url::to('admin/product-attributes') }}'">
                            	<i class="fa fa-times"></i> {{ trans("common.cancel")}}
                            </button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop

@section('script_content')
	<script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.tablednd_0_5.js') }}"></script>
	<script type="text/javascript">
		/** Show edit & delete icons **/
		$(document).ready(function()
		{
			initializeEditDelete();
		});

		/** Enables the row to respond to user by highlighting and showing edit/delete buttons **/
		function initializeEditDelete()
		{
			 $('.formBuilderRow').mouseover(
				function()
				{
					$(this).addClass('formBuilderMouseover');
					$(this).children('.formBuilderAction').children('.formBuilderRowDelete').show();
					$(this).children('.formBuilderAction').children('.formBuilderRowEdit').show();
				}
			).mouseout(
				function()
				{
					$(this).removeClass('formBuilderMouseover');
					$(this).children('.formBuilderAction').children('.formBuilderRowDelete').hide();
					$(this).children('.formBuilderAction').children('.formBuilderRowEdit').hide();
				}
			);
		}
		var attribute_options_edit_name = 'attribute_options_';
		function showAddForm(row_id)
		{
			$('html,body').animate({scrollTop: $("#addAttributefrm").offset().top}, 300);
			resetAddForm();
			if (row_id > 0)
			{
				$('#add_attributes').show();
				$('#attribute_action').val('update_attribute');
			}
			else
			{
				$('#add_attributes').hide(); // only show difference that form changes between add and edit;
				$('#add_attributes').show();
				$('#attribute_action').val('add_attribute');
			}
		}

		function showOptions()
		{
			if (isOptionUIElement($('#attribute_question_type').val()))
			{
				$('#options_ctrls').css('display','block');
				$('#default_value_row').css('display','none');
				changeValidationFields(false);
				return true;
			}
			else
			{
				$('#options_ctrls').css('display','none');
				$('#default_value_row').css('display','block');
				removeOptions();
				changeValidationFields(true);
				return false;
			}
		}

		function isOptionUIElement(ui_element)
		{
			// UI User Interface
			<?php
			$options = Config::get('webshoppack.ui_options');
			if (is_array($options) && !empty($options))
			{
				$ui_elements = "var ui_elements = Array('" . implode("', '", array_keys($options)) . "');\n";
				echo $ui_elements; // javascript generation
			}
			else
			{
				echo "var ui_elements = Array();\n";
			}
			?>
			if (!ui_element)  return false;

			for ( i = 0 ; i < ui_elements.length ; i++)
			{
				if (ui_elements[i] == ui_element)
					return true;
			}

			return false;
		}

		var changeValidationFields = function()
		{
			var enable_option = arguments[0];
			var change_fields_arr = Array('numeric', 'alpha', 'maxlength', 'minlength');
			var fields_length = change_fields_arr.length;

			for ( i = 0 ; i < fields_length ; i++)
			{
				// enable validations
				if(enable_option)
				{
					$('#'+change_fields_arr[i]).removeAttr('disabled');
					if($('#'+change_fields_arr[i]).attr('checked'))
					{
						$('.'+change_fields_arr[i]+'ListItem').show();
						$('#'+change_fields_arr[i]+'_input').removeAttr('disabled');
					}
				}
				// disable validations
				else
				{
					$('#'+change_fields_arr[i]).attr('checked', false).attr('disabled', true);
					$('.'+change_fields_arr[i]+'ListItem').hide();
					$('#'+change_fields_arr[i]+'_input').attr('disabled', true);
				}
			}
		}

		/** Remove the attribute option as the default value. **/
		function removeSelected(elem)
		{
			$(elem).css('font-weight','');
		}

		function resetAddForm()
		{
			document.getElementById('addAttributefrm').reset(); // reset form
			removeOptions(); // if more than 1 option present remove them..
			showOptions(); // while in edit attrib, the form.reset doesn't hide options txtbx(s)
			$('#ajaxMsgs').hide('{{ trans('admin/manageCategory.add-attribute.attribute_err_add') }}');
			$('.clsValidationRulesInputBoxesListItem').hide();
			$('.clsValidationRulesInputBox').attr('disabled','disabled');

			// Remove attribute type disable option by default
			$('#attribute_options_already_used').hide();
			$('#attribute_question_type').removeAttr('disabled');
			$('#attribute_question_type').trigger('change');
		}

		function removeOptions()
		{
			// one parent should be present for cloning.. if no parent present make the first child as parent and remove child(s)
			if($('#attribute_options_group_ul li').size('.child') == $('#attribute_options_group_ul li').size())
			{
				// removing child class so that  the first element doesn't get removed
				$('#attribute_options_group_ul li').first().removeClass('child');
			}

			var obj = $('#attribute_options_group_ul li').remove('.child');
			//$('#attribute_options_group_ul li').children().children().children().first('input').val('');
			//$('#attribute_options_group_ul li').children().children().children().first('input').attr('name','attribute_options[]');
			$('#attribute_options_group_ul li').children().find('input[type=text]').filter(':visible:first').val('').attr('name','attribute_options[]');
			$('#attribute_default_value').val(''); // reset default value
			$('#attribute_options_count').val($('#attribute_options_group_ul li').size());
		}

		/** Used to display corresponding list items & text box based on checkbox checked value **/
		$('.clsValidationRules').click(function()
		{
			if ($(this).prop('checked'))
			{
				$('.' + $(this).val() + 'ListItem').show();
				$('#' + $(this).val() + '_input').removeAttr('disabled');
				//$('#' + $(this).val() + '_input').addClass('required');
			}else
			{
				$('.' + $(this).val() + 'ListItem').hide();
				$('#' + $(this).val() + '_input').attr('disabled','disabled');
				//$('#' + $(this).val() + '_input').removeClass('required');
			}
		});

		var addClone = function()
		{
			var txt_val = arguments[0];
			var options_id = arguments[1];

			if (!txt_val) txt_val = '';
			var clone = $('#attribute_options_group_ul li').last().clone(); // clone the last node


			if (typeof options_id !='undefined')
			{

				clone.find('input').attr('name', attribute_options_edit_name + options_id);
			}
			else
			{

				clone.find('input').attr('name','attribute_options[]');
			}
			// Set tabindex value
			var cur_tabindex = parseInt(clone.find('input').attr('tabindex'));
			clone.find('input').attr('tabindex', cur_tabindex);
			$('#attributes_add_submit').attr('tabindex', cur_tabindex + 5);

			clone.find('input').val(txt_val); // set value to empty

			clone.addClass('child'); // add a class so that these can be removed easily if required
			clone.insertAfter($('#attribute_options_group_ul li').last()); // insert after the last element
			clone.find('input').focus();
			$('#attribute_options_count').val($('#attribute_options_group_ul li').size());
			/* Added to remove attribute selection of added clone */
			remove_attr_sel_index = $('#attribute_options_group_ul li').find('a.setAttributeOption').size() - 1;
			remove_attr_sel_elem = $('#attribute_options_group_ul li').find('a.setAttributeOption').get()[remove_attr_sel_index];
			removeSelected(remove_attr_sel_elem);
			return false; // to prevent the default action anchor
		}

		/** Removes the attribute option
		*	Note: attribute_options_count is set here and in addClone function **/
		function removeClone(elem)
		{
			// if childNodes length is 3 then atleast one input element is present
			if ($(elem).parent().parent().parent().parent().children().size() > 1)
			{
				$(elem).parent().parent().parent().remove();
			}
			$('#attribute_options_count').val($('#attribute_options_group_ul li').size());
		}

		/** Sets the attribute option as the default value. This also is set as the attribute default value. **/
		function setSelected(elem)
		{
			if ($(elem).parent().parent().find('input').val() == '')
			{
				$('#dialog-err-msg-content').html('{{ trans('admin/manageCategory.add-attribute.attribute_option_err_add') }}');
				$("#dialog-err-msg").dialog({ title: '{{ trans('admin/manageCategory.product_attribute_title') }}', modal: true,
					buttons: { "{{ trans('common.ok') }}": function() { $(this).dialog("close"); } }
				});
			}
			else
			{
				$('#attribute_default_value').val($(elem).parent().parent().find('input').val());
				$('.setAttributeOption').css('background-color','#7cdd8b');
				$(elem).css('background-color','#35aa47');
				//$(elem).parent().find('input').css('background-color','#efefef');
			}
		}

		$("#addAttributefrm").submit(function()
		{
			if ($("#addAttributefrm").validate().form())
			{
				formBuilderAddListRow();
			}
			return false;
		});

		var mes_required = '{{ trans('common.required') }}';
		$("#addAttributefrm").validate({
			rules: {
				attribute_label: {
					required: true
			    },
			    attribute_question_type: {
			    	required: true
			    }<?php
			    	$validation_rules = Config::get('webshoppack.validation_rules');
			    	foreach($validation_rules as $validation_rule ){
			    		if ($validation_rule['input_box']) {
			    			echo ',' . "\n\t\t\t" .$validation_rule['name'] . '_input: {'.  "\n\t\t\t" ;
				    		if ( (boolean)$validation_rule['validation']) {
				    			$vr = explode("|", $validation_rule['validation']);
								// Fixed js error because of extra one ',' at last
				    			$first_rule = true;
				    			foreach($vr as $v){
				    				if(!$first_rule)
				    				{
				    					echo ','. "\n\t\t\t";
				    				}
				    				echo $v . ': true';
				    				$first_rule = false;
				    			}
				    		}
				    		echo '}';
			    		}
			    	}
			    ?>
				},

			messages: {
				title: {
					required: mes_required
				},
				attribute_question_type: {
						required: mes_required
					}<?php
						$validation_rules = Config::get('webshoppack.validation_rules');
						reset($validation_rules);
				    	foreach($validation_rules as $validation_rule ){
				    		if ($validation_rule['input_box']) {
					    		echo ',' . "\n\t\t\t" .$validation_rule['name'] . '_input: {' . "\n\t\t\t" ;
					    		if ( (boolean)$validation_rule['validation']) {
					    			$vr = explode("|", $validation_rule['validation']);
									// Fixed js error because of extra one ',' at last
					    			$first_question = true;
					    			foreach($vr as $v){
					    				if(!$first_question)
					    				{
					    					echo ','. "\n\t\t\t";
					    				}
					    				echo $v . ': "'. trans('common.common_err_tip_'. $v) . '"';
					    				$first_question = false;
					    			}
					    		}
					    		echo '}';
				    		}
				    	}
				    ?>
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

		/** Adds new attribute to the DB and generates html and adds the same to the attribute list **/
		function formBuilderAddListRow()
		{
			$('#attribute_question_type').removeAttr('disabled');
			var data = $('#addAttributefrm').serialize();
			catalogOpenLoadingDialog();
			var url = '{{URL::action('AdminProductAttributesController@postAdd')}}';
			if($('#attribute_action').val() == 'update_attribute')
			{
				url = '{{URL::action('AdminProductAttributesController@postUpdate')}}';
			}
			$.post(url, data,  function(data)
			{
				var returnedData = JSON.parse(data);
				if (returnedData.err)
				{
					$('#ajaxMsgs').html(returnedData.err_msg);
					$('#ajaxMsgs').show();
				}
				else
				{
					if (returnedData.list_row)
					{
						if ($('#attribute_action').val() == 'add_attribute')
						{
							$('.formBuilderListBody').append(returnedData.list_row);
						}
						else
						{
							if ($('#attribute_id').val())
							{
								//alert($('#formBuilderRow_' + $('#attribute_id').val()).prev("tr").length);
								var attr_list_length = $('#formBuilderRow_' + $('#attribute_id').val()).prev("tr").length;
								if(attr_list_length > 0)
								{
									var prevObj = $('#formBuilderRow_' + $('#attribute_id').val()).prev();
									$('#formBuilderRow_' +$('#attribute_id').val()).remove();
									prevObj.after(returnedData.list_row);
								}
								else
								{
									$('#formBuilderRow_' +$('#attribute_id').val()).remove();
									$('.formBuilderListBody').prepend(returnedData.list_row);
								}
							}
						}

						$('.formBuilderTable').tableDnDUpdate(); // updates table to respond to tableDND events
						initializeEditDelete(); // initialize mouseover and mouseout events to show and hide the edit/delete buttons on table lists
					}
					resetAddForm();
				}
				catalogCloseLoadingDialog();
				if(!returnedData.err)
				{
					if($('#attribute_action').val() == 'add_attribute')
					{
						$('#ajaxMsgSuccess').html('{{ trans('admin/manageCategory.add-attribute.attribute_added_success') }}').show().fadeOut(2000);
					}
					else
					{
						if(returnedData.unremoved_options_count == 0)
						{
							$('#ajaxMsgSuccess').html('{{ trans('admin/manageCategory.add-attribute.attribute_updated_success') }}').show().fadeOut(2000);
						}
						else
						{
							$('#ajaxMsgSuccess').html('{{ trans('admin/manageCategory.add-attribute.attribute_updated_success') }}'+returnedData.unremoved_options_count+' {{ trans('admin/manageCategory.add-attribute.attribute_options_in_use_msg') }}').show().fadeOut(2000);
						}
						$('#attribute_action').val('add_attribute');
					}
				}

			});
			return false; // for not allowing to submit the form
		}

		/* open loading dialog */
		function catalogOpenLoadingDialog()
		{
			$('#catalogLoadingImageDialog').dialog({
				open: function() {
		    		$(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
		  		},
				height: 'auto',
				width: 'auto',
				modal: true,
				title: '{{ trans('common.loading') }}'
			});
		}
		/* close loading dialog */
		function catalogCloseLoadingDialog()
		{
			$('#catalogLoadingImageDialog').dialog("close");
		}

		function formBuilderEditListRow(row_id)
		{
			var set_attribute_option = '';
			var data_params = 'row_id=' + row_id;
			catalogOpenLoadingDialog();
			$.getJSON('{{URL::action('AdminProductAttributesController@getAttributesRow')}}', data_params,function(data)
			{
				showAddForm(row_id);

				$('#attribute_options_json').val(data.options);
				$('#attribute_id').val(row_id);
				$('#attribute_label').val(data.attribute_label);
				$('#description').val(data.description);

				//question_type
				$('#attribute_question_type').val(data.attribute_question_type);
				var selected_text = $('#attribute_question_type option:selected').text();
				var selected_text_element_name = $('#s2id_attribute_question_type').find('input').attr('aria-labelledby');
				if(selected_text_element_name!='')
					$('#'+selected_text_element_name).text(selected_text);

				//is_searchable
				$("input:radio[name=attribute_is_searchable]").each(function(){
					$(this).removeAttr('checked');
					$(this).parent().removeClass('checked');
				})
				$('#attribute_is_searchable_' + data.is_searchable).attr('checked','checked');
				$('#attribute_is_searchable_' + data.is_searchable).parent().addClass('checked');


				//status
				$("input:radio[name=status]").each(function(){
					$(this).removeAttr('checked');
					$(this).parent().removeClass('checked');
				})
				$('#status_' + data.status).attr('checked','checked');
				$('#status_' + data.status).parent().addClass('checked');

				showOptions();
				$('#attribute_default_value').val(data.default_value);

				if (data.validation_rules)
				{
					validation = data.validation_rules.split('|');

					for ( i = 0 ; i < validation.length; i++)
					{
						if (validation[i].search('-') != -1)
						{
							validation_text_arr = validation[i].split('-');
							validation_text = validation_text_arr[0];
							$('#' + validation_text + '_input').parent().show('li');
							$('#' + validation_text + '_input').removeAttr('disabled');
							$('#' + validation_text + '_input').val(validation_text_arr[1]);

						}
						else
						{
							validation_text = validation[i];
						}
						$('#' + validation_text).closest('span').addClass('checked');
						$('#' + validation_text).attr('checked',true);
					}
				}

				if (data.options_size > 0)
				{
					// first input box is always available and empty other can be cloned using this. while edit data is updated in first input box.
					// and then clone needed and update it as necessary
					$('#attribute_options_group_ul li').find('input').val(data.options[0].option_label);

					if(data.options[0].id)
					{
						$('#attribute_options_group_ul li').find('input').attr('name', attribute_options_edit_name + data.options[0].id);
					}
					else
					{
						$('#attribute_options_group_ul li').find('input').attr('name','attribute_options[]');
					}


				    if(data.options[0].is_default_option == 'yes')
					{
						set_attribute_option = $('#attribute_options_group_ul li').find('a.setAttributeOption').get()[0];
					}
					/* ends to set first row options labels in all languages */
					for ( i = 1 ; i <  data.options_size ; i++ )
					{

						addClone(data.options[i].option_label, data.options[i].id);

						if(data.options[i].is_default_option == 'yes')
						{
							set_attribute_option = $('#attribute_options_group_ul li').find('a.setAttributeOption').get()[i];
						}
					}
					setSelected(set_attribute_option);
				}

				if (data.options_used)
				{
					//$('#attribute_options_already_used').show();
					//$('#attribute_question_type').attr('disabled',true);
				}

				catalogCloseLoadingDialog();
			});
		}

		function formBuilderRemoveListRow(row_id)
		{
			$("#dialog-delete-confirm").dialog({ title: '{{ trans('admin/manageCategory.product_attribute_title') }}', modal: true,
				buttons: {
					"Yes": function() {
						$(this).dialog("close");
						$.getJSON('{{URL::action('AdminProductAttributesController@getAttributesDelete')}}?row_id=' + row_id,
						{
							beforeSend:function()
							{
								catalogOpenLoadingDialog();
							}
						},
						function(data)
						{
							catalogCloseLoadingDialog();
							if(data.result == 'success')
							{
								$('#formBuilderRow_' + data.row_id).remove();
							}
							else
							{
								if(data.err_msg)
								{
									$('#dialog-err-msg-content').html(data.err_msg);
								}
								else
								{
									$('#dialog-err-msg-content').html('{{ trans('admin/manageCategory.delete-attribute.delete_attribute_operation_err') }}');
								}
								$("#dialog-err-msg").dialog({ title: '{{ trans('admin/manageCategory.product_attribute_title') }}', modal: true,
									buttons: { "{{ trans('common.ok') }}": function() { $(this).dialog("close"); } }
								});
							}

						});
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}
		/*

		$('#radio-test').click(function(event) {
        if(this.checked) {
            $('.radio-check').each(function() {
                this.checked = true;
				$(this).parent( "span" ).addClass( "checked");
            });
        }else{
            $('.radio-check').each(function() {
                this.checked = false;
				$(this).parent( "span" ).removeClass( "checked");
            });
        }
    });*/
	</script>
@stop