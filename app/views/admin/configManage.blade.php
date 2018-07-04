@extends('admin')
{{ $header->setMetaTitle( trans('configManage.page_title') ) }}
@section('content')
	<!-- BEGIN: ALERT BLOCK -->
    @if (Session::has('msg'))
        <div class="alert alert-success">{{ Session::get('msg') }}</div>
    @endif
    <!-- END: ALERT BLOCK -->
	<button type="Clear All Cache" name="clear_all_cache" value="clear_all_cache" class="btn btn-info responsive-btn-block mb10" onclick="AdminClearCache('clear_all')"><i class="fa fa-arrow-circle-right"></i> {{ trans('admin/indexSettings.clear_all_cache'); }}</button>
	<button type="Clear Setting cache" name="clear_setting_cache" value="clear_setting_cache" class="btn btn-info responsive-btn-block mb10" onclick="AdminClearCache('clear_setting')"><i class="fa fa-arrow-circle-right"></i> {{ trans('admin/indexSettings.clear_setting_cache'); }}</button>

    <!-- BEGIN: CONFIG MANAGE -->
    @if (!Request::ajax())
        <div class="config-mng">
            <div id="tabsview" class="clearfix mobile-tab">
                <button class="btn default btn-block mob-view"><i class="fa fa-chevron-down pull-right"></i> <i class="fa fa-list-ul"></i> View Menu List </button>
                <ul class="unstyled mob-config">
                     @foreach($tab_list_arr AS $key => $value)
                        @if($key!='logo')
                            <li><a href="config-manage?config_category={{$key}}">{{trans('configManage.'.$key)}}</a></li>
                        @else
                            <li><a href="config-manage?config_category={{$key}}">{{trans('configManage.'.$key)}}</a></li>
                        @endif
                     @endforeach
                </ul>
            </div>
        </div>
    @endif
    <!-- END: CONFIG MANAGE -->
@stop

@section('script_content')
	<script language="javascript">
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
        $(window).load(function(){
            $(function() {
                $("#tabsview").tabs({
                    ajaxOptions: {
                        error: function(xhr, status, index, anchor) {
                            $(anchor.hash).html("Couldn't load this tab. We'll try to fix this as soon as possible. If this wouldn't be a demo.");
                        }
                    }
                });
            });
        });

        function updateConfig(category, form_id, tab_index){
            $('#act_'+ category).val('config_update');
            $('html,body').animate({scrollTop: $("#tabsview").offset().top - 10}, 300);
            return postAjaxForm(form_id, tab_index);
        }

        function AdminClearCache(cache){
        	var post_url = "{{ URL::to('admin/clear-cache') }}";
            var cmsg ="";
            if(cache == 'clear_all')
            	cmsg ="Are you sure want to clear all cache?";
			else
				cmsg ="Are you sure want to clear setting cache?";
            bootbox.dialog({
                message: cmsg,
                title: cfg_site_name,
                buttons: {
                    danger: {
                        label: "Ok",
                        className: "btn-danger",
                        callback: function() {
                            var post_data = 'cache_options='+cache;
                            $.ajax({
                                type: 'POST',
                                url: post_url,
                                data: post_data,
                                success: function(data){
                                    bootbox.hideAll();
                                    location.reload();
                                }
                            });
                        }
                    },
                    success: {
                        label: "Cancel",
                        className: "btn-default",
                    }
                }
            });
            return false;
        };
    </script>
@stop
