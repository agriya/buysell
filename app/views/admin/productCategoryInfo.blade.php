<div id="category_info">
	<!--- ERROR INFO STARTS --->
	@if(isset($error_msg) && $error_msg != '')
        <div class="note note-danger">{{ $error_msg }}</div>
    @endif
    <!--- ERROR INFO END --->
    
    <!--- SUCCESS INFO STARTS --->
	@if(isset($success_msg) && $success_msg != '')
		<div class="note note-success">{{	$success_msg }}</div>
    @endif
    <!--- SUCCESS INFO END --->
    
    {{ Form::model($category_info, ['url' => $cat_url,'method' => 'post','id' => 'addCategoryfrm', 'class' => 'form-horizontal','files' => true]) }}
        {{ Form::hidden('use_all_available_sort_options', 'Yes', array("id" => "use_all_available_sort_options")) }}
        <div class="portlet box blue-hoki">   
            <!--- TABLE TITLE STARTS --->  
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-plus-circle"></i> Add Category</div>
            </div>
            <!--- TABLE TITLE END --->  
        
            <div class="portlet-body form">
                <div class="form-body">
                    <div class="form-group">
                        {{ Form::label('parent_category_name', trans('admin/manageCategory.add-category.parent_category'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            {{ Form::label('parent_category_name', $d_arr['parent_category_name'], array('class' => 'control-label valid txt-left')) }}
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('category_name') ? 'error' : '' }}}">
                        {{ Form::label('category_name', trans('admin/manageCategory.add-category.category_name'), array('class' => 'col-md-3 control-label required-icon')) }}
                        <div class="col-md-4">
                            {{  Form::text('category_name', Input::get('category_name'), array('id' => 'category_name',  'class' => 'form-control valid')); }}
                            <label class="error">{{{ $errors->first('category_name') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('seo_category_name') ? 'error' : '' }}}">
                        {{ Form::label('seo_category_name', trans('admin/manageCategory.add-category.url_slug'), array('class' => 'col-md-3 control-label required-icon')) }}
                        <div class="col-md-4">
                            {{  Form::text('seo_category_name', Input::get('seo_category_name'), array('id' => 'seo_category_name', 'class' => 'form-control valid')); }}
                            <label class="error">{{{ $errors->first('seo_category_name') }}}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('category_description') ? 'error' : '' }}}">
                        {{ Form::label('category_description', trans('admin/manageCategory.add-category.description'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-5">
                            {{  Form::textarea('category_description', Input::get('category_description'), array('id' => 'category_description', 'class' => 'form-control valid', 'rows' => '7')); }}
                            <label class="error">{{{ $errors->first('category_description') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                        {{ Form::label('status', trans('admin/manageCategory.add-category.status'), array('class' => 'col-md-3 control-label required-icon')) }}
                        <div class="col-md-4">
                        	<div class="clearfix">
                                <label class="radio-inline">
                                    {{ Form::radio('status', 'active', (Input::get('status') == 'active') ? true : false, array('id' => 'status_active', 'name' => 'status', 'class' => 'ace')) }}
                                    {{ Form::label('status_active', trans('common.active'), array('class' => ''))}}
                                </label>
                                <label class="radio-inline">
                                    {{ Form::radio('status', 'inactive', (Input::get('status') == 'inactive') ? true : false, array('id' => 'status_inactive', 'name' => 'status', 'class' => 'ace')) }}
                                    {{ Form::label('status_inactive', trans('common.inactive'), array('class' => ''))}}
                                </label>
                            </div>
                            <label class="error">{{{ $errors->first('status') }}}</label>
                        </div>
                    </div>
        
                    @if($d_arr['edit_form'])
                        {{ Form::hidden('is_featured_category', $category_info['is_featured_category'], array("id" => "is_featured_category")) }}
                    @endif
            
                    <div class="form-group {{{ $errors->has('category_meta_title') ? 'error' : '' }}}">
                        {{ Form::label('category_meta_title', trans('admin/manageCategory.add-category.meta_title'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            {{  Form::text('category_meta_title', Input::get('category_meta_title'), array('id' => 'category_meta_title', 'class' => 'form-control valid')); }}
                            <label class="error">{{{ $errors->first('category_meta_title') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('category_meta_description') ? 'error' : '' }}}">
                        {{ Form::label('category_meta_description', trans('admin/manageCategory.add-category.meta_description'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-5">
                            {{  Form::textarea('category_meta_description', Input::get('category_meta_description'), array('id' => 'category_meta_description', 'class' => 'form-control valid', 'rows' => '7')); }}
                            <label class="error">{{{ $errors->first('category_meta_description') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('category_meta_keyword') ? 'error' : '' }}}">
                        {{ Form::label('category_meta_keyword', trans('admin/manageCategory.add-category.meta_Keyword'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            {{  Form::text('category_meta_keyword', Input::get('category_meta_keyword'), array('id' => 'category_meta_keyword', 'class' => 'form-control valid')); }}
                            <label class="error">{{{ $errors->first('category_meta_keyword') }}}</label>
                        </div>
                    </div>
                </div>
        
                <div class="form-actions fluid">
                    <div class="col-md-offset-3 col-md-8">
                        {{ Form::hidden('parent_category_id', $d_arr['parent_category_id'], array("id" => "parent_category_id")) }}
                        @if($d_arr['edit_form'])
                            {{ Form::hidden('category_id', $d_arr['category_id'], array("id" => "category_id")) }}
                            <button type="submit" name="category_submit" id="category_submit" class="btn green" onclick="doAjaxSubmit('addCategoryfrm', 'category_info');return false;"><i class="fa fa-arrow-up"></i> {{ trans('common.update') }}</button>
                            <button type="button" name="cancel_submit" id="cancel_submit" class="btn default" onclick="addSubCategory({{$d_arr['root_category_id']}});">
                            <i class="fa fa-times"></i> {{ trans('common.cancel') }}</button>
                        @else
                            <button type="submit" name="category_submit" id="category_submit" class="btn green" onclick="doAjaxSubmit('addCategoryfrm', 'category_info');return false;"><i class="fa fa-plus-circle"></i> {{ trans('common.add') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>	
    {{ Form::close() }}
</div>