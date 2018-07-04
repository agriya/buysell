@extends('admin')
@section('content')
	<!--- PAGE TITLE STARTS --->
    <a href="javascript:void(0);" onclick="addSubCategory({{ $root_category_id }});" title="{{ trans('admin/manageCategory.add_top_level_cat') }}" class="btn btn-success pull-right responsive-btn-block btn-xs mt10"><i class="fa fa-plus-circle"></i> {{ trans('admin/manageCategory.add_top_level_cat') }}</a>
    <h1 class="page-title">{{ trans('admin/manageCategory.manage_product_catalog_title') }}</h1>
    <!--- PAGE TITLE END --->

    <!--- LOADING STARTS --->
    <div id="catalogLoadingImageDialog" title="" style="display:none;">
		<p style="align:center;text-align:center;">
	        <img src="{{ URL::asset('images/general/loader.gif') }}" alt="loading" />
		</p>
	</div>
    <!--- LOADING END --->

    <!--- DELETE CONFIRM STARTS --->
	<div id="dialog-category-delete-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span class="show ml15">{{ trans('admin/manageCategory.delete-category.delete_category_confirm') }}</span>
	</div>
    <!--- DELETE CONFIRM END --->

    <!--- ERROR ALERT STARTS --->
	<div id="dialog-category-err-msg" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-category-err-msg-content" class="show ml15"></span>
	</div>
    <!--- ERROR ALERT END --->

	<!--- DELETE CONFIRM STARTS --->
    <div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
          <p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('admin/manageCategory.delete-category.delete_category_image_confirm') }}</small></p>
    </div>
    <!--- DELETE CONFIRM END --->

	<div class="portlet box blue-madison">
        <!--- TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-folder"></i> {{ trans('admin/manageCategory.root_category') }}
            </div>
        </div>
        <!--- TITLE END --->

        <div class="portlet-body">
        	<!--- TREE WIEW STARTS --->
            <div id="demo" class="demo customjs-tree"></div>
            <!--- tree wiew starts --->
        </div>
    </div>

    <!--- TABS AND TAB CONTENT STARTS --->
	@include('admin.manageProductCatalogTabs')
    <!--- TABS AND TAB CONTENT END --->
@stop

