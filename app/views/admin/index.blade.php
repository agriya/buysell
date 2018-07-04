@extends('admin')
@section('content')
	<!-- NOTIFICATIONS STARTS -->
    @include('notifications')
    <!-- NOTIFICATIONS END -->
	@if (Session::has('error_message') && Session::get('error_message') != "")
    	<!-- ERROR INFO STARTS -->
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
        <!-- ERROR INFO END -->
    @endif
	<!-- PAGE TITLE STARTS -->
	<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::to('admin/users/add') }}" title="{{ Lang::get('admin/addMember.addmember_page_title') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/addMember.addmember_page_title') }}
    </a>
    <h1 class="page-title">{{ Lang::get('admin/manageMembers.manage_members') }}</h1>
    <!-- PAGE TITLE END -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!-- SEARCH TITLE STARTS -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ Lang::get('admin/manageMembers.memberlist_search_members') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- SEARCH TITLE END -->

            <div class="portlet-body form">
            	<div id="search_holder">
            		<div id="selSrchScripts">
		                <div class="form-body">
		                    <div class="row">
								<div class="col-md-6">
									<div class="form-group">
			                            {{ Form::label('id', Lang::get('admin/manageMembers.viewmember_user_id'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('id', Input::get("id"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>

			                        <div class="form-group">
			                            {{ Form::label('user_name', Lang::get('admin/manageMembers.memberlist_user_name'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('user_name', Input::get("user_name"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>

			                        <div class="form-group">
			                            {{ Form::label('name', Lang::get('admin/manageMembers.memberlist_user_name_1'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('name', Input::get("name"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>

			                        <div class="form-group">
			                            {{ Form::label('user_email', Lang::get('admin/manageMembers.memberlist_user_email'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('user_email', Input::get("user_email"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>

									<div class="form-group">
			                            {{ Form::label('user_code', Lang::get('admin/manageMembers.memberlist_user_code'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('user_code', Input::get("user_code"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
		                    	</div>

								<div class="col-md-6">
			                        <div class="form-group">
										{{ Form::label('group_name_srch', Lang::get('admin/manageMembers.memberlist_group_name_srch'),array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::select('group_name_srch', $group_list, Input::get("group_name_srch"), array('class' =>'form-control bs-select input-medium')) }}
										</div>
									</div>

			                        <div class="form-group">
			                            {{ Form::label('shop_owner', Lang::get('default.is_shop_owner'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::select('is_shop_owner', $is_shop_owner, Input::get("is_shop_owner"), array('class' =>'form-control bs-select input-medium')) }}
			                            </div>
			                        </div>

									@if (Config::get('generalConfig.user_allow_to_add_product') == 0)
                                        <div class="form-group">
                                            {{ Form::label('allowed_seller', Lang::get('default.is_allowed_to_become_seller'), array('class' => 'control-label col-md-4')) }}
                                            <div class="col-md-6">
                                                {{ Form::select('is_allowed_to_add_product', $is_allowed_to_add_product, Input::get("is_allowed_to_add_product"), array('class' =>'form-control bs-select input-medium')) }}
                                            </div>
                                        </div>
			                        @endif

			                        <div class="form-group">
										{{ Form::label('status', Lang::get('default.status'),array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::select('status', $status,Input::get("status"), array('class' =>'form-control bs-select input-medium')) }}
										</div>
									</div>

									<div class="form-group">
			                            {{ Form::label('registered_date', Lang::get('admin/manageMembers.registered_date'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-5">
				                            <div class="input-group input-medium date date-picker input-daterange" data-date-format="dd-mm-yyyy">
			                                    {{ Form::text('from_date', Input::old('from_date', Input::get('from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
												<label for="date_added_to" class="input-group-addon">{{ Lang::get('common.to') }}</label>
			                                    {{ Form::text('to_date', Input::old('to_date', Input::get('to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
			                                </div>
			                                <label class="error" for="registered_date" generated="true">{{$errors->first('from_date')}}</label>
			                            </div>
			                        </div>
								</div>
		                    </div>
		                </div>
		                <!-- SEARCH ACTIONS STARTS -->
		                <div class="form-actions fluid">
		                	<div class="col-md-offset-2 col-md-4">
		                        <button type="submit" name="search_members" value="search_members" class="btn purple-plum">
								{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
		                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/users') }}'">
								<i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
		                    </div>
		                </div>
		                <!-- SEARCH ACTIONS END -->
					</div>
				</div>
            </div>
         </div>
    {{ Form::close() }}


	<div class="portlet box blue-hoki">
        <!-- TABLE TITLE STARTS -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('default.users_list') }}
            </div>
        </div>
        <!-- TABLE TITLE END -->

        <div class="portlet-body">
            @if(sizeof($user_list) > 0 )
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                            	<th width="40">{{ Form::checkbox('checkall','',false,array('class' => 'checkbox_common_class')) }}</th>
                                <th>{{ Lang::get('default.name') }}</th>
                                <th>{{ Lang::get('default.user_name') }}</th>
                                <th>{{ Lang::get('default.email') }}</th>
                                <th>{{ Lang::get('default.group_name') }}</th>
                                <th>{{ Lang::get('default.is_shop_owner') }}</th>
                                @if (Config::get('generalConfig.user_allow_to_add_product') == 0)
                                	<th>{{ Lang::get('default.is_allowed_to_become_seller') }}</th>
                                @endif
                                <th>{{ Lang::get('default.status') }}</th>
                                <th>{{ Lang::get('default.date_added') }}</th>
                                <th><div class="wid100">{{ Lang::get('default.action') }}</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_list as $usr)
                                <tr>
                                	@if(CUtil::getAuthUser()->id != $usr->id)
                                        <td>{{ Form::checkbox('change_group_name','',false,array('class' => 'checkbox_class checkbox_class_new' , 'id' => $usr->id )) }}</td>
                                	@else
                                        <td></td>
                                	@endif

                                    <td><a href="{{ URL::to('admin/users/user-details').'/'.$usr->id }}">{{ $usr->first_name.' '.$usr->last_name }}</a></td>

                                    <td>
										<a href="{{ URL::to('admin/users/user-details').'/'.$usr->id }}">{{ $usr->user_name }}</a>
										<p><small>(<a href="{{ URL::to('admin/users/user-details').'/'.$usr->id }}" class="text-muted">{{ BasicCutil::setUserCode($usr->id) }}</a>)</small></p>
									</td>

                                    <td><a href="mailto:{{ $usr->email }}">{{ $usr->email }}</a></td>

                                    <td>{{ $usr->group_name }}</td>

									<?php  $check_yes_no = (is_null($usr->is_shop_owner))?'No':$usr->is_shop_owner;
											if($check_yes_no == 'Yes') {
												$val = trans('common.yes');
											} elseif($check_yes_no == 'No') {
												$val = trans('common.no');
											}
									?>
									<td>
										{{ $val }}
										@if($usr->is_requested_for_seller == 'Yes')
											<div class="well pad10 mar0">
												<p class="font11"><strong>{{ Lang::get('admin/manageMembers.requested_to_become_seller') }}</strong></p>
												<a href="{{ URL::action('AdminUserController@getChangeSellerStatus').'?user_id='.$usr->id.'&action=approve' }}" class="fn_dialog_confirm btn-xs btn green" action="Approve" title="{{ Lang::get('admin/manageMembers.approve') }}">{{ Lang::get('admin/manageMembers.approve') }}</a>
												<a href="{{ URL::action('AdminUserController@getChangeSellerStatus').'?user_id='.$usr->id.'&action=block' }}" class="fn_dialog_confirm btn-xs btn red" action="Block" title="{{ Lang::get('admin/manageMembers.block') }}">{{ Lang::get('admin/manageMembers.block') }}</a>
											</div>
										@endif
									</td>

									@if (Config::get('generalConfig.user_allow_to_add_product') == 0)
										<?php
											if($usr->is_allowed_to_add_product == 'Yes') {
												$value = trans('common.yes');
											} elseif($usr->is_allowed_to_add_product == 'No') {
												$value = trans('common.no');
											}
										?>
										<td>
											{{ $value }}
											@if ($usr->is_allowed_to_add_product == 'No')
                                                <a href="{{ URL::to('admin/users/changestatus').'?action=allow_to_add_product&user_id='.$usr->id }}" class="fn_dialog_confirm btn green btn-xs" action="Allow" title="{{ Lang::get('admin/manageMembers.memberlist_allow_to_add_product') }}"><i class="fa fa-check"></i></a>
											@endif
										</td>
									@endif

                                    <td>
                                    	@if($usr->is_banned)
											<span class="label label-danger">{{Lang::get('common.blocked')}}</span>
                                    	@elseif($usr->activated)
                                        	<span class="label label-success">{{Lang::get('common.active')}}</span>
                                        @else
                                        	<span class="label label-default">{{Lang::get('common.inactive')}}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">{{ CUtil::FMTDate($usr->created_at, 'Y-m-d H:i:s', '') }}</td>

                                    <td class="status-btn">
                                        <a class="btn blue btn-xs" href="{{ URL::to('admin/users/edit').'/'.$usr->id }}" title="{{ Lang::get('common.edit') }}"><i class="fa fa-edit"></i></a>
                                        @if(CUtil::getAuthUser()->id != $usr->id)
                                        	@if($usr->is_banned)
												<a href="{{ URL::to('admin/users/changestatus').'?action=unblock&user_id='.$usr->id }}" class="fn_dialog_confirm btn green btn-xs" action="Un-Block" title="{{ Lang::get('admin/manageMembers.memberlist_unblock') }}"><i class="fa fa-check"></i></a>
                                        	@else
												@if($usr->activated)
													<a href="{{ URL::to('admin/users/changestatus').'?action=block&user_id='.$usr->id }}" class="fn_dialog_confirm btn-xs btn red" action="Block" title="{{ Lang::get('admin/manageMembers.memberlist_block') }}"><i class="fa fa-ban"></i></a>
                                                    <a href="{{ URL::to('admin/users/manage-credits').'?user_id='.$usr->id }}" title="{{ trans('admin/manageMembers.manage_credits') }}" class="fn_changeStatusPop btn-xs btn green"><i class="fa fa-credit-card"></i></a>
												@else
													<a href="{{ URL::to('admin/users/changestatus').'?action=block&user_id='.$usr->id }}" class="fn_dialog_confirm btn-xs btn red" action="Block" title="{{ Lang::get('admin/manageMembers.memberlist_block') }}"><i class="fa fa-ban"></i></a>
													<a href="{{ URL::to('admin/users/changestatus').'?action=activate&user_id='.$usr->id }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Activate" title="{{ Lang::get('admin/manageMembers.memberlist_activate') }}"><i class="fa fa-check"></i></a>
												@endif
                                        	@endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                            	<td colspan="10">
                                	<p class="pull-left margin-top-10 margin-right-10">
                                        {{ Form::select('group_name_srch',$group_list,Input::get("group_name_srch"),array('class' =>'form-control bs-select input-medium group_name_class','id' => $usr->id)) }}
                                        {{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
                                    </p>
                                    <p class="pull-left margin-top-10">
                                        <button type="Change Group Name" name="change_group_name" value="change_group_name" class="change_group_name_confirm btn btn-info responsive-btn-block mb10" onclick=""><i class="fa fa-arrow-circle-right"></i> {{ Lang::get('common.change_group_name') }}</button>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                     </table>
                </div>

                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $user_list->appends(array('user_name' => Input::get('user_name'), 'user_code' => Input::get('user_code'), 'is_shop_owner' => Input::get('is_shop_owner'), 'name' => Input::get('name'), 'id' => Input::get('id'), 'is_allowed_to_add_product' => Input::get('is_allowed_to_add_product'), 'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date'), 'user_email' => Input::get('user_email'), 'status' => Input::get('status'), 'search_members' => Input::get('search_members'), 'group_name_srch' => Input::get('group_name_srch')))->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('default.users_not_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>
    <div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
	    $(".checkbox_common_class").click(function() {
		    if($(".checkbox_common_class").length == $(".checkbox_common_class:checked").length) {
		     $(".checkbox_class_new").prop('checked', true);
		     $(".checkbox_class_new").parent( "span" ).addClass( "checked");
		    }
		    else {
		     $(".checkbox_class_new").removeAttr("checked");
		     $(".checkbox_class_new").parent( "span" ).removeClass( "checked");
		    }
	   });

		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
				var atag_href = $(this).attr("href");
				var action = $(this).attr("action");
				var cmsg = "";
				switch(action){
					case "Activate":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_activate_confirm_msg') }}";
						break;

					case "De-Activate":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_deactivate_confirm_msg') }}";
						break;
					case "Block":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_block_confirm_msg') }}";
						break;

					case "Un-Block":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_unblock_confirm_msg') }}";
						break;
					case "Allow":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_allow_to_add_product_confirm_msg') }}";
						break;
				}
				bootbox.dialog({
					message: cmsg,
					title: cfg_site_name,
					buttons: {
						danger: {
							label: "{{ trans('common.ok')}}",
							className: "btn-danger",
							callback: function() {
								Redirect2URL(atag_href);
								bootbox.hideAll();
							}
						},
						success: {
							label: "{{ trans('common.cancel')}}",
							className: "btn-default",
						}
					}
				});
					return false;
				});
			});

	$(function() {
        $('#from_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
        $('#to_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });

	//Change Group Name Confirm
	var post_url = "{{ URL::to('admin/users/change-group-name') }}";
	var page = $('#page').val();
	//alert(page);
	$(window).load(function(){
		  $(".change_group_name_confirm").click(function(){
				var cmsg ="";
				error_found = false;
				if ($('.checkbox_class:checked').length <= 0) {
					$('#dialog-confirm-content').html("{{ trans('common.select_the_checkbox') }}");
					error_found = true;
					//bootbox.alert("{{ trans('common.select_the_checkbox') }}");
					//return false;
				}
				if ($('.group_name_class').val() =='' ) {
					$('#dialog-confirm-content').html("{{ trans('common.select_group_name') }}");
					error_found = true;
					//bootbox.alert("{{ trans('common.select_group_name') }}");
					//return false;
				}
				if(error_found == true){
					$("#dialog-confirm").dialog({ title:  cfg_site_name, modal: true,
						buttons: {
							"{{ trans('common.cancel') }}": function() {
								$(this).dialog("close");
							}
						}
					});
					return false;
				}

				cmsg ="{{ Lang::get('admin/manageMembers.viewmember_chnage_group_name_confirm_msg') }}";
				var val = [];
        		$(':checkbox:checked').each(function(i){
          			val[i] = $(this).attr('id');
           		});
        		var selected_checkbox_id = val.join(',');
        		var selected_group_name_id = $('.group_name_class').val();
        		//alert(selected_group_name_id);
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok')}}",
				      		className: "btn-danger",
				      		callback: function() {
				      			var post_data = 'selected_checkbox_id='+selected_checkbox_id+'&selected_group_name_id='+selected_group_name_id;
					      		$.ajax({
	            					type: 'POST',
	           						url: post_url,
	            					data: post_data,
	            					success: function(data){
	            						window.location.replace("{{ URL::to('admin/users').'?page='}}"+page);
										bootbox.hideAll();
					      			}
					    		});
					    	}
				    	},
				    	success: {
				      		label: "{{ trans('common.cancel')}}",
				      		className: "btn-default",
				    	}
				  	}
				});
				return false;
			});
		});
	</script>
@stop