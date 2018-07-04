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
				<div class="@if(count($taxationslist) <= 0 && !$is_search_done) text-right @else responsive-text-center @endif">
					<a href="{{ URL::action('TaxationsController@getAddTaxation') }}" class="btn btn-xs green-meadow responsive-btn-block pull-right">
					<i class="fa fa-plus"></i> {{ Lang::get('taxation.add_taxation')  }}</a>
				</div>
				<h1>{{ Lang::get('taxation.taxations_list') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			<!-- ALERT BLOCK STARTS -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif

			<div class="well">

				{{ Form::open(array('action' => array('TaxationsController@getIndex'), 'id'=>'productFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
					<!-- SEARCH BLOCK STARTS -->
					<div id="search_holder" class="portlet bg-form">
						<div class="portlet-title">
							<div class="caption">
								{{ Lang::get('taxation.serach_taxations') }}
							</div>
							<div class="tools">
								<a class="collapse" href="javascript:;"></a>
							</div>
						</div>

						<div id="selSrchProducts" class="portlet-body">
							<fieldset>
								<div class="form-group">
									{{ Form::label('tax_name', Lang::get('taxation.tax_name'), array('class' => 'col-md-2 control-label')) }}
									<div class="col-md-4">
										{{ Form::text('tax_name', Input::get("tax_name"), array('class' => 'form-control valid')) }}
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-10">
										<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn purple-plum">
										<i class="fa fa-search"></i> {{ Lang::get('taxation.search') }}</button>
										<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'"><i class="fa fa-rotate-left"></i> {{ Lang::get('taxation.reset') }}</button>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<!-- SEARCH BLOCK ENDS -->

					<!-- TAXATION LIST STARTS -->
					<div class="table-responsive margin-bottom-30">
						@if(count($taxationslist) > 0)
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th class="">{{ Lang::get('taxation.tax_name') }}</th>
									<th class="col-md-5">{{ Lang::get('taxation.description') }}</th>
									<th>{{ Lang::get('taxation.tax_fee') }}</th>
									<th>{{ Lang::get('taxation.fee_type') }}</th>
									<th width="100">{{ Lang::get('taxation.action') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($taxationslist as $taxation)
									<tr>
										<td>{{ $taxation->tax_name }}</td>
										<td><div class="wid-400">{{ $taxation->tax_description }}</div></td>
										<td><strong>{{$taxation->tax_fee}}</strong></td>
										<td>{{ Lang::get('common.'.strtolower($taxation->fee_type))}}</td>
										<td class="action-btn">
											<a href="{{ URL:: action('TaxationsController@getUpdateTaxation',$taxation->id) }}" class="btn btn-xs blue" title="{{ Lang::get('common.edit') }}" >
											<i class="fa fa-edit"></i></a>
											<a href="javascript:void(0)" onclick="doActiondelete('{{ $taxation->id }}', 'delete')" class="btn btn-xs red" title="{{ Lang::get('common.delete') }}" >
											<i class="fa fa-trash-o"></i></a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="text-right">
							{{ $taxationslist->appends(array('tax_name' => Input::get('tax_name'), 'srchproduct_submit' => Input::get('srchproduct_submit')))->links() }}
						</div>
						@else
				           <div class="alert alert-info mar0">{{ Lang::get('admin/taxation.no_taxations_found') }}</div>
				        @endif
					</div>
					<!-- TAXATION LIST ENDS -->

				{{ Form::close() }}

				{{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::action('TaxationsController@postDeleteTaxations'))) }}
					{{ Form::hidden('taxation_id', '', array('id' => 'taxation_id')) }}
					{{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
				{{ Form::close() }}

				<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog-product-confirm-content" class="show"></span>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var page_name = "taxations_list";
		var show_search_filters = '{{ Lang::get('taxation.show_search_filters') }}';
		var hide_search_filters = '{{ Lang::get('taxation.hide_search_filters') }}';
		var confirm_delete = '{{ Lang::get('taxation.confirm_delete') }}';
		var taxations_list = '{{ Lang::get('taxation.taxations_list') }}';
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;

	</script>
@stop