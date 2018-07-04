@extends('admin')
	<?php
        $header_title = ($action == 'add')? 'add_title' : 'edit_title';
        $product_status = 'Draft';
    ?>
@section('content')
	<!-- ERROR INFO STARTS -->
    <div id="error_msg_div"></div>
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!-- ERROR INFO END -->

    <!-- SUCCESS INFO STARTS -->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
		<?php Session::forget('success_message'); ?>
    @endif

    @if(isset($d_arr['product_user_details']['shop_status']) && !$d_arr['product_user_details']['shop_status'])
		<div class="note note-danger">{{ trans("product.shop_inactive_product_wont_display_error_msg") }}
			{{trans('common.you_can')}} <a href="{{ URL::to('admin/shops').'?user_code='.$d_arr['product_user_details']['user_code'] }}">{{trans('admin/manageMembers.manage_shop')}}</a> {{trans('common.here')}}
		</div>
    @endif
    <!-- SUCCESS INFO END -->

    <!-- ERROR INFO STARTS -->
    @if(Session::has('validate_tab_arr') && count(Session::get('validate_tab_arr')) > 0 )
        <?php
            $validate_tab_arr = array_filter(Session::get('validate_tab_arr'), function($value){ return ($value)? false: true;});
            $product_status = 'Ok';
        ?>
        <div class="note note-danger">
            {{{ trans("product.tab_validation_err") }}}
            @foreach($validate_tab_arr AS $key => $value)
                <p>{{ $service_obj->p_tab_lang_arr[$key] }}</p>
            @endforeach
        </div>
        <?php Session::forget('validate_tab_arr'); ?>
    @endif
    <!-- ERROR INFO END -->

    <!-- PAGE TITLE STARTS -->
    <ul class="pull-right list-inline list-inline-alt">
        <!-- PRODUCT STATUS STARTS -->
        <?php
        $status = trans('product.status_in_draft');
        $lbl_class = "label-default";
        if(count($p_details) > 0)
        {
            if($p_details['product_status'] == "Ok" && $p_details['date_expires'] != "9999-12-31 00:00:00" && date('Y-m-d', strtotime($p_details['date_expires'])) < date('Y-m-d')) {
				$status =  trans('product.status_expired');
				$lbl_class ="label-warning";
			}
			elseif($p_details['product_status'] == 'Ok')
            {
                $status = trans('product.status_active');
                $lbl_class = "label-success";
            }
            elseif($p_details['product_status'] == 'ToActivate')
            {
                $status = trans('product.status_to_activate');
                $lbl_class = "label-warning";
            }
            elseif($p_details['product_status'] == 'NotApproved')
            {
                $status = trans('product.status_in_not_approved');
                $lbl_class = "label-danger";
            }
        }
        ?>
        <li>{{ trans("product.product_status_caption") }}: <span id="product_status_text" class="label {{ $lbl_class }}">{{ $status }}</span></li>
        <li><a href="{{ URL::to('admin/product/list') }}" class="btn btn-xs blue-stripe default"><i class="fa fa-chevron-left"></i>{{ trans("admin/productAdd.back_to_list") }}</a></li>
        <!-- PRODUCT STATUS END -->
    </ul>
    <h1 class="page-title">{{trans("product.".$header_title)}}@if(isset($p_details['product_name']) && $p_details['product_name']!='') - "{{{$p_details['product_name']}}}" @endif</h1>
    <!-- PAGE TITLE END -->

    <div class="clearfix">
        <!-- PRODUCT ADD STEPS STARTS -->
        <div class="tabbable-custom tabbable-customnew">
            <div class="mobilemenu mobmenu-only">
                <!-- MOBILE TOGGLER STARTS -->
                <button class="btn btn-primary btn-sm mobilemenu-bar mb10"><i class="fa fa-chevron-down"></i> Menu</button>
                <!-- MOBILE TOGGLER END -->

                <!-- TABS STARTS -->
                <ul class="nav nav-tabs mbldropdown-menu ac-custom-tabs">
                    @foreach($d_arr['tab_list'] AS $tab_index => $value)
                        <?php
                            $activeClass = ($tab_index == $d_arr['p']) ? 'active' : '';
                            $link = ($value) ? URL::to('admin/product/add?p='.$tab_index.'&id='.$p_id) : 'javascript:void(0)';
                        ?>
                        <li class="{{ $activeClass }}"><a href="{{ $link }}"><span>{{ isset($service_obj->p_tab_lang_arr[$tab_index]) ? $service_obj->p_tab_lang_arr[$tab_index] : ucwords($tab_index); }}</span></a></li>
                    @endforeach
                </ul>
                <!-- TABS END -->
            </div>

            <!-- BASIC INFO STARTS -->
            @if($d_arr['p'] == 'basic')
                {{ Form::model($p_details, ['url' => $p_url, 'method' => 'post', 'id' => 'addProductfrm', 'class' => 'form-horizontal']) }}
                    <div class="portlet box blue-hoki">
                        <!-- TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-file-text-o"></i> {{trans('product.basic_details')}}
                            </div>
                        </div>
                        <!-- TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">

                                <p class="note note-danger">{{{ trans("product.required_text") }}}</p>
                                <div class="form-group {{{ $errors->has('user_code_display') ? 'error' : '' }}}">
                                    {{ Form::label('user_code_display', trans("admin/productAdd.user_code"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        @if($action == 'add' )
                                            {{ Form::text('user_code_display', Input::get("user_code_display"), array('class' => 'form-control valid', 'autocomplete' => 'off', 'placeholder' => trans('admin/productAdd.serach_by_user_code'))); }}
                                            {{ Form::hidden('user_code', $d_arr['user_code'], array("id" => "user_code")) }}
                                            <label class="error fn_usercodeErr">{{{ $errors->first('user_code') }}}</label>
                                        @else
                                            <p class="form-control-static">{{ $d_arr['product_user_details']['user_code'] }}</p>
                                            {{ Form::hidden('user_code', $d_arr['product_user_details']['user_code'], array('id' => 'user_code')) }}
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('is_downloadable_product') ? 'error' : '' }}}">
                                    {{ Form::label('is_downloadable_product', trans("product.is_downloadable"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        <?php
                                            $is_downloadable_product = 'No';
                                            $is_downloadable_product_yes = false;
                                            $is_downloadable_product_no = true;
                                            if(isset($p_details['is_downloadable_product']))
                                            {
                                            $is_downloadable_product_yes = ($p_details['is_downloadable_product'] == "Yes")?true:false;
                                            $is_downloadable_product_no = ($p_details['is_downloadable_product'] == "No")?true:false;
                                            }
                                            //if(count(Input::old()))
                                            //{
                                            //	$is_downloadable_product = Input::old('is_downloadable_product');
                                            //}
                                        ?>
                                        <div class="radio-list">
                                            <label class="radio-inline">
                                                {{Form::radio('is_downloadable_product','Yes', Input::old('is_downloadable_product',$is_downloadable_product_yes), array('class' => 'radio')) }}
                                                <label>{{ trans('common.yes') }}</label>
                                            </label>
                                            <label class="radio-inline">
                                                {{Form::radio('is_downloadable_product','No', Input::old('is_downloadable_product',$is_downloadable_product_no) , array('class' => 'radio')) }}
                                                <label>{{trans('common.no')}}</label>
                                            </label>
                                        </div>
                                        <label class="error">{{{ $errors->first('is_downloadable_product') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('product_name') ? 'error' : '' }}}">
                                    {{ Form::label('product_name', trans("product.title"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('product_name', null, array('class' => 'form-control valid', 'onchange' => 'generateSlugUrl()', 'onblur' => 'generateSlugUrl()')); }}
                                        <label class="error">{{{ $errors->first('product_name') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('url_slug') ? 'error' : '' }}}">
                                    {{ Form::label('url_slug', trans("admin/productAdd.url_slug"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        {{ Form::text('url_slug', null, array('class' => 'form-control valid')); }}
                                        <label class="error" for="url_slug" generated="true">{{$errors->first('url_slug')}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('product_description') ? 'error' : '' }}}">
                                    {{ Form::label('product_description', trans("product.description"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-8">
                                        {{  Form::textarea('product_description', null, array('class' => 'form-control valid fn_editor', 'rows' => '7')); }}
                                        <label class="error">{{{ $errors->first('product_description') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('product_description') ? 'error' : '' }}}">
                                    {{ Form::label('product_support_content', trans('product.support_content'), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-8">
                                        {{  Form::textarea('product_support_content', null, array('class' => 'form-control fn_editor', 'rows' => '7')); }}
                                        <label class="error">{{{ $errors->first('product_support_content') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('product_highlight_text') ? 'error' : '' }}}">
                                    {{ Form::label('product_highlight_text', trans("product.summary"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-6">
                                        {{  Form::textarea('product_highlight_text', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                        <label class="error">{{{ $errors->first('product_highlight_text') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('product_category_id') ? 'error' : '' }}}">
                                    {{ Form::label('product_category_id', trans("product.category"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div id="sub_categories" class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-8">
                                                @if($d_arr['top_cat_count'] <=1)
                                                    {{ Form::select('product_category_id', $category_main_arr, $category_id, array('class' => 'form-control bs-select mb10 valid', 'onchange' => "listSubCategories('product_category_id', '1')", 'id' => 'product_category_id')) }}
                                                @else
                                                    <?php $i = 1; ?>
                                                    @foreach($category_sub_arr AS $cat_id => $sub_arr)
                                                        <?php
                                                            $drop_name = ($i==1)? 'product_category_id' : 'sub_category_id_'.$cat_id;
                                                            $next_sel_class = 'fn_subCat_'.$cat_id;
                                                            $selected_val = (isset($d_arr['top_cat_list_arr'][$i]))? $d_arr['top_cat_list_arr'][$i] : '';
                                                            $i++;
                                                        ?>
                                                        @if(count($sub_arr) > 1)
                                                            {{ Form::select($drop_name, $sub_arr, $selected_val, array('id' => $drop_name, 'class' => 'form-control bs-select mb10 valid '.$next_sel_class, 'onchange' => "listSubCategories('".$drop_name."',". $cat_id.")")) }}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="col-md-1">
                                                <div style="display:none" id="loading_sub_category"><img src="{{URL::asset('images/general/loading.gif')}}" alt="loading" /></div>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="error">{{{ $errors->first('product_category_id') }}}</label>
                                </div>

                                <div id="sel_addSection" class="form-group add-section" style="display:none;">
                                    {{ Form::label('section_name', trans("product.new_section_name"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-md-7">
                                                {{  Form::text('section_name', null, array('class' => 'form-control valid', 'id' => 'section_name')); }}
                                                <label class="error fn_sectionErr">{{{ $errors->first('section_name') }}}</label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mt5">
                                                    <a href="javascript: void(0);" class="fn_saveSection btn btn-xs green"><i class="fa fa-save"></i> {{ trans("product.save_section") }}</a>
                                                    <a href="javascript: void(0);" class="fn_saveSectionCancel btn btn-xs default"><i class="fa fa-times"></i> {{ trans("common.cancel") }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('user_section_id') ? 'error' : '' }}}">
                                    {{ Form::label('user_section_id', trans("product.section_name"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-8">
                                                {{ Form::select('user_section_id', $section_arr, null, array('class' => 'form-control select2me valid')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript: void(0);" class="fn_addSection btn btn-info btn-xs mt5"><i class="fa fa-plus-circle"></i> {{ trans("product.add_section") }}</a>
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('user_section_id') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('demo_url') ? 'error' : '' }}}">
                                    {{ Form::label('demo_url', trans("product.demo_url"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('demo_url', null, array('class' => 'form-control valid')); }}
                                        <label class="error">{{{ $errors->first('demo_url') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('demo_details') ? 'error' : '' }}}">
                                    {{ Form::label('demo_details', trans("product.demo_details"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-6">
                                        {{  Form::textarea('demo_details', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                        <label class="error">{{{ $errors->first('demo_details') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('product_tags') ? 'error' : '' }}}">
                                    {{ Form::label('tags', trans("product.product_tags"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('product_tags', null, array('class' => 'form-control valid')); }}
                                        <p><small class="text-muted">{{ trans("product.tags_place_holder") }}</small></p>
                                        <label class="error">{{{ $errors->first('product_tags') }}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('my_selected_categories', $d_arr['my_selected_categories'], array('id' => 'my_selected_categories')) }}
                                {{ Form::hidden('my_category_id', $d_arr['my_category_id'], array('id' => 'my_category_id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                @if($action == 'add')
                                    <div class="col-md-offset-3 col-md-8">
                                        <button name="add_product" id="add_product" value="add_product" type="submit" class="btn green">
                                            <i class="fa fa-check"></i> {{ trans("product.save_and_proceed") }}
                                        </button>
                                    </div>
                                @else
                                    <div class="col-md-offset-3 col-md-8">
                                        <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                            <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- BASIC INFO END -->

            <!-- PRICE INFO STARTS -->
            @if($d_arr['p'] == 'price')
                <?php
                    $p_details['product_discount_fromdate'] = ($p_details['product_discount_fromdate'] != '0000-00-00')? date('d/m/Y', strtotime($p_details['product_discount_fromdate'])):'';
                    $p_details['product_discount_todate'] = ($p_details['product_discount_todate'] != '0000-00-00')? date('d/m/Y', strtotime($p_details['product_discount_todate'])):'';
                    //To set default price value is empty instead of 0.00
                    $p_details['purchase_price'] = ($p_details['purchase_price'] == '0.00' || $p_details['purchase_price'] == '')? '0' : $p_details['purchase_price'];
                    $p_details['product_price'] = ($p_details['product_price'] == '0.00')? '' : $p_details['product_price'];
                    $p_details['product_discount_price'] = ($p_details['product_discount_price'] == '0.00')? '' : $p_details['product_discount_price'];
                    $price_currency = Config::get('generalConfig.site_default_currency');
                ?>
                {{ Form::model($p_details, ['url' => $p_url, 'method' => 'post', 'id' => 'addProductPricefrm', 'class' => 'form-horizontal']) }}
                    <div class="portlet box blue-hoki">
                        <!-- TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-file-text"><sup class="fa fa-money font11"></sup></i> {{trans('product.price_details')}}
                            </div>
                        </div>
                        <!-- TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="form-group fn_clsPriceFields {{{ $errors->has('purchase_price') ? 'error' : '' }}}">
                                    {{ Form::label('purchase_price', trans("product.purchase_price"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">{{$price_currency}}</span>
                                            {{  Form::text('purchase_price', null, array('class' => 'price form-control valid')); }}
                                        </div>
                                        <label class="error" id="errorpurchase_">{{{ $errors->first('purchase_price') }}}</label>
                                    </div>
                                </div>

                                @if(Config::get("webshoppack.can_upload_free_product"))
                                    <div class="form-group {{{ $errors->has('is_free_product') ? 'error' : '' }}}">
                                        {{ Form::label('is_free_product', trans("product.free_product"), array('class' => 'col-md-3 control-label required-icon')) }}
                                        <div class="col-md-4">
                                            <?php
                                                $is_free_product_yes = false;
                                                $is_free_product_no = true;
                                                if(isset($p_details['is_free_product']) && $p_details['is_free_product'] == 'Yes')
                                                {
                                                    $is_free_product_yes = ($p_details['is_free_product'] == "Yes")?true:false;
                                                    $is_free_product_no = ($p_details['is_free_product'] == "No")?true:false;
                                                }
                                            ?>

                                            <div class="radio-list">
                                                <label class="radio-inline">
                                                    {{Form::radio('is_free_product','Yes', Input::old('is_free_product',$is_free_product_yes), array('class' => 'radio')) }}
                                                    <label>{{ trans('common.yes')}}</label>
                                                </label>
                                                <label class="radio-inline">
                                                    {{Form::radio('is_free_product','No', Input::old('is_free_product',$is_free_product_no) , array('class' => 'radio')) }}
                                                    <label>{{ trans('common.no')}}</label>
                                                </label>
                                            </div>
                                            <label class="error">{{{ $errors->first('is_free_product') }}}</label>
                                        </div>
                                    </div>
                                @endif

                                <!--- Hided as we implement group prices--->
                                <!--
                                <div class="form-group fn_clsPriceFields {{{ $errors->has('product_price') ? 'error' : '' }}}">
                                    {{ Form::label('product_price', trans("product.product_price"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">{{$price_currency}}</span>
                                            {{  Form::text('product_price', null, array('class' => 'form-control valid')); }}
                                        </div>
                                        <label class="error">{{{ $errors->first('product_price') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_clsPriceFields {{{ $errors->has('product_discount_price') ? 'error' : '' }}}">
                                    {{ Form::label('product_discount_price', trans("product.product_price_after_discount"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">{{$price_currency}}</span>
                                            {{  Form::text('product_discount_price', null, array('class' => 'form-control valid')); }}
                                        </div>
                                        <label class="error">{{{ $errors->first('product_discount_price') }}}</label>
                                    </div>
                                </div>
                                -->
                                <div id="selGroupPriceBlock" style="display:none;">
                                	<?php
										$product_price_details = $d_arr['product_price_info'];
										$price_details = (isset($product_price_details['price_details'][0])) ? $product_price_details['price_details'][0] : array();
									?>
                                    <div class="mb40">
                                        @if(count($price_details) > 0)
                                            <div class="clsGroup_{{ $product_price_details['id']}} clsGroupPrices form-group fn_clsPriceFields" id="{{ $price_details['group_field'] }}">
                                                @if ($product_price_details['id'] == 0)
                                                    {{ Form::label($price_details['range_start_field'], $product_price_details['name'].' ('.$price_currency.')', array('class' => 'col-md-3 control-label '.$product_price_details['required'])) }}
                                                @else
                                                    <label class="col-md-3 control-label font0">&nbsp;</label>
                                                @endif
                                                <div id="{{ $price_details['group_elements_field'] }}" class="clsGroupFields_{{ $product_price_details['id']}} col-md-9 custom-priceqty">
                                                    <div class="clearfix">

                                                        <div class="priceqty-price" id="{{ 'selInput_'.$price_details['price_field'] }}">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><label for="{{ $price_details['price_field'] }}">{{trans('product.price_tab')}}</label></span>
                                                                <input type="text" value="{{ CUtil::formatAmount($price_details['price']) }}" name="{{ $price_details['price_field'] }}" id="{{ $price_details['price_field'] }}" class="price form-control" />
                                                            </div>
                                                        </div>

                                                        <div class="priceqty-disc" id="{{ 'selInput_'.$price_details['discount_percentage_field'] }}">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><label for="{{ $price_details['discount_percentage_field'] }}">{{trans('product.discount')}} %</label></span>
                                                                <input type="text" value="{{ CUtil::formatAmount($price_details['discount_percentage']) }}" name="{{ $price_details['discount_percentage_field'] }}" id="{{ $price_details['discount_percentage_field'] }}" class="discount-percentage form-control" />
                                                            </div>
                                                        </div>

                                                        <div class="priceqty-discprice" id="{{ 'selInput_'.$price_details['discount_field'] }}">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><label for="{{ $price_details['discount_field'] }}">{{ trans('product.discounted_price')}}</label></span>
                                                                <input type="text" value="{{ CUtil::formatAmount($price_details['discount']) }}" name="{{ $price_details['discount_field'] }}" id="{{ $price_details['discount_field'] }}" class="discount form-control" readonly="readonly"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label class="error" style="display: none" id="{{ $price_details['error_field'] }}"></label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!--- START: Sridharan asked to hide this  --->
                                <!--
                                <div class="form-group fn_clsPriceFields {{{ $errors->has('product_discount_fromdate') ? 'error' : '' }}}">
                                    {{ Form::label('product_discount_fromdate', trans("product.discount_from_date"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy">
                                            {{ Form::text('product_discount_fromdate', null, array('id'=>"product_discount_fromdate", 'class'=>'form-control', 'maxlength'=>'100')) }}
                                            <span class="input-group-btn">
                                                <button class="btn default" name="product_discount_fromdate" type="button" onclick="$('#product_discount_fromdate').focus()"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <label class="error">{{{ $errors->first('product_discount_fromdate') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_clsPriceFields {{{ $errors->has('product_discount_fromdate') ? 'error' : '' }}}">
                                    {{ Form::label('product_discount_todate', trans("product.discount_to_date"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy">
                                            {{ Form::text('product_discount_todate', null, array('id'=>"product_discount_todate", 'class'=>'form-control', 'maxlength'=>'100')) }}
                                            <span class="input-group-btn">
                                                <button class="btn default" name="product_discount_todate" type="button" onclick="$('#product_discount_todate').focus()"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <label class="error">{{{ $errors->first('product_discount_todate') }}}</label>
                                    </div>
                                </div>
                                -->
                                <!--- END: Sridharan asked to hide this  --->

                                <!--
                                <div class="form-group fn_clsPriceFields {{{ $errors->has('global_transaction_fee_used') ? 'error' : '' }}}">
                                    {{ Form::label('global_transaction_fee_used', trans("admin/productAdd.global_transaction_fee_used"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="checkbox-list">
                                            <?php
                                                $global_transaction_fee_used = $p_details['global_transaction_fee_used'];
                                                if(count(Input::old()))
                                                {
                                                    $global_transaction_fee_used = Input::old('global_transaction_fee_used');
                                                }
                                            ?>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="global_transaction_fee_used"  id="global_transaction_fee_used" class="checkbox ace" value="Yes" @if($global_transaction_fee_used == 'Yes') checked="checked" @endif>
                                                <label class="lbl"></label>
                                            </label>
                                            <label class="error">{{{ $errors->first('global_transaction_fee_used') }}}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group fn_clsFeeFields {{{ $errors->has('site_transaction_fee_type') ? 'error' : '' }}}">
                                    {{ Form::label('site_transaction_fee_type', trans("admin/productAdd.site_transaction_fee_type"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <div class="radio-list">
                                            <label class="radio-inline">
                                                {{ Form::radio('site_transaction_fee_type', 'Flat', ($p_details['site_transaction_fee_type'] == 'Flat') ? true : false, array('id' => 'site_transaction_fee_type_flat', 'name' => 'site_transaction_fee_type', 'onclick' => 'disableTransactionFee(this.value)', 'class' => 'ace')) }}
                                                <label class="lbl">{{ Form::label('site_transaction_fee_type_flat', trans("admin/productAdd.flat_fee_type"), array('class' => 'disp-block'))}}</label>
                                            </label>
                                            <label class="radio-inline">
                                                {{ Form::radio('site_transaction_fee_type', 'Percentage', ($p_details['site_transaction_fee_type'] == 'Percentage') ? true : false, array('id' => 'site_transaction_fee_type_percentage', 'name' => 'site_transaction_fee_type', 'onclick' => 'disableTransactionFee(this.value)', 'class' => 'ace')) }}
                                                <label class="lbl">{{ Form::label('site_transaction_fee_type_percentage', trans("admin/productAdd.percentage_fee"), array('class' => 'disp-block'))}}</label>
                                            </label>
                                            <label class="radio-inline">
                                                {{ Form::radio('site_transaction_fee_type', 'Mix', ($p_details['site_transaction_fee_type'] == 'Mix') ? true : false, array('id' => 'site_transaction_fee_type_mix', 'name' => 'site_transaction_fee_type', 'onclick' => 'disableTransactionFee(this.value)', 'class' => 'ace')) }}
                                                <label class="lbl">{{ Form::label('site_transaction_fee_type_mix', trans("admin/productAdd.mix_fee"), array('class' => 'disp-block'))}}</label>
                                            </label>
                                        </div>
                                        <label class="error" for="site_transaction_fee_type" generated="true">{{$errors->first('site_transaction_fee_type')}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_clsFeeFields {{{ $errors->has('site_transaction_fee') ? 'error' : '' }}}">
                                    {{ Form::label('site_transaction_fee', trans("admin/productAdd.site_transaction_fee"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('site_transaction_fee', null, array('class' => 'form-control valid')); }}
                                        <label class="error">{{{ $errors->first('site_transaction_fee') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_clsFeeFields {{{ $errors->has('site_transaction_fee_percent') ? 'error' : '' }}}">
                                    {{ Form::label('site_transaction_fee_percent', trans("admin/productAdd.site_transaction_fee_percent"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('site_transaction_fee_percent', null, array('class' => 'form-control valid')); }}
                                        <label class="error">{{{ $errors->first('site_transaction_fee_percent') }}}</label>
                                    </div>
                                </div>-->
                            </div>

                            @if($d_arr['allow_variation'] && $p_details['is_downloadable_product'] == 'No')
                                <div class="form-group {{{ $errors->has('use_variation') ? 'error' : '' }}}">
                                    {{ Form::label('use_variation', trans("product.use_variation"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <label class="radio">
                                            {{Form::checkbox('use_variation', 'Yes', Input::old('use_variation', $is_free_product_yes), array('class' => '')) }}
                                        </label>
                                        <label class="error">{{{ $errors->first('use_variation') }}}</label>
                                    </div>
                                </div>
                            @endif

                            @if($d_arr['allow_giftwrap'] && $p_details['is_downloadable_product'] == 'No')
                                <div class="form-group {{{ $errors->has('use_variation') ? 'error' : '' }}}">
                                    {{ Form::label('accept_giftwrap', trans("product.accept_giftwrap"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <label class="radio">
                                            {{Form::checkbox('accept_giftwrap', 'Yes', Input::old('accept_giftwrap', $is_free_product_yes), array('class' => '')) }}
                                        </label>
                                        <label class="error">{{{ $errors->first('accept_giftwrap') }}}</label>
                                    </div>
                                </div>
                                <div class="form-group fn_giftwrap_field {{{ $errors->has('accept_giftwrap_message') ? 'error' : '' }}}">
                                    {{ Form::label('accept_giftwrap_message', trans("product.accept_giftwrap_message"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <label class="radio">
                                            {{Form::checkbox('accept_giftwrap_message', 'Yes', Input::old('accept_giftwrap_message', $is_free_product_yes), array('class' => '')) }}
                                        </label>
                                        <label class="error">{{{ $errors->first('accept_giftwrap_message') }}}</label>
                                    </div>
                                </div>
                                <div class="form-group fn_giftwrap_field {{{ $errors->has('use_variation') ? 'error' : '' }}}">
                                    {{ Form::label('giftwrap_type', trans("product.giftwrap_type"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        <label class="radio">
                                            {{  Form::select('giftwrap_type', $d_arr['giftwrap_type_arr'], null, array('class' => 'form-control')); }}
                                        </label>
                                        <label class="error">{{{ $errors->first('giftwrap_type') }}}</label>
                                    </div>
                                </div>
                                <div class="form-group fn_giftwrap_field {{{ $errors->has('use_variation') ? 'error' : '' }}}">
                                    {{ Form::label('giftwrap_pricing', trans("product.giftwrap_price"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <label class="radio">
                                            {{  Form::text('giftwrap_pricing', null, array('class' => 'form-control')); }}
                                        </label>
                                        <label class="error">{{{ $errors->first('giftwrap_pricing') }}}</label>
                                    </div>
                                </div>
                            @endif

                            <div class="form-actions fluid">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                {{ Form::hidden('product_user_id', $p_details['product_user_id'], array('id' => 'product_user_id')) }}
                                {{ Form::hidden('global_transaction_fee_used', 'Yes', array('id' => 'product_user_id')) }}
                                <div class="col-md-offset-3 col-md-8">
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- PRICE INFO END -->

            {{-- STOCKS FORM START --}}
            @if($d_arr['p'] == 'stocks')
                {{ Form::model($stock_details, [
                    'url' => $p_url,
                    'method' => 'post',
                    'id' => 'addProductStocksfrm', 'class' => 'form-horizontal'
                    ]) }}
                    <div class="portlet box blue-hoki">
                        <!-- TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gear"></i> {{ trans("product.enter_stock_details") }}
                            </div>
                        </div>
                        <!-- TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <!--<div class="form-group {{{ $errors->has('stock_country_id_china') ? 'error' : '' }}}">
                                    {{ Form::label('stock_country_id_china', 'Select country', array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-8">
                                        <?php
                                            $stock_country_id_china = isset($stock_details['stock_country_id_china']) ? $stock_details['stock_country_id_china'] : 0;
                                            $china_country_id = 38;//country id for china in currency_exchange_rate tbl
                                            $stock_country_id_pak = isset($stock_details['stock_country_id_pak']) ? $stock_details['stock_country_id_pak'] : 0;
                                            $pak_country_id = 153;//country id for china in currency_exchange_rate tbl
                                            $fresh_entry = false;
                                            if($stock_country_id_china == 0 && $stock_country_id_pak == 0)
                                                $fresh_entry = true;
                                            $stock_country_id_chinas = ($stock_country_id_china == $china_country_id || $fresh_entry)?'checked="checked"':'';
                                            $stock_country_id_pakistan = ($stock_country_id_pak == $pak_country_id)?'checked="checked"':'';
                                        ?>
                                        <div class="checkbox-list">
                                            <label class="checkbox-inline">
                                                <input name="stock_country_id_china" {{$stock_country_id_chinas}} type="checkbox" value="38" id="stock_country_id_china">
                                                {{ Form::label('stock_country_id_china', 'Display in China') }}
                                            </label>
                                            <label class="checkbox-inline">
                                                <input name="stock_country_id_pak" {{$stock_country_id_pakistan}} type="checkbox" value="153" id="stock_country_id_pak">
                                                {{ Form::label('stock_country_id_pak', 'Display in Pakistan') }}
                                            </label>
                                            <label class="error">{{{ $errors->first('stock_country_id_china') }}}</label>
                                            <label class="error">{{{ $errors->first('stock_country_id_pak') }}}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group fn_china_div {{{ $errors->has('quantity_china') ? 'error' : '' }}}" @if($stock_country_id_china == 0 && !$fresh_entry) style="display:none;" @endif>
                                    {{ Form::label('quantity_china', 'Quantities in china', array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('quantity_china', null, array('class' => 'form-control valid')); }}
                                        <label class="error">{{{ $errors->first('quantity_china') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_china_div {{{ $errors->has('serial_numbers_china') ? 'error' : '' }}}" @if($stock_country_id_china == 0 && !$fresh_entry) style="display:none;" @endif>
                                    {{ Form::label('serial_numbers_china', 'Serial numbers in china', array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-6 custom-textarea">
                                        {{  Form::textarea('serial_numbers_china', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                        <p class="text-muted mt5"><strong>Note: </strong>Enter each serial number in separated line. Serial numbers count must be equal to the entered Quantity</p>
                                        <label class="error">{{{ $errors->first('serial_numbers_china') }}}</label>
                                    </div>
                                </div>-->

                                <div class="form-group {{{ $errors->has('quantity') ? 'error' : '' }}}">
									{{ Form::label('quantity', trans("product.quantities"), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-4">
										{{  Form::text('quantity', null, array('class' => 'form-control valid')); }}
										<label class="error">{{{ $errors->first('quantity') }}}</label>
									</div>
								</div>

                                <div class="form-group {{{ $errors->has('serial_numbers') ? 'error' : '' }}}">
									{{ Form::label('serial_numbers', trans("product.serial_numbers"), array('class' => 'col-md-3 control-label')) }}
									<div class="col-md-6 custom-textarea">
										{{  Form::textarea('serial_numbers', null, array('class' => 'form-control valid', 'rows' => '7')); }}
										<p class="text-muted margin-top-5"><strong>{{ trans("common.note") }}: </strong>{{ trans("product.serial_number_notes") }}</p>
										<label class="error">{{{ $errors->first('serial_numbers') }}}</label>
									</div>
								</div>
                            </div>
                            <div class="form-actions fluid">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                <div class="col-md-offset-3 col-md-8">
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            {{-- STOCKS FORM END --}}

            <!-- META INFO STARTS -->
            @if($d_arr['p'] == 'meta')
                {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductMetafrm', 'class' => 'form-horizontal']) }}
                    <div class="portlet box blue-hoki">
                        <!-- TABLE TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-file-text"></i> {{trans('product.meta_details')}}
                            </div>
                        </div>
                        <!-- TABLE TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="form-group {{{ $errors->has('meta_title') ? 'error' : '' }}}">
                                    {{ Form::label('meta_title', trans("product.meta_title_label"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        {{ Form::text('meta_title', null, array('class' => 'form-control valid')); }}
                                        <label class="error">{{{ $errors->first('meta_title') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('meta_description') ? 'error' : '' }}}">
                                    {{ Form::label('meta_description', trans("product.meta_description_label"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-6 custom-textarea">
                                        {{  Form::textarea('meta_description', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                        <label class="error">{{{ $errors->first('meta_description') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('meta_keyword') ? 'error' : '' }}}">
                                    {{ Form::label('meta_keyword', trans("product.meta_keyword_label"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-6 custom-textarea">
                                        {{  Form::textarea('meta_keyword', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                        <label class="error">{{{ $errors->first('meta_keyword') }}}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions fluid">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                {{ Form::hidden('product_user_id', $p_details['product_user_id'], array('id' => 'product_user_id')) }}
                                <div class="col-md-offset-3 col-md-8">
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- META INFO END -->

            <!-- SHIPPING INFO STARTS -->
            @if($d_arr['p'] == 'shipping')
                <?php $price_currency = Config::get('generalConfig.site_default_currency');?>
                <div class="portlet box blue-hoki">
                    <!-- TABLE TITLE STARTS -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o"><sup class="fa fa-plane font11"></sup></i> {{ trans("product.package_details") }}
                        </div>
                    </div>
                    <!-- TABLE TITLE END -->

                    <!-- PACKAGE DETAILS STARTS -->
					<div class="portlet-body form">
						{{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductPackageDetailsfrm', 'class' => 'form-horizontal']) }}
							<div class="form-body">
								<div class="clearfix">
									<h4 class="form-section">{{ trans("product.package_details") }} <small class="text-muted">{{ trans("product.weight_info") }}</small></h4>
									<div class="form-group clearfix {{{ $errors->has('package_weight') ? 'error' : '' }}}">
										{{ Form::label('weight_val', trans("product.package_weight"), array('class' => 'col-md-3 control-label required-icon')) }}
										@if(isset($d_arr['package_details']->weight))
											@if($weight = $d_arr['package_details']->weight)@endif
										@else
											@if($weight = '1.5')@endif
										@endif
										<div class="col-md-3">
											<div class="input-group">
												{{ Form::text('weight', $weight, array('class' => 'form-control valid weight_package', 'id' => 'weight_val')) }}
												<span class="input-group-addon">{{ trans("product.kg_piece") }}</span>
											</div>
											<label for="weight_val" generated="true" class="error">{{{ $errors->first('weight') }}}</label>
										</div>
									</div>
									<?php
									$custom_package = (isset($d_arr['package_details']->custom) && $d_arr['package_details']->custom == 'Yes')?'checked="checked"':'';
									?>
									<div class="col-md-offset-3 mb20">
										<div class="checkbox-list">
											<label class="checkbox-inline">
												<input name="custom" {{ $custom_package }} type="checkbox" value="Yes" id="checkbox_class_yes" class="custom_package">
												{{ Form::label('checkbox_class_yes', trans("product.custom")) }}
											</label>
										</div>
										<div class="clearfix fn_custom note note-warning pack-details">
											<div class="clearfix">
												{{ Form::label('first_qty', trans("product.first_qty"), array('class' => '')) }}
												@if(isset($d_arr['package_details']->first_qty))
													@if($qty = $d_arr['package_details']->first_qty)@endif
												@else
													@if($qty = '')@endif
												@endif
												{{ Form::text('first_qty', $qty, array('class' => 'form-control')) }}
												{{ Form::label('qty', trans("product.qty")) }}
												<label for="first_qty" generated="true" class="error">{{{ $errors->first('first_qty') }}}</label>
											</div>

											<div class="clearfix">
												{{ Form::label('additional_qty', trans("product.additional_qty"), array('class' => '')) }}
												@if(isset($d_arr['package_details']->additional_qty))
													@if($additional_qty = $d_arr['package_details']->additional_qty)@endif
												@else
													@if($additional_qty = '')@endif
												@endif
												{{ Form::text('additional_qty', $additional_qty, array('class' => 'form-control')) }}
												{{ Form::label('qty', trans("product.qty")) }}
												{{ Form::label('additional_weight', trans("product.additional_weight"), array('class' => '')) }}
												@if(isset($d_arr['package_details']->additional_qty))
													@if($additional_weight = $d_arr['package_details']->additional_weight)@endif
												@else
													@if($additional_weight = '')@endif
												@endif
												{{ Form::text('additional_weight',$additional_weight, array('class' => 'form-control')) }}
												{{ Form::label('weight', trans("product.weight")) }}
												<label for="additional_qty" generated="true" class="error">{{{ $errors->first('additional_qty') }}}</label>
												<label for="additional_weight" generated="true" class="error">{{{ $errors->first('additional_weight') }}}</label>
											</div>
										</div>
									</div>
									<div class="form-group clearfix {{{ $errors->has('size') ? 'error' : '' }}}">
										{{ Form::label('size', trans("product.size"), array('class' => 'col-md-3 control-label required-icon')) }}
										<div class="col-md-9 after-packing">
											<ul class="clearfix list-unstyled packing-input mar0">
												@if(isset($d_arr['package_details']->length))
													@if($length = $d_arr['package_details']->length)@endif
												@else
													@if($length = '25')@endif
												@endif
												<li>
													{{ Form::text('length', $length, array('class' => 'form-control valid length weight_package', 'id' => 'l_w_h_length')) }}
													<label for="l_w_h_length" generated="true" class="error"></label>
												</li>
												<li>x</li>
												@if(isset($d_arr['package_details']->width))
													@if($width = $d_arr['package_details']->width)@endif
												@else
													@if($width = '15')@endif
												@endif
												<li>
													{{  Form::text('width', $width, array('class' => 'form-control valid width weight_package', 'id' => 'l_w_h_width')) }}
													<label for="l_w_h_width" generated="true" class="error"></label>
												</li>
												<li>x</li>
												@if(isset($d_arr['package_details']->height))
													@if($height = $d_arr['package_details']->height)@endif
												@else
													@if($height = '10')@endif
												@endif
												<li>
													{{ Form::text('height', $height, array('class' => 'form-control valid height weight_package', 'id' => 'l_w_h_height')) }}
													<label for="l_w_h_height" generated="true" class="error"></label>
												</li>
											</ul>
											<label id="length_width_height_msg" class="info text-muted selector-def">
												<small>{{ str_replace('VAR_SIZE_CAL', '3750', trans("product.length_width_height_size")) }}</small>
											</label>
											<label for="l_w_h" generated="true" class="error"></label>
											<label class="error" id="size_error_message">{{{ $errors->first('length_width_height') }}}</label>
												<label id="size_info_message">
												{{ str_replace(array('VAR_WEIGHT', 'VAR_CAL_WEIGHT'), array('1.5', '0.75'), trans("product.weight_length_width_height_size"))  }}
											</label>
										</div>
									</div>
								</div>
								<!-- PACKAGE DETAILS END -->

								<!-- SHIPPING DETAILS STARTS -->
								<h4 class="form-section">{{trans('product.shipping_details')}}</h4>
								<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_template') ? 'error' : '' }}} margin-bottom-20">
									{{ Form::label('shipping_template', 'Shipping template', array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-5">
										{{  Form::select('shipping_template', $d_arr['ship_templates_list'], Input::old('shipping_template', $d_arr['ship_template_id']), array('class' => 'form-control select2me input-large fnShippingCostEstimate margin-bottom-5', 'id' => 'shipping_template')); }}
										@if(isset($p_details['product_user_id']) && $logged_user_id == $p_details['product_user_id'])
											<a target="_blank" href="{{ URL::action('AdminShippingTemplateController@getAdd')}}" class="btn btn-xs btn-primary mt5">
											<i class="fa fa-plus"></i> {{ trans("shippingTemplates.add_shipping_template") }}</a>
										@endif
										<label class="error">{{{ $errors->first('shipping_template') }}}</label>
									</div>
								</div>

								<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_from_country') ? 'error' : '' }}}">
									{{ Form::label('shipping_from_country', trans('product.shipping_from_country'), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-5">
										{{  Form::select('shipping_from_country', array('0' => Lang::get('common.please_select')) + $d_arr['countries_list'], Input::old('shipping_from_country', $d_arr['shipping_from_country']), array('class' => 'form-control select2me input-large fnShippingCostEstimate', 'id' => 'shipping_from_country')); }}
										<label class="error">{{{ $errors->first('shipping_from_country') }}}</label>
									</div>
								</div>

								<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_from_zip_code') ? 'error' : '' }}}">
									{{ Form::label('shipping_from_zip_code', trans('product.shipping_from_zip_code'), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-5">
										{{  Form::text('shipping_from_zip_code', Input::old('shipping_from_zip_code', $d_arr['shipping_from_zip_code']), array('class' => 'form-control select2me input-large', 'id' => 'shipping_from_zip_code', 'placeholder' => trans('product.zip_code'))); }}
										<label class="error">{{{ $errors->first('shipping_from_zip_code') }}}</label>
									</div>
								</div>

								<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_country') ? 'error' : '' }}} mb40">
									{{ Form::label('shipping_country', trans('product.reference_shipping_cost'), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-5">
										{{  Form::select('shipping_country', $d_arr['countries_list'], Input::old('shipping_country', $d_arr['ship_country_id']), array('class' => 'form-control select2me input-large fnShippingCostEstimate margin-bottom-5', 'id' => 'shipping_country')); }}
										<a id="calc_cost" href="javascript:;"><i class="fa fa-refresh"></i> {{ trans("product.refresh") }}</a>
										<label class="error">{{{ $errors->first('shipping_country') }}}</label>
									</div>
								</div>
								<!-- SHIPPING DETAILS END -->

								<!-- REFERENCE SHIPPING COST DETAILS STARTS -->
								<div id="reference_shipping_cost_holder">
									@include('admin.referenceShippingCost')
								</div>
								<!-- REFERENCE SHIPPING COST DETAILS END -->
							</div>
							<div class="form-actions fluid">
								{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
								{{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
								<div class="col-md-offset-3 col-md-6">
									<button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green"><i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}</button>
								</div>
							</div>
						{{ Form::close() }}
					</div>
                 </div>
            @endif
            <!-- SHIPPING INFO END -->

            <!-- Tax info start -->
            @if($d_arr['p'] == 'tax')
                <?php $price_currency = Config::get('generalConfig.site_default_currency');?>
                <div class="portlet box blue-hoki">
                    <!-- TABLE TITLE STARTS -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-briefcase"></i> {{trans('product.tax_details')}}
                        </div>
                    </div>
                    <!-- TABLE TITLE END -->

                    <div class="portlet-body form">
                        <div class="form-body">
                            {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductTaxfrm', 'class' => 'form-horizontal']) }}
                            <div class="mb40">
                                <h4 class="form-section">{{trans('product.add_tax')}}</h4>
                                <div class="form-group fn_clsPriceFields {{{ $errors->has('country_id') ? 'error' : '' }}}">
                                    {{ Form::label('taxation_id', trans("product.tax"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        {{  Form::select('taxation_id', $d_arr['taxations_list'], Input::old('taxation_id', ''), array('class' => 'form-control select2me input-medium margin-bottom-5', 'id' => 'js-taxation-id')); }}
                                        <div><a target="_blank" href="{{ URL::action('AdminTaxationsController@getAddTaxation')}}" class="btn btn-xs btn-primary mt5"><i class="fa fa-plus"></i>  {{ Lang::get('taxation.add_taxation')  }} </a></div>
                                        <label class="error">{{{ $errors->first('taxation_id') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_clsPriceFields {{{ $errors->has('tax_fee') ? 'error' : '' }}}">
                                    {{ Form::label('tax_fee', trans("product.tax_fee"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        <div class="clearfix">
                                            <div class="col-md-4 col-sm-3 pad-left-0 mb5">
                                                <div class="input-group">
                                                    {{ Form::text('tax_fee', null, array('class' => 'form-control', 'id' => 'js-tax_fee')); }}
                                                </div>
                                            </div>
                                            <div class="col-md-6 pad0">
                                                <div class="input-group">
                                                {{ Form::select('fee_type', array('percentage' => '%', 'flat' => 'Flat (In '.$price_currency.' )'),Input::old('fee_type', ''), array('class' => 'form-control bs-select input-small', 'id' => 'js-fee_type')); }}
                                                    <span id="js-tax-currency" class="hide input-group-addon">(In {{$price_currency}} )</span>
                                                </div>
                                                <label class="error">{{{ $errors->first('fee_type') }}}</label>
                                            </div>
                                        </div>
                                        <label for="js-tax_fee" generated="true" class="error">{{{ $errors->first('tax_fee') }}}</label>
                                    </div>
                                </div>

                                <!--<div class="form-group fn_clsPriceFields {{{ $errors->has('fee_type') ? 'error' : '' }}}">
                                    {{ Form::label('fee_type', trans("product.fee_type"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        {{ Form::select('fee_type', array('percentage' => 'Percentage', 'flat' => 'Flat'),Input::old('fee_type', ''), array('class' => 'form-control bs-select input-medium', 'id' => 'js-fee_type')); }}
                                        <label class="error">{{{ $errors->first('fee_type') }}}</label>
                                    </div>
                                </div>-->

                                <div class="form-group">
                                    {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                    {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                    <div class="col-md-offset-3 col-md-6">
                                        <button name="edit_product" id="add_tax" value="add_tax" type="submit" class="btn btn-info btn-sm"><i class="fa fa-plus-circle"></i> {{ trans("product.add_tax") }}</button>
                                    </div>
                                </div>
                            </div>

                            <h4 class="form-section">{{ trans("product.tax_fee") }} List</h4>
                            <div class="table-responsive">
                                <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
                                    <thead>
                                        <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                                            <th class="col-md-3">{{ trans("product.tax") }}</th>
                                            <th class="col-md-6">{{ trans("product.tax_fee") }}</th>
                                            <th class="col-md-3">{{ trans("common.action") }}</th>
                                        </tr>
                                    </thead>

                                    <tbody class="fn_formBuilderListBody" id="taxfee_tbl_body">
                                        @if(isset($d_arr['product_taxations_list']) && $d_arr['product_taxations_list']!= '' &&	count($d_arr['product_taxations_list']) > 0)
                                            @foreach($d_arr['product_taxations_list'] as $taxation)
                                                <tr id="itemTaxRow_{{$taxation->id}}">
                                                    <td>@if(isset($taxation->taxations->tax_name)){{$taxation->taxations->tax_name}}@else {{'-'}} @endif</td>
                                                    <td>
                                                        <div id="taxfeetd_{{$taxation->id}}" class="mw-560">
                                                            <?php  $taxation->tax_fee = round($taxation->tax_fee,2); ?>
                                                            @if($taxation->fee_type == 'percentage')
                                                                <strong>{{$taxation->tax_fee}}%</strong>
                                                            @else
                                                                <span class="text-muted">{{$price_currency}}</span> <strong>{{$taxation->tax_fee}}</strong>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="status-btn">
                                                        <a onclick="javascript:removeItemTaxRow({{ $taxation->id }});" href="javascript: void(0);" title="Delete" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
                                                        <a id="tax_fee_edit_link_{{$taxation->id}}" onclick="javascript:editItemTaxRow({{ $taxation->id }}, {{$taxation->tax_fee}}, '{{$taxation->fee_type}}');" href="javascript: void(0);" title="Edit" class="btn btn-xs blue"><i class="fa fa-edit"></i></a>
                                                        <div id="fee_action_{{$taxation->id}}"></div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="4"><p class="alert alert-info">{{trans("product.no_tax_added")}}</p></td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            {{ Form::close() }}
                        </div>
                        {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductTaxListfrm', 'class' => 'form-horizontal']) }}
                            <div class="form-actions fluid">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                <div class="col-md-8">
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green"><i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}</button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            @endif
            <!-- TAX INFO END -->

            <!-- ATTRIBUTE INFO STARTS -->
            @if($d_arr['p'] == 'attribute')
                {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductAttributefrm', 'class' => 'form-horizontal']) }}
                    <div class="portlet box blue-hoki">
                        <!-- TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus-circle"></i> {{trans('product.attribute_details')}}
                            </div>
                        </div>
                        <!-- TITLE END -->

                        <div class="portlet-body form addattribute-chkradio">
                            @if(count($d_arr['attr_arr']) > 0)
                                <div class="form-body">
                                    @foreach($d_arr['attr_arr'] AS $attr)
                                        <?php
                                            $elem_name = 'attribute_' .$attr['attribute_id'];
                                            $required_class = '';
                                            $validation =  explode("|", $attr['validation_rules']);
                                            if (in_array('required', $validation)) {
                                                $required_class = 'required-icon';
                                            }
                                            $elem_value = $attr['default_value'];
                                            if(count(Input::old()) > 0){
                                                $elem_value = Input::old($elem_name);
                                            }
                                        ?>
                                        <div class="form-group fn_clsPriceFields {{{ $errors->has($elem_name) ? 'error' : '' }}}">
                                            {{ Form::label($elem_name, ucfirst($attr['attribute_label']), array('class' => 'col-md-3 control-label '.$required_class)) }}
                                            <?php
                                                $option_arr = $service_obj->getAttributeOptions($attr['attribute_id']);
                                                $input_type = $attr['attribute_question_type'];
                                            ?>
                                            <div class="@if($input_type == 'textarea') col-md-6 @else col-md-4 @endif">
                                                @if($input_type == 'text')
                                                    {{ Form::text($elem_name, $elem_value, array('class' => 'form-control valid')) }}
                                                @elseif($input_type == 'textarea')
                                                    {{ Form::textarea($elem_name, null, array('class' => 'form-control fn_editor', 'rows' => '7')) }}
                                                @elseif($input_type == 'option')
                                                    <div class="radio-list">
                                                        @foreach($option_arr AS $key => $val)
                                                            <?php
                                                                $id_name = 'attribmopt_'.strtolower(str_replace(' ', '_', $val));
                                                                $checked = '';
                                                                if (is_array($elem_value)) {
                                                                $checked = in_array($key, $elem_value) ? ' checked ':'';
                                                                }
                                                                else{
                                                                $checked = ($key == $elem_value) ? ' checked ':'';
                                                                }
                                                            ?>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="{{ $elem_name }}" id="{{ $id_name }}" value="{{ $key }}" {{ $checked }}>
                                                                {{ Form::label($id_name, $val, array('class' => 'control-label')) }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($input_type == 'check')
                                                    <div class="checkbox-list">
                                                        @foreach($option_arr as $key => $val)
                                                            <?php
                                                                if (is_array($elem_value)) {
                                                                $checked = in_array($key, $elem_value) ? ' checked="checked" ':'';
                                                                } else {
                                                                $checked = '';
                                                                }
                                                                $id_name = 'attribmopt_'.strtolower(str_replace(' ', '_', $val));
                                                            ?>
                                                            <label class="checkbox-inline">
                                                                <input type="checkbox" id="{{ $id_name }}" name="{{ $elem_name.'[]' }}" value="{{ $key }}" {{ $checked }}>
                                                                {{ Form::label($id_name, $val, array('class' => 'control-label')) }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($input_type == 'select')
                                                    <select name="{{ $elem_name }}" class="form-control input-medium select2me">
                                                        <option value="">{{ trans('common.select_option') }}</option>
                                                        @foreach($option_arr as $key => $val)
                                                            <?php
                                                                if (is_array($elem_value)) {
                                                                $selected = in_array($key, $elem_value) ? ' selected="selected" ':'';
                                                                } else {
                                                                $selected = ($key == $elem_value) ? ' selected="selected" ':'';
                                                                }
                                                            ?>
                                                            <option value="{{ $key }}" {{ $selected }}>{{ $val }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($input_type == 'multiselectlist')
                                                    <select name="{{ $elem_name.'[]' }}" multiple class="form-control select2me">
                                                        <option value="">{{ trans('common.select_option') }}</option>
                                                        @foreach($option_arr as $key => $val)
                                                            <?php
                                                                if (is_array($elem_value)) {
                                                                $selected = in_array($key, $elem_value) ? ' selected="selected" ':'';
                                                                } else {
                                                                $selected = ($key == $elem_value) ? ' selected="selected" ':'';
                                                                }
                                                            ?>
                                                            <option value="{{ $key }}" {{ $selected }}>{{ $val }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                <label class="error">{{{ $errors->first($elem_name) }}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-actions fluid">
                                    {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                    {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                    {{ Form::hidden('product_category_id', $p_details['product_category_id'], array('id' => 'product_category_id')) }}
                                    <div class="col-md-offset-3 col-md-8">
                                        <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                            <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                     </div>
                {{ Form::close() }}
            @endif
            <!-- ATTRIBUTE INFO END -->

            <!-- PREVIEW FILES INFO STARTS -->
            @if($d_arr['p'] == 'preview_files')
                {{ Form::model($d_arr['p_img_arr'], ['url' => $p_url,'method' => 'post','id' => 'addProductPreviewFilesfrm', 'class' => 'form-horizontal', 'files' => true,]) }}
                    <div class="portlet box blue-hoki">
                        <!-- TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-file-text-o"><sup class="fa fa-search font11"></sup></i> {{trans('product.preview_details')}}
                            </div>
                        </div>
                        <!-- TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">
                                        {{ trans("product.products_thumb_image_details") }} <strong>({{ Config::get('webshoppack.photos_large_width') }} X {{ Config::get('webshoppack.photos_large_height') }})</strong>
                                        <p><small class="text-muted">({{ trans("product.image_for_product_list_page") }})</small></p>
                                    </label>
                                    <div class="col-md-4">
                                        <?php
                                            $p_default_img = $service_obj->getProductDefaultThumbImage($p_id, 'default', $d_arr['p_img_arr']);
                                            $p_thumb_img = $service_obj->getProductDefaultThumbImage($p_id, 'thumb', $d_arr['p_img_arr']);
                                        ?>
                                        @if($p_thumb_img['no_image'] != 1)
                                            <img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" alt="" @if(isset($p_thumb_img["thumbnail_width"])) width='{{$p_thumb_img["thumbnail_width"]}}' height='{{$p_thumb_img["thumbnail_height"]}}' @endif >
                                        @else
                                            <img style='display:none;' id="item_thumb_image_id" src="" width='' height='' title="" alt="">
                                        @endif
                                        <p @if($p_thumb_img['no_image']) style="display:none;"@endif id="link_remove_thumb_image" class="mt10">
                                            <a onclick="javascript:removeProductThumbImage({{ $p_id }});" class="btn btn-xs red" href="javascript: void(0);"><i class="fa fa-times"></i> {{ trans("product.remove_image") }}</a>
                                        </p>
                                    </div>
                                </div>

                                <div id="item_thumb_image_title_holder" class="form-group {{{ $errors->has('thumbnail_title') ? 'error' : '' }}}" @if($p_thumb_img['no_image'] == 1) style="display:none" @endif>
                                    {{ Form::label('thumbnail_title', trans("product.thumbnail_title"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-md-9">
                                            {{ Form::text('thumbnail_title', null, array('class' => 'form-control', 'id' => 'item_thumb_image_title', 'onkeypress' => "javascript:editItemImageTitle($p_id, 'thumb');", 'onblur' => "return false; saveProductImageTitle($p_id, 'thumb', true);")); }}
                                            </div>
                                            <div class="col-md-3 custom-pad1">
                                                <span id="item_thumb_edit_span">
                                                    <a onclick="javascript:editItemImageTitle({{ $p_id }}, 'thumb');" href="javascript: void(0);" class="btn blue btn-xs mt5"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
                                                </span>
                                                <span style="display:none;" id="item_thumb_image_save_span">
                                                    <a onclick="javascript:saveProductImageTitle({{ $p_id }}, 'thumb', false);" href="javascript: void(0);" class="btn btn-xs green mt5"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a>
                                                </span>
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('thumbnail_title') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('upload_thumb') ? 'error' : '' }}}">
                                    {{-- Form::label('upload_thumb', trans("product.upload_thumb"), array('class' => 'col-md-3 control-label')) --}}
                                    <div class="col-md-3">&nbsp;</div>
                                    <div class="col-md-4">
                                        <div class="btn purple-plum" id="upload_thumb">
                                        	<i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_thumb_image") }}
                                        </div>
                                        <div class="mt3">
                                            <i class="fa fa-question-circle pull-left mt3"></i>
                                            <div class="pull-left text-muted">
                                                {{ str_replace("\n",'<br />',sprintf(trans("product.products_thumb_allowed_image_formats_size"), implode(', ', Config::get("webshoppack.thumb_format_arr")) , Config::get("webshoppack.thumb_max_size"), trans("product.file_size_in_MB"))) }}
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('upload_thumb') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">
                                        {{ trans("product.products_default_image_details") }}  <strong>({{ Config::get('webshoppack.photos_large_width') }} X {{ Config::get('webshoppack.photos_large_height') }})</strong>
                                        <p><small class="text-muted">({{ trans("product.image_for_product_view_page") }})</small></p>
                                    </label>
                                    <div class="col-md-8">
                                        @if($p_default_img['no_image'] != 1)
                                            <img id="item_default_image_id" src="{{$p_default_img['image_url']}}" alt="" @if(isset($p_default_img["default_width"])) width='{{$p_default_img["default_width"]}}' height='{{$p_default_img["default_height"]}}' @endif >
                                        @else
                                            <img style="display:none;" id="item_default_image_id" src="" width='' height='' title="" alt="">
                                        @endif
                                        <p @if($p_default_img['no_image']) style="display:none;"@endif id="link_remove_default_image" class="mt10">
                                            <a onclick="javascript:removeItemDefaultImage({{ $p_id }});" href="javascript: void(0);" class="btn btn-xs red"><i class="fa fa-times"></i> {{ trans("product.remove_image") }}</a>
                                        </p>
                                    </div>
                                </div>

                                <div id="item_default_image_title_holder" class="form-group {{{ $errors->has('default_title') ? 'error' : '' }}}" @if($p_default_img['no_image'] == 1) style="display:none" @endif>
                                    {{ Form::label('default_title', trans("product.default_title"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-md-9">
                                                {{ Form::text('default_title', null, array('class' => 'form-control',  'id' => 'item_default_image_title', 'onkeypress' => "javascript:editItemImageTitle($p_id, 'default');", 'onblur' => "return false; saveProductImageTitle($p_id, 'default', true);")); }}
                                            </div>
                                            <div class="col-md-3 custom-pad1">
                                                <span id="item_default_edit_span">
                                                    <a onclick="javascript:editItemImageTitle({{ $p_id }}, 'default');" href="javascript: void(0);" class="btn blue btn-xs mt5"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
                                                </span>
                                                <span  style="display:none;" id="item_default_image_save_span">
                                                    <a onclick="javascript:saveProductImageTitle({{ $p_id }}, 'default', false);" href="javascript: void(0);" class="btn green btn-xs"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a>
                                                </span>
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('default_title') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('upload_default') ? 'error' : '' }}}">
                                    {{-- Form::label('upload_default', trans("product.upload_default"), array('class' => 'col-md-3 control-label')) --}}
                                    <div class="col-md-3">&nbsp;</div>
                                    <div class="col-md-4">
                                        <div class="btn purple-plum" id="upload_default"> <i class="fa fa-cloud-upload"></i> {{ trans("product.products_upload_default_image") }}</div>
                                        <div class="mt3">
                                            <i class="fa fa-question-circle pull-left mt3"></i>
                                            <div class="pull-left text-muted">
                                                {{ str_replace("\n",'<br />',sprintf(trans("product.products_default_allowed_image_formats_size"), implode(', ', Config::get("webshoppack.default_format_arr")) , Config::get("webshoppack.default_max_size"), trans("product.file_size_in_MB"))) }}
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('upload_default') }}}</label>
                                    </div>
                                </div>

                                <h4 class="form-section">{{ trans("product.products_uploaded_files") }}</h4>
                                <div class="note note-info">
                                    {{ str_replace("\n",'<br />',sprintf(trans("product.products_preview_allowed_file_formats_size"), implode(', ', Config::get("webshoppack.preview_format_arr")) , Config::get("webshoppack.preview_max_size"), trans("product.file_size_in_MB"), Config::get("webshoppack.preview_max"))) }}
                                </div>
                                <div class="table-responsive">
                                    <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                                                <th width="150">{{ trans("product.file_title") }}</th>
                                                <th>{{ trans("product.title") }}</th>
                                                <th>{{ trans("common.action") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fn_formBuilderListBody" id="preview_tbl_body">
                                            <?php
                                                $resources_arr = $d_arr['resources_arr'];
                                            ?>
                                            @foreach($resources_arr AS $inc => $value)
                                                <tr id="itemResourceRow_{{ $resources_arr[$inc]['resource_id'] }}" class="formBuilderRow">
                                                    <td>
                                                        @if($resources_arr[$inc]['resource_type'] == 'Image')
                                                            <a href="#"> <img src="{{ URL::asset(Config::get("webshoppack.photos_folder")) .'/'. $resources_arr[$inc]['filename_thumb'] }}" alt="{{ $resources_arr[$inc]['title'] }}" style="max-width:45px; max-height:45px;" /></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input class="form-control" type="text" name="item_resource_image_{{ $resources_arr[$inc]['resource_id'] }}" id="resource_title_field_{{ $resources_arr[$inc]['resource_id'] }}" value="{{ $resources_arr[$inc]['title'] }}" onkeypress="javascript:editItemResourceTitle({{ $resources_arr[$inc]['resource_id']  }});"  />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mt5">
                                                                    <p style="display:none;" id="resource_title_text_{{ $resources_arr[$inc]['resource_id'] }}">
                                                                    {{ $resources_arr[$inc]['title'] }}</p>
                                                                    <span id="item_resource_edit_span_{{ $resources_arr[$inc]['resource_id'] }}">
                                                                        <a onclick="javascript:editItemResourceTitle({{ $resources_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs blue"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
                                                                    </span>
                                                                    <span  style="display:none;" id="item_resource_save_span_{{ $resources_arr[$inc]['resource_id'] }}">
                                                                        <a onclick="javascript:saveItemResourceTitle({{ $resources_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs green"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="status-btn">
                                                        <a onclick="javascript:removeItemResourceRow({{ $resources_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs red" title="{{ trans('common.delete') }}"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="clearfix">
                                    <div class="{{{ $errors->has('upload_default') ? 'error' : '' }}}">
                                        <!-- UPLOAD BUTTON, USE ANY ID YOU WISH -->
                                        <div class="btn purple-plum mt10 fn_ItemUploadResourceFileButton" id="upload">
                                            <i class="fa fa-cloud-upload"></i> {{ trans("product.products_upload_item_file") }}
                                        </div>
                                        <span id="status"></span>
                                        <ul id="files"></ul>
                                    </div>
                                </div>

                            @if(isset($d_arr['allow_swap_image']) && $d_arr['allow_swap_image'] > 0)
                                 <!-- Swap image start -->
                                <h4 class="form-section">{{ trans("product.swap_images") }}:</h4>
                                <div class="note note-info">
                                    {{ str_replace("\n",'<br />',sprintf(trans("product.products_preview_allowed_file_formats_size"), implode(',', Config::get("webshoppack.preview_format_arr")) , Config::get("webshoppack.preview_max_size"), trans("product.file_size_in_MB"), Config::get("webshoppack.preview_max"))) }}
                                </div>

                                <div class="table-responsive margin-bottom-20">
                                    <table summary="" id="swapresourcednd" class="fn_ItemSwapImageListTable table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr class="nodrag nodrop" id="ItemSwapImageTblHeader">
                                                <th width="150">{{ trans("product.file_title") }}</th>
                                                <th>{{ trans("product.title") }}</th>
                                                <th>{{ trans("common.action") }}</th>
                                            </tr>
                                        </thead>

                                        <tbody class="fn_ItemSwapImageListBody preview-image" id="swap_image_tbl_body">
                                            <?php
                                                $swap_imges_arr = $d_arr['swap_imges_arr'];
                                            ?>
                                            @foreach($swap_imges_arr AS $inc => $value)
                                                <tr id="itemSwapImageRow_{{ $swap_imges_arr[$inc]['resource_id'] }}" class="formBuilderRow">
                                                    <td>
                                                        <a href="javascript:void(0)"> <img src="{{ URL::asset(Config::get("variations::variations.swap_img_folder")) .'/'. $swap_imges_arr[$inc]['filename_thumb'] }}" style="max-width:45px; max-height:45px;" alt="{{ $swap_imges_arr[$inc]['title'] }}"/></a>
                                                    </td>

                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input class="form-control" type="text" name="item_swap_image_{{ $swap_imges_arr[$inc]['resource_id'] }}" id="swap_image_title_field_{{ $swap_imges_arr[$inc]['resource_id'] }}" value="{{ $swap_imges_arr[$inc]['title'] }}" onkeypress="javascript:editItemSwapImageTitle({{ $swap_imges_arr[$inc]['resource_id']  }});"  />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mt5">
                                                                    <p style="display:none;" id="swap_image_title_text_{{ $swap_imges_arr[$inc]['resource_id'] }}">
                                                                    {{ $swap_imges_arr[$inc]['title'] }}</p>
                                                                    <span id="item_swap_image_edit_span_{{ $swap_imges_arr[$inc]['resource_id'] }}">
                                                                        <a onclick="javascript:editItemSwapImageTitle({{ $swap_imges_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs blue"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
                                                                    </span>
                                                                    <span  style="display:none;" id="item_swap_image_save_span_{{ $swap_imges_arr[$inc]['resource_id'] }}">
                                                                        <a onclick="javascript:saveItemSwapImageTitle({{ $swap_imges_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs green"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="status-btn">
                                                        <a onclick="javascript:removeSwapImageRow({{ $swap_imges_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs red" title="{{ trans('common.delete') }}"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="clearfix">
                                    <div class="pull-left {{{ $errors->has('upload_swap') ? 'error' : '' }}}">
                                        <!-- UPLOAD BUTTON, USE ANY ID YOU WISH-->
                                        <div class="btn purple-plum fn_ItemUploadSwapImageFileButton" id="upload_swap_image">
                                            <i class="fa fa-cloud-upload"></i> {{ trans("product.upload_swap_image") }}
                                        </div>
                                        <span id="swap_image_status"></span>
                                        <ul id="swap_image_files"></ul>
                                    </div>
                                </div>
                                <!-- Swap image end -->
                            @endif
                            </div>
                            <div class="form-actions">
                                <div class="text-right">
                                    {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                    {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-arrow-right"></i> {{ trans("product.products_proceed_next") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- PREVIEW FILES INFO END -->

            <!-- VARIATIONS Tab start -->
            @if($d_arr['allow_variation'] && $d_arr['p'] == 'variations')
                <div id="var_grp_block" @if($d_arr['show_matrix_block']) style="display:none;"@endif>
                    @include('variations::admin.manageItemsVariationsBlock')
                </div>

                <ul class="list-unstyled note note-info">
                    <li class="fonts18 mb10">{{ Lang::get('variations::variations.default_item_price_lbl') }}:
                         {{CUtil::convertAmountToCurrency($d_arr['price_details'][0]['discount'], Config::get('generalConfig.site_default_currency'), '', true)}}
                    </li>
                    @if(isset($p_details['giftwrap_pricing'])  && isset($p_details['accept_giftwrap']) && $p_details['accept_giftwrap'] > 0)
                        <li class="fonts18">{{ Lang::get('variations::variations.default_giftwrap_price_lbl') }}:
                            {{CUtil::convertAmountToCurrency($p_details['giftwrap_pricing'], Config::get('generalConfig.site_default_currency'), '', true)}}
                        </li>
                    @endif
                </ul>

                <div id="var_matrix_block">
                    @include('variations::admin.manageItemsAttribMatrix')
                </div>
                <div class="form-group">
                    {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductVariationfrm', 'class' => 'form-horizontal']) }}
                        {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                        {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                        <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green pull-right">
                            {{ trans("product.products_proceed_next") }} <i class="fa fa-chevron-right"></i>
                        </button>
                    {{ Form::close() }}
                </div>
            @endif
            <!-- VARIATIONS Tab end -->

            <!-- DOWNLOAD FILES INFO STARTS -->
            @if($d_arr['p'] == 'download_files')
                <?php
                    $resources_arr = $d_arr['resources_arr'];
                ?>
                {{ Form::open(array('id'=>'addProductDownloadFilesfrm', 'method'=>'post','class' => 'form-horizontal', 'url' => $p_url )) }}
                    <div class="portlet box blue-hoki">
                        <!-- TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cloud-download"></i> {{trans('product.download_files')}}
                            </div>
                        </div>
                        <!-- TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="note note-info clearfix">
                                    @if(Config::get('webshoppack.download_files_is_mandatory'))<span class="text-danger pull-left">*</span> @endif
                                    <div class="pull-left">{{ str_replace("\n",'<br />',sprintf(trans("product.products_download_file_upload_description"), implode(',', Config::get("webshoppack.download_format_arr")) , Config::get("webshoppack.download_max_size"), trans("product.file_size_in_MB"))) }}</div>
                                </div>

                                <div class="form-group {{{ $errors->has('upload_file') ? 'error' : '' }}}">
                                    <div class="ml15 pull-left">
                                        <div class="btn purple-plum clsItemUploadResourceFileButton" id="upload" @if(count($d_arr['resources_arr']) > 0) style="display:none" @endif>
                                        <i class="fa fa-cloud-upload"></i> {{ trans("product.products_upload_item_file") }}</div>
                                    </div>
                                    <label class="error">{{{ $errors->first('upload_file') }}}</label>
                                </div>
                                <span id="status"></span>
                                <ul id="files"></ul>

                                <div class="table-responsive">
                                    <table summary="" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                                                <th>{{ trans("product.file_title") }}</th>
                                                <th>{{ trans("product.title") }}</th>
                                                <th>{{ trans("common.action") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="formBuilderListBody">
                                            @foreach($resources_arr AS $inc => $value)
                                                <?php
                                                    if ($resources_arr[$inc]['is_downloadable'] == 'Yes')
                                                    {
                                                        $download_filename = preg_replace('/[^0-9a-z\.\_\-]/i','', $resources_arr[$inc]['title']);
                                                        if (empty($download_filename))
                                                        {
                                                            $download_filename = md5($p_id);
                                                        }
                                                        $download_url = URL::action('AdminProductAddController@getProductActions'). '?action=download_file&product_id=' . $p_id;
                                                    }
                                                ?>
                                                <tr id="itemResourceRow_{{ $resources_arr[$inc]['resource_id'] }}" class="formBuilderRow">
                                                    <td>
                                                        <a href="{{ $download_url }}" class="light-link">{{ $download_filename }}</a>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input class="form-control" type="text" name="item_resource_image_{{ $resources_arr[$inc]['resource_id'] }}" id="resource_title_field_{{ $resources_arr[$inc]['resource_id'] }}" value="{{ $resources_arr[$inc]['title'] }}"  onkeypress="javascript:editItemResourceTitle({{ $resources_arr[$inc]['resource_id'] }});" />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mt5">
                                                                    <span id="item_resource_edit_span_{{ $resources_arr[$inc]['resource_id'] }}">
                                                                        <a onclick="javascript:editItemResourceTitle({{ $resources_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs blue"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
                                                                    </span>
                                                                    <span  style="display:none;" id="item_resource_save_span_{{ $resources_arr[$inc]['resource_id'] }}">
                                                                        <a onclick="javascript:saveItemResourceTitle({{ $resources_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs green"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a onclick="javascript:removeDownloadItemResourceRow({{ $resources_arr[$inc]['resource_id'] }});" href="javascript: void(0);" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="text-right">
                                    {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                    {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                    <i class="fa fa-arrow-right"></i> {{ trans("product.products_proceed_next") }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- DOWNLOAD FILES INFO END -->

            <!-- CANCELLATION POLICY STARTS -->
            @if($d_arr['p'] == 'cancellation_policy')
                {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductCancellationPolicyfrm', 'class' => 'form-horizontal', 'files' => true]) }}
                    <div class="portlet box blue-hoki">
                        <!-- TABLE TITLE STARTS -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-ban"></i> {{trans('product.cancellation_policy')}}
                            </div>
                        </div>
                        <!-- TABLE TITLE END -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="form-group {{{ $errors->has('use_cancellation_policy') ? 'error' : '' }}}">
                                    {{ Form::label('use_cancellation_policy', trans("product.use_cancellation"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        <?php
                                            $is_use_cancellation_yes = true;
                                            $is_use_cancellation_no = false;
                                            if(Input::old('use_cancellation_policy'))
                                            {
                                                $is_use_cancellation_yes = (Input::old('use_cancellation_policy') == "Yes")?true:false;
                                                $is_use_cancellation_no = (Input::old('use_cancellation_policy') == "No")?true:false;
                                            }
                                            elseif(isset($p_details['use_cancellation_policy']))
                                            {
                                                $is_use_cancellation_yes = ($p_details['use_cancellation_policy'] == "Yes")?true:false;
                                                $is_use_cancellation_no = ($p_details['use_cancellation_policy'] == "No")?true:false;
                                            }
                                        ?>
                                        <div class="radio-list">
                                            <label class="radio-inline">
                                                {{Form::radio('use_cancellation_policy','Yes', Input::old('use_cancellation_policy',$is_use_cancellation_yes), array('class' => '')) }}
                                                <label>{{trans('common.yes')}}</label>
                                            </label>
                                            <label class="radio-inline">
                                                {{Form::radio('use_cancellation_policy','No', Input::old('use_cancellation_policy',$is_use_cancellation_no) , array('class' => '')) }}
                                                <label>{{ trans('common.no') }}</label>
                                            </label>
                                        </div>
                                        <label class="error">{{{ $errors->first('use_cancellation_policy') }}}</label>
                                    </div>
                                </div>

                                <?php
                                    $use_default_cancellation_yes = false;
                                    $use_default_cancellation_no = true;
                                ?>
                                @if($d_arr['default_cancel_available'])
                                    <div class="form-group js_clsCancellationFields js_defaultCancellationField {{{ $errors->has('use_default_cancellation') ? 'error' : '' }}}" @if($is_use_cancellation_no) style="display:none" @endif>
                                        {{ Form::label('use_default_cancellation', trans("product.use_currenct_default_cancellation_policy"), array('class' => 'col-md-3 control-label required-icon')) }}
                                        <div class="col-md-4">
                                            <?php
                                                if(Input::old('use_default_cancellation'))
                                                {
                                                    $use_default_cancellation_yes = (Input::old('use_default_cancellation') == "Yes")?true:false;
                                                    $use_default_cancellation_no = (Input::old('use_default_cancellation') == "No")?true:false;
                                                }
                                                elseif(isset($p_details['use_default_cancellation']))
                                                {
                                                    $use_default_cancellation_yes = ($p_details['use_default_cancellation'] == "Yes")?true:false;
                                                    $use_default_cancellation_no = ($p_details['use_default_cancellation'] == "No")?true:false;
                                                }
                                                //echo "<br>use_default_cancellation_yes: ".$use_default_cancellation_yes;
                                                //echo "<br>use_default_cancellation_no: ".$use_default_cancellation_no;
                                            ?>
                                            <div class="radio-list">
                                                <label class="radio-inline">
                                                    {{Form::radio('use_default_cancellation','Yes', Input::old('use_default_cancellation',$use_default_cancellation_yes), array('class' => 'radio')) }}
                                                    <label>{{ trans('common.yes') }}</label>
                                                </label>
                                                <label class="radio-inline">
                                                    {{Form::radio('use_default_cancellation','No', Input::old('use_default_cancellation',$use_default_cancellation_no) , array('class' => 'radio')) }}
                                                    <label>{{ trans('common.no') }}</label>
                                                </label>
                                            </div>
                                            <label class="error">{{{ $errors->first('use_cancellation_policy') }}}</label>
                                        </div>
                                    </div>
                                    <div class="form-group js_clsCancellationFields js_defaultCancellationViewField" @if($is_use_cancellation_no || $use_default_cancellation_no) style="display: none;" @endif>
                                        @if(count($cancellation_policy_value) > 0 && $cancellation_policy_value['cancellation_policy_filename'] != '')
                                            {{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
                                            <div class="col-md-4">
                                                <div id="uploadedFilesList" class="clearfix mt10">
                                                    <div id="shopCancellationPolicyRow_{{ $cancellation_policy_value['id'] }}">
                                                        <?php $filePath = $cancellation_policy_value['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
                                                                $filename = $cancellation_policy_value['cancellation_policy_filename'].'.'.$cancellation_policy_value['cancellation_policy_filetype'];?>
                                                        <a target="_blank" href="{{$filePath.'/'.$filename}}"><i class="fa fa-share"></i> {{ trans('product.click_to_view_cancellation') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(count($cancellation_policy_value) > 0 && $cancellation_policy_value['cancellation_policy_text'] == '')
                                            {{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
                                            <div class="col-md-4">
                                                <div id="uploadedFilesList" class="clearfix mt10">
                                                    <div id="shopCancellationPolicyRow_{{ $cancellation_policy_value['id'] }}">
                                                        <?php $filePath = $cancellation_policy_value['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
                                                                $filename = $cancellation_policy_value['cancellation_policy_filename'].'.'.$cancellation_policy_value['cancellation_policy_filetype'];?>
                                                        <a target="_blank" href="{{URL::to('admin/shop/edit/'.$p_details['product_user_id'])}}"><i class="fa fa-share"></i>{{ trans('product.click_to_add_cancellation') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
                                            <div class="col-md-4">
                                                <div id="uploadedFilesList" class="clearfix mt10">
                                                    <div id="shopCancellationPolicyRow_{{ $cancellation_policy_value['id'] }}">
                                                        <?php $cancellation_policy_text_value = nl2br($cancellation_policy_value['cancellation_policy_text']); ?>
                                                        <a id="{{ htmlspecialchars($cancellation_policy_text_value) }}" onclick="CancellationPolicy(this);"><i class="fa fa-share"></i> {{ trans('product.click_to_view_cancellation') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    {{Form::hidden('use_default_cancellation','No')}}
                                @endif

                                <?php
									$disp_fields = true;
									//echo "<pre>";print_r($p_details);echo "</pre>";
                                ?>
                                @if((isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename'] != ''))
                                    <?php $disp_fields = false;?>
                                @endif
                                @if($disp_fields)
                                    <div class="form-group js_clsCancellationFields " @if($is_use_cancellation_no || $use_default_cancellation_yes || !$disp_fields) style="display: none;" @endif>
                                    {{ Form::label('shop_cancellation_policy_file', trans("product.cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3 col-sm-3 col-xs-12')) }}
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            {{ Form::file('shop_cancellation_policy_file', array('title' => trans("product.cancellation_policy_file"), 'class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
                                            <label class="error">{{ $errors->first('shop_cancellation_policy_file') }}</label>

                                            <div class="mt3">
                                                <i class="fa fa-question-circle pull-left"></i>
                                                <div class="pull-left text-muted">
                                                	<small>
                                                        <span class="disp-block">{{ str_replace("VAR_FILE_FORMAT",  Config::get('webshoppack.shop_cancellation_policy_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}</span>
                                                        {{ str_replace("VAR_FILE_MAX_SIZE",  (Config::get('webshoppack.shop_cancellation_policy_allowed_file_size')/1024).' MB', trans('shop.uploader_allowed_upload_limit')) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group js_clsCancellationFields" @if($is_use_cancellation_no || $use_default_cancellation_yes || !$disp_fields) style="display: none;" @endif>
                                        <div class="col-md-offset-1 col-md-7"><center>( {{ trans('common.or') }} )</center></div>
                                    </div>

                                    <?php $text_attr_arr = array();?>
                                    @if(isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename'] != '')
                                        <?php $text_attr_arr = array('disabled' => 'disabled');?>
                                    @endif
                                    <div class="form-group js_clsCancellationFields" @if($is_use_cancellation_no || $use_default_cancellation_yes || !$disp_fields) style="display: none;" @endif>
                                    {{ Form::label('cancellation_policy_text', trans("product.cancellation_policy_text"), array('class' => 'control-label required-icon col-md-3')) }}
                                        <div class="col-md-6">
                                            {{  Form::textarea('cancellation_policy_text', Input::old('cancellation_policy_text'), array('class' => 'form-control')+$text_attr_arr) }}
                                            <label class="error">{{ $errors->first('cancellation_policy_text') }}</label>
                                        </div>
                                    </div>
                                @else
                                    @if(count($p_details) > 0 && isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename'] != '')
                                        <div class="form-group js_clsCancellationFields ">
                                            {{ Form::label('shop_cancellation_policy_file', trans("product.cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
                                            <div class="col-md-4">
                                                <div id="uploadedFilesList" class="clearfix mt10">
                                                    <div id="shopCancellationPolicyRow_{{ $p_details['id'] }}">
                                                        <?php $filePath = $p_details['cancellation_policy_server_url'];
                                                              $filename = $p_details['cancellation_policy_filename'].'.'.$p_details['cancellation_policy_filetype'];?>
                                                        <p class="mb5"><a target="_blank" href="{{$filePath.'/'.$filename}}"><i class="fa fa-share"></i> Click here to view cancellation policy</a></p>
                                                        <a title="{{trans('common.delete')}}" href="javascript: void(0);" onclick="javascript:removeShopCancellationPolicy({{ $p_details['id'] }});" class="btn btn-xs red" title="Remove"><i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </div>
                                                <label class="error">{{ $errors->first('shop_cancellation_policy_file') }}</label>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <!--- ADD MEMBER BASIC DETAILS END --->

                            <!--- ACTIONS STARTS --->
                            <div class="form-actions fluid">
                                <div class="col-md-offset-3 col-md-5">
                                    {{ Form::hidden('disp_fields', $disp_fields, array('id' => 'disp_fields')) }}
                                    {{ Form::hidden('default_cancel_available', $d_arr['default_cancel_available'], array('id' => 'default_cancel_available')) }}
                                    {{ Form::hidden('used_default', $d_arr['used_default'], array('id' => 'used_default')) }}
                                    {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                    {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                    {{ Form::hidden('product_user_id', $p_details['product_user_id'], array('id' => 'product_user_id')) }}
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                    </button>
                                </div>
                            </div>
                            <!--- ACTIONS END --->
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- CANCELLATION POLICY END -->

            <!-- APPROVAL STATUS FORM STARTS -->
            @if($d_arr['p'] == 'status')
                <?php
                    //$p_details['product_status'] = $product_status;
                    $logged_user_id = BasicCUtil::getLoggedUserId();
                ?>
                {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductStatusfrm', 'class' => 'form-horizontal']) }}
                    <div class="portlet box blue-hoki">
                        <!-- title starts -->
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-check-square"></i> {{trans('product.approval_status_tab')}}
                            </div>
                        </div>
                        <!-- title end -->

                        <div class="portlet-body form">
                            <div class="form-body">
                                <!--<div class="form-group {{{ $errors->has('delivery_days') ? 'error' : '' }}}">
                                    {{ Form::label('delivery_days', trans("product.delivery_days"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        {{  Form::text('delivery_days', null, array('class' => 'form-control valid')); }}
                                        <div class="text-muted mb5">{{ trans("product.delivery_days_help") }}</div>
                                        <label class="error">{{{ $errors->first('delivery_days') }}}</label>
                                    </div>
                                </div>-->
                                {{Form::hidden('delivery_days','0')}}

                                <div class="form-group {{{ $errors->has('product_status') ? 'error' : '' }}}">
                                    {{ Form::label('product_status', trans("admin/productAdd.product_status"), array('class' => 'col-md-3 control-label required-icon')) }}
                                    <div class="col-md-4">
                                        {{ Form::select('product_status', $d_arr['status_arr'], $p_details['product_status'], array('class' => 'form-control bs-select input-medium valid')) }}
                                        <label class="error">{{{ $errors->first('product_status') }}}</label>
                                    </div>
                                </div>

                                @if($p_details['product_user_id'] != $logged_user_id)
                                    <div class="form-group {{{ $errors->has('product_notes') ? 'error' : '' }}}">
                                        {{ Form::label('product_notes', trans("admin/productAdd.notes_to_user"), array('class' => 'col-md-3 control-label')) }}
                                        <div class="col-md-6">
                                            {{  Form::textarea('product_notes', Input::get('product_notes', ''), array('class' => 'form-control valid', 'rows' => '7')); }}
                                            <label class="error">{{{ $errors->first('product_notes') }}}</label>

                                            @if(count($d_arr['product_notes']) > 0)
                                                <a href="javascript:void(0);" onclick="javascript:return showHideNotesBlock();" id="showNotes" class="btn blue-stripe default btn-xs">
                                                    {{ trans("product.products_show_product_notes") }} <i class="fa fa-chevron-down"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div id="sel_NotesBlock" class="clearfix" style="display:none;">
                                    <div class="col-md-offset-3 col-md-8">
                                        @foreach($d_arr['product_notes'] AS $notes)
                                            <?php
                                                $lang = trans("product.user_notes_title");
                                                if($notes->added_by == 'Staff')
                                                {
                                                    $lang = trans("product.staff_notes_title");
                                                }
                                                elseif($notes->added_by == 'Admin')
                                                {
                                                    $lang = trans("product.admin_notes_title");
                                                }
                                                $title = str_replace('VAR_DATE', CUtil::FMTDate($notes->date_added, "Y-m-d H:i:s", ""), $lang);
                                            ?>
                                            <p><strong>{{ $title }}</strong></p>
                                            <p>{{ $notes->notes }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                {{ Form::hidden('product_user_id', $p_details['product_user_id'], array('id' => 'product_user_id')) }}
                                {{ Form::hidden('date_activated', $p_details['date_activated'], array('id' => 'date_activated')) }}
                                {{ Form::hidden('date_expires', $p_details['date_expires'], array('id' => 'date_expires')) }}
                                <div class="col-md-offset-3 col-md-8">
                                    <button name="edit_product" id="edit_product" value="set_to_draft" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("admin/productAdd.save_product") }}
                                    </button>
                                    <a href="{{ $d_arr['product_view_url'] }}" target="_blank" class="btn btn-info">
                                    <i class="fa fa-eye"></i> {{ trans("admin/productAdd.view_product") }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            @endif
            <!-- APPROVAL STATUS FORM END -->
        </div>
        <!-- PRODUCT ADD STEPS END -->

        <small id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></small>
        <div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
            <span class="ui-icon ui-icon-alert"></span>
            <span id="dialog_msg" class="show ml15">{{  trans('product.uploader_confirm_delete') }}</span>
        </div>

        <div id="dialog-cancellation-policy-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
            <span class="ui-icon ui-icon-alert"></span>
            <span id="dialog_msg" class="show ml15">{{  trans('product.cancellation_policy_delete_confirm') }}</span>
        </div>

        <div id="dialog-upload-errors" class="confirm-dialog-delete" title="" style="display:none;">
            <span class="ui-icon ui-icon-alert"></span>
            <span id="dialog-upload-errors-span" class="show ml15"></span>
        </div>
		<div id="dialog-confirm" title="" style="display:none;">
		    <span class="ui-icon ui-icon-alert"></span>
			<span id="dialog-confirm-content" class="show ml15"></span>
		</div>
	</div>
    <script language="javascript" type="text/javascript">
		var page_name = 'admin_add_product';
		var pagetab = "{{ $d_arr['p'] }}";
		@if(isset($d_arr['allow_giftwrap']) && $d_arr['allow_giftwrap'])
			var allow_giftwrap =  1;
		@else
			var allow_giftwrap =  0;
		@endif
	</script>
@stop

@section('script_content')
    @if($d_arr['p'] == 'basic')
        <script src="{{ URL::asset('/js/lib/jquery.inputlimiter.js') }}"></script>
    @endif

    @if($d_arr['p'] == 'preview_files')
        <script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.tablednd_0_5.js') }}"></script>
    @endif

    @if($d_arr['p'] == 'preview_files' || $d_arr['p'] == 'download_files')
        <script src="{{ URL::asset('/js/uploadDocument.js') }}"></script>
        <script src="{{ URL::asset('/js/ajaxupload.3.5.min.js') }}"></script>
    @endif

    <script language="javascript" type="text/javascript">
        var mes_required = "{{trans('common.required')}}";
        var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
        var product_actions_url = "{{ URL::action('AdminProductAddController@postProductActions')}}";
		var remove_swap_msg = '{{ Lang::get("variations::variations.are_you_sure_want_to_delete_this_swapimg") }}';
		@if($d_arr['p'] == 'variations')
			var matrix_select_action = '{{ Lang::get("variations::variations.matrix_select_action") }}';
			var matrix_edit_head = '{{ Lang::get("variations::variations.matrix_edit_head") }}';
			var matrix_edit_content = '{{ Lang::get("variations::variations.matrix_edit_content") }}';
			var selection_none_err = '{{ Lang::get("variations::variations.selection_none_err") }}';

		@endif
        var product_id = '{{ $p_id }}';
        var root_category_id = 1;
        var action = "{{ $action }}";

		var title_min_length = "{{Config::get('webshoppack.title_min_length')}}";
		var title_max_length = "{{Config::get('webshoppack.title_max_length')}}";
		var	summary_max_length = "{{Config::get('webshoppack.summary_max_length')}}";
		var product_add_section_url = "{{ URL::action('ProductAddController@postAddSectionName') }}";
		var category_list_url = "{{ URL::action('ProductAddController@getProductSubCategories')}}";
		var discounted_price_invalid = '{{ e(trans("product.discounted_price_invalid")) }}';
		var price_not_less_than_one = '{{trans("product.price_not_less_than_one")}}';
		var enter_valid_price = '{{trans("product.enter_valid_price")}}';
		var valid_discount_percent = "{{trans('product.valid_discount_percent')}}";
		var discount_not_less_than_one = '{{trans('product.discount_not_less_than_one')}}';
		var can_upload_free_product = 'Config::get("webshoppack.can_upload_free_product")';
		var total_amount_to_pay_txt = '';
		var is_featuredproducts_allowed = 0;
		@if(CUtil::chkIsAllowedModule('featuredproducts'))
			is_featuredproducts_allowed = 1;
			total_amount_to_pay_txt = "{{ trans('featuredproducts::featuredproducts.total_amount_to_pay') }}";
		@endif
		@if(isset($d_arr['allow_giftwrap']) && $d_arr['allow_giftwrap'])
			var allow_giftwrap = 1;
		@else
			var allow_giftwrap = 0;
		@endif

		var valid_discount = '{{trans('product.valid_discount')}}';
		var valid_discounted_price = '{{trans('product.valid_discounted_price')}}';
		@if($d_arr['p'] == 'price')
			var site_transaction_fee_type = '{{ $p_details['site_transaction_fee_type'] }}';
		@endif

		var common_no = "{{ trans('common.no') }}";
		var common_yes = "{{ trans('common.yes') }}";
		var common_save = '{{trans('common.save')}}';
		var common_cancel = '{{trans('common.cancel')}}';
		var common_ok = "{{ trans('common.ok') }}";
        function showErrorDialog(err_data) {
            var err_msg ='<div class="note note-danger">'+err_data.error_message+'</div>';
            $('#error_msg_div').html(err_msg);
            var body = $("html, body");
            body.animate({scrollTop:0}, '500', 'swing', function() {
            });
        }
        function removeErrorDialog() {
            $('#error_msg_div').html('');
        }

        @if($d_arr['p'] == 'basic')
        	jQuery.validator.addMethod("user_code_chk", function (value, element) {
                var user_code = $('#user_code').val();
                if (user_code != '') {
                    return true;
                }
                return false;
            }, "{{ trans('admin/productAdd.invalid_user_code') }}");

            $("#addProductfrm").validate({
                rules: {
                    @if($action == 'add')
                        user_code_display: {
                        	required: true,
                        	user_code_chk: true,
                        },
                    @endif
                    product_name: {
                        required: true,
                        minlength: "{{Config::get('webshoppack.title_min_length')}}",
                        maxlength: "{{Config::get('webshoppack.title_max_length')}}"
                    },
                    url_slug: {
                        required: true,
                    },
                        product_category_id: {
                        required: true,
                    },
                    product_tags: {
                        required: true,
                    },
                        product_highlight_text: {
                        maxlength: "{{Config::get('webshoppack.summary_max_length')}}"
                    },
                    demo_url: {
                        url:true
                    }
                },
                messages: {
                    @if($action == 'add')
                      user_code_display: {
                          required: mes_required
                      },
                    @endif
                    product_name: {
                        required: mes_required,
                        minlength: jQuery.format("{{trans('product.title_min_length')}}"),
                        maxlength: jQuery.format("{{trans('product.title_max_length')}}")
                    },
                    url_slug: {
                        required: mes_required
                    },
                    product_category_id: {
                        required: mes_required
                    },
                    product_tags: {
                        required: mes_required
                    },
                    product_highlight_text: {
                        maxlength: jQuery.format("{{trans('product.summary_max_length')}}")
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

            tinymce.init({
                menubar: "tools",
                selector: "textarea.fn_editor",
                mode : "exact",
                elements: "product_description",
                removed_menuitems: 'newdocument',
                apply_source_formatting : true,
                remove_linebreaks: false,
                height : 400,
                plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste emoticons jbimages"
                ],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons",
                relative_urls: false,
                remove_script_host: false
            });

            @if($action == 'add')
                /*var is_valid_product_user = false;
                var select_option = "{{ trans('common.select_option') }}";
                function chkValidUsername()
                {
                    var curr_url = product_actions_url;
                    var user_code = $('#user_code').val();
                    var postData = 'user_code='+user_code+'&action=check_user';
                    displayLoadingImage(true);
                    $(".fn_saveSectionCancel").trigger('click');
                    $.post(curr_url, postData,  function(response)
                    {
                        hideLoadingImage(false);
                        data = eval( '(' +  response + ')');
                        if(data.status=="success")
                        {
                            var section_options = eval(data.section_options);
                            $("#user_section_id").html('<option ="">'+ select_option +'</option>');
                            for(var i=0; i<section_options.length; i++)
                            {
                                $("#user_section_id").append('<option value='+ section_options[i].id +'>'+ section_options[i].section_name +'</option>');
                            }
                            $('.fn_addSection').show();
                            $('.fn_usercodeErr').html('');
                            is_valid_product_user = true;
                        }
                        else
                        {
                            $('#user_section_id').children().remove().end().append('<option ="">'+ select_option +'</option>');
                            $('.fn_addSection').hide();
                            $('.fn_usercodeErr').html(data.message);
                            is_valid_product_user = false;
                        }
                    });
                }*/

                $(window).load(function(){
					$.ajax({
						url: '{{ URL::to("admin/shop-owners-code-auto-complete") }}',
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

							$("#user_code_display").autocomplete({
								delay: 0,
								source: cat_data,
								minlength:3,
								select: function (event, ui) {
									$('#user_code').val(ui.item.user_id);
									return ui.item.label;
								},
								change: function (event, ui) {
									if (!ui.item) {
										$('#user_code').val('');
									}
								}
							});
						}
					});
		        });
            @endif

            $('.fn_addSection').click(function() {
                $('#sel_addSection').fadeIn();
                return false;
            });

            $('.fn_saveSectionCancel').click(function() {
                $('#section_name').val('');
                $('.fn_sectionErr').text('');
                $('#sel_addSection').fadeOut();
                return false;
            });

            $('.fn_saveSection').click(function(){
                var section_val = $("#section_name").val();
                if (section_val.trim() == '') {
                    $('.fn_sectionErr').html('{{ trans('common.required') }}');
                    $('#section_name').focus();
                    return false;
                }
                var user_code = '';
                if($('#user_code').length > 0)
                {
                    user_code = $('#user_code').val();
                }
                displayLoadingImage(true);

                $.post('{{ URL::action('AdminProductAddController@postAddSectionName') }}', { section_name: section_val, action: action, user_code: user_code},  function(response)
                {
                    data = eval( '(' +  response + ')');

                    if (data.status == 'success') {
                        $('#section_name').val('');

                        $('.fn_saveSectionCancel').trigger('click');
                        $('#user_section_id').append( new Option(data.section_name, data.section_id, true, true) );
                        hideLoadingImage(false);
                    } else {
                        hideLoadingImage(false);
                        $('.fn_sectionErr').html(data.error_message);
                    }
                });
            });

            var category_list_url = '{{ URL::action('AdminProductAddController@getProductSubCategories')}}';
            var listSubCategories = function ()
            {
                select_btn_id = arguments[0];	/* selected drop down box id */
                var sel_cat_id = $('#'+select_btn_id).val();	/* selected category id */
                remove_cat_id = parseInt(arguments[1]); /* catgory id to remove existing list */
                sel_cat_id_class = $('#'+select_btn_id).attr('class');	/* get existing class */
                $('#loading_sub_category').show();	/* display loading text */

                /* get sub category list */
                if (sel_cat_id != '')
                {
                    $.get(category_list_url + '?action=get_product_sub_categories&category_id=' + sel_cat_id,{},function(data)
                    {
                        data_arr = data.split('~~~');	/* contains new drop down element & new category id with top level categories */
                        data = data_arr[0];	/* assigned new drop down element */

                        existing_sel_ids = $('#my_selected_categories').val();
                        existing_sel_ids_arr = existing_sel_ids.split(',');

                        existing_sel_ids_length = existing_sel_ids_arr.length;
                        for (var i=0;i<existing_sel_ids_length;i++)
                        {

                            if( parseInt(existing_sel_ids_arr[i]) > remove_cat_id){
                                $('.fn_subCat_'+existing_sel_ids_arr[i]).remove()
                            }
                        }
                        $('.fn_clsNoSubCategryFound').hide();
                        /* add new sub categories list */
                        $('#sub_categories').append(data);

                        /* assign new hidden values */
                        $('#my_selected_categories').val(data_arr[1]); /* assign new categories list */
                        $('#my_category_id').val(sel_cat_id);	/* update category id hidden value */

                        //$('#sub_category_'+sel_cat_id).text("");	/* change css class */
                        $('#sub_category_'+sel_cat_id).addClass(sel_cat_id_class+' subCat_'+remove_cat_id)	/* change css class */
                        $('#loading_sub_category').hide();	/* hide loading text */

                    });
                }
                else
                {
                    $.get(category_list_url + '?action=get_product_sub_categories&category_id=' + remove_cat_id,{},function(data)
                    {
                        data_arr = data.split('~~~');	/* contains new drop down element & new category id with top level categories */
                        new_categories = data_arr[1];	/* assigned new categories */

                        existing_sel_ids = $('#my_selected_categories').val();
                        existing_sel_ids_arr = existing_sel_ids.split(',');
                        existing_sel_ids_length = existing_sel_ids_arr.length;
                        for (var i=0;i<existing_sel_ids_length;i++)
                        {
                            if( parseInt(existing_sel_ids_arr[i]) > remove_cat_id){
                                $('.fn_subCat_'+existing_sel_ids_arr[i]).remove()
                            }
                        }
                        /* assign new hidden values */
                        $('#my_selected_categories').val(new_categories); /* assign new categories list */
                        /* update category id hidden value */
                        if(root_category_id != remove_cat_id)
                        {
                            $('#my_category_id').val(remove_cat_id);
                        }
                        else
                        {
                            $('#my_category_id').val('');
                        }

                    });
                    $('#loading_sub_category').hide();
                }
            };
            setInputLimiterById('product_highlight_text', {{ Config::get('webshoppack.summary_max_length') }});

            function generateSlugUrl() {
                var title = $("#product_name").val();
                    if(title.trim() == "")
                    $("#url_slug").val('');
                    else if($("#url_slug").val().trim() == ''){
                    var slug_url = title.replace(/[^a-z0-9]/gi, '-');
                    slug_url = slug_url.replace(/(-)+/gi, '-');
                    slug_url = slug_url.replace(/^(-)+|(-)+$/g, '');
                    $("#url_slug").val(slug_url.toLowerCase());
                }
            }

            function setInputLimiterById(ident, char_limit)	{
            if ($('#'+ident).length > 0) {
                $('#'+ident).inputlimiter({
                    limit: char_limit,
                    remText: '{{ trans("common.words_remaining_1")}} %n {{ trans("common.words_remaining_2")}} %s {{ trans("common.words_remaining_3")}}',
                    limitText: '{{ trans("common.limitted_words_1")}} %n {{ trans("common.limitted_words_2")}}%s'
                });
                }
            }


        @elseif($d_arr['p'] == 'price')

            $(document).ready(function()
            {
                var specialKeys = new Array();
                specialKeys.push(8); //Backspace
                specialKeys.push(9); //tab
                specialKeys.push(13); //Enter
                $(function () {
                    //For Price
                    var specialKeysPrice = new Array();
                    specialKeysPrice.push(8); //Backspace
                    specialKeysPrice.push(9); //tab
                    specialKeysPrice.push(13); //Enter
                    specialKeysPrice.push(46); //decimal
                    $(".price").live("keypress", function (e) {
                        var keyCode = e.which ? e.which : e.keyCode
                        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
                        var errorDiv = '#error' + this.name.replace('price', '');
                        $(errorDiv).css("display", ret ? "none" : "inline");
                        $(errorDiv).text('Enter valid "Price"');
                        if (keyCode == 13 && this.value != '') {
                            $('#discount_percentage' + this.name.replace('price', '')).focus();
                        }
                        return ret;
                    });
                    $(".price").live("paste", function (e) {
                        return false;
                    });
                    $(".price").live("drop", function (e) {
                        return false;
                    });
                    $(".price").live("blur", function (e) {
                        var errorDiv = '#error' + this.name.replace('price', '');
                        if (this.value != '') {
                            this.value = parseFloat(this.value).toFixed(2);
                            var discount_percentage = 0;
                            if ($('#discount_percentage' + this.name.replace('price', '')).val() != '')
                                discount_percentage = parseFloat($('#discount_percentage' + this.name.replace('price', '')).val()).toFixed(2);
                            if (this.value) {
                                discounted_price = (this.value - ((this.value * discount_percentage) / 100)).toFixed(2);
                                $('#discount' + this.name.replace('price', '')).val(discounted_price);
                            }
                            if (this.value < 1) {
                                $(errorDiv).css("display", "inline");
                                $(errorDiv).text('"Price" should not less than {{ Config::get('generalConfig.site_default_currency') }} 1');
                                this.focus();
                            } else if (parseFloat($('#discount' + this.name.replace('price', '')).val()) < 1) {
                                $(errorDiv).css("display", "inline");
                                $(errorDiv).text('"Discount Price" should not less than {{ Config::get('generalConfig.site_default_currency') }} 1');
                                this.focus();
                            }
                        } else {
                            $('#discount_percentage' + this.name.replace('price', '')).val('');
                            $('#discount' + this.name.replace('price', '')).val('');
                        }
                    });
                    //For Discount Percentage
                    $(".discount-percentage").live("keypress", function (e) {
                        var keyCode = e.which ? e.which : e.keyCode
                        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
                        var errorDiv = '#error' + this.name.replace('discount_percentage', '');
                        $(errorDiv).css("display", ret ? "none" : "inline");
                        $(errorDiv).text('Enter valid "Discount"');
                        if (keyCode == 13) {
                            $('#discount' + this.name.replace('discount_percentage', '')).focus();
                        }
                        return ret;
                    });
                    $(".discount-percentage").live("paste", function (e) {
                        return false;
                    });
                    $(".discount-percentage").live("drop", function (e) {
                        return false;
                    });
                    $(".discount-percentage").live("keyup", function (e) {
                        if (this.value > 100) {
                            var errorDiv = '#error' + this.name.replace('discount_percentage', '');
                            $(errorDiv).css("display", "inline");
                            $(errorDiv).text('Enter valid discount percentage (less than or equal to 100%)');
                            $('#discount' + this.name.replace('discount_percentage', '')).val($('#price' + this.name.replace('discount_percentage', '')).val());
                        }
                    });
                    $(".discount-percentage").live("blur", function (e) {
                        var errorDiv = '#error' + this.name.replace('discount_percentage', '');
                        var discount_percentage = 0;
                        if (this.value)
                            discount_percentage = this.value = parseFloat(this.value).toFixed(2);
                        var price = 0;
                        var discounted_price = '';
                        if ($('#price' + this.name.replace('discount_percentage', '')).val() != '')
                            price = parseFloat($('#price' + this.name.replace('discount_percentage', '')).val()).toFixed(2);
                        if (price && discount_percentage <= 100) {
                            discounted_price = (price - ((price * discount_percentage) / 100)).toFixed(2);
                            $('#discount' + this.name.replace('discount_percentage', '')).val(discounted_price);
                        }
                        if (discounted_price != '' && discounted_price < 1) {
                            $(errorDiv).css("display", "inline");
                            $(errorDiv).text('"Discount Price" should not less than {{ Config::get('generalConfig.site_default_currency') }} 1');
                            this.focus();
                        }
                        if (this.value == '') {
                            $(errorDiv).css("display", "none");
                        }
                    });
                    //For discounted price
                    $(".discount").live("keypress", function (e) {
                        var keyCode = e.which ? e.which : e.keyCode
                        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeysPrice.indexOf(keyCode) != -1);
                        var errorDiv = '#error' + this.name.replace('discount', '');
                        $(errorDiv).css("display", ret ? "none" : "inline");
                        $(errorDiv).text('Enter valid "Discounted Price"');
                        return ret;
                    });
                    $(".discount").live("paste", function (e) {
                        return false;
                    });
                    $(".discount").live("drop", function (e) {
                        return false;
                    });
                    $(".discount").live("blur", function (e) {
                        if (this.value != '')
                            this.value = parseFloat(this.value).toFixed(2);
                    });

                    $("#edit_product").live("click", function(e){
                    	var submit = true;
						var purchase_price = $('#purchase_price').val();
                    	var purchase_err_div = $('#errorpurchase_');
						/*if (purchase_price == '' || isNaN(purchase_price) || parseFloat(purchase_price) < 0) {

							$(purchase_err_div).css("display", "inline");
                            $(purchase_err_div).text('Enter valid "Purchase price"');
                            submit = false;
						}*/
                        //Get the number of quantity ranges clsGroupPrices
                        var rangeLength = $('.clsGroupPrices').length;
                        var selected_val = $('input[name=is_free_product]:radio:checked').val();
                        if (submit && selected_val == 'No') { //validate group prices if not free product
                            //Loop group price block and validate each ranges fields
                            $('.clsGroupPrices').each(function(index) {
                                //Get the fields elements
                                var group_id = this.id.split('_')[1];
                                var range_index = this.id.split('_')[2];
                                var price = $('#price_' + group_id + '_' + range_index).val();
                                var discount_percentage = $('#discount_percentage_' + group_id + '_' + range_index).val();
                                var discount = $('#discount_' + group_id + '_' + range_index).val();
                                var errorDiv = $('#error_' + group_id + '_' + range_index);

                                //Get the range fields length
                                var currentGroupRangeLength = $('.clsGroupFields_' + group_id).length;

                                if ((price == '' && (parseInt(group_id) == 0 || currentGroupRangeLength > 1)) || parseFloat(price) <=  0) {
                                    $(errorDiv).css("display", "inline");
                                    $(errorDiv).text('Enter valid "Price"');
                                    submit = false;
                                } else if ((discount_percentage != '' && parseFloat(discount_percentage) >  100)) {
                                    $(errorDiv).css("display", "inline");
                                    $(errorDiv).text('Enter valid "Discount"');
                                    submit = false;
                                } else if ((discount == '' && (parseInt(group_id) == 0 || currentGroupRangeLength > 1)) || parseFloat(discount) <=  0 || parseFloat(price) < parseFloat(discount)) {
                                    $(errorDiv).css("display", "inline");
                                    $(errorDiv).text('"Discounted Price" is not valid');
                                    submit = false;
                                }

                            });
                        }
                        return submit;
                    });
                    $('#selGroupPriceBlock').show();
                });

                disableTransactionFee('{{ $p_details['site_transaction_fee_type'] }}');
                @if(Config::get("webshoppack.can_upload_free_product"))
                    var selected_val = $('input[name=is_free_product]:radio:checked').val();
                    if (selected_val=='Yes')
                    {
                        showPriceFields(false);
                    } else {
                        showPriceFields(true);
                    }

                    $('input[name=is_free_product]').click(function(){
                        var selected_val = $('input[name=is_free_product]:radio:checked').val();
                        if (selected_val=='Yes')
                        {
                            showPriceFields(false);
                        } else {
                            showPriceFields(true);
                        }

                    });
                @endif

                if ($('#global_transaction_fee_used').attr('checked')){
                    showTransactionFeeFields(false);
                }
                else{
                    showTransactionFeeFields(true);
                }

                $('#global_transaction_fee_used').click(function(){
                    if (this.checked){
                        showTransactionFeeFields(false);
                    }
                    else{
                        showTransactionFeeFields(true);
                    }
                });
            });

            function showPriceFields(flag) {
                if (flag) {
                    $('.fn_clsPriceFields').show();

                if($('#global_transaction_fee_used').is(":checked")){
                    showTransactionFeeFields(false);
                }
                else{
                    showTransactionFeeFields(true);
                }
                }
                else{
                    $('.fn_clsPriceFields').hide();
                    $('.fn_clsFeeFields').hide();
                }
            }

            function showTransactionFeeFields(flag){
                if (flag){
                    $('.fn_clsFeeFields').show();
                }
                else{
                    //Set transaction fee flat and percentage fields as zero
                    $('#site_transaction_fee').val(0);
                    $('#site_transaction_fee_percent').val(0);
                    $('.fn_clsFeeFields').hide();
                }
            }

            var disableTransactionFee = function() {
            var sel_type = arguments[0];

                if(sel_type == 'Mix') {
                    $('#site_transaction_fee').removeAttr('disabled');
                    $('#site_transaction_fee_percent').removeAttr('disabled');
                }
                else if(sel_type == 'Percentage') {
                    $('#site_transaction_fee').val(0);
                    $('#site_transaction_fee').attr('disabled', true);
                    $('#site_transaction_fee_percent').removeAttr('disabled');
                }
                else if(sel_type == 'Flat') {
                    $('#site_transaction_fee_percent').val(0);
                    $('#site_transaction_fee_percent').attr('disabled', true);
                    $('#site_transaction_fee').removeAttr('disabled');
                }
                return true;
            }
            $(function() {
                $('#product_discount_fromdate').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
                $('#product_discount_todate').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
            });
        @endif

        @if($d_arr['p'] == 'stocks')

            $('#stock_country_id_china').click(function() {
                if($('#stock_country_id_china').is(":checked")){
                    $('.fn_china_div').show();
                }
                else {
                    if ($('#quantity_china').val() > 0 || $('#serial_numbers_china').val() != "") {
                        confirmDialog('china');
                    } else {
                        $('.fn_china_div').hide();
                    }
                }
            });
            $('#stock_country_id_pak').click(function() {
                if($('#stock_country_id_pak').is(":checked")){
                    $('.fn_pak_div').show();
                }
                else {
                    if ($('#quantity_pak').val() > 0 || $('#serial_numbers_pak').val() != "") {
                        confirmDialog('pak');
                    } else {
                        $('.fn_pak_div').hide();
                    }
                }
            });

            function confirmDialog(country) {
                var dialog_msg = 'Are you sure that you want to remove Quantities & Serial numbers for China?';
                if(country == 'pak')
                    dialog_msg = 'Are you sure that you want to remove Quantities & Serial numbers for Pakistan?';
                $('#dialog_msg').html(dialog_msg);

                $("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
                    buttons: {
                        "{{ trans('common.yes') }}": function() {
                            if(country == 'china')
                                $('.fn_china_div').hide();
                            else
                                $('.fn_pak_div').hide();

                            $(this).dialog("close");
                        },
                        "{{ trans('common.no') }}": function() {
                            if(country == 'china') {
                                $('#stock_country_id_china').parent('span').addClass('checked');
                                $('#stock_country_id_china').attr('checked', 'checked');
                            }
                            else {
                                $('#stock_country_id_pak').parent('span').addClass('checked');
                                $('#stock_country_id_pak').attr('checked', 'checked');
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }

			$("#quantity").keypress(function (e) {
				if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					$("#card_error").html("Digits Only").show().fadeOut("slow");
					return false;
				}
			});

            $("#addProductStocksfrm").validate({
                onfocusout: injectTrim($.validator.defaults.onfocusout),
                rules: {
                    /*stock_country_id_china: {
                        required: {
                            depends: function(element) {
                                var quantity = $('#quantity_china').val();
                                var value = $('#serial_numbers_china').val();
                                var serial_numbers_array = value.split("\n");
                                var empty_lines = false;
                                for(i = 0; i < serial_numbers_array.length; i++) {
                                    if (serial_numbers_array[i].trim() == "") {
                                        empty_lines = true;
                                    }
                                }
                                if (!empty_lines || quantity != '')
                                    return true;
                                else
                                    return false;
                            }
						requried: {
                            	depends: function(element) {
                                	return ($("#stock_country_id_china").is(':checked')) ? true : false;
                            	}
                        	}
                        }
                    },*/
                    quantity: {
                        required: true,
                        min:1,
                        digits: true
                    },
                    serial_numbers: {
                        checkEmptyLines: {
                            depends: function(element) {
                                return ($(element).val()!='') ? true : false;
                            }
                        },
                        validateSerialNumber: {
                            depends: function(element) {
                                return ($(element).val()!='') ? true : false;
                            }
                        }
                    },
                    /*stock_country_id_pak: {
                        required: {
                            depends: function(element) {
                                var quantity = $('#quantity_pak').val();
                                var value = $('#serial_numbers_pak').val();
                                var serial_numbers_array = value.split("\n");
                                var empty_lines = false;
                                for(i = 0; i < serial_numbers_array.length; i++) {
                                    if (serial_numbers_array[i].trim() == "") {
                                        empty_lines = true;
                                    }
                                }
                                if (!empty_lines || quantity != '')
                                    return true;
                                else
                                    return false;
                            }
                        }
                    },*/

                },
                messages: {
                    quantity: {
                        required: mes_required,
                        min: jQuery.validator.format("{{trans('product.enter_value_greater_than')}} {0}"),
                        digits: jQuery.validator.format("{{trans('common.digits_validation')}}"),
                    },
                    serial_numbers: {
                        required: mes_required,
                    },
                },
                submitHandler: function(form) {
                    displayLoadingImage(true);
                    form.submit();
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

            jQuery.validator.addMethod("checkEmptyLines", function (value, element) {
                var serial_numbers_array = value.split("\n");
                var empty_lines = false;
                for(i = 0; i < serial_numbers_array.length; i++) {
                    if (serial_numbers_array[i].trim() == "") {
                        empty_lines = true;
                    }
                }
                if (empty_lines) {
                    return false;
                }
                return true;
            }, "{{trans('product.serial_number_empty_line_error')}}");

            jQuery.validator.addMethod("validateSerialNumber", function (value, element) {
                var quantity = $('#quantity').val();
                var serial_numbers_array = value.split("\n");
                if (serial_numbers_array.length == quantity) {
                    return true;
                }
                return false;
            }, "{{trans('product.serial_number_equals_qty_error')}}");
        @endif

        @if($d_arr['p'] == 'shipping')

            jQuery.validator.addMethod("decimallimit", function (value, element) {
                return this.optional(element) || /^[0-9]*(\.\d{0,2})?$/i.test(value);
            }, "Only two decimals allowed");

             jQuery.validator.addMethod("decimallimit3", function (value, element) {
                return this.optional(element) || /^[0-9]*(\.\d{0,3})?$/i.test(value);
            }, "Only three decimals allowed");

            $("#addProductShippingfrm").validate({
                rules: {
                    country_id: {
                        required: true,
                    },
                    shipping_fee: {
                        required: true,
                        number: true,
                        decimallimit: true
                    },
                },
                messages: {
                    country_id: {
                        required: mes_required,
                    },
                    shipping_fee: {
                        required: mes_required
                    },
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



            function editItemShippingRow(shipping_id, shipping_fee) {
                $('#shippingfeecurr_'+shipping_id).removeClass('hide');
                html = '<div class="clearfix">';
                html +=  '<div class="col-md-3 col-sm-3 col-xs-3 pad-left-0 mb10"><input type="text" name="shipping_fee" id="shipping_fee_'+shipping_id+'" value="' + shipping_fee + '" class="form-control" /></div>';
                html += '<div class="col-md-6 col-sm-6 col-xs-6 pad-left-0 mt5"><a class="btn btn-xs green" href="javascript: void(0);" onclick="saveShippingFeeAmount(' + shipping_id + ');"><i class="fa fa-save"></i> Save</a>';
                html += '<a class="btn btn-xs red" href="javascript: void(0);" onclick="cancelShippingFeeEdit(' + shipping_id + ', '+shipping_fee+');"><i class="fa fa-times"></i> Cancel</a></div>';
                html += '</div>';
                html += '<label for="shipping_fee_1" generated="true" class="error"></label>';
                $('#shippingfeetd_' + shipping_id).html(html);
            }

            function cancelShippingFeeEdit(shipping_id, shipping_fee) {
                if(shipping_fee <= 0)
                {
                    $('#shippingfeecurr_'+shipping_id).addClass('hide');
                    $('#shippingfeetd_' + shipping_id).html('<p class="mt5">{{trans('product.product_free')}}</p>');
                }
                else
                    $('#shippingfeetd_' + shipping_id).html('<p class="mt5"> '+shipping_fee+'</p>');
                removeErrorDialog();
            }

            function saveShippingFeeAmount(shipping_id)
            {
                if($('#shipping_fee_'+shipping_id).val() != '')
                {
                    shipping_fee = $('#shipping_fee_'+shipping_id).val();
                    postData = 'action=edit_shipping&shipping_id=' + shipping_id + '&shipping_fee=' + shipping_fee + '&product_id='+product_id,
                    displayLoadingImage(true);
                    $.post(product_actions_url, postData,  function(response)
                    {
                        hideLoadingImage (false);

                        data = eval( '(' +  response + ')');

                        if(data.result == 'success')
                        {
                            if(shipping_fee <= 0)
                            {
                                $('#shippingfeecurr_'+shipping_id).addClass('hide');
                                $('#shippingfeetd_' + shipping_id).html('<p class="mt5">{{trans('product.product_free')}}</p>');
                            }
                            else
                                $('#shippingfeetd_' + shipping_id).html('<p class="mt5"> '+shipping_fee+'</p>');
                            //$('#shippingfeetd_' + shipping_id).html('<p class="mt5"> '+shipping_fee+'</p>');

                            edited_event = 'javascript:editItemShippingRow('+shipping_id+','+shipping_fee+')';
                            $('#shipping_fee_edit_link_'+shipping_id).attr('onclick',edited_event);
                            updateProductStatus();
                            removeErrorDialog();
                        }
                        else
                        {

                            showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
                        }

                    });
                }
            }

            function removeItemShippingRow(shipping_id) {
                if (!confirmItemChange()) {
                    return false;
                }
                $("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
                buttons: {
                "{{ trans('common.yes') }}": function() {
                    postData = 'action=delete_shipping&shipping_id=' + shipping_id + '&product_id='+product_id,
                    displayLoadingImage(true);
                    $.post(product_actions_url, postData,  function(response)
                    {
                        hideLoadingImage (false);

                        data = eval( '(' +  response + ')');
                        if(data.result == 'success') {
                            $('#itemShippingRow_' + data.shipping_id).remove();

                            updateProductStatus();
                            removeErrorDialog();
                        }
                        else {
                            showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
                        }

                    });
                        $(this).dialog("close");
                    },
                        "{{ trans('common.no') }}": function() {
                        $(this).dialog("close");
                    }
                }
                });
            }
            $('#calc_cost').click(function(){
                $("#shipping_country").change();
            })
            $(".fnShippingCostEstimate" ).change(function() {
                var shipping_template = $("#shipping_template").val();
                var shipping_country = $("#shipping_country").val();
                var shipping_from_country = $("#shipping_from_country").val();
                var shipping_from_zip_code = $("#shipping_from_zip_code").val();

                var package_det = {};
                var weight = $('#weight_val').val();
                var custom = 'No';
                if($('input[name="custom"]:checked').length > 0)
                    custom = 'Yes';
                var first_qty = $('#first_qty').val();
                var additional_qty = $('#additional_qty').val();
                var additional_weight = $('#additional_weight').val();
                var length = $('#l_w_h_length').val();
                var width = $('#l_w_h_width').val();
                var height = $('#l_w_h_height').val();


                package_det['weight'] = weight;
                package_det['custom'] = custom;
                package_det['first_qty'] = first_qty;
                package_det['additional_qty'] = additional_qty;
                package_det['length'] = length;
                package_det['width'] = width;
                package_det['height'] = height;

                var pack_str = '';
                $.each(package_det, function(idx2,val2) {
                    if(pack_str == '')
                        pack_str  = idx2 + "=" + val2;
                    else
                        pack_str  += "&"+idx2 + "=" + val2;
                });

                var actions_url = '{{ URL::action('AdminProductAddController@postShippingCostEstimate')}}';
                var p_id = $('#id').val();
                postData = 'template_id=' + shipping_template + '&shipping_country=' + shipping_country + '&shipping_from_country='+shipping_from_country + '&shipping_from_zip_code='+shipping_from_zip_code +'&product_id=' + p_id+'&package_det='+package_det+'&'+pack_str,
                displayLoadingImage(true);
                $.post(actions_url, postData,  function(response)
                {
                    hideLoadingImage (false);
                    if(response)
                    {
                        $('#reference_shipping_cost_holder').html(response);
                    }
                });
            });
        @endif

        @if($d_arr['p'] == 'tax')

            jQuery.validator.addMethod("decimallimit", function (value, element) {
                return this.optional(element) || /^[0-9]*(\.\d{0,2})?$/i.test(value);
            }, "Only two decimals allowed");

            $("#addProductTaxfrm").validate({
                rules: {
                    taxation_id: {
                        required: true,
                    },
                    tax_fee: {
                        required: true,
                        number: true,
                        decimallimit: true
                    },
                    fee_type: {
                        required: true
                    }
                },
                messages: {
                    taxation_id: {
                        required: mes_required,
                    },
                    tax_fee: {
                        required: mes_required,
                    },
                    fee_type: {
                        required: mes_required,
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


            function editItemTaxRow(taxation_id, tax_fee, fee_type) {
                html = '<div class="clearfix">'
                html += '<div class="col-md-3 col-sm-3 col-xs-3 pad-left-0 mb10"><div class="input-group"><input type="text" name="tax_fee" id="tax_fee_'+taxation_id+'" class="form-control" value="' + tax_fee + '" class="clsTextBoxSmall"></div></div>';
                html += '<div class="col-md-3 col-sm-3 col-xs-3 pad-left-0 mb10"><div class="input-group"><select name="fee_type" id="fee_type_'+taxation_id+'" class="form-control"><option value="percentage">%</option><option value="flat">Flat (In {{ Config::get('generalConfig.site_default_currency') }})</option>	</select></div></div>';
                html += '<div class="col-md-6 col-sm-6 col-xs-6 pad-left-0 mt5"><a class="btn btn-xs green" href="javascript: void(0);" onclick="saveTaxFeeAmount(' + taxation_id + ');"><i class="fa fa-save"></i> Save</a>';
                html += '<a class="btn btn-xs red" href="javascript: void(0);" onclick="cancelTaxFeeEdit(' + taxation_id + ', '+tax_fee+', \''+fee_type+'\');"><i class="fa fa-times"></i> Cancel</a></div>';
                html += '</div>';
                html += '<label for="tax_fee_1" generated="true" class="error" style=""></label>';
                $('#taxfeetd_' + taxation_id).html('');
                $('#taxfeetd_' + taxation_id).append(html);
                $('#fee_type_'+taxation_id).val(fee_type);
            }

            function cancelTaxFeeEdit(taxation_id, tax_fee, fee_type) {
                if(fee_type == "flat")
                {
                    var currency_code = '{{Config::get('generalConfig.site_default_currency')}}';
                    tax_fee = '<strong>'+currency_code+' '+tax_fee+'</strong>';
                }
                else
                    tax_fee = '<strong>'+tax_fee+'%'+'</strong>';

                $('#taxfeetd_' + taxation_id).html('');
                $('#taxfeetd_' + taxation_id).append(tax_fee);

                //$('#feetypetd_' + taxation_id).html('');
                //$('#feetypetd_' + taxation_id).append(fee_type);

                //$('#fee_action_' + taxation_id).html('');
                removeErrorDialog();
            }

            function saveTaxFeeAmount(taxation_id)
            {
                if($('#shipping_fee_'+taxation_id).val() != '')
                {
                    tax_fee = $('#tax_fee_'+taxation_id).val();
                    fee_type = $('#fee_type_'+taxation_id).val();
                    postData = 'action=edit_tax&taxation_id=' + taxation_id + '&tax_fee=' + tax_fee + '&fee_type='+fee_type+'&product_id='+product_id,
                    displayLoadingImage(true);
                    var currency_code = '{{Config::get('generalConfig.site_default_currency')}}';
                    $.post(product_actions_url, postData,  function(response)
                    {
                        hideLoadingImage (false);
                        data = eval( '(' +  response + ')');
                        if(data.result == 'success') {
                            tax_fee_org = tax_fee;
                            $('#taxfeetd_' + taxation_id).html('');
                            if(fee_type == 'percentage')
                            tax_fee = '<strong>'+tax_fee+'%'+'</strong>';
                            else if(fee_type == 'flat')
                            tax_fee = '<strong>'+currency_code+' '+tax_fee+'</strong>';

                            $('#taxfeetd_' + taxation_id).append(tax_fee);

                            //$('#feetypetd_' + taxation_id).html('');
                            //$('#feetypetd_' + taxation_id).append(fee_type);

                            //$('#fee_action_' + taxation_id).html('');


                            edited_event = 'javascript:editItemTaxRow('+taxation_id+','+tax_fee_org+',\''+fee_type+'\')';
                            $('#tax_fee_edit_link_'+taxation_id).attr('onclick',edited_event);
                            updateProductStatus();

                            updateProductStatus();
                            removeErrorDialog();
                        }
                        else {
                            showErrorDialog({status: 'error', error_message: data.error_msg});
                        }
                    });
                }
            }

            function removeItemTaxRow(taxation_id)
            {
                if (!confirmItemChange()) {
                    return false;
                }
                $("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
                    buttons: {
                        "{{ trans('common.yes') }}": function()
                        {
                            postData = 'action=delete_tax&taxation_id=' + taxation_id + '&product_id='+product_id,
                            displayLoadingImage(true);
                            $.post(product_actions_url, postData,  function(response)
                            {
                                hideLoadingImage (false);

                                data = eval( '(' +  response + ')');
                                if(data.result == 'success')
                                {
                                    $('#itemTaxRow_' + data.taxation_id).remove();

                                    updateProductStatus();
                                    removeErrorDialog();
                                }
                                else
                                {
                                    showErrorDialog({status: 'error', error_message: data.error_msg});
                                }

                            });
                            $(this).dialog("close");
                        },
                        "{{ trans('common.no') }}": function()
                        {
                            $(this).dialog("close");
                        }
                    }
                });
            }

            $("#js-taxation-id" ).change(function() {
                if($(this).val()!='')
                {
                    postData = 'action=get_taxation_details&taxation_id=' + $(this).val() + '&product_id='+product_id,
                    displayLoadingImage(true);
                    $.post(product_actions_url, postData,  function(response)
                    {
                        hideLoadingImage (false);

                        data = eval( '(' +  response + ')');

                        if(data.result == 'success')
                        {
                            $('#js-tax_fee').val(data.tax_fee);
                            $('#js-fee_type').val(data.fee_type);
                            var fee_type_string = $("#js-fee_type option:selected" ).text();

                            $('*[data-id="js-fee_type"]').find(".filter-option").html(fee_type_string);
                            //$('#select2-chosen-2').html(fee_type_string);

                            if(data.fee_type == 'flat')
                            {
                                //$('#js-tax-currency').removeClass('hide');
                                //$('#js-tax-percentage').addClass('hide');
                            }
                            else
                            {
                                //$('#js-tax-currency').addClass('hide');
                                //$('#js-tax-percentage').removeClass('hide');
                            }
                        }
                        else
                        {
                            showErrorDialog({status: 'error', error_message: data.error_msg});
                        }
                    });//
                }
            });
            /*$("#js-fee_type").change(function() {

                if($(this).val() == 'flat')
                    $('#js-tax-currency').removeClass('hide');
                else
                    $('#js-tax-currency').addClass('hide');
            });*/

        @endif

        @if($d_arr['p'] == 'preview_files')
            var prev_max_upload = parseInt('{{ Config::get('webshoppack.preview_max') }}');
            function editItemImageTitle(product_id, type) {
                if (!confirmItemChange()) {
                    return false;
                }
                $('#item_' + type + '_image_title').show();
                $('#item_' + type + '_edit_span').hide();
                $('#item_' + type + '_image_save_span').show();
                $('#item_' + type + '_image_title').focus();
            }


            function saveProductImageTitle(product_id, type, no_process_dialog) {
                var image_title = encodeURIComponent($('#item_'+ type +'_image_title').val());
                postData = 'action=save_product_' + type + '_image_title&product_image_title=' + image_title +'&product_id=' + product_id;
                    if (!no_process_dialog)
                        displayLoadingImage (true);

                        $.post(product_actions_url, postData,  function(data)
                    {
                    if (data == 'success') {
                        $('#item_' + type + '_edit_span').show();
                        $('#item_' + type + '_image_save_span').hide();
                    } else {
                        showErrorDialog({status: 'error', error_message: '{{  trans("product.not_completed") }}'});
                    }
                    hideLoadingImage (false);
                });
            }

            $(function(){
                var btnUpload=$('#upload_thumb');
                new AjaxUpload(btnUpload, {
                    action: product_actions_url,
                    name: 'uploadfile',
                    data: ({action: 'upload_product_thumb_image',product_id : product_id, upload_tab: 'preview'}),
                    method: 'POST',
                    onSubmit: function(file, ext){
                        if (!confirmItemChange()) {
                            return false;
                        }
                        if (!(ext && /^({{ implode('|', Config::get("webshoppack.thumb_format_arr")) }})$/.test(ext))){
                            showErrorDialog({status: 'error', error_message: '{{ sprintf(trans("product.products_allowed_formats"), implode(',', Config::get("webshoppack.thumb_format_arr"))) }}'});
                            return false;
                        }
                        var settings = this._settings;
                        settings.data.item_image_title = $.trim($('#item_thumb_image_title').val());
                        displayLoadingImage(true);
                    },
                    onComplete: function(file, response) {

                        data = eval( '(' +  response + ')');
                        hideLoadingImage(false);
                        if(data.status=="success") {
                            $('#item_thumb_image_id').attr('src',data.server_url + '/'+ data.filename);
                            if (data.t_width == '') {
                                $('#item_thumb_image_id').removeAttr('width');
                            } else {
                                $('#item_thumb_image_id').attr('width',data.t_width)
                            }

                            if (data.t_height == '') {
                                $('#item_thumb_image_id').removeAttr('height');
                            } else {
                                $('#item_thumb_image_id').attr('height',data.t_height)
                            }

                            if ($('#item_thumb_image_title').val() == '') {
                                $('#item_thumb_image_title').val(data.title);
                            }

                            $('#item_thumb_image_id').attr('title', $('#item_thumb_image_title').val())
                                        .attr('alt', $('#item_thumb_image_title').val());


                            $('#item_thumb_image_id').show();
                            $('#link_remove_thumb_image').show();
                            $('#item_thumb_image_title_holder').show();

                            updateProductStatus();

                        } else{
                            showErrorDialog(data);
                        }
                    }
                });
            });

            function removeItemDefaultImage(product_id)
            {
                if (!confirmItemChange()) {
                    return false;
                }
                $("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
                    buttons:
                    {
                        "{{ trans('common.yes') }}": function()
                        {
                            postData = 'action=remove_default_image&product_id=' + product_id;
                            displayLoadingImage(true);
                            $.post(product_actions_url, postData,  function(data)
                            {
                                if (data == 'success') {
                                    $('#item_default_image_id').attr('src', '');
                                    @if(isset($d_arr['default_no_image']) && $d_arr['default_no_image']['width'] > 0)
                                        $('#item_default_image_id').attr('width', '');
                                    @else
                                        $('#item_default_image_id').removeAttr('width');
                                    @endif

                                    @if(isset($d_arr['default_no_image']) && $d_arr['default_no_image']['height'] > 0)
                                        $('#item_default_image_id').attr('height', '');
                                    @else
                                        $('#item_default_image_id').removeAttr('height');
                                    @endif

                                    $('#item_default_image_id').attr('title', '')
                                    .attr('alt', '');
                                    $('#item_default_image_id').hide();
                                    $('#link_remove_default_image').hide();
                                    $('#item_default_image_title').val('');
                                    $('#item_default_image_title_holder').hide();

                                    updateProductStatus();
                                } else {
                                    showErrorDialog({status: 'error', error_message: '{{  trans("product.not_completed") }}'});
                                }
                                hideLoadingImage(false);
                            });
                            $(this).dialog("close");
                        },
                        "{{  trans("common.no") }}": function() { $(this).dialog("close"); }
                    }
                });
            }

            function removeProductThumbImage(product_id)
            {
                if (!confirmItemChange()) {
                    return false;
                }
                $("#dialog-delete-confirm").dialog({
                    title: cfg_site_name, modal: true,
                    buttons:
                    {
                        "{{ trans('common.yes') }}": function()
                        {
                            postData = 'action=remove_default_thumb_image&product_id=' + product_id;
                            displayLoadingImage(true);
                            $.post(product_actions_url, postData,  function(data)
                            {
                                if (data == 'success') {
                                    $('#item_thumb_image_id').attr('src', '');

                                    @if(isset($d_arr['thumb_no_image']) && $d_arr['thumb_no_image']['width'] > 0)
                                        $('#item_thumb_image_id').attr('width', '');
                                    @else
                                        $('#item_thumb_image_id').removeAttr('width');
                                    @endif

                                    @if(isset($d_arr['thumb_no_image']) && $d_arr['thumb_no_image']['height'] > 0)
                                        $('#item_thumb_image_id').attr('height', '');
                                    @else
                                        $('#item_thumb_image_id').removeAttr('height');
                                    @endif

                                    $('#link_remove_thumb_image').hide();
                                    $('#item_thumb_image_id').attr('title', '');
                                    $('#item_thumb_image_id').attr('alt', '');
                                    $('#item_thumb_image_id').hide();
                                    $('#item_thumb_image_title').val('');
                                    $('#item_thumb_image_title_holder').hide();

                                    updateProductStatus();
                                } else {
                                    showErrorDialog({status: 'error', error_message: '{{  trans("product.not_completed") }}'});
                                }
                                hideLoadingImage(false);
                            });
                            $(this).dialog("close");
                        }, "{{  trans("common.no") }}": function() { $(this).dialog("close"); }
                    }
                });
            }

            $(function(){
                var btnUpload=$('#upload_default');
                new AjaxUpload(btnUpload, {
                    action: product_actions_url,
                    name: 'uploadfile',
                    data: ({action: 'upload_item_default_image',product_id : product_id, upload_tab: 'preview'}),
                    onSubmit: function(file, ext){
                        if (!confirmItemChange()) {
                            return false;
                        }
                        if (!(ext && /^({{ implode('|', Config::get("webshoppack.default_format_arr")) }})$/.test(ext))){
                            showErrorDialog({status: 'error', error_message: '{{ sprintf(trans("product.products_allowed_formats"), implode(',', Config::get("webshoppack.thumb_format_arr"))) }}'});
                            return false;
                        }

                    var settings = this._settings;
                        settings.data.item_image_title = $.trim($('#item_default_image_title').val());
                        displayLoadingImage(true);
                    },
                    onComplete: function(file, response){

                        data = eval( '(' +  response + ')');

                        hideLoadingImage (false);
                        if(data.status=="success"){
                            $('#item_default_image_id').attr('src',data.server_url + '/'+ data.filename);

                            $('#item_default_image_title_id').html(data.title);

                        if ($('#item_default_image_title').val() == '') {
                            $('#item_default_image_title').val(data.title);
                        }

                        $('#item_default_image_id').attr('title',$('#item_default_image_title').val())
                                    .attr('alt',$('#item_default_image_title').val());

                        $('#item_default_image_id').show();
                        $('#link_remove_default_image').show();
                        $('#item_default_image_title_holder').show();

                        updateProductStatus();

                        } else{
                            showErrorDialog(data);
                        }
                    }
                });
            });

			@if(isset($d_arr['allow_swap_image']) && $d_arr['allow_swap_image'] > 0)
				<!-- Swap image start -->
				var swap_img_max_upload = parseInt('{{ Config::get('variations::variations.swap_img_max') }}');
				$(function()
				{
					var btnUpload=$('#upload_swap_image');
					var status=$('#swap_image_status');
					new AjaxUpload(btnUpload,
					{
						action: product_actions_url,
						name: 'uploadfile',
						data: ({action: 'upload_swap_image',product_id : product_id, resource_type: 'swap_image', upload_tab: 'preview'}),
						onSubmit: function(file, ext){
							var resource_type = 'swap_image';
							if (!confirmItemChange()) {
								return false;
							}

							if (!(ext && /^({{ implode('|', Config::get("webshoppack.preview_format_arr")) }})$/.test(ext))){ // e.g. jpg|jpeg|gif etc
							showErrorDialog({status: 'error', error_message: '{{ sprintf(trans("product.products_allowed_formats"), implode(',', Config::get("webshoppack.preview_format_arr"))) }}'});
								return false;
							}

							displayLoadingImage(true);
						},
						onComplete: function(file, response) {
							//console.info(response);
							data = eval( '(' +  response + ')');

							hideLoadingImage (false);
							if(data.status=="success")
							{
								// files folder come from config media.folder
								$('#swap_image_files').html('');
								html_text = '<tr  id="itemSwapImageRow_' + data.resource_id  + '" class="formBuilderRow"><td>';

								if (data.resource_type == 'swap_image') {
									html_text += '<a href="#"><img width="' + data.t_width + '" height="' + data.t_height +'" src="' +  data.server_url + '/'+ data.filename + '"  alt="' + data.title + '" title="' + data.title + '" /></a>';
								} else {
									html_text += '<p><a href="#">{{ trans("product.products_product_preview_type_unknown")  }}</a></p>';
								}

								html_text +=  '</td><td><div class="row"><div class="col-md-8"><input class="form-control" type="text" name="item_swap_image_' + data.resource_id +'" id="swap_image_title_field_' + data.resource_id +'" value="'+ data.title +'" onkeypress="javascript:editItemSwapImageTitle(' + data.resource_id +');"  /></div>';
								html_text +=  '<div class="col-md-2"><div class="mt5"><span  style="display:none;" id="item_swap_image_save_span_' + data.resource_id +'"><a class="btn btn-xs green" onclick="javascript:saveItemSwapImageTitle(' + data.resource_id +');" href="javascript: void(0);"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a></span>';
								html_text += '<span id="item_swap_image_edit_span_' + data.resource_id +'"><a class="btn blue btn-xs" onclick="javascript:editItemSwapImageTitle(' + data.resource_id + ');" href="javascript: void(0);"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a></span></div>';
								html_text += '</div></div></td><td><a onclick="javascript:removeSwapImageRow(' + data.resource_id +');" href="javascript: void(0);" title="{{ trans('common.delete') }}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>';
								html_text += '</td></tr>';

								$('.fn_ItemSwapImageListBody').append(html_text);
								$('.fn_ItemSwapImageListTable').tableDnDUpdate();
								initializeRowMouseOver();
								updateProductStatus();
								hideResourceUploadButton('swap_image');

								if ($('#swap_image_tbl_body').children().length > 1) {
									 $('#id_item_preview').show();
								}
								//$(html_text).insertAfter('#ItemResourceTblHeader');
							} else{
								showErrorDialog(data);
							}
						}
					});
				});

				function removeSwapImageRow(resource_id)
				{
					if (!confirmItemChange()) {
						return false;
					}
					$("#dialog-delete-confirm").dialog({
						title: cfg_site_name, modal: true,
						buttons: {
							"{{ trans('common.yes') }}": function() {
								postData = 'action=delete_swap_image&row_id=' + resource_id + '&product_id='+product_id,
								//alert(product_actions_url+postData);return false;
								displayLoadingImage(true);
								$.post(product_actions_url, postData,  function(response)
								{
									hideLoadingImage (false);
									//console.info(data);
									data = eval( '(' +  response + ')');
									if(data.result == 'success')
									{
										$('#itemSwapImageRow_' + data.row_id).remove();
										if ($('#swap_image_tbl_body').children().length <= 1) { $('#id_item_preview').hide(); }

										// this should only show when the items is less than allowed
										if ($('.fn_formBuilderListBody').children().length < (prev_max_upload + 1)) {
											$('.fn_ItemUploadResourceFileButton').css('display', 'block');
										}
										updateProductStatus();
									}
									else
									{
										showErrorDialog({status: 'error', error_message: '{{ trans("product.not_completed") }}'});
									}
								});
								$(this).dialog("close");
							},
							"{{ trans('common.no') }}": function() { $(this).dialog("close"); }
						}
					});
				}

				function editItemSwapImageTitle(resource_id) {
					if (!confirmItemChange()) {
						return false;
					}
					$('#swap_image_title_field_' + resource_id).show();
					$('#item_swap_image_edit_span_' + resource_id).hide();
					$('#item_swap_image_save_span_' + resource_id).show(); //.addClass('clsSubmitButton');
					$('#swap_image_title_field_' + resource_id).focus();
					return false;
				}

				function saveItemSwapImageTitle(resource_id) {
					var resource_title = encodeURIComponent($('#swap_image_title_field_' + resource_id).val());
					postData = 'action=save_swap_image_title&row_id=' + resource_id + '&resource_title=' + 	encodeURIComponent($('#swap_image_title_field_' + resource_id).val());
					displayLoadingImage(true);
					$.post(product_actions_url, postData,  function(data)
					{
					if (data == 'success') {
						$('#item_swap_image_edit_span_' + resource_id).show();
						$('#item_swap_image_save_span_' + resource_id).hide();// .removeClass('clsSubmitButton');
						updateProductStatus();
					} else {
						showErrorDialog({status: 'error', error_message: '{{  trans("product.not_completed") }}'});
					}
						hideLoadingImage (false);
					});
					return false;
				}

				function hideSwapImageUploadButton(resource_type)
				{
					if (resource_type == 'swap_image' && $('.fn_ItemSwapImageListBody').children().length >= ( swap_img_max_upload + 1)) {
						$('.fn_ItemUploadSwapImageFileButton').css('display', 'none');
					}
				}
				<!-- Swap image end -->
			@endif

            $(function()
            {
                var btnUpload=$('#upload');
                var status=$('#status');
                new AjaxUpload(btnUpload,
                {
                    action: product_actions_url,
                    name: 'uploadfile',
                    data: ({action: 'upload_resource_preview',product_id : product_id, resource_type: 'image', upload_tab: 'preview'}),
                    onSubmit: function(file, ext){
                        var resource_type = 'image';
                        if (!confirmItemChange()) {
                            return false;
                        }

                        if (!(ext && /^({{ implode('|', Config::get("webshoppack.preview_format_arr")) }})$/.test(ext))){ // e.g. jpg|jpeg|gif etc
                        showErrorDialog({status: 'error', error_message: '{{ sprintf(trans("product.products_allowed_formats"), implode(',', Config::get("webshoppack.preview_format_arr"))) }}'});
                            return false;
                        }

                        displayLoadingImage(true);
                    },
                    onComplete: function(file, response) {

                        data = eval( '(' +  response + ')');

                        hideLoadingImage (false);
                        if(data.status=="success")
                        {
                            // files folder come from config media.folder
                            $('#files').html('');
                            html_text = '<tr  id="itemResourceRow_' + data.resource_id  + '" class="formBuilderRow"><td>';

                            if (data.resource_type == 'Image') {
                                html_text += '<a href="#"><img width="' + data.t_width + '" height="' + data.t_height +'" src="' +  data.server_url + '/'+ data.filename + '"  alt="' + data.title + '" title="' + data.title + '" /></a>';
                            } else {
                                html_text += '<p><a href="#">{{ trans("product.products_product_preview_type_unknown")  }}</a></p>';
                            }

                            html_text +=  '</td><td><div class="row"><div class="col-md-8"><input class="form-control" type="text" name="item_resource_image_' + data.resource_id +'" id="resource_title_field_' + data.resource_id +'" value="'+ data.title +'" onkeypress="javascript:editItemResourceTitle(' + data.resource_id +');"  /></div>';
                            html_text +=  '<div class="col-md-2"><div class="mt5"><span  style="display:none;" id="item_resource_save_span_' + data.resource_id +'"><a class="btn btn-xs green" onclick="javascript:saveItemResourceTitle(' + data.resource_id +');" href="javascript: void(0);"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a></span>';
                            html_text += '<span id="item_resource_edit_span_' + data.resource_id +'"><a class="btn blue btn-xs" onclick="javascript:editItemResourceTitle(' + data.resource_id + ');" href="javascript: void(0);"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a></span></div>';
                            html_text += '</div></div></td><td><a onclick="javascript:removeItemResourceRow(' + data.resource_id +');" href="javascript: void(0);" title="{{ trans('common.delete') }}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>';
                            html_text += '</td></tr>';

                            $('.fn_formBuilderListBody').append(html_text);
                            $('.fn_ItemResourceImageListTable').tableDnDUpdate();
                            initializeRowMouseOver();
                            updateProductStatus();
                            hideResourceUploadButton('image');

                            if ($('#preview_tbl_body').children().length > 1) {
                                 $('#id_item_preview').show();
                            }
                            //$(html_text).insertAfter('#ItemResourceTblHeader');
                        } else{
                            showErrorDialog(data);
                        }
                    }
                });
            });

            function removeItemResourceRow(resource_id)
            {
                if (!confirmItemChange()) {
                    return false;
                }
                $("#dialog-delete-confirm").dialog({
                    title: cfg_site_name, modal: true,
                    buttons: {
                        "{{ trans('common.yes') }}": function() {
                            postData = 'action=delete_resource&row_id=' + resource_id + '&product_id='+product_id,
                            //alert(product_actions_url+postData);return false;
                            displayLoadingImage(true);
                            $.post(product_actions_url, postData,  function(response)
                            {
                                hideLoadingImage (false);

                                data = eval( '(' +  response + ')');
                                if(data.result == 'success')
                                {
                                    $('#itemResourceRow_' + data.row_id).remove();
                                    if ($('#preview_tbl_body').children().length <= 1) { $('#id_item_preview').hide(); }

                                    // this should only show when the items is less than allowed
                                    if ($('.fn_formBuilderListBody').children().length < (prev_max_upload + 1)) {
                                        $('.fn_ItemUploadResourceFileButton').css('display', 'block');
                                    }
                                    updateProductStatus();
                                }
                                else
                                {
                                    showErrorDialog({status: 'error', error_message: '{{ trans("product.not_completed") }}'});
                                }
                            });
                            $(this).dialog("close");
                        },
                        "{{ trans('common.no') }}": function() { $(this).dialog("close"); }
                    }
                });
            }

            // Item status is not changed to Draft since, only ordering changes.
            $(document).ready(function()
            {
                $(".fn_ItemResourceImageListTable").tableDnD({
                    onDrop: function(table, row)
                    {
                        var postData = 'action=order_resource&' + $.tableDnD.serialize();
                        displayLoadingImage(true);
                        $.post(product_actions_url, postData,  function(data)
                        {
                            // mostly no output, only update happens;
                            hideLoadingImage (false);
                        });
                    }
                });

                initializeRowMouseOver();
                hideResourceUploadButton('image');
            });

            function hideResourceUploadButton(resource_type)
            {
                if (resource_type == 'image' && $('.fn_formBuilderListBody').children().length >= ( prev_max_upload + 1)) {
                    $('.fn_ItemUploadResourceFileButton').css('display', 'none');
                }
            }
        @endif

        @if($d_arr['p'] == 'download_files')
            $(function(){
                var btnUpload=$('#upload');
                var status=$('#status');
                new AjaxUpload(btnUpload, {
                    action: product_actions_url,
                    name: 'uploadfile',
                    data: ({action: 'upload_resource_file',product_id : product_id, upload_tab: 'download_file'}),
                    onSubmit: function(file, ext){
                        if (!confirmItemChange()) {
                            return false;
                        }

                        /*if ($('.formBuilderListBody').children().length >= 2) {
                        //alert('Only one file can be uploaded');
                        //return false;
                        }*/
                        if (!(ext && /^({{ implode('|', Config::get("webshoppack.download_format_arr")) }})$/.test(ext)))
                        {
                            showErrorDialog({
                                status: 'error', error_message: '{{ sprintf(trans("product.products_download_file_format_msg"), implode(',', Config::get("webshoppack.download_format_arr"))) }}'
                            });
                            return false;
                        }
                        displayLoadingImage(true);
                    },
                    onComplete: function(file, response)
                    {

                        data = eval( '(' +  response + ')');

                        hideLoadingImage (false);
                        if(data.status=="success"){
                            // files folder come from config media.folder
                            html_text = '<tr  id="itemResourceRow_' + data.resource_id  + '" class="formBuilderRow"><td>';
                            html_text += '<a href="' +  data.download_url + '">'+ data.filename + '</a></td><td><div class="row">';
                            html_text +=  '<div class="col-md-8"><input class="form-control" type="text" name="item_resource_image_' + data.resource_id +'" id="resource_title_field_' + data.resource_id +'" value="'+ data.title +'" onkeypress="javascript:editItemResourceTitle(' + data.resource_id +');" /></div>';
                            html_text += '<div class="col-md-2"><div class="mt5"><span id="item_resource_edit_span_' + data.resource_id +'"><a class="btn btn-xs blue" onclick="javascript:editItemResourceTitle(' + data.resource_id + ');" href="javascript: void(0);"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a></span>';
                            html_text += '<span  style="display:none;" id="item_resource_save_span_' + data.resource_id +'"><a class="btn btn-xs green" onclick="javascript:saveItemResourceTitle(' + data.resource_id +');" href="javascript: void(0);"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a></span></div>';
                            html_text += '</div></div></td><td><a onclick="javascript:removeDownloadItemResourceRow(' + data.resource_id +');" href="javascript: void(0);" title="{{ trans('common.delete') }}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>';
                            html_text += '</td></tr>';
                            $('.formBuilderListBody').append(html_text);
                            btnUpload.css('display','none');
                            updateProductStatus();
                        } else{
                        showErrorDialog(data);
                        }
                    }
                });
            });

            function removeDownloadItemResourceRow(resource_id)
            {
                if (!confirmItemChange()) {
                    return false;
                }
                $("#dialog-delete-confirm").dialog({
                    title: cfg_site_name, modal: true,
                    buttons: {
                        "{{ trans('common.yes') }}": function() {
                            postData = 'action=delete_resource&row_id=' + resource_id + '&product_id='+product_id,
                            displayLoadingImage(true);
                            $.post(product_actions_url, postData,  function(response)
                            {
                                hideLoadingImage (false);
                                data = eval( '(' +  response + ')');
                            if(data.result == 'success')
                                {
                                    $('#itemResourceRow_' + data.row_id).remove();
                                if ($('#upload').length > 0) {
                                    $('#upload').css('display','block');
                                }
                                    updateProductStatus();
                                }
                            else
                            {
                                showErrorDialog({status: 'error', error_message: '{{ trans("product.not_completed") }}'});
                            }

                            });
                                $(this).dialog("close");

                        },
                        "{{ trans('common.no') }}": function() { $(this).dialog("close"); }
                    }
                });
            }

        @endif

        @if($d_arr['p'] == 'download_files' || $d_arr['p'] == 'preview_files' )
            function editItemResourceTitle(resource_id) {
                if (!confirmItemChange()) {
                    return false;
                }
                $('#resource_title_field_' + resource_id).show();
                $('#item_resource_edit_span_' + resource_id).hide();
                $('#item_resource_save_span_' + resource_id).show(); //.addClass('clsSubmitButton');
                $('#resource_title_field_' + resource_id).focus();
                return false;
            }

            function saveItemResourceTitle(resource_id) {
                var resource_title = encodeURIComponent($('#resource_title_field_' + resource_id).val());
                postData = 'action=save_resource_title&row_id=' + resource_id + '&resource_title=' + 	encodeURIComponent($('#resource_title_field_' + resource_id).val());
                displayLoadingImage(true);
                $.post(product_actions_url, postData,  function(data)
                {
                if (data == 'success') {
                    $('#item_resource_edit_span_' + resource_id).show();
                    $('#item_resource_save_span_' + resource_id).hide();// .removeClass('clsSubmitButton');
                    updateProductStatus();
                } else {
                    showErrorDialog({status: 'error', error_message: '{{  trans("product.not_completed") }}'});
                }
                    hideLoadingImage (false);
                });
                return false;
            }
        @endif

		@if($d_arr['allow_variation'] && $d_arr['p'] == 'variations') // variation tab starts

			$('#prd_var_grp').change(function(){
				postData = 'action=load_group_variations_list&group_id=' +  $('#prd_var_grp').val() + '&product_id=' + product_id;
				$.post(product_actions_url, postData,  function(response)
				{
					$('#regenVarGrp').html(response);
					// disable the checkbox if not assigned yet
					$('.varChkOpt').each(function(index) {
						if ($(this).attr('checked'))
							$(this).parents('tr').find('.varAttrOpt').removeAttr("disabled");
						else
							$(this).parents('tr').find('.varAttrOpt').attr("disabled", "disabled");
					});
				});
				return false;
			});

			$( document ).on( "click", '#edit_product', function() {
				if($("#frmItemVarOptList input[type=checkbox]:checked").length == 0 ||
					($("#frmItemVarOptList input[type=checkbox]:checked").length == 1 && $("#checkall").attr('checked')))
					{
						showErrorDialog({status: 'error', error_message: 'Select atleast one variation and attribute '});
						return false;
					}
				return true;
			});

			$( document ).on( "click", '#variationlist_ckbox', function() {
				if($(this).is(':checked')) {
					$('.case').each(function(index) {
						$(this).prop('checked', true);
						$(this).parent().addClass('checked');
					});
				}
				else
				{
					$('.case').each(function(index) {
						$(this).prop('checked', false);
						$(this).parent().removeClass('checked');
					});
				}
			});

			$( document ).on( "click", '#variationlist_sel_ckbox', function() {
				if($(this).is(':checked'))
				{
					$('.case').each(function(index) {
						$(this).prop('checked', true);
						$(this).parent().addClass('checked');
					});

					$('.varAttrOpt').each(function(index) {
						$(this).prop('checked', true);
						$(this).parent().addClass('checked');
					});
				}
				else
				{
					$('.case').each(function(index) {
						$(this).prop('checked', false);
						$(this).parent().removeClass('checked');
					});

					$('.varAttrOpt').each(function(index) {
						$(this).prop('checked', false);
						$(this).parent().removeClass('checked');
					});

				}
				//$('.varAttrOpt').prop('checked', this.checked);
			});

			$(document).on("click", '.varChkOpt', function(){
				$(this).parents('tr').find('.varAttrOpt').prop('checked', this.checked);
				if ($(this).attr('checked'))
				{
					var elemt = $(this).parents('tr').find('.varAttrOpt');
					elemt.each(function(index) {
						$(this).removeAttr("disabled");
					});
				}
				else
				{
					var elemt = $(this).parents('tr').find('.varAttrOpt');
					elemt.each(function(index) {
						$(this).attr("disabled", "disabled").prop('checked', false).parent().removeClass('checked');
					});
				}
			});

			var showMatrixUpdate = function() {
				matrix_id = arguments[0];
				var edit_field = ''; //all fields
				if(arguments.length > 1)
				{
					edit_field =  arguments[1];
				}
				if(matrix_id == 'multiple')
				{
					var selectedItems = new Array();
					$("input[name='matrix_ids[]']:checked").each(function()
					{
						selectedItems.push($(this).val());
					});
					postData = 'matrix_ids[]='+selectedItems+'&mul_mat_edit=1&edit_field='+edit_field;
				}
				else
				{
					postData = 'matrix_id='+matrix_id;
				}
				displayLoadingImage(true);
				postData += '&action=getMatrixDetails&product_id=' + product_id;
				$.post(product_actions_url, postData,  function(response)
				{
					hideLoadingImage (false);
					$('#matrixUpdateBlock').html(response);
					$('#matrixUpdateBlock').removeClass('disp-none').focus();
				});
				return false;
			}

			function doMatrixAction(action, matrix_id, attribute_id) {
				if(action == 'delete_matrix')
				{
					$("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
							buttons: {
								"{{ trans('common.yes') }}": function() {
							$(this).dialog("close");
							displayLoadingImage(true);
						postData = 'action='+action+'&matrix_id='+matrix_id+'&product_id=' + product_id+ '&attribute_id=' + attribute_id;
						$.post(product_actions_url, postData,  function(response)
						{
							hideLoadingImage (false);
							$('#matrix_'+matrix_id).remove();
						});
					/* End of button YES */
							}, "{{ trans('common.no') }}": function() { $(this).dialog("close"); }
						}
					});
				}
				else
				{
					displayLoadingImage(true);
					postData = 'action='+action+'&matrix_id='+matrix_id+'&product_id=' + product_id+ '&attribute_id=' + attribute_id;
					$.post(product_actions_url, postData,  function(response)
					{
						hideLoadingImage (false);
						if(action == 'enable_matrix'){
							$('#matrix_'+matrix_id).removeClass('clsVariationListDisabled');
						}
						else if(action == 'disable_matrix') {
							$('#matrix_'+matrix_id).addClass('clsVariationListDisabled');
						}

						if(action == 'set_default_matrix' || action == 'rem_default_matrix') {
							$('#var_matrix_block').html(response);
						}
						else {
							$('#matrix_'+matrix_id).html(response);
						}
						return false;
					});
				}
			}

			$('.fn_updSwapImg').click(function() {
				var elementId  = $(this).closest('tr').attr('id');
				var elem_arr = elementId.split('_');
				$('#sel_matrix_id').val(elem_arr[1]);
			});

			$(".fn_updSwapImg").fancybox({
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

			function srcUpdImg(selImg, sel_img_id){
				$('#matrix_swap_img_id').val(sel_img_id);
				var matrix_id = $('#sel_matrix_id').val();
				if(matrix_id == '')
					return false;
				displayLoadingImage(true);
				postData = 'action=updateMatrixSwapImage&product_id=' + product_id + '&matrix_id=' + matrix_id + '&matrix_swap_img_id=' + sel_img_id;
				$.post(product_actions_url, postData,  function(response)
				{
					$('#sel_matrix_id').val('');
					hideLoadingImage (false);
					$('#matrix_'+matrix_id).html(response);
					return false;
				});
				return false;
			}

			function assgnImg(selImg, sel_img_id){
				$('#item_swap_image_id').attr('src', selImg);
				$('#matrix_swap_img_id').val(sel_img_id);
				$('#remSwapImg').parent('span').show();
			}

			function showGrp(){
				$('#var_grp_block').show();
				$('#var_matrix_block').hide();
				$('#var_matrix_block').next('div').hide();
			}

			$( document ).on( "click", '#cancel_populate', function() {
				$('#var_grp_block').hide();
				$('#var_matrix_block').show();
				$('#var_matrix_block').next('div').show();
			});

			$( document ).on( "click", '#cancel_update_matrix', function() {
				$('#frmItemMatrixUpdate')[0].reset();
				$('#matrix_price_impact, #matrix_giftwrap_price_impact, #matrix_shippingfee_impact').trigger('change');
				$('#matrixUpdateBlock').addClass('disp-none');
			});

			$( document ).on( "change", '#matrix_price_impact, #matrix_giftwrap_price_impact, #matrix_shippingfee_impact', function() {
				if($(this).val() == 'unchange') {
					$(this).parent('div').next('div').find('span').hide();
				}
				else {
					$(this).parent('div').next('div').find('span').show();
					$(this).parent('div').next('div').find('span').find('input[type=text]').val("");
				}

			});

			$(document).on( "click", '.remMatSwapImg', function() {
				var matrix_id = $(this).attr('alt');
				$("#dialog_msg").html(remove_swap_msg);
				$("#dialog-delete-confirm").dialog({
					title: matrix_edit_head, modal: true,
					buttons: [{	text: common_ok, click: function(){
									 $(this).dialog("close");
									displayLoadingImage(true);
									postData = 'action=removeMatrixSwapimg&product_id='+product_id+'&matrix_id='+ matrix_id;
									$.post(product_actions_url, postData,  function(response)
									{
										hideLoadingImage(false);
										data = eval( '(' +  response + ')');
										if (data.status == 'success') {
											$('#matrix_'+matrix_id).html(data.op_html);
											return false;
										}
									});
								}
							},{text: common_cancel,click:function(){	$(this).dialog("close");	}
						}]
				});
				return false;
			});

			$(document).on( "click", '#remSwapImg', function() {
				var matrix_id = $(this).attr('alt');
				$("#dialog_msg").html(remove_swap_msg);
				$("#dialog-delete-confirm").dialog({
					title: matrix_edit_head, modal: true,
					buttons: [{	text: common_ok, click: function(){
									$(this).dialog("close");
									displayLoadingImage(true);
									postData = 'action=remove_matrix_swapimg&product_id='+product_id+'&matrix_id='+ matrix_id;
									$.post(product_actions_url, postData,  function(response)
									{
										hideLoadingImage(false);
										data = eval( '(' +  response + ')');
										if (data.status == 'success') {
											var op_data = '<img class="clsItemResourceImage" src="'+data.data_arr.img_src+'" alt="'+data.data_arr.img_title+'" '+data.data_arr.disp_img+' />';
											$('#item_swap_image_id').attr('src', data.data_arr.img_src);
											$('#item_swap_image_id').attr('alt', data.data_arr.img_title);
											$('#item_swap_image_id').attr('width', 74);
											$('#item_swap_image_id').attr('height', 74);
											$('#remSwapImg').parent('span').hide();
											location.reload();
											return false;
										}
									});
								}
							},{text: common_cancel,click:function(){	$(this).dialog("close");	}
						}]
				});
				return false;
			});


			$(document).on( "click", '#update_matrix_det', function() {
				displayLoadingImage(true);
				postData = 'action=update_matrix_details&product_id='+ product_id +'&'+ $('#frmItemMatrixUpdate').serialize();
				$.post(product_actions_url, postData,  function(response)
				{
					hideLoadingImage(false);
					data = eval( '(' +  response + ')');
					if (data.status == 'success') {
						$('#matrixListBlock').html(data.op_html);
						return false;
					}
					else
					{
						showErrorDialog(data);
					}
				});
				return false;
			});

			$("#matrix_variationitem_list_submit").click(function()
			{
				multipleEditSelected($(this));
			});

			/* funtion called when action submitted from the matrix listing for edit */
			function multipleEditSelected(obj)
			{
				var selected_button = $(this).attr('id');
				var selected_action = $('#select_action').val();

				if(selected_action == '')
				{
					$("#dialog-confirm-content").html(matrix_select_action);
					$("#dialog-confirm").dialog({
                        title: matrix_edit_head,
                        modal: true,
                        buttons: [{
                            text: common_ok,
                            click: function() { $(this).dialog("close");     }
                        }]
                    });
				}
				else if($("#selFormMatrix input[type=checkbox]:checked").length == 0 ||
					($("#selFormMatrix input[type=checkbox]:checked").length == 1 && $("#checkall").attr('checked')))
				{
					$("#dialog-confirm-content").html(selection_none_err);
					$("#dialog-confirm").dialog({
                        title: matrix_edit_head,
                        modal: true,
                        buttons: [{
                            text: common_ok,
                            click: function()
                            {
                                 $(this).dialog("close");
                            }
                        }]
                    });
				}
				else
				{
					$("#dialog-confirm-content").html(matrix_edit_content);
					$("#dialog-confirm").dialog({
                        title: matrix_edit_head, modal: true,
                        buttons: [{	text: common_ok, click: function(){
										 $(this).dialog("close");
										$('#item_action').val(selected_action);
										showMatrixUpdate('multiple', selected_action);
									}
								},{text: common_cancel,click:function(){	$(this).dialog("close");	}
							}]
                    });
				}
				return false;
			}

		@endif // variation tab ends


        @if($d_arr['p'] == 'cancellation_policy')
            $(document).ready(function()
            {
                /*var selected_val = $('input[name=use_cancellation_policy]:radio:checked').val();
                if (selected_val == 'Yes')
                {
                    showCancellationFields(true);
                }
                else
                {
                    showCancellationFields(false);
                }*/

                $('input[name=use_cancellation_policy]').click(function(){
                    //default_cancel_available
                    var selected_val = $('input[name=use_cancellation_policy]:radio:checked').val();
                    if (selected_val=='Yes')
                    {
                        //default_cancel_available
                        if($('#default_cancel_available')!='')
                        {
                            var selected_def_val  = $('input[name=use_default_cancellation]:radio:checked').val();
                            if(selected_def_val == 'Yes')
                            {
                                showCancellationFields(false);
                                $('.js_defaultCancellationField').show();
                                $('.js_defaultCancellationViewField').show();

                            } else {
                                showCancellationFields(true);
                                $('.js_defaultCancellationViewField').hide();
                            }
                        }
                        else
                        {
                            showCancellationFields(true);
                            $('.js_defaultCancellationViewField').hide();
                        }
                    } else {
                        showCancellationFields(false);
                    }

                });

                $('input[name=use_default_cancellation]').click(function(){
                    var selected_val  = $('input[name=use_default_cancellation]:radio:checked').val();
                    if(selected_val == 'Yes')
                    {
                        showCancellationFields(false);
                        $('.js_defaultCancellationField').show();
                        $('.js_defaultCancellationViewField').show();
                    } else {
                        showCancellationFields(true);
                        $('.js_defaultCancellationViewField').hide();
                    }

                });


            });

            function showCancellationFields(flag) {
                if (flag) {
                    $('.js_clsCancellationFields').show();
                }
                else{
                    $('.js_clsCancellationFields').hide();
                }
            }

            var common_no_label = "{{ trans('common.cancel') }}" ;
            var common_yes_label = "{{ trans('common.yes') }}" ;
            var package_name = "{{ Config::get('generalConfig.site_name') }}" ;
            function removeShopCancellationPolicy(resource_id)
            {

                if($("#used_default").val() == '')
                {
                    //if($("#used_default").val() == ''){	}

                    $("#dialog-cancellation-policy-delete-confirm").dialog({
                        title: cfg_site_name,
                        modal: true,
                        buttons: [{
                            text: common_yes_label,
                            click: function()
                            {
                                displayLoadingImage(true);
                                postData = 'action=delete_cancellation_file&product_id='+product_id,
                                $.post(product_actions_url, postData,  function(response)
                                {
                                    data = eval( '(' +  response + ')');

                                    if(data.result == 'success')
                                    {
                                        window.location.reload(true);
                                        //updateProductStatus();
                                        //removeErrorDialog();
                                    }
                                    else
                                    {
                                        hideLoadingImage (false);

                                        showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
                                    }


                                });
                                $(this).dialog("close");
                            }
                        },
                        {
                            text: common_no_label,
                            click: function()
                            {
                                 $(this).dialog("close");
                            }
                        }]
                    });
                }
                else
                {
                    showCancellationFields(true);
                }
            }
        @endif

        @if($d_arr['p'] == 'status')
            function showHideNotesBlock() {
                if ($('#sel_NotesBlock').is(':visible')) {
                    $('#showNotes').html("{{  trans("product.products_show_product_notes") }} <i class='fa fa-chevron-down'></i>");
                    $('#sel_NotesBlock').hide();
                }
                else {
                    $('#showNotes').html("{{  trans("product.products_hide_product_notes") }} <i class='fa fa-chevron-up'></i>");
                    $('#sel_NotesBlock').show();
                }
            }
        @endif

        function confirmItemChange() {
            return true;
        }

        function updateProductStatus(){
            item_status = 'Draft';
            $('#item_status_text').html(item_status);
            $('#item_current_status').val(item_status);
        }

        /**
         *
         * @access public
         * @return void
         **/

        //var common_ok_label = "{{ trans('common.ok') }}" ;
        var cfg_site_name_1 = "{{ trans('productAdd.cancellation_policy_title') }} - " + cfg_site_name ;
        //var cancellation_policy_text ='';
        function CancellationPolicy(ele){
        var cancellation_policy_text = ele.id;
        //alert(cancellation_policy_text);
        bootbox.dialog({
            message: cancellation_policy_text,
            title: cfg_site_name_1,
          buttons: {
            main: {
              width: "800",
              height: "400",
              label: "Ok",
              className: "btn-primary",
              callback: function() {
                //window.location.reload(true);
              }
            }
          }
        });
        $('.modal-dialog').addClass('modal-container');
        }

        //Shipping tab : package details

        $(document).ready(function(){
        $('.fn_custom').hide();
        if($('.custom_package').is(":checked")){
                $('.fn_custom').show();
        }
        });
        $('.custom_package').click(function() {
            if($('.custom_package').is(":checked")){
                $('.fn_custom').show();
            }
            else {
                if ($('#checkbox_class_yes').val() == 'Yes') {
                    $('.fn_custom').show();
                } else {
                    $('.fn_custom').hide();
                }
                $('.fn_custom').hide();
            }
        });
            $("#addProductPackageDetailsfrm").validate({
                onfocusout: injectTrim($.validator.defaults.onfocusout),
                rules: {
                  first_qty: {
                        /*required: {
                            depends: function(element) {
                                return ($("#checkbox_class_yes").is(':checked')) ? true : false;
                            }
                        },*/
                        digits: true
                    },
                    additional_qty: {
                        /*required: {
                            depends: function(element) {
                                return ($("#checkbox_class_yes").is(':checked')) ? true : false;
                            }
                        },*/
                        digits: true
                    },
                    additional_weight: {
                        /*required: {
                            depends: function(element) {
                                return ($("#checkbox_class_yes").is(':checked')) ? true : false;
                            }
                        },*/
                        number: true,
                        decimallimit3: true
                    },
                    weight: {
                        required: true,
                        number: true,
                        decimallimit3: true,
                        min: 0.001,
                        max: 500

                    },
                    length: {
                        required: true,
                        digits: true,
                        min: 1,
                        max: 700

                    },
                    width: {
                        required: true,
                        digits: true,
                        min: 1,
                        max: 700

                    },
                    height: {
                        required: true,
                        digits: true,
                        min: 1,
                        max: 700

                    },
                    shipping_template: {
                        required: true,
                        min: 1
                    },
                    shipping_from_country: {
                        required: true,
                        min: 1
                    },
                    shipping_from_zip_code: {
                        required: true,
                    }
                },
                messages: {
                   weight: {
                        required: mes_required,
                        min: "Please fill in between the numbers 0.001 to 500",
                        max: "Please fill in between the numbers 0.001 to 500",
                    },
                    length: {
                        required: mes_required,
                        min: "Please fill in between the numbers 1 to 700",
                        max: "Please fill in between the numbers 1 to 700",
                    },
                    width: {
                        required: mes_required,
                        min: "Please fill in between the numbers 1 to 700",
                        max: "Please fill in between the numbers 1 to 700",
                    },
                    height: {
                        required: mes_required,
                        min: "Please fill in between the numbers 1 to 700",
                        max: "Please fill in between the numbers 1 to 700",
                    },
                    shipping_template: {
                        required: mes_required,
                        min: mes_required
                    },
                    shipping_from_country: {
                        required: mes_required,
                        min: mes_required
                    },
                    shipping_from_zip_code: {
                        required: mes_required,
                    }
                },
                submitHandler: function(form) {
                    displayLoadingImage(true);
                    form.submit();
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
        /*$("#len_hgt_wdt" ).change(function()
        {
            //var highest = -Infinity;
            var nums = [];
            $("input[type=text]").each( function() { nums.push( $(this).val() ); });
            var max = Math.max.apply(Math, nums);
            //alert(max);
            var text = '{{trans("product.length_width_height_size")}}';
            var length = parseFloat($('.length').val());
            var width = parseFloat($('.width').val());
            var height = parseFloat($('.height').val());
            var size_cal = length*width*height;
            var new_text = text.replace("3750", size_cal);
            var cal = length + ((width + height)*2);
            var cal1 = width + ((length + height) *2);
            var cal2 = height + ((width + height) *2);
            $('#length_width_height_msg').html(new_text);
            if(cal >= 2700 || cal1 >= 2700 || cal2 >= 2700)
            {
                var text1 =  '{{trans("product.length_width_height_msg")}}';
                $('#size_error_message').html(text1);
            }
        });

        $(".weight_id").change(function()
            {
            var text = '{{trans("product.weight_length_width_height_size")}}';
            var weight = $('#weight_val').val();
            var length = $('.length').val();
            var width = $('.width').val();
            var height = $('.height').val();
            var size_cal = length*width*height;
            var size_total = size_cal/5000;
            var new_text = text.replace("1.5", weight);
            var new_text1 = new_text.replace("0.75", size_total);
            if(weight >= size_total)
            {

            $('#size_error_message').html(new_text1);
            }
            else
            {

            $('#size_error_message').html('');
            }
        });*/
        $('#size_info_message').html('')
        $('#size_error_message').html('');
        $(".weight_package").change(function()
            {
            if(weight == '' || length == '' || width =='' || height =='')
            return false;

            var mesg = '{{trans("product.length_width_height_size")}}';
            var text = '{{trans("product.weight_length_width_height_size")}}';
            var weight = parseFloat($('#weight_val').val());
            var length = parseFloat($('.length').val());
            var width = parseFloat($('.width').val());
            var height = parseFloat($('.height').val());
            var size_cal = length*width*height;
            var cal = length + ((width + height)*2);
            var cal1 = width + ((length + height)*2);
            var cal2 = height + ((width + length)*2);
            var new_text = text.replace("VAR_WEIGHT", weight);
            var size_total = size_cal/5000;
            var new_text1 = new_text.replace("VAR_CAL_WEIGHT", size_total);
            var new_info = mesg.replace("VAR_SIZE_CAL", size_cal);
            $('#length_width_height_msg').html(new_info);
            $('#size_error_message').html('');
            if(2700 >= cal || 2700 >= cal1 || 2700 >= cal2)
            {
                if(weight >= size_total)
                {

                    $('#size_info_message').removeClass('note note-info').html('');
                }
                else
                {

                    $('#size_info_message').addClass('note note-info').html(new_text1);
                }

            }
            else
            {
                var text1 =  '{{trans("product.length_width_height_msg")}}';
                $('#size_error_message').html(text1);
            }
        });

    </script>
@stop