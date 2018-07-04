@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
    <!-- BEGIN: ALERT MESSAGE -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{   Session::get('success_message') }}</div>
    @endif
    @if (Session::has('warning_message') && Session::get('warning_message') != "")
        <div class="note note-warning">{{   Session::get('warning_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{    Session::get('error_message') }}</div>
    @endif
	<!-- END: ALERT MESSAGE -->

    <div class="portlet box blue-madison">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				{{$d_arr['actionicon']}} {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: BANNER SETTINGS FORM -->
            {{ Form::model($d_arr['banner_details'], ['method' => 'post', 'id' => 'banner_frm', 'class' => 'form-horizontal']) }}
            	{{ Form::hidden('banner_id', $d_arr['id']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('block') ? 'error' : '' }}}">
						{{ Form::label('block', trans("admin/manageSiteBanner.block"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							{{ Form::text('block', null, array ('class' => 'form-control mb10')); }}
							<div class="note note-info mar0">
								<p class="text-muted"><strong>{{ trans("admin/manageSiteBanner.banner_block_det") }}</strong></p>
								<ul class="list-unstyled">
									@if(count($banner_block) > 0)
								        @foreach($banner_block as $name => $size)
											<li class="mb5">
												<span class="text-muted mr5">&raquo;</span>{{ $name }} - <strong>{{ $size }} {{ trans("admin/manageSiteBanner.size") }}</strong>
											</li>
								        @endforeach
								    @endif
								</ul>
								<p>{{ trans("admin/manageSiteBanner.note_for_banner_block_size") }}</p>
							</div>
							<!-- <p class="mt5 mb5"><a class="btn btn-info btn-xs fn_blockview" title="{{ trans('admin/manageSiteBanner.banner_details') }}" onclick="showPopUp('block', 0)">{{ trans('admin/manageSiteBanner.view_banner_name') }}</a></p> -->
							<label class="error">{{{ $errors->first('block') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('source') ? 'error' : '' }}}">
						{{ Form::label('source', trans("admin/manageSiteBanner.source"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							{{ Form::textarea('source', null, array ('class' => 'form-control', 'rows'=>'7', 'id' => 'source')); }}
							<p class="mt5 mb5"><a class="btn btn-xs btn-info fn_view" title="{{ trans('admin/manageSiteBanner.preview') }}" onclick="previewBanner('source')">
							<i class="fa fa-eye"></i> {{ trans('admin/manageSiteBanner.preview') }}</a></p>
							<label class="error">{{{ $errors->first('source') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('about') ? 'error' : '' }}}">
						{{ Form::label('about', trans("admin/manageSiteBanner.about"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							{{ Form::textarea('about', null, array ('class' => 'form-control', 'rows'=>'7')); }}
							<label class="error">{{{ $errors->first('about') }}}</label>
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('start_date', trans("admin/manageSiteBanner.start_date"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
								{{ Form::text('start_date', Input::get("start_date"), array('id' => 'start_date', 'class' => 'form-control valid start_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
								<span class="input-group-btn">
									<label class="btn default" for="start_date"><i class="fa fa-calendar"></i></label>
								</span>
							</div>
							<label class="error" for="start_date" generated="true">{{{ $errors->first('start_date') }}}</label>
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('end_date', trans("admin/manageSiteBanner.end_date"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
								{{ Form::text('end_date', Input::get("end_date"), array('id' => 'end_date', 'class' => 'form-control valid end_date', 'data-date-format' => "yyyy-mm-dd", "readonly")) }}
								<span class="input-group-btn">
									<label class="btn default" for="end_date"><i class="fa fa-calendar"></i></label>
								</span>
							</div>
							<label class="error" for="end_date" generated="true">{{{ $errors->first('end_date') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('allowed_impressions') ? 'error' : '' }}}">
						{{ Form::label('allowed_impressions', trans("admin/manageSiteBanner.allowed_impressions"), array('class' => 'col-md-3 control-label ')) }}
						<div class="col-md-4">
							{{ Form::text('allowed_impressions', null, array ('class' => 'form-control input-small')); }}
							<label class="error">{{{ $errors->first('allowed_impressions') }}}</label>
							<label><small class="text-muted">Allowed count for banner display, Set <strong>0</strong> for unlimited</small></label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                        {{ Form::label('status', trans("common.status"), array('class' => 'col-md-3 control-label required-icon')) }}
                        <div class="col-md-4">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {{Form::radio('status', 'Active', null, array('class' => 'radio', 'checked')) }}
                                    <label>{{ trans("admin/manageSiteBanner.activate") }}</label>
                                </label>
                                <label class="radio-inline">
                                    {{Form::radio('status', 'Inactive', null , array('class' => 'radio')) }}
                                    <label>{{ trans("admin/manageSiteBanner.deactivate") }}</label>
                                </label>
                            </div>
                            <label class="error">{{{ $errors->first('status') }}}</label>
                        </div>
                    </div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						@if($d_arr['mode'] == 'edit')
							<button type="submit" name="edit_banner" class="btn green" id="edit_banner" value="edit_banner">
								<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                            </button>
						@else
							<button type="submit" name="add_banner" class="btn green" id="add_banner" value="add_banner">
								<i class="fa fa-check"></i> {{trans("common.submit")}}
                            </button>
						@endif
						<button type="reset" name="reset_banner" class="btn default" onclick="window.location = '{{ url::to('admin/manage-banner') }}'">
							<i class="fa fa-times"></i> {{trans("common.cancel")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: BANNER SETTINGS FORM -->
        </div>
    </div>
    {{ Form::model($d_arr['banner_details'], [
        'method' => 'get',
        'id' => 'siteBannerList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption">{{$d_arr['actionicon']}} {{ trans('admin/manageSiteBanner.list_banner') }}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: BANNER SETTINGS TABLE -->
            <div class="portlet-body clearfix">
            	<div class="text-right"><p class="label label-default">{{ str_replace('VAR_ICON', '<span class="label label-primary fn_blockview" title="Code"><i class="fa fa-code"></i></span>',  trans("admin/manageSiteBanner.to_get_code")) }}</p></div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <tr>
                                <th>{{ trans('admin/manageSiteBanner.block') }}</th>
                                <th>{{ trans('admin/manageSiteBanner.allowed_impressions') }}</th>
								<th>{{ trans('admin/manageSiteBanner.completed_impressions') }}</th>
                                <th>{{ trans('admin/manageSiteBanner.post_by') }}</th>
                                <th>{{ trans('common.status') }}</th>
                                <th>{{ trans('common.date') }}</th>
                                <th>{{ trans('common.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
							@if(count($details) > 0)
	                            @foreach($details as $banner)
	                                <tr>
	                                    <td><div class="wid100">{{ $banner->block }}</div></td>
	                                    <td>{{ $banner->allowed_impressions }}</td>
	                                    <td>{{ $banner->completed_impressions }}</td>
	                                    <td>
	                                    	<?php
	                                    		$user_details = CUtil::getUserDetails($banner->user_id);
	                                    	?>
											<p><a href="{{ $user_details['profile_url'] }}" class="show">{{ $user_details['display_name'] }}</a></p>
											<p>{{ trans('common.on') }} {{ CUtil::FMTDate($banner->date_added, 'Y-m-d H:i:s', '') }}</p>
										</td>
										<td>
											<?php
												if(count($banner) > 0) {
													if($banner['status'] == 'Active') {
														$lbl_class = "label-success";
													}
														elseif($banner['status'] == 'Inactive') {
															$lbl_class = "label-danger";
													}
												else
													{ $lbl_class = "label-default"; }
												}
											?>
											<span class="label {{ $lbl_class }}">{{ $banner->status }}</span>
										</td>
										<td>
											<div class="wid100">
												<p class="text-muted">{{ trans('admin/manageSiteBanner.start_date') }}</p>
												<p class="margin-bottom-10">{{ CUtil::FMTDate($banner->start_date, 'Y-m-d H:i:s', '') }}</p>

												<p class="text-muted">{{ trans('admin/manageSiteBanner.end_date') }}</p>
												<p>{{ CUtil::FMTDate($banner->end_date, 'Y-m-d H:i:s', '') }}</p>
											</div>
										</td>
	                                    <td class="status-btn">
											<div class="wid100">
												<a class="btn blue btn-xs" title="{{trans('common.edit') }}" href="{{ url::to('admin/manage-banner')}}?id={{$banner->add_id}}">
												<i class="fa fa-edit"></i></a>
												{{ Form::hidden('selPreview_'.$banner->add_id, $banner->source, array ('class' => 'form-control', 'id' => 'selPreview_'.$banner->add_id)) }}
												<a class="btn btn-xs btn-info fn_view" title="{{ trans('admin/manageSiteBanner.preview') }}" onclick="previewBanner('selPreview_{{$banner->add_id}}')"><i class="fa fa-eye"></i></a>
													<a class="btn btn-primary btn-xs fn_blockview" title="{{ trans('admin/manageSiteBanner.code') }}" onclick="showPopUp('code', '{{ $banner->add_id }}')"><i class="fa fa-code"></i></a>
												<a class="btn red btn-xs fn_dialog_confirm red" title="{{trans('common.delete') }}" href="{{ url::to('admin/manage-banner/delete-banner')}}?id={{$banner->add_id}}"><i class="fa fa-trash-o"></i></a>
											</div>
	                                    </td>
	                                </tr>
	                            @endforeach
                            @else
                                <tr>
                                    <td colspan="7"><p class="alert alert-info">{{ trans('admin/manageSiteBanner.no_result_found') }}</p></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

				<!--- BEGIN: PAGINATION --->
				@if(count($details) > 0)
					<div class="dataTables_paginate paging_bootstrap text-right">
						{{ $details->appends(array())->links() }}
					</div>
				@endif
				<!--- END: PAGINATION --->
            </div>
            <!--  END: BANNER SETTINGS TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";
        $("#banner_frm").validate({
            rules: {
                block: {
                    required: true
                },
                source: {
                    required: true
                },
                about: {
                    required: true
                },
                start_date: {
                    required: true
                },
                end_date: {
                    required: true
                },
                allowed_impressions: {
                    digits: true
                },
                status: {
                    required: true
                }
            },
            messages: {
                block: {
                    required: mes_required
                },
                source: {
                    required: mes_required
                },
                about: {
                    required: mes_required
                },
                start_date: {
                    required: mes_required
                },
                end_date: {
                    required: mes_required
                },
                status: {
                    required: mes_required
                }
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

        var common_ok_label = "{{ trans('common.yes') }}" ;
            var common_no_label = "{{ trans('common.cancel') }}" ;
            var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
            $(window).load(function(){
             $(".fn_dialog_confirm").click(function(){

                    var atag_href   = $(this).attr("href");
                    var cmsg        = "{{ trans('admin/manageSiteBanner.want_to_delete_banner') }}";
                    var txtCancel = common_no_label;
                    var buttonText = {};
                    buttonText["{{ trans('common.yes') }}"] = function(){
                                                Redirect2URL(atag_href);
                                                $( this ).dialog( "close" );
                                            };
                    buttonText["{{ trans('common.no') }}"] = function(){
                                                $(this).dialog('close');
                                            };



                    $("#fn_dialog_confirm_msg").html(cmsg);
                    $("#fn_dialog_confirm_msg").dialog({
                        resizable: false,
                        height:240,
                        width: 360,
                        modal: true,
                        title: cfg_site_name,
                        buttons:buttonText
                    });
                    return false;
                });

            });

        $(function() {
            $('.start_date').datepicker({
                format: 'dd/mm/yyyy',
				todayHighlight: true,
				autoclose: true,
				"minDate": 0,
				onSelect: function(selected) {
				  $('.end_date').datepicker("option","minDate", selected)
				}
            });

            $('.end_date').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
	        	onSelect: function(selected) {
				  //$('.start_date').datepicker("option","maxDate", selected)
				}
            });
        });

        function previewBanner(field)
        {
        	var content = $("#"+field).val();
        	if(content == '')
        	{
        		return false;
        	}
        	$(".fn_view").fancybox({
		        maxWidth    : 800,
		        maxHeight   : 430,
		        fitToView   : false,
		        autoSize    : true,
		        closeClick  : false,
		        type        : 'iframe',
		        content		: content,
		        openEffect  : 'none',
		        closeEffect : 'none'
		    });
        }

        function showPopUp(type, id)
		{
			if(type == 'block')
			{
				var fancybox_url = '{{URL::action('AdminSiteBannerController@getViewBannerPositions')}}';
			}
			else
			{
				var fancybox_url = '{{URL::action('AdminSiteBannerController@getBannerCode')}}?id='+id;
			}
			$(".fn_blockview").fancybox({
		        maxWidth    : 800,
		        maxHeight   : 430,
		        fitToView   : false,
		        autoSize    : false,
		        width       : '70%',
				height      : '432',
		        closeClick  : false,
		        type        : 'iframe',
		        href        : fancybox_url,
		        openEffect  : 'none',
		        closeEffect : 'none'
		    });
        }


    </script>
@stop