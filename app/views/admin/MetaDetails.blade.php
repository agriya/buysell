@extends('admin')
@section('content')
	<!--- BEGIN: NOTIFICATIONS --->
    @include('notifications')
    <!--- END: NOTIFICATIONS --->

    <!--- BEGIN: ERROR INFO --->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!--- END: ERROR INFO --->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{Lang::get('admin/manageCategory.meta_details.meta_details')}}</h1>
    <!-- END: PAGE TITLE -->

	@if($enable_edit)
		{{ Form::model($meta_info, ['url' => Url::action('AdminDashboardController@postMetaDetails'), 'method' => 'post', 'id' => 'categoryEditfrm', 'class' => 'form-horizontal']) }}
	    	{{-- Form::open(array('url'=> Url::action('AdminDashboardController@postMetaDetails'), 'id'=>'categoryEditfrm', 'method'=>'get','class' => 'form-horizontal' )) --}}
			<div class="portlet box blue-madison">
				<!--- BEGIN: SEARCH TITLE --->
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-edit"></i> {{Lang::get('admin/manageCategory.meta_details.edit_meta_details')}} {{ isset($current_language)?' - '.$current_language:'' }} {{isset($meta_info['page_name'])?' - '.$meta_info['page_name']:''}}
					</div>
					<div class="tools">
						<a class="collapse" href="javascript:;"></a>
					</div>
				</div>
				<!--- END: SEARCH TITLE --->

				<div class="portlet-body form">
					<div class="form-body" id="search_holder">
                    	<div class="form-group">
							{{ Form::label('common_terms', Lang::get('admin/manageCategory.meta_details.common_terms'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-4">
								{{ Form::text('common_terms', null, array('class' => 'form-control', 'readonly')) }}
								<label class="error">{{{ $errors->first('common_terms') }}}</label>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('meta_title', Lang::get('admin/manageCategory.meta_details.meta_title'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-4">
								{{ Form::text('meta_title', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('meta_title') }}}</label>
							</div>
						</div>

						<div class="form-group">
							{{ Form::label('meta_keyword', Lang::get('admin/manageCategory.meta_details.meta_keyword'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-7">
								{{ Form::textarea('meta_keyword', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('meta_keyword') }}}</label>
							</div>
						</div>

						<div class="form-group">
							{{ Form::label('meta_description', Lang::get('admin/manageCategory.meta_details.meta_description'), array('class' => 'control-label col-md-3')) }}
							<div class="col-md-7">
								{{ Form::textarea('meta_description', null, array('class' => 'form-control')) }}
								<label class="error">{{{ $errors->first('meta_description') }}}</label>
							</div>
						</div>
					</div>

					<!-- BEGIN: SEARCH ACTIONS -->
					<div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-4">
							{{Form::hidden('id')}}
							<button type="submit" name="search_tax" value="search_tax" class="btn green">
							<i class="fa fa-arrow-up bigger-110"></i> {{ Lang::get('common.update') }}</button>
							<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminDashboardController@getMetaDetails') }}'"><i class="fa fa-times bigger-110"></i> {{ Lang::get('common.cancel') }}</button>
						</div>
					</div>
					<!-- END: SEARCH ACTIONS -->
				</div>
			 </div>
	    {{ Form::close() }}
    @else

    {{ Form::open(array('id'=>'PageSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!--- BEGIN: SEARCH TITLE --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{Lang::get('admin/manageCategory.meta_details.search_page_name')}}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- END: SEARCH TITLE --->

            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking">
                        <div class="form-group">
                            {{ Form::label('page_name', Lang::get('admin/manageCategory.meta_details.page_name'), array('class' => 'required-icon control-label col-md-3')) }}
                            <div class="col-md-4">
                                {{ Form::text('page_name', Input::get("page_name"), array('class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEGIN: SEARCH ACTIONS -->
                <div class="form-actions fluid">
                	<div class="col-md-offset-3 col-md-4">
                        <button type="submit" name="search_tax" value="search_tax" class="btn purple-plum">{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminDashboardController@getMetaDetails') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- END: SEARCH ACTIONS -->
            </div>
         </div>
    {{ Form::close() }}

    {{ Form::open(array('url' => 'admin/admin-update-language', 'method' => 'post', 'id' => 'language_frm', 'class' => 'form-inline pull-right','files' => 'true', 'enctype' => 'multipart/form-data')) }}
          <div class="form-group">
            {{ Form::label('current_language', trans("admin/manageCategory.meta_details.change_language"), array('class' => '')) }}
            {{ Form::select('current_language', $languages_list, $current_language, array ('class' => 'form-control bs-select input-medium', 'onchange' => 'ChangeLanguage()')); }}
          </div>
    {{ Form::close() }}
	<div class="clearfix"></div><br />
	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/manageCategory.meta_details.meta_details') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(sizeof($all_meta_list) > 0 )
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.page_name') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.meta_title') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.meta_keyword') }}</th>
                                <th>{{ Lang::get('admin/manageCategory.meta_details.meta_description') }}</th>
                                <th width="100">{{ Lang::get('admin/manageCategory.meta_details.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($all_meta_list as $meta_detail)
								<tr>
                            		<td>{{ ucfirst(str_replace('-', ' ', $meta_detail->page_name)) }}</td>
                                    <td>{{ $meta_detail->meta_title }}</td>
                                    <td>{{ $meta_detail->meta_keyword }}</td>
                                    <td>{{ $meta_detail->meta_description }}</td>
                                    <td class="status-btn">
                                        <a href="{{ URL::action('AdminDashboardController@getMetaDetails').'?id='.$meta_detail->id}}" class="btn btn-xs blue" title="{{ trans('common.edit') }}"><i class="fa fa-edit"></i></a>
                                    </td>
                            	</tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
                <!--- BEGIN: PAGINATION --->
                <div class="text-right">
                    {{ $all_meta_list->appends(array('page_name' => Input::get('page_name'), 'id' => Input::get('id'), 'action' => Input::get('action')))->links() }}
                </div>
                <!--- END: PAGINATION --->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/manageCategory.meta_details.no_page_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>
    @endif

	<div id="dialog-tax-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-tax-confirm-content" class="show ml15"></span>
	</div>
@stop
@section('script_content')
	<script type="text/javascript">
    $(document).ready(function() {
		$("#PageSearchfrm").validate({
			rules: {
				page_name: {
					required: true
				}
			},
			messages: {
				page_name: {
					required: mes_required
				}
			}
		});
	});

	function ChangeLanguage()
	{
		$.ajax({
			type: "POST",
			url: 'admin-update-language',
			data: $("#language_frm").serialize(),
			beforeSend:displayLoadingImage(),
			success : function(data)
			{
				hideLoadingImage (false);
				$(location).attr('href',"meta-details");
			}
		});
	}
	</script>
@stop