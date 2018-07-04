@extends('adminPopup')
@section('content')
	<h1>{{ trans('admin/productList.change_status') }}</h1>
    <div class="pop-content">
        @if(Session::has('success_message') && Session::get('success_message') != '')            
            <div class="note note-success">{{ Session::get('success_message') }}</div>
            <button type="reset" name="close_change_status" value="close_change_status" class="btn red btn-sm mt10" onclick="javascript:closeDialog();">
                <i class="fa fa-times-circle"></i> {{ trans('common.close') }}
            </button>
            <?php Session::forget('success_message'); ?>
        @elseif(Session::has('error_message') && Session::get('error_message') != '')
        	<div class="note note-success">{{ Session::get('error_message') }}</div>
            <button type="reset" name="close_change_status" value="close_change_status" class="btn red btn-sm mt10" onclick="javascript:cancelDialog();">
                <i class="fa fa-times-circle"></i> {{ trans('common.close') }}
             </button>
            <?php Session::forget('error_message'); ?>
        @endif
        
        @if($allow_to_view_form)
            <form name="frmManageProductStatus" id="frmManageProductStatus" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        {{ Form::label('product_status', trans('admin/productList.change_status_to'), array('class' => 'col-sm-3 control-label required-icon')) }}
                        <div class="col-sm-5">
                            {{ Form::select('product_status', $d_arr['status_drop'], Input::get("product_status"),array('class' => 'form-control bs-select input-medium')) }}
                            <label class="error">{{{ $errors->first('product_status') }}}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('comment', trans('admin/productList.comment_label'), array('class' => 'col-sm-3 control-label required-icon')) }}
                        <div class="col-sm-7">
                            {{ Form::textarea('comment', Input::get("comment"), array ('class' => 'form-control')) }}
                            <label class="error">{{{ $errors->first('comment') }}}</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group label-none">
                    <label class="col-sm-3 control-label">&nbsp;</label>
                    <div class="col-sm-8">
                    	{{ Form::hidden('p_id', $p_id, array('id' => 'p_id')) }}
                        <button type="submit" name="change_status" class="btn btn-success" id="change_status" value="change_status">{{trans("common.submit")}}</button>
                        <button type="reset" name="cancel_change_status" value="cancel_change_status" class="btn default" onclick="javascript: cancelDialog();">
                        	{{trans("common.cancel")}}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@stop

@section('includescripts')
    <script type="text/javascript">
		var mes_required = '{{ trans('common.required') }}';
		@if($allow_to_view_form)
			$(document).ready(function() {
				$("#frmManageProductStatus").validate({
					rules: {
						product_status: {
							required: true
						},
						comment : {
							required: true
						}
					},
					messages: {
						product_status: {
							required: mes_required
						},
						comment : {
							required: mes_required
						}
					}
				});
			});
	    @endif
		function closeDialog()
		{
			parent.window.location.href = "{{ URL::to('admin/product/list') }}";
		}
		function cancelDialog() {
			parent.$.fancybox.close();
		}
    </script>
@stop
