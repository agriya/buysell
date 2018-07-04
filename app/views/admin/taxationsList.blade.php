@extends('admin')
@section('content')
	<!-- NOTIFICATIONS STARTS -->
    @include('notifications')
    <!-- NOTIFICATIONS END -->

    <!--- ERROR INFO STARTS --->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!--- ERROR INFO STARTS --->

    <!--- SUCCESS INFO STARTS --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- SUCCESS INFO END --->

	<!-- PAGE TITLE STARTS -->
	<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/taxation.add_taxation') }}
    </a>
    <h1 class="page-title">{{Lang::get('admin/taxation.manage_taxations')}}</h1>
    <!-- PAGE TITLE END -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!--- SEARCH TITLE STARTS --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{Lang::get('admin/taxation.search_taxation')}}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- SEARCH TITLE END --->

            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking">
                        <div class="form-group">
                            {{ Form::label('tax_name', Lang::get('admin/taxation.tax_name'), array('class' => 'control-label col-md-3')) }}
                            <div class="col-md-4">
                                {{ Form::text('tax_name', Input::get("tax_name"), array('class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEARCH ACTIONS STARTS -->
                <div class="form-actions fluid">
                	<div class="col-md-offset-3 col-md-4">
                        <button type="submit" name="search_tax" value="search_tax" class="btn purple-plum">{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminTaxationsController@getIndex') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- SEARCH ACTIONS END -->
            </div>
         </div>
    {{ Form::close() }}


	<div class="portlet box blue-hoki">
        <!--- TABLE TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/taxation.taxations_list') }}
            </div>
        </div>
        <!--- TABLE TITLE END --->

        <div class="portlet-body">
            @if(sizeof($taxationslist) > 0 )
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                                <th>{{ Lang::get('admin/taxation.tax_name') }}</th>
                                <th class="col-md-4">{{ Lang::get('admin/taxation.description') }}</th>
                                <th>{{ Lang::get('admin/taxation.tax_fee') }}</th>
                                <th>{{ Lang::get('admin/taxation.fee_type') }}</th>
                                <th class="col-md-1">{{ Lang::get('admin/taxation.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taxationslist as $taxation)
                            	<tr>
                            		<td>{{ $taxation->tax_name }}</td>
                                    <td><div class="wid-330">{{ $taxation->tax_description }}</div></td>
                                    <td><strong>{{$taxation->tax_fee}}</strong></td>
                                    <td>{{ $taxation->fee_type }}</td>
                                    <td class="status-btn">
                                        <a href="{{ URL:: action('AdminTaxationsController@getUpdateTaxation',$taxation->id) }}" class="btn btn-xs blue" title="{{trans('common.edit')}}">
										<i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0)" onclick="doAction('{{ $taxation->id }}', 'delete')" class="btn btn-xs red" title="{{trans('common.delete')}}">
										<i class="fa fa-trash-o"></i></a>
                                    </td>
                            	</tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
                <!--- PAGINATION STARTS --->
                <div class="text-right">
                    {{ $taxationslist->appends(array('tax_name' => Input::get('tax_name'), 'search_tax' => Input::get('search_tax')))->links() }}
                </div>
                <!--- PAGINATION END --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/taxation.no_taxations_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::action('AdminTaxationsController@postDeleteTaxations'))) }}
    {{ Form::hidden('taxation_id', '', array('id' => 'taxation_id')) }}
    {{ Form::hidden('tax_action', '', array('id' => 'tax_action')) }}
    {{ Form::close() }}

	<div id="dialog-tax-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-tax-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		function doAction(taxation_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-tax-confirm-content').html('{{ trans('admin/taxation.confirm_delete') }}');
			}
			$("#dialog-tax-confirm").dialog({ title: '{{ trans('admin/taxation.taxtions_head') }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#tax_action').val(selected_action);
						$('#taxation_id').val(taxation_id);
						document.getElementById("productsActionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}


		var common_ok_label = "{{ Lang::get('common.yes') }}" ;
		var common_no_label = "{{ Lang::get('common.cancel') }}" ;
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					var txtDelete = action;

					var txtCancel = common_no_label;
					var buttonText = {};
					buttonText[txtDelete] = function(){
												Redirect2URL(atag_href);
												$( this ).dialog( "close" );
											};
					buttonText[txtCancel] = function(){
												$(this).dialog('close');
											};
					switch(action){
						case "Activate":
							cmsg = "Are you sure you want to activate this Member?";

							break;
						case "De-Activate":
							cmsg = "Are you sure you want to de-activate this Member?";
							break;
						case "Block":
							cmsg = "Are you sure you want to block this Member?";
							break;

						case "Un-Block":
							cmsg = "Are you sure you want to un-block this Member?";
							break;
					}
					$("#fn_dialog_confirm_msg").html(cmsg);
					$("#fn_dialog_confirm_msg").dialog({
						resizable: false,
						height:140,
						width: 320,
						modal: true,
						title: cfg_site_name,
						buttons:buttonText
					});
					return false;
				});
			});
	</script>
@stop