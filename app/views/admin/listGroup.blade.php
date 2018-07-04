@extends('admin')
@section('content')
    <!--<a href="{{url('admin/group/add')}}" class="btn btn-xs btn-primary pull-right mt10"><i class="fa fa-plus"></i> {{trans('admin/manageGroups.create_group')}}</a>
    <h1 class="page-title">{{trans('admin/manageGroups.group_list')}}</h1>-->
     <!--- ERROR INFO STARTS --->
    @if (Session::has('error') && Session::get('error') != "")
        <div class="note note-danger">{{ Session::get('error') }}</div>
    @endif
    <!--- ERROR INFO END --->

     <!--- ERROR INFO STARTS --->
    @if (Session::has('success') && Session::get('success') != "")
        <div class="note note-success">{{ Session::get('success') }}</div>
    @endif
    <!--- ERROR INFO END --->

    @if(count($group_array) > 0)
        {{ Form::open(array('url' => 'admin/group/delete', 'method'=>'post', 'id'=>'group_list')) }}
        	<div class="portlet box blue-hoki">
                <!--- TABLE TITLE STARTS --->
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i> {{trans('admin/manageGroups.group_list')}}
                    </div>
                    <a href="{{url('admin/group/add')}}" class="btn default purple-stripe btn-xs pull-right"><i class="fa fa-plus-circle"></i> {{trans('admin/manageGroups.create_group')}}</a>
                </div>
                <!--- TABLE TITLE END --->

                <div class="portlet-body">
                    <div id="fn_groupDelete" style="display:none" class="form-group">
                        <button type="submit" name="delete" id="delete_id" class="btn btn-sm btn-danger" onclick="return confirmationMsgGroup('group_list','delete', '')"><i class="fa fa-trash-trash-o"></i> Delete</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>{{trans('admin/manageGroups.group_name')}}</th>
                                    <th>{{trans('admin/manageGroups.number_of_user')}}</th>
                                    <th>{{trans('admin/manageGroups.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group_array as $key => $values)
                                    <tr>
                                        <td>{{$values['name']}}</td>
                                        <td>
                                            <strong>
                                                @if(isset($userCountArray[$values['id']]) && $userCountArray[$values['id']] > 0)
                                                    <a id="fn_mailerSettingsList" href="{{url('admin/group/list-group-members?groupId='.$values['id'])}}"> {{$userCountArray[$values['id']]}}</a>
                                                @else
                                                	0
                                                @endif
                                            </strong>
                                        </td>
                                        <td class="status-btn">
                                            @if($values['super_admin'] == 0)
                                                <a class="btn btn-xs blue" href="{{url('admin/group/edit?id='.$values['id'])}}" title="{{trans('admin/manageGroups.edit')}}"><i class="fa fa-edit"></i> </a>
                                                <a class="btn red btn-xs" id="single_delete_{{$values['id']}}" href="javascript:void(0)" onclick="return deleteAction({{$values['id']}}, 'delete')" title="{{trans('admin/manageGroups.delete')}}"><i class="fa fa-trash-o"></i></a><!--href="{{url('admin/group/delete?id='.$values['id'])}}" -->
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        {{Form::close()}}
    @endif
    <div id="dialog-product-confirm" title="" style="display:none;">
        <span class="ui-icon ui-icon-alert"></span>
        <span id="dialog-product-confirm-content" class="show ml15"></span>
    </div>
    <script type="text/javascript">
        var page_name = "group";
        var are_you_ready_to_delete = "{{trans('admin/manageGroups.are_you_sure_to_delete_these_groups')}}";
        var ready_to_delete = "{{trans('admin/manageGroups.are_you_sure_to_delete_these_groups')}}";

		function deleteAction(p_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{trans('admin/manageGroups.are_you_sure_to_delete_these_groups')}}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ trans('admin/manageGroups.group_list') }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						//$('#product_action').val(selected_action);
						//$('#p_id').val(p_id);
						window.location.href = '{{url('admin/group/delete?id=')}}'+p_id;
						//document.getElementById("productsActionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}
    </script>
@stop