@section('script_content')
	<script src="{{ URL::asset('/js/formbuilder/js/jquery.form.js') }}"></script>
	<script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.cookie.js') }}"></script>
	<script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.hotkeys.js') }}"></script>
	<script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.jstree.js') }}"></script>
	<script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.tablednd_0_5.js') }}"></script>

	<script type="text/javascript">
		var category_id = '{{ $root_category_id }}';
		var root_category_id = '{{ $root_category_id }}';
		var selected_category = '{{ $root_category_id }}';
		var call_default_functions = true;
		/* total count of ajax functions checked when open & close loading dialog */
		var total_ajax_functions_to_complete = 0;
		var common_yes_label = "{{ trans('common.yes') }}";
		var common_no_label = "{{ trans('common.no') }}";

		$(function () {
			// Settings up the tree - using $(selector).jstree(options);
			// All those configuration options are documented in the _docs folder
			$("#demo")
				.jstree({
					// the list of plugins to include
					"plugins" : [ "themes", "json_data", "ui", "crrm", "search", "types", "hotkeys", "contextmenu", "dnd"  ],
					//[ "themes", "json_data", "ui", "crrm", "search", "types", "hotkeys", "contextmenu", "dnd" ],
					// Plugin configuration

					// I usually configure the plugin that handles the data first - in this case JSON as it is most common
					"json_data" : {
						// I chose an ajax enabled tree - again - as this is most common, and maybe a bit more complex
						// All the options are the same as jQuery's except for `data` which CAN (not should) be a function
						"ajax" : {
							// the URL to fetch the data
							"url" : "{{URL::action('AdminManageProductCatalogController@getCategoryTreeDetails')}}",
							// this function is executed in the instance's scope (this refers to the tree instance)
							// the parameter is the node being loaded (may be -1, 0, or undefined when loading the root nodes)
							"data" : function (n) {
								// the result is fed to the AJAX request `data` option
								return {
									"operation" : "get_children",
									"category_id" : n.attr ? n.attr("category_id").replace("node_","") : root_category_id,
									"id" : n.attr ? n.attr("category_id").replace("node_","") : root_category_id
								};
							}
						}
					},
						// For UI & core - the nodes to initially select and open will be overwritten by the cookie plugin

					// the UI plugin - it handles selecting/deselecting/hovering nodes
					"ui" : {
						// this makes the node with ID node_4 selected onload
						"initially_select" : [ "node_" + category_id ]
					},
					// the core plugin - not many options here
					"core" : {
						// just open those two nodes up
						// as this is an AJAX enabled tree, both will be downloaded from the server
						"initially_open" : [ "node_" + category_id ]
					},
					"contextmenu": {
						"items" : function(node){
							var obj = {
								"add_sub_cat" : {
									"separator_before" : false,
									"icon" : false,
									"separator_after" : false,
									"label" : "{{ trans('admin/manageCategory.add_sub_category') }}",
									"action" : function (obj) {
										var category_node = obj.attr('category_id');
		   								category_id = category_node ? category_node.replace("node_","") : root_category_id;
										addSubCategory(category_id);
									}
								},
								"remove_cat" : {
									"separator_before" : false,
									"icon" : false,
									"separator_after" : false,
									"label" : "{{ trans('admin/manageCategory.delete_category') }}",
									"action" : function (obj) {
										var category_node = obj.attr('category_id');
		   								category_id = category_node ? category_node.replace("node_","") : root_category_id;
										deleteCategory(category_id);
									}
								}
							}
							return obj;
						}
					}
				})
				.bind("move_node.jstree", function (e, data) {
					data.rslt.o.each(function (i) {

						node_id = $(this).attr("id").replace("node_","");
						position = data.rslt.cp + i;
						ref = data.rslt.cr === -1 ? '{{ $root_category_id }}' : data.rslt.np.attr("id").replace("node_","");
						moveMySelectedCategory(node_id, position, ref, data);
					});
				});
		});
		
		var moveMySelectedCategory = function()
		{
			setAjaxFunctionsCount();
			var category_ajax_url = 'manage-product-catalog/move-my-category';
			var sel_move_cat_id = arguments[0];
			var moved_position = arguments[1];
			var new_parent_cat_id = arguments[2];
			jstree_data = arguments[3];
		
			var pars = '?category_action=move_category';
			pars += '&category_id=' + sel_move_cat_id;
			pars += '&category_position=' + moved_position;
			pars += '&parent_category_id=' + new_parent_cat_id;
		
			$.getJSON(category_ajax_url+pars,  function(data)
			{
				resetAjaxFunctionsCount();
				if (data.err)
				{
					$('#dialog-category-err-msg-content').html(data.err_msg);
					$.jstree.rollback(jstree_data.rlbk);
				}
			});
		};

		$("#demo").bind('select_node.jstree', function(event, response_data) {
			var category_node = response_data.rslt.obj.attr('category_id');
		    category_id = category_node ? category_node.replace("node_","") : root_category_id;
			// open category node
			$('#demo').jstree('open_node', '#node_'+category_id);
			if(category_id != root_category_id && call_default_functions)
			{
				selected_category = category_id;
				getCategoryInfo();
				getAttributesList();
				getCategoryDetails();
			}
			else if(!call_default_functions)
			{
				// reset default function call option
				call_default_functions = true;
			}
		});
		//fix for the category not selected on adding newly
		$("#demo").bind('refresh.jstree', function(event, response_data) {
			if(call_default_functions && selected_category != category_id)
			{
				// no need to call default functions when refresh tree at first
				call_default_functions = false;
				// open category node
				$('#demo').jstree('open_node', '#node_'+category_id);
				// select added category
				$('#demo').jstree('select_node', '#node_'+selected_category);
			}
			else if(category_id == root_category_id)
			{
				// reset default function call option, when no categories selected select_node function not fired.
				call_default_functions = true;
			}
		});

		/* Display default details*/
		var displayDefaultDetails = function()
		{
			getCategoryInfo();
			getAttributesList();
			getCategoryDetails();
		}

		var getCategoryInfo = function()
		{
			setAjaxFunctionsCount();
			var catalog_ajax_url = '{{URL::action('AdminProductCategoryController@postCategoryInfo')}}';
			var pars = 'category_id='+category_id;
			var parent_id = arguments[0];
			if(parent_id)
			{
				pars = 'parent_category_id=' + parent_id;
				getCategoryDetails('add_sub_category');
			}
			jquery_ajax(catalog_ajax_url, pars, 'displayCategoryCatalog');
		};
		var displayCategoryCatalog = function()
		{
			var response = arguments[0];
			$('#category_info_block').html(response);
			resetAjaxFunctionsCount();
		};
		var getAttributesList = function()
		{
			setAjaxFunctionsCount();
			var attribute_ajax_url = '{{URL::action('AdminCategoryAttributesController@postAttributesInfo')}}';
			var pars = 'category_id='+category_id;
			jquery_ajax(attribute_ajax_url, pars, 'displayAttributes');
		};

		var displayAttributes = function()
		{
			var response = arguments[0];
			// in IE8 attribute content not displayed
			//$('#attributes_block').html(response);
			document.getElementById('attributes_block').innerHTML = response;
			// called document onready functions like open fancy box after ajax content display
			attributeOnReadyFunctions();
			resetAjaxFunctionsCount();
		};

		/** attributes related functions **/
		/** Updates the newly ordered/changed row to the DB **/
		//when display using ajax this onready functions not called,
		//so this is added as function and called when ajax complete
		var attributeOnReadyFunctions = function()
		{
			/* open fancy box to view attribute details */
			setAddViewAttributeFancyBox();

		    $(".formBuilderAssignedTable").tableDnD({
		        onDrop: function(table, row) {
					//alert($.tableDnD.serialize());
					$.ajax({
						type: 'GET',
						url: "{{URL::action('AdminCategoryAttributesController@getAttributesOrder')}}?" + $.tableDnD.serialize()+'&category_id='+category_id,
						data: '',
						beforeSend: setAjaxFunctionsCount(),
						success: function(data){
							resetAjaxFunctionsCount();
						}
					});
		        }
		    });
		    $('.formBuilderAddRow').dblclick(function()
		    {
				sel_row = $(this).attr('id');
				sel_attribute_id = sel_row ? sel_row.replace("formBuilderNewRow_","") : 0;
				attr_mgmt_category_id = $('#attr_mgmt_category_id').val();

				if(	attr_mgmt_category_id &&
					sel_attribute_id != 0 &&
					attr_mgmt_category_id != root_category_id)
				{
					formBuilderAddListRow(sel_attribute_id, attr_mgmt_category_id);
				}
		    });

			initializeEditDelete();
		};


		/** Enables the row to respond to user by highlighting and showing edit/delete buttons **/
		function initializeEditDelete()
		{
			 $('.formBuilderRow, .formBuilderAddRow').mouseover(
				function()
				{
					$(this).addClass('formBuilderMouseover');
					$(this).children('.formBuilderAction').children('.formBuilderRowDelete').show();
					$(this).children('.formBuilderAction').children('.formBuilderRowEdit').show();
					//$(this).children('.formBuilderAction').children('.formBuilderRowView').show();
				}
			).mouseout(
				function()
				{
					$(this).removeClass('formBuilderMouseover');
					$(this).children('.formBuilderAction').children('.formBuilderRowDelete').hide();
					$(this).children('.formBuilderAction').children('.formBuilderRowEdit').hide();
					//$(this).children('.formBuilderAction').children('.formBuilderRowView').hide();
				}
			);
		}

		/** Add new attribute to the DB and generates html and adds the same to the attribute list **/
		var formBuilderAddListRow = function()
		{
			var attribute_id = arguments[0];
			var attribute_category = arguments[1];
			var pars = 'attribute_id='+attribute_id+'&category_id='+attribute_category;
			setAjaxFunctionsCount();
			$.post('{{URL::action('AdminCategoryAttributesController@postAdd')}}', pars,  function(data)
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
						$('.noAttributeAssignedRow').hide();
						$('.formBuilderAssignedListBody').append(returnedData.list_row);
						$('.formBuilderAssignedTable').tableDnDUpdate(); // updates table to respond to tableDND events
						initializeEditDelete(); // initialize mouseover and mouseout events to show and hide the edit/delete buttons on table lists
						/* open fancy box to view attribute details */
						setAddViewAttributeFancyBox();
						$('#formBuilderNewRow_' + returnedData.row_id).remove();

						// display no record message
						var my_unassign_count =  $('.formUnassignedAttributes').length;
						if(my_unassign_count == 0)
						{
							$('.noAttributeAddedRow').show();
						}
					}
				}
				resetAjaxFunctionsCount();
				if(!returnedData.err)
				{
					$('#ajaxMsgSuccess').html('Successfully assigned').show().fadeOut(2000);
					$('#ajaxMsgs').hide();
				}

			});
			return false; // for not allowing to submit the form
		}


		var getCategoryDetails = function()
		{
			setAjaxFunctionsCount();
			var display_mode = arguments[0];
			var category_ajax_url = "{{URL::action('AdminManageProductCatalogController@postCategoryDetailsBlock')}}";
			var pars = 'display_block=category_details&category_id='+category_id;
			if(display_mode)
			{
				var pars = 'display_block=' + display_mode + '&category_id='+category_id;
			}
			jquery_ajax(category_ajax_url, pars, 'displayCategory');
		};
		var displayCategory = function()
		{
			var response = arguments[0];
			$('#category_details').html(response);
			resetAjaxFunctionsCount();
		};

		var addSubCategory = function()
		{
			var parent_category_id = arguments[0];
			if(parent_category_id == root_category_id)
			{
				category_id = parent_category_id;
				getCategoryInfo();
				getCategoryDetails();
			}
			else
			{
				getCategoryInfo(parent_category_id);
			}
			return false;
		}

		var deleteCategory = function()
		{
			var delete_category_id = arguments[0];

			$("#dialog-category-delete-confirm").dialog({ title: '{{ trans('admin/manageCategory.delete_category') }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						setAjaxFunctionsCount();
						var category_ajax_url = '{{URL::action('AdminProductCategoryController@getDeleteCategory')}}';
						var pars = '?category_id='+delete_category_id;
						$.getJSON(category_ajax_url+pars,  function(data)
						{
							resetAjaxFunctionsCount();
							if (data.err)
							{
								$('#dialog-category-err-msg-content').html(data.err_msg);
								$("#dialog-category-err-msg").dialog({ title: '{{ trans('admin/manageCategory.delete_category') }}', modal: true,
									buttons: { "{{ trans('common.ok') }}": function() { $(this).dialog("close"); } }
								});
							}
							else
							{
								// get sub categories of selected category,
								// when delete sub category of selected category then we must refresh subcategories.
								if(selected_category != root_category_id)
								{
									category_id = selected_category;
								}

								// remove selected category from tree
								$('#demo').jstree('remove', '#node_'+data.category_id);

								$('#dialog-category-err-msg-content').html('{{ trans('admin/manageCategory.delete-category.delete_category_success_msg') }}');
								$("#dialog-category-err-msg").dialog({ title: '{{ trans('admin/manageCategory.delete_category') }}', modal: true,
									buttons: { "{{ trans('common.ok') }}": function() { $(this).dialog("close"); } }
								});
							}
						});
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
		};

		/* set ajax functions count & open loading dialog */
		function setAjaxFunctionsCount()
		{
			if(total_ajax_functions_to_complete == 0)
			{
				catalogOpenLoadingDialog();
			}
			total_ajax_functions_to_complete++;
		}
		/* reset ajax functions count & close loading dialog */
		function resetAjaxFunctionsCount()
		{
			var close_dialog = arguments[0]?arguments[0]:'yes';
			total_ajax_functions_to_complete--;
			if(total_ajax_functions_to_complete == 0 && close_dialog == 'yes')
			{
				calalogCloseLoadingDialog();
			}
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
		function calalogCloseLoadingDialog()
		{
			$('#catalogLoadingImageDialog').dialog("close");
		}

		/* Display default details when page load at first time */
		displayDefaultDetails();

		//Function that called in add / update category details
//On failure displays category info form displayed
//On success proccess the requested action
var doAjaxSubmit = function() {
	var mes_required = '{{ trans('common.required') }}';
    $("#addCategoryfrm").validate({
        rules: {
            category_name: {
                required: true
            },
            seo_category_name : {
                required: true
            },
            status : {
                required: true
            }
        },
        messages: {
            category_name: {
                required: mes_required
            },
            seo_category_name : {
                required: mes_required
            },
            status : {
                required: mes_required
            }
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

	if($("#addCategoryfrm").valid())
	{
		setAjaxFunctionsCount();
		var frmname = arguments[0];
		var divname = arguments[1];
		var current_content = $("#"+divname).html();
		// prepare Options Object
		var options = {
		    target:     '#'+divname,
		    url:        $("#"+frmname).attr('action'),
		    success: function(originalRequest){
		   				agrs = originalRequest.split('|##|');
		   				//alert(agrs);return false;
		   				//check added / updated successfully
		   				if (agrs[1] == 'true' || agrs[1] == true) {
		   					// Show category form
				    		$("#"+divname).html(current_content);
				    		resetAjaxFunctionsCount('no');
				    		current_category_id = agrs[2];
							// category added
				    		if(category_id != current_category_id)
				    		{
				    			// Refresh category list
				    			$('#demo').jstree('refresh', '#node_'+category_id);
				    			//moved select added category to callback of refresh
				    			selected_category = current_category_id;
				    			if(selected_category == root_category_id)
				    			{
									getCategoryInfo();
				    			}
				    		}
				    		// category updated
				    		else
				    		{
								// Refresh category list
				    			$('#demo').jstree('refresh', 'node_'+category_id);
				    		}
				    	} else {
				    		//Show category form again with error message
				    		$("#"+divname).html(originalRequest);
				    		resetAjaxFunctionsCount();
				    	}
				   	}
			};

			// pass options to ajaxSubmit
			$('#'+frmname).ajaxSubmit(options);
		}
		return false;
	};

	$("body").delegate('#category_name', 'focusout', function(){
		if ($('#seo_category_name').val() == '') {
			$('#category_name').val($.trim($('#category_name').val()));
			var tmp_str = $('#category_name').val().replace(/\s/g,'-'); // to replace spaces with hypens
			tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
			tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
			$('#seo_category_name').val(tmp_str);
		}

	});

	var displayParentCategoryAttributes = function()
	{
		var sel_link = $('#linkShowParentAttributes');
		if(sel_link.hasClass('clsHide'))
		{
			sel_link.removeClass('clsHide').addClass('clsShow');
		}
		else
		{
			sel_link.removeClass('clsShow').addClass('clsHide');
		}
		$('#parentAttributesBlock').toggle();
	};

	function removeCategoryImage(resource_id, imagename, imageext, imagefolder) {
		$("#dialog-delete-confirm").dialog({
			title: "clonescripts",
			modal: true,
			buttons: [{
					text: common_yes_label,
					click: function()
					{
						displayLoadingImage();
						$.getJSON("{{ Url::action('AdminProductCategoryController@getDeleteCategoryImage') }}",
						{resource_id: resource_id, imagename: imagename, imageext: imageext, imagefolder: imagefolder},
							function(data)
							{
								hideLoadingImage();
								if(data.result == 'success')
								{
									$('#itemResourceRow_'+resource_id).remove();
								}
								else
								{
									showErrorDialog({status: 'error', error_message: '{{ trans('common.invalid_action') }}'});
								}
						});
						$(this).dialog("close");
					}
				},
				{
					text: common_no_label,
					click: function()
					{
						 $(this).dialog("close");
					}
				}
			]});
		}

	/** Removes the selected attribute **/
	var formBuilderRemoveListRow = function()
	{
		var attribute_id = arguments[0];
		var attribute_category = arguments[1];

		$("#dialog-attribute-remove-confirm").dialog({ title: "{{ trans('admin/manageCategory.attributes_title') }}", modal: true,
			buttons: {
				"{{ trans('common.yes') }}": function() {
					$(this).dialog("close");

					$.getJSON("{{URL::action('AdminCategoryAttributesController@getDeleteAttributes')}}?attribute_id=" + attribute_id + '&category_id=' + attribute_category,
					{
						beforeSend:function()
						{
							setAjaxFunctionsCount();
						}
					},
					function(data)
					{
						if (data.err)
						{
							$('#ajaxMsgs').html(data.err_msg);
							$('#ajaxMsgs').show();
						}
						else
						{
							if (data.list_row)
							{
								$('.noAttributeAddedRow').hide();
								$('.formBuilderAddedListBody').append(data.list_row);
								initializeEditDelete(); // initialize mouseover and mouseout events to show and hide the edit/delete buttons on table lists
								intializeDoubleClickAssign(data.row_id);
								/* open fancy box to view attribute details */
								setAddViewAttributeFancyBox();
								$('#formBuilderRow_' + data.row_id).remove();

								// display no record message
								var my_assign_count =  $('.formAssignedAttributes').length;
								if(my_assign_count == 0)
								{
									$('.noAttributeAssignedRow').show();
								}
							}
						}
						resetAjaxFunctionsCount();
						if(!data.err)
						{
							$('#ajaxMsgSuccess').html('{{ trans('admin/manageCategory.attributes_assigned_removed_msg') }}').show().fadeOut(2000);
							$('#ajaxMsgs').hide();
						}
					});
				}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
			}
		});
		return false;
	}


	function intializeDoubleClickAssign(sel_attribute_id)
	{
		$('#formBuilderNewRow_'+sel_attribute_id).dblclick(function()
		{
			attr_mgmt_category_id = $('#attr_mgmt_category_id').val();

			if(	attr_mgmt_category_id &&
				sel_attribute_id != 0 &&
				attr_mgmt_category_id != root_category_id)
			{
				formBuilderAddListRow(sel_attribute_id, attr_mgmt_category_id);
			}
		});
	}

	function setAddViewAttributeFancyBox()
	{
		$(".formBuilderRowView_old").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	}

	function openViewAttributeFancyBox(fancybox_url)
	{
		$.fancybox({
			maxWidth    : 800,
			maxHeight   : 432,
			fitToView   : false,
			width       : '70%',
			height      : '432',
			autoSize    : false,
			closeClick  : false,
			type        : 'iframe',
			href        : fancybox_url,
			openEffect  : 'none',
			closeEffect : 'none',
		});
	}
	</script>
@stop