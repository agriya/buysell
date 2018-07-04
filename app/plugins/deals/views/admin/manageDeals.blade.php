@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{ Lang::get('deals::deals.manage_deals') }}</h1>
    <!-- END: PAGE TITLE -->

	{{ Form::open(array('id'=>'dealsfrm', 'method'=>'get', 'class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ Lang::get('deals::deals.deal_search_title') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->

			<!-- BEGIN: SEARCH FORM -->
            <div class="portlet-body form">
            	<div id="search_holder">
                	<div class="form-body">
						<div class="row">
                        	<fieldset class="col-md-6">
                                <div class="form-group">
                                 {{ Form::label('deal_id', Lang::get('deals::deals.deal_id_head_lbl'), array('class' => 'col-md-4 control-label')) }}
                                    <div class="col-md-6">
                                        <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            {{ Form::text('deal_id_from', Input::get("deal_id_from"), array('class' => 'form-control', 'placeholder' => Lang::get('deals::deals.search_deal_id_from') )) }}
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            {{ Form::text('deal_id_to', Input::get("deal_id_to"), array('class' => 'form-control', 'placeholder' => Lang::get('deals::deals.search_deal_id_to') )) }}
                                        </div>
                                        </div>
                                        <label class="error" for="deal_id_from" generated="true">{{$errors->first('deal_id_from')}}</label>
                                    </div>
                                </div>

                				<div class="form-group">
                                    {{ Form::label('deal_title', Lang::get('deals::deals.search_deal_title'), array('class' => 'col-md-4 control-label')) }}
                                    <div class="col-md-6">
                                        {{ Form::text('deal_title', Input::get("deal_title"), array('class' => 'form-control')) }}
                                        <label class="error" for="deal_title" generated="true">{{$errors->first('deal_title')}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {{ Form::label('deal_author', trans('admin/productList.product_search_seller_code'), array('class' => 'col-md-4 control-label')) }}
                                    <div class="col-md-6">
                                        {{ Form::text('deal_author', Input::get("deal_author"), array('class' => 'form-control', 'autocomplete' => 'off')) }}
                                        {{ Form::hidden('srch_user_id', Input::get("srch_user_id"), array("id" => "srch_user_id")) }}
                                        <label class="error" for="deal_author" generated="true">{{$errors->first('deal_author')}}</label>
                                    </div>
                                </div>
							</fieldset>

                           	<fieldset class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('slug_url', Lang::get('deals::deals.search_slug_url'), array('class' => 'col-md-4 control-label')) }}
                                    <div class="col-md-6">
                                        {{ Form::text('slug_url', Input::get("slug_url"), array('class' => 'form-control')) }}
                                        <label class="error" for="slug_url" generated="true">{{$errors->first('slug_url')}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {{ Form::label('featured', Lang::get('deals::deals.search_featured_status'), array('class' => 'col-md-4 control-label')) }}
                                    <div class="col-md-6">
                                        {{ Form::select('featured', array('' => trans('common.select'), 'Yes' => trans('common.yes'), 'No' => trans('common.no')), Input::get("featured"), array('class' => 'form-control bs-select')) }}
                                        <label class="error" for="search_featured_status" generated="true">{{$errors->first('search_featured_status')}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {{ Form::label('deal_status', Lang::get('deals::deals.search_deal_status'), array('class' => 'col-md-4 control-label')) }}
                                    <div class="col-md-6">
                                        {{ Form::select('deal_status', array('' => trans('common.select'))+$d_arr['deal_status_arr'], Input::get("deal_status"), array('class' => 'form-control bs-select')) }}
                                        <label class="error" for="deal_status" generated="true">{{$errors->first('deal_status')}}</label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
					</div>
					<div class="form-actions fluid">
						<div class="col-md-offset-2 col-md-5">
							<button type="submit" name="search_deal" value="search_deal" class="btn purple-plum">
							{{ trans("common.search") }} <i class="fa fa-search"></i> </button>
							<button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/deals/manage-deals') }}'"><i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}
							</button>
						</div>
					</div>
                </div>
            </div>
			<!-- END: SEARCH FORM -->
     	</div>
    {{ Form::close() }}


	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-tags"></i> {{ Lang::get('deals::deals.deal_listing') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($deal_list) > 0 )
            	<!--- BEGIN: MANAGE DEALS LIST --->
                {{ Form::open(array('id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                	{{ Form::hidden('list_action', '', array('id' => 'list_action')) }}
                    {{ Form::hidden('ident', '', array('id' => 'ident')) }}
                    <div class="table-scrollable">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('deals::deals.deal_id_head_lbl') }}</th>
                                    <th>{{ Lang::get('deals::deals.deal_title_head') }}</th>
                                    <th>{{ Lang::get('deals::deals.search_deal_author') }}</th>
                                    <th>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}(%)</th>
                                    <th>{{ Lang::get('deals::deals.deal_is_featured') }}</th>
                                    <th>{{ Lang::get('deals::deals.deal_status') }}</th>
                                    <th>{{ Lang::get('deals::deals.actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($deal_list as $deal)
                                	<?php
                                    	$expiry_details = $deal_serviceobj->dealExpiryDetails($deal->date_deal_from, $deal->date_deal_to);
                                        $view_url = $deal_serviceobj->getDealViewUrl($deal);
										$status_lbl = (Lang::has('deals::deals.DEAL_STATUS_'.strtoupper($deal->deal_status))) ? Lang::get('deals::deals.DEAL_STATUS_'.strtoupper($deal->deal_status)): str_replace('_', ' ', $deal->deal_status);
                                    	$user_details = CUtil::getUserDetails($deal->user_id);
										$purchase_count = (isset($purchased_count[$deal->deal_id])) ? $purchased_count[$deal->deal_id] : 0;
										$deal_start_date = strtotime($deal->date_deal_from);
										$curr_date = strtotime(date('Y-m-d'));
										$is_pre_start = ($deal_start_date > $curr_date ) ? 1 : 0;
                                	?>
                                    <tr>
                                        <td>
                                        	{{Form::checkbox('deal_ids[]',$deal->deal_id, false, array('class' => 'checkboxes js-ids', 'id' => "deal_id_{$deal->deal_id}") )}}
                                        </td>
                                        <td>{{ $deal->deal_id }}</td>
                                        <td>
											<a target="_blank" href="{{ $view_url }}" title="{{ Lang::get('deals::deals.view_deal_link_label') }}">{{$deal->deal_title }}</a>
										</td>
                                        <td>
                                        	@if(isset($user_details['user_name']))
                                            	<a href="{{ URL::to('admin/users/user-details').'/'.$deal->user_id }}" title="{{ $user_details['user_name'] }}">
													{{ $user_details['user_name'] }}
												</a>
                                                <p>(<a href="{{ URL::to('admin/users/user-details').'/'.$deal->user_id }}" title="{{ $user_details['user_name'] }}" class="text-muted">{{ $user_details['user_code'] }}</a>)</p>
                                            @endif
                                        </td>
                                        <td>
                                        	<div class="dl-horizontal-new dl-horizontal wid-220">
												<dl>
													<dt>{{ Lang::get('deals::deals.deal_type_discount_lbl') }}</dt>
													<dd><span><strong>{{ $deal->discount_percentage }}</strong>%</span></dd>
												</dl>
												<dl>
													<dt>{{ Lang::get('deals::deals.deal_from_lbl') }}</dt>
													<dd><span>{{ CUtil::FMTDate($deal->date_deal_from, 'Y-m-d', '') }}</span></dd>
												</dl>
												<dl>
													<dt>{{ Lang::get('deals::deals.deal_to_lbl') }}</dt>
													<dd><span>{{ CUtil::FMTDate($deal->date_deal_to, 'Y-m-d', '') }}</span></dd>
												</dl>
												<?php /* ?>
                                                @if(isset($expiry_details) && COUNT($expiry_details) > 0)
													<dl>
														<dt>{{ $expiry_details['label'] }}</dt>
														<dd><span>{{ CUtil::FMTDate($deal->date_deal_to, 'Y-m-d', '') }}</span></dd>
													</dl>
												@endif
                                                 <?php */ ?>
												<dl>
													<dt>{{ Lang::get('deals::deals.deal_bought') }}</dt>
													<dd><span class="text-warning"><strong>{{ $purchase_count }}</strong></span></dd>
												</dl>
                                                @if($deal->tipping_qty_for_deal > 0)
                                                    <dl>
                                                        <dt>{{ Lang::get('deals::deals.tipping_qty_lbl') }}</dt>
                                                        <dd><span class="font-yellow-casablanca"><strong>{{ $deal->tipping_qty_for_deal }}</strong></span></dd>
                                                    </dl>
                                                @endif
											</div>
										</td>
                                        <td>
											@if($deal->is_featured_deal != 0)
												<?php /*@if($deal_serviceobj->chkIsFeaturedDeal($deal->deal_id))*/ ?>
												<span class="badge badge-success">{{ Lang::get('common.yes') }}</span>
											@else
												<span class="badge badge-danger">{{ Lang::get('common.no') }}</span>
											@endif
                                        </td>
                                        <td>
											@if($deal->deal_status == "to_activate")
												<label class="label label-warning">{{ $status_lbl }}</label>
											@elseif($deal->deal_status == "active")
												<label class="label label-success">{{ $status_lbl }}</label>
											@elseif($deal->deal_status == "deactivated")
												<label class="label bg-red-pink">{{ $status_lbl }}</label>
											@elseif($deal->deal_status == "expired")
												<label class="label bg-red">{{ $status_lbl }}</label>
											@elseif($deal->deal_status == "closed")
												<label class="label label-default">{{ $status_lbl }}</label>
											@endif
										</td>
                                        <td class="status-btn">
                                            <div class="wid-170">
												<div class="clearfix mb10">
													<a target="_blank" href="{{ $view_url }}" class="btn btn-xs btn-info" title="{{ Lang::get('deals::deals.view_deal_link_label') }}">
														<i class="fa fa-eye"></i>
													</a>
                                                    @if($deal->deal_status == "to_activate")
                                                    	<a title="{{ Lang::get('deals::deals.activate_deal') }}" href="javascript:void(0)" onclick="doAct('{{ $deal->deal_id }}', 'activate')" class="btn btn-xs bg-green-seagreen"><i class="fa fa-check"></i></a>
                                                    @endif
													@if($deal->deal_status == 'active')
														<a title="{{ Lang::get('deals::deals.close_deal') }}" href="javascript:void(0)" onclick="doAction('{{ $deal->deal_id }}', 'close')" class="btn btn-xs red"><i class="fa fa-ban"></i></a>
													@endif
													<a href="{{Url::to('admin/deals/purchased-details', array('deal_id' => $deal->deal_id)) }}" class="fn_pop btn btn-success btn-xs" title="{{ Lang::get('deals::deals.deal_purchased_stats_link') }}"><i class="fa fa-shopping-cart"></i></a>
													@if($expiry_details['expired'] == 0)
														@if($deal->is_featured_deal)
														<?php /* @if($deal_serviceobj->chkIsFeaturedDeal($deal->deal_id)) */ ?>
														<a href="javascript:;" onclick="doAction('{{$deal->deal_id}}', 'unfeature')" class="btn btn-xs bg-red-sunglo" title="{{ Lang::get('deals::deals.remove_featured') }}"><i class="fa fa-star"></i></a>
														@else
															@if($deal->deal_status == 'active')
																<a href="{{ URL::to('admin/deals/set-featured-request', array('deal_id' => $deal->deal_id)) }}" class="btn btn-xs green fn_pop" title="{{ Lang::get('deals::deals.set_featured')  }}"><i class="fa fa-star"></i></a>
															@endif
														@endif
													@else
                                                    	@if(!$is_pre_start)
															<span class="label label-danger">{{ Lang::get("deals::deals.deal_expired_label") }}</span>
                                                        @endif
													@endif
												</div>

                                                <span>{{ Lang::get('deals::deals.tipping_lbl') }}: </span>
                                                @if($deal->tipping_qty_for_deal > 0)
                                                    @if($deal->deal_tipping_status == '')
                                                        <strong class="font-red">{{ Lang::get('deals::deals.notstarted_tipping_lbl') }}</strong>
                                                    @elseif($deal->deal_tipping_status == 'pending_tipping')
                                                        <strong class="text-warning">{{ Lang::get('deals::deals.pending_tipping_lbl') }}</strong>
                                                    @elseif($deal->deal_tipping_status == 'tipping_reached')
                                                        <strong class="text-success">{{ Lang::get('deals::deals.tipping_reached_lbl') }}</strong>
                                                    @elseif($deal->deal_tipping_status == 'tipping_failed')
                                                        <strong class="text-danger">{{ Lang::get('deals::deals.tipping_failed_lbl') }}</strong>
                                                    @endif
                                                @else
                                                    <strong class="text-muted">{{ Lang::get('common.not_applicable') }}</strong>
                                                @endif
											</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                         </table>
                    </div>

                    <!--- BEGIN: PAGINATION --->
                    <div class="pull-right">
                        {{ $deal_list->appends(array('deal_id_from' => Input::get('deal_id_from'), 'deal_id_to' => Input::get('deal_id_to'),
                            'deal_title' => Input::get('deal_title'), 'deal_author' => Input::get('deal_author'),
                            'slug_url' => Input::get('slug_url'), 'featured' => Input::get('featured'), 'deal_status' => Input::get('deal_status')
                             ))->links() }}
                    </div>
                    <!--- END: PAGINATION --->

                    <div class="clearfix">
                        <p class="pull-left mt10 mr10">{{Form::select('action', $actions,'',array('class'=>'form-control bs-select input-medium', 'id'=>'deal_action'))}}</p>
                        <p class="pull-left mt10"><input type="submit" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action"></p>
                    </div>
                 {{Form::close()}}
                 <!--- END: MANAGE DEALS LIST --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('deals::deals.no_deals_added_msg') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

	<div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.each(function(){
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				});
			}
			else
			{
				checkboxes.each(function(){
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				});
			}
		});

		function doAct(id, selected_action)
		{
			if(selected_action == 'activate')
			{
				$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.confirm_activate') }}");
			}
			$("#dialog-confirm").dialog({ title: '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#deal_id_'+id).attr('checked', true).parent().addClass("checked");
						$('#deal_action').val(selected_action);
						document.getElementById("listFrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		function doAction(id, selected_action)
		{
			if(selected_action == 'unfeature')
			{
				$('#dialog-confirm-content').html('{{ Lang::get("deals::deals.confirm_unfeatured") }}');
			}
			else if(selected_action == 'close')
			{
				$('#dialog-confirm-content').html('{{ Lang::get("deals::deals.confirm_close") }}');
			}
			$("#dialog-confirm").dialog({ title: '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#ident').val(id);
						$('#list_action').val(selected_action);
						document.getElementById("listFrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.deal_item_null_err_msg') }}");
				error_found = true;
			}
			var selected_action = $('#deal_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.select_action') }}");
				error_found = true;
			}
			if(!error_found)
			{
				if(selected_action == 'activate')
				{
					$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.confirm_activate') }}");
				}
				if(selected_action == 'de-activate')
				{
					$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.confirm_deactivate') }}");
				}
				if(selected_action == 'unfeature')
				{
					$('#dialog-confirm-content').html("{{ Lang::get('deals::deals.confirm_unfeatured') }}");
				}
			}
			if(error_found)
			{
				$("#dialog-confirm").dialog({ title:  '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title:  '{{ Lang::get("deals::deals.deals_head") }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#listFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		})
		/*
		$(window).load(function(){
			$.ajax({
				url: '{{ URL::to("admin/users-auto-complete") }}',
				dataType: "json",
				success: function(data)
				{
					var cat_data = $.map(data, function(item, val)
					{
						return {
							user_id: val,
							label: item
						};
					});

					$("#deal_author").autocomplete({
						delay: 0,
						source: cat_data,
						minlength:3,
						select: function (event, ui) {
							$('#srch_user_id').val(ui.item.user_id);
							return ui.item.label;
						},
						change: function (event, ui) {
							if (!ui.item) {
								$('#srch_user_id').val('');
							}
						}
					});
				}
			});
        });
		*/
		$(".fn_pop").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 630,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none',
            afterClose : function() {
                location.reload();
                return;
            }
	    });
	</script>
@stop