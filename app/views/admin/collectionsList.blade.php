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

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <h1 class="page-title">{{Lang::get('admin/collection.manage_collection')}}</h1>
    <!-- END: PAGE TITLE -->

	{{ Form::open(array('url' => Url::action('AdminManageCollectionsController@getIndex'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/collection.search_collection') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->

			<!-- BEGIN: SEARCH FORM -->
            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                        	<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('collection_id', trans('admin/collection.collection_id'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('collection_id_from', Input::get("collection_id_from"), array('class' => 'form-control', 'placeholder' => trans('admin/collection.from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('collection_id_to', Input::get("collection_id_to"), array('class' => 'form-control', 'placeholder' => trans('admin/collection.to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="collection_id_from" generated="true">{{$errors->first('invoice_id_from')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('status', trans('admin/collection.status'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::select('status', $status, Input::get("status"), array('class' => 'form-control bs-select')) }}
                                            <label class="error" for="status" generated="true">{{$errors->first('status')}}</label>
                                        </div>
                                    </div>
                               </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('privacy', trans('admin/collection.privacy'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::select('privacy', $privacy_list, Input::get("privacy"), array('class' => 'form-control bs-select')) }}
                                            <label class="error" for="search_privacy" generated="true">{{$errors->first('privacy')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('featured', trans('admin/collection.featured'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::select('featured', array('' => trans('common.select'), 'Yes' => trans('common.yes'), 'No' => trans('common.no')), Input::get("featured"), array('class' => 'form-control bs-select')) }}
                                            <label class="error" for="search_privacy" generated="true">{{$errors->first('featured')}}</label>
                                        </div>
                                    </div>
                                </div>
                           	</div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-2 col-md-5">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                {{ trans("common.search") }} <i class="fa fa-search"></i> </button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminManageCollectionsController@getIndex') }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}
                                </button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
			<!-- END: SEARCH FORM -->
     	</div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-th"></i> {{ Lang::get('admin/collection.collections_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($collections_list) > 0 )
            	<!--- BEGIN: COLLECTIONS LIST --->
                {{ Form::open(array('url'=>URL::action('AdminManageCollectionsController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-scrollable">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('admin/collection.collection_name') }}</th>
                                    <th>{{ Lang::get('admin/collection.owner_id') }}</th>
                                    <th>{{ Lang::get('admin/collection.views') }}</th>
                                    <th>{{ Lang::get('admin/collection.comments') }}</th>
                                    <th>{{ Lang::get('admin/collection.clicks') }}</th>
                                    <th>{{ Lang::get('admin/collection.favorites') }}</th>
                                    <th>{{ Lang::get('admin/collection.date_added') }}</th>
                                    <th>{{ Lang::get('admin/collection.privacy') }}</th>
                                    <th>{{ Lang::get('admin/collection.status') }}</th>
                                    <th>{{ Lang::get('admin/collection.featured') }}</th>
                                    <th>{{ Lang::get('admin/collection.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($collections_list as $collection)
                                	<?php
                                		$collection_view_url = URL::action('CollectionsController@getViewCollection',$collection->collection_slug);
                                		$user_details = CUtil::getUserDetails($collection->user_id)
                                	?>
                                    <tr>
                                        <td>{{Form::checkbox('ids[]',$collection->id, false, array('class' => 'checkboxes js-ids') )}}</td>
                                        <td><a target="_blank" href="{{$collection_view_url}}">{{ $collection->collection_name }}</a></td>
                                        <td>
											<a href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{$user_details['display_name']}}</a>
											(<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{$user_details['user_code']}}</a>)
										</td>
                                        <td>{{ $collection->total_views }}</td>
                                        <td>{{ $collection->total_comments }}</td>
                                        <td>{{ $collection->total_clicks }}</td>
                                        <td>
											<?php
												if(count($collection) > 0) {
													if($collection['featured_collection'] == 'Yes') {
														$lbl_class = "badge-success";
													}
														elseif($collection['featured_collection'] == 'No') {
															$lbl_class = " badge-danger";
													}
												else
													{ $lbl_class = "badge-default"; }
												}
											?>
											<span class="badge {{ $lbl_class }}">{{ trans('common.'.strtolower($collection->featured_collection)) }} </span>
										</td>

                                        <td class="text-muted">{{ CUtil::FMTDate($collection->created_at, 'Y-m-d H:i:s', '')}}</td>
                                        <td>
											<?php
												if(count($collection) > 0) {
													if($collection['collection_access'] == 'Private') {
														$lbl_class = "text-warning";
													}
														elseif($collection['collection_access'] == 'Public') {
															$lbl_class = " text-success";
													}
												else
													{ $lbl_class = "text-muted"; }
												}
											?>
											<span class="{{ $lbl_class }}"><strong>{{ trans('common.'.strtolower($collection->collection_access)) }}</strong></span>
										</td>
                                        <td>
											<?php
												if(count($collection) > 0) {
													if($collection['collection_status'] == 'Active') {
														$lbl_class = "label-success";
													}
														elseif($collection['collection_status'] == 'InActive') {
															$lbl_class = " label-danger";
													}
												else
													{ $lbl_class = "label-default"; }
												}
											?>
											<span class="label {{ $lbl_class }}">{{ trans('common.'.strtolower($collection->collection_status)) }}</span>
										</td>
                                        <td>
											<?php
												if(count($collection) > 0) {
													if($collection['featured_collection'] == 'Yes') {
														$lbl_class = "badge-success";
													}
														elseif($collection['featured_collection'] == 'No') {
															$lbl_class = " badge-danger";
													}
												else
													{ $lbl_class = "badge-default"; }
												}
											?>
											<span class="badge {{ $lbl_class }}">{{ trans('common.'.strtolower($collection->featured_collection)) }}</span>
										</td>
                                        <td>
                                        	<a target="_blank" href="{{URL::action('CollectionsController@getViewCollection',$collection->collection_slug)}}" class="btn btn-xs btn-info" title="{{ trans('admin/collection.view') }}"><i class="fa fa-eye"></i></a>
											<div class="mt5">
												@if($collection->featured_collection == "No")
													<a href="javascript:;" onclick="doAction({{$collection->id}}, 'set_as_featured')" class="btn btn-xs bg-green-seagreen" title="{{ trans('admin/collection.set_as_featured') }}">{{ trans('admin/collection.set_as_featured') }}</a>
												@else
													<a href="javascript:;" onclick="doAction({{$collection->id}}, 'remove_from_featured')" class="btn btn-xs bg-red-sunglo" title="{{ trans('admin/collection.remove_from_featured') }}">{{ trans('admin/collection.remove_from_featured') }}</a>
												@endif
											</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                         </table>
                    </div>
                    <div class="clearfix">
		                <p class="pull-left mt10 mr10">
		                    {{Form::select('action',$actions,'',array('class'=>'form-control bs-select input-medium', 'id'=>'collection_action'))}}
		                </p>
		                <p class="pull-left mt10">
		                    <input type="submit" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action">
		                </p>
	            	</div>
                 {{Form::close()}}
                 <!--- END: COLLECTIONS LIST --->

                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $collections_list->appends(array('collection_id_from' => Input::get('collection_id_from'), 'collection_id_to' => Input::get('collection_id_to'),
						'status' => Input::get('status'), 'privacy' => Input::get('privacy'),
						'featured' => Input::get('featured') ))->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/collection.no_collection_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminManageCollectionsController@postAction'))) }}
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
			if(selected_action == 'set_as_featured')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/collection.confirm_set_as_featured') }}');
			}
			if(selected_action == 'remove_from_featured')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/collection.confirm_remove_from_featured') }}');

			}
			$("#dialog-confirm").dialog({ title: '{{ trans('admin/collection.collections_head') }}', modal: true,
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

		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html("{{ trans('admin/collection.select_atleast_one_collection') }}");
				error_found = true;
			}
			var selected_action = $('#collection_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html('{{ trans('common.please_select_action') }}');
				error_found = true;
			}
			if(!error_found)
			{
				if(selected_action == 'delete')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/collection.confirm_delete_collection') }}');
				}
				if(selected_action == 'Active')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/collection.confirm_active_collection') }}');
				}
				if(selected_action == 'InActive')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/collection.confirm_inactive_collection') }}');
				}
			}


			if(error_found)
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/collection.collections_head') }}', modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/collection.collections_head') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#listFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		})
	</script>
@stop