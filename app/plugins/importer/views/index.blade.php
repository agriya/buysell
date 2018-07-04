@extends('base')
@section('content')
	<div id="error_msg_div"></div>
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('importer::importer.csv_upload') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<div class="well bg-form import-file">
				{{ Form::open(array('action' => array('App\Plugins\Importer\Controllers\ImporterController@postIndex'), 'id'=>'importerFrm', 'files' => true, 'method'=>'post','class' => 'form-horizontal' )) }}
					<!-- BEGIN: SEARCH BLOCK -->
					<div class="row">
						<div class="col-md-6">
							<fieldset class="portlet">
								<div class="form-group">
									{{ Form::label('importer_type', Lang::get('importer::importer.importer_type'), array('class' => 'col-md-3 control-label')) }}
									<div class="col-md-7">
										{{ Form::select('importer_type', $importer_type, Input::get("importer_type"), array('class' => 'form-control valid', 'id' => 'js-importertype')) }}
									</div>
								</div>
								
                                <?php 
									$p_id = 1;
								?>
                                
                                <div class="form-group">
									{{ Form::label('csv_file', Lang::get('importer::importer.upload_csv_file'), array('class' => 'col-md-3 control-label')) }}									
                                    <div class="{{{ $errors->has('upload_thumb') ? 'error' : '' }}}" id="link_add_thumb_csv">
                                        <div class="col-md-7">
                                            <div class="btn purple-plum" id="upload_thumb"> 
                                            	<i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_csv_file") }}
                                            </div>
                                            <div class="margin-top-5">
                                                <i class="fa fa-question-circle pull-left"></i>
                                                <div><small class="text-muted">Upload only csv file</small></div>
                                            </div>
                                            <label class="error csv_file_upload_error">{{{ $errors->first('upload_thumb') }}}</label>
                                            <p class="margin-top-10">
                                                {{ Form::hidden('csv_file_input', '', array('id' => 'csv_file_input')) }}
                                                {{ Form::hidden('file_original_name', '', array('id' => 'file_original_name')) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div style="display:none;" id="link_remove_thumb_csv">
                                    	<div class="col-md-7 margin-top-10">
                                            <strong class="text-success"><i class="fa fa-check"></i> File uploaded</strong>
                                            <a onclick="javascript:removeProductThumbCSV();" class="label label-danger" href="javascript: void(0);"><i class="fa fa-times"></i> {{ trans("importer::importer.remove_file") }}</a>
                                        </div>
                                    </div>
								</div>
                                
								<div class="form-group" id="zip_upload" style="display:none">
                                     {{ Form::label('zip_file', Lang::get('importer::importer.upload_zip_file'), array('class' => 'col-md-3 control-label')) }}
                                     <div id="link_add_thumb_image">
                                        <div class="col-md-7">
                                            <div class="btn purple-plum" id="upload_thumb_image"> <i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_zip_file") }}</div>
                                            <div class="margin-top-5">
                                                <i class="fa fa-question-circle pull-left"></i>
                                                <div><small class="text-muted">Upload only zip file</small></div>
                                            </div>
                                            <label class="error image_file_upload_error">{{{ $errors->first('upload_thumb') }}}</label>
                                            <p class="margin-top-10">
                                                {{ Form::hidden('image_file_input', '', array('id' => 'image_file_input')) }}
                                                {{ Form::hidden('zip_original_name', '', array('id' => 'zip_original_name')) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div style="display:none;" id="link_remove_thumb_image">
                                    	<div class="col-md-7 margin-top-10">
                                            <strong class="text-success"><i class="fa fa-check"></i> File uploaded</strong>
                                            <a onclick="javascript:removeProductThumbImage();" class="label label-danger" href="javascript: void(0);"><i class="fa fa-times"></i> {{ trans("importer::importer.remove_file") }}</a>
                                        </div>
                                    </div>
								</div>
                                
                                <div class="col-md-offset-3 col-md-10">
                                    <button type="submit" name="srchcoupon_submit" value="srchcoupon_submit" class="btn green">
                                    <i class="fa fa-check"></i> {{ Lang::get('importer::importer.submit') }}</button>
                                </div>
							</fieldset>
						</div>
						<div class="col-md-6">
							<div class="portlet">
								<h2 class="title-one">{{ Lang::get('importer::importer.upload_zip_file') }}</h2>
								<ul class="list-inline">
									<li>
										<a id="readMeLink" data-toggle="modal" data-target="#myModal" class="label label-primary" title="{{ Lang::get('importer::importer.view') }}">
										<i class="fa fa-eye"></i> {{ Lang::get('importer::importer.readme') }}</a>
									</li>
									<li>
										<a href="{{ URL::action('App\Plugins\Importer\Controllers\ImporterController@getAction').'?action=download_general_csv' }}" class="label label-success" title="{{ Lang::get('importer::importer.download') }}">
										<i class="fa fa-cloud-download"></i> {{ Lang::get('importer::importer.general_csv_format') }}</a>
									</li>
									<li>
										<a target="_blank" href="{{ URL::action('App\Plugins\Importer\Controllers\ImporterController@getCategoryListing') }}" class="label label-info" title="{{ Lang::get('importer::importer.get_category_id_from') }}">
										<i class="fa fa-file-text"></i> {{ Lang::get('importer::importer.category_listing') }}</a>
									</li>
								</ul>

								<p><i class="fa fa-arrow-right text-muted"></i> {{ Lang::get('importer::importer.upload_your_csv_here') }}</p>

								<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<div class="margin-top-10 pull-right">
													<button type="button" class="close" data-dismiss="modal">
														<span aria-hidden="true"></span><span class="sr-only">{{trans('common.close')}}</span>
													</button>
												</div>
												<h1 class="margin-0" id="myModalLabel">{{Lang::get('importer::importer.csv_readme_instruction')}}</h1>
											</div>
											<div class="modal-body">
												@include('importer::csvReadme')
											</div>
											<div class="modal-footer">
												<button type="button" class="btn red pull-right" data-dismiss="modal"><i class="fa fa-times"></i> {{trans('common.close')}}</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END: SEARCH BLOCK -->
				{{ Form::close() }}

				@if(count($imported_files) > 0)
					<!-- BEGIN: CSV FILES LIST -->
					<div class="table-responsive margin-bottom-30">
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th class="col-md-1">{{ Lang::get('importer::importer.sl_no') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.csv_type') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.added_on') }}</th>
									<th class="col-md-2">{{ Lang::get('importer::importer.file_name') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.status') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.no_of_records') }}</th>
									<th class="col-md-2">{{ Lang::get('importer::importer.action') }}</th>
								</tr>
							</thead>

							<tbody>
								@if(count($imported_files) > 0)
									@foreach($imported_files as $file)
										<tr>
											<td>{{ $file->id }}</td>
											<td>{{ $file->file_from }}</td>
											<td>{{ CUtil::FMTDate($file->created_at, 'Y-m-d H:i:s', '') }}</td>
											<td>
												<a href="{{ URL::action('App\Plugins\Importer\Controllers\ImporterController@getAction').'?action=download_csv&file_id='.$file->id  }}">
												{{ $file->file_original_name }}</a>
											</td>
											<td>
												<?php
													if(count($file) > 0) {
														if($file['status'] == 'InActive') {
															$lbl_class = "label-danger";
														}
														elseif($file['status'] == 'Active') {
															$lbl_class = " label-primary";
														}
														elseif($file['status'] == 'Progress') {
															$lbl_class = "label-warning";
														}
														elseif($file['status'] == 'Completed') {
															$lbl_class = "label-success";
														}
													else
														{ $lbl_class = "label-default"; }
													}
												?>
												<span class="label {{ $lbl_class }}">{{ $file->status }}</span>
											</td>
											<td>{{ $file->item_count }}</td>
											<td class="action-btn">
												<a title="{{Lang::get('importer::importer.view')}}" href="{{ URL:: action('App\Plugins\Importer\Controllers\ImporterController@getView',$file->id) }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
												@if(strtolower($file->status) != 'completed')
													@if($file->status == 'InActive')
														<a title="{{Lang::get('importer::importer.activate')}}" href="javascript:void(0)" onclick="doAction('{{ $file->id }}', 'activate')" class="btn btn-xs green"><i class="fa fa-check"></i></a>
														<a title="{{Lang::get('importer::importer.delete')}}" href="javascript:void(0)" onclick="doAction('{{ $file->id }}', 'delete')" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
													@endif
												@endif
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="7"><p class="alert alert-info">{{ Lang::get('importer::importer.list_empty') }}</p></td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>

					@if(count($imported_files) > 0)
                        {{ $imported_files->links() }}
                    @endif
				@else
					<div class="note note-info">
					   {{ Lang::get('importer::importer.list_empty') }}
					</div>
				@endif
				<!-- END: CSV FILES LIST -->

				{{ Form::open(array('id'=>'csvFileActionfrm', 'method'=>'post', 'url' => URL::action('App\Plugins\Importer\Controllers\ImporterController@postAction'))) }}
					{{ Form::hidden('file_id', '', array('id' => 'file_id')) }}
					{{ Form::hidden('file_action', '', array('id' => 'file_action')) }}
				{{ Form::close() }}
				<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog-product-confirm-content" class="show"></span>
				</div>
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script src="{{ URL::asset('/js/uploadDocument.js') }}"></script>
	<script src="{{ URL::asset('/js/ajaxupload.3.5.min.js') }}"></script>

	
	<script type="text/javascript">
		$().ready(function() {
			$("#importerFrm").validate({
				rules: {
					csv_file_input: "required",
				},
				messages: {
					csv_file_input: "Please upload the csv file",
				}
			});
		});
		
		$('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('coupon.show_search_filters') }} <i class="fa fa-caret-down ml5"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('coupon.hide_search_filters') }} <i class="fa fa-caret-up ml5"></i>');
	        }
	        return false;
	    });
	    $(function() {
	    	var importer_type = $('#js-importertype').val();
           	if(importer_type == 'general')
	    		$('#zip_upload').show();
			else
				$('#zip_upload').hide();
        });
	    $('#js-importertype').change(function(){
			var importer_type = $(this).val();
	    	if(importer_type == 'general')
	    		$('#zip_upload').show();
			else
				$('#zip_upload').hide();

		});

	    function doAction(file_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('importer::importer.file_delete_confirm') }}');
			}
			else if(selected_action == 'deactivate')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('importer::importer.file_deactivate_confirm') }}');
			}
			else if(selected_action == 'activate')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('importer::importer.file_activate_confirm') }}');
			}

			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('importer::importer.csv_importer') }}', modal: true,
				buttons: {
					"{{ Lang::get('importer::importer.yes') }}": function() {
						$(this).dialog("close");
						$('#file_action').val(selected_action);
						$('#file_id').val(file_id);
						document.getElementById("csvFileActionfrm").submit();
					}, "{{ Lang::get('importer::importer.cancel') }}": function() { $(this).dialog("close"); }
				}
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
		
		
		
		
		
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
        var product_actions_url = "{{ URL::to('importer/product-actions')}}";
		
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

		$(function(){
                var btnUpload=$('#upload_thumb'); 
                new AjaxUpload(btnUpload, {
                    action: product_actions_url,
                    name: 'uploadfile',
                    data: ({action: 'upload_product_thumb_csv', upload_tab: 'preview'}),
                    method: 'POST',
                    onSubmit: function(file, ext){ 
						if (!(ext && $.trim(ext).toLowerCase() == 'csv')){ 
                            showErrorDialog({status: 'error', error_message: '{{ sprintf(trans("product.products_allowed_formats"), 'csv') }}'});
                            return false;
                        }
                        var settings = this._settings; 
                        settings.data.item_image_title = $.trim($('#item_thumb_image_title').val());
                        displayLoadingImage(true);
                    },
                    onComplete: function(file, response) { 
					hideLoadingImage(true); 
						if($.trim(response) != 0 && $.trim(response) != 'file_size_error'){ 
							var obj = jQuery.parseJSON(response);
							$('#csv_file_input').val(obj.file_name_with_extension);
							$('#file_original_name').val(obj.file_original_name);
							$('#link_add_thumb_csv').css("display", "none"); 
							$('#link_remove_thumb_csv').css("display", "inline"); 
							$('.error').html('');
						}else{
							if($.trim(response) != 'file_size_error'){
								$('.csv_file_upload_error').html('Something went wrong, please upload again');
							}else{
								$('.csv_file_upload_error').html('file size should not be greater than <?=Config::get('importer.max_csv_file_size') ?> KB');
							}
						}
                    }
                });
            });
			
			function removeProductThumbCSV(){
				$('#csv_file_input').val('');
				$('#link_add_thumb_csv').css("display", "inline"); 
				$('#link_remove_thumb_csv').css("display", "none"); 
			}
			
			
			
        //var product_actions_image = "{{ URL::to('importer/product-image')}}";
		

		//http://localhost/boobathi/buysell/public/importer/product-actions
		$(function(){
                var btnUpload=$('#upload_thumb_image'); 
                new AjaxUpload(btnUpload, {
                    action: product_actions_url,
                    name: 'uploadfile',
                    data: ({action: 'upload_product_thumb_image', upload_tab: 'preview'}),
                    method: 'POST',
                    onSubmit: function(file, ext){
						if (!(ext && $.trim(ext).toLowerCase() == 'zip')){ 
                            showErrorDialog({status: 'error', error_message: '{{ sprintf(trans("product.products_allowed_formats_zip"), 'csv') }}'});
                            return false;
                        }
                        var settings = this._settings; 
                        settings.data.item_image_title = $.trim($('#item_thumb_image_title').val());
                        displayLoadingImage(true);
                    },
                    onComplete: function(file, response) {
					hideLoadingImage(true); 
						if($.trim(response) != 0 && $.trim(response) != 'file_size_error'){ 
							var obj = jQuery.parseJSON(response);
							$('#image_file_input').val(obj.file_name_with_extension);
							$('#zip_original_name').val(obj.file_original_name);
							$('#link_add_thumb_image').css("display", "none"); 
							$('#link_remove_thumb_image').css("display", "inline"); 
						}else{
							if($.trim(response) != 'file_size_error'){
								$('.image_file_upload_error').html('Something went wrong, please upload again');
							}else{
								$('.image_file_upload_error').html('file size should not be greater than <?=Config::get('importer.max_csv_file_size') ?> KB');
							}
						}
                    }
                });
            });
			
			function removeProductThumbImage(){
				$('#csv_file_input').val('');
				$('#link_add_thumb_image').css("display", "inline"); 
				$('#link_remove_thumb_image').css("display", "none"); 
			}
		
		
	</script>
@stop