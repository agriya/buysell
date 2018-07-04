@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: INFO BLOCK --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
    <!--- END: INFO BLOCK --->

	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->

    <!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{Lang::get('admin/staticPage.manage_static_page')}}</h1>
    <!-- END: PAGE TITLE -->

	{{ Form::model($page_details, ['method' => 'post', 'id' => 'addEditfrm', 'class' => 'form-horizontal']) }}
    	<div class="portlet box blue-madison">
            <!--- BEGIN: SEARCH TITLE --->
            <div class="portlet-title">
                <div class="caption">
                    @if(is_null($page_id) || $page_id <= 0)
                        <i class="fa fa-plus"></i>
                        {{Lang::get('admin/staticPage.add_static_page')}}
                    @else
                        <i class="fa fa-edit"></i>
                        {{Lang::get('admin/staticPage.edit_static_page')}}
                    @endif
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- END: SEARCH TITLE --->

			<!--- BEGIN: STATIC EDIT BLOCK --->
            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking">
                        <div class="form-group">
                            {{ Form::label('page_type', Lang::get('admin/staticPage.page_type'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-4">
                                {{ Form::select('page_type', $page_type, null, array('class' => 'form-control bs-select', 'id' => 'js-page-type')) }}
                            	<label class="error">{{{ $errors->first('page_type') }}}</label>
							</div>
                        </div>

                        <div class="form-group fn_static_div">
                            {{ Form::label('content', Lang::get('admin/staticPage.content'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-7">
                                {{ Form::textarea('content', null, array('class' => 'form-control valid fn_editor', 'rows' => 7)) }}
								<label class="error">{{{ $errors->first('content') }}}</label>
                            </div>
                        </div>

                        <div class="form-group fn_external_link_div" style="display:none;">
                            {{ Form::label('external_link', Lang::get('admin/staticPage.external_link'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-7">
                                {{ Form::text('external_link', null, array('class' => 'form-control')) }}
                            	<label class="error">{{{ $errors->first('external_link') }}}</label>
							</div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('page_name', Lang::get('admin/staticPage.page_name'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-4">
                                {{ Form::text('page_name', null, array('class' => 'form-control')) }}
                           		<label class="error">{{{ $errors->first('page_name') }}}</label>
						    </div>
                        </div>

                        <div class="form-group fn_static_div">
                            {{ Form::label('title', Lang::get('admin/staticPage.title'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-4">
                                {{ Form::text('title', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('title') }}}</label>
                            </div>
                        </div>

						<div class="form-group {{{ $errors->has('display_in_footer') ? 'error' : '' }}}">
                            {{ Form::label('display_in_footer', Lang::get('admin/staticPage.display_in_footer'), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-4">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {{Form::radio('display_in_footer','Yes', null, array('class' => 'radio')) }}
                                        <label>{{Lang::get('admin/staticPage.yes')}}</label>
                                    </label>
                                    <label class="radio-inline">
                                        {{Form::radio('display_in_footer','No', null , array('class' => 'radio')) }}
                                        <label>{{Lang::get('admin/staticPage.no')}}</label>
                                    </label>
                                </div>
                                <label class="error">{{{ $errors->first('display_in_footer') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                            {{ Form::label('status', Lang::get('admin/staticPage.status'), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-4">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {{Form::radio('status','Active', null, array('class' => 'radio')) }}
                                        <label>{{Lang::get('admin/staticPage.activate')}}</label>
                                    </label>
                                    <label class="radio-inline">
                                        {{Form::radio('status','Inactive', null , array('class' => 'radio')) }}
                                        <label>{{Lang::get('admin/staticPage.not_now')}}</label>
                                    </label>
                                </div>
                                <label class="error">{{{ $errors->first('status') }}}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEGIN: SEARCH ACTIONS -->
                <div class="form-actions fluid">
                	<div class="col-md-offset-3 col-md-4">
                        <button type="submit" name="addedit" value="addedit" class="btn green"><i class="fa fa-save"></i> {{ Lang::get('common.save') }}</button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminStaticPageController@getIndex') }}'"><i class="fa fa-rotate-left"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- END: SEARCH ACTIONS -->
            </div>
            <!--- END: STATIC EDIT BLOCK --->
         </div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/staticPage.static_page_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($lists_arr) > 0 )
            	<!--- BEGIN: STATIC PAGE LIST --->
                {{ Form::open(array('url'=>URL::action('AdminStaticPageController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('admin/staticPage.page_name') }}</th>
                                    <th>{{ Lang::get('admin/staticPage.title') }}</th>
                                    <th>{{ Lang::get('admin/staticPage.status') }}</th>
                                    <th>{{ Lang::get('admin/staticPage.date_added') }}</th>
                                    <th width="100">{{ Lang::get('admin/staticPage.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($lists_arr as $list)
                                    <tr>
                                        <td>{{Form::checkbox('ids[]',$list->id, false, array('class' => 'checkboxes') )}}</td>
                                        <td>{{ $list->page_name }}</td>
                                        <td>
                                        	@if($list->title != '')
												{{ $list->title }}
											@else
												<span title="{{ trans('common.not_applicable_full') }}">{{ trans('common.not_applicable') }}</span>
											@endif
										</td>
                                        <td>
                                            <?php
                                                $lbl_class = "";
                                                if(strtolower ($list['status']) == "active") {
                                                    $lbl_class = "label-success";
                                                    $status = trans('common.active');
                                                } elseif(strtolower ($list['status']) == "inactive") {
                                                    $lbl_class = "label-danger";
                                                    $status = trans('common.inactive');
                                                }
                                            ?>
                                            <span class="label {{ $lbl_class }}">{{ $status }}</span>
                                        </td>
                                        <td>{{ CUtil::FMTDate($list->created_at, 'Y-m-d H:i:s', ''); }}</td>
                                        <td class="status-btn">
                                            <a href="{{ URL:: action('AdminStaticPageController@getIndex',$list->id) }}" class="btn btn-xs blue" title="{{ Lang::get('admin/staticPage.edit_static_page') }}">
                                            <i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0)" onclick="doAction('{{ $list->id }}', 'delete')" class="btn btn-xs red" title="{{ Lang::get('admin/staticPage.delete_static_page') }}">
                                            <i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">
                                        <p class="pull-left margin-top-10 margin-right-10">
                                            {{Form::select('action',$actions,'',array('class'=>'form-control status_class bs-select input-medium', 'id'=>'action'))}}
                                        </p>
                                        <p class="pull-left margin-top-10">
                                           <a onclick="actionStatus(id, 'status')"><input type="button" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action"></a>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!--- END: STATIC PAGE LIST --->

                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $lists_arr->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/staticPage.no_lists_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>

    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminStaticPageController@postAction'))) }}
    {{ Form::hidden('id', '', array('id' => 'list_id')) }}
    {{ Form::hidden('action', '', array('id' => 'list_action')) }}
    {{ Form::close() }}

	<div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;

		tinymce.init({
            menubar: "tools",
            selector: "textarea.fn_editor",
            mode : "exact",
            elements: "content",
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

		$(document).ready(function(){
			var val = $('#js-page-type').val();
			if(val == 'external')
			{
				$('.fn_static_div').hide();
				$('.fn_external_link_div').show();
			}
			else
			{
				$('.fn_static_div').show();
				$('.fn_external_link_div').hide();
			}
		})
		$('#js-page-type').change(function(){
			var val = $(this).val();

			if(val == 'external')
			{
				$('.fn_static_div').hide();
				$('.fn_external_link_div').show();
			}
			else
			{
				$('.fn_static_div').show();
				$('.fn_external_link_div').hide();
			}
		})

		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.each(function(){
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				});
			}
			else
			{
				checkboxes.each(function(){
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				});
			}
		});
		function doAction(id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/staticPage.confirm_delete') }}');
			}
			$("#dialog-confirm").dialog({ title: '{{ trans('admin/staticPage.static_page_head') }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#list_action').val(selected_action);
						$('#list_id').val(id);
						document.getElementById("actionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		function actionStatus(id, selected_action)
		{
			if ($('.checkboxes:checked').length <= 0) {
				bootbox.alert("{{ trans('admin/staticPage.select_the_checkbox') }}");
				return false;
			}
			if ($('.status_class').val() =='' ) {
				bootbox.alert("{{ trans('admin/staticPage.select_the_checkbox') }}");
				return false;
			}
			if(selected_action == 'status')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/staticPage.confirm_status') }}');
			}
			$("#dialog-confirm").dialog({ title: cfg_site_name, modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#list_action').val(selected_action);
						$('#list_id').val(id);
						document.getElementById("listFrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}
	</script>
@stop