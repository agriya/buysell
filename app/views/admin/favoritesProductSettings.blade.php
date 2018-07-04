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

        <div class="portlet-body form">
            <!-- BEGIN: FAVORITE PRODUCT FORM -->
            {{ Form::model($d_arr['favorites_details'], [
            'method' => 'post',
            'id' => 'favorites_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data'
            ]) }}
            	{{ Form::hidden('settings_id', $d_arr['id']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('product_code') ? 'error' : '' }}}">
						{{ Form::label('product_code', trans("admin/indexSettings.product_code"), array('class' => 'col-md-3 control-label required-icon', 'autocomplete' => 'off')) }}
						<div class="col-md-4">
							{{ Form::text('product_code', null, array ('class' => 'form-control', 'autocomplete' => 'off')); }}
							{{ Form::hidden('srch_product_id', Input::get("srch_product_id"), array("id" => "srch_product_id")) }}
							<label class="error">{{{ $errors->first('product_code') }}}</label>
						</div>
					</div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						@if($d_arr['mode'] == 'edit')
							<button type="submit" name="edit_favorites" class="btn green" id="edit_favorites" value="edit_favorites">
								<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                            </button>
						@else
							<button type="submit" name="add_favorites" class="btn green" id="add_favorites" value="add_favorites">
								<i class="fa fa-check"></i> {{trans("common.submit")}}
                            </button>
						@endif
						<button type="reset" name="cancel_fetured" class="btn default" onclick="window.location = '{{ url::to('admin/manage-favorite-products') }}'">
							<i class="fa fa-times"></i> {{trans("common.cancel")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: FAVORITE PRODUCT FORM -->
        </div>
    </div>
    {{ Form::model($d_arr['favorites_details'], [
        'method' => 'get',
        'id' => 'favoritesProductsList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption">{{$d_arr['actionicon']}} List {{$d_arr['pageTitle']}}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: FAVORITE PRODUCT TABLE -->
            <div class="portlet-body clearfix">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <tr>
                                <th>{{ trans('admin/indexSettings.product_code') }}</th>
                                <th>{{ trans('admin/indexSettings.product_name') }}</th>
                                <th width="180">{{ trans('common.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
							@if(count($details) > 0)
	                            @foreach($details as $favorites)
	                            	<?php
	                            		$prod_id = $favorites->product_id;
										$product = Products::initialize($prod_id);
										$product->setIncludeDeleted(true);
										$product->setIncludeBlockedUserProducts(true);
										$p_details = $product->getProductDetails();
										$prod_code = $prod_name = $prod_url = $prod_status = '';
										if(count($p_details) > 0) {
											$prod_code = $p_details['product_code'];
											$prod_name = $p_details['product_name'];
											$prod_status = $p_details['product_status'];
											$productService = new ProductService();
											$prod_url = $productService->getProductViewURL($prod_id, $p_details);
										}
									?>
	                                <tr>
	                                    <td>
											<p><a href="{{ $prod_url }}" target="_blank">{{ $prod_code }}</a></p>
											@if($prod_status == 'Deleted')
												<p><span class="label label-default">{{ $prod_status }}</span></p>
											@endif
										</td>
	                                    <td><a href="{{ $prod_url }}" target="_blank">{{ $prod_name }}</a></td>
	                                    <td class="status-btn">
											{{--<a class="btn btn-info btn-xs" title="{{trans('common.edit')}}" href="{{ url::to('admin/manage-favorite-products')}}?id={{$favorites->favorite_id}}"><i class="fa fa-edit"></i></a>--}}
											<a class="btn btn-info btn-xs fn_dialog_confirm red" title="{{trans('common.delete')}}" href="{{ url::to('admin/manage-favorite-products/delete')}}?id={{$favorites->favorite_id}}"><i class="fa fa-trash-o"></i></a>
	                                    </td>
	                                </tr>
	                            @endforeach
                            @else
                                <tr>
                                    <td colspan="3"><p class="alert alert-info">{{ trans('admin/indexSettings.no_favorite_products') }}</p></td>
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
            <!--  END: FAVORITE PRODUCT TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('common.required')}}";
        $("#favorites_frm").validate({
            rules: {
                product_code: {
                    required: true,
                }
            },
            messages: {
                product_code: {
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

        var common_ok_label = "{{ trans('common.yes') }}" ;
            var common_no_label = "{{ trans('common.cancel') }}" ;
            var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
            $(window).load(function(){
             $(".fn_dialog_confirm").click(function(){

                    var atag_href   = $(this).attr("href");
                    var cmsg        = "{{trans('common.uploader_confirm_delete')}}";
                    var txtCancel = common_no_label;
                    var buttonText = {};
                    buttonText['Yes'] = function(){
                                                Redirect2URL(atag_href);
                                                $( this ).dialog( "close" );
                                            };
                    buttonText['No'] = function(){
                                                $(this).dialog('close');
                                            };



                    $("#fn_dialog_confirm_msg").html(cmsg);
                    $("#fn_dialog_confirm_msg").dialog({
                        resizable: false,
                        height:240,
                        width: 360,
                        modal: true,
                        title: cfg_site_name,
                        buttons:buttonText
                    });
                    return false;
                });
            });

        $(window).load(function(){
			$.ajax({
				url: '{{ URL::to("admin/product-auto-complete") }}',
				dataType: "json",
				success: function(data)
				{
					var cat_data = $.map(data, function(item, val)
					{
						return {
							user_id: val,
							label: item
						};
					});

					$("#product_code").autocomplete({
						delay: 0,
						source: cat_data,
						minlength:3,
						select: function (event, ui) {
							$('#srch_product_id').val(ui.item.user_id);
							return ui.item.label;
						},
						change: function (event, ui) {
							if (!ui.item) {
								$('#srch_product_id').val('');
							}
						}
					});
				}
			});
        });
    </script>
@stop