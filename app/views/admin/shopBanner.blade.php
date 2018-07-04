<!-- ALERT BLOCK STARTS -->
@if (isset($success_message) && $success_message != "")
    <div class="note note-success" id="success_msg_div">{{ $success_message }}</div>
@endif
@if (isset($error_message) && $error_message != "")
    <div class="note note-danger" id="success_msg_div">{{ $error_message }}</div>
@endif
	<div class="note note-success" id="success_div" style="display:none;"></div>
<!-- ALERT BLOCK ENDS -->

<!-- SHOP BANNER DETAILS STARTS -->
{{ Form::model($shop_details, ['url' => URL::to('admin/shop/edit/'.$user_id),'method' => 'post','id' => 'shopbanner_frm', 'class' => 'form-horizontal', 'files' => true]) }}
	{{ Form::hidden('submit_form', "update_banner", array("name" => "submit_form", "id" => "submit_form"))}}
	<fieldset>
		<div class="form-group">
			{{-- Form::label('shop_banner_image', trans("shopDetails.shop_banner_image"), array('class' => 'col-md-2 control-label')) --}}
			<label class="col-md-4 control-label required-icon">{{ Lang::get('shopDetails.banner_image')}}</label>
			<div class="col-md-7">
				<div>{{ Form::file('shop_banner_image', array('title' => trans("shopDetails.shop_banner_image"), 'class' => 'btn green-meadow')) }}</div>
				<label class="error" for="shop_banner_image" generated="true">{{$errors->first('shop_banner_image')}}</label>
				<div class="margin-top-10 clearfix">
					<i class="fa fa-question-circle pull-left mt3"></i>
					<p class="pull-left">
                    	<small class="text-muted">
                            <span>{{ str_replace("VAR_FILE_FORMAT",  Config::get('webshoppack.shop_uploader_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}
                            </span>
                            <span class="show">{{ str_replace("VAR_FILE_MAX_SIZE",  (Config::get('webshoppack.shop_image_uploader_allowed_file_size')/1024).' MB', trans('shop.uploader_allowed_upload_limit')) }}</span>
                            <span>{{ str_replace("VAR_IMAGE_RESOLUTION",  Config::get('webshoppack.shop_image_thumb_width').'x'.Config::get('webshoppack.shop_image_thumb_height'), trans('shop.allowed_image_resolution')) }}</span>
                        </small>
					</p>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-4 col-md-6">
				@if(isset($shop_details) && count($shop_details) > 0 && isset($shop_details['image_name']) && $shop_details['image_name'] != '')
					<div class="uploadedimg-list clearfix">
						<ul id="uploadedFilesList" class="list-unstyled">
							<li id="itemResourceRow_{{ $shop_details['id'] }}">
							<?php $imgPath = URL::asset(Config::get('webshoppack.shop_image_folder')); ?>
								<span class="shopban-img">
									{{ HTML::image( $imgPath.'/'.$shop_details['image_name'].'_T.'.$shop_details['image_ext'], "", array()); }}
								</span>
								<a title="{{trans('common.delete')}}" href="javascript: void(0);" onclick="javascript:removeShopImage({{ $shop_details['id'] }}, '{{$shop_details['image_name']}}', '{{$shop_details['image_ext']}}','shop.shop_image_folder');" class="remove-image"><i class="fa fa-times-circle text-danger"></i></a>
							</li>
						</ul>
					</div>
				@endif
				<button type="button" name="update_banner" class="btn blue-madison" id="update_banner" value="update_banner" onclick="javascript:doSubmit('shopbanner_frm', 'banner_details');"><i class="fa fa-upload"></i> {{trans("shopDetails.shop_upload_btn")}}</button>
			</div>
		</div>
	</fieldset>
{{ Form::close() }}
<!-- SHOP BANNER DETAILS ENDS -->
