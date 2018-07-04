@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<a href="{{ URL::action('FeedbackController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('feedback.back_to_feedbacks_list') }}
				</a>
				@if($action=='add')
				   	<h1>{{ Lang::get('feedback.add_feedback') }}</h1>
				@else
					<h1>{{ Lang::get('feedback.edit_feedback') }}</h1>
				@endif
			</div>
			<!-- PAGE TITLE END -->

			<!-- ALERT BLOCK STARTS -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- ALERT BLOCK ENDS -->

			<!-- ADD FEEDBACK STARTS -->
			<div class="well">
				{{-- Form::open(array('action' => array('FeedbackController@postAddFeedback'), 'id'=>'addFeedbackFrm', 'method'=>'post','class' => 'form-horizontal' )) --}}
				{{ Form::model($feedback_details, ['method' => 'post','class' => 'form-horizontal', 'id' => 'addAddressfrm']) }}
					<div id="selSrchProducts">
						<fieldset>

							<div class="form-group">
								{{ Form::label('product', Lang::get('feedback.product'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-4 margin-top-8">
									<?php $product_view_url = $productService->getProductViewURL($invoice_details['product_id'], $invoice_details); ?>
									<a href="{{$product_view_url}}">{{$invoice_details['product_name']}}</a>
								</div>
							</div>


							<div class="form-group">
								<?php
									/*if(isset($review_for) && $review_for == 'seller')
									{
										$lable = Lang::get('feedback.review_for_buyer');
										$user_details = CUtil::getUserDetails($invoice_details['item_owner_id']);
									}
									else
									{*/
										$lable = Lang::get('feedback.review_for_seller');
										$user_details = CUtil::getUserDetails($invoice_details['item_owner_id']);
									/*}*/
								 ?>
								{{ Form::label('review', $lable, array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-4 margin-top-8">
									<a href="{{$user_details['profile_url']}}">{{$user_details['display_name']}}</a>
								</div>
							</div>

							<div class="form-group {{{ $errors->has('feedback_remarks') ? 'error' : '' }}}">
								{{ Form::label('feedback_remarks', Lang::get('feedback.feedback_remarks'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-5 adjmrg-lft0">
									<?php
										$feedback_remarks_positive = false;
										$feedback_remarks_negative = false;
										$feedback_remarks_neutral = true;
										if(isset($p_details['feedback_remarks']))
										{
											$feedback_remarks_positive = ($feedback_details['feedback_remarks'] == "Positive")?true:false;
											$feedback_remarks_no = ($feedback_details['feedback_remarks'] == "Negative")?true:false;
											$feedback_remarks_neutral = ($feedback_details['feedback_remarks'] == "Neutral")?true:false;
										}
										//if(count(Input::old()))
										//{
										//	$feedback_remarks = Input::old('feedback_remarks');
										//}
									?>
									<label class="radio-inline margin-right-10">
										{{Form::radio('feedback_remarks','Positive', Input::old('feedback_remarks',$feedback_remarks_positive), array('class' => '')) }}
										{{trans('feedback.positive')}}
									</label>
									<label class="radio-inline margin-right-10">
										{{Form::radio('feedback_remarks','Negative', Input::old('feedback_remarks',$feedback_remarks_negative) , array('class' => '')) }}
										{{trans('feedback.negative')}}
									</label>
									<label class="radio-inline margin-right-10">
										{{Form::radio('feedback_remarks','Neutral', Input::old('feedback_remarks',$feedback_remarks_neutral) , array('class' => '')) }}
										{{trans('feedback.neutral')}}
									</label>
									<label class="error">{{{ $errors->first('feedback_remarks') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('rating', trans('feedback.rating'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
									<div class="margin-top-5">
										{{ Form::input('number','rating',null, array('id' => 'input-21f', 'min' => 0, 'max' => 5, 'step' => 0.1, 'data-size' => 'sm'))}}
										<!--<input id="input-21f" name="rating" value="0" type="number" min=0 max=5 step=0.1 data-size="md" >-->
									</div>
									<label class="error">{{{ $errors->first('rating') }}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('feedback_comment', trans('feedback.comment'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-6">
									{{ Form::textarea('feedback_comment', null, array('class' => 'form-control valid', 'cols' => '8', 'rows' => '12')) }}
									<label class="error">{{{ $errors->first('feedback_comment') }}}</label>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-9">
									{{ Form::hidden('action',$action) }}
									@if($action=='add')
                                        <button type="submit" class="btn green">
                                            <i class="fa fa-plus"></i> {{trans('feedback.add_feedback')}}
                                        </button>
                                    @else
                                        <button type="submit" class="btn blue-madison">
                                            <i class="fa fa-edit"></i> {{trans('feedback.udpate_feedback')}}
                                        </button>
                                    @endif
									<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('FeedbackController@getIndex') }}'"><i class="fa fa-times"></i> {{ Lang::get('myAddresses.cancel') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
			</div>
			<!-- ADD FEEDBACK END -->
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var mes_required = '{{ Lang::get('auth/form.required') }}';
		$(document).ready(function() {

			 $("#input-21f").rating({
	            starCaptions: function(val) {
	                //if (val < 5) {
	                    return val;
	                //} else {
	                //    return 'high';
	                //}
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


			$("#addAddressfrm").validate({
                rules: {
	                city: {
						required: true
					},
					state: {
						required: true
					},
					country_id: {
						required: true
					},
					zip_code: {
						required: true
					}
				},
	            messages: {
	                city: {
						required: mes_required
					},
					state: {
						required: mes_required
					},
					country_id: {
						required: mes_required
					},
					zip_code: {
						required: mes_required
					}
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
            });
        });
	</script>
@stop