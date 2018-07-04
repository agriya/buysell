@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<a href="{{ URL::to('variations/add-group') }}" class="pull-right btn btn-xs green-meadow responsive-btn-block">
					<i class="fa fa-plus"></i> {{ Lang::get('variations::variations.add_variation_group') }}
				</a>
				<h1>{{ Lang::get('variations::variations.variation_group_list') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: INCLUDE NOTIFICATIONS -->
			@include('notifications')
			<!-- END: INCLUDE NOTIFICATIONS -->

			<!-- BEGIN: VARIATION GROUPS LIST -->
			<div class="well">
				@if(count($variations_group) > 0)
					<div id="fn_lists">
						{{ Form::model($variations_group, array('url' => 'variations/groups', 'method'=>'post', 'class' => 'form-horizontal pos-relative member-formbg mb40', 'role' => 'form', 'id'=>'variations_groups_frm')) }}
                        	<div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th width="40">{{ Form::checkbox('variationlist_ckbox', '', '', array('id'=>'variationlist_ckbox', 'class'=>'checkbox margin-top-5') ) }}</th>
                                            <th>{{Lang::get('variations::variations.group_name')}}</th>
                                            <th>{{Lang::get('variations::variations.variations_in_the_group')}}</th>
                                            <th>{{Lang::get('variations::variations.actions')}}</th>
                                        </tr>
                                    </thead>
    
                                    <tbody>
                                        @foreach($variations_group as $key => $val)
                                            <tr>
                                                <td>{{ Form::checkbox('row_id[]', $val->variation_group_id, '', array('id'=>$val->variation_group_id, 'class'=>'case')) }}</td>
                                                <td>{{ $val->variation_group_name }}</td>
                                                <td><div class="wid-220">{{ $variations_obj->getVariationsInGroupByGroupIdAsString($val->variation_group_id); }}</div></td>
                                                <td class="action-btn">
                                                    <div class="wid-100">
                                                        <a href="{{ URL::to('variations/add-group/'.$val->variation_group_id) }}" class="btn blue btn-xs" title="{{ Lang::get('variations::variations.edit') }}"><i class="fa fa-edit"></i></a>
                                                        <a href="{{ URL::to('variations/group-action').'?action=delete&variation_group_id='.$val->variation_group_id }}" class="fn_dialog_confirm btn red btn-xs" action="Delete" title="{{ Lang::get('variations::variations.delete') }}"><i class="fa fa-trash-o"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
						{{ Form::close() }}
					</div>
					
                    <!--- BEGIN: PAGINATION --->
                    <div class="pull-right margin-top-20">
	                    {{ $variations_group->links() }}
                    </div>
                    <!--- END: PAGINATION --->
                    
                    
                    <!--- BEGIN: ACTIONS --->
			        <ul class="list-inline margin-top-20">
						<li class="margin-bottom-5">
							{{ Form::select('variation_group_action', $action_list, Input::get("variation_group_action"), array('class' =>'form-control bs-select input-medium','id' => 'variation_group_action')) }}
							{{ Form::hidden('page', Input::get('page'), array('id' => 'page')) }}
						</li>
						<li>
							<button name="list_action" value="list_action" class="action_confirm btn green responsive-btn-block" onclick="">
							<i class="fa fa-arrow-circle-right"></i> {{ Lang::get('variations::variations.submit') }}</button>
						</li>
			        </ul>
			        <!--- END: ACTIONS --->
				@else
					<div class="note note-info margin-0">{{ Lang::get('variations::variations.no_variation_group_list') }}</div>
				@endif
				<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
			</div>
			<!-- END: VARIATION GROUPS LIST -->
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var mes_required = '{{ Config::get('common.required') }}';
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(function(){
			$("#variationlist_ckbox").click(function () {
				$('.case').prop('checked', this.checked);
				if($('input[name="row_id[]"]:checked').size() == 0) {
				$("#fn_listDelete").hide();
				} else {
				$("#fn_listDelete").show();
				}
			});

			$(".case").click(function(){
				if($('input[name="row_id[]"]:checked').size() == 0) {
					$("#fn_listDelete").hide();
			  	} else {
					$("#fn_listDelete").show();
			  	}
				if($(".case").length == $(".case:checked").length) {
					$("#variationlist_ckbox").prop("checked", "checked");
				} else {
					$("#variationlist_ckbox").removeAttr("checked");
				}
			});
		});

		$(window).load(function(){
		  	$(".fn_dialog_confirm").click(function(){
				var atag_href = $(this).attr("href");
				var action = $(this).attr("action");
				var cmsg = "";
				switch(action){
					case "Delete":
						cmsg = "{{ Lang::get('variations::variations.are_you_sure_want_to_delete_this_variation_group') }}";
						break;
				}
				bootbox.dialog({
					message: cmsg,
					title: cfg_site_name,
					buttons: {
						danger: {
							label: "{{ trans('common.ok') }}",
							className: "btn-danger",
							callback: function() {
								Redirect2URL(atag_href);
								bootbox.hideAll();
							}
						},
						success: {
							label: "{{ trans('common.cancel') }}",
							className: "btn-default",
						}
					}
				});
				return false;
			});
		});

		//Variation action
		var post_url = "{{ URL::to('variations/group-list-action') }}";
		var page = $('#page').val();
		//alert(page);
		$(window).load(function(){
			$(".action_confirm").click(function(){
				var cmsg ="";
				if($('input[name="row_id[]"]:checked').size() == 0) {
   					bootbox.alert("{{ Lang::get('variations::variations.select_the_checkbox') }}");
   					return false;
				}
				if ($('#variation_group_action').val() =='' ) {
					bootbox.alert("{{ Lang::get('variations::variations.select_the_action') }}");
					return false;
				}
				cmsg = "{{ Lang::get('variations::variations.are_you_sure_want_to_delete_selected_variations_groups') }}";
				var val = [];
        		$(':checkbox:checked').each(function(i){
          			val[i] = $(this).attr('id');
           		});
        		var selected_variation_group_id = val.join(',');
        		var variation_group_action = $('#variation_group_action').val();
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok') }}",
				      		className: "btn-danger",
				      		callback: function() {
				      			var post_data = 'variation_group_action='+variation_group_action+'&selected_variation_group_id='+selected_variation_group_id;
					      		$.ajax({
	            					type: 'POST',
	           						url: post_url,
	            					data: post_data,
	            					success: function(data){
	            						window.location.replace("{{ URL::to('variations/groups').'?page='}}"+page);
										bootbox.hideAll();
					      			}
					    		});
					    	}
				    	},
				    	success: {
				      		label: "{{ trans('common.cancel') }}",
				      		className: "btn-default",
				    	}
				  	}
				});
				return false;
			});
		});
	</script>
@stop