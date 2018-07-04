<!-- BEGIN: ALERT BLOCK -->
@if (isset($success_message) && $success_message != "")
    <div class="note note-success" id="cancellation_pollicy_success_div_msg">{{ $success_message }}</div>
@elseif (isset($error_message) && $error_message != "")
    <div class="note note-danger" id="cancellation_pollicy_success_div_msg">{{ $error_message }}</div>
@endif
<div class="note note-success" id="cancellation_pollicy_success_div" style="display:none;"></div>
<!-- END: ALERT BLOCK -->

<!-- BEGIN: SHOP CANCELLATION POLICY DETAILS -->
{{ Form::model($cancellation_policy_details, ['url' => URL::to('admin/shop/edit/'.$user_id),'method' => 'post','id' => 'shopcancellationpolicy_frm', 'class' => 'form-horizontal', 'onsubmit' => "return doSubmit('shopcancellationpolicy_frm', 'shop_cancellation_policy')", 'files' => true]) }}
	{{ Form::hidden('submit_form', "update_cancellation_policy", array("name" => "submit_form", "id" => "submit_form"))}}
	<fieldset>
		<div class="form-group {{{ $errors->has('cancellation_policy_text') ? 'error' : '' }}}">
			{{ Form::label('cancellation_policy_text', trans("shopDetails.cancellation_policy_text"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-7">
				{{  Form::textarea('cancellation_policy_text', Input::get('cancellation_policy_text'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('cancellation_policy_text') }}}</label>
				<div id="shop_desc_count"></div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-4 col-md-7"><center>(OR)</center></div>
		</div>
		<div class="form-group">
			{{ Form::label('shop_cancellation_policy_file', trans("shopDetails.shop_cancellation_policy_file"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-8">
				{{ Form::file('shop_cancellation_policy_file', array('title' => trans("shopDetails.shop_cancellation_policy_file"), 'class' => 'btn red-sunglo btn-sm text-left')) }}
				<label class="error clearfix" for="shop_cancellation_policy_file" generated="true">{{$errors->first('shop_cancellation_policy_file')}}</label>
				<div class="margin-top-10 clearfix">
					<i class="fa fa-question-circle pull-left mt3"></i>
					<p class="text-muted pull-left">
						<small class="show">{{ str_replace("VAR_FILE_FORMAT",  Config::get('webshoppack.shop_cancellation_policy_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}</small>
						<small>{{ str_replace("VAR_FILE_MAX_SIZE",  (Config::get('webshoppack.shop_cancellation_policy_allowed_file_size')/1024).' MB', trans('shop.uploader_allowed_upload_limit')) }}</small>
					</p>
				</div>
			</div>
		</div>
		@if(count($cancellation_policy_details) > 0 && $cancellation_policy_details['cancellation_policy_filename'] != '')
			<div class="form-group">
				<div class="col-md-offset-4 col-md-6">
					<div class="uploadedimg-list clearfix">
						<ul id="uploadedFilesList" class="list-unstyled">
							<li id="shopCancellationPolicyRow_{{ $cancellation_policy_details['id'] }}">
								<?php $filePath = $cancellation_policy_details['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
										$filename = $cancellation_policy_details['cancellation_policy_filename'].'.'.$cancellation_policy_details['cancellation_policy_filetype'];?>
								<a target="_blank" href="{{$filePath.'/'.$filename}}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> View file</a>
								<a title="{{trans('common.delete')}}" href="javascript: void(0);" onclick="javascript:removeShopCancellationPolicy({{ $cancellation_policy_details['id'] }});" class="remove-image"><i class="fa fa-times-circle text-danger"></i></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		@endif
		<div class="form-group">
			<div class="col-md-offset-4 col-md-8">
				@if(count($cancellation_policy_details) > 0)
					{{Form::hidden('id', $cancellation_policy_details['id'])}}
				@endif
				<button type="button" name="update_policy" class="btn blue-madison" id="update_policy" value="update_policy" onclick="javascript:doSubmit('shopcancellationpolicy_frm', 'shop_cancellation_policy');"><i class="fa fa-cloud-upload"></i> {{trans("common.update")}}</button>
			</div>
		</div>
	</fieldset>
{{ Form::close() }}
<!-- END: SHOP CANCELLATION POLICY DETAILS -->
