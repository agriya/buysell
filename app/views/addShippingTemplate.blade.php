@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<div id="error_msg_div"></div>
			<!-- NOTIFICATIONS STARTS -->
			@include('notifications')
			<!-- NOTIFICATIONS END -->
			<?php  $err_tab = (isset($err_tab) && $err_tab != '')?$err_tab:1;
			$err_company = (isset($err_company) && $err_company != '')?$err_company:'';
			?>

			<div class="responsive-pull-none">
				<!-- PAGE TITLE STARTS -->
				<a href="{{ URL::action('ShippingTemplateController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('shippingTemplates.shipping_templates_list') }}
				</a>
				@if(isset($id) && $id !='')
					<h1>{{ Lang::get('shippingTemplates.edit_shipping_template') }}</h1>
				@else
					<h1>{{ Lang::get('shippingTemplates.add_shipping_template') }}</h1>
				@endif
				<!-- PAGE TITLE END -->
			</div>

			<div class="well">
				<!--{{ Form::open(array('id'=>'shippingTemplateFrm', 'method'=>'post','class' => 'form-horizontal' )) }}-->
				{{ Form::model($shipping_template_details, ['method' => 'post', 'id' => 'shippingTemplateFrm', 'class' => 'form-horizontal']) }}
					<!-- STARTS TEMPLATE NAME FIELD -->
					<div class="margin-bottom-30">
						<div class="form-group {{{ $errors->has('template_name') ? 'error' : '' }}}">
							{{ Form::label('template_name', trans("shippingTemplates.shipping_template_name"), array('class' => 'forlabel-custom required-icon col-md-3 control-label')) }}
							<div class="col-md-4">
								{{  Form::text('template_name', null, array('class' => 'form-control valid')) }}
								<label class="error">{{{ $errors->first('template_name') }}}</label>
							</div>
						</div>
						<label class="error">{{{ $errors->first('companies') }}}</label>
					</div>
					<!-- ENDS TEMPLATE NAME FIELD -->

					<div class="tabbable-custom">
						<div class="customview-navtab mobviewmenu-480">
							<button class="btn bg-blue-steel btn-sm"><i class="fa fa-chevron-down"></i> {{ Lang::get('shippingTemplates.shipping_template') }}</button>
							<!-- TABS STARTS -->
							<ul role="tablist" class="nav nav-tabs margin-bottom-30" id="myTab">
								@if(count($post_service_companies) > 0)
									<li @if(isset($err_tab) && $err_tab == 1) class="active" @endif><a href="#postservice" id="postservice_tab" data-toggle="tab" role="tab" >{{ trans('shippingTemplates.post_service') }}</a></li>
								@endif

								@if(count($express_companies) > 0)
									<li @if(isset($err_tab) && $err_tab == 2) class="active" @endif><a href="#express" id="express_tab" data-toggle="tab" role="tab">{{ trans('shippingTemplates.express') }}</a></li>
								@endif

								@if(count($special_line_companies) > 0)
								<li @if(isset($err_tab) && $err_tab == 3) class="active" @endif><a href="#specialline" id="specialline_tab" data-toggle="tab" role="tab" >{{ trans('shippingTemplates.special_line') }}</a></li>
								@endif

								@if(count($other_companies) > 0)
									<li @if(isset($err_tab) && $err_tab == 4) class="active" @endif><a href="#others" id="others_tab" data-toggle="tab" role="tab">{{ trans('shippingTemplates.others') }}</a></li>
								@endif
							</ul>
							<!-- TABS END -->
						</div>

						<!-- TABLE TITLE STARTS -->

						<div class="tab-content margin-bottom-30">
							@if(count($post_service_companies) > 0)
								<div id="postservice" class="tab-pane fade in @if(isset($err_tab) && $err_tab == 1) active @endif">
									<div class="table-responsive">
										<table class="table table-bordered table-hover table-striped">
											<thead>
												<tr>
													<th colspan="4">{{ trans('shippingTemplates.post_service') }}</th>
												</tr>
											</thead>
											@include('displayShippingCompanies', array('companies' => $post_service_companies, 'tabname' => 'clsTab_postservice'))
										 </table>
									</div>
								</div>
							@endif

							@if(count($express_companies) > 0)
							<div id="express" class="tab-pane fade in @if(isset($err_tab) && $err_tab == 2) active @endif">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th colspan="4">{{ trans('shippingTemplates.express') }}</th>
											</tr>
										</thead>
										@include('displayShippingCompanies', array('companies' => $express_companies, 'tabname' => 'clsTab_express'))
									 </table>
								</div>
							</div>
							@endif

							@if(count($special_line_companies) > 0)
							<div id="specialline" class="tab-pane fade in @if(isset($err_tab) && $err_tab == 3) active @endif">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th colspan="4">{{ trans('shippingTemplates.special_line') }}</th>
											</tr>
										</thead>
										@include('displayShippingCompanies', array('companies' => $special_line_companies, 'tabname' => 'clsTab_specialline'))
									 </table>
								</div>
							</div>
							@endif

							@if(count($other_companies) > 0)
							<div id="others" class="tab-pane fade in @if(isset($err_tab) && $err_tab == 4) active @endif">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th colspan="4">{{ trans('shippingTemplates.others') }}</th>
											</tr>
										</thead>
										@include('displayShippingCompanies', array('companies' => $other_companies, 'tabname' => 'clsTab_others'))
									 </table>
								</div>
							</div>
							@endif
						</div>

						<div class="form-group">
							<div class="col-md-8">
								{{Form::hidden('template_company_id', $id, array('id' => 'template_company_id'))}}
								<button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
									<i class="fa fa-save"></i> {{ trans("shippingTemplates.save") }}
								</button>
								<button type="reset" name="reset_shipping" value="reset_shipping" class="btn default" onclick="javascript:location.href='{{ URL::action('ShippingTemplateController@getIndex') }}'">
		                        	<i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}
		                        </button>
							</div>
						</div>
						<!-- TABLE TITLE END -->
					</div>
				{{Form::close()}}
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		function openCustomShippingTemplatePopup(url, company_id)
		{
			var temp_comp_id = $('#template_company_id').val();
			if(temp_comp_id == ''){temp_comp_id =0;}
			url = url+'/'+temp_comp_id+'/'+company_id;
			var template_name = parent.$('#template_name').val();
			var checkbox_id = 'company_' + company_id;
			var fee_type_name = 'fee_type_' + company_id;
			var discount_name = 'discount_' + company_id;
			var discount_group = 'discount_group_'+company_id;
			var delivery_type_name = 'delivery_type_' + company_id;
			var delivery_days_name = 'delivery_days_' + company_id;

			var custom_fee_type = 'fee_type_'+company_id+'_custom';
			var standard_fee_type = 'fee_type_'+company_id+'_standard';

			$("input:radio[name="+fee_type_name+"]").removeAttr('disabled');
			$('#' + discount_group).hide();
			$("input:radio[name="+delivery_type_name+"]").removeAttr('disabled');
			$('#' + delivery_days_name).removeAttr('disabled');

			$("input:radio[name="+fee_type_name+"]").each(function(){
				$(this).removeAttr('checked');
				$(this).parent().removeClass('checked');
			})

			$('#'+checkbox_id).attr('checked', 'checked');
			$('#'+checkbox_id).parent().addClass('checked');

			$('#'+custom_fee_type).attr('checked', 'checked');
			$('#'+custom_fee_type).parent().addClass('checked');

			var actions_url = url+'?t_name='+template_name;
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

		function openCustomDeliveryTimePopup(url, company_id)
		{
			var temp_comp_id = $('#template_company_id').val();
			if(temp_comp_id == ''){temp_comp_id =0;}
			url = url+'/'+temp_comp_id+'/'+company_id;

			var template_name = parent.$('#template_name').val();
			var checkbox_id = 'company_' + company_id;
			var fee_type_name = 'fee_type_' + company_id;
			var delivery_type_name = 'delivery_type_' + company_id;
			var delivery_group = 'delivery_group_'+company_id;
			var discount_name = 'discount_' + company_id;
			var custom_delivery_type_name = 'delivery_type_'+company_id+'_custom';
			var delivery_type_name = 'delivery_type_' + company_id;
			var delivery_days_name = 'delivery_days_' + company_id;

			var custom_delivery_type = 'delivery_type_'+company_id+'_custom';

			$('#'+delivery_group).hide();

			$("input:radio[name="+fee_type_name+"]").removeAttr('disabled');
			$('#' + discount_name).removeAttr('disabled');
			$("input:radio[name="+delivery_type_name+"]").removeAttr('disabled');
			$('#' + delivery_days_name).removeAttr('disabled');

			$("input:radio[name="+delivery_type_name+"]").each(function(){
				$(this).removeAttr('checked');
				$(this).parent().removeClass('checked');
			})

			$('#'+checkbox_id).attr('checked', 'checked');
			$('#'+checkbox_id).parent().addClass('checked');

			$('#'+custom_delivery_type_name).attr('checked', 'checked');
			$('#'+custom_delivery_type_name).parent().addClass('checked');

			var actions_url = url+'?t_name='+template_name;;
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
				closeEffect : 'none'
				/*afterClose  : function() {
					 window.location.reload();
				}*/
			});
		};


		///start of document ready function
		$(document).ready(function() {
			//$('a[href^="#express"]').parent('li').addClass('active');
			//$('#express').addClass('active');
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

			//On load check default option and disable if company not selected
			$('.clsShippingCompany').each(function(index) {
				var company_id = this.id.replace('company_block_', '');
				var company_name = 'company_' + company_id;
				var fee_type_name = 'fee_type_' + company_id;
				var discount_name = 'discount_' + company_id;
				var delivery_type_name = 'delivery_type_' + company_id;
				var delivery_days_name = 'delivery_days_' + company_id;
				if ($("input:radio[name="+fee_type_name+"]:checked").length == 0) {
					if ($('#' + fee_type_name + '_standard').length > 0) {
						$('#' + fee_type_name + '_standard').parent('span').addClass('checked');
						$('#' + fee_type_name + '_standard').attr('checked', 'checked');
					} else {
						$('#' + fee_type_name + '_free').parent('span').addClass('checked');
						$('#' + fee_type_name + '_free').attr('checked', 'checked');
					}
				}
				else {
					var fee_type_value = $("input:radio[name="+fee_type_name+"]:checked").val();
					var discount_group = 'discount_group_' + company_id;
					if(fee_type_value == 2) //standard
						$('#' + discount_group).show();
					else
						$('#' + discount_group).hide();
				}

				if ($("input:radio[name="+delivery_type_name+"]:checked").length == 0) {
					$('#' + delivery_type_name + '_promised').parent('span').addClass('checked');
					$('#' + delivery_type_name + '_promised').attr('checked', 'checked');
				}
				else {
					var delivery_type_value = $("input:radio[name="+delivery_type_name+"]:checked").val();
					var delivery_group = 'delivery_group_' + company_id;
					if(delivery_type_value == 2) //promised
						$('#' + delivery_group).show();
					else
						$('#' + delivery_group).hide();
				}

				if ($('#' + company_name).is(":checked")) {
					//Do code for checked condition
				} else {
					$("input:radio[name="+fee_type_name+"]").attr('disabled', 'disabled');
					$('#' + discount_name).attr('disabled', 'disabled');
					$("input:radio[name="+delivery_type_name+"]").attr('disabled', 'disabled');
					$('#' + delivery_days_name).attr('disabled', 'disabled');
				}
			});
			//On click of company checkbox
			$("input:checkbox").live("click", function(e){
				var company_id = this.id.replace('company_', '');
				var company_name = 'company_' + company_id;
				var fee_type_name = 'fee_type_' + company_id;
				var discount_name = 'discount_' + company_id;
				var delivery_type_name = 'delivery_type_' + company_id;
				var delivery_days_name = 'delivery_days_' + company_id;
				if ($(this).is(":checked")) {
					$("input:radio[name="+fee_type_name+"]").removeAttr('disabled');
					$('#' + discount_name).removeAttr('disabled');
					$("input:radio[name="+delivery_type_name+"]").removeAttr('disabled');
					$('#' + delivery_days_name).removeAttr('disabled');
					$("input:radio[name="+fee_type_name+"]").each(function(element){
						$(this).closest('div').removeClass('disabled');
					});
					$("input:radio[name="+delivery_type_name+"]").each(function(element){
						$(this).closest('div').removeClass('disabled');
					});

				} else {
					$("input:radio[name="+fee_type_name+"]").attr('disabled', 'disabled');
					$('#' + discount_name).attr('disabled', 'disabled');
					$("input:radio[name="+delivery_type_name+"]").attr('disabled', 'disabled');
					$('#' + delivery_days_name).attr('disabled', 'disabled');
					$("input:radio[name="+fee_type_name+"]").each(function(element){
						$(this).closest('div').addClass('disabled');
					});
					$("input:radio[name="+delivery_type_name+"]").each(function(element){
						$(this).closest('div').addClass('disabled');
					});
				}
			});
			//On click of radio buttons
			$("input:radio").live("click", function(e){
				var company_id = this.id.split('_')[2];
				var type = this.id.split('_')[3];
				var category = this.id.split('_')[0];
				var discount_group = 'discount_group_' + company_id;
				var delivery_group = 'delivery_group_' + company_id;
				if (category == 'fee') {
					if (type == 'standard')
						$('#' + discount_group).show();
					else
						$('#' + discount_group).hide();
				} else if (category == 'delivery') {
					if (type == 'promised')
						$('#' + delivery_group).show();
					else
						$('#' + delivery_group).hide();
				}
			});

			var specialKeys = new Array();
			specialKeys.push(8); //Backspace
			specialKeys.push(9); //tab
			specialKeys.push(13); //Enter
			specialKeys.push(46); //Delete
			$(function () {
				//For Quantity range start
				$("input:text").live("keypress", function (e) {
					var company_id = 0;
					if(this.id.split('_').length == 3)
						var company_id = this.id.split('_')[2];
					else
						var company_id = this.id.split('_')[1];

					if (this.name == 'template_name'){
						return true;
					}
					var keyCode = e.which ? e.which : e.keyCode
					var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
					var limit = 2;
					if (keyCode == 8 || e.keyCode == 46) limit = 3;
					ret = (ret && this.value.length < limit);
					ret = (ret && !(e.keyCode == 0 && e.which == 46));
					var errorDiv = '#error_field_' + company_id;
					$(errorDiv).css("display", "none");
					return ret;
				});
				$("input:text").live("blur", function (e) {
					ret = (this.value.length > 2);
					if (ret) {
						var company_id = 0;
						if(this.id.split('_').length == 3)
							var company_id = this.id.split('_')[2];
						else
							var company_id = this.id.split('_')[1];
						var errorDiv = '#error_field_' + company_id;
						$(errorDiv).css("display", "none");
					}
				});
				$("input:text").live("paste", function (e) {
					if (this.name != 'template_name'){
						return false;
					}
				});
				$("input:text").live("drop", function (e) {
					if (this.name != 'template_name'){
						return false;
					}
				});
			});

			var template_id = '{{ isset($id) ? $id : 0 }}';


			$('#shippingTemplateFrm').live("submit", function(e){

				if(template_id == 0)
				{
					var  temp_id = $('#template_company_id').val();
					if( temp_id!= '0' && temp_id!='')
					{
						template_id = temp_id;
					}
				}
				//alert('template_id = '+template_id);
				var submit = true;
				var err_tab_id = '';
				var discount_div_msg = '';
				var delivery_days_div_msg = '';
				var custom_fee_company = '';
				var custom_fee_div_msg = '';
				var custom_delivery_div_msg = '';
				var custom_delivery_company = '';
				var custom_fee = new Array();
				var custom_delivery = new Array();
				custom_fee['postservice'] = new Array();
				custom_fee['express'] = new Array();
				custom_fee['specialline'] = new Array();
				custom_fee['others'] = new Array();
				custom_delivery['postservice'] = new Array();
				custom_delivery['express'] = new Array();
				custom_delivery['specialline'] = new Array();
				custom_delivery['others'] = new Array();

				$('.clsShippingCompany').each(function(index) {
					//removeErrorDialog();
					var company_id = this.id.replace('company_block_', '');
					var company_name = 'company_' + company_id;
					var fee_type_name = 'fee_type_' + company_id;
					var discount_name = 'discount_' + company_id;
					var delivery_type_name = 'delivery_type_' + company_id;
					var delivery_days_name = 'delivery_days_' + company_id;
					var class_list = $(this).attr('class');
					var tab_class = class_list.split(' ')[1];
					var tab_id = class_list.split('_')[1];

					if ($('#' + company_name).is(":checked")) {

						if ($("input:radio[name="+fee_type_name+"]:checked").length > 0) {
							var fee_type_value = $("input:radio[name="+fee_type_name+"]:checked").val();
							var discount_group = 'discount_group_' + company_id;
							if(fee_type_value == 2) { //standard
								var discount_value = $('#' + discount_name).val();
								if(discount_value == '') {

									var error_msg = '{{trans('shippingTemplates.enter_discount_price')}}';
									var errorDiv = '#error_field_' + company_id;
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(error_msg);
									if(err_tab_id == '') {
										$('#' + tab_id + '_tab').click();
										err_tab_id = tab_id;
										var tab_name = $('#' + tab_id + '_tab').html();
										discount_div_msg = '<p> {{trans('shippingTemplates.discount_price_missing_id')}} '+ tab_name +'</p>';
										showErrorDialog({status: 'error', error_message: discount_div_msg + discount_div_msg});
									}
									submit = false;
								}
							}
							if(fee_type_value == 1) { //custom
								if (template_id == 0) {
									submit = false;
								} else {
									custom_fee_company += company_id + ',';
									custom_fee[tab_id] += company_id;
									//calling ajax function
								}
								if (!submit) {
									var error_msg = ' {{trans('shippingTemplates.specify_country_for_custom_fee_type')}}';
									var errorDiv = '#error_field_' + company_id;
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(error_msg);
									if(err_tab_id == '') {
										var class_list = $(this).attr('class');
										var tab_class = class_list.split(' ')[1];
										var tab_id = class_list.split('_')[1];
										$('#' + tab_id + '_tab').click();
										err_tab_id = tab_id;
										var tab_name = $('#' + tab_id + '_tab').html();
										discount_div_msg = '<p> {{trans('shippingTemplates.discount_price_missing_in')}} '+ tab_name +'</p>';
										showErrorDialog({status: 'error', error_message: discount_div_msg + delivery_days_div_msg});
									}
									submit = false;
								}
							}
						}

						if ($("input:radio[name="+delivery_type_name+"]:checked").length > 0) {
							var delivery_type_value = $("input:radio[name="+delivery_type_name+"]:checked").val();
							var delivery_group = 'delivery_group_' + company_id;
							if(delivery_type_value == 2) { //promised
								var delivery_days_value = $('#' + delivery_days_name).val();
								if(delivery_days_value == '') {
									var error_msg = '{{trans('shippingTemplates.enter_delivery_days')}}';
									var errorDiv = '#error_field_' + company_id;
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(error_msg);
									if(err_tab_id == '') {
										var class_list = $(this).attr('class');
										var tab_class = class_list.split(' ')[1];
										var tab_id = class_list.split('_')[1];
										$('#' + tab_id + '_tab').click();
										err_tab_id = tab_id;
										var tab_name = $('#' + tab_id + '_tab').html();
										delivery_days_div_msg = '<p> {{trans('shippingTemplates.delivery_days_missing_in')}} '+ tab_name +'</p>';
										showErrorDialog({status: 'error', error_message: discount_div_msg + delivery_days_div_msg});
									}
									submit = false;
								}
							}
							if(delivery_type_value == 1) { //custom
								if (template_id == 0) {
									submit = false;
								} else {
									custom_delivery_company += company_id + ',';
									custom_delivery[tab_id] += company_id;
									//calling ajax function
								}
								if (!submit) {
									var error_msg = '{{trans('shippingTemplates.specify_country_for_delivery_type')}}';
									var errorDiv = '#error_field_' + company_id;
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(error_msg);
									if(err_tab_id == '') {
										var class_list = $(this).attr('class');
										var tab_class = class_list.split(' ')[1];
										var tab_id = class_list.split('_')[1];
										$('#' + tab_id + '_tab').click();
										err_tab_id = tab_id;
										var tab_name = $('#' + tab_id + '_tab').html();
										custom_delivery_div_msg = '<p> {{trans('shippingTemplates.specify_country_for_delivery_type')}} '+ tab_name +'</p>';
										showErrorDialog({status: 'error', error_message: delivery_days_div_msg + custom_delivery_div_msg});
									}
								}
							}
						}

					}
				});
				var custom_submit = true;
				if (submit && (custom_fee_company != '' || custom_delivery_company != '')){
					var post_data = 'fee_company_ids='+custom_fee_company+'&delivery_company_ids='+custom_delivery_company+'&template_id='+template_id;
					var post_url = '{{ URL::to('shipping-template/check-custom-values') }}';
					displayLoadingImage(true);
					//
					 $.ajax({
						data: post_data,
						url: post_url,
						type: 'POST',
						async: false,
						//cache: false,
						error: function(){
							return false;
						},
						success: function(msg){
							company_ids = msg.split('##');
							company_ids[0]; //fee company
							company_ids[1];
							//alert(company_ids[0]); //delivery company
							if (company_ids[0] != '') {
								var fee_compnay_ids = company_ids[0].split(',');
								for(var i=0; i<=fee_compnay_ids.length; i++) {
									var error_msg = '{{trans('shippingTemplates.specify_country_for_custom_fee_type')}}';
									var errorDiv = '#error_field_' + fee_compnay_ids[i];
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(error_msg);
									if(custom_fee['postservice'].indexOf(fee_compnay_ids[i]) != -1){
										//$('#postservice_tab').click();
										err_tab_id = 'postservice';
									}
									else if(custom_fee['express'].indexOf(fee_compnay_ids[i]) != -1){
										//$('#express_tab').click();
										err_tab_id = 'express';
									}
									else if(custom_fee['specialline'].indexOf(fee_compnay_ids[i]) != -1){
										//$('#specialline_tab').click();
										err_tab_id = 'specialline';
									}
									else if(custom_fee['others'].indexOf(fee_compnay_ids[i]) != -1){
										//$('#others_tab').click();
										err_tab_id = 'others';
									}
									custom_fee_div_msg = '<p> {{trans('shippingTemplates.specify_country_for_custom_fee_type')}} ' + err_tab_id + '</p>';
									showErrorDialog({status: 'error', error_message: discount_div_msg + custom_fee_div_msg});
								}
								custom_submit = false;
								hideLoadingImage();
							}
							//alert(company_ids[1]);
							if (company_ids[1] != '') {
								var delivery_compnay_ids = company_ids[1].split(',');
								for(var i=0; i<=delivery_compnay_ids.length; i++)
								{
									var error_msg = '{{trans('shippingTemplates.specify_country_for_delivery_type')}}';
									var errorDiv = '#error_field_' + delivery_compnay_ids[i];
									$(errorDiv).css("display", "inline");
									$(errorDiv).text(error_msg);
									if(custom_delivery['postservice'].indexOf(delivery_compnay_ids[i]) != -1){
										//$('#postservice_tab').click();
										err_tab_id = 'postservice';
									}
									else if(custom_delivery['express'].indexOf(delivery_compnay_ids[i]) != -1){
										//$('#express_tab').click();
										err_tab_id = 'express';
									}
									else if(custom_delivery['specialline'].indexOf(delivery_compnay_ids[i]) != -1){
										//$('#specialline_tab').click();
										err_tab_id = 'specialline';
									}
									else if(custom_delivery['others'].indexOf(delivery_compnay_ids[i]) != -1){
										//$('#others_tab').click();
										err_tab_id = 'others';
									}
									custom_delivery_div_msg = '<p> {{trans('shippingTemplates.specify_country_for_delivery_type')}} ' + err_tab_id + '</p>';
									showErrorDialog({status: 'error', error_message: discount_div_msg + custom_delivery_div_msg});
								}
								custom_submit = false;
								hideLoadingImage();
							}
							hideLoadingImage();
							if(custom_submit)
							{
								return true;

							}
							else
							{
								$('#'+err_tab_id+'_tab').click();
								e.preventDefault();
								return custom_submit;
							}
						}
					});
				} else {
					if(submit)
						submit = custom_submit;

					if(submit)
						return true;
					else
					{
						$('#'+err_tab_id+'_tab').click();
						e.preventDefault();
						return false;
					}
					//return submit;
				}
			});

			$("#shippingTemplateFrm").validate({
				rules: {
					template_name: {
						required: true
					}
				},
				messages: {
					template_name: {
						required: mes_required
					}
				}
			});
		}); ///End of document ready function
	</script>
@stop