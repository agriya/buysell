@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!-- BEGIN: INFO BLOCK -->
	@if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
    <!-- END: INFO BLOCK -->

	<!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{ Lang::get('admin/manageMembers.manage_shops') }}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ Lang::get('admin/manageMembers.shops_list_search_shop') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->

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
			                            {{ Form::label('shop_name', Lang::get('shopDetails.shop_name'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('shop_name', Input::get("shop_name"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
									<div class="form-group">
										{{ Form::label('shop_status', Lang::get('shopDetails.shop_status'),array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::select('shop_status', $shop_status,Input::get("shop_status"), array('class' =>'form-control bs-select')) }}
										</div>
									</div>
									@if(CUtil::chkIsAllowedModule('featuredsellers'))
										<div class="form-group">
		                                    {{ Form::label('featured_sellers', Lang::get('featuredsellers::featuredsellers.featured_sellers'), array('class' => 'col-md-4 control-label')) }}
		                                    <div class="col-md-6">
		                                        {{ Form::select('featured_sellers', array('' => Lang::get('common.select'), 'Yes' => Lang::get('common.yes'), 'No' => Lang::get('common.no')), Input::get("featured_sellers"), array('class' => 'form-control bs-select')) }}
		                                    </div>
		                                </div>
	                                @endif
								</div>
								<div class="col-md-6">
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
			                                {{ Form::text('user_email', Input::get("user_email"), array('class' => 'form-control', "placeholder" => trans('admin/manageMembers.user_email'))) }}
			                            </div>
			                        </div>
			                        <div class="form-group">
			                            {{ Form::label('user_code', Lang::get('admin/manageMembers.memberlist_user_code'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('user_code', Input::get("user_code"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
		                    	</div>
		                    </div>
		                </div>

		                <!-- BEGIN: SEARCH ACTIONS -->
		                <div class="form-actions fluid">
		                	<div class="col-md-offset-2 col-md-4">
		                        <button type="submit" name="search_members" value="search_members" class="btn purple-plum">
								{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
		                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/shops') }}'">
								<i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
		                    </div>
		                </div>
		                <!-- END: SEARCH ACTIONS -->
					</div>
				</div>
            </div>
         </div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/manageMembers.shops_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(sizeof($user_list) > 0 )
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                            	<th width="40">{{ Form::checkbox('checkall','',false,array('class' => 'checkbox_common_class')) }}</th>
                                <th>{{ Lang::get('shopDetails.shop_details') }}</th>
                                <th>{{ Lang::get('admin/manageMembers.memberlist_user_details') }}</th>
                                <th>{{ Lang::get('admin/manageMembers.user_email') }}</th>
                                <th>{{ Lang::get('common.status') }}</th>
                                <th>{{ Lang::get('default.date_added') }}</th>
                                <th><div class="wid100">{{ Lang::get('default.action') }}</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_list as $usr)
                            	<?php $url_slug = $usr->url_slug;?>
                                <tr>
                                	<td>
	                                	@if(CUtil::getAuthUser()->id != $usr->user_id)
	                                		{{ Form::checkbox('change_group_name','',false,array('class' => 'checkbox_class checkbox_class_new' , 'id' => $usr->user_id )) }}
	                                	@endif
                                	</td>
                                    <td>
                                    	<p><a target="_blank" href="{{{URL::to('shop/'.$usr->url_slug)}}}">{{{$usr->shop_name}}}</a></p>
									</td>
                                    <td>
                                    	<p><a href="{{ URL::to('admin/users/user-details').'/'.$usr->user_id }}">{{ $usr->first_name.' '.$usr->last_name }}</a></p>
										<small>
											(<a href="{{ URL::to('admin/users/user-details').'/'.$usr->user_id }}" class="text-muted">{{ BasicCutil::setUserCode($usr->user_id) }}</a>)
										</small>
									</td>
                                    <td>{{$usr->email}}</td>
									<td>
										@if(is_null($usr->shop_status) || !$usr->shop_status)
											<span class="label label-danger">{{trans('common.inactive')}}</span>
										@else
											<span class="label label-success">{{trans('common.active')}}</span>
										@endif
									</td>
									<td class="text-muted">{{ CUtil::FMTDate($usr->created_at, 'Y-m-d H:i:s', '')}}</td>
                                    <td class="status-btn">
                                        <p class="clearfix">
                                            <a class="btn blue btn-xs" href="{{ URL::to('admin/shop/edit').'/'.$usr->user_id }}" title="{{ Lang::get('common.edit') }}">
                                            <i class="fa fa-edit"></i></a>
                                            @if(CUtil::getAuthUser()->id != $usr->user_id)
                                                @if($usr->shop_status)
                                                    <a href="{{ URL::to('admin/users/changestatus').'?action=deactivateshop&user_id='.$usr->user_id }}" class="fn_dialog_confirm btn red btn-xs" action="deactivateshop" title="{{ Lang::get('admin/manageMembers.deactivate_shop') }}"><i class="fa fa-ban"></i></a>
                                                @else
                                                    <a href="{{ URL::to('admin/users/changestatus').'?action=activateshop&user_id='.$usr->user_id }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="activateshop" title="{{ Lang::get('admin/manageMembers.activate_shop') }}"><i class="fa fa-check"></i></a>
                                                @endif
                                            @endif
                                        </p>
                                        @if(CUtil::chkIsAllowedModule('featuredsellers'))
                                            @if($usr->is_featured_seller == "Yes" && (strtotime($usr->featured_seller_expires) >= strtotime(date('Y-m-d'))))
                                                <p class="label label-info"><i class="fa fa-check"></i> {{ Lang::get('featuredsellers::featuredsellers.featured') }}</p>
                                            @elseif($usr->is_shop_owner == "Yes")
                                                <p><a href="{{ URL::to('featuredsellers/set-as-featured?id='.$usr->user_id) }}" class="btn btn-xs green fn_changeStatus">{{ trans('featuredproducts::featuredproducts.set_as_featured') }}</a></p>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                            	<td colspan="7">
                                	<p class="pull-left margin-top-10 margin-right-10">
                                        {{ Form::select('shop_action',$shop_action,Input::get("shop_action"),array('class' =>'form-control bs-select input-medium shop_staus_class','id' => $usr->id)) }}
                                        {{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
                                    </p>
                                    <p class="pull-left margin-top-10">
                                    	<button type="Change Group Name" name="change_group_name" value="change_group_name" class="change_shop_status_confirm btn btn-info responsive-btn-block mb10" onclick=""><i class="fa fa-arrow-circle-right"></i> {{ Lang::get('admin/manageMembers.change_shop_status') }}</button>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                     </table>
                </div>

                <!-- BEGIN: PAGINATION -->
                <div class="text-right">
                    {{ $user_list->appends(array('user_name' => Input::get('user_name'), 'user_code' => Input::get('user_code'), 'is_shop_owner' => Input::get('is_shop_owner'), 'name' => Input::get('name'), 'id' => Input::get('id'), 'is_allowed_to_add_product' => Input::get('is_allowed_to_add_product'), 'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date'), 'user_email' => Input::get('user_email'), 'status' => Input::get('status'), 'shop_status' => Input::get('shop_status'), 'shop_name' => Input::get('shop_name'), 'search_members' => Input::get('search_members'), 'group_name_srch' => Input::get('group_name_srch')))->links() }}
                </div>
                <!-- END: PAGINATION -->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('default.users_not_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
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
					case "activateshop":
						cmsg = "{{ Lang::get('admin/manageMembers.shopslist_shop_activate_confirm') }}";
						break;

					case "deactivateshop":
						cmsg = "{{ Lang::get('admin/manageMembers.shopslist_shop_deactivate_confirm') }}";
						break;
					case "Block":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_block_confirm_msg') }}";
						break;

					case "Un-Block":
						cmsg = "{{ Lang::get('admin/manageMembers.viewmember_unblock_confirm_msg') }}";
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
	var post_url = "{{ URL::action('AdminUserController@postChangeShopStatus') }}";
	var page = $('#page').val();
	//alert(page);
	$(window).load(function(){
		  $(".change_shop_status_confirm").click(function(){
				var cmsg ="";
				if ($('.checkbox_class:checked').length <= 0) {
   					bootbox.alert("{{ Lang::get('common.select_atleast_one_checkbox') }}");
   					return false;
				}
				if ($('.shop_staus_class').val() =='' ) {
					bootbox.alert("{{ Lang::get('admin/manageMembers.shopslist_select_status') }}");
					return false;
				}
				cmsg ="{{ Lang::get('admin/manageMembers.shopslist_bulk_update_confirm') }}";
				var val = [];
        		$(':checkbox:checked').each(function(i){
          			val[i] = $(this).attr('id');
           		});
        		var selected_checkbox_id = val.join(',');
        		var selected_status_id = $('.shop_staus_class').val();
        		//alert(selected_status_id);
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok')}}",
				      		className: "btn-danger",
				      		callback: function() {
        						displayLoadingImage(true);
				      			var post_data = 'selected_checkbox_id='+selected_checkbox_id+'&selected_status_id='+selected_status_id;
					      		$.ajax({
	            					type: 'POST',
	           						url: post_url,
	            					data: post_data,
	            					success: function(data){
	            						hideLoadingImage(false);
	            						window.location.replace("{{ URL::to('admin/shops').'?page='}}"+page);
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

		@if(CUtil::chkIsAllowedModule('featuredsellers'))
			$(".fn_changeStatus").fancybox({
				maxWidth    : 800,
				maxHeight   : 430,
				fitToView   : false,
				width       : '70%',
				height      : '430',
				autoSize    : false,
				closeClick  : false,
				type        : 'iframe',
				openEffect  : 'none',
				closeEffect : 'none'
			});
		@endif
	</script>
@stop