<div id="attributes_block">
	<!--- CONFIRM DELETE DIALOG BOX STARTS --->
	<div id="dialog-attribute-remove-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span class="show ml15">{{ trans('admin/manageCategory.remove-association.remove_association_confirm') }}</span>
	</div>
	<!--- CONFIRM DELETE DIALOG BOX END --->

	<div class="portlet box blue-hoki attribute-list">
        <!--- TABLE TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ trans('admin/manageCategory.attributes_management_title') }}
            </div>
            <a href="javascript:void(0);" onclick="window.location.href='{{ URL::to('admin/product-attributes')  }}';" title="{{ trans('admin/manageCategory.add_new_attribute') }}" class="btn default purple-stripe btn-xs pull-right responsive-pull-none">
                <i class="fa fa-plus-circle"></i> {{ trans('admin/manageCategory.add_new_attribute') }}
            </a>
            @if($d_arr['category_id'] != $d_arr['root_category_id'] && $attribs_arr['parent'])
                <a href="javascript:void(0);" onclick="displayParentCategoryAttributes();" title="{{ trans('admin/manageCategory.add_new_attribute') }}" id="linkShowParentAttributes" class="clsHide btn default purple-stripe btn-xs pull-right">
                    <i class="fa fa-plus-circle"></i> {{ trans('admin/manageCategory.parent_category_attribute') }}
                </a>
            @endif
        </div>
        <!--- TABLE TITLE END --->

        <div class="portlet-body form">
        	<div class="form-body">
                <!--- INFO STARTS --->
                @if($d_arr['category_id'] != $d_arr['root_category_id'])
                    <?php $cat_name = Products::getCategoryName($d_arr['category_id']); ?>
                    <div class="well"><h4 class="mar0">{{ $cat_name }}</h4></div>
                @else
                    <p class="note note-danger">{{ trans('admin/manageCategory.category_not_selected')}}</p>
                @endif
                <!--- INFO END --->

                <!--- ATTRIBUTES ASSIGNED IN PARENT CATEGORIES STARTS --->
                @if($d_arr['category_id'] != $d_arr['root_category_id'] && $attribs_arr['parent'])
                    <div id="parentAttributesBlock" style="display:none;" class="clearfix">
                        <h4 class="form-section">{{ trans('admin/manageCategory.parent_category_attribute_title') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr class="nodrag nodrop">
                                        <th>{{ trans('admin/manageCategory.list-attribute.attribute_label') }}</th>
                                        <th class="col-md-5">{{ trans('admin/manageCategory.list-attribute.attribute_type') }}</th>
                                        <th>{{ trans('common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attribs_arr['parent'] as $inc => $value)
                                        <tr class="nodrag nodrop">
                                            <td>
                                                {{ $value['attribute_label'] }}
                                            </td>
                                            <td>
                                                {{ $attr_service_obj->getHTMLElement($value['attribute_question_type'], $value['attribute_options'], $value['default_value']) }}
                                            </td>
                                            <td class="formBuilderAction status-btn">
                                                <a class="btn btn-xs btn-info formBuilderRowView" onclick="openViewAttributeFancyBox('{{URL::action('AdminCategoryAttributesController@getViewAttribute')}}?attribute_id={{ $value['attribute_id'] }}')" href="javascript:;" title="{{ trans('admin/manageCategory.view_attribute') }}" id="formBuilderRowView_{{ $value['attribute_id'] }}">
                                                <i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                <!--- ATTRIBUTES ASSIGNED IN PARENT CATEGORIES END --->

                <!--- INFO STARTS --->
                @if($d_arr['category_id'] && $d_arr['category_id'] != $d_arr['root_category_id'])
                    {{ Form::hidden('attr_mgmt_category_id', $d_arr['category_id'], array("id" => "attr_mgmt_category_id")) }}
                    <div class="note note-info">
                        <strong>{{ trans('common.note') }}:</strong> {{ trans('admin/manageCategory.attribute_assign_msg') }}
                    </div>
                @endif
                <!--- INFO END --->

                <!--- INFO STARTS --->
                <div id="ajaxMsgs" class="note note-danger" style="display:none;"></div>
                <div id="ajaxMsgSuccess" class="note note-success" style="display:none;"></div>
                <!--- INFO STARTS --->

                <div id="sample-table-1">
                	<div class="clearfix">
                        <!--- ASSIGNED ATRRIBUTES STARTS --->
                        @if($d_arr['category_id'] && $d_arr['category_id'] != $d_arr['root_category_id'])
                            <h4 class="form-section">{{ trans('admin/manageCategory.assigned_attributes') }}</h4>
                        @endif
                        <div class="table-responsive mb20">
                            <table id="attrdnd" class="formBuilderAssignedTable table table-striped table-bordered table-hover">
                                <thead>
                                    <tr class="nodrag nodrop">
                                        <th>{{ trans('admin/manageCategory.list-attribute.attribute_label') }}</th>
                                        <th class="col-md-5">{{ trans('admin/manageCategory.list-attribute.attribute_type') }}</th>
                                        <th>{{ trans('common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="formBuilderAssignedListBody">
                                    @if($d_arr['category_id'] == $d_arr['root_category_id'])
                                        <tr class="nodrag nodrop">
                                            <td colspan="3">
                                                <p class="alert alert-info">{{ trans('admin/manageCategory.category_not_selected') }}</p>
                                            </td>
                                        </tr>
                                    @else
                                        @if(count($attribs_arr['assigned']) > 0)
                                            @foreach($attribs_arr['assigned'] as $inc => $value)
                                                <tr id="formBuilderRow_{{$value['attribute_id'] }}" class="formBuilderRow formAssignedAttributes">
                                                    <td>
                                                        {{$value['attribute_label'] }}
                                                    </td>
                                                    <td class="multi-select">
                                                        {{ $attr_service_obj->getHTMLElement($value['attribute_question_type'], $value['attribute_options'], $value['default_value']) }}
                                                    </td>
                                                    <td class="formBuilderAction status-btn">
                                                        <a class="btn btn-info btn-xs formBuilderRowView" href="javascript:;" onclick="openViewAttributeFancyBox('{{URL::action('AdminCategoryAttributesController@getViewAttribute')}}?attribute_id={{ $value['attribute_id'] }}')" title="{{ trans('admin/manageCategory.view_attribute') }}" id="formBuilderRowView_{{$value['attribute_id'] }}"><i class="fa fa-eye"></i></a>
                                                        <a class="btn btn-xs red" onclick="javascript:formBuilderRemoveListRow({{$value['attribute_id'] }}, {{$d_arr['category_id']}});" href="javascript: void(0);" title="{{ trans('admin/manageCategory.remove_attribute') }}"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="nodrag nodrop noAttributeAssignedRow">
                                                <td colspan="3">
                                                    <p class="alert alert-info">{{ trans('admin/manageCategory.click_assign_attributes_msg') }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!--- ASSIGNED ATRRIBUTES END --->
                    </div>

                    <!--- UNASSIGNED ATRRIBUTES STARTS --->
                    @if($d_arr['category_id'] && $d_arr['category_id'] != $d_arr['root_category_id'])
                        <h4 class="form-section">{{ trans('admin/manageCategory.unassigned_attributes') }}</h4>
                        <div class="table-responsive">
                            <table class="formBuilderAddedTable table table-striped table-bordered table-hover">
                                <thead>
                                    <tr class="nodrag nodrop">
                                        <th>{{ trans('admin/manageCategory.list-attribute.attribute_label') }}</th>
                                        <th class="col-md-5">{{ trans('admin/manageCategory.list-attribute.attribute_type') }}</th>
                                        <th>{{ trans('common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="formBuilderAddedListBody">
                                    @if(count($attribs_arr['new']) > 0)
                                        @foreach($attribs_arr['new'] as $inc => $value)
                                            <tr id="formBuilderNewRow_{{$value['attribute_id'] }}" class="nodrag nodrop formBuilderAddRow formUnassignedAttributes" title="{{ trans('admin/manageCategory.double_click_assign_attributes_msg') }}">
                                                <td>{{$value['attribute_label'] }}</td>
                                                <td class="multi-select">{{ $attr_service_obj->getHTMLElement($value['attribute_question_type'], $value['attribute_options'], $value['default_value']) }}</td>
                                                <td class="formBuilderAction status-btn">
                                                    <a class="btn btn-xs btn-info formBuilderRowView" onclick="openViewAttributeFancyBox('{{URL::action('AdminCategoryAttributesController@getViewAttribute')}}?attribute_id={{ $value['attribute_id'] }}')" href="javascript:;" title="{{ trans('admin/manageCategory.view_attribute') }}" id="formBuilderRowView_{{$value['attribute_id'] }}"><i class="fa fa-eye"></i></a>
                                                    <a class="btn btn-xs green" onclick="javascript:formBuilderAddListRow({{$value['attribute_id'] }}, {{$d_arr['category_id']}});" href="javascript: void(0);" title="{{ trans('admin/manageCategory.assign_attribute_title') }}"><i class="fa fa-share"></i> </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="nodrag nodrop noAttributeAddedRow">
                                            <td colspan="3">
                                                <p class="alert alert-info">{{ trans('admin/manageCategory.attributes_not_found') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <!--- UNASSIGNED ATRRIBUTES END --->
                </div>
            </div>
        </div>
	</div>
</div>