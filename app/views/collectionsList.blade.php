@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<div class="@if(count($collections) <= 0 && !$is_search_done) @else responsive-text-center @endif">
					<a href="{{ URL::action('MyCollectionsController@getAdd') }}" class="btn btn-xs green-meadow responsive-btn-block pull-right">
					<i class="fa fa-plus margin-right-5"></i>{{ Lang::get('collection.add_collection')  }}</a>
				</div>
				<h1>{{ Lang::get('collection.collections_list') }}</h1>
			</div>
			<!-- END TITLE STARTS -->

			<!-- ALERT BLOCK STARTS -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- ALERT BLOCK ENDS -->

			<div class="well">
				{{ Form::open(array('action' => array('MyCollectionsController@getIndex'), 'id'=>'collectionFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
					<!-- SEARCH BLOCK STARTS -->
					<div id="search_holder" class="portlet bg-form">
						<div class="portlet-title">
							<div class="caption">
								{{ Lang::get('collection.serach_collections') }}
							</div>
							<div class="tools">
								<a class="collapse" href="javascript:;"></a>
							</div>
						</div>

						<div id="selSrchProducts" class="portlet-body">
							<fieldset>
								<div class="form-group">
									{{ Form::label('collection_name', Lang::get('collection.collection_name'), array('class' => 'col-md-2 control-label')) }}
									<div class="col-md-4">
										{{ Form::text('collection_name', Input::get("collection_name"), array('class' => 'form-control valid')) }}
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-10">
										<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn purple-plum">
										<i class="fa fa-search"></i> {{ Lang::get('collection.search') }}</button>
										<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'">
										<i class="fa fa-rotate-left"></i> {{ Lang::get('collection.reset') }}</button>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<!-- SEARCH BLOCK ENDS -->

					<!-- TAXATION LIST STARTS -->
					<div class="table-responsive margin-bottom-30">
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th class="col-md-2">{{ Lang::get('collection.collection_name') }}</th>
									<th class="col-md-2">{{ Lang::get('collection.description') }}</th>
									<th class="col-md-1">{{ Lang::get('collection.action') }}</th>
								</tr>
							</thead>
							<tbody>
								@if(count($collections) > 0)
									@foreach($collections as $collection)
										<?php
											$collection_view_url = URL::action('CollectionsController@getViewCollection',$collection->collection_slug);
										 ?>
										<tr>
											<td><a target="_blank" href="{{$collection_view_url}}">{{ $collection->collection_name }}</a></td>
											<td><div class="wid-400">{{ $collection->collection_description }}</div></td>
											<td class="action-btn">
												<a href="{{ URL:: action('MyCollectionsController@getUpdate',$collection->id) }}" class="btn btn-xs blue" title="{{ Lang::get('common.edit') }}">
												<i class="fa fa-edit"></i></a>
												<a href="javascript:void(0)" onclick="doAction('{{ $collection->id }}', 'delete')" class="btn btn-xs red" title="{{ Lang::get('common.delete') }}">
												<i class="fa fa-trash-o"></i></a>
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="6"><p class="alert alert-info">{{ Lang::get('collection.list_empty') }}</p></td>
									</tr>
								@endif
							</tbody>
						</table>
						<div class="text-center">
                        	{{ $collections->appends(array('collection_name' => Input::get('collection_name'), 'srchproduct_submit' => Input::get('srchproduct_submit')))->links() }}
                        </div>
					</div>
					<!-- TAXATION LIST ENDS -->
				{{ Form::close() }}

				{{ Form::open(array('id'=>'collectionsActionfrm', 'method'=>'post', 'url' => URL::action('MyCollectionsController@postDelete'))) }}
					{{ Form::hidden('collection_id', '', array('id' => 'collection_id')) }}
					{{ Form::hidden('collection_action', '', array('id' => 'collection_action')) }}
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
	<script type="text/javascript">
		$('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('collection.show_search_filters') }} <i class="fa fa-caret-down ml5"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('collection.hide_search_filters') }} <i class="fa fa-caret-up ml5"></i>');
	        }
	        return false;
	    });

	    function doAction(collection_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('collection.confirm_delete') }}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('collection.collections_list') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#collection_action').val(selected_action);
						$('#collection_id').val(collection_id);
						document.getElementById("collectionsActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
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
	</script>
@stop