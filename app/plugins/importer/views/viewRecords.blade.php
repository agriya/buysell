@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<div class="">
					<a href="{{ URL::action('App\Plugins\Importer\Controllers\ImporterController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left margin-right-5"></i>{{ Lang::get('importer::importer.back_to_list')  }}</a>
				</div>
				<h1>{{ ucfirst($imported_file->file_from) }} - {{ $imported_file->file_original_name }}</h1>
			</div>
			<!-- END: TITLE STARTS -->

			<!-- EGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- BEGIN: ALERT BLOCK -->

			<div class="well">
				@if(count($file_records) > 0)
					<!-- BEGIN: CSV FILES LIST TABLE -->
					<div class="table-responsive margin-bottom-30">
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th class="col-md-1">{{ Lang::get('importer::importer.sl_no') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.title') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.added_on') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.status') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.action') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.product_id') }}</th>
									<th class="col-md-1">{{ Lang::get('importer::importer.error_reasons') }}</th>
								</tr>
							</thead>

							<tbody>
								@if(count($file_records) > 0)
									@foreach($file_records as $record)
										<tr>
											<td>{{ $record->id }}</td>
											<td><div class="wid-200">{{ $record->title }}</div></td>
											<td>{{ CUtil::FMTDate($record->created_at, 'Y-m-d H:i:s', '') }}</td>
											<td>
												<?php
													if(count($record) > 0) {
														if($record['status'] == 'InActive') {
															$lbl_class = "label-danger";
														}
														elseif($record['status'] == 'Active') {
															$lbl_class = " label-primary";
														}
														elseif($record['status'] == 'Progress') {
															$lbl_class = "label-warning";
														}
														elseif($record['status'] == 'Completed') {
															$lbl_class = "label-success";
														}
														elseif($record['status'] == 'Failed') {
															$lbl_class = "label-danger";
														}
													else
														{ $lbl_class = "label-default"; }
													}
												?>
												<span class="label {{ $lbl_class }}">{{ $record->status }}</span>
											</td>
											<td class="action-btn">
												<a title="{{Lang::get('importer::importer.view')}}" href="#" class="btn btn-xs btn-info" data-toggle="modal" data-target="#myModal_{{$record->id}}"><i class="fa fa-eye"></i></a>
												@if(strtolower($record->status) != 'completed')
													@if($record->status == 'InActive')
														<a title="{{Lang::get('importer::importer.activate')}}" href="javascript:void(0)" onclick="doAction('{{ $record->id }}', 'activate')" class="btn btn-xs green"><i class="fa fa-check"></i></a>
													@else
														<a title="{{Lang::get('importer::importer.deactivate')}}" href="javascript:void(0)" onclick="doAction('{{ $record->id }}', 'deactivate')" class="btn btn-xs red"><i class="fa fa-ban"></i></a>
													@endif
												@endif
												<div class="modal fade" id="myModal_{{$record->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	                                                <div class="modal-dialog">
	                                                    <div class="modal-content">
	                                                        <div class="modal-header">
	                                                            <button type="button" class="close" data-dismiss="modal">
	                                                                <span aria-hidden="true"></span><span class="sr-only">{{trans('common.close')}}</span>
	                                                            </button>
	                                                            <h3 class="mar0" id="myModalLabel">{{Lang::get('importer::importer.preview')}}</h3>
	                                                        </div>
	                                                        <div class="modal-body">
	                                                        	{{$service->getRecordPreview($imported_file, $record)}}
	                                                        </div>
	                                                        <div class="modal-footer">
	                                                            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">
																<i class="fa fa-times"></i> {{trans('common.close')}}</button>
	                                                        </div>
	                                                    </div>
	                                                </div>
	                                            </div>
											</td>
											<td>
												@if(is_null($record->product_id) || $record->product_id == '')
													{{Lang::get('importer::importer.n_a')}}
												@else
													<?php $view_url = Products::getProductViewURL($record->product_id);?>
													<a target="_blank" href="{{$view_url}}"><strong>{{$record->product_id}}</strong></a>
												@endif
											</td>
											<td>
												@if($record->error_reasons != '')
													<div class="wid-200">
                                                    	<?php	
															$error_msgs = unserialize($record->error_reasons);
															if(count($error_msgs) > 0){
																foreach($error_msgs as $error_msg){
																	echo "<p>".trans("products.$error_msg")."</p>";
																}
															}
														?>
                                                    </div>
												@else
													-
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

					@if(count($file_records) > 0)
                        {{ $file_records->links() }}
                    @endif
				@else
					<div class="note note-info">
					   {{ Lang::get('importer::importer.list_empty') }}
					</div>
				@endif
				<!-- END: CSV FILES LIST TABLE -->

				{{ Form::open(array('id'=>'csvFileActionfrm', 'method'=>'post', 'url' => URL::action('App\Plugins\Importer\Controllers\ImporterController@postRecordAction'))) }}
					{{ Form::hidden('file_id', $csv_file_id, array('id' => 'file_id')) }}
					{{ Form::hidden('record_id', '', array('id' => 'record_id')) }}
					{{ Form::hidden('record_action', '', array('id' => 'record_action')) }}
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

	    function doAction(file_id, selected_action)
		{
			if(selected_action == 'deactivate')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('importer::importer.record_deactivate_confirm') }}');
			}
			else if(selected_action == 'activate')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('importer::importer.record_activate_confirm') }}');
			}

			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('importer::importer.csv_importer') }}', modal: true,
				buttons: {
					"{{ Lang::get('importer::importer.yes') }}": function() {
						$(this).dialog("close");
						$('#record_action').val(selected_action);
						$('#record_id').val(file_id);
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
	</script>
@stop