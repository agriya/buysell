@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->
     @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif

	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{Lang::get('admin/staticPage.edit_sell_page_static_content')}}</h1>
    <!-- END: PAGE TITLE -->

	{{ Form::model($static_page_content, ['method' => 'post', 'id' => 'addEditfrm', 'class' => 'form-horizontal']) }}
    	<div class="portlet box blue-madison">
            <!--- BEGIN: SEARCH TITLE --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i> {{Lang::get('admin/staticPage.edit_static_page')}}
                </div>
                <!--<div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>-->
            </div>
            <!--- END: SEARCH TITLE --->

			<!--- BEGIN: STATIC EDIT BLOCK --->
            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking">
	                	<p class="note note-info">{{Lang::get('admin/staticPage.use_site_name_in_content')}}</p>
                        <div class="form-group">
                            {{ Form::label('page_title', Lang::get('admin/staticPage.page_title'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-4">
                                {{ Form::text('page_title', null, array('class' => 'form-control valid fn_editor', 'rows' => 7)) }}
								<label class="error">{{{ $errors->first('page_title') }}}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('what_can_you_sell', Lang::get('admin/staticPage.what_can_you_sell'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-7">
                                {{ Form::textarea('what_can_you_sell', null, array('class' => 'form-control')) }}
                            	<label class="error">{{{ $errors->first('what_can_you_sell') }}}</label>
							</div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('how_doest_it_work', Lang::get('admin/staticPage.how_doest_it_work'), array('class' => 'control-label col-md-3 required-icon')) }}
                            <div class="col-md-7">
                                {{ Form::textarea('how_doest_it_work', null, array('class' => 'form-control')) }}
                           		<label class="error">{{{ $errors->first('how_doest_it_work') }}}</label>
						    </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('additional_title', Lang::get('admin/staticPage.additional_title'), array('class' => 'control-label col-md-3')) }}
                            <div class="col-md-4">
                                {{ Form::text('additional_title', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('additional_title') }}}</label>
                            </div>
                        </div>

						<div class="form-group">
                            {{ Form::label('additional_content', Lang::get('admin/staticPage.additional_content'), array('class' => 'control-label col-md-3')) }}
                            <div class="col-md-7">
                                {{ Form::textarea('additional_content', null, array('class' => 'form-control')) }}
                           		<label class="error">{{{ $errors->first('how_doest_it_work') }}}</label>
						    </div>
                        </div>

                    </div>
                </div>

                <div class="form-actions fluid">
                	<div class="col-md-offset-3 col-md-4">
                        <button type="submit" name="addedit" value="addedit" class="btn green"><i class="fa fa-arrow-up"></i> {{ Lang::get('common.update') }}</button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminStaticPageController@getSellStaticPage') }}'"><i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}</button>
                    </div>
                </div>
            </div>
            <!--- END: STATIC EDIT BLOCK --->
         </div>
    {{ Form::close() }}

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
				$('#static-content-div').hide();
				$('#external-link-div').show();
			}
			else
			{
				$('#static-content-div').show();
				$('#external-link-div').hide();
			}
		})
		$('#js-page-type').change(function(){
			var val = $(this).val();

			if(val == 'external')
			{
				$('#static-content-div').hide();
				$('#external-link-div').show();
			}
			else
			{
				$('#static-content-div').show();
				$('#external-link-div').hide();
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
	</script>
@stop