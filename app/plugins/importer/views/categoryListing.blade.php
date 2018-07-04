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
				<h1>{{ Lang::get('importer::importer.categories_list') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<div class="well">
				@if(count($category_list) > 0)
					<div class="table-responsive importcatg-list">
						<table class="tree table table-bordered table-hover table-striped">
							<tr>
								<th width="300">{{ Lang::get('importer::importer.category') }}</th>
								<th width="60">{{ Lang::get('importer::importer.id') }}</th>
							</tr>
							@foreach($category_list as $category)
								<tr class="treegrid-{{$category['id']}} @if($category['parent_category_id'] > 0) treegrid-parent-{{$category['parent_category_id']}} @endif">
									<td>{{$category['category_name']}}</td><td>{{$category['category_id_lbl']}}</td>
								</tr>
							@endforeach
						</table>
					</div>
				@else
					<div class="note note-info">
					   {{ Lang::get('importer::importer.list_empty') }}
					</div>
				@endif
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script src="{{ URL::asset('/js/lib/treegrid/jquery.treegrid.js') }}"></script>
	<link href="{{ URL::asset('/js/lib/treegrid/css/jquery.treegrid.css') }}" rel="stylesheet"/>
	<script type="text/javascript">
		$('.tree').treegrid();

		//this is tree forming with tree structure of array with ul and li. Omitted now
		/*$(function () {
		    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
		    $('.tree li.parent_li > span').on('click', function (e) {
		        var children = $(this).parent('li.parent_li').find(' > ul > li');
		        if (children.is(":visible")) {
		            children.hide('fast');
		            $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
		        } else {
		            children.show('fast');
		            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
		        }
		        e.stopPropagation();
		    });
		});*/
	</script>
@stop