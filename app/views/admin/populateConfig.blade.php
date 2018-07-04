<section>
    <div class="portlet box blue-madison mt10">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
            <div class="caption"><i class="fa fa-cogs"></i> {{ trans('configManage.config_manage_title') }}</div>
        </div>
		<!-- END: PAGE TITLE -->
		
        <div style="display: block;" class="portlet-body form">
        	<!-- BEGIN: ALERT BLOCK -->
            <div class="@if ((isset($error_msg)) || (isset($success_msg))) pad10 @else pad0 @endif">
                @if (isset($error_msg))
                    <div class="alert alert-danger">{{ $error_msg }}</div>
                @elseif(isset($success_msg))
                    <div class="alert alert-success">{{ $success_msg }}</div>
                @endif
            </div>
            <!-- END: ALERT BLOCK -->
            
            <!-- BEGIN: FORM -->
            {{ Form::open(array('url' => 'admin/config-manage', 'method'=>'post', 'class' => 'form-horizontal text-left tab-content', 'id'=>'form_configedit_'.Input::get('config_category'), 'name'=>'form_configedit_'.Input::get('config_category'))) }}
            {{ Form::hidden('config_category', Input::get('config_category'), array("name" => "config_category", "id" => "config_category"))}}
                @if (count($populate_section_arr) > 0)
                    @foreach($populate_section_arr AS $section_key => $section_val)
                        <?php
                            $section_title = trans('configManage.general');
                            if($section_val['section'] != '')
                            {
                                $section_title =  ucfirst($section_val['section']). ' settings';
                            }
                        ?>
                        <fieldset>
                        	<h4 class="form-section">{{$section_title}}</h4>
                            <div class="form-body">
                                @foreach($section_val['records'] AS $rec_key => $rec_val)
                                    <div class="form-group">
                                        @if($rec_val['config_type'] == 'Boolean')
                                            <label class="col-md-4 control-label no-padding-right">{{$rec_val['description']}}</label>
                                            <div class="col-md-4">
                                                <ul class="list-unstyled chk-box">
                                                    <li class="radio-inline mr30">
                                                        {{ Form::radio($rec_val['config_var'], '1', ($rec_val['config_value'] == '1')?true: false, array('id' => $rec_val['config_var'].'_true', 'name' => $rec_val['config_var'], 'class' => 'ace')) }}
                                                        {{ Form::label($rec_val['config_var'].'_true', trans('common.yes'))}}
                                                    </li>
                                                    <li class="radio-inline">
                                                        {{ Form::radio($rec_val['config_var'], '0', ($rec_val['config_value'] == '0')?true: false, array('id' => $rec_val['config_var'].'_false', 'name' => $rec_val['config_var'], 'class' => 'ace')) }}
                                                        {{ Form::label($rec_val['config_var'].'_false', trans('common.no'))}}
                                                        @if (isset($error_message_arr[$rec_val['config_var']]) && $error_message_arr[$rec_val['config_var']] != '')
                                                            <label class="error">{{$error_message_arr[$rec_val['config_var']]}}</label>
                                                        @endif
                                                    </li>
                                               </ul>
                                            </div>
                                        @else
                                           {{ Form::label($rec_val['config_var'], $rec_val['description'], array('class' => 'col-md-4 control-label')) }}
                                            <div class="col-md-4">
                                                {{ Form::text($rec_val['config_var'], $rec_val['config_value'] , array('name' => $rec_val['config_var'], 'id' => $rec_val['config_var'], 'class' => 'form-control text-ellipsis'))}}
                                            </div>
                                            @if (isset($error_message_arr[$rec_val['config_var']]) && $error_message_arr[$rec_val['config_var']] != '')
                                                <label class="error">{{$error_message_arr[$rec_val['config_var']]}}</label>
                                            @endif
                                        @endif
                                    </div>
                                 @endforeach
	                        </div>                        
                        </fieldset>
                    @endforeach
                    <div class="form-actions fluid config-input">
                        <div class="col-md-offset-4 col-md-7">
                            {{ Form::hidden('act', '', array("name" => "act", "id" => "act_".Input::get('config_category')))}}
                            {{ Form::submit(trans('configManage.update_submit'), array('name' => 'config_update', 'id' => 'config_update', 'onclick' => "return updateConfig('".Input::get('config_category')."','form_configedit_".Input::get('config_category')."', 'ui-tabs-".$tab_list_arr[Input::get('config_category')]."');", 'class'=>"btn blue")) }}
	                    </div>
                    </div>
               @else
                   <div class="pad10"><p class="alert alert-info mar0">{{trans("common.no_settings")}}</p></div>
               @endif
            {{ Form::close() }}
            <!-- END: FORM -->
		</div>
	</div>
</section>