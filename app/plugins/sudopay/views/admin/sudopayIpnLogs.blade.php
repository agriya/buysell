@extends('admin')
@section('content')
	<!-- BEGIN: INCLUDE NOTIFICATIONS -->
    @include('notifications')
    <!-- END: INCLUDE NOTIFICATIONS -->

    <!-- BEGIN: INFO BLOCK -->
	@if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
    <!-- END: INFO BLOCK -->

	<div class="portlet box blue-hoki">
        <!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-database sudo-icon"></i> {{ trans('sudopay::sudopay.sudopay_ipn_logs') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(sizeof($ipn_log_list) > 0 )
                <div class="table-scrollable">
                    <table class="table table-bordered table-hover table-striped">
                    	<thead>
                            <tr>
                                <th width="150">{{ trans('sudopay::sudopay.added_on') }}</th>
                                <th width="150">{{ trans('sudopay::sudopay.ip') }}</th>
                                <th>{{ trans('sudopay::sudopay.post_variable') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ipn_log_list as $log)
                                <tr>
                                    <td class="text-muted">{{ CUtil::FMTDate($log->created, 'Y-m-d H:i:s', '') }}</td>
                                    <td>{{ $log->ip }}</td>
                                    <?php
										$post_var = unserialize($log->post_variable);
										$post_var_arr =  http_build_query($post_var, ',', '&');
									?>
                                    <td><div class="ipn-logvar">{{ $post_var_arr }}</div></td>
                                </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>

				<div class="clearfix">
					<!-- BEGIN: PAGINATION -->
					<div class="text-right">
						{{ $ipn_log_list->appends(array())->links() }}
					</div>
					<!-- END: PAGINATION -->
				</div>
            @else
                <div class="alert alert-info mar0">{{ trans('sudopay::sudopay.sudopay_ipn_logs_none_err_msg') }}</div>
            @endif
    	</div>
    </div>

    <div id="dialog-product-confirm" title="" style="display:none;">
        <span class="ui-icon ui-icon-alert"></span>
        <span id="dialog-product-confirm-content" class="show ml15"></span>
    </div>
@stop

@section('script_content')
	<script type="text/javascript">
	</script>
@stop