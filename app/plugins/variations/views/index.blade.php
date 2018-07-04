@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN:PAGE TITLE -->
			<div class="responsive-pull-none">
				<a href="{{ URL::to('variations/add-variation') }}" class="pull-right btn btn-xs green-meadow responsive-btn-block">
					<i class="fa fa-plus"></i> {{ Lang::get('variations::variations.add_variation') }}
				</a>
				<h1>{{ Lang::get('variations::variations.variation_listing') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->
            @if(count($variations) > 0)
                <div class="alert alert-info margin-top-20">
                    <?php 
                        $note_msg = Lang::get('variations::variations.variation_group_head_note_info');
                        $var_group_link = "<a href=".URL::to('variations/add-group')."> here </a>";
                        $note_msg = str_replace('VAR_LINK', $var_group_link, $note_msg);
                    ?>
                    {{ $note_msg }}
                </div>
            @endif

			<!-- BEGIN: INCLUDE NOTIFICATIONS -->
			@include('notifications')
			<!-- END: INCLUDE NOTIFICATIONS -->

			<!-- BEGIN: VARIATION LIST -->
			<div class="well">
            
				@if(count($variations) > 0)
					<div id="fn_lists">
						{{ Form::model(Session::get('list_array'), array('url' => 'lists/add', 'method'=>'post', 'class' => 'form-horizontal pos-relative member-formbg mb40', 'role' => 'form', 'id'=>'list_add')) }}
                        	<div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th width="40">{{ Form::checkbox('variationlist_ckbox', '', '', array('id'=>'variationlist_ckbox', 'class'=>'checkbox') ) }}</th>
                                            <th>{{Lang::get('variations::variations.name')}}</th>
                                            <th>{{Lang::get('variations::variations.value')}}</th>
                                            <th>{{Lang::get('variations::variations.actions')}}</th>
                                        </tr>
                                    </thead>
    
                                    <tbody>
                                        @foreach($variations as $key => $val)
                                            <tr>
                                                <td>{{ Form::checkbox('row_id[]', $val->variation_id, '', array('id'=>$val->variation_id, 'class'=>'case')) }}</td>
                                                <td>{{ $val->name }}</td>
                                                <td><div class="wid-220">{{ $variations_obj->getVariationAttributesValues($val->variation_id); }}</div></td>
                                                <td class="action-btn">
                                                    <div class="wid-100">
                                                        {{-- Lang::get('variations::variations.re_order') --}}
                                                        <a href="{{ URL::to('variations/add-variation/'.$val->variation_id) }}" class="btn blue btn-xs" title="{{ Lang::get('variations::variations.edit') }}"><i class="fa fa-edit"></i></a>
                                                        <a href="{{ URL::to('variations/variations-action').'?action=delete&variation_id='.$val->variation_id }}" class="fn_dialog_confirm btn red btn-xs" action="Delete" title="{{ Lang::get('variations::variations.delete') }}"><i class="fa fa-trash-o"></i></a>
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
	                    {{ $variations->links() }}
                    </div>
                    <!--- END: PAGINATION --->
                    
					<!--- BEGIN: ACTIONS --->
			        <ul class="list-inline margin-top-20">
						<li class="margin-bottom-5">
							{{ Form::select('variation_action', $action_list, Input::get("variation_action"), array('class' =>'form-control bs-select input-medium','id' => 'variation_action')) }}
							{{ Form::hidden('page', Input::get('page'), array('id' => 'page')) }}
						</li>
						<li>
                            <button type="Change Group Name" name="change_group_name" value="change_group_name" class="action_confirm btn green responsive-btn-block" onclick="">
                            	<i class="fa fa-arrow-circle-right"></i> {{ Lang::get('variations::variations.submit') }}
                            </button>
                        </li>
			        </ul>
			        <!--- END: ACTIONS --->
				@else
					<div class="note note-info margin-0">{{ Lang::get('variations::variations.no_variation_list')}}</div>
				@endif
				<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
			</div>
			<!-- END: VARIATION LIST -->
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
						cmsg = "{{ Lang::get('variations::variations.are_you_sure_want_to_delete_this_variation') }}";
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
		var post_url = "{{ URL::to('variations/variations-list-action') }}";
		var page = $('#page').val();
		//alert(page);
		$(window).load(function(){
			$(".action_confirm").click(function(){
				var cmsg ="";
				if($('input[name="row_id[]"]:checked').size() == 0) {
   					bootbox.alert("{{ Lang::get('variations::variations.select_the_checkbox') }}");
   					return false;
				}
				if ($('#variation_action').val() =='' ) {
					bootbox.alert("{{ Lang::get('variations::variations.select_for_the_group_name') }}");
					return false;
				}
				cmsg = "{{ Lang::get('variations::variations.are_you_sure_want_to_delete_selected_variations') }}";
				var val = [];
        		$(':checkbox:checked').each(function(i){
          			val[i] = $(this).attr('id');
           		});
        		var selected_variation_id = val.join(',');
        		var variation_action = $('#variation_action').val();
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok') }}",
				      		className: "btn-danger",
				      		callback: function() {
				      			var post_data = 'variation_action='+variation_action+'&selected_variation_id='+selected_variation_id;
					      		$.ajax({
	            					type: 'POST',
	           						url: post_url,
	            					data: post_data,
	            					success: function(data){
	            						window.location.replace("{{ URL::to('variations').'?page='}}"+page);
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