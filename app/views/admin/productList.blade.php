@extends('admin')
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
                    <i class="fa fa-search"></i> {{ trans('admin/productList.product_search_title') }}
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
                                        {{ Form::label('search_product_code', trans('admin/productList.product_search_product_code'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_product_code', Input::get("search_product_code"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_product_code" generated="true">{{$errors->first('search_product_code')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('search_product_name', trans('admin/productList.product_search_product_name'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('search_product_name', Input::get("search_product_name"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_product_name" generated="true">{{$errors->first('search_product_name')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('search_product_from_date', trans('admin/productList.product_added_date'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
			                                <div class="input-group date date-picker input-daterange" data-date-format="dd-mm-yyyy">
			                                    {{ Form::text('search_product_from_date', Input::old('search_product_from_date', Input::get('search_product_from_date')), array('id'=>"search_product_from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
												<label for="search_product_to_date" class="input-group-addon">{{ trans('admin/productList.product_to') }}</label>
			                                    {{ Form::text('search_product_to_date', Input::old('search_product_to_date', Input::get('search_product_to_date')), array('id'=>"search_product_to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
			                                </div>
			                                <label class="error">{{{ $errors->first('search_product_from_date') }}}</label>
			                            </div>
                                    </div>

                                    @if(CUtil::chkIsAllowedModule('featuredproducts'))
										<div class="form-group">
		                                    {{ Form::label('search_featured_product', Lang::get('product.product_listing_featured'), array('class' => 'col-md-4 control-label')) }}
		                                    <div class="col-md-6">
		                                        {{ Form::select('search_featured_product', array('' => Lang::get('common.select'), 'Yes' => Lang::get('common.yes'), 'No' => Lang::get('common.no')), Input::get("search_featured_product"), array('class' => 'form-control bs-select')) }}
		                                    </div>
		                                </div>
	                                @endif
                                </div>

                                <div class="col-md-6">
	                                <div class="form-group">
	                                    {{ Form::label('search_product_category', trans('admin/productList.product_search_product_category'), array('class' => 'col-md-4 control-label')) }}
	                                    <div class="col-md-6">
	                                        {{ Form::select('search_product_category', $d_arr['category_arr'], Input::get("search_product_category"), array('class' => 'form-control select2me')) }}
	                                        <label class="error" for="search_product_category" generated="true">{{$errors->first('search_product_category')}}</label>
	                                    </div>
	                                </div>

									<div class="form-group">
	                                    {{ Form::label('search_product_status', trans('admin/productList.product_search_status'), array('class' => 'col-md-4 control-label')) }}
	                                    <div class="col-md-6">
	                                        {{ Form::select('search_product_status', $d_arr['status_arr'], Input::get("search_product_status"), array('class' => 'form-control select2me')) }}
	                                        <label class="error" for="search_product_status" generated="true">{{$errors->first('search_product_status')}}</label>
	                                    </div>
	                                </div>

                                    <div class="form-group">
                                        {{ Form::label('search_seller_code', trans('admin/productList.product_search_seller_code'), array('class' => 'col-md-4 control-label')) }}
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
                <i class="fa fa-list"></i> {{ $d_arr['product_list_title'] }} List
            </div>
		    <a href="{{ URL:: to('admin/product/add') }}" class="btn default purple-stripe btn-xs pull-right"><i class="fa fa-plus-circle"></i> {{trans('admin/productList.add_product')}}</a>
        </div>
        <!-- TABLE TITLE END -->

        <div class="portlet-body">
            {{ Form::open(array('id'=>'productsListfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('admin/productList.product_details') }}</th>
                                {{-- @if(Config::get('generalConfig.user_allow_to_add_product')) --}}
                                    <th>{{ trans('admin/productList.product_author') }}</th>
                                {{-- @endif --}}
                                <th>{{ trans('admin/productList.product_price') }}</th>
                                <th>{{ trans('admin/productList.product_stocks') }}</th>
                                <th>{{ trans('admin/productList.product_status') }}</th>
                                <!--@if(CUtil::chkIsAllowedModule('featuredproducts'))
								<th class="col-md-1">{{ Lang::get('featuredproducts::featuredproducts.featured') }}</th>
								@endif-->
                                <th><div class="wid118">{{ trans('admin/productList.product_action') }}</div></th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(count($products_arr) > 0)
                                <?php
                                    $p_service = new ProductService();
									$allow_variation = (CUtil::chkIsAllowedModule('variations')) ? 1 : 0;
                                ?>
                                @foreach($products_arr as $key => $prd)
                                    <tr>
                                        <?php
                                            $shipping_template_name = CUtil::getSippingTemplateName($prd->shipping_template);
                                            //echo $shipping_template_name;exit;
                                            $product_view_url = $p_service->getProductViewURL($prd->id, $prd);
                                            $user_details = CUtil::getUserDetails($prd->product_user_id);
                                            $category_arr = $service_obj->getProductCategoryArr($prd->product_category_id);
                                            $price_group = $service_obj->getPriceGroupsDetailsNew($prd->id, 0);
                                            $user_name = $user_details['display_name'];
											$site_default_currency = Config::get('generalConfig.site_default_currency');
                                        ?>
                                        <td>
                                            <div class="wid-280">
                                                <div class="mb15">
                                                    <p>{{ HTML::link($product_view_url, $prd->product_name, array('target' => '_blank')) }}</p>
                                                    <p class="text-muted"> {{{ implode(' / ', $category_arr) }}} </p>
                                                </div>

                                                <div class="mb15">
                                                    <p>
                                                        <span class="text-bold">{{ trans('admin/productList.product_product_code') }}:</span>
                                                        {{ HTML::link($product_view_url, $prd->product_code, array('target' => '_blank')) }}
                                                    </p>

                                                    <p>
                                                        <span class="text-bold">{{ trans('admin/productList.product_id') }}:</span>
                                                        {{ HTML::link($product_view_url, $prd->id, array('target' => '_blank')) }}
                                                    </p>
                                                </div>
                                                <p>
                                                    {{ trans('admin/productList.product_added_date') }}:
                                                    <span class="text-muted">{{ CUtil::FMTDate($prd['product_added_date'], "Y-m-d", "") }}</span>
                                                 </p>
                                                <p>
                                                    {{ trans('admin/productList.product_update_date') }}:
                                                    <span class="text-muted">{{ CUtil::FMTDate($prd['last_updated_date'], "Y-m-d H:i:s", "") }}</span>
                                                </p>
                                                <p>
                                                    {{ trans('admin/productList.product_added_by') }}:
                                                    <span class="text-muted"><a href="{{ URL::to('admin/users/user-details').'/'.$prd->product_user_id }}" title="{{ $user_name }}">{{ $user_name }}</a> (<strong><a href="{{ URL::to('admin/users/user-details').'/'.$prd->product_user_id }}" title="{{ $user_name }}">{{ $user_details['user_code'] }}</a></strong>)</span>
                                                </p>
                                                <p>
                                                    {{ trans('admin/productList.product_shipping_template') }}:
                                                    @if(count($shipping_template_name) > 0)
                                                        <span class="text-muted">{{ $shipping_template_name }}</span>
                                                    @else
                                                        <span class="text-muted">{{ trans('admin/productList.template_not_select') }}</span>
                                                    @endif
                                                </p>


                                                @if($allow_variation && $prd->use_variation > 0 && $prd->variation_group_id > 0)
	                                                <p class="label label-success" title="{{ Lang::get('variations::variations.variation_available_tip') }}"> {{ Lang::get("variations::variations.variation_available") }}</p>
                                                @endif

                                            </div>
                                        </td>
                                        {{-- @if(Config::get('generalConfig.user_allow_to_add_product')) --}}
                                            <td>
                                                <a href="{{ URL::to('admin/users/user-details').'/'.$prd->product_user_id }}" title="{{ $user_name }}">{{ $user_details['display_name'] }}</a>
                                                (<strong><a href="{{ URL::to('admin/users/user-details').'/'.$prd->product_user_id }}" title="{{ $user_name }}">{{ $user_details['user_code'] }}</a></strong>)
                                            </td>
                                        {{-- @endif  --}}
                                        <td>
                                            @if($prd->is_free_product == 'Yes')
                                                <p class="text-success"><strong class="badge badge-primary">{{ trans('admin/productList.product_free') }}</strong></p>
                                            @else
                                                <div class="dl-horizontal-new dl-horizontal">
                                                    <!-- this block span gave for colon -->
                                                    <dl>
                                                        <dt>{{ trans('admin/productList.product_purchase_price') }}</dt>
                                                        <dd><span>{{ CUtil::convertAmountToCurrency($prd->purchase_price, $site_default_currency, '', true) }}</span></dd>
                                                    </dl>
                                                    @if(count($price_group) > 0)
                                                        <dl>
                                                            <dt>{{ trans('admin/productList.product_product_price') }}</dt>
                                                            <dd><span>{{ CUtil::convertAmountToCurrency($price_group['price'], $site_default_currency, '', true) }}</span></dd>
                                                        </dl>
                                                        @if($price_group['price'] > $price_group['discount'])
                                                            <dl>
                                                                <dt>{{ trans('admin/productList.product_discount_price') }}</dt>
                                                                <dd><span>{{ CUtil::convertAmountToCurrency($price_group['discount'], $site_default_currency, '', true) }}</span></dd>
                                                            </dl>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($allow_variation && $prd->use_variation > 0 && $prd->variation_group_id > 0)
	                                             <a href="{{ Request::root().'/variations/vartion-stock/'.$prd->id }}" class="view-stock-sales">{{ Lang::get("variations::variations.click_here") }}</span>
                                            @else
												<?php
                                                    $stock_details =  $service_obj->getProductStocks($prd->id);
                                                    //echo "<pre>";print_r($stock_details);echo "</pre>";
                                                ?>
                                                @if($stock_details)
                                                    @if(isset($stock_details['quantity']))
                                                        <p>{{ trans('admin/productList.product_stocks') }}: <strong>{{ $stock_details['quantity'] }}</strong></p>
                                                    @endif
                                                @else
                                                    --
                                                @endif
                                           @endif
                                        </td>
                                        <td>
                                            <?php
                                                $lbl_class = "label-default";
                                                if(count($prd) > 0)
                                                    {
                                                   if($prd['product_status'] == "Ok" && $prd['date_expires'] != "9999-12-31 00:00:00" && date('Y-m-d', strtotime($prd['date_expires'])) < date('Y-m-d'))
                                                   		{
                                                   			$lbl_class ="label-warning";
                                                   			$display_product_status = 'Expired';
														}
                                                   elseif($prd['product_status'] == 'Ok')
                                                        {
                                                            $lbl_class = "label-success";
                                                            $display_product_status = $prd->product_status;
                                                        }
                                                    elseif($prd['product_status'] == 'ToActivate')
                                                        {
                                                            $lbl_class = "label-warning";
                                                            $display_product_status = $prd->product_status;
                                                        }
                                                    elseif($prd['product_status'] == 'NotApproved' || $prd['product_status'] == 'Deleted')
                                                        {
                                                            $lbl_class = "label-danger";
                                                            $display_product_status = $prd->product_status;
                                                        }
                                                    elseif($prd['product_status'] == 'Draft')
                                                        {
                                                            $lbl_class = "label-default";
                                                           $display_product_status = $prd->product_status;
                                                        }
                                                }
                                            ?>
                                            <p class="label {{ $lbl_class }}">{{ $service_obj->product_status_arr[$display_product_status] }}</p>
                                        </td>
                                        <td class="status-btn">
                                            <div class="clearfix">
                                                @if($prd['product_status']!='Deleted')
                                                    <a href="{{ URL:: to('admin/product/add?id='.$prd->id) }}" class="btn btn-xs blue" title="{{ trans('admin/productList.product_edit') }}">
                                                    <i class="fa fa-edit"></i></a>
                                                @endif
                                                <a target="_blank" href="{{$product_view_url}}" class="btn btn-info btn-xs" title="{{ trans('admin/productList.product_view') }}">
                                                <i class="fa fa-eye"></i></a>
                                                @if($d_arr['allow_to_change_status'])
                                                    @if($prd->product_status == 'ToActivate')
                                                        <a class="fn_changeStatus btn btn-xs green" href="{{ URL::to('admin/product/list/change-status?p_id='.$prd->id) }}" title="{{ trans('admin/productList.change_status') }}"><i class="fa fa-cog"></i></a>
                                                    @endif
                                                    @if($prd['product_status']!='Deleted')
                                                        <a href="javascript:void(0)" onclick="doAction('{{ $prd->id }}', 'delete')" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
                                                    @endif
                                                @endif
                                            </div>
                                            @if($prd['product_status']!='Deleted' && CUtil::chkIsAllowedModule('featuredproducts'))
                                                @if($prd->is_featured_product == "Yes" && (strtotime($prd->featured_product_expires) >= strtotime(date('Y-m-d'))))
                                                    <p class="mt10 label label-info"><i class="fa fa-check"></i> {{ Lang::get('featuredproducts::featuredproducts.featured') }}</p>
                                                @elseif($prd->product_status == "Ok")
                                                    <p class="mt10"><a href="{{ URL::to('featuredproducts/set-as-featured?id='.$prd->id) }}" class="btn btn-xs green fn_changeStatus">{{ trans('featuredproducts::featuredproducts.set_as_featured') }}</a></p>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <p class="note note-info">{{ trans('admin/productList.product_no_products_to_list') }}</p>
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

    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('admin/product/list/product-action'))) }}
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
				if(selected_action == 'delete')
				{
					$('#dialog-product-confirm-content').html('{{ trans('admin/productList.product_confirm_delete') }}');
				}
				else if(selected_action == 'feature')
				{
					$('#dialog-product-confirm-content').html('{{ trans('admin/productList.product_confirm_featured') }}');
				}
				else if(selected_action == 'unfeature')
				{
					$('#dialog-product-confirm-content').html('{{ trans('admin/productList.product_confirm_unfeatured') }}');
				}
				$("#dialog-product-confirm").dialog({ title: '{{ trans('admin/productList.product_head') }}', modal: true,
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

			$(".view-stock-sales").fancybox({
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
		@endif
	</script>
@stop