@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: INFO BLOCK --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: INFO BLOCK --->

	<!-- BEGIN: PAGE TITLE -->
	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <h1 class="page-title">{{Lang::get('admin/feedback.manage_feedback')}}</h1>
    <!-- END: PAGE TITLE -->
    {{ Form::open(array('url' => Url::action('AdminManageFeedbackController@getIndex'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/feedback.search_feedback') }}
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
                                        {{ Form::label('id', trans('admin/feedback.invoice_id'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('invoice_id_from', Input::get("invoice_id_from"), array('class' => 'form-control', 'placeholder' => trans('admin/feedback.from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('invoice_id_to', Input::get("invoice_id_to"), array('class' => 'form-control', 'placeholder' => trans('admin/feedback.to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="search_product_id_from" generated="true">{{$errors->first('invoice_id_from')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('search_status', trans('admin/feedback.status'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::select('search_status', $status, Input::get("search_status"), array('class' => 'form-control bs-select')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('search_status')}}</label>
                                        </div>
                                    </div>
                               </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('feedback_id_from', trans('admin/feedback.feedback_id'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('feedback_id_from', Input::get("feedback_id_from"), array('class' => 'form-control', 'placeholder' => trans('admin/feedback.from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('feedback_id_to', Input::get("feedback_id_to"), array('class' => 'form-control', 'placeholder' => trans('admin/feedback.to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="search_product_id_from" generated="true">{{$errors->first('feedback_id_from')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('feedback_by', trans('admin/feedback.feedback_by'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('feedback_by', Input::get("feedback_by"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('feedback_by')}}</label>
                                        </div>
                                    </div>
                                </div>
                           	</div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-2 col-md-5">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                	{{ trans("common.search") }} <i class="fa fa-search"></i>
                                </button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminManageFeedbackController@getIndex') }}'">
                                    <i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}
                                </button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
     	</div>
    {{ Form::close() }}


	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/feedback.feedbacks_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->


        <div class="portlet-body">
            @if(count($feedbacks_list) > 0 )
            	<!--- BEGIN: FEEDBACK LIST --->
                {{ Form::open(array('url'=>URL::action('AdminManageFeedbackController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th width="40">{{ Lang::get('admin/feedback.id') }}</th>
                                    <th class="col-md-1">{{ Lang::get('admin/feedback.status') }}</th>
                                    <th class="col-md-2">{{ Lang::get('admin/feedback.invoice') }}</th>
                                    <th class="col-md-2">{{ Lang::get('admin/feedback.by') }}</th>
                                    <th>{{ Lang::get('admin/feedback.feedback') }}</th>
                                    <th class="col-md-1">{{ Lang::get('admin/feedback.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feedbacks_list as $feedback)
                                    <?php
                                        $buyer_details = CUtil::getUserDetails($feedback['buyer_id']);
                                        $seller_details = CUtil::getUserDetails($feedback['seller_id']);
                                    ?>
                                    <tr>
                                        <td>{{Form::checkbox('ids[]',$feedback['id'], false, array('class' => 'checkboxes') )}}</td>
                                        <td>{{ $feedback['id'] }}</td>
                                        <td>
										   <?php
												if(count($feedback) > 0) {
													if($feedback['feedback_remarks'] == 'Negative') {
														$lbl_class = "label-danger";
													}
														elseif($feedback['feedback_remarks'] == 'Neutral') {
															$lbl_class = " label-info";
													}
														elseif($feedback['feedback_remarks'] == 'Positive') {
															$lbl_class = "label-success";
													}
												else
													{ $lbl_class = "label-default"; }
												}
											?>
											<span class="label {{ $lbl_class }}" id="status_txt_{{$feedback['id']}}">{{ Lang::get('admin/feedback.'.strtolower($feedback['feedback_remarks'])) }}</span>
                                        </td>
                                        <td>
                                            <p>{{ Lang::get('admin/feedback.id') }}: <span class="text-muted">{{ $feedback['invoice_id'] }}</span></p>
                                            <p>{{ Lang::get('admin/feedback.seller') }}: <a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$feedback['seller_id'] }}">{{ $seller_details['display_name'] }}</a></p>
                                            (<a target="_blank" class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$feedback['buyer_id'] }}">{{ $seller_details['user_code'] }}</a>)
                                            <p>{{ Lang::get('admin/feedback.buyer') }}: <a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$feedback['buyer_id'] }}">{{ $buyer_details['display_name'] }}</a></p>
                                            (<a target="_blank" class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$feedback['buyer_id'] }}">{{ $buyer_details['user_code'] }}</a>)
                                        </td>
                                        <td>
                                            @if($feedback['feedback_user_id'] == $feedback['buyer_id'])
                                            	<a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$feedback['buyer_id'] }}">{{ $buyer_details['display_name'] }}</a>
                                            	(<a target="_blank" class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$feedback['buyer_id'] }}">{{ $buyer_details['user_code'] }}</a>)
												<p class="text-muted"><i class="fa fa-shopping-cart font11"></i> {{Lang::get('admin/feedback.buyer')}}</p>
                                            @else
                                                <a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$feedback['seller_id'] }}">{{ $seller_details['display_name'] }}</a>
                                                (<a target="_blank" class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$feedback['seller_id'] }}">{{ $seller_details['user_code'] }}</a>)
												<p class="text-muted"><i class="fa fa-tags font11"></i> {{Lang::get('admin/feedback.seller')}}</p>
                                            @endif
                                        </td>
                                        <td>
                                        	<p id="feedback_comment_text_{{$feedback['id']}}">{{ nl2br($feedback['feedback_comment']) }}</p>
                                            <div id="feedback_edit_comment_{{$feedback['id']}}" class="mw-150" style="display:none">
                                                <p>
                                                    {{ Form::input('number','rating_'.$feedback['id'],$feedback['rating'], array('id' => 'edit_rating_'.$feedback['id'], 'class' => 'rating', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
                                                    <label class="error" for="edit_rating_{{$feedback['id']}}" id="rating_error_div_{{$feedback['id']}}" generated="true">{{$errors->first('feedback_by')}}</label>
                                                </p>
                                                <p>
                                                    {{Form::select('edit_status_'.$feedback['id'],$status,strtolower($feedback['feedback_remarks']), array('class' => 'form-control', 'id' => 'edit_status_'.$feedback['id']))}}
                                                    <label class="error" for="edit_status_{{$feedback['id']}}" id="status_error_div_{{$feedback['id']}}" generated="true">{{$errors->first('feedback_by')}}</label>
                                                </p>
                                                <p>
                                                    {{Form::textarea('edit_comment_'.$feedback['id'],$feedback['feedback_comment'], array('rows'=>'3', 'cols'=>'30', 'class' => 'form-control', 'id' => 'edit_comment_'.$feedback['id']))}}
                                                    <label class="error" for="edit_comment_{{$feedback['id']}}" id="comment_error_div_{{$feedback['id']}}" generated="true">
													{{$errors->first('feedback_by')}}</label>
                                                </p>
                                                <p>
                                                    <a href="javascript:;" onclick="editComment({{$feedback['id']}})" class="text-success">{{trans('common.save')}}</a> |
                                                    <a href="javascript:;" onclick="cancelEditComment({{$feedback['id']}})" class="text-danger">{{trans('common.cancel')}}</a>
                                                </p>
                                                <p>
                                                    <label class="text-danger" id="common_err_div_{{$feedback['id']}}"></label>
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:;" onclick="editFeedback({{$feedback['id']}})" class="btn btn-xs blue" title="{{ trans('admin/feedback.edit') }}">
											<i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="7">
                                        <p class="pull-left margin-top-10 margin-right-10">
                                            {{Form::select('action',$actions,'',array('class'=>'form-control bs-select input-medium', 'id'=>'action'))}}
                                        </p>
                                        <p class="pull-left margin-top-10">
                                            <input type="submit" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!--- END: FEEDBACK LIST --->

                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $feedbacks_list->appends(array('invoice_id_from' => Input::get('invoice_id_from'), 'invoice_id_to' => Input::get('invoice_id_to'),
						'feedback_id_from' => Input::get('feedback_id_from'), 'feedback_id_to' => Input::get('feedback_id_to'),
						'search_status' => Input::get('search_status'), 'feedback_by' => Input::get('feedback_by')))->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/feedback.no_feedback_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>

    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminStaticPageController@postAction'))) }}
    {{ Form::hidden('id', '', array('id' => 'list_id')) }}
    {{ Form::hidden('action', '', array('id' => 'list_action')) }}
    {{ Form::close() }}

	<div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var feedback_actions_url = '{{URL::action('AdminManageFeedbackController@postFeedbackAction')}}';
		tinymce.init({
            menubar: "tools",
            selector: "textarea.fn_editor",
            mode : "exact",
            elements: "content",
            removed_menuitems: 'newdocument',
            apply_source_formatting : true,
            remove_linebreaks: false,
            height : 400,
            plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste emoticons jbimages"
            ],
            toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons",
            relative_urls: false,
            remove_script_host: false
        });

		$(document).ready(function(){
            $("input.rating").rating({
                starCaptions: function(val) {
                        return val;
                },
                starCaptionClasses: function(val) {
                    if (val < 3) {
                        return 'label label-danger';
                    } else {
                        return 'label label-success';
                    }
                },
                hoverOnClear: false,
                showClear: false
            });

			var val = $('#js-page-type').val();
			if(val == 'external')
			{
				$('#static-content-div').hide();
				$('#external-link-div').show();
			}
			else
			{
				$('#static-content-div').show();
				$('#external-link-div').hide();
			}
		})
		$('#js-page-type').change(function(){
			var val = $(this).val();

			if(val == 'external')
			{
				$('#static-content-div').hide();
				$('#external-link-div').show();
			}
			else
			{
				$('#static-content-div').show();
				$('#external-link-div').hide();
			}
		})

		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.each(function(){
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				});
			}
			else
			{
				checkboxes.each(function(){
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				});
			}
		});
		function doAction(id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/feedback.confirm_delete') }}');
			}
			$("#dialog-confirm").dialog({ title: '{{ trans('admin/feedback.static_page_head') }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#list_action').val(selected_action);
						$('#list_id').val(id);
						document.getElementById("actionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		function editFeedback(feedback_id)
		{
			$('#feedback_edit_comment_'+feedback_id).show();
			$('#feedback_comment_text_'+feedback_id).hide();


		}
		function cancelEditComment(feedback_id)
		{
			$('#feedback_edit_comment_'+feedback_id).hide();
			$('#feedback_comment_text_'+feedback_id).show();
		}
		function nl2br (str, is_xhtml) {
		    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
		    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
		}

		function editComment(feedback_id)
		{
			var status = $('#edit_status_'+feedback_id).val();
			var comment = $('#edit_comment_'+feedback_id).val();
            var rating = $('#edit_rating_'+feedback_id).val();
            if(rating <=0)
                $('#rating_error_div_'+feedback_id).text('Required');
			if(status=='')
				$('#status_error_div_'+feedback_id).text('Required');
			if(comment=='')
				$('#comment_error_div_'+feedback_id).text('Required');

			if(status=='' || comment=='' || rating<=0)
				return false;


			postData = 'action=edit_feedback&feedback_id=' + feedback_id + '&status=' + status + '&comment='+comment+ '&rating='+rating;

            displayLoadingImage(true);

            $.post(feedback_actions_url, postData,  function(response)
            {
                hideLoadingImage (false);
                data = eval( '(' +  response + ')');
                if(data.result == 'success') {
                	$('#feedback_edit_comment_'+feedback_id).hide();
                	$('#feedback_comment_text_'+feedback_id).html(nl2br(comment, true));
                	$('#feedback_comment_text_'+feedback_id).show();
                	$('#status_txt_'+feedback_id).text(data.status);
                }
                else {
                	$('#common_err_div_'+feedback_id).text(data.message);
                	$('#common_err_div_'+feedback_id).show();
                }
				if(data.status == 'Positive') {
					$('#status_txt_'+feedback_id).addClass('label-success').removeClass('label-info label-danger');
				}
				else if(data.status == 'Negative') {
					$('#status_txt_'+feedback_id).addClass('label-danger').removeClass('label-info label-success');
				}
				else if(data.status == 'Neutral') {
					$('#status_txt_'+feedback_id).addClass('label-info').removeClass('label-success label-danger');
				}
            });
		}

		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(window).load(function(){
		  $("#page_action").click(function(){
				var cmsg ="";
				error_found = false;
				if ($('.checkboxes:checked').length <= 0) {
					$('#dialog-confirm-content').html("{{ trans('common.select_the_checkbox') }}");
					error_found = true;
					//bootbox.alert("{{ trans('common.select_the_checkbox') }}");
					//return false;
				}
				if ($('#action').val() =='' ) {
					$('#dialog-confirm-content').html("{{ trans('common.please_select_an_action') }}");
					error_found = true;
					//bootbox.alert("{{ trans('common.please_select_an_action') }}");
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
				var action = $('#action').val();
				var cmsg ="{{ Lang::get('admin/sellerRequest.set_as_new_request_confirm') }}";
				if(action == 'delete' || action == 'positive' || action == 'neutral' || action == 'negative') {
					replace_txt = action;
					cmsg ="{{ Lang::get('admin/sellerRequest.seller_request_action_confirm') }}";
					cmsg = cmsg.replace("VAR_ACTION", replace_txt);
				}
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok')}}",
				      		className: "btn-danger",
				      		callback: function() {
				      			$('#listFrm').submit();
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