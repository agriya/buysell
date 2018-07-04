<!--<div class="clearfix">
    <p class="@if($p_details['is_free_product'] == 'Yes') text-right @else pull-right @endif">
        <small>{{ trans('viewProduct.delivery') }}:
            @if($p_details['delivery_days'] > 0)
                <strong>{{ $p_details['delivery_days'].' '.Lang::choice('viewProduct.day_choice', $p_details['delivery_days']) }}</strong>
            @else
                <strong>{{ trans('viewProduct.instant_delivery') }}</strong>
            @endif
        </small>
    </p>
</div>-->

@if(isset($d_arr['show_variation']) && $d_arr['show_variation'] > 0 && isset($d_arr['allow_variation']) && $d_arr['allow_variation'] > 0)
       @include('variations::itemVariationPriceTab')
@endif

<!-- BEGIN: PRICE DETAILS -->
<p title="{{{ $product_title }}}">{{{ $product_title }}}</p>
<div class="clearfix price-bg text-center">
    @if($p_details['is_free_product'] == 'Yes')
    	<div class="label label-primary label-free">{{ trans('viewProduct.free') }}</div>
    @else
    	@if(isset($deal_details['deal_available']) && COUNT($deal_details) > 0 && $deal_details['deal_available'] && isset($discount_price_arr))
			<?php
				$subtotal_price_arr = $discount_price_arr;
                $discPrice = CUtil::convertAmountToCurrency($price_group['discount_usd'], Config::get('generalConfig.site_default_currency'), '', true);
            ?>
            <p class="strike-out" id="finalprice_org">{{ $product_price }}</p>

            @if(isset($price_group['discount_percentage']) && $price_group['discount_percentage'] > 0)
                <p class="strike-out" id="discprice_org">{{ $discPrice }}</p>
            @endif

            <div class="margin-top-5 deals-priceblk">
				<p class="no-cursor" id="finalprice">
					<span>{{ Lang::get('deals::deals.deal_price_lbl') }}</span> {{ CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}
				</p>
			</div>
			{{ Form::hidden('matrix_id_val', 0, array('id' => 'matrix_id_val')) }}
	    @else
            @if($discount_price)
                <?php $subtotal_price_arr = $discount_price_arr;?>
                <p class="strike-out" id="finalprice_org">{{ $product_price }}</p>
                 <p class="no-cursor" id="finalprice">{{ $discount_price }}</p>
            @else
                <?php $subtotal_price_arr = $product_price_arr;?>
                <p class="no-cursor" id="finalprice">{{ $product_price }}</p>
            @endif
        @endif
    @endif

    <!-- BEGIN: ASK A QUEST DETAILS -->
    @if(CUtil::isMember())
        @if($logged_user_id != $p_details['product_user_id'])
            <span class="show"><a href="@if($preview_mode){{ $no_url }} @else{{ URL::to('shop/user/message/add/'.$user_details['user_code']) }} @endif" class="fn_signuppop">
			{{ trans('viewProduct.ask_a_question') }}</a></span>
        @endif
    @else
        <?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
        <span class="show"><a href="{{ $login_url }}" class="fn_signuppop">{{ trans('viewProduct.ask_a_question') }}</a></span>
    @endif
    <!-- END: ASK A QUEST DETAILS -->
</div>
<!-- END: PRICE DETAILS -->

<!-- BEGIN: QUANTITY & SIZE -->
<!--
<form class="form-horizontal clearfix" role="form">
    <div class="row bs-quantsz">
		<div class="col-md-5 col-sm-12 col-xs-12">
			<div class="form-group">
				<label class="col-md-6 col-sm-3 col-xs-3">{{ trans('viewProduct.quantity') }}</label>
				<select class="col-md-6 col-sm-9 col-xs-9 form-control bs-select">
					<option>1</option>
					<option>2</option>
					<option>3</option>
				</select>
			</div>
		</div>
		<div class="col-md-7 col-sm-12 col-xs-12">
			<div class="form-group">
				<label class="col-md-3 col-sm-3 col-xs-3">Size</label>
				<select class="col-md-9 col-sm-9 col-xs-9 form-control bs-select">
					<option>Select Size</option>
					<option>Select Size 1</option>
					<option>Select Size 2</option>
				</select>
			</div>
		</div>
	</div>
</form>
-->
<!-- END: QUANTITY & SIZE -->

<div class="margin-top-20">
    @if(Config::get('generalConfig.user_allow_to_add_product'))
        <!-- BEGIN: PRODUCT EDIT DETAILS -->
        <div class="user-info-det">
            @if($logged_user_id == $p_details['product_user_id'])
                <?php $edit_url =  URL::to('product/add?id='.$p_details['id']); ?>
                <p class="pull-right no-margin"><a href='{{$edit_url}}' class="btn purple-plum"><i class="fa fa-edit"></i> {{ trans('common.edit') }}</a></p>
            @endif
        </div>
        <!-- END: PRODUCT EDIT DETAILS -->
    @endif

    <!-- BEGIN: PRODUCT OVERVIEW -->
    <h2>{{ trans('viewProduct.overview') }}:</h2>
    <ul class="viewpg-overview list-unstyled margin-bottom-30">
    	<li><span>{{ trans('viewProduct.product_code') }}: <strong class="text-muted">{{ $p_details['product_code'] }}</strong></span></li>
        @if(count($d_arr['shop_details']) > 0)
            <li>
				<span>
					{{ trans('viewProduct.feedback_label') }}:
					<a href="{{  URL::to('shop/'.$d_arr['shop_details']['url_slug'].'/shop-reviews') }}"><strong>{{ $d_arr['feed_back_cnt'] }} {{ Lang::get('common.reviews')}}</strong></a>
				</span>
			</li>
        @endif
        <li>
			<span>
				{{ trans('viewProduct.listed_on') }}
				@if($p_details['date_activated'] == '0')
					{{ trans('viewProduct.not_available') }}
				@else
					<span class="text-muted">{{ date('M d, Y', $p_details['date_activated'])  }} </span>
				@endif
			</span>
		</li>
    </ul>
    <!-- END: PRODUCT OVERVIEW -->
</div>

<!-- BEGIN: SHIPPING DETAILS -->
@if(isset($p_details['shipping_template']) && $p_details['shipping_template'] > 0)
    <?php $shipping_total_price = $shipping_total_price + $d_arr['default_shipping_fee']; ?>
    <div class="margin-bottom-20 price-details">
        <h2 class="margin-bottom-20 margin-top-10">{{ trans('viewProduct.shipping_details') }}:</h2>
        <dl class="dl-horizontal shipping-details">
            <dt>{{ trans('viewProduct.shipping') }}</dt>
            <dd>
                <div id="product_shipping_info">
                    <p>
                        @if($d_arr['default_shipping_company'] != '')
                            <span id="shipping_price" class="shipping-prc">
                                @if($d_arr['default_shipping_fee'] > 0)
                                    <strong>{{ CUtil::convertAmountToCurrency($d_arr['default_shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }} </strong>
                                    <span class="value_check hidden">1</span>
                                @elseif($d_arr['shipping_company_err_msg'] != '')
                                    <strong class="text-danger">{{ $d_arr['shipping_company_err_msg'] }} </strong>
									<span class="value_check hidden">0</span>
                                @else
                                    <strong class="badge badge-primary">Free </strong>
                                    <span class="value_check hidden">1</span>
                                @endif
                            </span>
                            <span class="text-muted"> to </span>
                            <a id="shipping_country_company_info" href="javascript:void(0)" onclick="openShippingCompanyPopup();">{{ $d_arr['default_shipping_country'] }} via {{ $d_arr['default_shipping_company'] }}</a>
                        @else
                            <span id="shipping_price"></span><a id="shipping_country_company_info" href="javascript:void(0)" onclick="openShippingCompanyPopup();">
                            {{ trans('common.do_not_ship_to') }} {{ $d_arr['default_shipping_country'] }}</a>
                        @endif
                    </p>
                </div>
            </dd>

            <dt class="text-ellipsis">{{ trans('viewProduct.quantity') }}</dt>
            <dd>
                <div class="num-type">
                	{{ Form::input('number', 'shipping_quantity', 1, array ('id'=>'shipping_quantity', 'class' => 'form-control fnQuantity', 'autocomplete' => 'off', 'min' => '1', 'max' => '1000')); }}
                	<small class="help-block text-muted">Max 1000 qty</small>
                </div>
            </dd>

            <dt>{{ trans('viewProduct.total_price') }}</dt>
            <dd><p id="shipping_total">{{ CUtil::convertAmountToCurrency($shipping_total_price, Config::get('generalConfig.site_default_currency'), '', true) }}</p></dd>
        </dl>
    </div>
@endif

@if(!$preview_mode)
    <div id="showAddCartBtn">
        <!--
        @if(CUtil::isMember())
            {{ Form::open(array('url' => 'checkout', 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'checkOutfrm', 'name' => 'checkOutfrm')) }}
                {{ Form::hidden('pid', $p_details['id'], array("id" => "pid"))}}
                {{ Form::hidden('type', 'product', array("id" => "type"))}}
                {{ Form::hidden('product_services', '', array("id" => "product_services"))}}
                @if($preview_mode)
                    <button name="buy_now" id="buy_now" value="buy_now" type="button" class="btn green btn-lg btn-block">{{ trans('viewProduct.buy_now') }}</button>
                @else
                    <button name="buy_now" id="buy_now" value="buy_now" type="submit" class="btn green btn-lg btn-block">{{ trans('viewProduct.buy_now') }}</button>
                @endif
            {{ Form::close() }}
        @else
            <a href="{{ url('users/login?form_type=selLogin') }}" class="fn_signuppop showAddCartBtn btn green btn-lg btn-block" id="buy_now" action="{{ url('users/signup-pop/selLogin') }}">{{ trans('viewProduct.buy_now') }}</a>
        @endif
        -->
        <div id="addCartButton" class="margin-top-20">
            {{ Form::open(array('url' => 'cart/add', 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'addCartfrm', 'name' => 'addCartfrm')) }}
                {{ Form::hidden('product_id', $p_details['id'], array("id" => "product_id"))}}
                {{ Form::hidden('qty', 1, array("id" => "qty"))}}

              	@if(isset($d_arr['show_variation'])  && $d_arr['show_variation'] > 0)
	                {{ Form::hidden('matrix_id', 0, array("id" => "matrix_id")) }}
				@endif
                {{ Form::hidden('shipping_country_id', $d_arr['default_shipping_country_id'], array("id" => "shipping_country_id"))}}
                {{ Form::hidden('shipping_company_id', $d_arr['default_shipping_company_id'], array("id" => "shipping_company_id"))}}
				<div class="pos-relative cart-disap">
                	<button type="submit" name="add_to_cart" value="add_to_cart" class="btn btn-lg btn-block btn-success" @if(CUtil::isAdmin() || $logged_user_id == $p_details['product_user_id']) disabled="disabled" @endif><i class="fa fa-shopping-cart margin-right-5"></i> {{ trans("viewProduct.add_to_cart") }}</button>
					@if(CUtil::isAdmin() || $logged_user_id == $p_details['product_user_id']) <span @if($logged_user_id == $p_details['product_user_id']) title="{{ trans("viewProduct.you_cant_purchase_your_own_product") }}" @endif class="cart-disabled disabled1" @if(CUtil::isAdmin()) title="{{ trans("viewProduct.admin_cant_add") }}" @endif><i class="fa fa-ban"></i></span> @endif
				</div>
            {{ Form::close() }}
               <!-- <button type="submit" name="add_to_cart" value="add_to_cart" class="btn btn-lg btn-block btn-success"><i class="fa fa-shopping-cart margin-right-5"></i> {{ trans("viewProduct.add_to_cart") }}</button>
            {{ Form::close() }} -->
        </div>
        <div class="stock_value pos-relative cart-disap margin-bottom-20" style="display:none">
			<button type="submit" name="add_to_cart" value="add_to_cart" class="btn btn-lg btn-block btn-success" disabled="disabled"><i class="fa fa-shopping-cart margin-right-5"></i>{{ trans("viewProduct.add_to_cart") }}</button>
			<span title="{{ trans("viewProduct.stock_not_avalible") }}" class="cart-disabled disabled1"><i class="fa fa-ban"></i></span>
		</div>
    </div>
@else
    {{ Form::hidden('product_id', $p_details['id'], array("id" => "product_id"))}}
    {{ Form::hidden('shipping_country_id', $d_arr['default_shipping_country_id'], array("id" => "shipping_country_id"))}}
    {{ Form::hidden('shipping_company_id', $d_arr['default_shipping_company_id'], array("id" => "shipping_company_id"))}}
@endif
{{ Form::hidden('hidden_arry_details_store', '', array('id' => 'hidden_arry_details_store'))  }}
<!-- END: SHIPPING DETAILS -->

<!-- BEGIN: SERVICE DETAILS -->
@if(isset($enable_serivices) && $enable_serivices)
    @if(isset($product_service_details) && count($product_service_details) > 0)
        <ul class="list-unstyled">
            @foreach($product_service_details as $product_service)
                <li>{{Form::checkbox('productservices[]',$product_service['id'], false, array('class'=> 'js-service-checkbox', 'data-price' =>$product_service['price'] ))}}</li>
                <li title="{{{$product_service['service_name']}}}">{{{$product_service['service_name']}}}</li>
                <li>
                    <?php $price_det = CUtil::convertAmountToCurrency($product_service['price'], Config::get('generalConfig.site_default_currency'), '', true);?>
                    <p class="price-value">{{$price_det}}</p>
                </li>
                <li class="text-muted"><i title="{{{$product_service['service_name']}}}" class="fa fa-question-circle"></i></li>
            @endforeach

            {{Form::hidden('org_price', $subtotal_price_arr['amt'], array('id' => 'orgamount'))}}
            <li>Subtotal Price:</li>
            <li><p class="price-value">{{$subtotal_price_arr['currency_symbol_font']}}<strong id="subtotal_price">{{$subtotal_price_arr['amt']}}</strong></p></li>
        </ul>
    @endif
@endif
<!-- END: SERVICE DETAILS -->