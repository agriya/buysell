<div class="portlet bg-form">
    <div class="portlet-title">
        <h2>{{ Lang::get('variations::variations.item_edit_variation_head_label') }}</h2>
    </div>

    <div class="portlet-body">
        @if($item_matrix_details_arr['multiple_edit'] == 1)
            <div class="note note-info">{{ Lang::get('variations::variations.item_matrix_multiple_update_label') }}</div>
            <div class="form-group">
                <label class="control-label col-md-2">{{ Lang::get('variations::variations.attribute') }}</label>
                <ul class="list-unstyled row col-md-9">
		            <?php
						$attr_val_str = '';
						if(isset($item_matrix_details_arr['matrix_id'])) {
							$matrix_ids = explode(',', $item_matrix_details_arr['matrix_id']);
							foreach($matrix_ids as $matrix_id_val) {
								$val = DB::table('item_var_matrix_details')->where('matrix_id', $matrix_id_val)->first();
								$attr_val_str .= ($attr_val_str == '') ? $val->content : ', '.$val->content;
							}
						}
						$attr_label_name = explode(',', $attr_val_str);
			        ?>
			        @foreach($attr_label_name as $attr_label)
	         			<li class="col-md-6 margin-top-10">{{ str_replace('#@@#', ', ', $attr_label) }}</li>
	         		@endforeach
                </ul>
            </div>
        @else
            <div class="form-group">
                <label class="control-label col-md-2">{{ Lang::get('variations::variations.attribute') }}</label>
                <div class="col-md-3">
                    <?php
						$attr_val_str = '';
						if(isset($item_matrix_details_arr['attrib_labels'])) {
							foreach($item_matrix_details_arr['attrib_labels'] as $attr_val) {
								$attr_val_str .= ($attr_val_str == '') ? $attr_val : ', '.$attr_val;
							}
						}
                    ?>
                    <p class="margin-top-8">{{ $attr_val_str }}</p>
                </div>
            </div>
        @endif

        @if($item_matrix_details_arr['edit_field'] == 'all' OR $item_matrix_details_arr['edit_field'] == 'edit_price')
            <div class="form-group">
                <label for="matrix_price_impact" class="control-label col-md-2">{{ Lang::get('variations::variations.price') }}</label>
                <div class="col-md-4 row">
                	<div class="col-md-7 margin-bottom-5">
                    	{{ Form::select('matrix_price_impact', $item_matrix_details_arr['update_block_select_action_arr'], $item_matrix_details_arr['price_impact'], array('class' => 'form-control', 'id' => 'matrix_price_impact')); }}
                        <small class="text-muted"> {{ Lang::get('common.from_default_price') }} </small>
                    </div>
                    <div class="col-md-5">
                        <span @if($item_matrix_details_arr['price_impact'] == '') style="display:none"@endif>
                            <?php $matrix_price = str_replace("-","",$item_matrix_details_arr['price']); ?>
                            <input type="text" class="form-control giftwrapprice" name="matrix_price" id="matrix_price" tabindex="" value="{{ $matrix_price }}" maxlength="6"/>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        @if($item_matrix_details_arr['show_giftwrap'] == 1)
            @if($item_matrix_details_arr['edit_field'] == 'all' OR $item_matrix_details_arr['edit_field'] == 'edit_gift_wrapprice')
                <div class="form-group">
                    <label for="matrix_giftwrap_price_impact" class="control-label col-md-2">{{ Lang::get('variations::variations.giftwrap_pricing') }}</label>
                    <div class="col-md-4 row">
                        <div class="col-md-7 margin-bottom-5">
                            {{ Form::select('matrix_giftwrap_price_impact', $item_matrix_details_arr['update_block_select_action_arr'], $item_matrix_details_arr['giftwrap_price_impact'], array('class' => 'form-control', 'id' => 'matrix_giftwrap_price_impact')); }}
                            <small class="text-muted"> {{ Lang::get('common.from_default_gift_wrap_price') }} </small>
                        </div>
                        <div class="col-md-5">
                            <span @if($item_matrix_details_arr['giftwrap_price_impact'] == '') style="display:none"@endif>
                                <?php $matrix_giftwrap_price = str_replace("-","",$item_matrix_details_arr['giftwrap_price']); ?>
                                <input type="text" class="form-control giftwrapprice" name="matrix_giftwrap_price" id="matrix_giftwrap_price" tabindex="" value="{{ $matrix_giftwrap_price }}" maxlength="6"/>
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if($item_matrix_details_arr['edit_field'] == 'all' OR $item_matrix_details_arr['edit_field'] == 'edit_shipping_fee')
            <div class="form-group">
                <label for="matrix_shippingfee_impact" class="control-label col-md-2">{{ Lang::get('variations::variations.shipping_fee') }}</label>
                <div class="col-md-4 row">
                        <div class="col-md-7 margin-bottom-5">
                    		{{ Form::select('matrix_shippingfee_impact', $item_matrix_details_arr['update_block_select_action_arr'], $item_matrix_details_arr['shipping_price_impact'], array('class' => 'form-control', 'id' => 'matrix_shippingfee_impact')); }}
                            <small class="text-muted"> {{ Lang::get('common.from_default_shipping_price') }} </small>
        				</div>
                        <div class="col-md-5">
                            <span @if($item_matrix_details_arr['shipping_price_impact'] == '') style="display:none"@endif >
                                <?php $matrix_shippingfee = str_replace("-","",$item_matrix_details_arr['shipping_price']); ?>
                                <input type="text" class="form-control giftwrapprice" name="matrix_shippingfee" id="matrix_shippingfee" tabindex="" value="{{ $matrix_shippingfee }}" maxlength="6"/>
                            </span>
                        </div>
                	</div>
                </div>
            </div>
        @endif

        @if($item_matrix_details_arr['show_stock'] == 1)
            @if($item_matrix_details_arr['edit_field'] == 'all' OR $item_matrix_details_arr['edit_field'] == 'edit_stock')
                <div class="form-group">
                    <label for="matrix_stock" class="control-label col-md-2">{{ Lang::get('variations::variations.stock') }}</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control fn_varstock" name="matrix_stock" id="matrix_stock" tabindex="" value="{{ $item_matrix_details_arr['stock'] }}" maxlength="10" />
                        <small class="text-muted"> {{ Lang::get('variations::variations.stock_variation_note') }}</small>
                    </div>
                </div>
            @endif
        @endif

        @if($item_matrix_details_arr['multiple_edit'] != 1)
            <input type="hidden" name="matrix_desc" id="matrix_desc" value="{{ $attr_val_str }}" />
        @endif

        @if($item_matrix_details_arr['multiple_edit'] == 0 AND ( $item_matrix_details_arr['edit_field'] == 'all' OR $item_matrix_details_arr['edit_field'] == 'edit_swap_image'))
            <div class="form-group">
                <label class="control-label col-md-2">{{ Lang::get('variations::variations.swap_image') }}</label>
                <div class="col-md-4">
                    <input type="hidden" name="matrix_swap_img_id" id="matrix_swap_img_id" value="{{ $item_matrix_details_arr['swap_img_id'] }}" />
                    @if($item_matrix_details_arr['swap_img_id'] == 0)
                        <img id="item_swap_image_id" src="{{ $item_matrix_details_arr['swap_img_no_image_folder'] }}/prodnoimage-215x170.jpg" alt="No image" title="No image" }} />
                    @else
                        <img id="item_swap_image_id" src="{{ $item_matrix_details_arr['swap_img_folder'] }}/{{ $item_matrix_details_arr['filename_thumb'] }}" alt="{{ $item_matrix_details_arr['title'] }}" title="{{ $item_matrix_details_arr['title'] }}" />
                    @endif

                    <div class="clearfix margin-top-3">
                        <small @if($item_matrix_details_arr['swap_img_id'] == 0) style="display:none" @endif>
                            <a id="remSwapImg" class="text-danger" alt='{{ $item_matrix_details_arr['matrix_id'] }}'>{{ Lang::get('variations::variations.remove') }}</a>
                            <span class="text-muted"> | </span>
                        </small>
                        <small>
                            <a onclick="selSwapImageOpen('{{ $item_matrix_details_arr['changeSwapImgLink'] }}')"  href="javascript:void(0);"  title="{{ Lang::get('variations::variations.item_change_swap_image_lbl') }}" class="text-primary" >{{ Lang::get('variations::variations.change') }}</a>
                        </small>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-group">
            <div class="col-md-offset-2 col-md-9">
                <input type="hidden" value="{{ $item_matrix_details_arr['matrix_id'] }}" id="matrix_id" name="matrix_id" >
                <input type="hidden" value="{{ $item_matrix_details_arr['edit_field'] }}" id="edit_field" name="edit_field" >
                <input type="hidden" value="{{ $item_matrix_details_arr['multiple_edit'] }}" id="mul_mat_edit" name="mul_mat_edit" >
                <input type="button" value="Update" tabindex="" id="update_matrix_det" name="update_matrix_det" class="btn btn-sm blue">
                <input type="button" value="Cancel" tabindex="" id="cancel_update_matrix" name="cancel_update_matrix" class="btn btn-sm default">
            </div>
        </div>
    </div>
</div>

<script language="javascript" type="text/javascript">
	$(document).ready(function()
	{
		$('#selSwapImage').fancybox({
	        maxWidth    : 800,
	        maxHeight   : 630,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none',
		});

	$(".price, .giftwrapprice").live("paste", function (e) {
        return false;
	   });
	    $(".price, .giftwrapprice").live("drop", function (e) {
	        return false;
	    });
	    $(".price, .giftwrapprice").live("blur", function (e) {
	    	if (this.value != '') {
				this.value = parseFloat(this.value).toFixed(2);
	        }
	    });

		$(".giftwrapprice").live("keypress", function (e) {
	        var keyCode = e.which ? e.which : e.keyCode
	        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
	        return ret;
	    });
	});
</script>