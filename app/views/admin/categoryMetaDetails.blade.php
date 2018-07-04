@extends('admin')
@section('content')
	<!--- BEGIN: NOTIFICATIONS --->
    @include('notifications')
    <!--- END: NOTIFICATIONS --->

    <!--- BEGIN: ERROR INFO --->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!--- END: ERROR INFO --->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{Lang::get('admin/manageCategory.meta_details.category_meta_details')}}</h1>
    <!-- END: PAGE TITLE -->

	@if($enable_edit)
		{{ Form::model($category_info, ['url' => Url::action('AdminProductCategoryController@postCategoryMetaDetails'), 'method' => 'post', 'id' => 'categoryEditfrm', 'class' => 'form-horizontal']) }}
	    	{{-- Form::open(array('url'=> Url::action('AdminProductCategoryController@postCategoryMetaDetails'), 'id'=>'categoryEditfrm', 'method'=>'get','class' => 'form-horizontal' )) --}}
			<div class="portlet box blue-madison">
				<!--- BEGIN: SEARCH TITLE --->
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-edit"></i> {{Lang::get('admin/manageCategory.meta_details.edit_meta_details')}} {{isset($category_info['parent_category_name'])?' - '.$category_info['parent_category_name']:''}}
					</div>
					<div class="tools">
						<a class="collapse" href="javascript:;"></a>
					</div>
				</div>
				<!--- END: SEARCH TITLE --->

				<div class="portlet-body form">
					<div class="form-body" id="search_holder">
						<div class="form-group {{{ $errors->has('use_parent_meta_detail') ? 'error' : '' }}}">
							{{ Form::label('use_parent_meta_detail', trans("admin/manageCategory.meta_details.used_parent_meta_details"), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-4">
								<?php
									$use_parent_meta_detail = false;
									if(isset($category_info['use_parent_meta_detail']) && strtolower($category_info->use_parent_meta_detail)=='yes')
										$use_parent_meta_detail =true;
								?>
								<div class="radio-list">
									<label class="radio-inline">
										{{Form::checkbox('use_parent_meta_detail','Yes',$use_parent_meta_detail, array('class' => 'checkboxes')) }}
										<label>{{trans('common.yes')}}</label>
									</label>
								</div>
								<label class="error">{{{ $errors->first('use_parent_meta_detail') }}}</label>
							</div>
						</div>

						<div class="form-group">
							{{ Form::label('category_meta_title', Lang::get('admin/manageCategory.meta_details.meta_title'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-4">
								{{ Form::text('category_meta_title', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('category_meta_title') }}}</label>
							</div>
						</div>

						<div class="form-group">
							{{ Form::label('category_meta_keyword', Lang::get('admin/manageCategory.meta_details.meta_keyword'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-7">
								{{ Form::textarea('category_meta_keyword', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('category_meta_keyword') }}}</label>
							</div>
						</div>

						<div class="form-group">
							{{ Form::label('category_meta_description', Lang::get('admin/manageCategory.meta_details.meta_description'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-7">
								{{ Form::textarea('category_meta_description', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('category_meta_description') }}}</label>
							</div>
						</div>
					</div>

					<!-- BEGIN: SEARCH ACTIONS -->
					<div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-4">
							{{Form::hidden('id')}}
							<button type="submit" name="search_tax" value="search_tax" class="btn green">
							<i class="fa fa-arrow-up bigger-110"></i> {{ Lang::get('common.update') }}</button>
							<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminProductCategoryController@getCategoryMetaDetails') }}'"><i class="fa fa-times bigger-110"></i> {{ Lang::get('common.cancel') }}</button>
						</div>
					</div>
					<!-- END: SEARCH ACTIONS -->
				</div>
			 </div>
	    {{ Form::close() }}
    @endif

    {{ Form::open(array('id'=>'categorySearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!--- BEGIN: SEARCH TITLE --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{Lang::get('admin/manageCategory.meta_details.search_categories')}}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- END: SEARCH TITLE --->

            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking">
                        <div class="form-group">
                            {{ Form::label('category_name', Lang::get('admin/manageCategory.meta_details.category_name'), array('class' => 'control-label col-md-3')) }}
                            <div class="col-md-4">
                                {{ Form::text('category_name', Input::get("category_name"), array('class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEGIN: SEARCH ACTIONS -->
                <div class="form-actions fluid">
                	<div class="col-md-offset-3 col-md-4">
                        <button type="submit" name="search_tax" value="search_tax" class="btn purple-plum">{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminProductCategoryController@getCategoryMetaDetails') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- END: SEARCH ACTIONS -->
            </div>
         </div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/manageCategory.meta_details.category_meta_details') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(sizeof($all_categories) > 0 )
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.category') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.meta_title') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.meta_keyword') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.meta_description') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.used_parent_meta_details') }}</th>
                                <th width="100">{{ Lang::get('admin/manageCategory.meta_details.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($all_categories as $category)
								<tr>
                            		<td>{{ implode(' &raquo; ',$category->category_link) }}</td>
                                    <td>{{ $category->category_meta_title }}</td>
                                    <td>{{ $category->category_meta_keyword }}</td>
                                    <td>{{ $category->category_meta_keyword }}</td>
                                    <td>
										<?php
											if(count($category) > 0) {
												if($category['use_parent_meta_detail'] == 'Yes') {
													$lbl_class = "badge-success";
												}
													elseif($category['use_parent_meta_detail'] == 'No') {
														$lbl_class = " badge-danger";
												}
											else
												{ $lbl_class = "badge-default"; }
											}
										?>
										<span class="badge {{ $lbl_class }}">{{ trans('common.'.strtolower($category->use_parent_meta_detail)) }}</span>
									</td>
                                    <td class="status-btn">
                                    	<a target="_blank" href="{{$category->cat_link}}" class="btn btn-xs btn-info" title="{{ trans('common.view') }}">
										<i class="fa fa-eye"></i></a>
                                        <a href="{{ URL::action('AdminProductCategoryController@getCategoryMetaDetails').'?category_id='.$category->id}}" class="btn btn-xs blue" title="{{ trans('common.edit') }}"><i class="fa fa-edit"></i></a>
                                    </td>
                            	</tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $all_categories->appends(array('category_name' => Input::get('category_name'), 'category_id' => Input::get('category_name'), 'action' => Input::get('action')))->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/manageCategory.meta_details.no_category_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::action('AdminTaxationsController@postDeleteTaxations'))) }}
    {{ Form::hidden('taxation_id', '', array('id' => 'taxation_id')) }}
    {{ Form::hidden('tax_action', '', array('id' => 'tax_action')) }}
    {{ Form::close() }}

	<div id="dialog-tax-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-tax-confirm-content" class="show ml15"></span>
	</div>
@stop