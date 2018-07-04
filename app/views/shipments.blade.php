@extends('base')
@section('content')
    <div class="add-product">
        <!-- BEGIN: ALERT BLOCK -->
        @if(Session::has('error_message') && Session::get('error_message') != '')
            <div class="alert alert-danger">{{ Session::get('error_message') }}</div>
            <?php Session::forget('error_message'); ?>
        @endif
        
        @if(Session::has('success_message') && Session::get('success_message') != '')
            <div class="alert alert-success">{{ Session::get('success_message') }}</div>
            <?php Session::forget('success_message'); ?>
        @endif
        <!-- END: ALERT BLOCK -->
        
        @if(!Session::has('final_success') || Session::get('final_success') != 1)
            <?php Session::forget('final_success'); ?>
            {{ Form::open(array('url' => 'shipments/add-shipment', 'method' => 'post')) }}
                <fieldset class="well">
                    <div class="form-group {{{ $errors->has('product_name') ? 'error' : '' }}}">
                        {{ Form::label('country', Lang::get('shippingTemplates.country'), array('class' => 'col-lg-3 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{  Form::select('country', $countries) }}
                            <label class="error">{{{ $errors->first('country') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('product_name') ? 'error' : '' }}}">
                        {{ Form::label('fee', Lang::get('shippingTemplates.shipping_fee'), array('class' => 'col-lg-3 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{  Form::text('fee', null, array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('fee') }}}</label>
                        </div>
                    </div>
        
        
                    <div class="form-group {{{ $errors->has('product_name') ? 'error' : '' }}}">
                        {{ Form::label('foreign_id', Lang::get('shippingTemplates.foreign_id'), array('class' => 'col-lg-3 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{  Form::select('foreign_id', $foreign_ids) }}
                            <label class="error">{{{ $errors->first('foreign_id') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group">
                        {{ Form::hidden('id', 2, array('id' => 'id')) }}
                        <div class="col-lg-offset-3 col-lg-5">
                            <button name="add_product" id="add_product" value="add_product" type="submit" class="btn btn-success">{{Lang::get('common.add')}}</button>
                        </div>
                    </div>
        
                </fieldset>
            {{ Form::close() }}
        
            <fieldset class="well">
                <h1>{{Lang::get('shippingTemplates.search_shipping_details')}}</h1>
                {{ Form::open(array('url' => 'shipments', 'method' => 'post')) }}
                    <div class="form-group {{{ $errors->has('product_name') ? 'error' : '' }}}">
                        {{ Form::label('country', Lang::get('shippingTemplates.country'), array('class' => 'col-lg-3 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{  Form::select('country', $countries, Input::get('country')) }}
                            <label class="error">{{{ $errors->first('country') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group {{{ $errors->has('product_name') ? 'error' : '' }}}">
                        {{ Form::label('foreign_id', Lang::get('shippingTemplates.foreign_id'), array('class' => 'col-lg-3 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{  Form::select('foreign_id', $foreign_ids, Input::get('foreign_id')) }}
                            <label class="error">{{{ $errors->first('foreign_id') }}}</label>
                        </div>
                    </div>
        
                    <div class="form-group">
                        <div class="col-lg-offset-3 col-lg-5">
                            <button name="add_product" id="add_product" value="add_product" type="submit" class="btn btn-success">Search</button>
                        </div>
                    </div>    
                {{ Form::close() }}
            </fieldset>
        
            @if(count($shipping_fee_list) > 0)
                <table class="table">
                    <tr>
                        <th>{{Lang::get('shippingTemplates.country')}}</th>
                        <th>{{Lang::get('shippingTemplates.foreign_id')}}</th>
                        <th>{{Lang::get('shippingTemplates.shipping_fee')}}</th>
                    </tr>
                    @foreach($shipping_fee_list as $shipping_fee)
                        <tr>
                            <td>{{$shipping_fee->countries->country}}</td>
                            <td>{{$shipping_fee->foreign_id}}</td>
                            <td>{{$shipping_fee->shipping_fee}}</td>
                            <td>
                                {{ Form::open(array('url' => 'shipments/update', 'method' => 'post')) }}
                                    {{Form::hidden('country_id', $shipping_fee->country_id)}}
                                    {{Form::hidden('foreign_id', $shipping_fee->foreign_id)}}
                                    {{Form::hidden('primary', $shipping_fee->id)}}
                                    {{Form::text('shipping_fee', $shipping_fee->shipping_fee)}}
                                    <button name="add_product" id="add_product" value="add_product" type="submit" class="btn btn-success">{{Lang::get('common.update')}}</button>
                                {{ Form::close() }}
                            </td>
                            <td>
                                {{ Form::open(array('url' => 'shipments/delete', 'method' => 'post')) }}
                                    {{Form::hidden('country_id', $shipping_fee->country_id)}}
                                    {{Form::hidden('foreign_id', $shipping_fee->foreign_id)}}
                                    {{Form::hidden('primary', $shipping_fee->id)}}
                                    <button name="add_product" id="add_product" value="add_product" type="submit" class="btn btn-success">{{Lang::get('common.delete')}}</button>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endif
    </div>
@stop