@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
	<!-- BEGIN: INFO BLOCK -->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif

    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- END: INFO BLOCK -->

    {{ Form::open(array('id'=>'productssSearchfrm', 'method'=>'get','class' => 'form-horizontal search-form' )) }}
    	<div class="portlet box blue-madison">
            <!-- SEARCH TITLE STARTS -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('featuredproducts::featuredproducts.product_search_title') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- SEARCH TITLE END -->

            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                        	<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('search_product_code', trans('featuredproducts::featuredproducts.product_search_product_code'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_product_code', Input::get("search_product_code"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_product_code" generated="true">{{$errors->first('search_product_code')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('search_product_from_date', trans('featuredproducts::featuredproducts.product_added_date'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
			                                <div class="input-group date date-picker input-daterange" data-date-format="dd-mm-yyyy">
			                                    {{ Form::text('search_product_from_date', Input::old('search_product_from_date', Input::get('search_product_from_date')), array('id'=>"search_product_from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
												<label for="search_product_to_date" class="input-group-addon">{{ trans('common.to') }}</label>
			                                    {{ Form::text('search_product_to_date', Input::old('search_product_to_date', Input::get('search_product_to_date')), array('id'=>"search_product_to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
			                                </div>
			                                <label class="error">{{{ $errors->first('search_product_from_date') }}}</label>
			                            </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                	<div class="form-group">
                                        {{ Form::label('search_product_name', trans('featuredproducts::featuredproducts.product_search_product_name'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_product_name', Input::get("search_product_name"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_product_name" generated="true">{{$errors->first('search_product_name')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('search_seller_code', trans('featuredproducts::featuredproducts.product_search_seller_code'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_seller_code', Input::get("search_seller_code"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_product_code" generated="true">{{$errors->first('search_seller_code')}}</label>
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
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'">
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
    	<!-- TABLE TITLE STARTS -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ $d_arr['product_list_title'] }} {{ trans('common.list') }}
            </div>
        </div>
        <!-- TABLE TITLE END -->

        <div class="portlet-body">
            {{ Form::open(array('id'=>'productsListfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('featuredproducts::featuredproducts.product_details') }}</th>
                                <th>{{ trans('featuredproducts::featuredproducts.product_author') }}</th>
                                <th>{{ trans('featuredproducts::featuredproducts.product_price') }}</th>
                                <th>{{ trans('featuredproducts::featuredproducts.product_status') }}</th>
                                <th>{{ trans('featuredproducts::featuredproducts.product_feature_expiry_date') }}</th>
                                <th><div class="wid118">{{ trans('featuredproducts::featuredproducts.product_action') }}</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($products_arr) > 0)
                                <?php
                                    $prod_service = new ProductService();
                                ?>
                                @foreach($products_arr as $key => $prd)
                                    <tr>
                                        <?php
                                            $shipping_template_name = CUtil::getSippingTemplateName($prd->shipping_template);
                                            //echo $shipping_template_name;exit;
                                            $product_view_url = $prod_service->getProductViewURL($prd->id, $prd);
                                            $user_details = CUtil::getUserDetails($prd->product_user_id);
                                            $category_arr = $prod_list_service->getProductCategoryArr($prd->product_category_id);
                                            $price_group = $prod_list_service->getPriceGroupsDetailsNew($prd->id, 0);
                                            $user_name = $user_details['display_name'];
                                        ?>
                                        <td>
                                            <div class="wid-280">
                                                <div class="mb15">
                                                    <p>{{ HTML::link($product_view_url, $prd->product_name, array('target' => '_blank')) }}</p>
                                                    <p class="text-muted"> {{{ implode(' / ', $category_arr) }}} </p>
                                                </div>

                                                <div class="mb15">
                                                    <p>
                                                        <span class="text-bold">{{ trans('featuredproducts::featuredproducts.product_product_code') }}:</span>
                                                        {{ HTML::link($product_view_url, $prd->product_code, array('target' => '_blank')) }}
                                                    </p>

                                                    <p>
                                                        <span class="text-bold">{{ trans('featuredproducts::featuredproducts.product_id') }}:</span>
                                                        {{ HTML::link($product_view_url, $prd->id, array('target' => '_blank')) }}
                                                    </p>
                                                </div>
                                                <p>
                                                    {{ trans('featuredproducts::featuredproducts.product_added_date') }}:
                                                    <span class="text-muted">{{ CUtil::FMTDate($prd['product_added_date'], "Y-m-d", "") }}</span>
                                                 </p>
                                                <p>
                                                    {{ trans('featuredproducts::featuredproducts.product_update_date') }}:
                                                    <span class="text-muted">{{ CUtil::FMTDate($prd['last_updated_date'], "Y-m-d H:i:s", "") }}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ URL::to('admin/users/user-details').'/'.$prd->product_user_id }}" title="{{ $user_name }}">{{ $user_details['display_name'] }}</a>
                                            (<strong><a href="{{ URL::to('admin/users/user-details').'/'.$prd->product_user_id }}" title="{{ $user_name }}">{{ $user_details['user_code'] }}</a></strong>)
                                        </td>
                                        <td>
                                            @if($prd->is_free_product == 'Yes')
                                                <p class="text-success"><strong class="badge badge-primary">{{ trans('featuredproducts::featuredproducts.product_free') }}</strong></p>
                                            @else
                                                <div class="dl-horizontal-new dl-horizontal">
                                                    <!-- this block span gave for colon -->
                                                    <dl>
                                                        <dt>{{ trans('featuredproducts::featuredproducts.product_purchase_price') }}</dt>
                                                        <dd><span>{{ CUtil::convertAmountToCurrency($prd->purchase_price, Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                    </dl>
                                                    @if(count($price_group) > 0)
                                                        <dl>
                                                            <dt>{{ trans('featuredproducts::featuredproducts.product_product_price') }}</dt>
                                                            <dd><span>{{ CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                        </dl>
                                                        @if($price_group['price'] > $price_group['discount'])
                                                            <dl>
                                                                <dt>{{ trans('featuredproducts::featuredproducts.product_discount_price') }}</dt>
                                                                <dd><span>{{ CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd>
                                                            </dl>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                                if(count($prd) > 0) {
                                                    if($prd['product_status'] == 'Ok')
                                                            $lbl_class = "label-success";
                                                    elseif($prd['product_status'] == 'ToActivate')
                                                            $lbl_class = "label-warning";
                                                    elseif($prd['product_status'] == 'NotApproved')
                                                            $lbl_class = "label-danger";
                                                    elseif($prd['product_status'] == 'Draft')
                                                            $lbl_class = "label-default";
                                                }
                                            ?>
                                            <p class="label {{ $lbl_class }}">{{ $prod_list_service->product_status_arr[$prd->product_status] }}</p>
                                        </td>
                                        <td>
                                            {{ CUtil::FMTDate(date('Y-m-d', strtotime($prd->featured_product_expires)), 'Y-m-d', '') }}
                                        </td>
                                        <td class="status-btn">
                                            <a href="javascript:void(0)" onclick="doAction('{{ $prd->id }}', 'unfeature')" class="btn btn-xs red">{{ trans('featuredproducts::featuredproducts.product_featured_remove') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <p class="note note-info">{{ trans('featuredproducts::featuredproducts.product_no_products_to_list') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    {{ $products_arr->appends( array('search_product_from_date' => Input::get('search_product_from_date'), 'search_product_to_date' => Input::get('search_product_to_date'), 'search_product_name' => Input::get('search_product_name'), 'search_product_category' => Input::get('search_product_category'), 'search_featured_product' => Input::get('search_featured_product'), 'search_product_author' => Input::get('search_product_author'), 'search_product_status' => Input::get('search_product_status'),'serach_submit' =>'search_submit'))->links() }}
                </div>
            {{ Form::close() }}
        </div>
    </div>

    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('admin/featuredproducts/product-action'))) }}
        {{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
        {{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
    {{ Form::close() }}

    <div id="dialog-product-confirm" title="" style="display:none;">
        <span class="ui-icon ui-icon-alert"></span>
        <span id="dialog-product-confirm-content" class="show ml15"></span>
    </div>
@stop

@section('script_content')
	<script type="text/javascript">
		@if($d_arr['allow_to_change_status'])
			function doAction(p_id, selected_action)
			{
				if(selected_action == 'unfeature')
				{
					$('#dialog-product-confirm-content').html('{{ trans('featuredproducts::featuredproducts.product_confirm_unfeatured') }}');
				}
				$("#dialog-product-confirm").dialog({ title: '{{ trans('featuredproducts::featuredproducts.product_featured_head') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$(this).dialog("close");
							$('#product_action').val(selected_action);
							$('#p_id').val(p_id);
							document.getElementById("productsActionfrm").submit();
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