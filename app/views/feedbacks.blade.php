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
				<h1>{{ Lang::get('feedback.manage_feedbacks') }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<!-- BEGIN: FEEDBACK LIST -->
			<div style="display:none;" title="" id="dialog-cart-confirm">
				<p><span style="float:left; margin:0 7px 20px 0;" class="ui-icon ui-icon-alert"></span><span id="dialog_update_state_confirm_content"></span></p>
			</div>
			<div class="tabbable-custom">
				<p class="pull-right margin-top-10 feedback-status">
					<span class="badge badge-success" title="Positive"><i class="fa fa-thumbs-up"></i> <strong>{{$feedback_counts['Positive']}}</strong></span>
					<span class="badge badge-danger" title="Negative"><i class="fa fa-thumbs-down"></i> <strong>{{$feedback_counts['Negative']}}</strong></span>
					<span class="badge bg-grey-cascade" title="Neutral"><i class="fa fa-exchange"></i> <strong>{{$feedback_counts['Neutral']}}</strong></span>
				</p>
				<div class="customview-navtab mobviewmenu-480">
					<button class="btn bg-blue-steel btn-sm"><i class="fa fa-chevron-down"></i>View Menu</button>
					<ul class="nav nav-tabs margin-bottom-20">
						<li @if($view_type=='awaiting') class="active" @endif><a href="{{URL::action('FeedbackController@getIndex')}}" title="{{ Lang::get('common.give_feedback_for_the_products_you_purchased')}}">
						{{ Lang::get('feedback.items_awaiting_feedback') }}</a></li>
						<li @if($view_type=='feedback_completed') class="active" @endif><a href="{{URL::action('FeedbackController@getIndex').'?view_type=feedback_completed'}}">
						{{ Lang::get('feedback.completed_feedback') }}</a></li>
					</ul>
				</div>
				<div class="well">
					@if($view_type=='awaiting')
						@if(count($awaiting_invoices_list) > 0)
							<h2 class="title-one">{{ Lang::get('feedback.items_awaiting_feedback') }}</h2>
							<div class="table-responsive margin-bottom-30">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>{{ Lang::get('feedback.product') }}</th>
											<th>{{ Lang::get('feedback.invoice_status') }}</th>
											<th>{{ Lang::get('feedback.review_for') }}</th>
										</tr>
									</thead>

									<tbody>
										<?php $logged_user_id = BasicCUtil::getLoggedUserId(); ?>
										@foreach($awaiting_invoices_list as $invoice)
											<?php $product_view_url = $productService->getProductViewURL($invoice['product_id'], $invoice); ?>
											<tr>
												<td><a href="{{$product_view_url}}"><strong>{{{nl2br($invoice['product_name'])}}}</a></td>
												<td>
												<?php
														if(count($invoice) > 0)
														{
															if($invoice['invoice_status'] == 'pending')
															{
																$lbl_class = "label-warning";
															}
																elseif($invoice['invoice_status'] == 'completed')
															{
																$lbl_class = " label-success";
															}
																elseif($invoice['invoice_status'] == 'refund_requested')
															{
																$lbl_class = " label-warning";
															}
																elseif($invoice['invoice_status'] == 'refunded')
															{
																$lbl_class = " label-primary";
															}
																elseif($invoice['invoice_status'] == 'refund_rejected')
															{
																$lbl_class = " label-danger";
															}
														else
															{
																$lbl_class = "label-default";
															}
														}
													?>
													<span class="label {{ $lbl_class }}">{{ trans('myPurchases.status_txt_'.$invoice['invoice_status']) }}</span>
												</td>
												<td>
													@if($logged_user_id == $invoice['item_owner_id'])
														<?php
															if(!isset($all_user_details[$invoice['buyer_id']]))
															$all_user_details[$invoice['buyer_id']] = CUtil::getUserDetails($invoice['buyer_id']);
															$user_details = $all_user_details[$invoice['buyer_id']];
														?>
														<p class="margin-bottom-5">
															<i class="fa fa-shopping-cart text-muted"></i>
															<span>{{ Lang::get('feedback.buyer') }}</span>:
															<a href="{{$user_details['profile_url']}}"><strong>{{$user_details['display_name']}}</strong></a>
														</p>
														@if($invoice['buyer_id'] == $invoice['item_owner_id'])
															<!--<p><a href="{{Url::action('FeedbackController@getAddFeedback', array($invoice['id']))}}" class="label bg-blue-steel label-xs">
															{{ Lang::get('feedback.write_review_for_buyer') }}</a></p> -->
														@endif
													@else
														<?php
															if(!isset($all_user_details[$invoice['item_owner_id']]))
															$all_user_details[$invoice['item_owner_id']] = CUtil::getUserDetails($invoice['item_owner_id']);
															$user_details = $all_user_details[$invoice['item_owner_id']];
														?>
														<p  class="margin-bottom-5">
															<i class="fa fa-tags text-muted"></i> <span>{{ Lang::get('feedback.seller') }}</span>:
															<a href="{{$user_details['profile_url']}}" class="text-info"><strong>{{$user_details['display_name']}}</strong></a>
														</p>
														<p><a href="{{Url::action('FeedbackController@getAddFeedback',array($invoice['id']))}}" class="label bg-green-jungle label-xs">
														{{ Lang::get('feedback.write_review_for_seller') }}</a></p>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							@if(count($awaiting_invoices_list) > 0)
								<div class="text-right">
									{{ $awaiting_invoices_list->appends(array('view_type' => Input::get('view_type')))->links() }}
								</div>
							@endif
						@else
							@if($view_type == 'awaiting')
								<p class="note note-info margin-0">{{ Lang::get('common.no_awaiting_feedbacks') }}</p>
							@else
								<p class="note note-info margin-0">{{ Lang::get('common.no_feedback_added_received') }}</p>
							@endif
						@endif
					@endif

					@if($view_type=='feedback_completed')
						@if(count($completed_feedbacks_list) > 0)
							<h2 class="title-one">{{ Lang::get('feedback.completed_feedback') }}</h2>
							<div class="table-responsive margin-bottom-30">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>{{ Lang::get('feedback.product') }}</th>
											<th class="col-md-4">{{ Lang::get('feedback.comment') }}</th>
											<th>{{ Lang::get('feedback.from') }}</th>
											<th>{{ Lang::get('feedback.to') }}</th>
											<th>{{ Lang::get('feedback.date') }}</th>
											<th>{{ Lang::get('feedback.action') }}</th>
										</tr>
									</thead>

									<tbody>
										<?php $logged_user_id = BasicCUtil::getLoggedUserId(); ?>
										@foreach($completed_feedbacks_list as $invoice)
											<?php
												$product_view_url = $productService->getProductViewURL($invoice['product_id'], $invoice);
												$from_user_id = ($invoice['buyer_id']); //== $logged_user_id)?$invoice['buyer_id']:$invoice['seller_id'];
												$to_user_id = ($invoice['seller_id']);// == $logged_user_id)?$invoice['seller_id']:$invoice['seller_id'];
												if(!isset($all_user_details[$to_user_id]))
												$all_user_details[$to_user_id] = CUtil::getUserDetails($to_user_id);
												$to_user_details = $all_user_details[$to_user_id];
												if(!isset($all_user_detail[$from_user_id]))
												$all_user_detail[$from_user_id] = CUtil::getUserDetails($from_user_id);
												$from_user_detail = $all_user_detail[$from_user_id];
											?>
											<tr>
												<td>
													<p><span class="text-muted">{{ Lang::get('feedback.invoice_id') }}</span> <strong>{{$invoice['id']}}</strong></p>
													<p><a href="{{$product_view_url}}"><strong>{{{nl2br($invoice['product_name'])}}}</strong></a></p>
													<?php
														if(count($invoice) > 0)
														{
															if($invoice['feedback_remarks'] == 'Neutral')
															{
																$lbl_class = "label-primary";
															}
																elseif($invoice['feedback_remarks'] == 'Positive')
															{
																$lbl_class = " label-success";
															}
																elseif($invoice['feedback_remarks'] == 'Negative')
															{
																$lbl_class = " label-danger";
															}
														else
															{
																$lbl_class = "label-default";
															}
														}
													?>
													<span class="text-muted">{{ Lang::get('feedback.feedback') }}:</span>
													<span class="label {{ $lbl_class }}">{{$invoice['feedback_remarks']}}</span>
												</td>
												<td><div class="wid-330">{{ nl2br($invoice['feedback_comment']) }}</div></td>
												<td><a href="{{$from_user_detail['profile_url']}}" class="text-success"><strong>{{$from_user_detail['display_name']}}</strong></a></td>
												<td><a href="{{$to_user_details['profile_url']}}" class="text-success"><strong>{{$to_user_details['display_name']}}</strong></a></td>
												<td>{{ CUtil::FMTDate($invoice['feedback_updated_at'], 'Y-m-d H:i:s', ''); }}</td>
												<td class="action-btn">
													@if ($invoice['buyer_id'] == $logged_user_id)
														<a href="{{ URL::action('FeedbackController@getUpdateFeedback',$invoice['feedback_id']) }}" title="{{ Lang::get('common.edit') }}" class="btn btn-xs blue"><i class="fa fa-edit"></i></a>
														<a href="javascript:void(0)" title="{{ Lang::get('common.delete') }}" onclick="doAction('{{ $invoice['feedback_id'] }}', 'delete')" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
													@else
														--
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>

							@if(count($completed_feedbacks_list) > 0)
								<div class="text-right">
									{{ $completed_feedbacks_list->appends(array('view_type' => Input::get('view_type')))->links() }}
								</div>
							@endif
						@else
							@if($view_type == 'awaiting')
								<p class="note note-info margin-0">{{ Lang::get('common.no_awaiting_feedbacks') }}</p>
							@else
								<p class="note note-info margin-0">{{ Lang::get('common.no_feedback_added_received') }}</p>
							@endif
						@endif
					@endif
				</div>
			</div>
			<!-- END: FEEDBACK LIST -->
		</div>
	</div>
	{{ Form::open(array('action' => array('FeedbackController@postFeedbackAction'), 'method'=>'post', 'id' => 'feedbackActionfrm')) }}
		{{Form::hidden('feedback_id','delete',array('id'=>'feedback_id'))}}
		{{Form::hidden('feedback_action','delete',array('id'=>'feedback_action'))}}
	{{Form::close()}}

	<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
		<span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-product-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
	      $('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.show_search_filters') }} <i class="fa fa-caret-down"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.hide_search_filters') }} <i class="fa fa-caret-up"></i>');
	        }
	        return false;
	    });

	    function doAction(p_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{Lang::get('feedback.confirm_delete_feedback')}} ');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('feedback.feedback') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#feedback_id').val(p_id);
						$('#feedback_action').val(selected_action);
						document.getElementById("feedbackActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}
		function openViewShippingPopup(order_id) {
            var actions_url = '{{ URL::action('PurchasesController@getViewShippingPopup') }}';
            var postData = 'order_id='+order_id;
            fancybox_url = actions_url + '?' + postData;
            $.fancybox({
                maxWidth    : 800,
                maxHeight   : 432,
                fitToView   : false,
                width       : '70%',
                height      : '432',
                autoSize    : false,
                closeClick  : false,
                type        : 'iframe',
                href        : fancybox_url,
                openEffect  : 'none',
                closeEffect : 'none',
                /*afterClose  : function() {
                     window.location.reload();
                }*/
            });
        };

		$(".fn_signuppop").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 630,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });

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
	</script>
@stop