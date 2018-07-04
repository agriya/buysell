@extends('admin')
@section('content')
    <!-- BEGIN: ALERT BLOCK -->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="alert alert-success">{{	Session::get('success_message') }}</div>
    @endif
    
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="alert alert-danger">{{ Session::get('error_message') }}</div>
    @endif
    <!-- END: ALERT BLOCK -->
	
    <!-- BEGIN: PAGE TITLE -->
    <div class="message-navbar mb20">
        <h1 class="page-title">{{ $d_arr['page_title'] }}</h1>
    </div>
    <!-- END: PAGE TITLE -->
	
    <!-- BEGIN: MESSAGE LIST FORM -->
    {{ Form::open(array('id'=>'messageListfrm', 'method'=>'get','class' => 'form-horizontal form-request overflw-auto' )) }}
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <tbody>
                    @if(count($static_currency_arr) > 0)
                        <?php $cnt = 0; ?>
                        <tr><th colspan="4" style="background:#f5f5f5;">{{Lang::get('common.form')}} USD</th></tr>
                        @foreach($static_currency_arr as $val)
                            <tr class="sel_fees_details_{{$cnt}}">
                                <td style="display:none">
                                    {{$val['currency_from']}}
                                </td>
                                <td style="display:none">
                                    {{$val['currency_to']}}
                                </td>
                                <td width="20%">
                                    <p>To {{$val['currency_to']}} </p>
                                    @if($val['currency_from'] !='' && $val['currency_to'] != '')
                                        <p class="text-muted">(1 {{$val['currency_from']}} = {{ (number_format($val['exchange_rate'], 4, '.', '')) }} {{$val['currency_to']}})</p>
                                    @endif
                                </td>
                                <td width="40%" class="site-convfee">
                                    <div id="sel_fees_details_{{$cnt}}" class="fees_details" title="{{Lang::get('common.edit')}}">{{$val['exchange_rate_static']}}</div>
                                    @if($val['exchange_rate_static'] == '')
                                        <div class="row">
                                            <div class="col-md-4">
                                               <input type="text" id="sel_fees_text_{{$cnt}}" class="form-control mb10" value="{{$val['exchange_rate_static']}}" placeholder="Exchange rate" />
                                            </div>
                                            <div class="col-md-8 custom-pad1">
                                                <button type="button" name="edit_request" class="btn btn-xs btn-success sel_fees_submit" id="sel_fees_submit_{{$cnt}}" value="edit_request">
                                                    {{trans("common.update")}}
                                                </button>
                                                <button type="button" name="cancel_request" class="btn btn-xs default sel_fees_cancel" id="sel_fees_cancel_{{$cnt}}">
                                                    {{trans("common.cancel")}}
                                                </button>
                                            </div>
                                        </div>
                                        <div id="sel_fees_conversion_{{$cnt}}" style="display:none;"></div>
                                        <div id="sel_fees_exchange_{{$cnt}}" class="text-muted"> ({{ trans('admin/currencies.static_exchange_rate') }}: 1 {{$val['currency_from']}} = {{ number_format($val['exchange_rate_static'], 4, '.', '') }})</div>
                                        <label id="sel_fees_text_label_{{$cnt}}" style="display:none" generated="true" class="error"></label>
                                    @else
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input style="display:none" class="form-control mb10"  type="text" id="sel_fees_text_{{$cnt}}" value="{{$val['exchange_rate_static']}}" placeholder="Exchange rate" />
                                            </div>
                                            <div class="col-md-8 custom-pad1">
                                                <button style="display:none" type="button" name="edit_request" class="btn btn-xs btn-success sel_fees_submit" id="sel_fees_submit_{{$cnt}}">
                                                    {{trans("common.update")}}
                                                </button>
                                                <button style="display:none" type="button" name="cancel_request" class="btn btn-xs default sel_fees_cancel" id="sel_fees_cancel_{{$cnt}}">
                                                    {{trans("common.cancel")}}
                                                </button>
                                            </div>
                                        </div>
                                        <div id="sel_fees_conversion_{{$cnt}}" style="display:none;"></div>
                                        <div id="sel_fees_exchange_{{$cnt}}" class="text-muted"> ({{ trans('admin/currencies.static_exchange_rate') }}: 1  {{$val['currency_from']}} = {{ number_format($val['exchange_rate_static'], 4, '.', '') }} {{$val['currency_to']}})</div>
                                        <label id="sel_fees_text_label_{{$cnt}}" style="display:none" generated="true" class="error"></label>
                                    @endif
                                </td>
                            </tr>
                            <?php $cnt++; ?>
                        @endforeach
                    @else
                        <tr><td colspan="4"><p class="alert alert-info">{{ trans('myaccount/form.site_convesion_fee.no_record') }} </p></td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    {{ Form::close() }}
    <!-- END: MESSAGE LIST FORM -->
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script type="text/javascript">
        $(document).ready(function() {
            $(".fees_details").click(function(){
            iD=($(this).attr("id"));
            iD=iD.replace("sel_fees_details_","");
            //alert($("tr."+IDName+" td:nth-child(5)" ).html());
            //alert($(this).html())
            $("#sel_fees_details_"+iD).hide();
            $("#sel_fees_text_"+iD).val($(this).html());
            $("#sel_fees_text_"+iD).show();
            $("#sel_fees_submit_"+iD).show();
            $("#sel_fees_cancel_"+iD).show();

            })

            $(".sel_fees_submit").click(function(){
                iD=($(this).attr("id"));
                iD=iD.replace("sel_fees_submit_","");
                fromCurr=$("tr.sel_fees_details_"+iD+" td:nth-child(1)" ).html().trim();
                toCurr=$("tr.sel_fees_details_"+iD+" td:nth-child(2)" ).html().trim();
                fee=$('#sel_fees_text_'+iD).val().trim();

                if($('#sel_fees_text_'+iD).val().trim()=='')
                {
                    $('#sel_fees_text_'+iD).addClass("error");
                    $('#sel_fees_text_label_'+iD).html('{{trans('common.required') }}').show();
                    return false;
                }

                if(!Number(fee) || fee <= 0)
                {
                    $('#sel_fees_text_'+iD).addClass("error");
                    $('#sel_fees_text_label_'+iD).html('{{trans('common.invalid_amount') }}').show();
                    return false;
                }

                fee= parseFloat(fee);
                fee=fee.toFixed(2);
                if(fee !='')
                {
                    $.ajax
                    ({
                        type: "post",
                        url: "{{URL::to('admin/currency-exchange/update-static-currency-exchange-rate')}}",
                        data: { 'from':fromCurr,'to':toCurr,'fee':fee}
                    })
                    .done(function( resp )
                    {
                        if(resp != 0)
                        {
                            var display_fee = '<div class="text-muted">({{ trans('admin/currencies.static_exchange_rate') }}: 1 '+fromCurr+' = '+resp+' '+toCurr+')</div>';
                            $("#sel_fees_exchange_"+iD).html(display_fee);
                            $("#sel_fees_details_"+iD).html(fee);
                            $('#sel_fees_text_'+iD).removeClass("error");
                            $('#sel_fees_text_label_'+iD).html('').hide();
                            $("#sel_fees_details_"+iD).show();
                            $("#sel_fees_text_"+iD).hide();
                            $("#sel_fees_submit_"+iD).hide();
                            $("#sel_fees_cancel_"+iD).hide();
                            $("#sel_fees_exchange_"+iD).show();
                            $("[id ^= 'sel_fees_conversion_']").hide();
                        }
                        else
                        {
                            $('#sel_fees_text_'+iD).addClass("error");
                            $('#sel_fees_text_label_'+iD).html('invalid amount').show();
                        }
                    });
                }
            })

            $(".sel_fees_cancel").click(function(){

                iD=($(this).attr("id"));
                iD=iD.replace("sel_fees_cancel_","");
                showFee=$("#sel_fees_details_"+iD).html().trim();
                if(showFee=='')
                {
                    return false;
                }
                $('#sel_fees_text_'+iD).removeClass("error");
                $('#sel_fees_text_label_'+iD).html('').hide();
                $("#sel_fees_details_"+iD).show();
                $("#sel_fees_text_"+iD).val($(this).html());
                $("#sel_fees_text_"+iD).hide();
                $("#sel_fees_submit_"+iD).hide();
                $("#sel_fees_cancel_"+iD).hide();

            })
        });
    </script>
@stop