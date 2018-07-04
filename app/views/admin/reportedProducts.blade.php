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

	<div id="report_message_div"></div>

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: INFO BLOCK --->

	<!-- BEGIN: PAGE TITLE -->
	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <h1 class="page-title">{{Lang::get('admin/reportedProduct.reported_products')}}</h1>
    <!-- END: PAGE TITLE -->

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-warning"></i> {{ Lang::get('admin/reportedProduct.reported_products_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($reported_products) > 0 )
            	<!--- BEGIN: REPORTED_PRODUCT LIST --->
                {{ Form::open(array('url'=>URL::action('AdminReportedProductsController@postBulkAction'),'id'=>'reportListFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('admin/reportedProduct.reported_product') }}</th>
                                    <th>{{ Lang::get('admin/reportedProduct.total_reports') }}</th>
                                    <th>{{ Lang::get('admin/reportedProduct.reporters') }}</th>
                                    <th>{{ Lang::get('admin/reportedProduct.options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reported_products as $reported_product)
                                	<?php
                                		$product_view_url = $productService->getProductViewURL($reported_product['product_id'], $reported_product);
                                		$p_img_arr = $prod_obj->getProductImage($reported_product['product_id']);
                                        $p_thumb_img = $productService->getProductDefaultThumbImage($reported_product['product_id'], 'small', $p_img_arr);
                                	?>
                                    <tr>
                                    	<td>{{Form::checkbox('ids[]',$reported_product['id'], false, array('class' => 'checkboxes js-ids') )}}</td>
                                        <td>
                                        	<div class="custom-feature">
                                                <figure>
                                                    <a target="_blank" href="{{ $product_view_url }}" class="img81x61 imgalt81x61">
                                                        <img src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" />
                                                    {{ CUtil::showFeaturedProductIcon($reported_product['product_id'], array()) }}
                                                    </a>
                                                </figure>
                                            </div>
											<p class="mt5"><a target="_blank" href="{{$product_view_url}}">{{ $reported_product['product_name'] }}</a></p>
										</td>
                                        <td>
											<?php $thread_count = array_count_values($reported_product['reported_threads']);?>
                                        	@if(!empty($thread_count))
                                        		@foreach($thread_count as $thread => $count)
                                        			<p>
														<span class="pull-left">&raquo;</span>
														<span class="ml15 show">
															{{Lang::get('admin/reportedProduct.thread_txt_'.$thread)}} : <strong class="text-muted">{{$count}}</strong>
														</span>
													</p>
                                        		@endforeach
                                        	@endif
                                        </td>
										<td>
											@if(!empty($reported_product['reported_users']))
												@foreach($reported_product['reported_users'] as $reporter)
													<p><a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$reporter['user_id'] }}">{{ $reporter['user_name'] }}</a></p>
													<p>(<a target="_blank" class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$reporter['user_id'] }}">{{ BasicCUtil::setUserCode($reporter['user_id']) }}</a>)</p>
												@endforeach
											@endif
										</td>
										<td class="status-btn">
											<a href="{{Url::action('AdminReportedProductsController@getView',$reported_product['product_id'])}}" title="{{ trans('common.view_details') }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
										</td>
									</tr>
                                @endforeach
                                <tr>
                                    <td colspan="5">
                                        <p class="pull-left mt10 mr10">
                                            {{Form::select('action',$actions,'',array('class'=>'form-control bs-select input-medium', 'id'=>'report_action'))}}
                                        </p>
                                        <p class="pull-left mt10">
                                            <input type="submit" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!--- END: REPORTED_PRODUCT LIST --->

                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $reported_products->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/reportedProduct.no_reports_found') }}</div>
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
		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html('{{ trans('common.select_atleast_one') }}');
				error_found = true;
			}
			var selected_action = $('#report_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html('{{ trans('common.please_select_action') }}');
				error_found = true;
			}
			if(!error_found)
			{
				if(selected_action == 'delete_report')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/reportedProduct.confirm_delete_report') }}');
				}
				if(selected_action == 'delete_product')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/reportedProduct.confirm_delete_product') }}');
				}
			}
			if(error_found)
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/reportedProduct.reported_products') }}', modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/reportedProduct.reported_products') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#reportListFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		})

		function doAction(id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-confirm-content').html('{{ trans('admin/reportedProduct.confirm_delete') }}');
			}
			$("#dialog-confirm").dialog({ title: '{{ trans('admin/reportedProduct.static_page_head') }}', modal: true,
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

		function editComment(feedback_id)
		{
			var status = $('#edit_status_'+feedback_id).val();
			var comment = $('#edit_comment_'+feedback_id).val();

			if(status=='')
				$('#status_error_div_'+feedback_id).text('Required');
			if(comment=='')
				$('#comment_error_div_'+feedback_id).text('Required');

			if(status=='' || comment=='')
				return false;


			postData = 'action=edit_feedback&feedback_id=' + feedback_id + '&status=' + status + '&comment='+comment;

            displayLoadingImage(true);

            $.post(feedback_actions_url, postData,  function(response)
            {
                hideLoadingImage (false);
                data = eval( '(' +  response + ')');
                if(data.result == 'success') {
                	$('#feedback_edit_comment_'+feedback_id).hide();
                	$('#feedback_comment_text_'+feedback_id).text(comment);
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
	</script>
@stop