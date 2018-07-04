var populateAnalyticsInfo = function() {
	$.geocode('#geobyte_info', '#maxmind_info', '#browser_info');
	populateHiddenFields(this);
};

var displayLoadingImage = function(){
	$("#selLoading").show();
};

var hideLoadingImage = function(){
	$("#selLoading").hide();
};

var Redirect2URL = function(){
	if(arguments[0]){
		location.replace(arguments[0]);
	}
	else{
		window.back();
	}
	return false;
};

var postAjaxForm = function(){
	/* form name to post */
	var frmname = arguments[0];
	/* div id to populate the response */
	var divname = arguments[1];
	/* action url change */
	var action = $("#"+frmname).attr('action');
	if(arguments.length>2){
		action = arguments[2];
	}
	/* To remove particularElement */
	var remove_element = '';
	if(arguments.length>3){
		remove_element = arguments[3];
	}
	var data = $("#"+frmname).serialize();
	if(arguments.length>4){
		data = arguments[4];
	}

	$.ajax({
		type: "POST",
		url: action,
		data: data,
		beforeSend:displayLoadingImage(),
		success: function(html){
					if(remove_element){
						$(remove_element).remove();
					}
					hideLoadingImage();
				 	$("#"+divname).html(html);
				}
	 });
	 return false;
};

function jquery_ajax(url, pars, function_name){
	if(arguments.length<=0){
		var url = callBackArguments[0];
		var pars = callBackArguments[1];
		var function_name = callBackArguments[2];
	}
	$.ajax({
		type: "POST",
		url: url,
		data: pars,
		/* beforeSend:displayLoadingImage(), */
		success: eval(function_name)
	 });
	return false;
};

function getPageName(url) {
    var currurl = url;
    var index = currurl.lastIndexOf("/") - 13;
    var filenameWithExtension = currurl.substr(index);
    var filename = filenameWithExtension.split(".")[0]; // <-- added this line
    return filename;                                    // <-- added this line
};

function fancyPopupUrlRedirect(link){
	window.location.href = link;
};

function injectTrim(handler) {
  return function (element, event) {
    if (element.tagName === "TEXTAREA" || (element.tagName === "INPUT" && element.type !== "password")) {
      element.value = $.trim(element.value);
    }
    return handler.call(this, element, event);
  };
};

Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   var return_val = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
   return return_val.replace(".00","");
};

if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
  }
};

function chkIsValidPrice(value){
	var regex = new RegExp("^[0-9]+(\\.[0-9]{1,2})?$");
	var val = parseFloat(value);
	if(value == "")
		return true;
	if(val > 0 && value.match(regex)){
		return true;
	}
	return false;
};

function chkIsValidPriceWithZero(value){
	var regex = new RegExp("^[0-9]+(\\.[0-9]{2})?$");
	var val = parseFloat(value);
	if(value == "")
		return true;
	if(value.match(regex)){
		return true;
	}
	return false;
};

function closeFancyPopUp()
{
	parent.$.fancybox.close();
};

function urlencode( str ) {
    /* // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // %          note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'*/

    var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
    var ret = str.toString();

    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };

    /* The histogram is identical to the one in urldecode.*/
    histogram['!']   = '%21';
    histogram['%20'] = '+';

    /* Begin with encodeURIComponent, which most resembles PHP's encoding functions*/
    ret = encodeURIComponent(ret);

    for (search in histogram) {
        replace = histogram[search];
        ret = replacer(search, replace, ret); /* Custom replace. No regexing */
    }

    /* Uppercase for full PHP compatibility */
    return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
        return "%"+m2.toUpperCase();
    });

    return ret;
};

$(document).ready(function() {

	//Coupon code make all letter caps
	$(".coupon_code").keyup(function (event){
		if(event.which != '16' && event.which != '17' && event.which != '67' && event.which != '35' && event.which != '36' && event.which != '37' && event.which != '38' && event.which != '39' && event.which != '35')
		{
			if (this.value != '') {
				this.value = this.value.toUpperCase();
			}
		}
    });
    $(".coupon_code").blur(function (){
    	if (this.value != '') {
			this.value = this.value.toUpperCase();
        }
    });
    $(".coupon_code").bind("paste", function (e) {
        if (this.value != '') {
			this.value = this.value.toUpperCase();
        }
    });
    $(".coupon_code").bind("drop", function (e) {
        return false;
    });

	//Allow only two decimals in price textbox
	var specialKeysPrice = new Array();
    specialKeysPrice.push(8); //Backspace
    specialKeysPrice.push(9); //tab
    specialKeysPrice.push(13); //Enter
    specialKeysPrice.push(46); //decimal
	$(".price").live("keypress", function (e) {
        var keyCode = e.which ? e.which : e.keyCode
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
        var errorDiv = '#error' + this.name.replace('price', '');
        $(errorDiv).css("display", ret ? "none" : "inline");
        $(errorDiv).text('Enter valid price');
        if (keyCode == 13 && this.value != '') {
            $('#discount_percentage' + this.name.replace('price', '')).focus();
        }
        return ret;
    });
    $(".price, .giftwrapprice, .fn_varstock").live("paste", function (e) {
        return false;
    });
    $(".price, .giftwrapprice, .fn_varstock").live("drop", function (e) {
        return false;
    });
    $(".price, .giftwrapprice").live("blur", function (e) {
    	if (this.value != '') {
			this.value = parseFloat(this.value).toFixed(2);
        }
    });

	$(".fn_varstock").live("keypress", function (e) {
        var keyCode = e.which ? e.which : e.keyCode
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
        return ret;
    });

	$(".giftwrapprice").live("keypress", function (e) {
        var keyCode = e.which ? e.which : e.keyCode
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
        var errorDiv = '#err_giftwrap_pricing';
        $(errorDiv).css("display", ret ? "none" : "inline");
        $(errorDiv).text('Enter valid price');
        return ret;
    });

});

function setInputLimiterById(ident, char_limit)
{
	if ($('#'+ident).length > 0)
	{
		$('#'+ident).inputlimiter({
			limit: char_limit,
			remText: remText,
			limitText: limitText
		});
	}
}




