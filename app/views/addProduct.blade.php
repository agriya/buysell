@extends('base')
<?php
	$header_title = ($action == 'add')? 'add_title' : 'edit_title';
	$product_status = 'Draft';
?>
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: INFO BLOCK -->
			<div id="error_msg_div"></div>
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif

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
			<!-- END: INFO BLOCK -->

			<div class="clearfix product-mobstatus">
            	<!-- BEGIN: PAGE TITLE -->
				<div class="responsive-pull-none pull-left">
					<h1>
						{{trans("product.".$header_title)}}@if(isset($p_details['product_name']) && $p_details['product_name']!='') - "{{{$p_details['product_name']}}}" @endif
					</h1>
				</div>
                <!-- END: PAGE TITLE -->

                <!-- BEGIN: PRODUCT STATUS -->
				<ul class="pull-right list-inline list-inline-alt margin-top-5">
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
                    <li><strong>{{ trans("product.product_status_caption") }}:</strong> <span id="product_status_text" class="label {{ $lbl_class }}">{{ $status }}</span></li>
                    <li><a href="{{ URL::to('myproducts') }}" class="btn btn-xs blue-stripe default"><i class="fa fa-chevron-left"></i>{{ trans("common.back_to_list") }}</a></li>
				</ul>
                <!-- END: PRODUCT STATUS -->
			</div>

			<div class="tabbable-custom custom-navtabs custom-mobmenu">
				<div class="customview-navtab margin-bottom-20 margin-top-10">
					<button href="javascript:void(0);" class="btn-mobmenu btn green">
						@if(Input::get('pro-acc') == "")
							<i class="fa fa-chevron-circle-down pull-right margin-top-3"></i> Show Product Menu
						@else
							<i class="fa fa-chevron-circle-up pull-right margin-top-3"></i> Hide Product Menu
						@endif
					</button>

					<!-- BEGIN: TABS -->
					<ul class="nav nav-tabs custom-mobnav" id="pro-acc">                    	
						@foreach($d_arr['tab_list'] AS $tab_index => $value)
							<?php
								$activeClass = ($tab_index == $d_arr['p']) ? 'active' : '';
								$link = ($value) ? URL::to('product/add?p='.$tab_index.'&id='.$p_id) : 'javascript:void(0)';
							?>
							<li class="{{ $activeClass }}"><a href="{{ $link }}"><span>{{ isset($service_obj->p_tab_lang_arr[$tab_index]) ? $service_obj->p_tab_lang_arr[$tab_index] : ucwords($tab_index); }}</span></a></li>
						@endforeach
					</ul>
					<!-- END: TABS -->
				</div>

				<!-- BEGIN: PRODUCT ADD STEPS -->
				<div class="clearfix well">
					<!-- BEGIN: BASIC INFO -->
					@if($d_arr['p'] == 'basic')
					{{ Form::model($p_details, ['url' => $p_url, 'method' => 'post', 'id' => 'addProductfrm', 'class' => 'form-horizontal']) }}
						<h2 class="title-one">{{trans('product.basic_details')}}</h2>
						{{ Form::hidden('user_code', $admin_user_code, array('id' => 'user_code')) }}
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
                                    <label class="radio-inline margin-right-10">
                                        {{Form::radio('is_downloadable_product','Yes', Input::old('is_downloadable_product',$is_downloadable_product_yes), array('class' => '')) }}
                                        <label>{{trans("common.yes")}}</label>
                                    </label>
                                    <label class="radio-inline">
                                        {{Form::radio('is_downloadable_product','No', Input::old('is_downloadable_product',$is_downloadable_product_no) , array('class' => '')) }}
                                        <label>{{trans("common.no")}}</label>
                                    </label>
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
                                {{ Form::label('url_slug', trans("productAdd.url_slug"), array('class' => 'col-md-3 control-label required-icon')) }}
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
                                                {{ Form::select('product_category_id', $category_main_arr, $category_id, array('class' => 'form-control bs-select margin-bottom-10 valid', 'onchange' => "listSubCategories('product_category_id', '1')", 'id' => 'product_category_id')) }}
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
                                                        {{ Form::select($drop_name, $sub_arr, $selected_val, array('id' => $drop_name, 'class' => 'form-control bs-select margin-bottom-10 valid '.$next_sel_class, 'onchange' => "listSubCategories('".$drop_name."',". $cat_id.")")) }}
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
                                <div class="col-md-4">
                                    {{  Form::text('section_name', null, array('class' => 'form-control valid', 'id' => 'section_name')); }}
                                    <label class="error fn_sectionErr">{{{ $errors->first('section_name') }}}</label>
                                    <div class="margin-top-5">
                                        <a href="javascript: void(0);" class="fn_saveSection btn btn-xs green"><i class="fa fa-save"></i> {{ trans("product.save_section") }}</a>
                                        <a href="javascript: void(0);" class="fn_saveSectionCancel btn btn-xs default"><i class="fa fa-times"></i> {{ trans("common.cancel") }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{{ $errors->has('user_section_id') ? 'error' : '' }}}">
                                {{ Form::label('user_section_id', trans("product.section_name"), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-4">
                                    {{ Form::select('user_section_id', $section_arr, null, array('class' => 'form-control select2me input-medium valid')) }}
                                    <a href="javascript: void(0);" class="fn_addSection btn btn-info btn-xs margin-top-5">
                                    <i class="fa fa-plus-circle"></i> {{ trans("product.add_section") }}</a>
                                    <label class="error">{{{ $errors->first('user_section_id') }}}</label>
                                </div>
                            </div>

                            <div class="form-group {{{ $errors->has('demo_url') ? 'error' : '' }}}">
                                {{ Form::label('demo_url', trans("product.demo_url"), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-4">
                                    {{ Form::text('demo_url', null, array('class' => 'form-control valid')); }}
                                    <label class="error">{{{ $errors->first('demo_url') }}}</label>
                                </div>
                            </div>

                            <div class="form-group {{{ $errors->has('demo_details') ? 'error' : '' }}}">
                                {{ Form::label('demo_details', trans("product.demo_details"), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{ Form::textarea('demo_details', null, array('class' => 'form-control valid', 'rows' => '7')); }}
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

                            <div class="form-group">
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
                        {{ Form::close() }}
					@endif
					<!-- END: BASIC INFO -->

					<!-- BEGIN: PRICE INFO -->
					@if($d_arr['p'] == 'price')
						<?php
                            $p_details['product_discount_fromdate'] = ($p_details['product_discount_fromdate'] != '0000-00-00')? date('d/m/Y', strtotime($p_details['product_discount_fromdate'])):'';
                            $p_details['product_discount_todate'] = ($p_details['product_discount_todate'] != '0000-00-00')? date('d/m/Y', strtotime($p_details['product_discount_todate'])):'';
                            //To set default price value is empty instead of 0.00
                            $p_details['purchase_price'] = ($p_details['purchase_price'] == '0.00' || $p_details['purchase_price'] == '')? '0' : $p_details['purchase_price'];
                            $p_details['product_price'] = ($p_details['product_price'] == '0.00')? '' : $p_details['product_price'];
                            $p_details['product_discount_price'] = ($p_details['product_discount_price'] == '0.00')? '' : $p_details['product_discount_price'];
                            $price_currency = ($p_details['product_price_currency']!='')?$p_details['product_price_currency']:Config::get('generalConfig.site_default_currency');
                        ?>
                        {{ Form::model($p_details, ['url' => $p_url, 'method' => 'post', 'id' => 'addProductPricefrm', 'class' => 'form-horizontal']) }}
                            <h2 class="title-one">{{trans("product.price_details")}}</h2>
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
                                        <label class="radio-inline margin-right-10">
                                            {{Form::radio('is_free_product','Yes', Input::old('is_free_product',$is_free_product_yes), array('class' => '')) }}
                                            <label>{{trans('common.yes')}}</label>
                                        </label>
                                        <label class="radio-inline">
                                            {{Form::radio('is_free_product','No', Input::old('is_free_product',$is_free_product_no) , array('class' => '')) }}
                                            <label>{{trans('common.no')}}</label>
                                        </label>
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
                                    //echo "sdfsd<pre>"; print_r($price_details); echo "</pre>";exit;
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
                                                            <span class="input-group-addon"><label for="{{ $price_details['price_field'] }}">{{trans('product.price')}}</label></span>
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
                                                            <span class="input-group-addon"><label for="{{ $price_details['discount_field'] }}">{{trans('product.discounted_price')}}</label></span>
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
                            {{ Form::label('global_transaction_fee_used', trans("productAdd.global_transaction_fee_used"), array('class' => 'col-md-3 control-label')) }}
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
                            {{ Form::label('site_transaction_fee_type', trans("productAdd.site_transaction_fee_type"), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                            <div class="radio-list">
                            <label class="radio-inline">
                            {{ Form::radio('site_transaction_fee_type', 'Flat', ($p_details['site_transaction_fee_type'] == 'Flat') ? true : false, array('id' => 'site_transaction_fee_type_flat', 'name' => 'site_transaction_fee_type', 'onclick' => 'disableTransactionFee(this.value)', 'class' => 'ace')) }}
                            <label class="lbl">{{ Form::label('site_transaction_fee_type_flat', trans("productAdd.flat_fee_type"), array('class' => 'disp-block'))}}</label>
                            </label>
                            <label class="radio-inline">
                            {{ Form::radio('site_transaction_fee_type', 'Percentage', ($p_details['site_transaction_fee_type'] == 'Percentage') ? true : false, array('id' => 'site_transaction_fee_type_percentage', 'name' => 'site_transaction_fee_type', 'onclick' => 'disableTransactionFee(this.value)', 'class' => 'ace')) }}
                            <label class="lbl">{{ Form::label('site_transaction_fee_type_percentage', trans("productAdd.percentage_fee"), array('class' => 'disp-block'))}}</label>
                            </label>
                            <label class="radio-inline">
                            {{ Form::radio('site_transaction_fee_type', 'Mix', ($p_details['site_transaction_fee_type'] == 'Mix') ? true : false, array('id' => 'site_transaction_fee_type_mix', 'name' => 'site_transaction_fee_type', 'onclick' => 'disableTransactionFee(this.value)', 'class' => 'ace')) }}
                            <label class="lbl">{{ Form::label('site_transaction_fee_type_mix', trans("productAdd.mix_fee"), array('class' => 'disp-block'))}}</label>
                            </label>
                            </div>
                            <label class="error" for="site_transaction_fee_type" generated="true">{{$errors->first('site_transaction_fee_type')}}</label>
                            </div>
                            </div>

                            <div class="form-group fn_clsFeeFields {{{ $errors->has('site_transaction_fee') ? 'error' : '' }}}">
                            {{ Form::label('site_transaction_fee', trans("productAdd.site_transaction_fee"), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                            {{  Form::text('site_transaction_fee', null, array('class' => 'form-control valid')); }}
                            <label class="error">{{{ $errors->first('site_transaction_fee') }}}</label>
                            </div>
                            </div>

                            <div class="form-group fn_clsFeeFields {{{ $errors->has('site_transaction_fee_percent') ? 'error' : '' }}}">
                            {{ Form::label('site_transaction_fee_percent', trans("productAdd.site_transaction_fee_percent"), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                            {{  Form::text('site_transaction_fee_percent', null, array('class' => 'form-control valid')); }}
                            <label class="error">{{{ $errors->first('site_transaction_fee_percent') }}}</label>
                            </div>
                            </div>-->

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
                                        <p class="text-muted margin-top-5"><small>{{ Lang::get('variations::variations.giftwrap_type_note_msg') }}</small></p>
                                        <label class="error">{{{ $errors->first('giftwrap_type') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group fn_giftwrap_field {{{ $errors->has('use_variation') ? 'error' : '' }}}">
                                    {{ Form::label('giftwrap_pricing', trans("product.giftwrap_price"), array('class' => 'col-md-3 control-label')) }}
                                    <div class="col-md-4">
                                        <label class="radio">
                                            {{  Form::text('giftwrap_pricing', null, array('class' => 'giftwrapprice form-control')); }}
                                        </label>
                                        <label class="error" style="display: none" id="err_giftwrap_pricing">{{{ $errors->first('giftwrap_pricing') }}}</label>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group clearfix">
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
                        {{ Form::close() }}
					@endif
					<!-- END: PRICE INFO -->

					<!-- BEGIN: STOCKS FORM -->
					@if($d_arr['p'] == 'stocks' && $p_details['is_downloadable_product'] == 'No')
                        {{ Form::model($stock_details, [
                        'url' => $p_url,
                        'method' => 'post',
                        'id' => 'addProductStocksfrm', 'class' => 'form-horizontal'
                        ]) }}
                            <h2 class="title-one">{{ trans("product.enter_stock_details") }}</h2>

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
                                    <p class="text-muted margin-top-5"><strong>Note: </strong>Enter each serial number in separated line. Serial numbers count must be equal to the entered Quantity</p>
                                    <label class="error">{{{ $errors->first('serial_numbers_china') }}}</label>
                                </div>
                            </div>

                            <div class="form-group fn_pak_div {{{ $errors->has('quantity_pak') ? 'error' : '' }}}" @if($stock_country_id_pak == 0) style="display:none;" @endif>
                                {{ Form::label('quantity_pak', 'Quantity in pakistan', array('class' => 'col-md-3 control-label required-icon')) }}
                                <div class="col-md-4">
                                    {{  Form::text('quantity_pak', null, array('class' => 'form-control valid')); }}
                                    <label class="error">{{{ $errors->first('quantity_pak') }}}</label>
                                </div>
                            </div>

                            <div class="form-group fn_pak_div {{{ $errors->has('serial_numbers_pak') ? 'error' : '' }}}" @if($stock_country_id_pak == 0) style="display:none;" @endif>
                                {{ Form::label('serial_numbers_pak', 'Serial numbers in pakistan', array('class' => 'col-md-3 control-label required-icon')) }}
                                <div class="col-md-6 custom-textarea">
                                    {{  Form::textarea('serial_numbers_pak', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                    <p class="text-muted margin-top-5"><strong>Note: </strong>Enter each serial number in separated line. Serial numbers count must be equal to the entered Quantity</p>
                                    <label class="error">{{{ $errors->first('serial_numbers_pak') }}}</label>
                                </div>
                            </div>-->

                            <div class="form-group {{{ $errors->has('quantity') ? 'error' : '' }}}">
                                {{ Form::label('quantity', trans("product.quantities"), array('class' => 'col-md-3 control-label required-icon')) }}
                                <div class="col-md-4">
                                    {{  Form::text('quantity', null, array('class' => 'form-control valid')); }}
                                    @if($d_arr['allow_variation'])
                                        <p class="text-muted margin-top-5"><small>({{ Lang::get('variations::variations.stock_variation_note') }})</small></p>
                                    @endif
                                    <label class="error">{{{ $errors->first('quantity') }}}</label>
                                </div>
                            </div>

                            <div class="form-group {{{ $errors->has('serial_numbers') ? 'error' : '' }}}">
                                {{ Form::label('serial_numbers', trans("product.serial_numbers"), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6 custom-textarea">
                                    {{  Form::textarea('serial_numbers', null, array('class' => 'form-control valid', 'rows' => '7')); }}
                                    <p class="text-muted margin-top-5"><small><strong>{{ trans("common.note") }}: </strong>{{ trans("product.serial_number_notes") }}</small></p>
                                    <label class="error">{{{ $errors->first('serial_numbers') }}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                <div class="col-md-offset-3 col-md-8">
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
					@endif
					<!-- END: STOCKS FORM -->

					<!-- BEGIN: META INFO -->
					@if($d_arr['p'] == 'meta')
                        {{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductMetafrm', 'class' => 'form-horizontal']) }}
                            <h2 class="title-one">{{trans('product.meta_details')}}</h2>
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

                            <div class="form-group">
                                {{ Form::hidden('id', $p_id, array('id' => 'id')) }}
                                {{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
                                {{ Form::hidden('product_user_id', $p_details['product_user_id'], array('id' => 'product_user_id')) }}
                                <div class="col-md-offset-3 col-md-8">
                                    <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                        <i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
					@endif
					<!-- END: META INFO -->

					<!-- BEGIN: SHIPPING INFO -->
					@if($d_arr['p'] == 'shipping')
					<?php $price_currency = ($p_details['product_price_currency']!='')?$p_details['product_price_currency']:Config::get('generalConfig.site_default_currency');?>
					{{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductPackageDetailsfrm', 'class' => 'form-horizontal']) }}
						<!-- BEGIN: PACKAGE DETAILS -->
						<h2 class="title-one">{{ trans("product.package_details") }} <small class="text-muted"><strong>{{ trans("product.weight_info") }}</strong></small></h2>
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
						<div class="form-group margin-bottom-0">
							<div class="col-md-offset-3 col-md-9">
								<label class="checkbox-inline margin-bottom-10">
									<input name="custom" {{ $custom_package }} type="checkbox" value="Yes" id="checkbox_class_yes" class="custom_package">
									{{ Form::label('checkbox_class_yes', trans("product.custom")) }}
								</label>
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
						</div>
						<div class="form-group clearfix {{{ $errors->has('size') ? 'error' : '' }}}">
							{{ Form::label('size', trans("product.size"), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-9 after-packing">
								<ul class="clearfix list-unstyled packing-input">
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
									<label id="length_width_height_msg" class="info text-muted margin-top-5 selector-def">
										<small class="text-muted"><strong>{{ str_replace('VAR_SIZE_CAL', '3750', trans("product.length_width_height_size")) }}</strong></small>
									</label>
									<label for="l_w_h" generated="true" class="error"></label>
								</ul>
								<label class="error" id="size_error_message">{{{ $errors->first('length_width_height') }}}</label>
								<label id="size_info_message">
									{{ str_replace(array('VAR_WEIGHT', 'VAR_CAL_WEIGHT'), array('1.5', '0.75'), trans("product.weight_length_width_height_size"))  }}
								</label>
							</div>
						</div>
						<!-- END: PACKAGE DETAILS -->

						<!-- BEGIN: SHIPPING DETAILS -->
						<h2 class="title-one">{{trans('product.shipping_details')}}</h2>
						<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_template') ? 'error' : '' }}} margin-bottom-20">
							{{ Form::label('shipping_template', trans('product.shipping_template'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::select('shipping_template', $d_arr['ship_templates_list'], Input::old('shipping_template', $d_arr['ship_template_id']), array('class' => 'form-control select2me input-large fnShippingCostEstimate margin-bottom-5', 'id' => 'shipping_template')); }}
								<a target="_blank" href="{{ URL::action('ShippingTemplateController@getAdd')}}" class="btn btn-info btn-xs">
								<i class="fa fa-plus"></i> {{ trans("shippingTemplates.add_shipping_template") }}</a>
								<label class="error">{{{ $errors->first('shipping_template') }}}</label>
							</div>
						</div>

						<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_from_country') ? 'error' : '' }}}">
							{{ Form::label('shipping_from_country', trans('product.shipping_from_country'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::select('shipping_from_country', array('' => Lang::get('common.please_select')) + $d_arr['countries_list'], Input::old('shipping_from_country', $d_arr['shipping_from_country']), array('class' => 'form-control select2me input-large fnShippingCostEstimate', 'id' => 'shipping_from_country')); }}
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

						<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_country') ? 'error' : '' }}} margin-bottom-40">
							{{ Form::label('shipping_country', trans('product.reference_shipping_cost'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::select('shipping_country', $d_arr['countries_list'], Input::old('shipping_country', $d_arr['ship_country_id']), array('class' => 'form-control select2me input-large fnShippingCostEstimate margin-bottom-5', 'id' => 'shipping_country')); }}
								<a id="calc_cost" href="javascript:;" class="text-info"><i class="fa fa-refresh"></i> {{ trans("product.refresh") }}</a>
								<label class="error">{{{ $errors->first('shipping_country') }}}</label>
							</div>
						</div>
						<!-- END: SHIPPING DETAILS -->

						<!--  BEGIN: REFERENCE SHIPPING COST DETAILS -->
						<div id="reference_shipping_cost_holder">
							@include('referenceShippingCost')
						</div>
						<!--  END: REFERENCE SHIPPING COST DETAILS -->

						<div class="form-group">
							{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
							{{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
							<div class="col-md-6">
								<button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
								<i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}</button>
							</div>
						</div>
						{{ Form::close() }}
					@endif
					<!-- END: SHIPPING INFO -->

					<!-- BEGIN: TAX INFO -->
					@if($d_arr['p'] == 'tax')
						<?php $price_currency = ($p_details['product_price_currency']!='')?$p_details['product_price_currency']:Config::get('generalConfig.site_default_currency');?>
						{{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductTaxfrm', 'class' => 'form-horizontal']) }}
							<div class="margin-bottom-40">
								<h2 class="title-one">{{trans('product.add_tax')}}</h2>
								<div class="form-group fn_clsPriceFields {{{ $errors->has('country_id') ? 'error' : '' }}}">
									{{ Form::label('taxation_id', trans("product.tax"), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-4">
										{{  Form::select('taxation_id', $d_arr['taxations_list'], Input::old('taxation_id', ''), array('class' => 'form-control select2me input-medium margin-bottom-5', 'id' => 'js-taxation-id')); }}
										<a target="_blank" href="{{ URL::action('TaxationsController@getAddTaxation')}}" class="label label-primary">
										<i class="fa fa-plus"></i>  {{ Lang::get('taxation.add_taxation')  }} </a>
										<label class="error">{{{ $errors->first('taxation_id') }}}</label>
									</div>
								</div>

								<div class="form-group fn_clsPriceFields {{{ $errors->has('tax_fee') ? 'error' : '' }}}">
									{{ Form::label('tax_fee', trans("product.tax_fee"), array('class' => 'col-md-3 control-label required-icon')) }}
									<div class="col-md-4">
										<div class="clearfix row">
											<div class="col-md-4 col-sm-3 margin-bottom-5">
												<div class="input-group">
													{{ Form::text('tax_fee', null, array('class' => 'form-control', 'id' => 'js-tax_fee')); }}
												</div>
											</div>
											<div class="col-md-6">
												<div class="input-group">
													{{ Form::select('fee_type', array('percentage' => '%', 'flat' => 'Flat (In '.$price_currency.' )'),Input::old('fee_type', ''), array('class' => 'form-control bs-select', 'id' => 'js-fee_type')); }}
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
										<button name="edit_product" id="add_tax" value="add_tax" type="submit" class="btn btn-info btn-sm">
										<i class="fa fa-plus-circle"></i> {{ trans("product.add_tax") }}</button>
									</div>
								</div>
							</div>

							<h2 class="title-one">{{ trans("product.tax_fee_list") }}</h2>
							<div class="table-responsive margin-bottom-30">
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
														<div id="taxfeetd_{{$taxation->id}}">
															<?php  $taxation->tax_fee = round($taxation->tax_fee,2); ?>
															@if($taxation->fee_type == 'percentage')
																<strong>{{$taxation->tax_fee}}%</strong>
															@else
																<span class="text-muted">{{$p_details['product_price_currency']}}</span> <strong>{{$taxation->tax_fee}}</strong>
															@endif
														</div>
													</td>
													<td class="action-btn">
														<a onclick="javascript:removeItemTaxRow({{ $taxation->id }});" href="javascript: void(0);" title="{{trans('common.delete')}}" class="btn btn-xs red">
														<i class="fa fa-trash-o"></i></a>
														<a id="tax_fee_edit_link_{{$taxation->id}}" onclick="javascript:editItemTaxRow({{ $taxation->id }}, {{$taxation->tax_fee}}, '{{$taxation->fee_type}}');" href="javascript: void(0);" title="{{trans('common.edit')}}" class="btn btn-xs blue"><i class="fa fa-edit"></i></a>
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

						{{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductTaxListfrm', 'class' => 'form-horizontal']) }}
							<div class="form-group">
								{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
								{{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
								<div class="col-md-offset-3 col-md-8">
									<button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
									<i class="fa fa-save"></i> {{ trans("product.save_and_proceed") }}</button>
								</div>
							</div>
						{{ Form::close() }}
					@endif
					<!-- END: TAX INFO -->

					<!-- BEGIN: ATTRIBUTE INFO -->
					@if($d_arr['p'] == 'attribute')
						{{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductAttributefrm', 'class' => 'form-horizontal']) }}
							<h2 class="title-one">{{trans('product.attribute_details')}}</h2>
							<div class="addattribute-chkradio">
								@if(count($d_arr['attr_arr']) > 0)
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
                                                            {{ Form::label($id_name, $val, array('class' => '')) }}
                                                        </label>
                                                    @endforeach
												@elseif($input_type == 'check')
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
															{{ Form::label($id_name, $val, array('class' => '')) }}
														</label>
													@endforeach
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
									<div class="form-group">
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
						{{ Form::close() }}
					@endif
					<!-- END: ATTRIBUTE INFO -->

					<!-- BEGIN: PREVIEW FILES INFO -->
					@if($d_arr['p'] == 'preview_files')
						{{ Form::model($d_arr['p_img_arr'], ['url' => $p_url,'method' => 'post','id' => 'addProductPreviewFilesfrm', 'class' => 'form-horizontal', 'files' => true,]) }}
                        	<!-- BEGIN: PREVIEW DETAILS -->
                            <div class="margin-bottom-30">
								<h2 class="title-one">{{trans('product.preview_details')}}</h2>
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
                                        <p @if($p_thumb_img['no_image']) style="display:none;"@endif id="link_remove_thumb_image" class="margin-top-10">
                                            <a onclick="javascript:removeProductThumbImage({{ $p_id }});" class="btn btn-xs red" href="javascript: void(0);">
                                            <i class="fa fa-times"></i> {{ trans("product.remove_image") }}</a>
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
                                            <div class="col-md-3 custom-pad1 margin-top-6">
                                                <span id="item_thumb_edit_span">
                                                    <a onclick="javascript:editItemImageTitle({{ $p_id }}, 'thumb');" href="javascript: void(0);" class="btn blue btn-xs"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
                                                </span>
                                                <span style="display:none;" id="item_thumb_image_save_span">
                                                    <a onclick="javascript:saveProductImageTitle({{ $p_id }}, 'thumb', false);" href="javascript: void(0);" class="btn btn-xs green"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a>
                                                </span>
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('thumbnail_title') }}}</label>
                                    </div>
                                </div>

                                <div class="form-group {{{ $errors->has('upload_thumb') ? 'error' : '' }}}">
                                    {{-- Form::label('upload_thumb', trans("product.upload_thumb"), array('class' => 'col-md-3 control-label')) --}}
                                    <div class="col-md-3">&nbsp;</div>
                                    <div class="col-md-5">
                                        <div class="btn purple-plum" id="upload_thumb"> <i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_thumb_image") }}</div>
                                        <div class="margin-top-5">
                                            <i class="fa fa-question-circle pull-left"></i>
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
                                        <p @if($p_default_img['no_image']) style="display:none;"@endif id="link_remove_default_image" class="margin-top-10">
                                            <a onclick="javascript:removeItemDefaultImage({{ $p_id }});" href="javascript: void(0);" class="btn btn-xs red">
                                            <i class="fa fa-times"></i> {{ trans("product.remove_image") }}</a>
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
                                            <div class="col-md-3 custom-pad1 margin-top-6">
                                                <span id="item_default_edit_span">
                                                    <a onclick="javascript:editItemImageTitle({{ $p_id }}, 'default');" href="javascript: void(0);" class="btn blue btn-xs"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a>
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
                                    <div class="col-md-5">
                                        <div class="btn purple-plum" id="upload_default">
                                            <i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_default_image") }}
                                        </div>
                                        <div class="margin-top-5">
                                            <i class="fa fa-question-circle pull-left"></i>
                                            <div class="pull-left text-muted">
                                                {{ str_replace("\n",'<br />',sprintf(trans("product.products_default_allowed_image_formats_size"), implode(', ', Config::get("webshoppack.default_format_arr")) , Config::get("webshoppack.default_max_size"), trans("product.file_size_in_MB"))) }}
                                            </div>
                                        </div>
                                        <label class="error">{{{ $errors->first('upload_default') }}}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- END: PREVIEW DETAILS -->

                            <!-- BEGIN: PREVIEW FILES -->
                            <div class="margin-bottom-30">
                                <h2 class="title-one">{{ trans("product.products_uploaded_files") }}</h2>
                                <div class="note note-info">
                                    {{ str_replace("\n",'<br />',sprintf(trans("product.products_preview_allowed_file_formats_size"), implode(', ', Config::get("webshoppack.preview_format_arr")) , Config::get("webshoppack.preview_max_size"), trans("product.file_size_in_MB"), Config::get("webshoppack.preview_max"))) }}
                                </div>

                                <div class="table-responsive margin-bottom-20">
                                    <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                                                <th width="150">{{ trans("product.file_title") }}</th>
                                                <th>{{ trans("product.title") }}</th>
                                                <th>{{ trans("common.action") }}</th>
                                            </tr>
                                        </thead>

                                        <tbody class="fn_formBuilderListBody preview-image" id="preview_tbl_body">
                                            <?php
                                                $resources_arr = $d_arr['resources_arr'];
                                            ?>
                                            @foreach($resources_arr AS $inc => $value)
                                                <tr id="itemResourceRow_{{ $resources_arr[$inc]['resource_id'] }}" class="formBuilderRow">
                                                    <td>
                                                        @if($resources_arr[$inc]['resource_type'] == 'Image')
                                                            <a href="javascript:void(0)"> <img src="{{ URL::asset(Config::get("webshoppack.photos_folder")) .'/'. $resources_arr[$inc]['filename_thumb'] }}" alt="{{ $resources_arr[$inc]['title'] }}"/></a>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input class="form-control" type="text" name="item_resource_image_{{ $resources_arr[$inc]['resource_id'] }}" id="resource_title_field_{{ $resources_arr[$inc]['resource_id'] }}" value="{{ $resources_arr[$inc]['title'] }}" onkeypress="javascript:editItemResourceTitle({{ $resources_arr[$inc]['resource_id']  }});"  />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="margin-top-5">
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
                                    <div class="margin-bottom-20 pull-left margin-right-10 {{{ $errors->has('upload_default') ? 'error' : '' }}}">
                                        <!-- UPLOAD BUTTON, USE ANY ID YOU WISH-->
                                        <div class="btn purple-plum fn_ItemUploadResourceFileButton pull-left" id="upload">
                                            <i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_item_file") }}
                                        </div>
                                        <span id="status"></span>
                                        <ul id="files"></ul>
                                    </div>
                                </div>
                            </div>
                            <!-- END: PREVIEW FILES -->

                            <!-- BEGIN: SWAP IMAGES -->
							@if(isset($d_arr['allow_swap_image']) && $d_arr['allow_swap_image'] > 0)
                                <h2 class="title-one">{{ trans("product.swap_images") }}:</h2>
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
                                                        <a href="javascript:void(0)"> <img src="{{ URL::asset(Config::get("variations::variations.swap_img_folder")) .'/'. $swap_imges_arr[$inc]['filename_thumb'] }}" alt="{{ $swap_imges_arr[$inc]['title'] }}"/></a>
                                                    </td>

                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input class="form-control" type="text" name="item_swap_image_{{ $swap_imges_arr[$inc]['resource_id'] }}" id="swap_image_title_field_{{ $swap_imges_arr[$inc]['resource_id'] }}" value="{{ $swap_imges_arr[$inc]['title'] }}" onkeypress="javascript:editItemSwapImageTitle({{ $swap_imges_arr[$inc]['resource_id']  }});"  />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="margin-top-5">
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
                                        <div class="btn purple-plum fn_ItemUploadSwapImageFileButton" id="upload_swap_image">
                                            <i class="fa fa-cloud-upload margin-right-5 margin-right-5"></i> {{ trans("product.upload_swap_image") }}
                                        </div>
                                        <span id="swap_image_status"></span>
                                        <ul id="swap_image_files"></ul>
                                    </div>
                                </div>
                            @endif
                            <!-- END: SWAP IMAGES -->

							<div class="pull-right responsive-pull-none">
								{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
								{{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
								<button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
									<i class="fa fa-arrow-right"></i> {{ trans("product.products_proceed_next") }}
								</button>
							</div>
						{{ Form::close() }}
					@endif
					<!-- END: PREVIEW FILES INFO -->

                    <!-- BEGIN: VARIATIONS TAB -->
					@if($d_arr['allow_variation'] && $d_arr['p'] == 'variations')
                    	<div class="portlet bg-form clearfix margin-bottom-10">
                        	<!-- BEGIN: INCLUDE - MANAGEITEMS VARIATION BLOCK -->
                            <div id="var_grp_block" @if($d_arr['show_matrix_block']) style="display:none;" @endif>
                                @include('variations::manageItemsVariationsBlock')
                            </div>
                            <!-- END: INCLUDE - MANAGEITEMS VARIATION BLOCK -->

                            <ul class="list-unstyled margin-0">
                                <li class="fonts18 margin-bottom-10">
                                    {{ Lang::get('variations::variations.default_item_price_lbl') }}:
                                    {{CUtil::convertAmountToCurrency($d_arr['price_details'][0]['discount'], Config::get('generalConfig.site_default_currency'), '', true)}}
                                </li>

                                <li class="fonts18">
                                    @if(isset($p_details['giftwrap_pricing'])  && isset($p_details['accept_giftwrap']) && $p_details['accept_giftwrap'] > 0)
										{{ Lang::get('variations::variations.default_giftwrap_price_lbl') }}:
                                        {{CUtil::convertAmountToCurrency($p_details['giftwrap_pricing'], Config::get('generalConfig.site_default_currency'), '', true)}}
                                    @endif
                                </li>
                            </ul>
                        </div>

                        <!-- BEGIN: INCLUDE - MANAGEITEMS ATTRIBUTE MATRIX -->
                        <div id="var_matrix_block">
                            @include('variations::manageItemsAttribMatrix')
                        </div>
                        <!-- END: INCLUDE - MANAGEITEMS ATTRIBUTE MATRIX -->

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
					<!-- END: VARIATIONS TAB -->

					<!-- BEGIN: DOWNLOAD FILES INFO -->
					@if($d_arr['p'] == 'download_files')
						<?php
							$resources_arr = $d_arr['resources_arr'];
						?>
						{{ Form::open(array('id'=>'addProductDownloadFilesfrm', 'method'=>'post','class' => 'form-horizontal', 'url' => $p_url )) }}
							<h2 class="title-one">{{trans('product.download_files')}}</h2>
							<div class="note note-info clearfix">
								@if(Config::get('webshoppack.download_files_is_mandatory')) <span class="text-danger pull-left">*</span> @endif
								<div class="pull-left">{{ str_replace("\n",'<br />',sprintf(trans("product.products_download_file_upload_description"), implode(',', Config::get("webshoppack.download_format_arr")) , Config::get("webshoppack.download_max_size"), trans("product.file_size_in_MB"))) }}</div>
							</div>

							<div class="table-responsive margin-bottom-20">
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
													$download_url = URL::action('ProductAddController@getProductActions'). '?action=download_file&product_id=' . $p_id;
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
															<div class="margin-top-5">
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

							<div class="pull-right">
								{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
								{{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
								<button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
								<i class="fa fa-arrow-right"></i> {{ trans("product.products_proceed_next") }}</button>
							</div>

							<div class="pull-left {{{ $errors->has('upload_file') ? 'error' : '' }}}">
								<div class="btn purple-plum clsItemUploadResourceFileButton" id="upload" @if(count($d_arr['resources_arr']) > 0) style="display:none" @endif>
								<i class="fa fa-cloud-upload margin-right-5"></i> {{ trans("product.products_upload_item_file") }}</div>
								<label class="error">{{{ $errors->first('upload_file') }}}</label>
								<span id="status"></span>
								<ul id="files"></ul>
							</div>
						{{ Form::close() }}
					@endif
					<!-- END: DOWNLOAD FILES INFO -->

					<!-- BEGIN: CANCELLATION POLICY -->
					@if($d_arr['p'] == 'cancellation_policy')
						{{ Form::model($p_details, ['url' => $p_url,'method' => 'post','id' => 'addProductCancellationPolicyfrm', 'class' => 'form-horizontal', 'files' => true]) }}
							<h2 class="title-one">{{trans('product.cancellation_policy')}}</h2>
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

									<label class="radio-inline margin-right-10">
										{{Form::radio('use_cancellation_policy','Yes', Input::old('use_cancellation_policy',$is_use_cancellation_yes)) }}
										<label>{{trans('common.yes')}}</label>
									</label>
									<label class="radio-inline">
										{{Form::radio('use_cancellation_policy','No', Input::old('use_cancellation_policy',$is_use_cancellation_no)) }}
										<label>{{trans('common.no')}}</label>
									</label>
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

										<label class="radio-inline margin-right-10">
											{{Form::radio('use_default_cancellation','Yes', Input::old('use_default_cancellation',$use_default_cancellation_yes)) }}
											<label>{{trans('common.yes')}}</label>
										</label>
										<label class="radio-inline">
											{{Form::radio('use_default_cancellation','No', Input::old('use_default_cancellation',$use_default_cancellation_no)) }}
											<label>{{trans('common.no')}}</label>
										</label>
										<label class="error">{{{ $errors->first('use_cancellation_policy') }}}</label>
									</div>
								</div>
								<div class="form-group js_clsCancellationFields js_defaultCancellationViewField" @if($is_use_cancellation_no || $use_default_cancellation_no) style="display: none;" @endif>
									@if(count($cancellation_policy_value) > 0 && $cancellation_policy_value['cancellation_policy_filename'] != '')
										{{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
										<div class="col-md-4">
											<div id="uploadedFilesList" class="clearfix margin-top-10">
												<div id="shopCancellationPolicyRow_{{ $cancellation_policy_value['id'] }}">
													<?php $filePath = $cancellation_policy_value['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
													$filename = $cancellation_policy_value['cancellation_policy_filename'].'.'.$cancellation_policy_value['cancellation_policy_filetype'];?>
													<a target="_blank" href="{{$filePath.'/'.$filename}}"><i class="fa fa-share"></i> {{trans('product.click_to_view_cancellation')}}</a>
												</div>
											</div>
										</div>
									@elseif(count($cancellation_policy_value) > 0 && $cancellation_policy_value['cancellation_policy_text'] == '')
										{{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
										<div class="col-md-4">
											<div id="uploadedFilesList" class="clearfix margin-top-10">
												<div id="shopCancellationPolicyRow_{{ $cancellation_policy_value['id'] }}">
													<?php $filePath = $cancellation_policy_value['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
													$filename = $cancellation_policy_value['cancellation_policy_filename'].'.'.$cancellation_policy_value['cancellation_policy_filetype'];?>
													<a target="_blank" href="{{URL::action('ShopController@getIndex')}}"><i class="fa fa-share"></i>{{ trans('product.click_to_add_cancellation') }}</a>
												</div>
											</div>
										</div>
									@else

									{{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
										<div class="col-md-4">
											<div id="uploadedFilesList" class="clearfix margin-top-10">
												<div id="shopCancellationPolicyRow_{{ $cancellation_policy_value['id'] }}">
												<?php $cancellation_policy_text_value = nl2br($cancellation_policy_value['cancellation_policy_text']); ?>
													<a id="{{ htmlspecialchars($cancellation_policy_text_value) }}" onclick="CancellationPolicy(this);">
													<i class="fa fa-share"></i> {{ trans('product.click_to_view_cancellation') }}</a>
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
									{{ Form::label('shop_cancellation_policy_file', trans("product.cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
									<div class="col-md-5">
                                        <div class="custom-upldimg">
                                            {{ Form::file('shop_cancellation_policy_file', array('title' => trans("product.cancellation_policy_file"), 'class' => 'filestyle', 'id' => 'cancellation_policy_file_id', 'data-buttonText' => Lang::get('common.choose_file'))) }}
                                        </div>
										<label class="error" id="file_type_size">{{ $errors->first('shop_cancellation_policy_file') }}</label>
										<div class="margin-top-5">
											<i class="fa fa-question-circle pull-left"></i>
											<div class="pull-left text-muted">
												<small>
													{{ str_replace("VAR_FILE_FORMAT",  Config::get('webshoppack.shop_cancellation_policy_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}<br />
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
											<div id="uploadedFilesList" class="clearfix margin-top-10">
												<div id="shopCancellationPolicyRow_{{ $p_details['id'] }}">
													<?php $filePath = $p_details['cancellation_policy_server_url'];
														  $filename = $p_details['cancellation_policy_filename'].'.'.$p_details['cancellation_policy_filetype'];?>
													<p class="margin-bottom-5"><a target="_blank" href="{{$filePath.'/'.$filename}}"><i class="fa fa-share"></i> Click here to view cancellation policy</a></p>
													<a title="{{trans('common.delete')}}" href="javascript: void(0);" onclick="javascript:removeProductCancellationPolicy({{ $p_details['id'] }});" class="btn btn-xs red" title="Remove"><i class="fa fa-times"></i> Remove</a>
												</div>
											</div>
											<label class="error">{{ $errors->first('shop_cancellation_policy_file') }}</label>
										</div>
									</div>
								@endif
							@endif
							<!--- END: ADD MEMBER BASIC DETAILS --->

							<div class="form-group">
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
						{{ Form::close() }}
					@endif
					<!-- END: CANCELLATION POLICY -->

					<!-- BEGIN: APPROVAL STATUS FORM -->
					@if($d_arr['p'] == 'status')
						<fieldset>
							{{ Form::open(array('id'=>'addProductPublishfrm', 'method'=>'post','class' => 'form-horizontal', 'url' => $p_url )) }}
								<h2 class="title-one">{{trans('product.approval_status_tab')}}</h2>
								<?php
									$need_to_pay = false;
									$user_account_balance = $d_arr['user_account_balance']['amount'];
									$amount_to_pay = 0.00;
								?>
								@if(Config::get('products.paid_listing'))
									@if($p_details['date_expires'] == '0000-00-00 00:00:00')
										<p>{{trans('product.listing_need_to_activated')}}</p>
										<p>{{trans('product.number_of_days_listing')}}:
										<strong>{{(Config::get('products.product_listing_days')=='-1')?trans('product.life_time'):Config::get('products.product_listing_days')}}</strong></p>
										<p>{{trans('product.listing_fee')}}: <strong>{{Config::get('generalConfig.site_default_currency')}} {{Config::get('products.product_listing_fee')}}</strong></p>
										<?php
											$need_to_pay = true;
											$amount_to_pay = Config::get('products.product_listing_fee');
										?>
									@elseif($p_details['date_expires']!='0000-00-00 00:00:00' && $p_details['date_expires']!='9999-12-31 00:00:00' && (strtotime($p_details['date_expires']) < strtotime(date('Y-m-d'))))
										<p class="note note-info">{{trans('product.listing_expired')}}</p>
										<?php
											$need_to_pay = true;
											$amount_to_pay = Config::get('products.product_listing_fee');
										?>
									@elseif($p_details['date_expires']!='0000-00-00 00:00:00' && ($p_details['date_expires']=='9999-12-31 00:00:00' || (strtotime($p_details['date_expires']) >= strtotime(date('Y-m-d')) ) ) )
										@if($p_details['date_expires'] != '9999-12-31 00:00:00')
											<p class="note note-info">{{trans('product.listing_going_to_expire')}} {{date('Y-m-d', strtotime($p_details['date_expires'])) }}</p>
										@else
											<p class="note note-info">{{trans('product.listing_never_expire')}}</p>
										@endif
									@endif
								@endif

								@if(CUtil::chkIsAllowedModule('featuredproducts'))
									@if($need_to_pay)
										@if($p_details['is_featured_product'] == 'No' && (strtotime($p_details['date_expires']) < strtotime(date('Y-m-d'))))
											<?php
                                                $featured_product_service = new FeaturedProductsService();
                                                $plans_arr = $featured_product_service->getFeaturedProductsPlans();
                                                $d_arr['plans_arr'] = array('' => trans('common.select')) + $plans_arr;
                                                $need_to_pay = true;
                                            ?>

                                            @if(isset($plans_arr) && !empty($plans_arr))
	                                            <div class="margin-bottom-10">
	                                            {{ Form::label('plan', trans('featuredproducts::featuredproducts.set_as_featured'), array('class' => 'pull-left margin-right-10 control-label ')) }}

	                                            {{ Form::select('plan', $d_arr['plans_arr'], null, array('class' => 'form-control select2me input-medium valid', 'onchange' => 'checkAccBalance()', 'id' => 'plan')) }}
	                                            </div>
                                            @endif
					                    @endif
					                @endif
								@endif

								@if($need_to_pay)
									{{Form::hidden('amount_to_pay', $amount_to_pay, array('id' => 'amount_to_pay'))}}
									{{Form::hidden("user_account_balance", $user_account_balance, array('id' => 'user_account_balance')) }}
									<div id='total_amount_to_pay' class="margin-bottom-10"></div>
									<button name="edit_product" id="edit_product" value="pay_from_credit" type="submit" class="btn green">
									<i class="fa fa-credit-card"></i> {{ trans("product.pay_from_credit") }}</button>

									@if(CUtil::chkIsAllowedModule('featuredproducts'))
										<a href="{{URL::action('WalletAccountController@getAddAmount')}}" id="add_amount_to_credit" style="display:none;" class="btn blue"><i class="fa fa-plus"></i> {{trans('product.add_amount_to_credit')}}</a>
									@else
										<a href="{{URL::action('WalletAccountController@getIndex')}}" id="add_amount_to_credit" style="display:none;" class="btn blue"><i class="fa fa-plus"></i> {{trans('product.add_amount_to_credit')}}</a>
									@endif
								@endif

							@if(!$need_to_pay)

								<!--<div class="form-group {{{ $errors->has('delivery_days') ? 'error' : '' }}}">
									{{ Form::label('delivery_days', trans("product.delivery_days"), array('class' => 'col-md-3 control-label')) }}
									<div class="col-md-3">
										{{  Form::text('delivery_days', Input::get('delivery_days', $p_details['delivery_days']), array('class' => 'form-control')); }}
										<p class="text-muted">{{ trans("product.delivery_days_help") }}</p>
										<label class="error">{{{ $errors->first('delivery_days') }}}</label>
									</div>
								</div>-->
								{{Form::hidden('delivery_days',0)}}

								<div class="form-group {{{ $errors->has('product_notes') ? 'error' : '' }}}">
									<div class="clearfix">
										{{ Form::label('product_notes', trans("product.notes_to_admin"), array('class' => 'col-md-3 control-label')) }}
										<div class="col-md-5">
											{{  Form::textarea('product_notes', Input::get('product_notes', ''), array('class' => 'form-control')); }}
											<label class="error">{{{ $errors->first('product_notes') }}}</label>
										</div>
									</div>
								</div>

								@if(count($d_arr['product_notes']) > 0)
									<div class="form-group">
										<div class="col-md-offset-3 col-md-8">
											<p><a class="btn btn-xs btn-info" href="javascript:void(0);" onclick="javascript:return showHideNotesBlock();" id="showNotes">
											{{ trans("product.products_show_product_notes") }} <i class='fa fa-chevron-down'></i></a></p>
											<div id="sel_NotesBlock" style="display:none;">
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
													<p>{{ nl2br(e($notes->notes)) }}</p>
												@endforeach
											</div>
										</div>
									</div>
								@endif

								<div class="form-group">
									<div class="col-md-offset-3 col-md-8">
										@if($p_details['product_status'] != 'Ok' && Config::get('products.product_auto_approve'))
											<button name="edit_product" id="edit_product" value="publish" type="submit" class="btn green">
											<i class="fa fa-globe"><sup class="fa fa-arrow-up"></sup></i> {{ trans("product.products_product_publish") }}</button>
										@elseif($p_details['product_status'] != 'Ok' && $p_details['product_status'] != 'ToActivate')
											<button name="edit_product" id="edit_product" value="send_for_approval" type="submit" class="btn green">
											{{ trans("product.products_product_send_for_approval") }} <i class="fa fa-location-arrow"></i></button>
										@else
											<button name="edit_product" id="edit_product" value="update" type="submit" class="btn blue-madison">
											<i class="fa fa-upload"></i> {{ trans("common.update") }}</button>
										@endif
										@if($p_details['product_status'] != 'Draft')
											<button name="edit_product" id="edit_product" value="set_to_draft" type="submit" class="btn default">
											<i class="fa fa-file-text-o"></i> {{ trans("product.product_set_to_draft") }}</button>
										@endif
									</div>
								</div>
							@endif
							{{ Form::hidden('id', $p_id, array('id' => 'id')) }}
							{{ Form::hidden('p', $d_arr['p'], array('id' => 'p')) }}
							{{ Form::close() }}
						</fieldset>
					@endif
					<!-- END: APPROVAL STATUS FORM -->
				</div>
				<!-- END: PRODUCT ADD STEPS -->

				<small id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></small>
				<div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog_msg" class="show">{{  trans('product.uploader_confirm_delete') }}</span>
				</div>

				<div id="dialog-cancellation-policy-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog_msg" class="show">{{  trans('product.cancellation_policy_delete_confirm') }}</span>
				</div>

				<div id="dialog-upload-errors" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog-upload-errors-span" class="show"></span>
				</div>
			</div>
		</div>
	</div>

	<script language="javascript" type="text/javascript">
		var title_min_length_message = "{{trans('product.title_min_length')}}";
		var title_max_length_message = "{{trans('product.title_max_length')}}";
		var summary_max_length_message = "{{trans('product.summary_max_length')}}";
		var page_name 	= "add_product";
		var pagetab 	= "{{ $d_arr['p'] }}";
		var mes_required = "{{trans('common.required')}}";
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var site_default_currency = "{{ Config::get('generalConfig.site_default_currency') }}" ;
		var product_actions_url = "{{ URL::action('ProductAddController@postProductActions')}}";
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
		var enter_value_greater_than_msg = '{{trans('product.enter_value_greater_than')}}';
		var serial_number_empty_line_error_msg = "{{trans('product.serial_number_empty_line_error')}}";
		var serial_number_equals_qty_error_msg = "{{trans('product.serial_number_equals_qty_error')}}";
		var product_free_label = "{{trans('product.product_free')}}";
		var common_no = "{{ trans('common.no') }}";
		var common_yes = "{{ trans('common.yes') }}";
		var common_save = '{{trans('common.save')}}';
		var common_cancel = '{{trans('common.cancel')}}';
		var common_ok = "{{ trans('common.ok') }}";

		var shipping_cost_estimate_url = '{{ URL::action('ProductAddController@postShippingCostEstimate')}}';
		@if($d_arr['p'] == 'tax')
			var product_price_currency_value = '{{$p_details['product_price_currency']}}';
		@endif
		@if($d_arr['p'] == 'preview_files')
            var prev_max_upload = parseInt('{{ Config::get('webshoppack.preview_max') }}');
		@endif
		var not_completed = '{{  trans("product.not_completed") }}';
		var upload_format_err_msg = '{{ sprintf(trans("product.products_allowed_formats"), implode(',', Config::get("webshoppack.thumb_format_arr"))) }}';
		var alowed_thumb_formats = '{{ implode('|', Config::get("webshoppack.thumb_format_arr")) }}';
		var alowed_preview_formats = '{{ implode('|', Config::get("webshoppack.preview_format_arr")) }}';
		var preview_img_format_err_msg = '{{ sprintf(trans("product.products_allowed_formats"), implode(',', Config::get("webshoppack.preview_format_arr"))) }}';

		var common_no_label = "{{ trans('common.cancel') }}" ;
        var common_yes_label = "{{ trans('common.yes') }}" ;
        var package_name = "{{ Config::get('webshoppack.package_name') }}" ;
		var products_show_product_notes = '{{  trans("product.products_show_product_notes") }}';
		var products_hide_product_notes = '{{  trans("product.products_hide_product_notes") }}';

		var length_width_height_size_lbl = '{{trans("product.length_width_height_size")}}';
		var length_width_height_msg = '{{trans("product.length_width_height_msg")}}';
		var remove_swap_msg = '{{ Lang::get("variations::variations.are_you_sure_want_to_delete_this_swapimg") }}';
		@if($d_arr['p'] == 'variations')
			var matrix_select_action = '{{ Lang::get("variations::variations.matrix_select_action") }}';
			var matrix_edit_head = '{{ Lang::get("variations::variations.matrix_edit_head") }}';
			var matrix_edit_content = '{{ Lang::get("variations::variations.matrix_edit_content") }}';
			var selection_none_err = '{{ Lang::get("variations::variations.selection_none_err") }}';

		@endif


		/*
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

		$('#submit').click(function() {
			var content = tinyMCE.activeEditor.getContent(); // get the content
			$('#content').val(content); // put it in the textarea
		});
		*/
	</script>
@stop
@section('script_content')

    @if($d_arr['p'] == 'preview_files')
        <script src="{{ URL::asset('/js/lib/jQuery_plugins/jquery.tablednd_0_5.js') }}"></script>
    @endif

    @if($d_arr['p'] == 'preview_files' || $d_arr['p'] == 'download_files')
        <script src="{{ URL::asset('/js/uploadDocument.js') }}"></script>
        <script src="{{ URL::asset('/js/ajaxupload.3.5.min.js') }}"></script>
    @endif

    <script language="javascript" type="text/javascript">
	/*
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
			*/

			function showErrorDialog(err_data) {
				var err_msg ='<div class="note note-danger">'+err_data.error_message+'</div>';
				$('#error_msg_div').html(err_msg);
				var body = $("html, body");
				body.animate({scrollTop:0}, '500', 'swing', function() {
				});
			}
    @if($d_arr['p'] == 'preview_files')
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
                        //console.info(response); hideLoadingImage (false);
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
                                    showErrorDialog({status: 'error', error_message: not_completed});
                                }
                                 hideLoadingImage(false);
                            });
                            $(this).dialog("close");
                        }, "{{  trans("common.no") }}": function() { $(this).dialog("close"); }
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
                        //console.info(response);
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

			<!-- Swap image start -->
			@if(isset($d_arr['allow_swap_image']) && $d_arr['allow_swap_image'] > 0)
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

						if (!(ext && /^({{ implode('|', Config::get("webshoppack.preview_format_arr")) }})$/.test(ext))){
                        showErrorDialog({status: 'error', error_message: preview_img_format_err_msg});
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
                            html_text +=  '<div class="col-md-2"><div class="margin-top-5"><span  style="display:none;" id="item_swap_image_save_span_' + data.resource_id +'"><a class="btn btn-xs green" onclick="javascript:saveItemSwapImageTitle(' + data.resource_id +');" href="javascript: void(0);"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a></span>';
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
				title: cfg_site_name,
				modal: true,
				buttons: [{
					text: common_yes_label,
					click: function()
					{
                            postData = 'action=delete_swap_image&row_id=' + resource_id + '&product_id='+product_id,
                            displayLoadingImage(true);
                            $.post(product_actions_url, postData,  function(response)
                            {
                                hideLoadingImage (false);
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
						}
					},{
					text: common_no_label,	click: function(){		 $(this).dialog("close");	}
					}]
				});
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
                        //console.info(response);
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
                            html_text +=  '<div class="col-md-2"><div class="margin-top-5"><span  style="display:none;" id="item_resource_save_span_' + data.resource_id +'"><a class="btn btn-xs green" onclick="javascript:saveItemResourceTitle(' + data.resource_id +');" href="javascript: void(0);"><i class="fa fa-save"></i> {{ trans("product.products_save_resource_title") }}</a></span>';
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
                                //console.info(data);
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
                        //console.log(response);
                        data = eval( '(' +  response + ')');
                        hideLoadingImage (false);
                        if(data.status=="success"){
                            // files folder come from config media.folder
                            html_text = '<tr  id="itemResourceRow_' + data.resource_id  + '" class="formBuilderRow"><td>';
                            html_text += '<a href="' +  data.download_url + '">'+ data.filename + '</a></td><td><div class="row">';
                            html_text +=  '<div class="col-md-8"><input class="form-control" type="text" name="item_resource_image_' + data.resource_id +'" id="resource_title_field_' + data.resource_id +'" value="'+ data.title +'" onkeypress="javascript:editItemResourceTitle(' + data.resource_id +');" /></div>';
                            html_text += '<div class="col-md-2"><div class="margin-top-5"><span id="item_resource_edit_span_' + data.resource_id +'"><a class="btn btn-xs blue" onclick="javascript:editItemResourceTitle(' + data.resource_id + ');" href="javascript: void(0);"><i class="fa fa-edit"></i> {{ trans("product.products_edit_resource_title") }}</a></span>';
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
                            postData = 'action=delete_resource&row_id='+resource_id+'&product_id='+product_id,
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
                $('#item_resource_save_span_' + resource_id).show(); 	// .addClass('clsSubmitButton');
                $('#resource_title_field_' + resource_id).focus();
                return false;
            }

            function saveItemResourceTitle(resource_id) {
                var resource_title = encodeURIComponent($('#resource_title_field_' + resource_id).val());
                postData = 'action=save_resource_title&row_id=' + resource_id + '&resource_title=' + encodeURIComponent($('#resource_title_field_' + resource_id).val());
                displayLoadingImage(true);
                $.post(product_actions_url, postData,  function(data)
                {
                if (data == 'success') {
                    $('#item_resource_edit_span_' + resource_id).show();
                    $('#item_resource_save_span_' + resource_id).hide();	// .removeClass('clsSubmitButton');
                    updateProductStatus();
                } else {
                    showErrorDialog({status: 'error', error_message: '{{  trans("product.not_completed") }}'});
                }
                    hideLoadingImage (false);
                });
                return false;
            }
        @endif

		//Shipping tab : package details
		if ( $.browser.msie  && $.browser.version  > 10.0) {
			$('#cancellation_policy_file_id').bind('change', function() {
			  var current_file = this.files[0].size;
			  var max_allowed = <?php echo Config::get("webshoppack.shop_cancellation_policy_allowed_file_size") * 1024 ?>;
			  var allowed_extension = $.trim(String('<?php echo Config::get("webshoppack.shop_cancellation_policy_allowed_extensions") ?>'));
			  var allowed_split = allowed_extension.split(',');
			  var file_name = $(this).val();
			  var reverse_name = file_name.split('.');
			  var last_element = $.trim(String(reverse_name[reverse_name.length -1])).toLowerCase();
			  if($.inArray(last_element, allowed_split) < 0){
				$('#file_type_size').html('File type not support');
				$('#edit_product').addClass('hide');
			  }else if(max_allowed < current_file){
				$('#file_type_size').html('Upload file size is too large');
				$('#edit_product').addClass('hide');
			  }else{
				$('#file_type_size').html('');
				$('#edit_product').removeClass('hide');
			  }
		   });
		}
    	</script>
@stop