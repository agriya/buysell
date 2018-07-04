@extends('popup')
@section('content')
    <h1>{{trans("featuredproducts::featuredproducts.set_as_featured")}}</h1>
    <div class="pop-content">
    	<!-- BEGIN: INFO BLOCK -->
		@if(isset($d_arr['error_msg']))
			<div class="note note-danger">
				{{ $d_arr['error_msg'] }}
			</div>
            <a href="javascript://" itemprop="url" class="fnClose">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle margin-right-5"></i>{{ trans('common.close') }}</button>
            </a>
        @elseif(isset($d_arr['success_message']))
			<div class="note note-success">
				{{ $d_arr['success_message'] }}
			</div>
            <a href="javascript://" itemprop="url" class="fnClose">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle margin-right-5"></i>{{ trans('common.close') }}</button>
            </a>
		@elseif(Session::has('success_message'))
			<div class="note note-success">
				{{ Session::get('success_message') }}
			</div>
            <a href="javascript://" itemprop="url" class="fnClose">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle margin-right-5"></i>{{ trans('common.close') }}</button>
            </a>
        <!-- END: INFO BLOCK -->
    	@else
    	@if((isset($d_arr['plans_arr']) && count($d_arr['plans_arr']) > 1 ) || (\CUtil::isAdmin()))
            {{ Form::open(array('url' => 'featuredproducts/set-as-featured', 'class' => 'form-horizontal',  'id' => 'setfeaturedfrm')) }}
                    {{ Form::hidden("product_id", $d_arr['product_id']) }}
                    {{ Form::hidden("user_account_balance", $d_arr['user_account_balance']['amount'], array('id' => 'user_account_balance')) }}
                    <fieldset>
                        <div class="form-group {{{ $errors->has('plan') ? 'error' : '' }}}">
                            {{ Form::label('plan', trans('featuredproducts::featuredproducts.select_plan'), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{ Form::select('plan', $d_arr['plans_arr'], null, array('class' => 'form-control select2me valid', 'onchange' => 'checkAccBalance()', 'id' => 'plan')) }}
                                <label class="error">{{{ $errors->first('plan') }}}</label>
                            </div>
                            @if(\CUtil::isAdmin())
                                <a target="_blank" href="{{ URL::to('admin/featuredproducts/manage-featured-product-plans') }}" title="{{ Lang::get('featuredproducts::featuredproducts.add_featured_product_plan') }}" class="btn blue-madison" >
                                    <i class="fa fa-plus"></i>
                                </a>
                            @endif
                        </div>

                        <div class="form-group label-none">
                            <label class="col-sm-3 control-label">&nbsp;</label>
                            <div class="col-sm-8">
                                @if(\CUtil::isAdmin())
                                    <a href="{{ URL::to('admin/users/manage-credits').'?user_id='.$d_arr['seller_id'] }}" id="add_amount_to_credit" style="display:none;" class="btn blue"><i class="fa fa-plus"></i> {{trans('featuredsellers::featuredsellers.add_amount_to_seller_credit')}}</a>
                                @else
                                    <a href="{{URL::action('WalletAccountController@getAddAmount')}}" id="add_amount_to_credit" style="display:none;" class="btn blue"><i class="fa fa-plus"></i> {{trans('product.add_amount_to_credit')}}</a>
                                @endif
                                <button name="set_featured_product" id="set_featured_product" value="pay_from_credit" type="submit" class="btn green">
                               		<i class="fa fa-credit-card"></i> {{ trans("product.pay_from_credit") }}
                                </button>
                                <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                                    <button type="reset" class="btn default">{{ trans('common.cancel') }}</button>
                                </a>
                            </div>
                        </div>
                    </fieldset>
            {{ Form::close() }}
			@else
				<div class="note note-danger">{{ trans('featuredproducts::featuredproducts.admin_not_yet_added') }}</div>
			@endif
            <div id="dialog-product-confirm" title="" style="display:none;">
                <span class="ui-icon ui-icon-alert"></span>
                <span id="dialog-product-confirm-content" class="show ml15"></span>
            </div>
		    <script type="text/javascript">
                var mes_required = '{{ Lang::get('auth/form.required') }}';
                $("#setfeaturedfrm").validate({
                    rules: {
                        plan: {
                            required: true
                        }
                    },
                    messages: {
                        plan: {
                            required: mes_required
                        }
                    },
                    /* For Contact info violation */
                    submitHandler: function(form) {
                        var alert_msg = '{{ trans('featuredproducts::featuredproducts.confirm_featured_product_fee_from_credit') }}';
                        @if(\CUtil::isAdmin())
                            alert_msg = '{{ trans('featuredproducts::featuredproducts.confirm_featured_product_fee_from_seller_credit') }}';
                        @endif
                        $('#dialog-product-confirm-content').html(alert_msg);
                        $("#dialog-product-confirm").dialog({ title: '{{ trans('featuredproducts::featuredproducts.product_featured_head') }}', modal: true,
                            buttons: {
                                "{{ trans('common.yes') }}": function() {
                                    form.submit();
                                }, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
                            }
                        });
                        return false;
                    }
                });

                function checkAccBalance() {
                    var user_account_balance = parseFloat($("#user_account_balance").val());
                    var plan_amount = 0.00;
                    if($('#plan').length > 0) {
                        if($("#plan option:selected").val() != '') {
                            var plan = $("#plan option:selected").text();
                            var plan_amount_currency = plan.split(':')[1];
                            var plan_amount = plan_amount_currency.trim().split(' ')[1];
                            plan_amount = parseFloat(plan_amount.trim());
                        }
                    }
                    if(user_account_balance < plan_amount) {
                        $('#set_featured_product').hide();
                        $('#add_amount_to_credit').show();
                    }
                    else {
                        $('#set_featured_product').show();
                        $('#add_amount_to_credit').hide();
                    }
                }

                $('#add_amount_to_credit').click(function() {
                    parent.window.location.href = $(this).attr('href');
                });
           </script>
  		@endif
    </div>

	<script type="text/javascript">
		$('.fnClose').click(function() {
			parent.window.location.reload(true);
		});
	</script>
@stop