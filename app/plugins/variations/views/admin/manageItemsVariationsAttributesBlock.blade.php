<div class="form-group clearfix">
    <div class="col-md-offset-2 col-md-9">
        <table class="table table-bordered">
             <thead>
                <th>
                    @if(isset($var_resource_options_arr) AND count($var_resource_options_arr) > 0)
                        {{ Form::checkbox('variationlist_ckbox', '', '', array('id'=>'variationlist_sel_ckbox', 'class'=>'') ) }} 
                    @endif
                    <strong>{{ Lang::get('variations::variations.variation_name') }}</strong>
                </th>
                <th>{{ Lang::get('variations::variations.options_head') }}</th>
            </thead>
            
            <tbody>
                @if(isset($var_resource_options_arr) AND count($var_resource_options_arr) > 0)
                    @foreach($var_resource_options_arr as $val)
                        <tr>
                            <td width="350">
                                <div for="variation_{{ $val['variation_id'] }}">
                                    <input type="checkbox" class="varChkOpt case" name="variation_ids[]" id="variation_{{ $val['variation_id'] }}" {{ $val['checked'] }} value="{{ $val['variation_id'] }}" tabindex="" />
                                    {{ $val['name'] }}
                                </div>
                            </td>
                            <td>
                                @if(count($val['options_arr']) > 0)
                                    @foreach($val['options_arr'] as $varOpt)
                                        <div for="attribs_{{$varOpt['attribute_id']}}">
                                            <input type="checkbox" class="varAttrOpt" name="attrib_ids[]" id="attribs_{{$varOpt['attribute_id']}}" {{$varOpt['checked']}} value="attribs_{{$val['variation_id']}}_{{$varOpt['attribute_id']}}" tabindex="" />
                                            {{ $varOpt['attribute_name'] }}
                                        </div>
                                    @endforeach
                                @else
                                    <div class="note note-info">{{ Lang::get('variations::variations.no_options') }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2"><div class="note note-info">{{ Lang::get('variations::variations.no_product_variation') }}</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
	
<div class="form-actions fluid">
    <div class="col-md-offset-2 col-md-9">
        @if(isset($var_resource_options_arr) && count($var_resource_options_arr) > 0)	
            <button name="edit_product" id="edit_product" value="Next" type="submit" class="btn blue">{{ Lang::get('variations::variations.set_variation_values') }}</button>
        @endif
        
        @if(isset($var_show_cancel_button) && $var_show_cancel_button == 1)
            <input type="button" name="cancel_populate" id="cancel_populate" value="Cancel"  class="btn default" />        
        @else
	        <input type="button" name="reset_populate" id="reset_populate" value="Reset"  class="btn default" onclick="window.location.reload();"/>        
        @endif
    </div>
</div>