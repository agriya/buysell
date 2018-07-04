@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!-- BEGIN: INFO BLOCK -->
	@if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
    <!-- END: INFO BLOCK -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('featuredsellers::featuredsellers.search_featured_sellers') }}
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
			                            {{ Form::label('id', trans('featuredsellers::featuredsellers.user_id'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('id', Input::get("id"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
			                        <div class="form-group">
			                            {{ Form::label('shop_name', trans('featuredsellers::featuredsellers.shop_name'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('shop_name', Input::get("shop_name"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
			                        <div class="form-group">
										{{ Form::label('status', trans('featuredsellers::featuredsellers.user_status'),array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::select('status', $status,Input::get("status"), array('class' =>'form-control bs-select')) }}
										</div>
									</div>
									<div class="form-group">
										{{ Form::label('shop_status', trans('featuredsellers::featuredsellers.shop_status'),array('class' => 'control-label col-md-4')) }}
										<div class="col-md-6">
											{{ Form::select('shop_status', $shop_status,Input::get("shop_status"), array('class' =>'form-control bs-select')) }}
										</div>
									</div>
								</div>
								<div class="col-md-6">
			                        <div class="form-group">
			                            {{ Form::label('user_name', trans('featuredsellers::featuredsellers.name'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('user_name', Input::get("user_name"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
			                        <div class="form-group">
			                            {{ Form::label('name', trans('featuredsellers::featuredsellers.user_name'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('name', Input::get("name"), array('class' => 'form-control')) }}
			                            </div>
			                        </div>
			                        <div class="form-group">
			                            {{ Form::label('user_email', trans('featuredsellers::featuredsellers.email'), array('class' => 'control-label col-md-4')) }}
			                            <div class="col-md-6">
			                                {{ Form::text('user_email', Input::get("user_email"), array('class' => 'form-control', "placeholder" => trans('admin/manageMembers.user_email'))) }}
			                            </div>
			                        </div>
			                        <div class="form-group">
			                            {{ Form::label('user_code', trans('featuredsellers::featuredsellers.user_code'), array('class' => 'control-label col-md-4')) }}
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
		                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/featuredsellers/manage-featured-sellers') }}'">
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
                <i class="fa fa-list"></i> {{ trans('featuredsellers::featuredsellers.featured_sellers_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(sizeof($user_list) > 0 )
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                                <th>{{ trans('featuredsellers::featuredsellers.shop_details') }}</th>
                                <th>{{ trans('featuredsellers::featuredsellers.user_details') }}</th>
                                <th>{{ trans('featuredsellers::featuredsellers.user_email_label') }}</th>
                                <th>{{ trans('featuredsellers::featuredsellers.shop_status') }}</th>
                                <th>{{ trans('featuredsellers::featuredsellers.seller_feature_expiry_date') }}</th>
                                <th><div class="wid100">{{ trans('default.action') }}</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_list as $usr)
                            	<?php $url_slug = $usr->url_slug;?>
                                <tr>
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
									<td class="text-muted">{{ CUtil::FMTDate(date('Y-m-d', strtotime($usr->featured_seller_expires)), 'Y-m-d', '') }}</td>
                                    <td class="status-btn">
										<a href="javascript:void(0)" onclick="doAction('{{ $usr->user_id }}', 'unfeature')" class="btn btn-xs red">{{ trans('featuredsellers::featuredsellers.featured_sellers_remove') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>

				<div class="clearfix">
					<!-- BEGIN: PAGINATION -->
					<div class="text-right">
						{{ $user_list->appends(array('user_name' => Input::get('user_name'), 'user_code' => Input::get('user_code'), 'is_shop_owner' => Input::get('is_shop_owner'), 'name' => Input::get('name'), 'id' => Input::get('id'), 'is_allowed_to_add_product' => Input::get('is_allowed_to_add_product'), 'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date'), 'user_email' => Input::get('user_email'), 'status' => Input::get('status'), 'shop_status' => Input::get('shop_status'), 'shop_name' => Input::get('shop_name'), 'search_members' => Input::get('search_members'), 'group_name_srch' => Input::get('group_name_srch')))->links() }}
					</div>
					<!-- END: PAGINATION -->
				</div>
            @else
                <div class="alert alert-info mar0">{{ trans('featuredsellers::featuredsellers.no_featured_sellers_found_to_list') }}</div>
            @endif
    	</div>
    </div>

    {{ Form::open(array('id'=>'sellersActionfrm', 'method'=>'post', 'url' => URL::to('admin/featuredsellers/sellers-action'))) }}
        {{ Form::hidden('seller_id', '', array('id' => 'seller_id')) }}
        {{ Form::hidden('seller_action', '', array('id' => 'seller_action')) }}
    {{ Form::close() }}

    <div id="dialog-product-confirm" title="" style="display:none;">
        <span class="ui-icon ui-icon-alert"></span>
        <span id="dialog-product-confirm-content" class="show ml15"></span>
    </div>
@stop

@section('script_content')
	<script type="text/javascript">
		@if($d_arr['allow_to_change_status'])
			function doAction(seller_id, selected_action)
			{
				if(selected_action == 'unfeature')
				{
					$('#dialog-product-confirm-content').html('{{ trans('featuredsellers::featuredsellers.seller_confirm_unfeatured') }}');
				}
				$("#dialog-product-confirm").dialog({ title: '{{ trans('featuredsellers::featuredsellers.featured_sellers') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$(this).dialog("close");
							$('#seller_action').val(selected_action);
							$('#seller_id').val(seller_id);
							document.getElementById("sellersActionfrm").submit();
						}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
					}
				});

				return false;
			}

			$(function() {
	            $('#search_product_from_date').datepicker({
	                format: 'yyyy-mm-dd',
	                autoclose: true,
	                todayHighlight: true
	            });
	            $('#search_product_to_date').datepicker({
	                format: 'yyyy-mm-dd',
	                autoclose: true,
	                todayHighlight: true
	            });
	        });
		@endif
	</script>
@stop