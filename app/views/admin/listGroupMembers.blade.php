@extends('admin')
@section('content')
    <a href="{{url('admin/group')}}" class="btn btn-xs default purple-stripe mt5 pull-right responsive-btn-block"><i class="fa fa-chevron-left"></i> {{trans('admin/manageGroups.back_to_group_list')}}</a>
    <h1 class="page-title">{{ '"'.$group_details['group_name'].'" - '.trans('admin/manageGroups.members')}}</h1>
    <div class="portlet box blue-madison">
        <!--- SEARCH TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-search"></i> {{ Lang::get('admin/manageMembers.memberlist_search_members') }}
            </div>
            <div class="tools">
                <a class="collapse" href="javascript:;"></a>
            </div>
        </div>
        <!--- SEARCH TITLE END --->

        <div id="fn_memberSearchId" class="portlet-body form">
            {{ Form::open(array('url' => 'admin/group/list-group-members', 'method'=>'get', 'class' => 'form-horizontal', 'id'=>'subscribers_search')) }}
                {{ Form::hidden('groupId', $append_arr['groupId'], array('class'=>'form-control')) }}
                <div class="form-body">
                    <div class="form-group">
                        {{ Form::label('name', Lang::get("admin/manageGroups.name"), array('class' => 'control-label col-md-2')) }}
                        <div class="col-md-4">{{ Form::text('name', Input::get('name'), array('class'=>'form-control')) }}</div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('email', Lang::get("admin/manageGroups.email"), array('class' => 'control-label col-md-2')) }}
                        <div class="col-md-4">{{ Form::text('email', Input::get('email'), array('class'=>'form-control')); }}</div>
                    </div>
                </div>
                <div class="form-actions fluid">
                    <div class="col-md-10 col-md-offset-2">
                        <button type="submit" name="members_search" class="btn purple-plum">{{trans('admin/manageGroups.search')}} <i class="fa fa-search"></i></button>
                        <button type="button" class="btn default" onclick="javascript:location.href='{{url('admin/group/list-group-members?groupId='.$append_arr['groupId'])}}'"><i class="fa fa-rotate-left"></i> {{ trans("admin/manageGroups.reset")}}</button>
                    </div>
                </div>
            {{ Form::close(array('id'=>'subscribers_search')) }}
        </div>
     </div>
    {{ Form::open(array('url' => 'admin/group/list-group-members', 'method'=>'post', 'id'=>'member_list')) }}
    {{ Form::hidden('groupId', $append_arr['groupId'], array('class'=>'form-control')) }}
    {{ Session::put('sess_page_no', Input::get('page')); }}
        @if(count($members_arr) > 0)
        	<div class="portlet box blue-hoki">
                <!--- TABLE TITLE STARTS --->
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i> {{ $group_details['group_name'].' - '.trans('admin/manageGroups.members')}} List
                    </div>
                </div>
                <!--- TABLE TITLE END --->

                <div class="portlet-body">
                    <div class="pull-right">
                        {{ $members->appends($append_arr)->links(); }}
                    </div>
                    <div class="table-responsive">
                    	<table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>{{trans('admin/manageGroups.name')}}</th>
                                <th>{{trans('admin/manageGroups.email')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $members_arr as $nl_key=>$nl_values )
                                <tr>
                                    <td>
                                        <a href="{{ URL::to('admin/users/user-details').'/'.$nl_values['user_id'] }}">{{ $nl_values['first_name']. ' ' .$nl_values['last_name'] }}</a>
                                        (<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$nl_values['user_id'] }}">{{ BasicCUtil::setUserCode($nl_values['user_id']) }}</a>)
                                    </td>
                                    <td>{{ $nl_values['email'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="text-right">
                        {{ $members->appends($append_arr)->links(); }}
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info mar0">{{Lang::get("admin/manageGroups.no_group_members")}}</div>
        @endif
    {{ Form::close(array('id'=>'newsletter_list')) }}
    @if($page_name = 'group_member')@endif
    <script type="text/javascript">
		var page_name = "group_member";
	</script>
@stop
