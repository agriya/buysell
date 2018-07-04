<!-- to display item price details -->
<div id="dialog-variation-tooltip" title="" style="display:none;">
	<p id="tooltipMsg"></p>
</div>

<div id="saleprice">
    <div id="selSwapImgErrMsg" style="display:none;"><p id="swapImgErrMsg" class="note note-danger"></p></div>
    <div id="variationGroup" class="form-horizontal">
        <fieldset>
    		@foreach($d_arr['variation_det']['variation_list'] AS $varkey => $vars)
                <div class="form-group">
            		<label class="col-md-5 control-label">
                    	{{ $vars['name'] }}
                        @if($vars['help_text'] != '')
                            <a class="fn_VariationDescToolTip"><strong><i class="fa fa-question-circle" title=""></i></strong></a>
                            <div style="display:none; position:absolute;" class="fn_helpText var-helptxt">{{ $vars['help_text'] }}</div>
                        @endif
                    </label>
                    <div class="col-md-6">
                        <select class="form-control" name="item_variations" id="item_variations_{$varkey}" onchange="getDetails()">
                            @foreach($vars['attrirb_det'] as $varattribs)
                                <option value="{{ $varattribs['attribute_id'] }}" @if($varattribs['high_light'] == 1) selected="selected" @endif>{{ $varattribs['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    		@endforeach
            <div id="varStock" class="alert alert-info"></div>

            <!-- <div class="form-group">
                <div class="col-md-offset-5 col-md-7">
                    <input type="button" name="preview" id="preview" value="Preview" class="btn blue-madison" />
                </div>
            </div> -->
        </fieldset>
    </div>

    <input type="hidden" name="var_preview_img_src" id="var_preview_img_src" value="" />
    <input type="hidden" name="var_preview_img_dim" id="var_preview_img_dim" value="" />
    <input type="hidden" name="var_org_img_src" id="var_org_img_src" value="" />
    <input type="hidden" name="var_img_title" id="var_img_title" value="" />
    <input type="hidden" name="var_thumb_img_src" id="var_thumb_img_src" value="" />
</div>