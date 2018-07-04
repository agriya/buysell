//	set mouse over class
function initializeRowMouseOver()
{
	 $('.formBuilderRow').mouseover(
		function()
		{
			$(this).addClass('formBuilderMouseover');
		}
	).mouseout(
		function()
		{
			$(this).removeClass('formBuilderMouseover');
		}
	);
};


function showProcessing(flag, dialog)
{
	dialog = true;
	if (flag) {
		if (dialog) {
			$('#dialog-upload-in-process').dialog({
				open: function() {
					$(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
				},
				height: 150,
				width: 250,
				modal: true,
				title: cfg_site_name
			});
		} else {
			$('.loading_image').show();
		}
	} else {
		if (dialog) {
			$('#dialog-upload-in-process').dialog("close");
		} else {
		}	$('.loading_image').hide();
	}
};

function showErrorDialog(data)
{
	//html_text = '' + data.status.toUpperCase() + ': ';
	html_text = '' + data.error_message +  ' <br />' ;
	if (data.filename != undefined) {
		html_text +=  data.filename + '';
	}
	html_text += '';

	$('#dialog-upload-errors-span').html(html_text);
	$("#dialog-upload-errors").dialog({
			 open: function() {	$(this).parents(".ui-dialog-titlebar:first").find(".ui-dialog-titlebar-close").remove();	},
				height: 150,
				width: 300,
			 	title: cfg_site_name,
				modal: true
				//buttons: [ {  text: common_close_label, click: function() 	{	$(this).dialog("close");		}}]
			 });
	hideLoadingImage();
};

function showError(data) {
	html = '<label class="error" generated="true" for="' + data.element_id  + '">' + data.error_message + '</label>';

	if ($('#' + data.element_id).next('.error').attr('for') !=  data.element_id) {
		$(html).insertAfter('#' + data.element_id);
	}
};

function removeElement(value, replaceValue)
{
	var result = ","+value+",";
	result = result.replace(","+replaceValue+",",",");
	result = result.substr(1,result.length);
	result = result.substr(0,result.length-1);
	return result;
};

var removeValue = function(list, value, separator) {
  separator = separator || ",";
  var values = list.split(",");
  for(var i = 0 ; i < values.length ; i++) {
	if(values[i] == value) {
	  values.splice(i, 1);
	  return values.join(",");
	}
  }
  return list;
};


function removeError(data) {
	$('#' + data.element_id).next('.error').remove();
};

function setAutoFocus()
{
	$(document).ready(function() {
		$(':input:visible:enabled:first').focus();
	});
};


function toggleUploadButton(idVal, max_allowed_limit){
	var attachedCount = $('#uploadedFilesList_'+idVal).children("li").length;
	$("#file_count_"+idVal).html(max_allowed_limit-attachedCount); //Update available upload document count
	if(attachedCount >= 1)	{
		$('#attach_doc_lbl_'+idVal).css('display', 'block');
	}
	if (attachedCount >= max_allowed_limit ){
		$('#upload_'+idVal).css('display', 'none');
	}
	else{
		$('#upload_'+idVal).css('display', 'block');
	}
};


function removeDocRowEx(resource_id, resource_type, idVar, max_allowed_limit, imagename, imageext, imagefolder) {
	//alert("called this"); return false;
	$("#dialog-delete-confirm").dialog({
		title: "Newsletter",
		modal: true,
		buttons: [
			{
				text: common_yes_label,
				click: function()
				{
					//displayLoadingImage();
					//$.getJSON("/cf/imageDelete" ,
					$.getJSON("{{ URL::to('tours/add/image_delete') }}",
//										  {{ Url::to('tours/add/upload')}}
						{resource_id: resource_id, resource_type: resource_type, imagename: imagename, imageext: imageext, imagefolder: imagefolder},

						function(data)
						{
							hideLoadingImage();
							if(data.result == 'success')
							{
								var doc_ids = $("#doc_ids_" + idVar).val();
								var res = removeElement(doc_ids, resource_id) ;
								$("#doc_ids_"+ idVar).val(res);
								if(res != "")
									$('#attach_doc_lbl_'+ idVar).show();
								else
									$('#attach_doc_lbl_'+ idVar).hide();

								$('#itemResourceRow_'+resource_id).remove();

								//Used for add tour itinerary page
								if($('.fn_ItineraryCounter').length > 0) {
									if(typeof window.removeItineraryPhotos == 'function') {
										// function exists, so we can now call it
										removeItineraryPhotos(resource_id);
									}
								}
								toggleUploadButton(idVar,  max_allowed_limit);
								$('.clsItemUploadResourceFileButton').css('display', 'block'); // this should only show when the items is less than allowed
							}
							else
							{
								showErrorDialog({status: 'error', error_message: opr_not_completed_err_msg});
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
		]

	});

};

