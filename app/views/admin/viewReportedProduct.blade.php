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
    <h1 class="page-title">{{Lang::get('admin/reportedProduct.reported_product')}}</h1>
    <!-- END: PAGE TITLE -->

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-warning"></i> {{ Lang::get('admin/reportedProduct.report_for_the_product') }}
            </div>
            <a class="btn default btn-xs purple-stripe pull-right" href="{{Url::action('AdminReportedProductsController@getIndex')}}" title="{{Lang::get('admin/reportedProduct.back_to_reports')}}"><i class="fa fa-chevron-left"></i> {{Lang::get('admin/reportedProduct.back_to_reports')}}</a>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
        	@if(!empty($product_details))
				<?php
						$p_img_arr = $prod_obj->getProductImage($product_details['id']);
                        $p_thumb_img = $productService->getProductDefaultThumbImage($product_details['id'], 'small', $p_img_arr);
                        $view_url = $productService->getProductViewURL($product_details['id'], $product_details);
				?>

				<div class="well well-new clearfix bg-colf9">
					<div class="pull-left mr10 mb5 custom-feature">
                    	<figure>
                            <a href="{{ $view_url }}" class="img81x61 imgalt81x61">
                                <img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" />                            
                                {{ CUtil::showFeaturedProductIcon($product_details['id'], $product_details) }}
                            </a>
                        </figure>
					</div>
					<div class="pull-left">
						<a href="{{$view_url}}" title="{{{ $product_details['product_name']  }}}" class="fonts18">{{{ $product_details['product_name'] }}}</a>
						<p class="mt5">
							{{ Lang::get('common.product_code')}}:
							<a href="{{$view_url}}" title="{{{ $product_details['product_name']  }}}">{{{ $product_details['product_code'] }}}</a>
						</p>
					</div>
				</div>
        	@endif

            @if(count($report_details) > 0 )
            	<!--- BEGIN: REPORTED_PRODUCT LIST --->
                <div class="table-responsive mb30">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="col-md-3">{{ Lang::get('admin/reportedProduct.reported_by') }}</th>
                                <th class="col-md-4">{{ Lang::get('admin/reportedProduct.reports') }}</th>
                                <th class="col-md-4">{{ Lang::get('admin/reportedProduct.custom_message') }}</th>
                                <th class="col-md-2">{{ Lang::get('admin/reportedProduct.date') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($report_details as $report_user)
                                <tr>
                                    <td>
                                        <p><a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$report_user['user_id'] }}">{{$report_user['user_name']}}</a></p>
                                        <p>(<a class="text-muted" target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$report_user['user_id'] }}">{{ BasicCUtil::setUserCode($report_user['user_id'])}}</a>)</p>
                                    </td>
                                    <td>
                                        @if(!empty($report_user['reported_threads']))
                                            @foreach($report_user['reported_threads'] as $thread)
                                                <p><span class="pull-left">&raquo;</span><span class="ml15 show">{{Lang::get('admin/reportedProduct.thread_txt_'.$thread)}}</span></p>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <div class="wid-330">{{$report_user['custom_message']}}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted wid100">{{CUtil::FMTDate($report_user['created_at'], 'Y-m-d H:i:s', '')}}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
                 <!--- END: REPORTED_PRODUCT LIST --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/reportedProduct.no_reports_found') }}</div>
            @endif
            
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>
@stop