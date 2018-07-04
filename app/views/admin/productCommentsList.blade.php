@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <h1 class="page-title">{{Lang::get('admin/comments.manage_comments')}}</h1>
    <!-- END: PAGE TITLE -->
	
    {{ Form::open(array('url' => Url::action('AdminProductCommentsController@getIndex'), 'id'=>'commentsFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/comments.search_comments') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->
			
			<!-- BEGIN: SEARCH FORM -->
            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                        	<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('id', trans('admin/comments.product_id'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('product_id_from', Input::get("product_id_from"), array('class' => 'form-control', 'placeholder' => trans('admin/comments.from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('product_id_to', Input::get("product_id_to"), array('class' => 'form-control', 'placeholder' => trans('admin/comments.to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="search_product_id_from" generated="true">{{$errors->first('product_id_from')}}</label>
                                            <label class="error" for="search_product_id_to" generated="true">{{$errors->first('product_id_to')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('product_title', trans('admin/comments.product_title'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('product_title', Input::get("product_title"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('product_title')}}</label>
                                        </div>
                                    </div>
                               </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('comment_id', trans('admin/comments.comment_id'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('comment_id_from', Input::get("comment_id_from"), array('class' => 'form-control', 'placeholder' => trans('admin/comments.from'))) }}
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    {{ Form::text('comment_id_to', Input::get("comment_id_to"), array('class' => 'form-control', 'placeholder' => trans('admin/comments.to'))) }}
                                                </div>
                                            </div>
                                            <label class="error" for="search_comment_id_from" generated="true">{{$errors->first('comment_id_from')}}</label>
                                            <label class="error" for="search_comment_id_to" generated="true">{{$errors->first('comment_id_to')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('commented_by', trans('admin/comments.commented_by'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('commented_by', Input::get("commented_by"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('commented_by')}}</label>
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
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminProductCommentsController@getIndex') }}'">
                                    <i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}
                                </button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
			<!-- END: SEARCH FORM -->
     	</div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-comments"></i> {{ Lang::get('admin/comments.comments_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($comments_list) > 0 )
            	<!--- BEGIN: PRODUCT COMMENT LIST --->
                {{ Form::open(array('url'=>URL::action('AdminProductCommentsController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('admin/comments.id') }}</th>
                                    <th>{{ Lang::get('admin/comments.product') }}</th>
                                    <th>{{ Lang::get('admin/comments.commented_by') }}</th>
                                    <th>{{ Lang::get('admin/comments.comment') }}</th>
                                    <th>{{ Lang::get('admin/comments.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comments_list as $comment)
                                    <?php $user_profile_url = CUtil::userProfileUrl($comment->user_id);
										$view_url = $productService->getProductViewURL($comment['product_id'], $comment);
										 ?>
                                    <tr>
                                        <td>{{Form::checkbox('ids[]',$comment->id, false, array('class' => 'checkboxes js-ids') )}}</td>
                                        <td>{{ $comment->id }}</td>
                                        <td><a target="_blank" href="{{$view_url}}">{{ $comment->product_name }}</a></td>
                                        <td><a target="_blank" href="{{$user_profile_url}}" title="{{$comment->user_name}}">{{ $comment->user_name }} </a></td>
                                        <td><p id="feedback_comment_text_{{$comment->id}}">{{ $comment->comments }}</p>
											<div id="feedback_edit_comment_{{$comment->id}}" class="mw-150" style="display:none">
                                                <p> {{Form::textarea('edit_comment_'.$comment->id,$comment->comments, array('rows'=>'3', 'cols'=>'30', 'class' => 'form-control', 'id' => 'edit_comment_'.$comment->id))}}<label class="error" for="edit_comment_{{$comment->id}}" id="comment_error_div_{{$comment->id}}" generated="true"></p>
                                                <p>
                                                    <a href="javascript:;" onclick="saveComment({{$comment->id}})" class="text-success">Save</a> |
                                                    <a href="javascript:;" onclick="cancelEditComment({{$comment->id}})" class="text-danger">Cancel</a>
                                                </p>
                                                <p><label id="common_err_div_{{$comment['id']}}"></label></p>
                                            </div>
										</td>
                                        <td>
                                            <a href="javascript:;" onclick="editComment({{$comment['id']}})" class="btn btn-xs blue" title="{{ trans('admin/comments.edit') }}">
											<i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">
                                        <p class="pull-left margin-top-10 margin-right-10">
                                            {{Form::select('action',$actions,'',array('class'=>'form-control', 'id'=>'comments_action'))}}
                                        </p>
                                        <p class="pull-left margin-top-10">
                                            <input type="submit" value="Submit" class="btn green" id="page_action" name="page_action">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!--- END: PRODUCT COMMENT LIST --->

                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $comments_list->appends(array('product_id_from' => Input::get('product_id_from'), 'product_id_to' => Input::get('product_id_to'),
						'comment_id_from' => Input::get('comment_id_from'), 'comment_id_to' => Input::get('comment_id_to'),
						'product_title' => Input::get('product_title'), 'commented_by' => Input::get('commented_by')))->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/comments.no_comments_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminProductCommentsController@postAction'))) }}
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
		var feedback_actions_url = '{{URL::action('AdminProductCommentsController@postAction')}}';

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
				$('#dialog-confirm-content').html('{{ trans('admin/comments.confirm_delete_comment') }}');
			}
			$("#dialog-confirm").dialog({ title: '{{ trans('admin/comments.comments_head') }}', modal: true,
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

		function editComment(comment_id)
		{
			$('#feedback_edit_comment_'+comment_id).show();
			$('#feedback_comment_text_'+comment_id).hide();


		}
		function cancelEditComment(comment_id)
		{
			$('#feedback_edit_comment_'+comment_id).hide();
			$('#feedback_comment_text_'+comment_id).show();
		}

		function saveComment(comment_id)
		{
			var comment = $('#edit_comment_'+comment_id).val();

			if(comment=='')
				$('#comment_error_div_'+comment_id).text('Required');

			if(comment=='')
				return false;


			postData = 'action=update_comment&comment_id=' + comment_id + '&comment='+comment;

            displayLoadingImage(true);

            $.post(feedback_actions_url, postData,  function(response)
            {
                hideLoadingImage (false);
                data = eval( '(' +  response + ')');
                if(data.result == 'success') {
                	$('#feedback_edit_comment_'+comment_id).hide();
                	$('#feedback_comment_text_'+comment_id).text(comment);
                	$('#feedback_comment_text_'+comment_id).show();
                }
                else {
                	$('#common_err_div_'+comment_id).text(data.message);
                	$('#common_err_div_'+comment_id).show();
                }
			});
		}

		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html("{{ trans('admin/comments.select_atleast_one_comment') }}");
				error_found = true;
			}
			var selected_action = $('#comments_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/comments.please_select_action') }}');
				error_found = true;
			}
			if(!error_found)
			{
				if(selected_action == 'delete')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/comments.confirm_delete_comment') }}');
				}
			}
			if(error_found)
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/comments.comments_head') }}', modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/comments.comments_head') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#listFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		})

	</script>
@stop