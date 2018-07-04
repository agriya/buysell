@if(isset($attribs) && count($attribs) > 0)
    @foreach( $attribs as $attrKey => $attr_values )
        <div class="clsAddVariationInput">
            <div class="row margin-bottom-10 bg-form portlet">                
                <div class="col-md-5 col-sm-5 col-xs-5">
                    <div class="input-group">
                        <span class="input-group-addon">{{ Lang::get('variations::variations.key') }}</span>
                        {{ Form::text('option_key[]', $attr_values['option_key'], array('class'=>'fn_splblk form-control', 'maxlength'=>'100')) }}
                        <label class="error">{{{ $errors->first('option_key') }}}</label>
                    </div>
                </div>
                <div class="col-md-5 col-sm-5 col-xs-5">
                    <div class="input-group">
                        <span class="input-group-addon">{{ Lang::get('variations::variations.label') }}</span>
                        {{ Form::text('option_label[]', $attr_values['option_label'], array('class'=>'form-control', 'maxlength'=>'100')) }}
                        <label class="error">{{{ $errors->first('option_label') }}}</label>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <button type="button" name="add_variation_option" class="btn green ClsBtnAddOption" title="{{ Lang::get('variations::variations.add') }}">
                        <i class="fa fa-plus-circle"></i>
                    </button>
                    <button type="button" name="remove_variation_option" class="btn red ClsBtnRemoveOptions" title="{{ Lang::get('variations::variations.remove') }}">
                        <i class="fa fa-minus-circle"></i>
                    </button>
                </div>
            
            </div>
        </div>
    @endforeach
@elseif(isset($attributes_arr) && count($attributes_arr) > 0)
	@foreach( $attributes_arr as $attr_values )
		<div class="clsAddVariationInput">
			<div class="row margin-bottom-10 bg-form portlet">
				<div class="col-md-5 col-sm-5 col-xs-4">
					<div class="input-group">
						<span class="input-group-addon">{{ Lang::get('variations::variations.key') }}</span>
						{{ Form::text('option_key[]', $attr_values['attribute_key'], array('id'=>"option_key_".$attr_values['attribute_id'], 'class'=>'fn_splblk form-control', 'maxlength'=>'100')) }}
                        <label class="error">{{{ $errors->first('option_key') }}}</label>
					</div>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-4">
					<div class="input-group">
						<span class="input-group-addon">{{ Lang::get('variations::variations.label') }}</span>
						{{ Form::text('option_label[]', $attr_values['label'], array('id'=>"option_label_".$attr_values['attribute_id'], 'class'=>'form-control', 'maxlength'=>'100')) }}
                        <label class="error" for="option_label_".$attr_values['attribute_id']>{{{ $errors->first('option_label') }}}</label>
					</div>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-4">
					<button type="button" name="add_variation_option" class="btn green ClsBtnAddOption" title="{{ Lang::get('variations::variations.add') }}">
						<i class="fa fa-plus-circle"></i>
					</button>
					<button type="button" name="remove_variation_option" class="btn red ClsBtnRemoveOptions" title="{{ Lang::get('variations::variations.remove') }}">
						<i class="fa fa-minus-circle"></i>
					</button>
				</div>
			</div>
		</div>
	@endforeach
@else	
    <div class="clsAddVariationInput">
        <div class="row margin-bottom-10 bg-form portlet">
            <div class="col-md-5 col-sm-5 col-xs-5">
                <div class="input-group">
                    <span class="input-group-addon">{{ Lang::get('variations::variations.key') }}</span>
                    {{ Form::text('option_key[]', '', array('class'=>'fn_splblk form-control', 'maxlength'=>'100')) }}
                    <label class="error">{{{ $errors->first('option_key') }}}</label>
                </div>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-5">
                <div class="input-group">
                    <span class="input-group-addon">{{ Lang::get('variations::variations.label') }}</span>
                    {{ Form::text('option_label[]', '', array('class'=>'form-control', 'maxlength'=>'100')) }}
                    <label class="error">{{{ $errors->first('option_label') }}}</label>
                </div>
            </div>
            <div class="col-md-2 col-sm-2 col-xs-2">
                <button type="button" name="add_variation_option" class="btn green ClsBtnAddOption" title="{{ Lang::get('variations::variations.add') }}">
                    <i class="fa fa-plus-circle"></i>
                </button>
                <button type="button" name="remove_variation_option" class="btn red ClsBtnRemoveOptions" title="{{ Lang::get('variations::variations.remove') }}">
                    <i class="fa fa-minus-circle"></i>
                </button>
            </div>
        </div>
    </div>    
@endif