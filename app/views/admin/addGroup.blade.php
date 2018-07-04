@extends('admin')
@section('content')
     <!--- ERROR INFO STARTS --->
    @if (Session::has('error') && Session::get('error') != "")
        <div class="note note-danger">{{	Session::get('error') }}</div>
    @endif
    <!--- ERROR INFO END --->

    @if(count($group_details) == 0 || !isset($group_details['id']) || $group_details['id'] == '')
        {{ Form::model($group_details, array('url' => 'admin/group/add', 'method'=>'post', 'class' => 'form-horizontal', 'role' => 'form', 'id'=>'group_add')) }}
    @else
        {{ Form::model($group_details, array('url' => 'admin/group/edit', 'method'=>'post', 'class' => 'form-horizontal', 'role' => 'form', 'id'=>'group_add')) }}
    @endif
    
    {{ Form::hidden('id', null, array('class' => 'form-control')) }}
        <div class="portlet box blue-hoki">
            <!--- TITLE STARTS --->
            <div class="portlet-title">
                <div class="caption">
                    @if(count($group_details) == 0)
                        <i class="fa fa-plus-circle"></i> {{trans('admin/manageGroups.create_group')}}
                    @else
                        <i class="fa fa-edit"></i> {{trans('admin/manageGroups.update_group')}}
                    @endif
                </div>
                <a href="{{url('admin/group')}}" class="btn default btn-xs purple-stripe pull-right responsive-pull-none"><i class="fa fa-chevron-left"></i> {{trans('admin/manageGroups.back_to_group_list')}}</a>
            </div>
            <!--- TITLE END --->

            <div class="portlet-body form">
                <!--- ADD MEMBER BASIC DETAILS STARTS --->
                <div class="form-body">
                    <div class="form-group {{{ $errors->has('template_name') ? 'error' : '' }}}">
                        {{ Form::label('group_name', trans('admin/manageGroups.group_name').'  ', array('class' => 'col-md-3 control-label required-icon')) }}
                        <div class="col-md-4">
                            {{ Form::text('group_name', $group_details['group_name'], array('class' => 'form-control')) }}
                            <label id="group_name_error" for="group_name" generated="true" class="error disp-block">{{{ $errors->first('group_name') }}}</label>
                        </div>
                    </div>
                    <label for="actions[]" generated="true" class="error"></label>
                </div>
                <div class="form-actions fluid">
                    <div class="col-md-offset-3 col-md-8">
                        @if(count($group_details) == 0 || (isset($group_details['group_name']) && $group_details['group_name'] == ''))
                            <button type="submit" class="btn green"><span class="fa fa-plus"></span> {{trans('admin/manageGroups.add')}}</button>
                        @else
                            <button type="submit" class="btn green"><span class="fa fa-arrow-up"></span> {{trans('admin/manageGroups.update')}}</button>
                        @endif
                        <a href="{{url('admin/group')}}" id="select_all" class="btn default"><span class="fa fa-times"></span> {{trans('admin/manageGroups.cancel')}}</a>
                    </div>
                </div>
                <!--- ADD MEMBER BASIC DETAILS END --->
            </div>
        </div>
    {{ Form::close() }}

	<script type="text/javascript">
        var page_name = "group";
    </script>
@stop