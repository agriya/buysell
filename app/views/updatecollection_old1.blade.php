@extends('base')
@section('content')
	<a href="{{ URL::action('TaxationsController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default margin-top-5">
        <i class="fa fa-chevron-left"></i> {{ Lang::get('taxation.taxations_list') }}
    </a>
    <!-- Page title Starts -->
        <h1>{{ Lang::get('taxation.update_taxation') }}</h1>
    <!-- Page title end -->

    <!-- Alert Block Starts -->
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="alert alert-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="alert alert-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- Alert Block Ends -->

    <!-- Update Taxation Fields Starts -->
    {{ Form::open(array('action' => array('TaxationsController@postUpdateTaxation', $taxation_id), 'id'=>'productFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
        <div id="selSrchProducts">
            <fieldset>
                <div class="form-group">
                    {{ Form::label('tax_name', Lang::get('taxation.tax_name'), array('class' => 'col-md-2 control-label required-icon')) }}
                    <div class="col-md-3">
                        {{ Form::text('tax_name', Input::old("tax_name",$taxation_det->tax_name), array('class' => 'form-control valid')) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('tax_description', Lang::get('taxation.tax_description'), array('class' => 'col-md-2 control-label')) }}
                    <div class="col-md-6">
                        {{ Form::textarea('tax_description', Input::old("tax_description",$taxation_det->tax_description), array('class' => 'form-control valid')) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('tax_fee', Lang::get('taxation.tax_fee'), array('class' => 'col-md-2 control-label required-icon')) }}
                    <div class="col-md-3">
                        {{ Form::text('tax_fee', Input::old("tax_fee",$taxation_det->tax_fee), array('class' => 'form-control valid')) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('fee_type', Lang::get('taxation.fee_type'), array('class' => 'col-md-2 control-label')) }}
                    <div class="col-md-3">
                        {{ Form::select('fee_type', array('percentage' => 'Percentage', 'flat' => 'Flat'), Input::old("fee_type",$taxation_det->fee_type), array('class' => 'form-control valid')) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        {{ Form::hidden('user_id', $user_id) }}
                        <button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn green"><i class="fa fa-arrow-up"></i> {{ Lang::get('taxation.update_taxation') }}</button>
                        <button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('TaxationsController@getIndex') }}'">
                        <i class="fa fa-times"></i> {{ Lang::get('taxation.tax_cancel') }}</button>
                    </div>
                </div>
            </fieldset>
        </div>
    {{ Form::close() }}
    <!-- Update Taxation Fields Ends -->
@stop