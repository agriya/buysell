@extends('adminPopup')
@section('content')
	<div id="error_msg_div" class="note note-danger"></div>
    <h1>{{ trans('admin/purchaseslist.set_as_paid') }}</h1>
	@foreach($invoice_details as $invoice)
        <div class="pop-content">
            {{ Form::open(array('url' => URL::action('AdminPurchasesController@postSetAsPaidPopup'), 'class' => 'form-horizontal',  'id' => 'set_as_paid_frm', 'name' => 'form_checkout')) }}
            {{Form::hidden('oder_id', $invoice->reference_id, array('id' => 'oder_id'))}}
                <div class="form-group">
                    {{ Form::label('invoice_id', trans('admin/purchaseslist.invoice_id'), array('class' => 'col-sm-3 control-label')) }}
                    <div class="col-sm-3">
                        {{ Form::label('invoice_id', $invoice->common_invoice_id, array('class' => 'control-label text-bold')) }}
                    </div>
                </div>
                
                <div class="form-group">
                    {{ Form::label('order_id', trans('admin/purchaseslist.order_id'), array('class' => 'col-sm-3 control-label')) }}
                    <div class="col-sm-3">
                        {{ Form::label('order_id', $order_code, array('class' => 'control-label text-bold')) }}
                    </div>
                </div>
                
                <div class="form-group">
                    {{ Form::label('user_name', trans('default.user_name'), array('class' => 'col-sm-3 control-label')) }}
                    <div class="col-sm-6">
                        {{ Form::label('user_name', $user_name, array('class' => 'control-label text-bold')) }}
                    </div>
                </div>
                
                <div class="form-group">
                    {{ Form::label('select_transaction', trans('admin/purchaseslist.select_transaction') , array('class' => 'col-sm-3 control-label required-icon')) }}
                    <div class="col-sm-4">
                        {{ Form::select('select_transaction', array('pay_pal' => 'Pay Pal', 'credit' => 'Credit', 'others' => 'Others'),'', array('class' => 'form-control select2me input-medium ', 'id' => 'transaction_type')) }}
                        <label class="error">{{{ $errors->first('select_transaction') }}}</label>
                    </div>
                </div>
                
                <div class="form-group">
                    {{ Form::label('amount', trans('admin/purchaseslist.amount'), array('class' => 'col-sm-3 control-label required-icon')) }}
                    <div class="col-sm-5">
                        {{ Form::text('amount', $invoice->amount, array('class' => 'form-control','id' => 'amount_value')) }}
                        <label class="error">{{{ $errors->first('amount') }}}</label>
                    </div>
                </div>
                
                <div class="form-group">
                    {{ Form::label('description', trans('default.description'), array('class' => 'col-sm-3 control-label required-icon')) }}
                    <div class="col-sm-7">
                        {{ Form::textarea('description', Null, array('class' => 'form-control', 'id' => 'description_value')) }}
                        <label class="error">{{{ $errors->first('description') }}}</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">&nbsp;</label>
                    <div class="col-sm-8">
                        <button type="button" onclick="updateSetAsPaid('{{ $invoice->reference_id }}');" class="btn btn-success">
                            <i class="fa fa-check"></i> {{ trans('common.submit') }}
                        </button>
                        <a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
                            <button type="reset" class="btn default"><i class="fa fa-times"></i> {{ trans('common.cancel') }}</button>
                        </a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
	@endforeach

	<script language="javascript" type="text/javascript">
		$("#set_as_paid_frm").validate({
				rules: {
						select_transaction: {
							required: true
						},
						amount: {
							required: true,
							number: true
						},
						description: {
							required: true
						}
					},
					messages: {
						select_transaction: {
							required: mes_required,
							number: jQuery.format("{{trans('common.number_validation')}}"),
						},
						amount: {
							required: mes_required
						},
						description: {
							required: mes_required
						}
					},
				submitHandler: function(form) {
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

		function closeFancyBox() {
			parent.$.fancybox.close();
		}

		$('#error_msg_div').hide();
		function updateSetAsPaid(order_id) {

			if($("#set_as_paid_frm").valid())
			{
				var transaction_type =$('#transaction_type').val();
				var amount_value = $('#amount_value').val();
				var description_value = $('#description_value').val();
				//alert(transaction_type); return false;
				var actions_url = '{{ URL::action('AdminPurchasesController@postSetAsPaidPopup')}}';
				postData = 'order_id=' + order_id+'&amount_value='+amount_value+'&description_value='+description_value+'&transaction_type='+transaction_type;
				parent.displayLoadingImage(true);
				$.post(actions_url, postData,  function(data)
				{
					parent.hideLoadingImage (false);
					//data = eval( '(' +  response + ')');

					if(data == 'success')
					{
						parent.$('#set_as_paid_info_'+order_id).html(data);
						parent.$('#container_'+order_id).html('<p class="label label-success">{{trans('admin/purchaseslist.status_txt_payment_completed')}}</p>');
						//parent.updateSetAsPaidValues(data);
						parent.$.fancybox.close();
					}
					else{
						$('#error_msg_div').show();
						$('#error_msg_div').html(data);
					}
				});
			}
			return false;
		}
	</script>
@stop