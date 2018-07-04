@extends('popup')
@section('content')
    <h1>{{ Lang::get('variations::variations.change_variations') }}</h1>

    <div id="variationGroup" class="pop-content">
        @if(isset($d_arr['variation_det']['variation_list']) && COUNT($d_arr['variation_det']['variation_list']) > 0)
        <fieldset class="form-horizontal">
            @foreach($d_arr['variation_det']['variation_list'] AS $varkey => $vars)
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ $vars['name'] }}
                    @if($vars['help_text'] != '')
                        <a class="fn_VariationDescToolTip"><strong><i class="fa fa-question-circle" title=""></i></strong></a>
						<div style="display:none; position:absolute;" class="fn_helpText var-helptxt">{{ $vars['help_text'] }}</div>
                    @endif
                </label>
                <div class="col-sm-4">
                    <select class="form-control" name="item_variations" id="item_variations_{$varkey}" onchange="getDetails()">
                        @foreach($vars['attrirb_det'] as $varattribs)
                            <option value="{{ $varattribs['attribute_id'] }}" @if($varattribs['high_light'] == 1) selected="selected" @endif>{{ $varattribs['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endforeach

            @if(isset($d_arr['variation_det']['variation_list'])  && COUNT($d_arr['variation_det']['variation_list']) > 0)
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ Lang::get('variations::variations.price') }}</label>
                    <div class="col-sm-4 margin-top-10 fonts18">
	                    <p id="itemSalePrice">{{ Lang::get('variations::variations.sale_price') }}</p>
                    </div>
                </div>

                <div class="form-group">
	                <label class="col-sm-3 control-label">{{ Lang::get('variations::variations.stock') }}</label>
                    <div id="itemStockAvail" class="col-sm-4 margin-top-10 fonts18">
                        {{ Lang::get('variations::variations.stock') }}
                    </div>
                </div>

                <div id="updBtn" class="form-group">
                	<div class="col-sm-offset-3 col-sm-7">
                        {{ Form::hidden('item_matrix_id', $d_arr['matrix_id'], array('id' => 'item_matrix_id')) }}
                        {{ Form::hidden('var_preview_img_src', '', array('id' => 'var_preview_img_src')) }}
                        {{ Form::hidden('var_preview_img_dim', '', array('id' => 'var_preview_img_dim')) }}
                        {{ Form::hidden('var_org_img_src', '', array('id' => 'var_org_img_src')) }}
                    	<input type="button" name="update_variation" id="update_variation" value="Update" class="btn btn-success"/>
                     </div>
                </div>
            @endif
        </fieldset>
        @else
            <p class="note note-info">{{ Lang::get('variations::variations.no_variation_list') }}</p>
        @endif
    </div>

	<script type="text/javascript" language="javascript">
        var page_name = "update_item_variation";
        $(document).ready(function() {
            @if(isset($d_arr['variation_det']['variation_list']) && COUNT($d_arr['variation_det']['variation_list']) > 0)
                getDetails();
            @endif
        });

        var currSymbol = '{{ Config::get('generalConfig.site_default_currency') }}';
        var currCode  = '{{ Config::get('generalConfig.site_default_currency') }}';
        var has_discount = 0;
        var stockAvailLbl = '{{ Lang::get('variations::variations.available'); }}'
        var viewitem_no_stock_msg = '{{ Lang::get("variations::variations.no_stock") }}';
        var no_swap_img_msg = ' {{ Lang::get('variations::variations.no_swap_image') }}';
        var item_id = '{{ $p_details["id"] }}';
        var item_owner_id = '{{ $p_details["product_user_id"] }}';
        var matrix_details_arr = '{{ $product_this_obj->variation_service->getItemMatrixDetailsArr($p_details["id"]); }}';
        var getDetails = function()
            {
                var checkBoxes = $('div#variationGroup [name="item_variations"]');
                var tempArr = [];
                checkBoxes.each ( function () {
                    tempArr.push($(this).val());
                });

                retArr = sort_unique(tempArr);
                var selAttr = retArr.join(",");
                var matrixData = JSON.parse(matrix_details_arr);
                $.each(matrixData, function (i, val) {
                //	alert("Fetching attib="+selAttr);		alert("current attrib="+val.attrib_id);
                    if (val.attrib_id == selAttr)
                    {
                        var largeimgsrc = val.large_img_src;
                        var itemPrice = val.price;
                        var itemNetPrice = val.net_price;
                        var priceLbl = currSymbol +''+ itemPrice+'<span>'+currCode+'</span>';
                        var upriceLbl = val.item_price_ucurrency_symbol +''+ val.item_price_ucurrency_amount+'<span>'+val.item_price_ucurrency_amount_label+'</span>';

                        if($("#itemSalePrice").length > 0)
                            $('#itemSalePrice').html(itemPrice);

                        var stock = val.stock;
                        $('#updBtn').show();

                        if(stock == 0)
                        {
                            $('#itemStockAvail').html(viewitem_no_stock_msg);
                            $('#updBtn').hide();
                        }
                        else
                        {
                            $('#addCartButton').show();
                            var stockLbl = stockAvailLbl+'<span> '+stock+'</span>';
                            $('#itemStockAvail').html(stockLbl);
                        }
                        $('#var_stock').val(stock);

                        $('#var_preview_img_src').val(largeimgsrc);
                        $('#var_preview_img_dim').val(val.large_img_dim);
                        $('#var_org_img_src').val(val.full_img_src);
                        $('#item_matrix_id').val(val.matrix_id);
                        return false;
                    }
                });
            }

            function sort_unique(arr) {
                arr = arr.sort(function (a, b) { return a*1 - b*1; });
                var ret = [arr[0]];
                for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
                    if (arr[i-1] !== arr[i]) {
                        ret.push(arr[i]);
                    }
                }
                return ret;
            }

            $(".fn_VariationDescToolTip").click(function(){
                $(this).next('.fn_helpText').toggle();
            });

            @if($d_arr['r_fnname'])
                var fnname = parent.{{ $d_arr['r_fnname'] }};
            @else
                var fnname = 0;
            @endif

            $('#update_variation').click(function(event) {
                var matrix_id = $('#item_matrix_id').val();

                if(matrix_id != '')
                {
                    if(fnname)
                        fnname(matrix_id, item_id, item_owner_id);
                    parent.$.fancybox.close();
                }
            });
    </script>
@stop