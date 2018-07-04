<div id="matrixListBlock" class="row">
	<div class="col-md-12">
		@if(isset($d_arr['matrix_options_arr']) && count($d_arr['matrix_options_arr']) > 0)
			{{ Form::open(array('action' => array('ProductAddController@postEdit'), 'id'=>'frmItemMatrixUpdate', 'method'=>'post','class' => 'form-horizontal')) }}
				{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
				{{ Form::hidden('p', 'variations', array('id' => 'p')) }}
				{{ Form::hidden('sel_matrix_id', null, array('id' => 'sel_matrix_id')) }}
				<div id="matrixUpdateBlock">
			        <!--  this block will load the matrix details when click on edit -->
			    </div>
			{{ Form::close() }}
			
			<div class="clearfix">
			    <a onclick="return showGrp();" class="btn blue pull-right mb10">{{ Lang::get('variations::variations.change_variation_group_head') }}</a>
            </div>
            
            <p class="margin-top-8 alert alert-info">
                <span class="required-icon"></span> 
                <span>{{ Lang::get('variations::variations.attribute_order_by_note') }}:&nbsp; <strong>{{ $d_arr['head_label_str'] }}</strong></span><br />
                <span> {{ Lang::get('variations::variations.stock_variation_note') }}</span> 
            </p>
				
			<!-- BEGIN: INVOICE LIST -->
            {{ Form::open(array('action' => array('AdminProductAddController@postEdit'), 'name' => 'selFormMatrix', 'id'=>'selFormMatrix', 'method'=>'post', 'class' => 'form-horizontal' )) }}
                <div class="tabbable tabbable-custom">						
					<?php	
                        //echo "<pre>"; print_r($d_arr['matrix_options_arr']); exit;
                    ?>
                    <div id="current_table_id">
                        @if(count($d_arr['matrix_options_arr']) <= 0)
                            <div class="note note-info margin-0">No results</div>
                        @else
                            <div class="table-responsive margin-bottom-20">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="40">{{ Form::checkbox('variationlist_ckbox', '', '', array('id'=>'variationlist_ckbox', 'class'=>'checkbox') ) }}</th>
                                            <th>{{ Lang::get('variations::variations.attribute') }} ({{ $d_arr['head_label_str'] }})</th>
                                            <th>{{ Lang::get('variations::variations.price') }}</th>
                                            <th>{{ Lang::get('variations::variations.giftwrap_pricing') }}</th>
                                            <th>{{ Lang::get('variations::variations.shipping_fee') }}</th>
                                            <th>{{ Lang::get('variations::variations.stock') }}</th>
                                            <th>{{ Lang::get('variations::variations.swap_image') }}</th>
                                            <th>{{ Lang::get('variations::variations.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($d_arr['matrix_options_arr'] as $key => $val)
                                            <tr id="matrix_{{$val['matrix_id']}}">
                                                <td>
                                                	{{ Form::checkbox('matrix_ids[]', $val['matrix_id'], '', array('id'=>'matrixid_chkbox_'.$val['matrix_id'], 'class'=>'case')) }}
                                                </td>
                                                <td>
                                                    <?php
                                                    $attr_val_str = '';
                                                    if(isset($val['attrib_label'])) {
                                                        foreach($val['attrib_label'] as $attr_val) {
                                                            $attr_val_str .= ($attr_val_str == '') ? $attr_val : ', '.$attr_val;
                                                        }
                                                    }
                                                    ?>
                                                    <span>{{ $attr_val_str }}</span>
                                                </td>
                                                <td>
                                                    @if($val['price'] != 0)
                                                        {{ $val['price'] }}
                                                    @else
                                                        {{ Lang::get('variations::variations.no_change') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($val['show_giftwrap'] == 0)
                                                        {{ Lang::get('variations::variations.not_applicable') }}
                                                    @else
                                                        @if($val['giftwrap_price'] != 0)
                                                            {{ $val['giftwrap_price'] }}
                                                        @else
                                                            {{ Lang::get('variations::variations.no_change') }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($val['shipping_price'] != 0)
                                                        {{ $val['shipping_price'] }}
                                                    @else
                                                        {{ Lang::get('variations::variations.no_change') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($val['show_stock'] == 0)
                                                        {{ Lang::get('variations::variations.not_applicable') }}
                                                    @else
                                                        {{ $val['stock'] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($val['swap_img_id'] != 0)
                                                        <p id="matrix_swap_img_{{$val['matrix_id']}}" >
                                                            <?php
                                                                $this->variations_service = new VariationsService();
                                                                $swap_img_src = $this->variations_service->getSwapImageSrc($val['filename_thumb']);
                                                            ?>
                                                            <img src="{{$swap_img_src}}" alt="{{$val['title']}}" title="{{$val['title']}}" {{CUtil::DISP_IMAGE(74, 74, $val['t_width'], $val['t_height'])}} />
                                                        </p>
                                                    @else
                                                        <span id="matrix_no_swap_img_{{$val['matrix_id']}}">{{ Lang::get('variations::variations.no_swap_image') }}</span>
                                                    @endif

                                                    @if($val['is_active'] == 1)
                                                        @if($val['swap_img_id'] != 0)
                                                        	<a class="remMatSwapImg" alt='{{$val['matrix_id']}}' title="Remove">{{ Lang::get('variations::variations.remove') }}</a>
                                                            <span class="text-muted"> | </span>
                                                        @endif
                                                        <a href="{{ $val['changeSwapImgLink'] }}" title="Change" class="fn_updSwapImg">{{ Lang::get('variations::variations.change') }}</a>
                                                    @endif
                                                </td>
                                                <td class="action-btn">
                                                    @if($val['is_active'] == 1)
                                                        <span onclick="showMatrixUpdate('{{$val['matrix_id']}}')"><a title="{{ Lang::get('variations::variations.edit') }}" class="btn btn-xs blue"><i class="fa fa-edit"></i></a></span>
                                                        <span onclick="doMatrixAction('delete_matrix', '{{$val['matrix_id']}}', '{{$val['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.delete') }}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a></span>
                                                    @endif
                                                    
                                                    @if($val['is_active'] == 0)
                                                        <span onclick="doMatrixAction('enable_matrix', '{{$val['matrix_id']}}', '{{$val['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.enable') }}" class="btn btn-xs green"><i class="fa fa-check"></i></a></span>
                                                    @else
                                                        <span onclick="doMatrixAction('disable_matrix', '{{$val['matrix_id']}}', '{{$val['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.disable') }}" class="btn btn-xs btn-info"><i class="fa fa-ban"></i></a></span>
                                                    @endif

                                                    @if($val['is_active'] == 1)
                                                        <div class="mt8">
                                                            @if($val['is_default'] == 0)
                                                                <span onclick="doMatrixAction('set_default_matrix', '{{$val['matrix_id']}}', '{{$val['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.set_as_default') }}" class="btn btn-xs green">{{ Lang::get('variations::variations.set_as_default') }}</a></span>
                                                            @else
                                                                <span onclick="doMatrixAction('rem_default_matrix', '{{$val['matrix_id']}}', '{{$val['attribute_id']}}')"><a title="{{ Lang::get('variations::variations.remove_default') }}" class="btn btn-xs red">{{ Lang::get('variations::variations.remove_default') }}</a></span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-3">
                            {{ Form::select('select_action', $d_arr['select_action'], Input::old('select_action', ''), array('class' => 'form-control select2me', 'id' => 'select_action')); }}
                        </div>
                        <button name="matrix_variationitem_list_submit" id="matrix_variationitem_list_submit" value="Submit" type="button" class="btn green">Submit</button>
                    </div>
                </div>
            {{ Form::close() }}
			<!-- END: INVOICE LIST -->
		@endif
	</div>
</div>

<script type="text/javascript">
	/*
	$(function(){
		$("#variationlist_ckbox").click(function () {
			$('.case').prop('checked', this.checked);
		});

		$(".case").click(function(){
			if($(".case").length == $(".case:checked").length) {
				$("#variationlist_ckbox").prop("checked", "checked");
			} else {
				$("#variationlist_ckbox").removeAttr("checked");
			}
		});
	});
	*/
	$(document).ready(function(){
		$("#matrix_variationitem_list_submit").click(function()
		{
			multipleEditSelected($(this));
		});
	
		/* funtion called when action submitted from the matrix listing for edit */
		function multipleEditSelected(obj)
		{
			var selected_button = $(this).attr('id');
			var selected_action = $('#select_action').val();
	
			if(selected_action == '')
			{
				$("#dialog-confirm-content").html(matrix_select_action);
				$("#dialog-confirm").dialog({
					title: matrix_edit_head,
					modal: true,
					buttons: [{
						text: common_ok,
						click: function() { $(this).dialog("close");     }
					}]
				});
			}
			else if($("#selFormMatrix input[type=checkbox]:checked").length == 0 ||
				($("#selFormMatrix input[type=checkbox]:checked").length == 1 && $("#checkall").attr('checked')))
			{
				$("#dialog-confirm-content").html(selection_none_err);
				$("#dialog-confirm").dialog({
					title: matrix_edit_head,
					modal: true,
					buttons: [{
						text: common_ok,
						click: function()
						{
							 $(this).dialog("close");
						}
					}]
				});
			}
			else
			{
				$("#dialog-confirm-content").html(matrix_edit_content);
				$("#dialog-confirm").dialog({
					title: matrix_edit_head, modal: true,
					buttons: [{	text: common_ok, click: function(){
									 $(this).dialog("close");
									$('#item_action').val(selected_action);
									showMatrixUpdate('multiple', selected_action);
								}
							},{text: common_cancel,click:function(){	$(this).dialog("close");	}
						}]
				});
			}
			return false;
		}
	});
</script>