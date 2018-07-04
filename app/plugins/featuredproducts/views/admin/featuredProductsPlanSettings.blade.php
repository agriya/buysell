@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
     <!-- BEGIN: ALERT MESSAGE -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{   Session::get('success_message') }}</div>
    @endif
    @if (Session::has('warning_message') && Session::get('warning_message') != "")
        <div class="note note-warning">{{   Session::get('warning_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{    Session::get('error_message') }}</div>
    @endif
	<!-- END: ALERT MESSAGE -->

    <div class="portlet box blue-madison">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				{{$d_arr['actionicon']}} {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: FEATURED SELLERS FORM -->
            {{ Form::model($d_arr['featured_prod_plans'], [
            'method' => 'post',
            'id' => 'featured_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data'
            ]) }}
            	{{ Form::hidden('feature_id', $d_arr['id']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('featured_days') ? 'error' : '' }}}">
						{{ Form::label('featured_days', trans("featuredproducts::featuredproducts.featured_days"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							{{ Form::text('featured_days', Input::get("featured_days"), array ('class' => 'form-control')); }}
							<label class="error">{{{ $errors->first('featured_days') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('featured_price') ? 'error' : '' }}}">
						{{ Form::label('featured_price', trans("featuredproducts::featuredproducts.featured_price").' ('.Config::get('generalConfig.site_default_currency').')' , array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							{{ Form::text('featured_price', Input::get("featured_price"), array ('class' => 'form-control price')); }}
							<label class="error">{{{ $errors->first('featured_price') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                        {{ Form::label('status', trans('featuredproducts::featuredproducts.status'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                        	{{ Form::select('status', $d_arr['status_arr'], Input::get("status"), array('class' => 'form-control bs-select')) }}
                        	<label class="error">{{{ $errors->first('featured_price') }}}</label>
                        </div>
                    </div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						@if($d_arr['mode'] == 'edit')
							<button type="submit" name="edit_featured" class="btn green" id="edit_featured" value="edit_featured">
								<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                            </button>
						@else
							<button type="submit" name="add_featured" class="btn green" id="add_featured" value="add_featured">
								<i class="fa fa-check"></i> {{trans("common.submit")}}
                            </button>
						@endif
						<button type="reset" name="cancel_fetured" class="btn default" onclick="window.location = '{{ url::to('admin/featuredproducts/manage-featured-product-plans') }}'">
							<i class="fa fa-times"></i> {{trans("common.cancel")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: FEATURED SELLERS FORM -->
        </div>
    </div>

    {{ Form::model($d_arr['featured_prod_plans'], [
        'method' => 'get',
        'id' => 'featuredProdPlanList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-cogs"><sup class="fa fa-list font11"></sup></i> {{ trans('featuredproducts::featuredproducts.list_featured_product_plan') }}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: FEATURED SELLERS TABLE -->
            <div class="portlet-body clearfix">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <tr>
                            	<th>{{ trans('featuredproducts::featuredproducts.featured_days')}}</th>
                                <th>{{ trans('featuredproducts::featuredproducts.featured_price')}}</th>
                                <th width="180">{{ trans('featuredproducts::featuredproducts.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
							@if(count($details) > 0)
	                            @foreach($details as $featured)
	                                <tr>
	                                    <td>{{ $featured->featured_days }}</td>
										<td>{{ CUtil::convertAmountToCurrency($featured->featured_price, Config::get('generalConfig.site_default_currency'), '', true) }}</td>
	                                    <td class="status-btn">
											<a class="btn btn-info btn-xs" title="{{trans('common.edit')}}" href="{{ url::to('admin/featuredproducts/manage-featured-product-plans')}}?id={{$featured->featured_prod_plan_id}}"><i class="fa fa-edit"></i></a>
											@if($featured->status == 'Active')
                                            	<a href="{{ URL::to('admin/featuredproducts/change-plan-status').'?action=Inactive&feature_id='.$featured->featured_prod_plan_id }}" class="fn_dialog_confirm btn red btn-xs" action="Inactive" title="{{ Lang::get('featuredproducts::featuredproducts.deactivate') }}"><i class="fa fa-ban"></i></a>
                                            @else
                                                <a href="{{ URL::to('admin/featuredproducts/change-plan-status').'?action=Active&feature_id='.$featured->featured_prod_plan_id }}" class="btn btn-success btn-xs fn_dialog_confirm green" action="Active" title="{{ Lang::get('featuredproducts::featuredproducts.activate') }}"><i class="fa fa-check"></i></a>
                                            @endif
	                                    </td>
	                                </tr>
	                            @endforeach
                            @else
                                <tr>
                                    <td colspan="4"><p class="alert alert-info">{{ trans('featuredproducts::featuredproducts.no_featured_product_plan') }}</p></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

				<!--- BEGIN: PAGINATION --->
				@if(count($details) > 0)
					<div class="dataTables_paginate paging_bootstrap text-right">
						{{ $details->appends(array())->links() }}
					</div>
				@endif
				<!--- END: PAGINATION --->
            </div>
            <!--  END: FEATURED SELLERS TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";
        $("#featured_frm").validate({
            rules: {
                featured_days: {
                    required: true,
                    number: true
                },
                featured_price: {
                    required: true,
                }
            },
            messages: {
                featured_days: {
                    required: mes_required,
                    number: jQuery.validator.format("{{trans('common.number_validation')}}")
                },
                featured_price: {
                    required: mes_required
                }
            },

            highlight: function (element) { // hightlight error inputs
               $(element)
                .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                .closest('.form-group').removeClass('has-error'); // set error class to the control group
            }

        });

        var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(".fn_dialog_confirm").click(function(){
			var atag_href = $(this).attr("href");
			var action = $(this).attr("action");
			var cmsg = "";
			switch(action){
				case "Active":
					cmsg = "{{ Lang::get('featuredproducts::featuredproducts.activate_confirm') }}";
					break;

				case "Inactive":
					cmsg = "{{ Lang::get('featuredproducts::featuredproducts.deactivate_confirm') }}";
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
    </script>
@stop