@extends('adminPopup')
@section('content')
    <h1>{{ trans('admin/manageCategory.view_attribute') }}</h1>
    <div class="pop-content">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ trans('admin/manageCategory.list-attribute.attribute_label') }} :</label>
                <div class="col-sm-5"><p class="form-control-static">{{$attribute_details['attribute_label']}}</p></div>
            </div>
            @if($attribute_details['description'])
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('admin/manageCategory.add-attribute.attribute_description') }} :</label>
                    <div class="col-sm-5"><p class="form-control-static">{{$attribute_details['description']}}</p></div>
                </div>
            @endif
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ trans('admin/manageCategory.list-attribute.attribute_type') }} :</label>
                <div class="col-sm-5 multi-select radio-checkbox">
                    {{$attr_service_obj->getHTMLElement($attribute_details['attribute_question_type'], $attribute_details['attribute_options'], $attribute_details['default_value'])}}
                </div>
            </div>

            @if($attribute_details['validation_rules'])
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('admin/manageCategory.list-attribute.attribute_validation') }} :</label>
                    <div class="col-sm-5"><p class="form-control-static">{{$attribute_details['validation_rules']}}</p></div>
                </div>
            @endif
            @if($attribute_details['default_value'])
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('admin/manageCategory.list-attribute.attribute_default_value') }} :</label>
                    <div class="col-sm-5"><p class="form-control-static">{{$attribute_details['default_value']}}</p></div>
                </div>
            @endif
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ trans('admin/manageCategory.add-attribute.attribute_is_searchable') }} :</label>
                <div class="col-sm-5">
                	<p class="form-control-static">
						<?php
                            $lbl_class = "";
                            if(strtolower ($attribute_details['is_searchable']) == "yes")
                                $lbl_class = "text-success";
                            elseif(strtolower ($attribute_details['is_searchable']) == "no")
                                $lbl_class = "text-danger";
                        ?>
                        <span class="{{ $lbl_class }}">{{ $attribute_details['is_searchable'] }}</span>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ trans('common.status') }} :</label>
                <div class="col-sm-5">
                	<p class="form-control-static">
						<?php
                            $lbl_class = "";
                            if(strtolower ($attribute_details['status']) == "active")
                                $lbl_class = "label-success";
                            elseif(strtolower ($attribute_details['status']) == "inactive")
                                $lbl_class = "label-grey arrowed-in arrowed-in-right";
                        ?>
                        <span class="label {{ $lbl_class }}">{{ $attribute_details['status'] }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop