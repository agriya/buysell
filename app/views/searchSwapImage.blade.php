@extends('popup')
@section('content')
	<h1>Swap Image</h1>
	{{ Form::open(array('id'=>'selListImages', 'method'=>'get','class' => 'form-horizontal' )) }}
		@if(sizeof($swap_images_list) > 0 )
        <div class="pop-content">
		    <div class="table-responsive">
		        <table summary="Select swap image" id="selSearchImgTbl" class="table table-bordered table-hover table-striped">
		        	<thead>
		                <tr>
		                    <th>{{ Lang::get('variations::variations.file_lbl') }}</th>
		                    <th>{{ Lang::get('variations::variations.title_lbl') }}</th>
		                    <th>{{ Lang::get('variations::variations.action_lbl') }}</th>
		                </tr>
		            </thead>
		            <tbody>
		                @foreach($swap_images_list as $key => $val)
		                	<?php
								$thumb_img_src = $data_arr['swap_img_folder'].'/'.$val->filename . 'T.' . $val->ext;
							?>
		                	<tr id="{{ $val->swap_image_id }}" alt="{{ $thumb_img_src }}" >
		                		<td>
			                    	<img style="max-width:75px; max-height:75px;" src="{{ $thumb_img_src }}" alt="{{ $val->title }}" />
								</td>
		                        <td>{{ $val->title }}</td>
		                        <td><span class="btn btn-success btn-xs"><i class="fa fa-check"></i> {{ Lang::get('variations::variations.select') }}</span></td>
		                	</tr>
		                @endforeach
		            </tbody>
		         </table>
		    </div>
        </div>
		@else
		    <div class="alert alert-info mar0">{{ Lang::get('variations::variations.no_swap_msg_note_msg') }}.</div>
		@endif
	{{ Form::close() }}
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$('#selSearchImgTbl tr').click(function(event) {
				sel_img = $(this).attr('alt');
				sel_img_id = $(this).attr('id');
			    callSingleAssign(sel_img, sel_img_id);
			});
		});
		@if($data_arr['r_fnname'])
			var fnname = parent.{{ $data_arr['r_fnname'] }};
		@else
			var fnname = 0;
		@endif
		
		function callSingleAssign(sel_img, sel_img_id) {
			if(fnname)
				fnname(sel_img, sel_img_id);
			parent.$.fancybox.close();
		}
	</script>
@stop