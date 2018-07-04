@extends('admin')
@section('content')
	<div id="error_msg_div"></div>
	<!--- SUCCESS INFO STARTS --->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{ Session::get('success_message') }}</div>
    @endif
    <!--- SUCCESS INFO END --->

    @if (Input::has('delete') && Input::get('delete') == "succ")
        <div class="note note-success">Stock deleted successfully</div>
    @endif

    @if (Session::has('stock_error_message') && Session::get('stock_error_message') != "")
    	<!--- ERROR INFO STARTS --->
        <div class="note note-danger">{{ Session::get('stock_error_message') }}</div>
        <?php Session::forget('stock_error_message'); ?>
        <!--- ERROR INFO END --->
    @endif

    @if (Session::has('error_message') && Session::get('error_message') != "")
    	<!--- ERROR INFO STARTS --->
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
        <!--- ERROR INFO END --->
    @else
        @if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
            <!--- ERROR INFO STARTS --->
            <p class="note note-danger">{{ $d_arr['error_msg'] }}</p>
            <!--- ERROR INFO END --->
        @else
        <div class="portlet box blue-hoki">
            <!-- TABLE TITLE STARTS -->
            <div class="portlet-title">
                <div class="caption">
                	@if(isset($p_details['product_name']))
                    	<i class="fa fa-edit"></i> Manage stocks for the product "{{{ nl2br($p_details['product_name']) }}}"
                    @else
                    	<i class="fa fa-plus-circle"></i> Manage stocks
					@endif
                </div>
                <a class="btn default btn-xs purple-stripe pull-right" title="Back to list" href="{{ URL::to('admin/product/list') }}">
                	<i class="fa fa-chevron-left"></i> Back to list
                </a>
            </div>
            <!-- TABLE TITLE END -->

            <div class="portlet-body form">
                    {{ Form::model($stock_details, ['url' => URL::action('AdminManageStocksController@postIndex'), 'method' => 'post', 'id' => 'addProductStocksfrm', 'class' => 'form-horizontal tab-content']) }}
                        <div class="form-body">
                            <h4 class="form-section">Enter Stocks Details</h4>
                            <!--<div class="form-group fn_clsPriceFields {{{ $errors->has('stock_country_id') ? 'error' : '' }}}">
                                {{ Form::label('stock_country_id', trans("product.country"), array('class' => 'col-md-3 control-label required-icon')) }}
                                <div class="col-lg-2">
                                    {{  Form::select('stock_country_id', $d_arr['countries_list'], null, array('class' => 'form-control select2me input-medium')); }}
                                    <label class="error">{{{ $errors->first('stock_country_id') }}}</label>
                                </div>
                            </div>-->

                            <div class="form-group {{{ $errors->has('stock_country_id_china') ? 'error' : '' }}}">
                                {{ Form::label('stock_country_id_china', 'Select country', array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-7">
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
                                    <p class="text-muted mt5"><strong>Note: </strong>Enter each serial number in separated line. Serial numbers count must be equal to the entered Quantity</p>
                                    <label class="error">{{{ $errors->first('serial_numbers_pak') }}}</label>
                                </div>
                            </div>
						</div>
                        <div class="form-actions fluid">
                            {{ Form::hidden('p_id', $d_arr['p_id'], array('id' => 'p_id')) }}
                            {{ Form::hidden('action', $d_arr['mode'], array('id' => 'action')) }}
                            <div class="col-md-offset-3 col-md-8">
                                <button name="add_stocks" id="add_stocks" value="add_stocks" type="submit" class="btn green">
									@if($d_arr['mode'] == 'edit')
										<i class="fa fa-arrow-up"></i> Update stocks
									@else
										<i class="fa fa-plus-circle"></i> Add stocks
									@endif
								</button>
								@if($d_arr['mode'] == 'edit')
									<button type="reset" name="reset_members" value="reset_members" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/manage-stocks?product_id='.$d_arr['p_id']) }}'">
	                                <i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}
	                                </button>
	                            @endif
                            </div>
                        </div>
					{{ Form::close() }}
                    <!--<h4 class="form-section">Stock List</h4>
                    <div class="table-responsive">
                        <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
                            <thead>
                                <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                                    <th class="col-lg-3">Country</th>
                                    <th class="col-md-3">Quantity</th>
                                    <th class="col-md-3">Serial Numbers</th>
                                    <th class="col-lg-3">{{ trans("common.action") }}</th>
                                </tr>
                            </thead>
                            <tbody class="fn_formBuilderListBody" id="shippingfee_tbl_body">
                                @if(count($stocks_list) > 0)
                                    @foreach($stocks_list as $key=>$stocks)
                                        <tr id="itemStockRow_{{$stocks['id']}}">
                                            <td>
                                            	<?php
													$county_name = '';
													$county_details = Products::getCountryDetailsByCountryId($stocks['stock_country_id']);
													if(count($county_details) > 0) {
														$county_name = $county_details['country'];
													}
												?>
												{{$county_name}}
											</td>
                                            <td>{{$stocks['quantity']}}</td>
                                            <td>{{nl2br($stocks['serial_numbers'])}}</td>
                                            <td class="status-btn">
                                                <a onclick="javascript:removeStock({{ $stocks['id'] }}, {{ $stocks['product_id'] }});" href="javascript: void(0);" title="Delete" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
                                                <a href="{{ $d_arr['edit_url'] }}&stock_id={{ $stocks['id'] }}" title="Edit" class="btn btn-xs blue"><i class="fa fa-edit"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><p class="alert alert-info">No Stocks added for this product yet.</p></td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    -->
                 </div>
            </div>
    	</div>
    	@endif
    @endif
    <small id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></small>
    <div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
        <span class="ui-icon ui-icon-alert"></span>
        <span id="dialog_msg" class="show ml15">{{  trans('product.uploader_confirm_delete') }}</span>
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('common.required')}}";
        var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
        var stock_actions_url = '{{ URL::action('AdminManageStocksController@postStockActions')}}';
    
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
                    }
                },*/
                quantity_china: {
                    required: {
                        depends: function(element) {
                            return ($("#stock_country_id_china").is(':checked')) ? true : false;
                        }
                    },
                    min:1,
                    digits: true
                },
                serial_numbers_china: {
                    required: {
                        depends: function(element) {
                            return ($("#stock_country_id_china").is(':checked')) ? true : false;
                        }
                    },
                    checkEmptyLines: {
                        depends: function(element) {
                            return ($("#stock_country_id_china").is(':checked')) ? true : false;
                        }
                    },
                    validateSerialNumberChina: {
                        depends: function(element) {
                            return ($("#stock_country_id_china").is(':checked')) ? true : false;
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
                quantity_pak: {
                    required: {
                        depends: function(element) {
                            return ($("#stock_country_id_pak").is(':checked')) ? true : false;
                        }
                    },
                    min:1,
                    digits: true
                },
                serial_numbers_pak: {
                    required: {
                        depends: function(element) {
                            return ($("#stock_country_id_pak").is(':checked')) ? true : false;
                        }
                    },
                    checkEmptyLines: {
                        depends: function(element) {
                            return ($("#stock_country_id_pak").is(':checked')) ? true : false;
                        }
                    },
                    validateSerialNumberPak: {
                        depends: function(element) {
                            return ($("#stock_country_id_pak").is(':checked')) ? true : false;
                        }
                    }
                }
            },
            messages: {
                stock_country_id_china: {
                    required: mes_required,
                },
                quantity_china: {
                    required: mes_required
                },
                serial_numbers_china: {
                    required: mes_required,
                },
                stock_country_id_pak: {
                    required: mes_required,
                },
                quantity_pak: {
                    required: mes_required
                },
                serial_numbers_pak: {
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
        }, "There are some empty lines. Enter each serial number in sperate lines without empty line");
    
        jQuery.validator.addMethod("validateSerialNumberChina", function (value, element) {
            var quantity = $('#quantity_china').val();
            var serial_numbers_array = value.split("\n");
            if (serial_numbers_array.length == quantity) {
                return true;
            }
            return false;
        }, "Serial numbers count must be equal to the entered Quantity");
    
        jQuery.validator.addMethod("validateSerialNumberPak", function (value, element) {
            var quantity = $('#quantity_pak').val();
            var serial_numbers_array = value.split("\n");
            if (serial_numbers_array.length == quantity) {
                return true;
            }
            return false;
        }, "Serial numbers count must be equal to the entered Quantity");
    
        function removeStock(stock_id, product_id) {
            $("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
            buttons: {
            "{{ trans('common.yes') }}": function() {
                postData = 'action=delete&stock_id=' + stock_id + '&product_id='+product_id,
                displayLoadingImage(true);
                $.post(stock_actions_url, postData,  function(response)
                {
                    hideLoadingImage (false);

                    data = eval( '(' +  response + ')');
                    if(data.result == 'success') {
                        //$('#itemStockRow_' + data.stock_id).remove();
                        //window.location.reload();
                        //removeErrorDialog();
                        window.location.href="{{ Url::to('admin/manage-stocks') }}?product_id="+product_id+"&delete=succ";
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
    </script>
@stop
