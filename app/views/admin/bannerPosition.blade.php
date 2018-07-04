@extends('adminPopup')
@section('content')
    <h1>{{ trans('admin/manageSiteBanner.banner_details') }}</h1>
    <div class="table-scrollable">
    	<table class="table table-striped table-bordered table-hover api-log">
	        <thead>
	            <tr>
	                <th width="40">{{ trans('admin/manageSiteBanner.banner_serial_no') }}</th>
	                <th>{{ trans('admin/manageSiteBanner.banner_name') }}</th>
	                <th>{{ trans('admin/manageSiteBanner.banner_size') }}</th>
	            </tr>
	        </thead>
	        <tbody>
	        	@if(count($details) > 0)
	        		<?php $count = 0; ?>
			        @foreach($details as $name => $size)
			        	<?php $count++; ?>
			        	<tr>
							<td>{{ $count }}</td>
							<td>{{ $name }}</td>
							<td>{{ $size }}</td>
						</tr>
			        @endforeach
			    @endif
	        </tbody>
	    </table>
    </div>
@stop