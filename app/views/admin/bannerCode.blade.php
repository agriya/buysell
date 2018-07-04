@extends('adminPopup')
@section('content')
    <h1>{{ trans('admin/manageSiteBanner.code') }}</h1>
    <div class="form-group">
    	{{ Form::textarea('code', $code, array ('class' => 'form-control', 'rows'=>'7', 'onclick' => 'this.select()', 'onfocus' => 'this.select()')); }}
    	<span>{{ trans('admin/manageSiteBanner.copy_code_note') }}</span>
    </div>
@stop