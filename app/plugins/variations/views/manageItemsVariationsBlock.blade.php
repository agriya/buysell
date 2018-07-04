<div class="portlet-title">
	<h2 class="title-one">{{ Lang::get('variations::variations.product_variation_options_head') }}</h2>
</div>

<div class="portlet-body">
    {{ Form::model($p_details, ['url' => $p_url, 'method' => 'post', 'id' => 'frmItemVarOptList', 'class' => 'form-horizontal margin-left-10']) }}
        <?php
            $sel_var_grp_id ='';
            if(isset($variations_obj)) {
                $sel_var_grp_id = $variations_obj->getSelctedVariationGroupsByUser($p_id);
                $var_grp = $variations_obj->getVariationGroupsByUser(BasicCUtil::getLoggedUserId());
            }
        ?> 
        @if(isset($var_grp) && count($var_grp) > 0 )        	                  
            <div class="form-group">
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
                        @include('variations::manageItemsVariationsAttributesBlock', array($var_resource_options_arr, $var_show_cancel_button))
                    @endif
                </div>
            </div>
        @else 
            <div class="form-group">
				<?php
                    $add_var_grp_msg = Lang::get('variations::variations.add_variation_group_link_msg');
                    $link = "<a href=".URL::to('variations/add-group')." title=".Lang::get('variations::variations.add_variation_group')." target='_blank'> ".Lang::get('variations::variations.click_here_lbl')." </a>";
                    $add_var_grp_msg = str_replace("CLICK_LINK", $link, $add_var_grp_msg);
                ?>	
                
                <div class="col-md-9 add-vargrp">
                    <i class="fa fa-plus"></i> {{ $add_var_grp_msg }}
                </div>  
            </div>
        @endif
    {{ Form::close() }}
</div>