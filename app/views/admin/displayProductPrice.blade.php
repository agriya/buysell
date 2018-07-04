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
</div>

@if($p_details['is_free_product'] == 'Yes')
    <div class="btn blue-hoki btn-lg btn-block">{{ trans('viewProduct.free') }}</div>
@else
    @if($discount_price)
        <?php $subtotal_price_arr = $discount_price_arr;?>
        <p class="strike-out">{{ $product_price }}</p>
        <p class="btn default btn-lg btn-block no-margin" id="finalprice">{{ $discount_price }}</p>
    @else
        <?php $subtotal_price_arr = $product_price_arr;?>
        <p class="btn default btn-lg btn-block no-margin" id="finalprice">{{ $product_price }}</p>
    @endif
@endif-->

<div class="well well-new clearfix">
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

	<div>
        @if($p_details['is_free_product'] == 'Yes')
            <div class="btn btn-info btn-block">{{ trans('viewProduct.free') }}</div>
        @else
            @if($discount_price)
                <?php $subtotal_price_arr = $discount_price_arr;?>
                <p class='price-value strike-out'>{{ $product_price }}</p>
                <p class="btn default btn-lg btn-block no-margin no-cursor" id="finalprice">{{ $discount_price }}</p>
            @else
                <?php $subtotal_price_arr = $product_price_arr;?>
                <p class="btn default btn-lg btn-block no-margin no-cursor" id="finalprice">{{ $product_price }}</p>
            @endif
        @endif
    </div>
</div>