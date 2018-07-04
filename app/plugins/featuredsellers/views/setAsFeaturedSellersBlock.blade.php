<div>
    {{ Form::open(array('url' => 'featuredsellers/set-as-featured-block', 'class' => 'form-horizontal',  'id' => 'setfeaturedfrm')) }}
        {{ Form::hidden("seller_id", $d_arr['seller_id']) }}
        {{ Form::hidden("user_account_balance", $d_arr['user_account_balance']['amount'], array('id' => 'user_account_balance')) }}
        <fieldset>
            <div class="form-group {{{ $errors->has('plan') ? 'error' : '' }}}">
                {{ Form::label('plan', trans('featuredsellers::featuredsellers.set_as_featured'), array('class' => 'col-md-4 control-label required-icon')) }}
                <div class="col-md-5">
                	{{ Form::select('plan', $d_arr['plans_arr'], null, array('class' => 'form-control valid', 'onchange' => 'checkAccBalance()', 'id' => 'plan')) }}
                    <label class="error">{{{ $errors->first('plan') }}}</label>
                </div>
            </div>

            <div class="form-group label-none">
                <label class="col-md-4 control-label">&nbsp;</label>
                <div class="col-md-8">
                	<a href="{{ URL::action('WalletAccountController@getAddAmount')}}" id="add_amount_to_credit" style="display:none;" class="btn blue"><i class="fa fa-plus"></i> {{trans('featuredsellers::featuredsellers.add_amount_to_credit')}}</a>
                    <button name="set_featured_sellers" id="set_featured_sellers" value="pay_from_credit" type="submit" class="btn green">
					<i class="fa fa-credit-card"></i> {{ trans("featuredsellers::featuredsellers.pay_from_credit") }}</button>
                </div>
            </div>
        </fieldset>
    {{ Form::close() }}
    <div id="dialog-product-confirm" title="" style="display:none;">
        <span class="ui-icon ui-icon-alert"></span>
        <span id="dialog-product-confirm-content" class="show ml15"></span>
    </div>
</div>