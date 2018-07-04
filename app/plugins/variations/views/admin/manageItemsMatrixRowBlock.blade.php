<td>{{ Form::checkbox('row_id[]', $matrix_row_details['matrix_id'], '', array('id'=>'matrixid_'.$matrix_row_details['matrix_id'], 'class'=>'case')) }}</td>
<td>
	<?php
	$attr_val_str = '';
	if(isset($matrix_row_details['attrib_labels'])) {
		foreach($matrix_row_details['attrib_labels'] as $attr_val) {
			$attr_val_str .= ($attr_val_str == '') ? $attr_val : ', '.$attr_val;
		}
	}
	?>
	<span>{{ $attr_val_str }}</span>
</td>
<td>
	@if($matrix_row_details['price'] != 0)
		{{ $matrix_row_details['price'] }}
	@else
		{{ Lang::get('variations::variations.no_change') }}
	@endif
</td>
<td>
	@if($matrix_row_details['show_giftwrap'] == 0)
		{{ Lang::get('variations::variations.not_applicable') }}
	@else
		@if($matrix_row_details['giftwrap_price'] != 0)
			{{ $matrix_row_details['giftwrap_price'] }}
		@else
			{{ Lang::get('variations::variations.no_change') }}
		@endif
	@endif
</td>
<td>
	@if($matrix_row_details['shipping_price'] != 0)
		{{ $matrix_row_details['shipping_price'] }}
	@else
		{{ Lang::get('variations::variations.no_change') }}
	@endif
</td>
<td>
	@if($matrix_row_details['show_stock'] != 0)
		{{ Lang::get('variations::variations.not_applicable') }}
	@else
		{{ $matrix_row_details['stock'] }}
	@endif
</td>
<td>
	@if($matrix_row_details['swap_img_id'] != 0)
        <p id="matrix_swap_img_{{$matrix_row_details['matrix_id']}}" >
        	<?php
        		$this->variations_service = new VariationsService();
				$swap_img_src = $this->variations_service->getSwapImageSrc($matrix_row_details['filename_thumb']);
			?>
        	<img src="{{ $swap_img_src }}" alt="{{$matrix_row_details['title']}}" title="{{$matrix_row_details['title']}}" {{CUtil::DISP_IMAGE(74, 74, $matrix_row_details['t_width'], $matrix_row_details['t_height'])}} />
        </p>
    @else
    	<span id="matrix_no_swap_img_{{$matrix_row_details['matrix_id']}}">No swap image</span>
    @endif

    @if($matrix_row_details['is_active'] == 1)
    	@if($matrix_row_details['swap_img_id'] != 0)
			<a class="remMatSwapImg" alt='{{$matrix_row_details['matrix_id']}}' title="{{ Lang::get('variations::variations.remove') }}">{{ Lang::get('variations::variations.remove') }}</a>
            <span class="text-muted"> | </span>
        @endif
            <a href="{{ $matrix_row_details['changeSwapImgLink'] }}" title="{{ Lang::get('variations::variations.change') }}" class="fn_updSwapImg">{{ Lang::get('variations::variations.change') }}</a>
    @endif
</td>
<td class="action-btn">
	<p>
		@if($matrix_row_details['is_active'] == 1)
            <span onclick="showMatrixUpdate('{{$matrix_row_details['matrix_id']}}')"><a title="{{ Lang::get('variations::variations.edit') }}" class="btn btn-xs blue"><i class="fa fa-edit"></i></a></span>
            <span onclick="doMatrixAction('delete_matrix', '{{$matrix_row_details['matrix_id']}}', '{{$matrix_row_details['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.delete') }}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a></span>
        @endif
        @if($matrix_row_details['is_active'] == 0)
            <span onclick="doMatrixAction('enable_matrix', '{{$matrix_row_details['matrix_id']}}', '{{$matrix_row_details['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.enable') }}" class="btn btn-xs green"><i class="fa fa-check"></i></a></span>
        @else
        	<span onclick="doMatrixAction('disable_matrix', '{{$matrix_row_details['matrix_id']}}', '{{$matrix_row_details['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.disable') }}" class="btn btn-xs btn-info"><i class="fa fa-ban"></i></a></span>
        @endif
    </p>

	@if($matrix_row_details['is_active'] == 1)
        @if($matrix_row_details['is_default'] == 0)
        	<span onclick="doMatrixAction('set_default_matrix', '{{$matrix_row_details['matrix_id']}}', '{{$matrix_row_details['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.set_as_default') }}" class="btn btn-xs green">{{ Lang::get('variations::variations.set_as_default') }}</a></span>
        @else
            <span onclick="doMatrixAction('rem_default_matrix', '{{$matrix_row_details['matrix_id']}}', '{{$matrix_row_details['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.remove_default') }}" class="btn btn-xs red">{{ Lang::get('variations::variations.remove_default') }}</a></span>
        @endif
    @endif
</td>