// Add product page script starts
if (typeof page_name === 'undefined') {
	// page name not defined
	var page_name = '';
}
else {
	if(page_name == 'add_product')
	{

		function selSwapImageOpen(url){
		    jQuery.fancybox.open([
		        {
		            maxWidth    : 800,
			        maxHeight   : 630,
			        fitToView   : false,
			        width       : '70%',
			        height      : '430',
			        autoSize    : false,
			        closeClick  : false,
			        type        : 'iframe',
			        openEffect  : 'none',
			        closeEffect : 'none',
		            href: url,
		        }
		    ]);
		}

		if (typeof pagetab === 'undefined') {
			//  tab not defined
			//alert("Tab undefined");
		}
		else
		{
			function showErrorDialog(err_data) {
				var err_msg ='<div class="note note-danger">'+err_data.error_message+'</div>';
				$('#error_msg_div').html(err_msg);
				var body = $("html, body");
				body.animate({scrollTop:0}, '500', 'swing', function() {
				});
			}

			function removeErrorDialog() {
				$('#error_msg_div').html('');
			}

			if(pagetab ==  'basic')
			{
				$("#addProductfrm").validate({
					rules: {
						product_name: {
							required: true,
							minlength: title_min_length,
							maxlength: title_max_length
						},
						url_slug: {
							required: true,
						},
							product_category_id: {
							required: true,
						},
						product_tags: {
							required: true,
						},
						product_highlight_text: {
							maxlength: summary_max_length
						},
						demo_url: {
							url:true
						}
					},
					messages: {
						product_name: {
							required: mes_required,
							minlength: jQuery.format(title_min_length_message),
							maxlength: jQuery.format(title_max_length_message)
						},
						url_slug: {
							required: mes_required
						},
						product_category_id: {
							required: mes_required
						},
						product_tags: {
							required: mes_required
						},
						product_highlight_text: {
							maxlength: jQuery.format(summary_max_length_message)
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

				tinymce.init({
					menubar: "tools",
					selector: "textarea.fn_editor",
					mode : "exact",
					elements: "product_description",
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


				$('.fn_addSection').click(function() {
					$('#sel_addSection').fadeIn();
					return false;
				});

				$('.fn_saveSectionCancel').click(function() {
					$('#section_name').val('');
					$('.fn_sectionErr').text('');
					$('#sel_addSection').fadeOut();
					return false;
				});


				$('.fn_saveSection').click(function(){
					var section_val = $("#section_name").val();
					if (section_val.trim() == '') {
						$('.fn_sectionErr').html(mes_required);
						$('#section_name').focus();
						return false;
					}
					var user_code = '';
					if($('#user_code').length > 0)
					{
						user_code = $('#user_code').val();
					}
					displayLoadingImage(true);

					$.post(product_add_section_url, { section_name: section_val, action: action, user_code: user_code},  function(response)
					{
						data = eval( '(' +  response + ')');

						if (data.status == 'success') {
							$('#section_name').val('');
							$('.fn_saveSectionCancel').trigger('click');
							//$('#user_section_id').append( new Option(data.section_name, data.section_id, true, true) );

							var o = new Option(data.section_name, data.section_id, true, true);
							/// jquerify the DOM object 'o' so we can use the html method
							$(o).html(data.section_name);
							$("#user_section_id").append(o);

							hideLoadingImage(false);
						} else {
							hideLoadingImage(false);
							$('.fn_sectionErr').html(data.error_message);
						}
					});
				});

				var listSubCategories = function ()
				{
					select_btn_id = arguments[0];	/* selected drop down box id */
					var sel_cat_id = $('#'+select_btn_id).val();	/* selected category id */
					remove_cat_id = parseInt(arguments[1]); /* catgory id to remove existing list */
					sel_cat_id_class = $('#'+select_btn_id).attr('class');	/* get existing class */
					$('#loading_sub_category').show();	/* display loading text */

					/* get sub category list */
					if (sel_cat_id != '')
					{
						$.get(category_list_url + '?action=get_product_sub_categories&category_id=' + sel_cat_id,{},function(data)
						{
							data_arr = data.split('~~~');	/* contains new drop down element & new category id with top level categories */
							data = data_arr[0];	/* assigned new drop down element */

							existing_sel_ids = $('#my_selected_categories').val();
							existing_sel_ids_arr = existing_sel_ids.split(',');

							existing_sel_ids_length = existing_sel_ids_arr.length;
							for (var i=0;i<existing_sel_ids_length;i++)
							{

								if( parseInt(existing_sel_ids_arr[i]) > remove_cat_id){
									$('.fn_subCat_'+existing_sel_ids_arr[i]).remove()
								}
							}
							$('.fn_clsNoSubCategryFound').hide();
							/* add new sub categories list */
							$('#sub_categories').append(data);

							/* assign new hidden values */
							$('#my_selected_categories').val(data_arr[1]); /* assign new categories list */
							$('#my_category_id').val(sel_cat_id);	/* update category id hidden value */

							//$('#sub_category_'+sel_cat_id).text("");	/* change css class */
							$('#sub_category_'+sel_cat_id).addClass(sel_cat_id_class+' subCat_'+remove_cat_id)	/* change css class */
							$('#loading_sub_category').hide();	/* hide loading text */

						});
					}
					else
					{
						$.get(category_list_url + '?action=get_product_sub_categories&category_id=' + remove_cat_id,{},function(data)
						{
							data_arr = data.split('~~~');	/* contains new drop down element & new category id with top level categories */
							new_categories = data_arr[1];	/* assigned new categories */

							existing_sel_ids = $('#my_selected_categories').val();
							existing_sel_ids_arr = existing_sel_ids.split(',');
							existing_sel_ids_length = existing_sel_ids_arr.length;
							for (var i=0;i<existing_sel_ids_length;i++)
							{
								if( parseInt(existing_sel_ids_arr[i]) > remove_cat_id){
									$('.fn_subCat_'+existing_sel_ids_arr[i]).remove()
								}
							}
							/* assign new hidden values */
							$('#my_selected_categories').val(new_categories); /* assign new categories list */
							/* update category id hidden value */
							if(root_category_id != remove_cat_id)
							{
								$('#my_category_id').val(remove_cat_id);
							}
							else
							{
								$('#my_category_id').val('');
							}

						});
						$('#loading_sub_category').hide();
					}
				};

				setInputLimiterById('product_highlight_text', summary_max_length);

				function generateSlugUrl() {
					var title = $("#product_name").val();
						if(title.trim() == "")
						$("#url_slug").val('');
						else if($("#url_slug").val().trim() == ''){
						var slug_url = title.replace(/[^a-z0-9]/gi, '-');
						slug_url = slug_url.replace(/(-)+/gi, '-');
						slug_url = slug_url.replace(/^(-)+|(-)+$/g, '');
						$("#url_slug").val(slug_url.toLowerCase());
					}
				}
			}
			if(pagetab == 'price') // Price starts
			{
				if(allow_giftwrap == 1)
				{
					if ($('#accept_giftwrap').attr('checked'))
						$('.fn_giftwrap_field').show();
					else
						$('.fn_giftwrap_field').hide();


					$('#accept_giftwrap').click(function() {
						if (this.checked) {
							$('.fn_giftwrap_field').fadeIn();
						} else {
							$('.fn_giftwrap_field').fadeOut();
						}
					});
				}

				$(document).ready(function()
				{
					var specialKeys = new Array();
					specialKeys.push(8); //Backspace
					specialKeys.push(9); //tab
					specialKeys.push(13); //Enter
					$(function () {
						//For Price
						var specialKeysPrice = new Array();
						specialKeysPrice.push(8); //Backspace
						specialKeysPrice.push(9); //tab
						specialKeysPrice.push(13); //Enter
						specialKeysPrice.push(46); //decimal
						$(".price").live("keypress", function (e) {
							var keyCode = e.which ? e.which : e.keyCode
							var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
							var errorDiv = '#error' + this.name.replace('price', '');
							$(errorDiv).css("display", ret ? "none" : "inline");
							$(errorDiv).text(enter_valid_price);
							if (keyCode == 13 && this.value != '') {
								$('#discount_percentage' + this.name.replace('price', '')).focus();
							}
							return ret;
						});
						$(".price").live("paste", function (e) {
							return false;
						});
						$(".price").live("drop", function (e) {
							return false;
						});
						$(".price").live("blur", function (e) {
							var errorDiv = '#error' + this.name.replace('price', '');
							if (this.value != '') {
								this.value = parseFloat(this.value).toFixed(2);
								var discount_percentage = 0;
								if ($('#discount_percentage' + this.name.replace('price', '')).val() != '')
									discount_percentage = parseFloat($('#discount_percentage' + this.name.replace('price', '')).val()).toFixed(2);
								if (this.value) {
									discounted_price = (this.value - ((this.value * discount_percentage) / 100)).toFixed(2);
									$('#discount' + this.name.replace('price', '')).val(discounted_price);
								}
								if (this.value < 1) {
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(price_not_less_than_one);
									this.focus();
								} else if (parseFloat($('#discount' + this.name.replace('price', '')).val()) < 1) {
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(discount_not_less_than_one);
									this.focus();
								}
							} else {
								$('#discount_percentage' + this.name.replace('price', '')).val('');
								$('#discount' + this.name.replace('price', '')).val('');
							}
						});
						//For Discount Percentage
						$(".discount-percentage").live("keypress", function (e) {
							var keyCode = e.which ? e.which : e.keyCode
							var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
							var errorDiv = '#error' + this.name.replace('discount_percentage', '');
							$(errorDiv).css("display", ret ? "none" : "inline");
							$(errorDiv).text(valid_discount);
							if (keyCode == 13) {
								$('#discount' + this.name.replace('discount_percentage', '')).focus();
							}
							return ret;
						});
						$(".discount-percentage").live("paste", function (e) {
							return false;
						});
						$(".discount-percentage").live("drop", function (e) {
							return false;
						});
						$(".discount-percentage").live("keyup", function (e) {
							if (this.value > 100) {
								var errorDiv = '#error' + this.name.replace('discount_percentage', '');
								$(errorDiv).css("display", "inline");
								$(errorDiv).text(valid_discount_percent);
								$('#discount' + this.name.replace('discount_percentage', '')).val($('#price' + this.name.replace('discount_percentage', '')).val());
							}
						});
						$(".discount-percentage").live("blur", function (e) {
							var errorDiv = '#error' + this.name.replace('discount_percentage', '');
							var discount_percentage = 0;
							if (this.value)
								discount_percentage = this.value = parseFloat(this.value).toFixed(2);
							var price = 0;
							var discounted_price = '';
							if ($('#price' + this.name.replace('discount_percentage', '')).val() != '')
								price = parseFloat($('#price' + this.name.replace('discount_percentage', '')).val()).toFixed(2);
							if (price && discount_percentage <= 100) {
								discounted_price = (price - ((price * discount_percentage) / 100)).toFixed(2);
								$('#discount' + this.name.replace('discount_percentage', '')).val(discounted_price);
							}
							if (discounted_price != '' && discounted_price < 1) {
								$(errorDiv).css("display", "inline");
								$(errorDiv).text();
								this.focus();
							}
							if (this.value == '') {
								$(errorDiv).css("display", "none");
							}
						});
						//For discounted price
						$(".discount").live("keypress", function (e) {
							var keyCode = e.which ? e.which : e.keyCode
							var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
							var errorDiv = '#error' + this.name.replace('discount', '');
							$(errorDiv).css("display", ret ? "none" : "inline");
							$(errorDiv).text(valid_discounted_price);
							return ret;
						});
						$(".discount").live("paste", function (e) {
							return false;
						});
						$(".discount").live("drop", function (e) {
							return false;
						});
						$(".discount").live("blur", function (e) {
							if (this.value != '')
								this.value = parseFloat(this.value).toFixed(2);
						});

						$("#edit_product").live("click", function(e){
							var submit = true;
							var purchase_price = $('#purchase_price').val();
							var purchase_err_div = $('#errorpurchase_');
							/*if (purchase_price == '' || isNaN(purchase_price) || parseFloat(purchase_price) < 0) {

								$(purchase_err_div).css("display", "inline");
								$(purchase_err_div).text('{{trans('product.valid_purchase_price')}}');
								submit = false;
							}*/
							//Get the number of quantity ranges clsGroupPrices
							var rangeLength = $('.clsGroupPrices').length;
							var selected_val = $('input[name=is_free_product]:radio:checked').val();
							if (submit && selected_val == 'No') { //validate group prices if not free product
								//Loop group price block and validate each ranges fields
								$('.clsGroupPrices').each(function(index) {
									//Get the fields elements
									var group_id = this.id.split('_')[1];
									var range_index = this.id.split('_')[2];
									var price = $('#price_' + group_id + '_' + range_index).val();
									var discount_percentage = $('#discount_percentage_' + group_id + '_' + range_index).val();
									var discount = $('#discount_' + group_id + '_' + range_index).val();
									var errorDiv = $('#error_' + group_id + '_' + range_index);

									//Get the range fields length
									var currentGroupRangeLength = $('.clsGroupFields_' + group_id).length;

									if ((price == '' && (parseInt(group_id) == 0 || currentGroupRangeLength > 1)) || parseFloat(price) <=  0) {
										$(errorDiv).css("display", "inline");
										$(errorDiv).text(enter_valid_price);
										submit = false;
									} else if ((discount_percentage != '' && parseFloat(discount_percentage) >  100)) {
										$(errorDiv).css("display", "inline");
										$(errorDiv).text(valid_discount);
										submit = false;
									} else if ((discount == '' && (parseInt(group_id) == 0 || currentGroupRangeLength > 1)) || parseFloat(discount) <=  0 || parseFloat(price) < parseFloat(discount)) {
										$(errorDiv).css("display", "inline");
										$(errorDiv).text(discounted_price_invalid);
										submit = false;
									}

								});
							}
							return submit;
						});

						$('#selGroupPriceBlock').show();
					});

					disableTransactionFee(site_transaction_fee_type);
					if(can_upload_free_product)
					{
						var selected_val = $('input[name=is_free_product]:radio:checked').val();
						if (selected_val=='Yes')
						{
							showPriceFields(false);
						} else {
							showPriceFields(true);
						}

						$('input[name=is_free_product]').click(function(){
							var selected_val = $('input[name=is_free_product]:radio:checked').val();
							if (selected_val=='Yes')
							{
								showPriceFields(false);
							} else {
								showPriceFields(true);
							}

						});
					}

					if ($('#global_transaction_fee_used').attr('checked')){
						showTransactionFeeFields(false);
					}
					else{
						showTransactionFeeFields(true);
					}

					$('#global_transaction_fee_used').click(function(){
						if (this.checked){
							showTransactionFeeFields(false);
						}
						else{
							showTransactionFeeFields(true);
						}
					});
				});

				 function showPriceFields(flag)
				 {
					if (flag) {
						$('.fn_clsPriceFields').show();

					if($('#global_transaction_fee_used').is(":checked")){
						showTransactionFeeFields(false);
					}
					else{
						showTransactionFeeFields(true);
					}
					}
					else{
						$('.fn_clsPriceFields').hide();
						$('.fn_clsFeeFields').hide();
					}
				}


				function showTransactionFeeFields(flag)
				{
					if (flag){
						$('.fn_clsFeeFields').show();
					}
					else{
						//Set transaction fee flat and percentage fields as zero
						$('#site_transaction_fee').val(0);
						$('#site_transaction_fee_percent').val(0);
						$('.fn_clsFeeFields').hide();
					}
				}

				var disableTransactionFee = function()
				{
					var sel_type = arguments[0];
					if(sel_type == 'Mix') {
						$('#site_transaction_fee').removeAttr('disabled');
						$('#site_transaction_fee_percent').removeAttr('disabled');
					}
					else if(sel_type == 'Percentage') {
						$('#site_transaction_fee').val(0);
						$('#site_transaction_fee').attr('disabled', true);
						$('#site_transaction_fee_percent').removeAttr('disabled');
					}
					else if(sel_type == 'Flat') {
						$('#site_transaction_fee_percent').val(0);
						$('#site_transaction_fee_percent').attr('disabled', true);
						$('#site_transaction_fee').removeAttr('disabled');
					}
					return true;
				}

				$(function() {
					$('#product_discount_fromdate').datepicker({
						format: 'dd/mm/yyyy',
						autoclose: true,
						todayHighlight: true
					});
					$('#product_discount_todate').datepicker({
						format: 'dd/mm/yyyy',
						autoclose: true,
						todayHighlight: true
					});
				});

				if(allow_giftwrap > 0)
				{
					if ($('#accept_giftwrap').attr('checked'))
						$('.fn_giftwrap_field').show();
					else
						$('.fn_giftwrap_field').hide();

					$('#accept_giftwrap').click(function() {
						if (this.checked) {
							$('.fn_giftwrap_field').fadeIn();
						} else {
							$('.fn_giftwrap_field').fadeOut();
						}
					});
				}

			} // Price ends


			if(pagetab == 'stocks') // Stock tabs starts
			{
				$('#stock_country_id_china').click(function() {
					if($('#stock_country_id_china').is(":checked")){
						$('.fn_china_div').show();
					}
					else {
						if ($('#quantity').val() > 0 || $('#serial_numbers').val() != "") {
							confirmDialog('china');
						} else {
							$('.fn_china_div').hide();
						}
					}
				});
				$('#stock_country_id_pak').click(function() {
					if($('#stock_country_id_pak').is(":checked")){
						$('.fn_pak_div').show();
					}
					else {
						if ($('#quantity_pak').val() > 0 || $('#serial_numbers_pak').val() != "") {
							confirmDialog('pak');
						} else {
							$('.fn_pak_div').hide();
						}
					}
				});

				function confirmDialog(country) {
					var dialog_msg = 'Are you sure that you want to remove Quantities & Serial numbers for China?';
					if(country == 'pak')
						dialog_msg = 'Are you sure that you want to remove Quantities & Serial numbers for Pakistan?';
					$('#dialog_msg').html(dialog_msg);
					$("#dialog-delete-confirm").dialog({ title: cfg_site_name,	modal: true,
						buttons: [{
							text: common_yes_label,	click: function()
							{
								if(country == 'china')
									$('.fn_china_div').hide();
								else
									$('.fn_pak_div').hide();

								$(this).dialog("close");
							}
						},{
							text: common_no_label, click: function()
							{
								 if(country == 'china') {
									$('#stock_country_id_china').parent('span').addClass('checked');
									$('#stock_country_id_china').attr('checked', 'checked');
								}
								else {
									$('#stock_country_id_pak').parent('span').addClass('checked');
									$('#stock_country_id_pak').attr('checked', 'checked');
								}
							}
						}]
					});
				}

				$("#quantity").keypress(function (e) {
					if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
						$("#card_error").html("Digits Only").show().fadeOut("slow");
						return false;
					}
				});

				 $("#addProductStocksfrm").validate({
					onfocusout: injectTrim($.validator.defaults.onfocusout),
					rules: {
						quantity: {
							required: true,
							min:1,
							digits: true
						},
						serial_numbers: {
							checkEmptyLines: {
								depends: function(element) {
								return ($(element).val()!='') ? true : false;
								}
							},
							validateSerialNumber: {
								depends: function(element) {
								return ($(element).val()!='') ? true : false;
								}
							}
						},
					},
					messages: {
						quantity: {
							required: mes_required,
							min: jQuery.validator.format(enter_value_greater_than_msg+" {0}")
						},
						serial_numbers: {
							required: mes_required,
						},
					},
					submitHandler: function(form) {
						displayLoadingImage(true);
						form.submit();
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


				jQuery.validator.addMethod("checkEmptyLines", function (value, element) {
					var serial_numbers_array = value.split("\n");
					var empty_lines = false;
					for(i = 0; i < serial_numbers_array.length; i++) {
						if (serial_numbers_array[i].trim() == "") {
							empty_lines = true;
						}
					}
					if (empty_lines) {
						return false;
					}
					return true;
				}, serial_number_empty_line_error_msg);

				jQuery.validator.addMethod("validateSerialNumber", function (value, element) {
					var quantity = $('#quantity').val();
					var serial_numbers_array = value.split("\n");
					if (serial_numbers_array.length == quantity) {
						return true;
					}
					return false;
				}, serial_number_equals_qty_error_msg);

			} // Stock tabs ends

			if(pagetab == 'shipping') // Shipping tabs starts
			{
				jQuery.validator.addMethod("decimallimit", function (value, element) {
					return this.optional(element) || /^[0-9]*(\.\d{0,2})?$/i.test(value);
				}, "Only two decimals allowed");

				jQuery.validator.addMethod("decimallimit3", function (value, element) {
					return this.optional(element) || /^[0-9]*(\.\d{0,3})?$/i.test(value);
				}, "Only three decimals allowed");

				$("#addProductShippingfrm").validate({
					rules: {
						country_id: {
							required: true,
						},
						shipping_fee: {
							required: true,
							number: true,
							decimallimit: true
						},
					},
					messages: {
						country_id: {
							required: mes_required,
						},
						shipping_fee: {
							required: mes_required
						},
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

				function editItemShippingRow(shipping_id, shipping_fee) {
					$('#shippingfeecurr_'+shipping_id).removeClass('hide');
					html = '<div class="clearfix">';
					html +=  '<div class="col-md-3 col-sm-3 col-xs-3 margin-bottom-10"><input type="text" name="shipping_fee" id="shipping_fee_'+shipping_id+'" value="' + shipping_fee + '" class="form-control" /></div>';
					html += '<div class="col-md-6 col-sm-6 col-xs-6 margin-top-5"><a class="btn btn-xs green" href="javascript: void(0);" onclick="saveShippingFeeAmount(' + shipping_id + ');"><i class="fa fa-save"></i> Save</a>';
					html += '<a class="btn btn-xs red" href="javascript: void(0);" onclick="cancelShippingFeeEdit(' + shipping_id + ', '+shipping_fee+');"><i class="fa fa-times"></i> Cancel</a></div>';
					html += '</div>';
					html += '<label for="shipping_fee_1" generated="true" class="error"></label>';
					$('#shippingfeetd_' + shipping_id).html(html);
				}

				function cancelShippingFeeEdit(shipping_id, shipping_fee) {
					if(shipping_fee <= 0)
					{
						$('#shippingfeecurr_'+shipping_id).addClass('hide');
						$('#shippingfeetd_'+shipping_id).html('<p class="margin-top-5">'+product_free_label+'</p>');
					}
					else
					{
						$('#shippingfeetd_' + shipping_id).html('<p class="margin-top-5"> '+shipping_fee+'</p>');
					}
					removeErrorDialog();
				}

				function saveShippingFeeAmount(shipping_id)
				{
					if($('#shipping_fee_'+shipping_id).val() != '')
					{
						shipping_fee = $('#shipping_fee_'+shipping_id).val();
						postData = 'action=edit_shipping&shipping_id=' + shipping_id + '&shipping_fee=' + shipping_fee + '&product_id='+product_id,
						displayLoadingImage(true);
						$.post(product_actions_url, postData,  function(response)
						{
							hideLoadingImage (false);
							data = eval( '(' +  response + ')');
							if(data.result == 'success')
							{
								if(shipping_fee <= 0)
								{
									('#shippingfeecurr_'+shipping_id).addClass('hide');
									$('#shippingfeetd_' + shipping_id).html('<p class="margin-top-5">'+product_free_label+'</p>');
								}
								else
								{
									$('#shippingfeetd_' + shipping_id).html('<p class="margin-top-5"> '+shipping_fee+'</p>');
								}
								//$('#shippingfeetd_' + shipping_id).html('<p class="margin-top-5"> '+shipping_fee+'</p>');
								edited_event = 'javascript:editItemShippingRow('+shipping_id+','+shipping_fee+')';
								$('#shipping_fee_edit_link_'+shipping_id).attr('onclick',edited_event);
								updateProductStatus();
								removeErrorDialog();
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
							}
						});
					}
				}

				function removeItemShippingRow(shipping_id) {
					if (!confirmItemChange()) {
						return false;
					}
					$("#dialog-delete-confirm").dialog({ title: cfg_site_name,	modal: true,
						buttons: [{
								text: common_yes_label,	click: function(){
									postData = 'action=delete_shipping&shipping_id=' + shipping_id + '&product_id='+product_id,
									displayLoadingImage(true);
									$.post(product_actions_url, postData,  function(response)
									{
										hideLoadingImage (false);
										data = eval( '(' +  response + ')');
										if(data.result == 'success') {
											$('#itemShippingRow_' + data.shipping_id).remove();
											updateProductStatus();
											removeErrorDialog();
										}
										else {
											showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
										}
									});
										$(this).dialog("close");
								}
							},{
								text: common_no_label,	click: function()
								{
									$(this).dialog("close");
								}
							}]
						});
				}

				$('#calc_cost').click(function(){
					$("#shipping_country").change();
				})

				$(".fnShippingCostEstimate" ).change(function() {
					var shipping_template = $("#shipping_template").val();
					var shipping_country = $("#shipping_country").val();
					var shipping_from_country = $("#shipping_from_country").val();
					var shipping_from_zip_code = $("#shipping_from_zip_code").val();
					var package_det = {};
					var weight = $('#weight_val').val();
					var custom = 'No';
					if($('input[name="custom"]:checked').length > 0)
						custom = 'Yes';
					var first_qty = $('#first_qty').val();
					var additional_qty = $('#additional_qty').val();
					var additional_weight = $('#additional_weight').val();
					var length = $('#l_w_h_length').val();
					var width = $('#l_w_h_width').val();
					var height = $('#l_w_h_height').val();
					package_det['weight'] = weight;
					package_det['custom'] = custom;
					package_det['first_qty'] = first_qty;
					package_det['additional_qty'] = additional_qty;
					package_det['length'] = length;
					package_det['width'] = width;
					package_det['height'] = height;
					var pack_str = '';
					$.each(package_det, function(idx2,val2) {
						if(pack_str == '')
							pack_str  = idx2 + "=" + val2;
						else
							pack_str  += "&"+idx2 + "=" + val2;
					});

					var actions_url = shipping_cost_estimate_url;
					var p_id = $('#id').val();
					postData = 'template_id=' + shipping_template + '&shipping_country=' + shipping_country + '&shipping_from_country='+shipping_from_country + '&shipping_from_zip_code='+shipping_from_zip_code +'&product_id=' + p_id+'&package_det='+package_det+'&'+pack_str,
					displayLoadingImage(true);
					$.post(actions_url, postData,  function(response)
					{
						hideLoadingImage (false);
						if(response)
						{
							$('#reference_shipping_cost_holder').html(response);
						}
					});
				});
			} // Shipping tabs ends

			if(pagetab == 'tax') // Tax tabs starts
			{
				jQuery.validator.addMethod("decimallimit", function (value, element) {
					return this.optional(element) || /^[0-9]*(\.\d{0,2})?$/i.test(value);
				}, "Only two decimals allowed");

				$("#addProductTaxfrm").validate({
					rules: {
						taxation_id: {
							required: true,
						},
						tax_fee: {
							required: true,
							number: true,
							decimallimit: true
						},
						fee_type: {
							required: true
						}
					},
					messages: {
						taxation_id: {
							required: mes_required,
						},
						tax_fee: {
							required: mes_required,
						},
						fee_type: {
							required: mes_required,
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

				function editItemTaxRow(taxation_id, tax_fee, fee_type) {
					html = '<div class="clearfix">'
					html += '<div class="col-md-3 col-sm-3 col-xs-3 margin-bottom-10"><div class="input-group"><input type="text" name="tax_fee" id="tax_fee_'+taxation_id+'" class="form-control" value="' + tax_fee + '" class="clsTextBoxSmall"></div></div>';
					html += '<div class="col-md-4 col-sm-4 col-xs-4 margin-bottom-10"><div class="input-group"><select name="fee_type" id="fee_type_'+taxation_id+'" class="form-control"><option value="percentage">%</option><option value="flat">Flat (In USD)</option>	</select></div></div>';
					html += '<div class="col-md-5 col-sm-5 col-xs-5 margin-top-5"><a class="btn btn-xs green" href="javascript: void(0);" onclick="saveTaxFeeAmount(' + taxation_id + ');"><i class="fa fa-save"></i> '+common_save+'</a>';
					html += '<a class="btn btn-xs red" href="javascript: void(0);" onclick="cancelTaxFeeEdit(' + taxation_id + ', '+tax_fee+', \''+fee_type+'\');"><i class="fa fa-times"></i> '+common_cancel+'</a></div>';
					html += '</div>';
					html += '<label for="tax_fee_1" generated="true" class="error" style=""></label>';
					$('#taxfeetd_' + taxation_id).html('');
					$('#taxfeetd_' + taxation_id).append(html);
					$('#fee_type_'+taxation_id).val(fee_type);
				}

				function cancelTaxFeeEdit(taxation_id, tax_fee, fee_type)
				{
					if(fee_type == "flat")
					{
						var currency_code = product_price_currency_value;
						tax_fee = '<strong>'+currency_code+' '+tax_fee+'</strong>';
					}
					else
					{
						tax_fee = '<strong>'+tax_fee+'%'+'</strong>';
					}
					$('#taxfeetd_' + taxation_id).html('');
					$('#taxfeetd_' + taxation_id).append(tax_fee);
					//$('#feetypetd_' + taxation_id).html('');
					//$('#feetypetd_' + taxation_id).append(fee_type);
					//$('#fee_action_' + taxation_id).html('');
					removeErrorDialog();
				}

				function saveTaxFeeAmount(taxation_id)
				{
					if($('#shipping_fee_'+taxation_id).val() != '')
					{
						tax_fee = $('#tax_fee_'+taxation_id).val();
						fee_type = $('#fee_type_'+taxation_id).val();
						postData = 'action=edit_tax&taxation_id=' + taxation_id + '&tax_fee=' + tax_fee + '&fee_type='+fee_type+'&product_id='+product_id,
						displayLoadingImage(true);
						var currency_code = product_price_currency_value;
						$.post(product_actions_url, postData,  function(response)
						{
						hideLoadingImage (false);
						data = eval( '(' +  response + ')');
						if(data.result == 'success') {
							tax_fee_org = tax_fee;
							$('#taxfeetd_' + taxation_id).html('');
							if(fee_type == 'percentage')
							tax_fee = '<strong>'+tax_fee+'%'+'</strong>';
							else if(fee_type == 'flat')
							tax_fee = '<strong>'+currency_code+' '+tax_fee+'</strong>';
							$('#taxfeetd_' + taxation_id).append(tax_fee);
							//$('#feetypetd_' + taxation_id).html('');
							//$('#feetypetd_' + taxation_id).append(fee_type);
							//$('#fee_action_' + taxation_id).html('');
							edited_event = 'javascript:editItemTaxRow('+taxation_id+','+tax_fee_org+',\''+fee_type+'\')';
							$('#tax_fee_edit_link_'+taxation_id).attr('onclick', edited_event);
							updateProductStatus();
							updateProductStatus();
							removeErrorDialog();
						}
						else {
							showErrorDialog({status: 'error', error_message: data.error_msg});
						}
						});
					}
				}

				function removeItemTaxRow(taxation_id)
				{
					if (!confirmItemChange()) {
						return false;
					}

					$("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
						buttons: [{	text: common_yes, click: function(){
								postData = 'action=delete_tax&taxation_id=' + taxation_id + '&product_id='+product_id,
								displayLoadingImage(true);
								$.post(product_actions_url, postData,  function(response)
								{
									hideLoadingImage (false);
									data = eval( '(' +  response + ')');
									if(data.result == 'success')
									{
										$('#itemTaxRow_' + data.taxation_id).remove();
										updateProductStatus();
										removeErrorDialog();
									}
									else
									{
										showErrorDialog({status: 'error', error_message: data.error_msg});
									}
								});
								$(this).dialog("close");
							}
							},{text: common_no,click:function(){	$(this).dialog("close");	}
						}]
					});
				}

				 $("#js-taxation-id" ).change(function() {
					if($(this).val()!='')
					{
						postData = 'action=get_taxation_details&taxation_id=' + $(this).val() + '&product_id='+product_id,
						displayLoadingImage(true);
						$.post(product_actions_url, postData,  function(response)
						{
							hideLoadingImage (false);
							data = eval( '(' +  response + ')');
							if(data.result == 'success')
							{
								$('#js-tax_fee').val(data.tax_fee);
								$('#js-fee_type').val(data.fee_type);
								var fee_type_string = $("#js-fee_type option:selected" ).text();
								$('*[data-id="js-fee_type"]').find(".filter-option").html(fee_type_string);
								//$('#select2-chosen-2').html(fee_type_string);
								if(data.fee_type == 'flat')
								{
								//$('#js-tax-currency').removeClass('hide');
								//$('#js-tax-percentage').addClass('hide');
								}
								else
								{
								//$('#js-tax-currency').addClass('hide');
								//$('#js-tax-percentage').removeClass('hide');
								}
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data.error_msg});
							}
						});//
					}
				});

			} // Tax tabs ends


			if(pagetab == 'preview_files') // Preview tabs starts
			{
				function editItemImageTitle(product_id, type) {
					if (!confirmItemChange()) {
						return false;
					}
					$('#item_' + type + '_image_title').show();
					$('#item_' + type + '_edit_span').hide();
					$('#item_' + type + '_image_save_span').show();
					$('#item_' + type + '_image_title').focus();
				}

				function saveProductImageTitle(product_id, type, no_process_dialog) {
					var image_title = encodeURIComponent($('#item_'+ type +'_image_title').val());
					postData = 'action=save_product_' + type + '_image_title&product_image_title=' + image_title +'&product_id=' + product_id;
					if (!no_process_dialog)
						displayLoadingImage (true);
					$.post(product_actions_url, postData,  function(data)
					{
						if (data == 'success') {
							$('#item_' + type + '_edit_span').show();
							$('#item_' + type + '_image_save_span').hide();
						} else {
							showErrorDialog({status: 'error', error_message: not_completed});
						}
						hideLoadingImage (false);
					});
				}

				 $(function(){
					var btnUpload=$('#upload_thumb');
					new AjaxUpload(btnUpload, {
						action: product_actions_url,
						name: 'uploadfile',
						data: ({action: 'upload_product_thumb_image',product_id : product_id, upload_tab: 'preview'}),
						method: 'POST',
						onSubmit: function(file, ext){
							if (!confirmItemChange()) {
								return false;
							}
							if (!(ext && /^(alowed_thumb_formats)$/.test(ext))){
								showErrorDialog({status: 'error', error_message: upload_format_err_msg});
								return false;
							}
							var settings = this._settings;
							settings.data.item_image_title = $.trim($('#item_thumb_image_title').val());
							displayLoadingImage(true);
						},
						onComplete: function(file, response) {
							//console.info(response); hideLoadingImage (false);
							data = eval( '(' +  response + ')');
							hideLoadingImage(false);
							if(data.status=="success") {
								$('#item_thumb_image_id').attr('src',data.server_url + '/'+ data.filename);
								if (data.t_width == '') {
									$('#item_thumb_image_id').removeAttr('width');
								} else {
									$('#item_thumb_image_id').attr('width',data.t_width)
								}
								if (data.t_height == '') {
									$('#item_thumb_image_id').removeAttr('height');
								} else {
									$('#item_thumb_image_id').attr('height',data.t_height)
								}
								if ($('#item_thumb_image_title').val() == '') {
									$('#item_thumb_image_title').val(data.title);
								}
								$('#item_thumb_image_id').attr('title', $('#item_thumb_image_title').val()).attr('alt', $('#item_thumb_image_title').val());
								$('#item_thumb_image_id').show();
								$('#link_remove_thumb_image').show();
								$('#item_thumb_image_title_holder').show();
								updateProductStatus();
							} else{
								showErrorDialog(data);
							}
						}
					});
				});

				 ////////
				 function editItemSwapImageTitle(resource_id) {
					if (!confirmItemChange()) {
						return false;
					}
					$('#swap_image_title_field_' + resource_id).show();
					$('#item_swap_image_edit_span_' + resource_id).hide();
					$('#item_swap_image_save_span_' + resource_id).show(); //.addClass('clsSubmitButton');
					$('#swap_image_title_field_' + resource_id).focus();
					return false;
				}
			}	// Preview tabs ends

			if(pagetab == 'variations') // variations tabs starts
			{
				$('#prd_var_grp').change(function(){
					postData = 'action=load_group_variations_list&group_id=' +  $('#prd_var_grp').val() + '&product_id=' + product_id;
					$.post(product_actions_url, postData,  function(response)
					{
						$('#regenVarGrp').html(response);
						// disable the checkbox if not assigned yet
						$('.varChkOpt').each(function(index) {
							if ($(this).attr('checked'))
								$(this).parents('tr').find('.varAttrOpt').removeAttr("disabled");
							else
								$(this).parents('tr').find('.varAttrOpt').attr("disabled", "disabled");
						});
					});
					return false;
				});


				$(document).ready(function(){

					$( document ).on( "click", '#matrix_variationitem_list_submit', function() {
					//$("#matrix_variationitem_list_submit").click(function(){
						multipleEditSelected($(this));
					});

					/* funtion called when action submitted from the matrix listing for edit */
					function multipleEditSelected(obj)
					{
						var selected_button = $(this).attr('id');
						var selected_action = $('#select_action').val();

						if(selected_action == '')
						{
							$("#dialog-confirm-content").html(matrix_select_action);
							$("#dialog-confirm").dialog({
								title: matrix_edit_head,
								modal: true,
								buttons: [{
									text: common_ok,
									click: function() { $(this).dialog("close");     }
								}]
							});
						}
						else if($("#selFormMatrix input[type=checkbox]:checked").length == 0 ||
							($("#selFormMatrix input[type=checkbox]:checked").length == 1 && $("#checkall").attr('checked')))
						{
							$("#dialog-confirm-content").html(selection_none_err);
							$("#dialog-confirm").dialog({
								title: matrix_edit_head,
								modal: true,
								buttons: [{
									text: common_ok,
									click: function()
									{
										 $(this).dialog("close");
									}
								}]
							});
						}
						else
						{
							$("#dialog-confirm-content").html(matrix_edit_content);
							$("#dialog-confirm").dialog({
								title: matrix_edit_head, modal: true,
								buttons: [{	text: common_ok, click: function(){
												 $(this).dialog("close");
												$('#item_action').val(selected_action);
												showMatrixUpdate('multiple', selected_action);
											}
										},{text: common_cancel,click:function(){	$(this).dialog("close");	}
									}]
							});
						}
						return false;
					}
				});

				$( document ).on( "click", '#edit_product', function() {
					if($("#frmItemVarOptList input[type=checkbox]:checked").length == 0 ||
						($("#frmItemVarOptList input[type=checkbox]:checked").length == 1 && $("#checkall").attr('checked')))
						{
							showErrorDialog({status: 'error', error_message: 'Select atleast one variation and attribute '});
							return false;
						}
					return true;
				});

				$( document ).on( "click", '#variationlist_ckbox', function() {
					$('.case').prop('checked', this.checked);
				});

				$( document ).on( "click", '#variationlist_sel_ckbox', function() {
					$('.case').prop('checked', this.checked);
					if($(this).is(':checked'))
					{
						$('.varAttrOpt').prop('checked', this.checked).removeAttr("disabled");
					}
					else
					{
						$('.varAttrOpt').prop('checked', this.checked).attr("disabled", "disabled");

					}
				});

				$(document).on("click", '.varChkOpt', function(){
					$(this).parents('tr').find('.varAttrOpt').prop('checked', this.checked);
					if ($(this).attr('checked'))
						$(this).parents('tr').find('.varAttrOpt').removeAttr("disabled");
					else
						$(this).parents('tr').find('.varAttrOpt').attr("disabled", "disabled");
				});


				var showMatrixUpdate = function() {
					matrix_id = arguments[0];
					var edit_field = ''; //all fields
					if(arguments.length > 1)
					{
						edit_field =  arguments[1];
					}
					if(matrix_id == 'multiple')
					{
						var selectedItems = new Array();
						$("input[name='matrix_ids[]']:checked").each(function()
						{
							selectedItems.push($(this).val());
						});
						postData = 'matrix_ids[]='+selectedItems+'&mul_mat_edit=1&edit_field='+edit_field;
					}
					else
					{
						postData = 'matrix_id='+matrix_id;
					}
					displayLoadingImage(true);
					postData += '&action=getMatrixDetails&product_id=' + product_id;
					$.post(product_actions_url, postData,  function(response)
					{
						hideLoadingImage (false);
						document.getElementById('matrixUpdateBlock').innerHTML = response;
						//$('#matrixUpdateBlock').html(response);
						$('#matrixUpdateBlock').removeClass('disp-none').focus();
					});
					return false;
				}

				function doMatrixAction(action, matrix_id, attribute_id) {
					if(action == 'delete_matrix')
					{
						$("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
							buttons: [{
								text: common_yes, click: function() {
									displayLoadingImage(true);
									postData = 'action='+action+'&matrix_id='+matrix_id+'&product_id=' + product_id + '&attribute_id=' + attribute_id;
									$.post(product_actions_url, postData,  function(response)
									{
										hideLoadingImage (false);
										$('#matrix_'+matrix_id).remove();
									});
									$(this).dialog("close"); }
								},{ text: common_no_label, click: function() { $(this).dialog("close"); } }]
						});

					}
					else
					{
						displayLoadingImage(true);
						postData = 'action='+action+'&matrix_id='+matrix_id+'&product_id=' + product_id + '&attribute_id=' + attribute_id;
						$.post(product_actions_url, postData,  function(response)
						{
							hideLoadingImage (false);
							if(action == 'enable_matrix'){
								$('#matrix_'+matrix_id).removeClass('clsVariationListDisabled');
							}
							else if(action == 'disable_matrix') {
								$('#matrix_'+matrix_id).addClass('clsVariationListDisabled');
							}

							if(action == 'set_default_matrix' || action == 'rem_default_matrix') {
								$('#var_matrix_block').html(response);
							}
							else {
								$('#matrix_'+matrix_id).html(response);
							}
							return false;
						});
					}
				}

				$( document ).on( "click", '.fn_updSwapImg', function() {
					var elementId  = $(this).closest('tr').attr('id');
					var elem_arr = elementId.split('_');
					$('#sel_matrix_id').val(elem_arr[1]);
				});

				$(".fn_updSwapImg").fancybox({
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

				function srcUpdImg(selImg, sel_img_id){
					$('#matrix_swap_img_id').val(sel_img_id);
					var matrix_id = $('#sel_matrix_id').val();
					if(matrix_id == '')
						return false;
					displayLoadingImage(true);
					postData = 'action=updateMatrixSwapImage&product_id=' + product_id + '&matrix_id=' + matrix_id + '&matrix_swap_img_id=' + sel_img_id;
					$.post(product_actions_url, postData,  function(response)
					{
						$('#sel_matrix_id').val('');
						hideLoadingImage (false);
						$('#matrix_'+matrix_id).html(response);
						return false;
					});
					return false;
				}

				function assgnImg(selImg, sel_img_id){
					$('#item_swap_image_id').attr('src', selImg);
					$('#matrix_swap_img_id').val(sel_img_id);
					$('#remSwapImg').parent('span').show();
				}

				function showGrp(){
					$('#var_grp_block').show();
					$('#var_matrix_block').hide();
					$('#var_matrix_block').next('div').hide();
				}

				$( document ).on( "click", '#cancel_populate', function() {
					$('#var_grp_block').hide();
					$('#var_matrix_block').show();
					$('#var_matrix_block').next('div').show();
				});

				$( document ).on( "click", '#cancel_update_matrix', function() {
					$('#frmItemMatrixUpdate')[0].reset();
					$('#matrix_price_impact, #matrix_giftwrap_price_impact, #matrix_shippingfee_impact').trigger('change');
					$('#matrixUpdateBlock').addClass('disp-none');
				});

				$( document ).on( "change", '#matrix_price_impact, #matrix_giftwrap_price_impact, #matrix_shippingfee_impact', function() {
					if($(this).val() == 'unchange') {
						$(this).parent('div').next('div').find('span').hide();
					}
					else {
						$(this).parent('div').next('div').find('span').show();
						$(this).parent('div').next('div').find('span').find('input[type=text]').val("");
					}
				});

				$(document).on( "click", '.remMatSwapImg', function() {
					var matrix_id = $(this).attr('alt');
					$("#dialog_msg").html(remove_swap_msg);
					$("#dialog-delete-confirm").dialog({
						title: matrix_edit_head, modal: true,
						buttons: [{	text: common_ok, click: function(){
										 $(this).dialog("close");
										displayLoadingImage(true);
										postData = 'action=removeMatrixSwapimg&product_id='+product_id+'&matrix_id='+ matrix_id;
										$.post(product_actions_url, postData,  function(response)
										{
											hideLoadingImage(false);
											data = eval( '(' +  response + ')');
											if (data.status == 'success') {
												$('#matrix_'+matrix_id).html(data.op_html);
												return false;
											}
										});
									}
								},{text: common_cancel,click:function(){	$(this).dialog("close");	}
							}]
					});
					return false;
				});

				$(document).on( "click", '#remSwapImg', function() {
					var matrix_id = $(this).attr('alt');
					$("#dialog_msg").html(remove_swap_msg);
					$("#dialog-delete-confirm").dialog({
						title: matrix_edit_head, modal: true,
						buttons: [{	text: common_ok, click: function(){
										$(this).dialog("close");
										displayLoadingImage(true);
										postData = 'action=remove_matrix_swapimg&product_id='+product_id+'&matrix_id='+ matrix_id;
										$.post(product_actions_url, postData,  function(response)
										{
											hideLoadingImage(false);
											data = eval( '(' +  response + ')');
											if (data.status == 'success') {
												var op_data = '<img class="clsItemResourceImage" src="'+data.data_arr.img_src+'" alt="'+data.data_arr.img_title+'" '+data.data_arr.disp_img+' />';
												$('#item_swap_image_id').attr('src', data.data_arr.img_src);
												$('#item_swap_image_id').attr('alt', data.data_arr.img_title);
												$('#item_swap_image_id').attr('width', 74);
												$('#item_swap_image_id').attr('height', 74);
												$('#remSwapImg').parent('span').hide();
												location.reload();
												return false;
											}
										});
									}
								},{text: common_cancel,click:function(){	$(this).dialog("close");	}
							}]
					});
					return false;
				});

				$(document).on( "click", '#update_matrix_det', function() {
					displayLoadingImage(true);
					removeErrorDialog();
					postData = 'action=update_matrix_details&product_id='+ product_id +'&'+ $('#frmItemMatrixUpdate').serialize();
					$.post(product_actions_url, postData,  function(response)
					{
						hideLoadingImage(false);
						data = eval( '(' +  response + ')');
						if (data.status == 'success') {
							$('#matrixListBlock').html(data.op_html);
							return false;
						}
						else
						{
							showErrorDialog(data);
						}
					});
					return false;
				});


				$("#matrix_variationitem_list_submit").click(function()
				{
					multipleEditSelected($(this));
				});

				/* funtion called when action submitted from the matrix listing for edit */
				function multipleEditSelected(obj)
				{
					var selected_button = $(this).attr('id');
					var selected_action = $('#select_action').val();
					if(selected_action == '')
					{
						$("#dialog-confirm-content").html(matrix_select_action);
						$("#dialog-confirm").dialog({
							title: matrix_edit_head,
							modal: true,
							buttons: [{
								text: common_ok,
								click: function() { $(this).dialog("close");     }
							}]
						});
					}
					else if($("#selFormMatrix input[type=checkbox]:checked").length == 0 ||
						($("#selFormMatrix input[type=checkbox]:checked").length == 1 && $("#checkall").attr('checked')))
					{
						$("#dialog-confirm-content").html(selection_none_err);
						$("#dialog-confirm").dialog({
							title: matrix_edit_head,
							modal: true,
							buttons: [{
								text: common_ok,
								click: function()
								{
									 $(this).dialog("close");
								}
							}]
						});
					}
					else
					{
						$("#dialog-confirm-content").html(matrix_edit_content);
						$("#dialog-confirm").dialog({
							title: matrix_edit_head, modal: true,
							buttons: [{	text: common_ok, click: function(){
											 $(this).dialog("close");
											$('#item_action').val(selected_action);//console.log('update column');
											showMatrixUpdate('multiple', selected_action);
										}
									},{text: common_cancel,click:function(){	$(this).dialog("close");	}
								}]
						});
					}
					return false;
				}
			}	// variations tabs ends


			if(pagetab == 'cancellation_policy') // cancellation_policy tabs starts
			{
				$(document).ready(function()
				{
					/*var selected_val = $('input[name=use_cancellation_policy]:radio:checked').val();
					if (selected_val == 'Yes')
					{
						showCancellationFields(true);
					}
					else
					{
						showCancellationFields(false);
					}*/

					$('input[name=use_cancellation_policy]').click(function(){
						//default_cancel_available
						var selected_val = $('input[name=use_cancellation_policy]:radio:checked').val();
						if (selected_val=='Yes')
						{
							//default_cancel_available
							if($('#default_cancel_available')!='')
							{
								var selected_def_val  = $('input[name=use_default_cancellation]:radio:checked').val();
								if(selected_def_val == 'Yes')
								{
									showCancellationFields(false);
									$('.js_defaultCancellationField').show();
									$('.js_defaultCancellationViewField').show();

								} else {
									showCancellationFields(true);
									$('.js_defaultCancellationViewField').hide();
								}
							}
							else
							{
								showCancellationFields(true);
								$('.js_defaultCancellationViewField').hide();
							}
						} else {
							showCancellationFields(false);
						}
					});

					$('input[name=use_default_cancellation]').click(function(){
						var selected_val  = $('input[name=use_default_cancellation]:radio:checked').val();
						if(selected_val == 'Yes')
						{
							showCancellationFields(false);
							$('.js_defaultCancellationField').show();
							$('.js_defaultCancellationViewField').show();
						} else {
							showCancellationFields(true);
							$('.js_defaultCancellationViewField').hide();
						}
					});

				});

				 function showCancellationFields(flag) {
					if (flag) {
						$('.js_clsCancellationFields').show();
					}
					else{
						$('.js_clsCancellationFields').hide();
					}
				}

				function removeProductCancellationPolicy(resource_id)
				{
					//console.log($("#used_default").val());
					if($("#used_default").val() == '')
					{
						//if($("#used_default").val() == ''){	}
						$("#dialog-cancellation-policy-delete-confirm").dialog({
							title: cfg_site_name,
							modal: true,
							buttons: [{
								text: common_yes_label,
								click: function()
								{
									displayLoadingImage(true);
									postData = 'action=delete_cancellation_file&product_id='+product_id,
									$.post(product_actions_url, postData,  function(response)
									{
										data = eval( '(' +  response + ')');
										//console.log(data);
										if(data.result == 'success')
										{
											window.location.reload(true);
										}
										else
										{
											hideLoadingImage (false);
											showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
										}
									});
									$(this).dialog("close");
								}
							},{
								text: common_no_label,
								click: function()
								{
									 $(this).dialog("close");
								}
							}]
						});
					}
					else
					{
						showCancellationFields(true);
					}
				}
			}	// cancellation_policy ends

			if(pagetab == 'status') // status tabs starts
			{
				function showHideNotesBlock() {
					if ($('#sel_NotesBlock').is(':visible')) {
						$('#showNotes').html(products_show_product_notes+" <i class='fa fa-chevron-down'></i>");
						$('#sel_NotesBlock').hide();
					}
					else {
						$('#showNotes').html(products_hide_product_notes+" <i class='fa fa-chevron-up'></i>");
						$('#sel_NotesBlock').show();
					}
				}

				function checkAccBalance() {
					if($('#user_account_balance').length > 0 && $('#amount_to_pay').length > 0) {
						var user_account_balance = parseFloat($("#user_account_balance").val());
						var amount_to_pay = parseFloat($("#amount_to_pay").val());
						if(is_featuredproducts_allowed) {
							if($('#plan').length > 0) {
					            if($("#plan option:selected").val() != '') {
					            	var plan = $("#plan option:selected").text();
						            var plan_amount_currency = plan.split(':')[1];
						            var plan_amount = plan_amount_currency.trim().split(' ')[1];
						            plan_amount = parseFloat(plan_amount.trim());
						            amount_to_pay = amount_to_pay + plan_amount;
					        	}
				            }
				            $('#total_amount_to_pay').html(total_amount_to_pay_txt + ': ' + site_default_currency + ' ' + amount_to_pay.toFixed(2));
				        }
				        if(user_account_balance < amount_to_pay) {
							$('#edit_product').hide();
							$('#add_amount_to_credit').show();
						}
						else {
							$('#edit_product').show();
							$('#add_amount_to_credit').hide();
						}
					}
				}
				checkAccBalance();

			}	// status tabs ends
		}
		function confirmItemChange()
		{
			return true;
		}

		function updateProductStatus(){
			item_status = 'Draft';
			$('#item_status_text').html(item_status);
			$('#item_current_status').val(item_status);
		}

		/**
		 *
		 * @access public
		 * @return void
		 **/

		//var common_ok_label = "{{ trans('common.ok') }}" ;
		var cfg_site_name_1 = "Cancellation Policy - Webshop" ;
		//var cancellation_policy_text ='';
		function CancellationPolicy(ele)
		{
			var cancellation_policy_text = ele.id;
			//alert(cancellation_policy_text);
			bootbox.dialog({message: cancellation_policy_text, title: cfg_site_name_1,
				buttons: {
				main: {
				  width: "800",
				  height: "400",
				  label: "Ok",
				  className: "btn-primary",
				  callback: function() {
					//window.location.reload(true);
				  }
				}
			  }
			});
			$('.modal-dialog').addClass('modal-container');
		}

		$(document).ready(function(){
			$('.fn_custom').hide();
			if($('.custom_package').is(":checked")){
				$('.fn_custom').show();
			}
		});

		$('.custom_package').click(function() {
			if($('.custom_package').is(":checked")){
				$('.fn_custom').show();
			}
			else {
				if ($('#checkbox_class_yes').val() == 'Yes') {
					$('.fn_custom').show();
				} else {
					$('.fn_custom').hide();
				}
				$('.fn_custom').hide();
			}
		});

		$("#addProductPackageDetailsfrm").validate({
			onfocusout: injectTrim($.validator.defaults.onfocusout),
			rules: {
			  first_qty: {
					digits: true
				},
				additional_qty: {
					digits: true
				},
				additional_weight: {
					number: true,
					decimallimit3: true
				},
				weight: {
					required: true,
					number: true,
					decimallimit3: true,
					min: 0.001,
					max: 500
				},
				length: {
					required: true,
					digits: true,
					min: 1,
					max: 700
				},
				width: {
					required: true,
					digits: true,
					min: 1,
					max: 700
				},
				height: {
					required: true,
					digits: true,
					min: 1,
					max: 700
				},
				shipping_from_country: {
					required: true
				},
				shipping_from_zip_code: {
					required: true
				},
			},
			messages: {
			   weight: {
					required: mes_required,
					min: "Please fill in between the numbers 0.001 to 500",
					max: "Please fill in between the numbers 0.001 to 500",
				},
				length: {
					required: mes_required,
					min: "Please fill in between the numbers 1 to 700",
					max: "Please fill in between the numbers 1 to 700",
				},
				width: {
					required: mes_required,
					min: "Please fill in between the numbers 1 to 700",
					max: "Please fill in between the numbers 1 to 700",
				},
				height: {
					required: mes_required,
					min: "Please fill in between the numbers 1 to 700",
					max: "Please fill in between the numbers 1 to 700",
				},
				shipping_from_country: {
					required: mes_required
				},
				shipping_from_zip_code: {
					required: mes_required
				},

			},
			submitHandler: function(form) {
				displayLoadingImage(true);
				form.submit();
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

		$('#size_info_message').html('')
		$('#size_error_message').html('');
		$(".weight_package").change(function(){
			if(weight == '' || length == '' || width =='' || height =='')
			return false;

			var mesg = length_width_height_size_lbl;
			var text = length_width_height_size_lbl;
			var weight = parseFloat($('#weight_val').val());
			var length = parseFloat($('.length').val());

			var width = parseFloat($('.width').val());
			var height = parseFloat($('.height').val());
			var size_cal = length*width*height;
			var cal = length + ((width + height)*2);
			var cal1 = width + ((length + height)*2);
			var cal2 = height + ((width + length)*2);
			var new_text = text.replace("VAR_WEIGHT", weight);
			var size_total = size_cal/5000;
			var new_text1 = new_text.replace("VAR_CAL_WEIGHT", size_total);
			var new_info = mesg.replace("VAR_SIZE_CAL", size_cal);
			$('#length_width_height_msg').html(new_info);
			$('#size_error_message').html('');
			if(2700 >= cal || 2700 >= cal1 || 2700 >= cal2)
			{
				if(weight >= size_total)
				{
					//console.log('success');
					$('#size_info_message').removeClass('note note-info').html('');
				}
				else
				{
					//console.log('fail');
					$('#size_info_message').addClass('note note-info').html(new_text1);
				}
			}
			else
			{
				var text1 =  length_width_height_msg;
				$('#size_error_message').html(text1);
			}
		});

	}
	// Add product page script ends

	// Add product page script starts
	else if(page_name == 'admin_add_product')
	{
		if (typeof pagetab === 'undefined') {
			//  tab not defined
		}
		else
		{
			if(pagetab == 'price')
			{
				if(allow_giftwrap == 1)
				{
					if ($('#accept_giftwrap').attr('checked'))
						$('.fn_giftwrap_field').show();
					else
						$('.fn_giftwrap_field').hide();


					$('#accept_giftwrap').click(function() {
						if (this.checked) {
							$('.fn_giftwrap_field').fadeIn();
						} else {
							$('.fn_giftwrap_field').fadeOut();
						}
					});
				}
			}
		}
	}
	// Add product page script ends

	// Edit profile page script starts
	else if(page_name == 'edit_profile')
	{
		jQuery.validator.addMethod(
				  "chkIsNameHasRestrictedWordsLike",
				  function(value, element) {
					if(value != "") {
						var filterWords = new Array();
						var restricted_keywords = restrict_keywords_message;
						filterWords = restricted_keywords.split(",");
						for(i = 0; i < filterWords.length; i++) {
							// "i" is to ignore case
							var regex = new RegExp(filterWords[i], "gi");
							if(value.match(regex)) {
								err_msg = err_msg_message;
								err_msg = err_msg.replace("{0}", filterWords[i]);
								return false;
							}
						}
						return true;
					}
					return true;
				  },
				  messageFunc
			);

			jQuery.validator.addMethod(
				  "oldpasswordvalidate",
				  function(value, element) {
					var new_password = document.getElementById('password');
					var confirm_password = document.getElementById('password_confirmation');
					if((new_password.value != "" || confirm_password.value != "") && value == "")
						{
							return false;
						}
					else return true;
				  },
				 mes_required
			);

			jQuery.validator.addMethod(
				"newpasswordvalidate",
				function(value, element) {
				var old_password = document.getElementById('Oldpassword');
				if(old_password.value != "" && value == "")
					{
						return false;
					}
				else return true;
				},
				mes_required
			);


			jQuery.validator.addMethod("notEqual", function(value, element) {
			   return $('#Oldpassword').val() != $('#password').val();
			}, "Current password & New password should be different");

			jQuery.validator.addMethod(
			  "chkIsNameHasRestrictedWordsExact",
			  function(value, element) {
				if(value != "") {
					var filterWords = new Array();
					var restricted_keywords = restrict_keywords_exact_message;
					filterWords = restricted_keywords.split(",");
					for(i = 0; i < filterWords.length; i++) {
						// "i" is to ignore case
						var regex = new RegExp('\\b' + filterWords[i] + '\\b' , "gi");
						if(value.match(regex)) {
							err_msg = err_msg_message;
							err_msg = err_msg.replace("{0}", filterWords[i]);
							return false;
						}
					}
					return true;
				}
				return true;
			  },
			  messageFunc
			);
			jQuery.validator.addMethod(
				"chkAlphaNumericchars",
				function(value, element) {
					if(value!=""){
						if (/^[a-zA-Z0-9\s]*$/.test(value))
							return true;
						return false;
					}
					return true;
				},
				merchant_signup_specialchars
			);
			jQuery.validator.addMethod(
				"chkspecialchars",
				function(value, element) {
					if(value!=""){
						if (/^[a-zA-Z0-9'/,&() -]*$/.test(value))
							return true;
						return false;
					}
					return true;
				},
				merchant_signup_specialchars
			);
			jQuery.validator.addMethod(
					"chkSpecialCharsRepeatedTwice",
					function(value, element) {
					if(value!=""){
						value = value.trim();
						if ((/[,]{2}/.test(value)) || (/[&]{2}/.test(value)) || (/[-]{2}/.test(value)) || (/[ ]{2}/.test(value)) || (/[/(]{2}/.test(value)) || (/[/)]{2}/.test(value)) || (/[']{2}/.test(value)) || (/[//]{2}/.test(value)))
							return false;
						return true;
					}
					return true;
				},
				merchant_signup_twice_not_allowed
			);

			$("#editaccount_frm").validate({
				onfocusout: injectTrim($.validator.defaults.onfocusout),
				rules: {
					Oldpassword: {
						required: true,
						oldpasswordvalidate: true
					},
					password: {
						newpasswordvalidate: true,
						minlength: fieldlength_password_min,
						maxlength: fieldlength_password_max,
						notEqual: true
					},
					password_confirmation:{
						required: function(){ if($('#password').val() != ''){
													return true;
												}else{
													return false;
												}},
						equalTo: "#password"
					}
				},
				messages: {
					Oldpassword: {
						required: mes_required,
						oldpasswordvalidate: mes_required
					},
					password:{
						newpasswordvalidate: mes_required,
						minlength: jQuery.format(validation_password_length_low),
						maxlength: jQuery.format(validation_maxLength)
					},
					password_confirmation:{
						required: mes_required,
						equalTo: validation_password_mismatch
					}
				}
			});

		$("#editpersonal_details_frm").validate({
			rules: {
				first_name: {
					required: true,
					minlength: fieldlength_name_min_length,
					maxlength: fieldlength_name_max_length,
					chkIsNameHasRestrictedWordsLike: true,
					chkIsNameHasRestrictedWordsExact: true,
					chkspecialchars: true,
				},
				last_name: {
					required: true,
					minlength: fieldlength_name_min_length,
					maxlength: fieldlength_name_max_length,
					chkIsNameHasRestrictedWordsLike: true,
					chkIsNameHasRestrictedWordsExact: true,
					chkspecialchars: true,
				}
			},
			messages: {
				first_name: {
					required: mes_required
				},
				last_name: {
					required: mes_required
				}
			},
			submitHandler: function(form) {
				form.submit();
			}
		});

		$("#editimageaccount_frm").validate({
			rules: {
				file: {
					required: true,
					accept: "jpg,png,jpeg,gif"
				}
			},
			messages: {
				file: {
					required: mes_required,
					accept: format
				}
			}
		});
	}
	// Edit profile page script ends

	// Shop Details page script starts
	else if(page_name == 'shop_details')
	{
		   function showHidepanels(obj, div_id) {
				var link_class = obj.className;
				$('#'+div_id).slideToggle(500);
				// toggle open/close symbol
				var span_elm = $('.'+link_class+' i');
				if(span_elm.hasClass('customize-fn_show')) {
					span_elm.removeClass('customize-fn_show');
					span_elm.addClass('customize-fn_hide');
				}
				else {
					span_elm.removeClass('customize-fn_hide');
					span_elm.addClass('customize-fn_show');
				}
				return false;
			}

			$("#shopanalytics_frm").validate({
				rules: {
					shop_analytics_code: {
						required: true
					}
				},
				messages: {
					shop_analytics_code:{
						required: mes_required
					}
				}
			});

			jQuery.validator.addMethod("slug", function(value, element) {
				return this.optional(element) || /^([a-z0-9_-])+$/i.test(value);
			}, alpha_numeric);

			var doSubmit = function(){
				var frmname = arguments[0];
				var divname = arguments[1];
				var form_validated = true;
				//if(frmname != "shopaddress_frm"){
					var validator = $("#"+frmname).validate({  });
					if(!$("#"+frmname).valid())
					{
						form_validated = false;
					}
				//}

				if(form_validated)
				{
					displayLoadingImage(true);
					var options = {
						target:     '#'+divname,
						url:        $("#"+frmname).attr('action'),
						success: function(responseData)
						{
							if(frmname == "shopbanner_frm")
							{
								$('#success_div').hide();
								$(document).ready(function(){
									$("#shopbanner_frm").validate({
										rules: {
											shop_banner_image: {
												required: true,
											}
										}
									});
								});
							}

							if(frmname == "shoppolicy_frm")
							{
								$(document).ready(function(){
									$("#shoppolicy_frm").validate({
										rules: {
											shop_name: {
												required: true,
												minlength: shopname_min_length,
												maxlength: shopname_max_length
											},
											url_slug: {
												required: true
											},
											shop_slogan: {
												minlength: shopslogan_min_length,
												maxlength: shopslogan_max_length
											},
											shop_desc: {
												minlength: fieldlength_shop_description_min,
												maxlength: fieldlength_shop_description_max
											},
											shop_status: {
												required: true
											},
											shop_contactinfo: {
												minlength: fieldlength_shop_contactinfo_min,
												maxlength: fieldlength_shop_contactinfo_max
											}
										},
										messages: {
											shop_name: {
												required : mes_required,
												minlength: 'Please enter greater than '+shopname_min_length+' characters',
												maxlength: 'Please enter within '+shopname_max_length+' characters',
											},
											url_slug: {
												required : mes_required,
											},
											shop_slogan: {
												minlength: 'Please enter greater than '+shopslogan_min_length+' characters',
												maxlength: 'Please enter within '+shopslogan_max_length+' characters',
											},
											shop_desc: {
												minlength: 'Please enter greater than '+fieldlength_shop_description_min+' characters',
												maxlength: 'Please enter within '+fieldlength_shop_description_max+' characters',
											},
											shop_status: {
												required : mes_required,
											},
											shop_contactinfo: {
												minlength: 'Please enter greater than '+fieldlength_shop_contactinfo_min+' characters',
												maxlength: 'Please enter within '+fieldlength_shop_contactinfo_max+' characters'
											}
										}
									});
								});
							}
							if(frmname == "shopaddress_frm")
							{
								$(document).ready(function() {
									$("#shopaddress_frm").validate({
										rules: {
											shop_country: {
												required: true
											},
											shop_address1: {
												required: true
											},
											shop_city: {
												required: true
											},
											shop_state: {
												required: true
											},
											shop_zipcode: {
												required: true
											}
										},
										messages: {
											shop_country: {
												required: mes_required
											},
											shop_address1: {
												required: mes_required
											},
											shop_city: {
												required: mes_required
											},
											shop_state: {
												required: mes_required
											},
											shop_zipcode: {
												required: mes_required
											},
										},
										/* For Contact info violation */
										submitHandler: function(form) {
											form.submit();
										}
									});
								});
							}
							hideLoadingImage(true);
						}
					};
					// pass options to ajaxSubmit
					$('#'+frmname).ajaxSubmit(options);
				}
				else
				{
					validator.focusInvalid();
				}
				return false;
			};

			function removeShopImage(resource_id, imagename, imageext, imagefolder) {
				$("#dialog-delete-confirm").dialog({
					title: package_name,
					modal: true,
					buttons: [{
							text: common_yes_label,
							click: function()
							{
								displayLoadingImage();
								$.getJSON( url_del_image,
								{resource_id: resource_id, imagename: imagename, imageext: imageext, imagefolder: imagefolder},
									function(data)
									{
										hideLoadingImage();
										if(data.result == 'success')
										{
											$('#itemResourceRow_'+resource_id).remove();
											$('#success_div').show();
											$('#success_msg_div').hide();
											$('#success_div').html(shopdetails_banner_succ);
										}
										else
										{
											$('#success_div').hide();
											$('#success_msg_div').hide();
											//showErrorDialog({status: 'error', error_message: '{{ trans('common.invalid_action') }}'});
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

			function removeShopCancellationPolicy(resource_id) {
				$("#dialog-cancellation-policy-delete-confirm").dialog({
					title: package_name,
					modal: true,
					buttons: [{
							text: common_yes_label,
							click: function()
							{
								displayLoadingImage();
								$.getJSON( url_policy,
								{resource_id: resource_id},
									function(data)
									{
										hideLoadingImage();
										if(data.result == 'success')
										{
											$('#shopCancellationPolicyRow_'+resource_id).remove();
											$('#cancellation_pollicy_success_div_msg').hide();
											$('#cancellation_pollicy_success_div').show();
											$('#cancellation_pollicy_success_div').html(shopdetails_cancellation_succ);
										}
										else
										{
											$('#cancellation_pollicy_success_div').hide();
											$('#cancellation_pollicy_success_div_msg').hide();
											//showErrorDialog({status: 'error', error_message: '{{ trans('common.invalid_action') }}'});
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

			 $("#shoppaypal_frm").validate({
				rules: {
					paypal_id: {
						required: true,
						email: true
					},
				},
				messages: {
					paypal_id: {
						required : mes_required,
						email: valid_email
					},
				}
			});

			$(document).ready(function() {
				$('#shop_desc').keyup(function(e) {
					var text_length = $('#shop_desc').val().length;
					var text_remaining = desc_max - text_length;
					if(text_remaining >= 0)
					{
						$('#shop_desc_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
					}
					else
					{
						 $('#shop_desc').val($('#shop_desc').val().substring(0, desc_max));
					}
				});

				$('#shop_contactinfo').keyup(function(e) {
					var text_length = $('#shop_contactinfo').val().length;
					var text_remaining = contactinfo_max - text_length;
					if(text_remaining >= 0)
					{
						$('#shop_contactinfo_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
					}
					else
					{
						 $('#shop_contactinfo').val($('#shop_contactinfo').val().substring(0, contactinfo_max));
					}
				});

				$('#shop_name').focusout(function() {
					if ($('#url_slug').val() == '') {
						var tmp_str = $('#shop_name').val().replace(/\s/g,'-'); // to replace spaces with hypens
						tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
						tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
						tmp_str = alltrimhyphen(tmp_str);
						$('#url_slug').val(tmp_str);
					}
				});
				$('#url_slug').focusout(function() {
					if ($('#url_slug').val() != '') {
						var tmp_str = $('#url_slug').val().replace(/\s/g,'-'); // to replace spaces with hypens
						tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
						tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
						tmp_str = alltrimhyphen(tmp_str);
						$('#url_slug').val(tmp_str);
					}
				});
				function alltrimhyphen(str) {
					return str.replace(/^\-+|\-+$/g, '');
				}
			});

			$("#shoppolicy_frm").validate({
				rules: {
					shop_name: {
						required: true,
						minlength: shopname_min_length,
						maxlength: shopname_max_length
					},
					url_slug: {
						required: true
					},
					shop_slogan: {
						minlength: shopslogan_min_length,
						maxlength: shopslogan_max_length
					},
					shop_desc: {
						minlength: fieldlength_shop_description_min,
						maxlength: fieldlength_shop_description_max
					},
					shop_status: {
						required: true
					},
					shop_contactinfo: {
						minlength: fieldlength_shop_contactinfo_min,
						maxlength: fieldlength_shop_contactinfo_max
					}
				},
				messages: {
					shop_name: {
						required : mes_required,
						minlength: 'Please enter greater than '+shopname_min_length+' characters',
						maxlength: 'Please enter within '+shopname_max_length+' characters',
					},
					url_slug: {
						required : mes_required,
					},
					shop_slogan: {
						minlength: 'Please enter greater than '+shopslogan_min_length+' characters',
						maxlength: 'Please enter within '+shopslogan_max_length+' characters',
					},
					shop_desc: {
						minlength: 'Please enter greater than '+fieldlength_shop_description_min+' characters',
						maxlength: 'Please enter within '+fieldlength_shop_description_max+' characters',
					},
					shop_status: {
						required : mes_required,
					},
					shop_contactinfo: {
						minlength: 'Please enter greater than '+fieldlength_shop_contactinfo_min+' characters',
						maxlength: 'Please enter within '+fieldlength_shop_contactinfo_max+' characters'
					}
				}
			});

			$("#shopbanner_frm").validate({
				rules: {
					shop_banner_image: {
						required: true,
					}
				}
			});

				$("#shopaddress_frm").validate({
					rules: {
						shop_country: {
							required: true
						},
						shop_address1: {
							required: true
						},
						shop_city: {
							required: true
						},
						shop_state: {
							required: true
						},
						shop_zipcode: {
							required: true
						}
					},
					messages: {
						shop_country: {
							required: mes_required
						},
						shop_address1: {
							required: mes_required
						},
						shop_city: {
							required: mes_required
						},
						shop_state: {
							required: mes_required
						},
						shop_zipcode: {
							required: mes_required
						},
					},
					/* For Contact info violation */
					submitHandler: function(form) {
						form.submit();
					}
				});


		if(featuredsellers)
		{
			$(".fn_changeStatus").fancybox({
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


			$("#setfeaturedfrm").validate({
				rules: {
					plan: {
						required: true
					}
				},
				messages: {
					plan: {
						required: mes_required
					}
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					$('#dialog-product-confirm-content').html(alert_msg);
					$("#dialog-product-confirm").dialog({ title: sellers_featured_head, modal: true,
						buttons: [{ text: common_yes_label, click: function() {
										form.submit();
										$(this).dialog("close");
									}
								},{
									text: common_no_label,
									click: function() {
										$(this).dialog("close");
									}
								}
								]
					});
					return false;
				}
			});

			function checkAccBalance() {
				var user_account_balance = parseFloat($("#user_account_balance").val());
				var plan_amount = 0.00;
				if($('#plan').length > 0) {
					if($("#plan option:selected").val() != '') {
						var plan = $("#plan option:selected").text();
						var plan_amount_currency = plan.split(':')[1];
						var plan_amount = plan_amount_currency.trim().split(' ')[1];
						plan_amount = parseFloat(plan_amount.trim());
					}
				}
				if(user_account_balance < plan_amount) {
					$('#set_featured_sellers').hide();
					$('#add_amount_to_credit').show();
				}
				else {
					$('#set_featured_sellers').show();
					$('#add_amount_to_credit').hide();
				}
			}

			$('#add_amount_to_credit').click(function() {
				window.location.href = $(this).attr('href');
			});
		}
	}
	//Shop Details page script ends

	// Shop Policies page script Starts
	else if(page_name == 'shop_policy_details')
	{
		function showHidepanels(obj, div_id) {
				var link_class = obj.className;
				$('#'+div_id).slideToggle(500);
				// toggle open/close symbol
				var span_elm = $('.'+link_class+' i');
				if(span_elm.hasClass('customize-fn_show')) {
					span_elm.removeClass('customize-fn_show');
					span_elm.addClass('customize-fn_hide');
				}
				else {
					span_elm.removeClass('customize-fn_hide');
					span_elm.addClass('customize-fn_show');
				}
				return false;
			}

			$("#shopanalytics_frm").validate({
				rules: {
					shop_analytics_code: {
						required: true
					}
				},
				messages: {
					shop_analytics_code:{
						required: mes_required
					}
				}
			});

			jQuery.validator.addMethod("slug", function(value, element) {
				return this.optional(element) || /^([a-z0-9_-])+$/i.test(value);
			}, alpha_numeric);

			var doSubmit = function(){
				var frmname = arguments[0];
				var divname = arguments[1];

				var form_validated = true;
				if(frmname != "shopaddress_frm")
				{
					var validator = $("#"+frmname).validate({  });
					if(!$("#"+frmname).valid())
					{
						form_validated = false;
					}
				}

				if(form_validated)
				{
					displayLoadingImage(true);
					var options = {
						target:     '#'+divname,
						url:        $("#"+frmname).attr('action'),
						success: function(responseData)
						{
							if(frmname == "shopbanner_frm")
							{
								$('#success_div').hide();
							}
							hideLoadingImage(true);
						}
					};
					// pass options to ajaxSubmit
					$('#'+frmname).ajaxSubmit(options);
				}
				else
				{
					validator.focusInvalid();
				}
				return false;
			};


			tinymce.init({
				menubar: "tools",
				selector: "textarea.fn_editor",
				mode : "exact",
				elements: "product_description",
				removed_menuitems: 'newdocument',
				apply_source_formatting : true,
				remove_linebreaks: false,
				height : 200,
				plugins: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste emoticons jbimages"
				],
				toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons",
				relative_urls: false,
				remove_script_host: false
			});

		$("#shoppaypal_frm").validate({
				rules: {
					paypal_id: {
						required: true,
						email: true
					},
				},
				messages: {
					paypal_id: {
						required : mes_required,
						email: valid_email
					},
				}
			});

			$(document).ready(function() {
				$('#shop_desc').keyup(function(e) {
					var text_length = $('#shop_desc').val().length;
					var text_remaining = desc_max - text_length;
					if(text_remaining >= 0)
					{
						$('#shop_desc_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
					}
					else
					{
						 $('#shop_desc').val($('#shop_desc').val().substring(0, desc_max));
					}
				});

				$('#shop_contactinfo').keyup(function(e) {
					var text_length = $('#shop_contactinfo').val().length;
					var text_remaining = contactinfo_max - text_length;
					if(text_remaining >= 0)
					{
						$('#shop_contactinfo_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
					}
					else
					{
						 $('#shop_contactinfo').val($('#shop_contactinfo').val().substring(0, contactinfo_max));
					}
				});

				$('#shop_name').focusout(function() {
					if ($('#url_slug').val() == '') {
						var tmp_str = $('#shop_name').val().replace(/\s/g,'-'); // to replace spaces with hypens
						tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
						tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
						tmp_str = alltrimhyphen(tmp_str);
						$('#url_slug').val(tmp_str);
					}
				});
				$('#url_slug').focusout(function() {
					if ($('#url_slug').val() != '') {
						var tmp_str = $('#url_slug').val().replace(/\s/g,'-'); // to replace spaces with hypens
						tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
						tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
						tmp_str = alltrimhyphen(tmp_str);
						$('#url_slug').val(tmp_str);
					}
				});
				function alltrimhyphen(str) {
					return str.replace(/^\-+|\-+$/g, '');
				}
			});

			$("#shoppolicy_frm").validate({
				rules: {
					shop_name: {
						required: true,
						minlength: shopname_min_length,
						maxlength: shopname_max_length
					},
					url_slug: {
						required: true
					},
					shop_slogan: {
						minlength: shopslogan_min_length,
						maxlength: shopslogan_max_length
					},
					shop_desc: {
						minlength: fieldlength_shop_description_min,
						maxlength: fieldlength_shop_description_max
					},
					shop_status: {
						required: true
					},
					shop_contactinfo: {
						minlength: fieldlength_shop_contactinfo_min,
						maxlength: fieldlength_shop_contactinfo_max
					}
				},
				messages: {
					shop_name: {
						required : mes_required,
						minlength: jQuery.format(shopname_min_length),
						maxlength: jQuery.format(shopname_max_length)
					},
					url_slug: {
						required : mes_required,
					},
					shop_slogan: {
						minlength: jQuery.format(shopslogan_min_length),
						maxlength: jQuery.format(shopslogan_max_length),
					},
					shop_status: {
						required : mes_required,
					}
				}
			});
	}
	// Shop Policies page script ends


	// Product List page script Starts
	else if(page_name == 'product_list')
	{
		 $('.fn_clsDropSearch').click(function() {
				$('#search_holder').slideToggle(500);
				// toggle open/close symbol
				var span_elm = $('.fn_clsDropSearch i');
				if(span_elm.hasClass('fa fa-caret-up')) {
					$('.fn_clsDropSearch').html(show_search_filters+'<i class="fa fa-caret-down margin-left-5"></i>');
				} else {
					$('.fn_clsDropSearch').html(hide_search_filters+'<i class="fa fa-caret-up margin-left-5"></i>');
				}
				return false;
			});

			function doActionProduct(p_id, selected_action)
			{
				if(selected_action == 'delete')
				{
					$('#dialog-product-confirm-content').html(product_confirm_delete);
				}
				else if(selected_action == 'feature')
				{
					$('#dialog-product-confirm-content').html(product_confirm_featured);
				}
				else if(selected_action == 'unfeature')
				{
					$('#dialog-product-confirm-content').html(product_confirm_unfeatured);
				}
				$("#dialog-product-confirm").dialog({ title: my_products_title, modal: true,
					buttons:[{ text: common_yes_label, click: function() {
								$(this).dialog("close");
								$('#product_action').val(selected_action);
								$('#p_id').val(p_id);
								document.getElementById("productsActionfrm").submit();
								$(this).dialog("close");
								}
							},{
								text: common_no_label,
									click: function() {
									$(this).dialog("close");
								}
							}
							]
					});
					return false;
				}

			$(".fn_changeStatus").fancybox({
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

			$(".view-stock-sales").fancybox({
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
	// Product List page script ends

	// View Product page script Starts
	else if(page_name == "view_product")
	{
		function openShippingCompanyPopup() {
			var shipping_country_id = $('#shipping_country_id').val();
			var shipping_company_id = $('#shipping_company_id').val();
			var quantity = $('#shipping_quantity').val();
			var matrix_id = $('#matrix_id').val();
			var product_id = $('#product_id').val();
			var postData = 'ship_template_id=' + ship_template_id + '&shipping_country_id=' + shipping_country_id + '&shipping_company_id=' + shipping_company_id + '&quantity=' + quantity + '&product_id=' + product_id + '&matrix_id=' +matrix_id,
			fancybox_url = actions_url + '?' + postData;
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
				/*afterClose  : function() {
					 window.location.reload();
				}*/
			});
		};

			function updateShippingCompanyValues(data, quantity) {
				quantity = (quantity > 0) ? quantity : 1;
				$('#shipping_country_company_info').html(data.shipping_country +' via '+ data.shipping_company);
				$('#shipping_country_id').val(data.shipping_country_id);
				$('#shipping_company_id').val(data.shipping_company_id);
				$('#hidden_arry_details_store').val(data.matrix_details_arr);
				if(data.shipping_company_err_msg == '') {
					if(data.shipping_fee == '0')
						$('#shipping_price').html('<strong class="badge badge-primary">Free </strong><span class="value_check hidden">1</span>');
					else
						$('#shipping_price').html('<strong>'+data.shipping_fee_formated+'</strong><span class="value_check hidden">1</span>');
				}
				else {
					$('#shipping_price').html('<strong class="text-danger">'+data.shipping_company_err_msg+'</strong><span class="value_check hidden">0</span>');
				}

				/*if($("#finalprice").length > 0)
					$("#finalprice").html(data.product_price_formated);
				if($("#finalprice_org").length > 0)
					$("#finalprice_org").html(data.org_price_formated);

				var price = shipping_price = 0;
				if($("#finalprice strong").length > 0)
					price = parseFloat($("#finalprice strong").html().split('$')[1]);
				if($("#shipping_price strong").length > 0)
					shipping_price = parseFloat($("#shipping_price strong strong").html());

				if(isNaN(price)){	price = 0;	}
				if(isNaN(shipping_price)){	shipping_price = 0;	}
				var shipping_total = parseFloat(price * quantity) + parseFloat(shipping_price);
				$("#shipping_total > strong").html(shipping_total.toFixed(2));*/

				$("#shipping_total").html(data.toal_with_shipping_formatted)

				$.fancybox.close();
			}

			if(!preview_mode) {

				$(".fn_ChangeStatus").fancybox({
					maxWidth    : 772,
					maxHeight   : 432,
					fitToView   : false,
					width       : '70%',
					height      : '432',
					autoSize    : true,
					closeClick  : true,
					type        : 'iframe',
					openEffect  : 'none',
					closeEffect : 'none'
				});
			}

				$(document).ready(function() {
					$('#hidden_arry_details_store').val('');
					$("input.rating").rating({
						starCaptions: function(val) {
							//if (val < 5) {
								return val;
							//} else {
							//    return 'high';
							//}
						},
						starCaptionClasses: function(val) {
							if (val < 3) {
								return 'label label-danger';
							} else {
								return 'label label-success';
							}
						},
						hoverEnabled: false,
						hoverOnClear: false,
						readonly: true,
						showClear: false,
						showCaption: false,
					});


					$(".fn_fancybox").fancybox({
						openEffect	: 'none',
						closeEffect	: 'none'
					});

					/*$("#shipping_quantity").change(function() {
						$("#qty").val($(this).val());
					});*/

					$("#shipping_quantity").live("keypress", function (e) {

						var specialKeysPrice = new Array();
						specialKeysPrice.push(8); //Backspace
						specialKeysPrice.push(9); //tab
						specialKeysPrice.push(13); //Enter
						var keyCode = e.which ? e.which : e.keyCode
						var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
						var limit = 4;
						if (keyCode == 8 || e.keyCode == 46) limit = 5;
						ret = (ret && this.value.length < limit);
						return ret;
					});

					$("#shipping_quantity").live("keyup", function (e) {
						var quantity = $(this).val();
						if (quantity == '' || quantity <= 0) {
							$(this).val(1);
							$("#shipping_quantity").change();
						}
						if (quantity > 1000) {
							$(this).val(1000);
							$("#shipping_quantity").change();
						}
					});

					$("#shipping_quantity").change(function() {
						var quantity = $(this).val();

						if (quantity == '' || quantity <= 0) {
							$(this).val(1);
						}

						$("#qty").val($(this).val());

						var product_id = $('#product_id').val();
						var shipping_country_id = $('#shipping_country_id').val();
						var shipping_company_id = $('#shipping_company_id').val();
						if($("#matrix_id").length > 0)
							var matrix_id = $('#matrix_id').val();
						else
							var matrix_id = 0;
						if(shipping_company_id == 0)
							return false;
						var quantity = $(this).val();
						postData = 'ship_template_id=' + ship_template_id + '&shipping_country=' + shipping_country_id + '&shipping_company=' + shipping_company_id + '&quantity=' + quantity + '&matrix_id=' + matrix_id+ '&product_id=' + product_id,
						displayLoadingImage(true);
						$.post(actions_url_country, postData,  function(response)
						{
							hideLoadingImage (false);
							data = eval( '(' +  response + ')');

							if(data.result == 'success')
							{
								updateShippingCompanyValues(data, quantity);
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data.error_msg});
							}
						});
					});
				});

				$(".fn_fancyboxview").fancybox({
					beforeShow: function() {
						$(".fancybox-wrap").addClass('view-proprevw');
					},
					maxWidth    : 772,
					maxHeight   : 432,
					fitToView   : false,
					autoSize    : true,
					closeClick  : true,
					openEffect  : 'none',
					closeEffect : 'none'
				});


			$('.carousel').carousel({
			  interval: 0
			});
			var like_ajax = 0;
			$(document).ready(function() {
				$(".product_tabs li:first").addClass('active');
				$(".tab-content div:first").addClass('active');
				//$( ".nav-tabs" ).tabs({ active: 1 });
				$(".js_showAddCartBtn").hover(function() {
					$(this).children('#addCartButton').slideToggle('fast');
				});
			});

				//$('#support').removeClass('active');
				//$('#demo').removeClass('active');
				/*$('ul.support_demo li').click(function(e){
					$('.support_demo li').removeClass('active');
					$(this).addClass('active');

					$('.support_demo li').attr('href')
					$(".tab-content div").addClass('active');
				});*/
				$('#myTab a').click(function (e) {
					e.preventDefault();
					$(this).tab('show');
				});
				$('#myTab a[href="#profile"]').tab('show');

				$('#myTab1 a').click(function (e) {
					e.preventDefault();
					$(this).tab('show');
				});
				$('#myTab1 a[href="#profile"]').tab('show');

			$('.js-service-checkbox').click(function() {
				var final_price = $('#orgamount').val();
				final_price = parseFloat(final_price);
				var services_price = 0;
				var service_ids = [];
				$.each($("input[name='productservices[]']:checked"), function() {
					service_ids.push($(this).val());
					services_price = services_price + $(this).data('price');
				});
				service_ids.join(',');
				var subtotal_price = final_price+services_price;
				$('#subtotal_price').html(subtotal_price);
				$('#product_services').val(service_ids);
			});

			$('.js-submit-report').click(function(){

				if($(".js-report-checkbox:checkbox:checked").length <= 0)
				{
					$('#report_message_div').html('<div class="alert alert-danger">'+select_atleast_one_thread+'</div>');
					return false;
				}

				//postData = 'favorites=product&user_id=' + user_id + '&product_id=' + $(this).data('productid'),
				postData = $('#frmReportItem').serialize();
				displayLoadingImage(true);
				$.post(report_item_url, postData,  function(response)
				{
					hideLoadingImage (false);
					data = eval( '(' +  response + ')');
					if(data.result == 'success')
					{
						$('#report_message_div').html('<div class="alert alert-success">'+data.message+'</div>');
					}
					else
					{
						$('#report_message_div').html('<div class="alert alert-danger">'+data.message+'</div>');
					}
				}).error(function() {
					hideLoadingImage (false);
					$('#report_message_div').html('<div class="alert alert-danger">'+some_problem_try_later+'</div>');
				});
			});

			$('.fn_clsDescMore').click(function() {
				$(this).parent().hide();
				$(this).parent().next().show();
			});
			$('.fn_clsDescLess').click(function() {
				$(this).parent().hide();
				$(this).parent().prev().show();
			});

			function showErrorDialog(err_data)
			{
				var err_msg ='<div class="alert alert-danger">'+err_data.error_message+'</div>';
				$('#error_msg_div').html(err_msg);
			}
			function showSuccessDialog(err_data)
			{
				var err_msg ='<div class="alert alert-success">'+err_data.success_message+'</div>';
				$('#error_msg_div').html(err_msg);
			}

			function removeErrorDialog()
			{
				$('#error_msg_div').html('');
			}

			$(document).ready(function() {
				$(".fn_signuppop").fancybox({
					maxWidth    : 800,
					maxHeight   : 630,
					fitToView   : false,
					width       : '70%',
					height      : '430',
					autoSize    : false,
					closeClick  : false,
					type        : 'iframe',
					openEffect  : 'none',
					closeEffect : 'none'
				});

				$( ".js-addto-favorite").click(function() {
					$this = $(this);
					if($(this).data('productid')!='')
					{
						postData = 'favorites=product&user_id=' + user_id + '&product_id=' + $(this).data('productid'),
						displayLoadingImage(true);
						$.post(favorite_product_url, postData,  function(response)
						{
							hideLoadingImage (false);

							data = eval( '(' +  response + ')');
							var fav_count = parseInt($('#js-fav-count').html());
							if(data.result == 'success')
							{
								removeErrorDialog();
								var act = data.action_to_show;
								var text_to_disp = '';
								var favorite_text_msg = '';
								if(act == "remove")
								{
									fav_count = fav_count + 1;
									text_to_disp = '<i class="fa fa-heart text-pink"></i>'+unfavorite;
									favorite_text_msg = '<strong>'+favorited_item+'</strong>'+revisit_from_favorite;
								}else
								{
									fav_count = fav_count - 1;
									text_to_disp = '<i class="fa fa-heart text-muted"></i>'+favorite;
									favorite_text_msg = '<strong>'+like_this_item+'</strong>'+add_to_favorite_and_revisit;
								}
								//$this.html(text_to_disp);
								$('.js-addto-favorite').html(text_to_disp);
								$('#js-favorite-msg').html(favorite_text_msg);
								$('#js-fav-count').html(fav_count);
								showSuccessDialog({status: 'success', success_message: data.success_msg});
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data.error_msg});
							}
						}).error(function() {
							hideLoadingImage (false);
							showErrorDialog({status: 'error', error_message: some_problem_try_later});
						});
					}
				});

				$( ".js-addto-favorite-shop").click(function() {

					$this = $(this);
					shop_id = $(this).data('shopid');
					shop_user_id = $(this).data('userid');

					if(typeof shop_id != 'undefined' && shop_id!='' && typeof shop_user_id != 'undefined' && shop_user_id!='')
					{
						postData = 'favorites=shop&shop_user_id=' + shop_user_id + '&shop_id=' + shop_id + '&user_id=' + user_id,
						displayLoadingImage(true);
						$.post(favorite_product_url, postData,  function(response)
						{
							hideLoadingImage (false);

							data = eval( '(' +  response + ')');

							if(data.result == 'success')
							{
								removeErrorDialog();
								var act = data.action_to_show;
								var text_to_disp = '';
								var favorite_text_msg = '';
								if(act == "remove")
								{
									text_to_disp = '<i class="fa fa-heart text-pink"></i>'+unfavorite_shop;
									//favorite_text_msg = '<strong>Favorited item!</strong> You can revisit it from your favorites.';
								}else
								{
									text_to_disp = '<i class="fa fa-heart text-muted"></i>'+favorite_shop;
									//favorite_text_msg = '<strong>Like this item?</strong> Add it to your favorites to revisit it later.';
								}
								$this.html(text_to_disp);
								//$('#js-favorite-msg').html(favorite_text_msg);
								showSuccessDialog({status: 'success', success_message: data.success_msg});
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data.error_msg});
							}
						}).error(function() {
							hideLoadingImage (false);
							showErrorDialog({status: 'error', error_message: some_problem_try_later});
						});
					}
				});
			});

			$(document).ready(function() {
				$( ".js-addto-favorite-heart").click(function() {
					$this = $(this);
					if($(this).data('productid')!='')
					{
						postData = 'favorites=product&user_id=' + user_id + '&product_id=' + $(this).data('productid'),
						displayLoadingImage(true);
						$.post(favorite_product_url, postData,  function(response)
						{
							hideLoadingImage (false);

							data = eval( '(' +  response + ')');
							if(data.result == 'success')
							{
								removeErrorDialog();
								var act = data.action_to_show;
								var text_to_disp = '';
								var favorite_text_msg = '';
								if(act == "remove")
								{
									text_to_disp = '<i class="fa fa-heart text-pink"></i>';
								}else
								{
									text_to_disp = '<i class="fa fa-heart text-muted"></i>';
								}
								$this.html(text_to_disp);
								//showSuccessDialog({status: 'success', success_message: data.success_msg});
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data.error_msg});
							}
						}).error(function() {
							hideLoadingImage (false);
							showErrorDialog({status: 'error', error_message: some_problem_try_later});
						});
					}
				});

				$( ".js-show-list").click(function() {
					$this = $(this);
					if($(this).data('productid')!='') {
						var product_id = $(this).data('productid');
						var block = $(this).data('block');
						//alert($('#'+block+'_holder_'+product_id).css('display'));
						if($('#'+block+'_holder_'+product_id).css('display') == 'none')
						{
							//$('#'+block+'_holder_main_'+product_id).css('display', 'block');
							$('#'+block+'_holder_'+product_id).show();
							$('#'+block+'_holder_'+product_id).html('<img src="'+loading_image_url+'" alt="loading" />');
							postData = 'user_id=' + user_id + '&product_id=' + product_id + '&block=' + block,
							//displayLoadingImage(true);
							$.post(favorite_product_list_url, postData,  function(response)
							{
								//hideLoadingImage (false);

								data_arr = response.split('|~~|');

								if(data_arr[0] == 'success')
								{
									//removeErrorDialog();
									$('#'+block+'_holder_'+product_id).html(data_arr[1]);
								}
								else
								{
									showErrorDialog({status: 'error', error_message: data_arr[1]});
								}
							}).error(function() {
								//hideLoadingImage (false);
								showErrorDialog({status: 'error', error_message: some_problem_try_later});
							});
						}
						else {
							$('#'+block+'_holder_'+product_id).html("").hide();
						}
					}
				});

				$(document).mouseup(function (e) {
					var container = $(".fnFavListHolder");
					if (!container.is(e.target) // if the target of the click isn't the container...
						&& container.has(e.target).length === 0) {
						container.hide();
					}
				});
			});

			$(".fn_VariationDescToolTip").click(function(){
				$(this).next('.fn_helpText').toggle();
			});

			var getDetails = function()
			{
				if(allow_variation == 0)
					return false;

				var checkBoxes = $('div#variationGroup [name="item_variations"]');
				var tempArr = [];
				checkBoxes.each ( function () {
					tempArr.push($(this).val());
				});
				retArr = sort_unique(tempArr);
				var selAttr = retArr.join(",");
				var hidden_arry_details_store = '';
				if($("#hidden_arry_details_store").length)
					var hiddenvalues = document.getElementById("hidden_arry_details_store").value;
				if(hiddenvalues == ''){
					var matrixData = JSON.parse(matrix_details_arr);
				}else{
					var matrixData = JSON.parse(hiddenvalues);
				}
				$.each(matrixData, function (i, val) {
			//	alert("Fetching attib="+selAttr);		alert("current attrib="+val.attrib_id);
					if (val.attrib_id == selAttr)
					{
						var largeimgsrc = val.large_img_src;
						var itemPrice = val.price;
						var itemNetPrice = val.net_price;
						var variation_shipping_fee = val.variation_shipping_fee;

						var matrix_id = val.matrix_id;
						//var itemNetPrice = val.net_price;
						var priceLbl = currSymbol +''+ itemPrice+'<span>'+currCode+'</span>';
						var upriceLbl = val.item_price_ucurrency_symbol +''+ val.item_price_ucurrency_amount+'<span>'+val.item_price_ucurrency_amount_label+'</span>';
						$('#itemSalePrice').html(priceLbl);
						$('#itemucurrencyPrice').html(upriceLbl);
						// Handle deal block
						if($(".deals-itemblk").length > 0)
						{
							if($("#finalprice_org").length > 0 && val.variation_org_price != "")
								$("#finalprice_org").html(val.variation_org_price);

							if($("#finalprice").length > 0)
								$("#finalprice").html('<span>'+deal_price_lbl+'</span> '+itemPrice);
								$("#matrix_id_val").val(val.matrix_id);
						}
						else
						{
							if($("#finalprice").length > 0)
								$("#finalprice").html(itemPrice);
								$("#matrix_id_val").val(matrix_id);
						}
						if($.trim(val.variation_price_default) == '0' || $.trim(val.variation_price_default) == '')
							$('#shipping_price').html('<strong class="badge badge-primary">Free </strong><span class="value_check hidden">1</span>');
						else{
							if($('.value_check').html() == '1')
								$('#shipping_price').html('<strong>'+val.variation_shipping_fee+'</strong><span class="value_check hidden">1</span>');
						}
						//$('#shipping_price').html(variation_shipping_fee);
						if($("#shipping_total").length > 0)
							$("#shipping_total").html(itemNetPrice);
						//$('#shipping_quantity').trigger('change');
						$('#shipping_quantity').val("1");
						if(has_discount > 0)
						{
							var strikePriceLbl = currSymbol +''+ val.before_discount+'<span>'+currCode+'</span>';
							var ustrikePriceLbl = val.discount_price_ucurrency_symbol +''+ val.discount_price_ucurrency_amount+'<span>'+val.discount_price_ucurrency_amount_label+'</span>';
							$('#itemStrikePrice').html(strikePriceLbl);
							$('#itemStrikeuPrice').html(ustrikePriceLbl);
						}

						var stock = val.stock;
						$('.stock_value').hide();
						$('#addCartButton').show();
						if(stock == 0)
						{
							$('#itemStockAvail').html(viewitem_no_stock_msg);
							$('#varStock').html(viewitem_no_stock_msg).removeClass("alert-info").addClass("alert-danger");
							if(show_add_cart_button == 1){
								$('#backordtxt').show();
								$('#addCartButton').hide();
								$('.stock_value').show();
							}
						}
						else
						{
							var stockLbl = stockAvailLbl+'<strong> '+stock+'</strong>';
							$('#itemStockAvail').html(stockLbl);
							$('#varStock').html(stockLbl).addClass("alert-info").removeClass("alert-danger");
							$('#backordtxt').hide();
						}
						$('#var_stock').val(stock);
						$('#var_preview_img_src').val(largeimgsrc);
						$('#var_preview_img_dim').val(val.large_img_dim);
						$('#var_org_img_src').val(val.full_img_src);
						$('#var_thumb_img_src').val(val.thumb_img_src);
						$('#matrix_id').val(val.matrix_id);
						$('#var_img_title').val(val.title);
						//$('#preview').trigger('click');
						swapPreviewImagefn(1)
						return false;
					}
				});
			}

			function sort_unique(arr) {
				arr = arr.sort(function (a, b) { return a*1 - b*1; });
				var ret = [arr[0]];
				for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
					if (arr[i-1] !== arr[i]) {
						ret.push(arr[i]);
					}
				}
				return ret;
			}

			$('#preview').click(function(){
				swapPreviewImagefn();
			});
			function hideAnimateBlock(elmt){
				//fade, slide, glide, wipe, unfurl, grow, shrink, highlight
				$("#"+elmt).fadeOut(5000);
			};

			var swapPreviewImagefn = function()
			{
				var show_error_message = true;
				if(arguments.length > 0)
					show_error_message = false;
				var imgDimDet = $('#var_preview_img_dim').val();
				var imgSrc = $('#var_preview_img_src').val();
				var thumbImgSrc = $('#var_thumb_img_src').val();
				var orgImgSrc = $('#var_org_img_src').val();
				var imgTitle = $('#var_img_title').val();
				// if image already available the load it otherwise so the error message.
				$('.carousel-inner').each(function(){
					$('.item').removeClass('active');
				});
				$('.fn_main-image').addClass('active');
				if(imgDimDet != "")
				{
					data = imgDimDet.split('x');
					//$(".fn_main-image img").attr({ src: imgSrc, width: data[0], height:data[1], title: imgTitle });
					$(".fn_main-image img").attr({ src: imgSrc, title: imgTitle });
					$(".fn_slide-image img").attr({ src: thumbImgSrc });
				//	$('#enlargeImangeLink').attr('href', orgImgSrc);
				//	$('#enlargeImangeLink').attr('title', imgTitle);
				//	$('#enlargeImangeBlock').show();
				}
				else
				{
					$(".fn_main-image img").attr({ src: default_img, title: imgTitle });
					$(".fn_slide-image img").attr({ src: default_img_img });
					if(show_error_message)
					{
						$('#swapImgErrMsg').html(no_swap_img_msg);
						$('#swapImgErrMsg').parent('div').show();
						if($('#selSwapImgErrMsg'))
							hideAnimateBlock('selSwapImgErrMsg');
					}
				//	$('#enlargeImangeBlock').hide();
				}
			};

			$(document).ready(function(){
				getDetails();
			});
	}
// View Product page script ends

	// Taxations List page script starts
	else if(page_name == "taxations_list")
	{
		$('.fn_clsDropSearch').click(function() {
				$('#search_holder').slideToggle(500);
				// toggle open/close symbol
				var span_elm = $('.fn_clsDropSearch i');
				if(span_elm.hasClass('fa fa-caret-up')) {
					$('.fn_clsDropSearch').html(show_search_filters+'<i class="fa fa-caret-down ml5"></i>');
				} else {
					$('.fn_clsDropSearch').html(hide_search_filters+'<i class="fa fa-caret-up ml5"></i>');
				}
				return false;
			});

			function doActiondelete(taxation_id, selected_action)
			{
				if(selected_action == 'delete')
				{
					$('#dialog-product-confirm-content').html(confirm_delete);
				}
				$("#dialog-product-confirm").dialog({ title: taxations_list, modal: true,
					buttons: [{ text: common_yes_label, click: function() {
								$(this).dialog("close");
								$('#product_action').val(selected_action);
								$('#taxation_id').val(taxation_id);
								document.getElementById("productsActionfrm").submit();
								$(this).dialog("close");
								}
							},{
								text: common_no_label,
									click: function() {
									$(this).dialog("close");
								}
							}
							]
					});
				return false;
			}

			$(".fn_changeStatus").fancybox({
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
	// Taxations List page script ends

	// My Sales List page script starts
	else if(page_name == "my_sales_list")
	{
		$('.fn_clsDropSearch').click(function() {
				$('#search_holder').slideToggle(500);
				// toggle open/close symbol
				var span_elm = $('.fn_clsDropSearch i');
				if(span_elm.hasClass('fa fa-caret-up')) {
					$('.fn_clsDropSearch').html(show_search_filters+'<i class="fa fa-caret-down"></i>');
				} else {
					$('.fn_clsDropSearch').html(hide_search_filters+'<i class="fa fa-caret-up"></i>');
				}
				return false;
			});

			$(function() {
				$('#from_date').datepicker({
					format: 'yyyy-mm-dd',
					autoclose: true,
					todayHighlight: true
				});
				$('#to_date').datepicker({
					format: 'yyyy-mm-dd',
					autoclose: true,
					todayHighlight: true
				});
			});

			function doAction(p_id, selected_action)
			{
				if(selected_action == 'delete')
				{
					$('#dialog-product-confirm-content').html(product_confirm_delete);
				}
				else if(selected_action == 'feature')
				{
					$('#dialog-product-confirm-content').html(product_confirm_featured);
				}
				else if(selected_action == 'unfeature')
				{
					$('#dialog-product-confirm-content').html(product_confirm_unfeatured);
				}
				$("#dialog-product-confirm").dialog({ title: my_products_title, modal: true,
					buttons: [{ text: common_yes_label, click: function() {
								$(this).dialog("close");
								$('#product_action').val(selected_action);
								$('#p_id').val(p_id);
								document.getElementById("productsActionfrm").submit();
								$(this).dialog("close");
							}
							},{
								text: common_no_label,
									click: function() {
									$(this).dialog("close");
								}
							}
							]
					});
					return false;
				}

			function openViewShippingPopup(order_id) {
				var postData = 'order_id='+order_id;
				fancybox_url = actions_url_popup + '?' + postData;
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
					/*afterClose  : function() {
						 window.location.reload();
					}*/
				});
			};


			$(".fn_signuppop").fancybox({
				maxWidth    : 800,
				maxHeight   : 630,
				fitToView   : false,
				width       : '70%',
				height      : '430',
				autoSize    : false,
				closeClick  : false,
				type        : 'iframe',
				openEffect  : 'none',
				closeEffect : 'none'
			});


			$(".fn_changeStatus").fancybox({
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

			$(window).load(function(){
				  $(".fn_dialog_confirm").click(function(){
						var atag_href = $(this).attr("href");
						var action = $(this).attr("action");
						var cmsg = "";
						//alert(action); return false;
						switch(action){
							case "Delivered":
								cmsg = product_confirm_delivered;
								break;
						}
						bootbox.dialog({
							message: cmsg,
							title: cfg_site_name,
							buttons: {
								danger: {
									label: common_yes_label,
									className: "btn-danger",
									callback: function() {
										Redirect2URL(atag_href);
										bootbox.hideAll();
									}
								},
								success: {
									label: common_no_label,
									className: "btn-default",
								}
							}
						});
						return false;
					});
				});
	}
	// My Sales List page script ends

	// sales order details page script starts
	else if(page_name == "sales_order_details")
	{
		var ajax_proceed = 0;
		   $('.fn_clsDropSearch').click(function() {
				$('#search_holder').slideToggle(500);
				// toggle open/close symbol
				var span_elm = $('.fn_clsDropSearch i');
				if(span_elm.hasClass('fa fa-caret-up')) {
					$('.fn_clsDropSearch').html(show_search_filters+'<i class="fa fa-caret-down"></i>');
				} else {
					$('.fn_clsDropSearch').html(hide_search_filters+'<i class="fa fa-caret-up"></i>');
				}
				return false;
			});

			function doAction(p_id, selected_action)
			{
				if(selected_action == 'delete')
				{
					$('#dialog-product-confirm-content').html(product_confirm_delete);
				}
				else if(selected_action == 'feature')
				{
					$('#dialog-product-confirm-content').html(product_confirm_featured);
				}
				else if(selected_action == 'unfeature')
				{
					$('#dialog-product-confirm-content').html(product_confirm_unfeatured);
				}
				$("#dialog-product-confirm").dialog({ title: my_products_title, modal: true,
					buttons:[{ text: common_yes_label, click: function() {
								$(this).dialog("close");
								$('#product_action').val(selected_action);
								$('#p_id').val(p_id);
								document.getElementById("productsActionfrm").submit();
							}
							},{
								text: common_no_label,
									click: function() {
									$(this).dialog("close");
								}
							}
							]
					});
				return false;
			}

			$(".js-refund-form").click(function(){
				var invoice_id = $(this).data('invoice');
				var div_id = 'refundreasondiv_'+invoice_id;
				$('#'+div_id).toggle(500);
			});


			$(".js-response-refund").click(function(){
				var invoice_id = $(this).data('invoice');
				var refund_action = $('#refund_action_'+invoice_id).val();
				var item_id = $('#item_id_'+invoice_id).val();
				var refund_amount = $('#seller_refund_amount_'+invoice_id).val();
				var refund_response = $('#refund_response_'+invoice_id).val();
				var div_id = 'error_'+invoice_id;

				if(ajax_proceed)
					ajax_proceed.abort();

				var params = {"invoice_id": invoice_id, "refund_action":refund_action, "refund_response": refund_response, "refund_amount": refund_amount, "item_id": item_id };
				ajax_proceed = $.post(response_cancel_url, params, function(data) {
					if(data) {
						var data_arr = data.split("|~~|");
						if(data_arr.length > 1) {
							if($.trim(data_arr[0]) == "success") {
								$('#'+div_id).removeClass('hide').html('<span class="success">'+data_arr[1]+'</span>');
								window.location.reload();
							}
							else if($.trim(data_arr[0]) == "error")
							{
								$('#'+div_id).removeClass('hide').html('<span class="error">'+data_arr[1]+'</span>');
							}
							else
							{
								window.location.reload();
							}
						}
						else {
							window.location.reload();
						}
					}
				})

			});


			$(window).load(function(){
				  $(".fn_dialog_confirm").click(function(){
						var atag_href = $(this).attr("href");
						var action = $(this).attr("action");
						var cmsg = "";
						//alert(action); return false;
						switch(action){
							case "Delivered":
								cmsg = product_confirm_delivered;
								break;
						}
						bootbox.dialog({
							message: cmsg,
							title: cfg_site_name,
							buttons: {
								danger: {
									label: "Ok",
									className: "btn-danger",
									callback: function() {
										Redirect2URL(atag_href);
										bootbox.hideAll();
									}
								},
								success: {
									label: "Cancel",
									className: "btn-default",
								}
							}
						});
						return false;
					});
				});

			function openViewShippingPopup(order_id) {
					var actions_url = shipping_popup_url;
					var postData = 'order_id='+order_id;
					fancybox_url = actions_url + '?' + postData;
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
						/*afterClose  : function() {
							 window.location.reload();
						}*/
					});
				};

			$(".fn_signuppop").fancybox({
				maxWidth    : 800,
				maxHeight   : 630,
				fitToView   : false,
				width       : '70%',
				height      : '430',
				autoSize    : false,
				closeClick  : false,
				type        : 'iframe',
				openEffect  : 'none',
				closeEffect : 'none'
			});


			$(".fn_changeStatus").fancybox({
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
	// sales order details page script ends

	// Shipping Templates List page script starts
	else if(page_name == "shipping_templates_list")
	{
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					//alert(action); return false;
					switch(action){
						case "Delete":
							cmsg = confirm_delete_shipping_template;

							break;
						case "Default":
							cmsg = confirm_set_default_shipping_template;
							break;
					}
					bootbox.dialog({
						message: cmsg,
						title: cfg_site_name,
						buttons: {
							danger: {
								label: ok_label,
								className: "btn-danger",
								callback: function() {
									Redirect2URL(atag_href);
									bootbox.hideAll();
								}
							},
							success: {
								label: cancel_label,
								className: "btn-default",
							}
						}
					});
					return false;
				});
			});

			/*
			 $(".defult_action").click(function(ele){
			 var page = "{{ Input::get('page') }}";
			 var post_action_url = "{{ URL::to('shipping-template/index/set-as-default-action') }}";
				var id = this.id;
				alert(id);
				var post_data = 'id='+id;
				$.ajax({
					type: 'GET',
					url: post_action_url,
					data: post_data,
					success: function(data){
						if(data)
						{
							//window.location = "{{ URL::to('shipping-template/index').'?page='}}"+page;
						}
					}
				});
			 });
			*/
	}
	// Shipping Templates List page script ends

	// My Withdrawals page script starts
	else if(page_name == "my_withdrawals")
	{
		$("#withdrawalReqfrm").validate({
				rules: {
					withdraw_amount: {
						required: true
					},
					withdraw_currency: {
						required: true
					},
					pay_to_details:{
						required: true
					}
				},
				messages: {
					withdraw_amount:{
						required: mes_required
					},
					withdraw_currency:{
						required: mes_required
					},
					pay_to_details:{
						required: mes_required
					}
				}
			});

			$(".fn_quoteHistoryPop").fancybox({
				maxWidth    : 772,
				maxHeight   : 432,
				title		: '',
				fitToView   : false,
				width       : '70%',
				height      : '432',
				autoSize    : false,
				closeClick  : false,
				type        : 'iframe',
				openEffect  : 'none',
				closeEffect : 'none'
			});
	}
	// My Withdrawals page script ends

	// My Withdrawals page script starts
	else if(page_name == "manage_withdrawal")
	{
		if(allow_withdrawal){
			$(document).ready(function() {
				$("#withdrawalReqfrm").validate({
					rules: {
							request_amount: {
								required: true
							},
							pay_to_details: {
								required: true
							}
						},
					messages: {
						request_amount: {
							required: mes_required
						},
						pay_to_details: {
							required: mes_required
						}
					}
				});

				function formatCurrency(amount) {
					var i = parseFloat(amount);
					if(isNaN(i)) { i = 0.00; }
					var minus = '';
					if(i < 0) { minus = '-'; }
					i = Math.abs(i);
					i = parseInt((i + .005) * 100);
					i = i / 100;
					s = new String(i);
					if(s.indexOf('.') < 0) { s += '.00'; }
					if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
					s = minus + s;
					return s;
				}

				var fee = "";
				var fee_amt = "";
				function setMinimumAmount()
				{
					var withdraw_currency = $("#withdraw_currency").val()
					var currency_symbol = "$"
					var fee_usd = "{{ $d_arr['withdraw_fee'] }}";

					if(withdraw_currency == "INR"){
						currency_symbol = '<span class="clsWebRupe">Rs</span>';
						minimum_amount = minimum_amount_inr;
					}

					if(withdraw_currency == "USD" && fee_usd >0){
						fee = withdraw_currency + ' ' + currency_symbol + formatCurrency(fee_usd);
						fee_amt = fee_usd;
					}
					else if(withdraw_currency == "INR" && fee_usd > 0){

						fee = withdraw_currency + ' ' + currency_symbol + formatCurrency(fee_usd);
						fee_amt = fee_usd;
					}
					else{
						$("#fee_row").hide();
						fee = "";
						fee_amt ="";
					}
					if(fee != ""){
						$("#fee_row").show();
					}

					$("#min_amount").html(minimum_allowed + withdraw_currency + ' ' + currency_symbol + formatCurrency(minimum_amount)+')');
					$("#fee").html(fee);
					$(".currency").html(withdraw_currency + ' ' + currency_symbol);
					calculateBalAmount();
				}

				setMinimumAmount();

				function calculateBalAmount()
				{
					var amount = $("#request_amount").val();
					var balance_amount = 0;
					if(amount != "" && chkIsValidPrice(amount))
					{
						amount = parseFloat(amount);
						balance_amount = amount - fee_amt;
					}
					$("#bal_amt").html(balance_amount.formatMoney(2,'.',','));
				}
				var timeout;
				$('#request_amount').change(function () {
					clearTimeout(timeout);
					timeout = setTimeout(function () {	calculateBalAmount();	}, 500);
				});
			});
		}
	}
	// My Withdrawals page script ends

	// Add Wallet Amount page script starts
	else if(page_name == "add_wallet_amount")
	{
		if(typeof confirm_data_arr !== 'undefined') {
			function proceedPaymentCredits(payment_mode, payment_gateway_chosen)
			{
				var parent_gateway_id = $('#parent_gateway_id').val();
				var response = true;
				//var validate = $("#addressValidatioinFrm").validate({ });

				if(parent_gateway_id == ''){
					parent_gateway_id = 4922;
				}

			//	if($("#addressValidatioinFrm").valid()) {
				var valid = true;
				if(parent_gateway_id != 5333 && parent_gateway_id != 5346 ){
					var valid = $('#ProcessPaymentCreditFrm').valid();
				}

				if(valid == false) {
					return false;
				} else {
					if(parent_gateway_id != 5333 &&  parent_gateway_id != 5346) {
						var response = cardValidation();
					}
					if(response == false) {
						return false;
					} else {
						var gateway_id = 0;
						//if(parent_gateway_id == 4922 || parent_gateway_id == 5333) {
							if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0)
								gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();
						//}
						if(ajax_proceed)
						{
							ajax_proceed.abort();
							displayLoadingImage(false);
						}

						var common_invoice_id = 0;
						var currency_code = payment_mode;

						var d_arr = [];
						sudopay_arr = {};
						sudopay_arr['sudopay_fees_payer'] = $("#sudopay_fees_payer").val();
						if($('#buyer_fees_payer_confirmation_token_'+parent_gateway_id+'_'+gateway_id).length > 0 && $("#sudopay_fees_payer").val() == 'Buyer')
						{
							sudopay_arr['fees_payer_token'] = $('#buyer_fees_payer_confirmation_token_'+parent_gateway_id+'_'+gateway_id).val();
						}						
						sudopay_arr['credit_card_number'] = $("#credit_card_number").val();
						sudopay_arr['credit_card_expire'] = $("#credit_card_expire").val();
						sudopay_arr['credit_card_name_on_card'] = $("#credit_card_name_on_card").val();
						sudopay_arr['credit_card_code'] = $("#credit_card_code").val();
						sudopay_arr['parent_gateway_id'] = parent_gateway_id;
						sudopay_arr['gateway_id'] = gateway_id;
						if(parent_gateway_id == 5346) // For offline / manual payment include user note
							sudopay_arr['payment_note'] = $("#payment_note").val();
						
						sudopay_arr['buyer_address_line1'] = $("#address_line1").val();
						sudopay_arr['buyer_address_line2'] = $("#address_line2").val();
						sudopay_arr['buyer_street'] = $("#street").val();
						sudopay_arr['buyer_city'] = $("#city").val();
						sudopay_arr['buyer_state'] = $("#state").val();
						sudopay_arr['buyer_country_iso'] = $("#country_iso").val();
						sudopay_arr['buyer_zip_code'] = $("#zip_code").val();
						sudopay_arr['buyer_phone_no'] = $("#phone_no").val();
						d_arr.push(JSON.stringify(sudopay_arr));
						console.log(d_arr);

						var params = {"common_invoice_id": common_invoice_id, "payment_gateway_chosen": payment_gateway_chosen, "currency_code": currency_code, "amount": amount, "d_arr[]": d_arr };

						displayLoadingImage(true);
						ajax_proceed = $.post(add_users_credits, params, function(data) {
							if(data) {
								var data_arr = data.split("|~~|");

								if(data_arr.length > 1) {
									window.location.href = data_arr[0];
								}
								else {
									displayLoadingImage(false);
									$("#paypal_form").html(data);
									document.getElementById("frmTransaction").submit();
								}
							}
						});
					}
				}
			}
		}

		$(document).ready(function() {
			$("#addAmountToWalletFrm").validate({
				rules: {
					acc_balance: {
						required: true,
						min: min_amount
					},
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
					},
				},
				messages: {
					acc_balance: {
						required: mes_required,
					},
					address_line1: {
						required: mes_required,
					},
					city: {
						required: mes_required,
					},
					state: {
						required: mes_required,
					},
					country_id: {
						required: mes_required,
					},
					zip_code: {
						required: mes_required,
					},
					phone_no: {
						required: mes_required,
					},
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
			});
		});
	}
	// Add Wallet Amount page script ends


	// Add variation block starts
	else if(page_name == "add_variation")
	{
		$(document).ready(function() {
			$("#add_variations_form").validate({
				rules: {
					name: {
						required: true
					}
				},
				submitHandler: function(form) {
					form.submit();
				}
			});
		});

		$('.ClsBtnAddOption').live('click', function(){
			var clone = $('#variation_options_group').children().last().clone();
			var tabind =  parseInt(clone.find('#option_key').attr('tabindex'));
			clone.find('input:text').val('');
			clone.find('#option_key').attr('tabindex', tabind+30);
			clone.find('#option_label').attr('tabindex', tabind+40);
			clone.find('.ClsBtnAddOption').attr('tabindex', tabind+50);
			clone.find('.ClsBtnRemoveOptions').attr('tabindex', tabind+60);
			$('#variation_options_group').append(clone);
		});

		$('.ClsBtnRemoveOptions').live('click', function() {
			if ($('#variation_options_group').children().size() > 1) {
				$(this).parents("div.clsAddVariationInput").remove();
			}
			else {
				$(this).parents().find('#variation_options_group input[type=text]:first-child').val('');
			}
		});

		$( document ).on( "keypress keyup paste", '.fn_splblk', function(e) {

			var keyCode = e.which ? e.which : e.keyCode;
			var specialKeysPrice = new Array();
			specialKeysPrice.push(8); //Backspace
			specialKeysPrice.push(9); //tab
			specialKeysPrice.push(13); //Enter
			specialKeysPrice.push(46); //decimal

			if((keyCode >= 48 && keyCode <= 57) ||  specialKeysPrice.indexOf(keyCode) != -1)
			{
				return true;
			}
			else if((keyCode < 97 || keyCode > 122) && (keyCode < 65 || keyCode > 90) && (keyCode != 45))
				return false;
		});

	}

	// Add variation block ends

	// Checkout page starts
	else if(page_name == "pay_checkout")
	{
		jQuery(document).ready(function(){
			var d = new Date();
			d = d.getTime();
			if (jQuery('#reloadValue').val().length == 0){
				jQuery('#reloadValue').val(d);
			} else {
				jQuery('#reloadValue').val('');
				location.reload();
			}
		});
		function showTaxDetails(item_id)
		{
			var div_id = 'tax_fee_details_'+item_id;
			$('#'+div_id).toggle('slow');
		}
		if(pay_page){
			function showOtherCurr(selValue, selText)
			{
				if(selValue != "" && selValue !="Select")
				{
					if( selValue in temp_currency_array )
					{
						var disp_txt = temp_currency_array[selValue];
						var resphtml = "<button type=\"button\" value=\"+pay_via_paypal+\" class=\"btn green\" onclick=\"proceedPayment('"+selValue+"');\">"+disp_txt+"</button>";
						$("#otherCurrOpt").html(resphtml).show();
					}
				}
				else
				{
					$("#otherCurrOpt").html("").hide();
				}
			}

			function proceedFreePayment(pay_url)
			{
				$('#common_invoice_id').val(common_invoice_id);
				$('#payment_gateway_chosen').val('dummy');//wallet
				$('#processfreepayment').submit();
			}
		}
	}

	if((page_name == "pay_checkout" && pay_page) || page_name == "add_wallet_amount") {

		function proceedPayment(payment_mode, payment_gateway_chosen, is_credit)
		{
			if (page_name == "add_wallet_amount") {
				return proceedPaymentCredits(payment_mode, payment_gateway_chosen);
			}
			var parent_gateway_id = $('#parent_gateway_id').val();
			var response = true;
			var valid = true;
			var wallet_payment = $('#wallet_payment').val();
			var total_discounted_amount = $('#total_discounted_amount').val();

			if(parent_gateway_id == ''){
				parent_gateway_id = 4922;
			}

			if(parent_gateway_id != 5333 && parent_gateway_id != 5346  && payment_gateway_chosen != 'wallet') {
				var valid = $('#ProcessPaymentCreditFrm').valid();
			}

			if(valid == false) {
				return false;
			} else {
				if(parent_gateway_id == 4922 && payment_gateway_chosen != 'wallet') {
					var response = cardValidation();
				}
				if(response == false) {
					return false;
				} else {
					var gateway_id = 0;
					if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0)
						gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();

					if(ajax_proceed)
					{
						ajax_proceed.abort();
						displayLoadingImage(false);
					}
					var currency_code = payment_mode;
					var d_arr = [];
					sudopay_arr = {};
					sudopay_arr['sudopay_fees_payer'] = $("#sudopay_fees_payer").val();
					
					if($('#buyer_fees_payer_confirmation_token_'+parent_gateway_id+'_'+gateway_id).length > 0 && $("#sudopay_fees_payer").val() == 'Buyer')
					{
						sudopay_arr['fees_payer_token'] = $('#buyer_fees_payer_confirmation_token_'+parent_gateway_id+'_'+gateway_id).val();
					}					
					
					sudopay_arr['credit_card_number'] = $("#credit_card_number").val();
					sudopay_arr['credit_card_expire'] = $("#credit_card_expire").val();
					sudopay_arr['parent_gateway_id'] = parent_gateway_id;
					sudopay_arr['credit_card_name_on_card'] = $("#credit_card_name_on_card").val();
					sudopay_arr['credit_card_code'] = $("#credit_card_code").val();
					if(parent_gateway_id == 5346 && $("#payment_note").length > 0 ) // For offline / manual payment include user note
						sudopay_arr['payment_note'] = $("#payment_note").val();					
					sudopay_arr['gateway_id'] = gateway_id;
					d_arr.push(JSON.stringify(sudopay_arr));
					//console.log(d_arr);

					var params = {"common_invoice_id": common_invoice_id_val, "payment_gateway_chosen": payment_gateway_chosen, "currency_code": currency_code, "is_credit": is_credit, "d_arr[]": d_arr };
					displayLoadingImage(true);
					ajax_proceed = $.post(proceedpayment, params, function(data) {
						if(data) {
							var data_arr = data.split("|~~|");
							if(data_arr.length > 1) {
								window.location.href = data_arr[0];
							}
							else {
								displayLoadingImage(false);
								$("#paypal_form").html(data);
								document.getElementById("frmTransaction").submit();
							}
						}
					});
				}
			}
		}


		$(document).ready(function() {
			$("#ProcessPaymentCreditFrm").validate({
				rules: {
					credit_card_number: {
						required: true
					},
					credit_card_expire: {
						required: true
					},
					credit_card_name_on_card: {
						required: true
					},
					credit_card_code: {
						required: true
					},
				},
				messages: {
					credit_card_number: {
						required: mes_required,
					},
					credit_card_expire: {
						required: mes_required,
					},
					credit_card_name_on_card: {
						required: mes_required,
					},
					credit_card_code: {
						required: mes_required,
					},
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
			});
		});

		//Sudo pay script start
		$(document).ready(function () {
			$("#credit_card_number").attr('maxlength','16');
			$("#credit_card_expire").attr('maxlength','7');
			$("#credit_card_code").attr('maxlength','4');

			$("#credit_card_number").keypress(function (e) {
				if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					$("#card_error").html("Digits Only").show().fadeOut("slow");
					return false;
				}
			});

			$("#credit_card_code").keypress(function (e) {
				if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					$("#card_error").html("Numbers Only").show().fadeOut("slow");
					return false;
				}
			});

			defaultGatewaySetter();
			calcBuyerFeesFormula();
		});

		$("#card_error").hide();


		/**
		 *
		 * @access public
		 * @return void
		 **/
		function defaultGatewaySetter()
		{
			$('.fnGatwayFinder').each(function(){
				if($(this).hasClass("active")) {
					var element_id = $(this).attr("id");
					var data_arr = element_id.split("-");
					if(data_arr.length > 1) {
						$('#parent_gateway_id').val(data_arr[1]);
					}
				}
			});
		}

		function hideCreditCard(id){
			if(id == 5333 || id == 5346){
				$('.js-form-tpl-credit_card').hide();
				$('#parent_gateway_id').val(id);
				if( id == 5346)
				{
					$('.js-form-tpl-manual').removeClass("hide").find('fieldset').prop("disabled", false);	
				}
				else	
				{
					$('.js-form-tpl-manual').addClass("hide").find('fieldset').prop("disabled", true);
				}
			}else{
				$('.js-form-tpl-credit_card').show();
				$('#parent_gateway_id').val(id);
				$('.js-form-tpl-manual').addClass("hide").find('fieldset').prop("disabled", true);
			}			
			calcBuyerFeesFormula();
		}

		function calcBuyerFeesFormula() {
			//4922 = credit card, 5333 = electronics gateways
			var buyer_fees_formula = '';
			var gateway_name = '';
			var gateway_id = 0;
			var parent_gateway_id = $('#parent_gateway_id').val();
			if(parent_gateway_id == '') {
				parent_gateway_id = 4922;
			}
			if(parent_gateway_id == 4922 || parent_gateway_id == 5333) {
				if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0) {
					gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();
				}
			}

			if(gateway_id > 0) {
				if($('#buyer_fees_formula_' + parent_gateway_id + '_' + gateway_id).length > 0) {
					buyer_fees_formula = $('#buyer_fees_formula_' + parent_gateway_id + '_' + gateway_id).val();
				}
				if($('#wallet_gateway_name_disp').length > 0) {
					if($('#sudopay_gateway_' + parent_gateway_id + '_' +gateway_id).length > 0)
						gateway_name = $('#sudopay_gateway_' + parent_gateway_id + '_' +gateway_id).data("gateway-name");
					$('#wallet_gateway_name_disp').html(gateway_name);
				}
			}

			if(buyer_fees_formula != '') {
			    var wallet_payment = $('#wallet_payment').val();
				var amount = eval(discounted_amount_bf_revise).toFixed(2);
				var revised_amount = eval(buyer_fees_formula).toFixed(2);
				var wallet_amount_disp = eval(eval(buyer_fees_formula) - wallet_payment);
				$('#wallet_amount_disp').html(wallet_amount_disp.toFixed(2));
				//var formula = buyer_fees_formula;
				//$('#buyer_fees_fourmula_disp').html(discounted_currency_bf_revise + ' ' + eval(buyer_fees_formula));
				if(amount != revised_amount) {
					var revised_txt = payment_gateway_revised_amount_txt.replace(/VAR_CURRENCY/g, discounted_currency_bf_revise);
					var revised_txt = revised_txt.replace(/VAR_AMOUNT/g, amount);
					var revised_txt = revised_txt.replace(/VAR_REVISED_AMOUNT/g, eval(buyer_fees_formula).toFixed(2));
					$('#buyer_fees_fourmula_disp').text(revised_txt);
					$('#buyer_fees_fourmula_disp').parent('.alert').show();
				}
				else {
					$('#buyer_fees_fourmula_disp').parent('.alert').hide();
				}
			}
		}
		//Sudo pay script end

	}
	// Checkout page end

	//Cart Checkout page script start
	if(page_name == 'cart_checkout')
	{

		function openCustomPopup(url, address_type)
		{
			if(address_type=='billing')
				var address_id = $('#js-billing-address-id').val();
			else
				var address_id = $('#js-shipping-address-id').val();
			var actions_url = url+'?address_type='+address_type+'&address_id='+address_id+'&item_owner_id='+item_owner_id;
			//var postData = 'ship_template_id=' + ship_template_id + '&shipping_country_id=' + shipping_country_id + '&shipping_company_id=' + shipping_company_id,
			fancybox_url = actions_url;
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
				/*afterShow  : function() {
				},
				afterClose  : function() {
				}*/
			});
		};

		function openCustomVariationPopup(url)
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
				href        : url,
				openEffect  : 'none',
				closeEffect : 'none'
			});
		}


		$(".fn_fancyboxview").fancybox({
			beforeShow: function() {
				$(".fancybox-wrap").addClass('view-proprevw');
			},
			maxWidth    : 772,
			maxHeight   : 432,
			fitToView   : false,
			autoSize    : true,
			closeClick  : true,
			openEffect  : 'none',
			closeEffect : 'none'
		});

		/*$(".fn_signuppop").fancybox({
			maxWidth    : 800,
			maxHeight   : 630,
			fitToView   : false,
			width       : '70%',
			height      : '430',
			autoSize    : false,
			closeClick  : false,
			type        : 'iframe',
			openEffect  : 'none',
			closeEffect : 'none'
		});*/


		$('.js-use_as_billing').click(function() {
            if ($(this).is(':checked')) {
				var shipping_address = $('#shipping_address').html();
				var billing_address = $('#billing_address').html(shipping_address);
				$('#js-billing-address-id').val($('#js-shipping-address-id').val());

                /*$('.js-copy-text').each(function(){
                    $('#billing_'+$(this).attr('name')).val($(this).val());
                    $('#billing_'+$(this).attr('name')).prop('disabled', true);
                });*/
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

		$('.js-service-checkbox').click(function() {
			var final_price = $('#orgamount').val();
			final_price = parseFloat(final_price);
			var services_price = 0;
			var service_ids = [];
			$.each($("input[name='productservices[]']:checked"), function() {
				service_ids.push($(this).val());
				services_price = services_price + $(this).data('price');
			});
			service_ids.join(',');
			var subtotal_price = final_price+services_price;
			$('#subtotal_price').html(subtotal_price);
			//$('#subtotal_price1').html(subtotal_price);
			//$('#product_services').val(service_ids);
		});

		function showTaxDetails(item_id)
        {
            var div_id = 'tax_fee_details_'+item_id;
            $('#'+div_id).toggle('slow');
        }

		function removeCartItem(item_id, item_type) {
			var cmsg = remove_item_confirm_msg;
			var txtYes = common_yes_lbl;
			var txtNo = common_cancel_lbl;
			var buttonText = {};
			buttonText[txtYes] = function(){
										window.location.href=delete_order_url+"?item_id="+item_id+"&item_type="+item_type+"&item_owner_id="+item_owner_id;
									};
			buttonText[txtNo] = function(){
										$(this).dialog('close');
										return false;
									};
			$("#fn_dialog_confirm_msg").html(cmsg);
			$("#fn_dialog_confirm_msg").dialog({
				resizable: false,
				height: 180,
				modal: true,
				title: remove_item_title,
				buttons: buttonText
			});
		}

		function removeAllCartItems()
		{
			//$('#item_owner_id').val('{{-- $d_arr['item_owner_id'] --}}');
			$('#cookie_id').val(cookie_id);
			$('#act').val('remove_all');
			document.getElementById("form_checkout").submit();
		}

		function removeCouponCode()
		{
			var total_amount = remove_coupon_total_amount;
			$('#applied_coupon_code').val('');
			$('#remove_code').hide();
			$('#apply_code').show();
			$('#discount_price_div').hide();
			$('#subtotal_price').html(remove_coupon_total_amount);
			if($('#subtotal_price_with_currency').length > 0)
			{
				var org_amt_frmt_curr = $('#org_total_formatted_curr').html();
				$('#subtotal_price_with_currency').html(org_amt_frmt_curr);
			}
		}

		function applyCouponCode()
		{
			var total_amount = apply_coupon_total_amount;
			var coupon_code = $('#coupon_code').val();
			if(coupon_code == "")
			{
				$('#coupon_error').text('');
				return false;
			}
			displayLoadingImage(true);
			$.post(postCouponUrl, { coupon_code: coupon_code, total_amount:total_amount, item_owner_id:item_owner_id},  function(response)
        	{
        		data = eval( '(' +  response + ')');
                if(data.status=="success")
                {
                	$('#applied_coupon_code').val(coupon_code);
                	$('#coupon_error').text('');
                	$('#dicount_price').html(data.discount_amount_formatted);
                	if($('#dicount_price_with_currency').length > 0)
                		$('#dicount_price_with_currency').html(data.discount_amount_formatted_curr);
                	$('#discount_price_div').show();
					$('#subtotal_price').html(data.discounted_amount_formatted);
					if($('#subtotal_price_with_currency').length > 0)
                		$('#subtotal_price_with_currency').html(data.discounted_amount_formatted_curr);
					$('#remove_code').show();
					$('#apply_code').hide();
                }
                else
                {
                	$('#applied_coupon_code').val('');
					$('#coupon_error').text(data.error_message);
				}
	        	hideLoadingImage(false);
	        });
		}


		$(document).ready(function() {
			$(".fn_fancybox").fancybox({
				openEffect  : 'none',
				closeEffect : 'none'
			});
			//If back button pressed, then reload page
			var d = new Date();
			d = d.getTime();
			if (jQuery('#reloadValue').val().length == 0){
				jQuery('#reloadValue').val(d);
			} else {
				jQuery('#reloadValue').val('');
				location.reload();
			}

			$(".fnchangeShippingCompany").click(function() {
				var elem_id = $(this).attr('id');
				var cart_id = elem_id.split('_')[4];
				var shipping_company = $('input[name=shipping_company_'+ cart_id +']:checked').attr('id');
				var shipping_company_id = shipping_company.split('_')[3];
				displayLoadingImage(true);
				var actions_url = change_shipping_company_url;
				var item_owner_id = $('#item_owner_id').val();
				var qry_str = 'cart_id=' + cart_id + '&shipping_company=' + shipping_company_id + '&redirect_to=checkout'+ '&item_owner_id='+item_owner_id;

				window.location.href = actions_url + "?" + qry_str;
			});

			$(document).mouseup(function (e) {
				var container = $(".fnShippingCompanies");
				if (!container.is(e.target) // if the target of the click isn't the container...
					&& container.has(e.target).length === 0) {
					container.hide();
				}
			});

			$(".fnShippingCompaniesOpener").click(function() {
				var elem_id = $(this).attr('id');
				var cart_id = elem_id.split('_')[3];
				if($("#shipping_companies_" + cart_id).is(":hidden")) {
					$(".fnShippingCompanies").hide();
					$("#shipping_companies_" + cart_id).show();
				}
				else
					$("#shipping_companies_" + cart_id).hide();
			});

			$(".fnShippingCompaniesClose").click(function() {
				$(".fnShippingCompanies").hide();
			});

		});


		function updVar(matrix_id, item_id, item_owner_id)
		{
			displayLoadingImage(true);

			$.ajax({
				type:'POST',
				url: BASE+''+update_item_variation_link,
				data: {"item_id": item_id, "item_owner_id" : item_owner_id, "matrix_id": matrix_id},
				success: function(data){
					data_arr = eval( '(' +  data + ')');
					if(!data_arr.error_exists)
					{
						window.location.href =check_out_url+'/'+item_owner_id;
					}
				},
			});
		}

		$('.giftwrapChk').click(function(event) {
			if($(this).attr('checked'))
				var use_giftwrap = 1;
			else
				var use_giftwrap = 0;
			displayLoadingImage(true);
			var elem_id = $(this).attr('id');
			var elem_id_str = elem_id.split('_');
			//var item_owner_id = elem_id_str[2];
			var item_id = elem_id_str[2];
			$.ajax({
				type:'POST',
				url: BASE +''+update_item_giftwrap,
				data: {"item_id": item_id, "item_owner_id" : item_owner_id, "use_giftwrap": use_giftwrap},
				success: function(data){

					data_arr = eval( '(' +  data + ')');
					if(!data_arr.error_exists)
					{
						//alert("No errors found");
						window.location.href = check_out_url+'/'+item_owner_id;
					}
				},
			});
		});

		$(document).ready(function(){
			$('.fn_giftwrap_msg').each(function(){
				$.data(this, default_txt, this.value);
			}).focus(function(){
				if ($.data(this, default_txt) == this.value) {
					this.value = '';
				}
			}).blur(function(){
				if (this.value == '') {
					this.value = $.data(this, default_txt);
				}
			});
		});


	}
	// Cart Checkout page script end


}