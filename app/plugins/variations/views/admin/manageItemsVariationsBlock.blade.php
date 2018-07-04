<div class="portlet box blue-madison">
    <div class="portlet-title">
        <div class="caption">{{ Lang::get('variations::variations.product_variation_options_head') }}</div>
    </div>
    
    <div class="portlet-body form">
        {{ Form::model($p_details, ['url' => $p_url, 'method' => 'post', 'id' => 'frmItemVarOptList', 'class' => 'form-horizontal margin-left-10']) }}
            <?php
                $sel_var_grp_id ='';
                if(isset($variations_obj)) {
                    $sel_var_grp_id = $variations_obj->getSelctedVariationGroupsByUser($p_id);
                    $var_grp = $variations_obj->getVariationGroupsByUser($p_details['product_user_id']);
                }
            ?>                 
            @if(isset($var_grp) && count($var_grp) > 0 )
                <div class="form-group pad-t20">
                    <label class="control-label col-md-2">{{ Lang::get('variations::variations.variation_group') }}</label>
                    <div class="col-md-4">
                        <?php
                            $default_arr = array('' => Lang::get('variations::variations.select_variation_group_option'));
                            $var_grp_all = $default_arr + $var_grp;
                        ?>
                        {{ Form::select('prd_var_grp', $var_grp_all, Input::old('prd_var_grp', $sel_var_grp_id), array('class' => 'form-control', 'id' => 'prd_var_grp')); }}
                    </div>
                </div>
            
                <div class="vargrp-selectbox">
                    {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                    {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                    <div id="regenVarGrp">
                        @if($sel_var_grp_id > 0)
                            <?php
                                $result_arr = $variations_obj->populateVariationsInGroupByGroupId($sel_var_grp_id, $p_id);
                                $var_resource_options_arr = $result_arr['var_resource_options_arr'];
                                $var_show_cancel_button = $result_arr['var_show_cancel_button'];
                            ?>
                            @include('variations::admin.manageItemsVariationsAttributesBlock', array($var_resource_options_arr, $var_show_cancel_button))
                        @endif
                    </div>
                </div>
            @else
	            <div class="alert alert-info mar0">{{ Lang::get('variations::variations.no_variation_list') }}</div>
            @endif   
        {{ Form::close() }}
    </div>
</div>