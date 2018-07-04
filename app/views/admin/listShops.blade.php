@extends('admin')
@section('content')
	<!--- SUCCESS INFO STARTS --->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif
    <!--- SUCCESS INFO END --->

    <!--- ERROR INFO STARTS --->
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
    @endif
    <!--- ERROR INFO END --->

    <!--- SEARCH BLOCK STARTS --->
    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal search-bar' )) }}
        <div class="portlet box blue-madison">
            <!--- SEARCH TITLE STARTS --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/manageShops.shoplist_search_shops') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- SEARCH TITLE END --->

            <div id="search_holder" class="portlet-body form">
                <div id="selSrchScripts">
                    <div class="form-body">
                        <div class="form-group">
                            {{ Form::label('shop_name', trans('admin/manageShops.shoplist_shop_name'), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                                {{ Form::text('shop_name', Input::get("shop_name"), array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('shop_featured', trans('admin/manageShops.shoplist_featured'), array('class' => 'col-md-3 control-label')) }}
                            <div class="col-md-4">
                                {{ Form::select('shop_featured', array('' => trans('common.all'), 'Yes' => trans('common.yes'), 'No' => trans('common.no')), Input::get("shop_featured"), array('class' => 'form-control select2me input-medium')) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-offset-3">
                            <button type="submit" name="search_shops" value="search_shops" class="btn purple-plum">
                                {{ trans("common.search") }} <i class="fa fa-search"></i>
                            </button>
                            <button type="reset" name="reset_members" value="reset_members" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'">
                                <i class="fa fa-rotate-left"></i> {{ trans("common.reset")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{ Form::close() }}
    <!--- SEARCH BLOCK END --->

    <div class="portlet box blue-hoki">
        <!--- TABLE TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ trans('admin/manageShops.shoplist_page_title') }}
            </div>
        </div>
        <!--- TABLE TITLE END --->

        <!--- SHOP LIST STARTS --->
        <div class="portlet-body">
            {{ Form::open(array('id'=>'memberListfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('admin/manageShops.shoplist_shop_details') }} </th>
                                <th>{{ trans('admin/manageShops.shoplist_user_details') }} </th>
                                <th>{{ trans('admin/manageShops.shoplist_product_count') }} </th>
                                <th>{{ trans('admin/manageShops.shoplist_featured') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($shop_details) > 0)
                                @foreach($shop_details as $reqKey => $shop)
                                    <tr>
                                        <td>
                                            <p><strong>{{ $shop['shop_name'] }} </strong></p>
                                            @if($shop['shop_city'] != '' && $shop['shop_state'] != '' && $shop['shop_country'] != '')
                                                <p>{{{ $shop['shop_city'] }}}, {{{ $shop['shop_state'] }}}, {{{ $country_arr[$shop['shop_country']] }}}</p>
                                            @elseif($shop['shop_state'] != '' && $shop['shop_country'] != '')
                                                <p>{{{ $shop['shop_state'] }}}, {{{ $shop['shop_country'] }}}</p>
                                            @elseif($shop['shop_country'] != '')
                                                <p>{{{ $country_arr[$shop['shop_country']] }}}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                                $user_details = CUtil::getUserDetails($shop['user_id']);
                                                $total_products = 0;
                                                $usr_shop_details = $shop_obj->getUsersShopDetails($shop['user_id']);
                                                if($usr_shop_details) {
                                                    $total_products = $usr_shop_details['total_products'];
                                                }
                                            ?>
                                            <p><strong>{{ $user_details['first_name'] }} {{ $user_details['last_name'] }}</strong></p>
                                            <p><a href="mailto:{{ $user_details['email'] }}">{{ $user_details['email'] }}</a></p>
                                            <p title="User Code / User ID" class="grey">{{ BasicCUtil::setUserCode($shop['user_id']) }} / {{ $shop['user_id'] }}</p>
                                        </td>
                                        <td>{{ $total_products }}</td>
                                        <td title="User Status" class="status-btn">
                                            @if(strtolower($shop['is_featured_shop']) == "yes")
                                                <i class="icon-ok bigger-150 green" title='{{ ucwords(str_replace("_", " ", "Active")) }}'></i>
                                                <p>
                                                    <a href="{{ URL::to('admin/shop/changestatus').'?action=removefeatured&shop_id='.$shop['id'] }}" class="fn_dialog_confirm red" action="Remove Featured" title="{{ trans('admin/manageShops.shoplist_remove_featured') }}">{{ trans('admin/manageShops.shoplist_remove_featured') }} </a>
                                                </p>
                                            @else
                                                <i class="icon-ban-circle bigger-150 red" title='{{ ucwords(str_replace("_", " ", "ToActivate")) }}'></i>
                                                <p>
                                                    <a href="{{ URL::to('admin/shop/changestatus').'?action=setfeatured&shop_id='.$shop['id'] }}" class="fn_dialog_confirm green" action="Set Featured" title="{{ trans('admin/manageShops.shoplist_set_featured') }}">{{ trans('admin/manageShops.shoplist_set_featured') }} </a>
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <p class="alert alert-info">{{ trans('admin/manageShops.shoplist_none_err_msg') }} </p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(count($shop_details) > 0)
                    <div class="text-right">{{ $shop_list->appends(array('shop_name' => Input::get('shop_name'), 'shop_featured' => Input::get('shop_featured'), 'search_shops' => Input::get('search_shops')))->links() }}</div>
                @endif
            {{ Form::close() }}
        </div>
        <!--- SHOP LIST END --->
    </div>
	<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script type="text/javascript">
		$(".fn_viewgeo").fancybox({
			maxWidth    : 800,
			maxHeight   : 430,
			fitToView   : false,
			width       : '70%',
			height      : '430',
			autoSize    : false,
			closeClick  : false,
			type        : 'iframe',
			openEffect  : 'none',
			closeEffect : 'none'
		});
		var common_ok_label = "{{ trans('common.yes') }}" ;
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var cfg_package_name = "{{ Config::get('generalConfig.site_name') }}" ;
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
					case "Set Featured":
						cmsg = "{{ trans('admin/manageShops.shoplist_feature_confirm') }}";

						break;
					case "Remove Featured":
						cmsg = "{{ trans('admin/manageShops.shoplist_remove_feature_confirm') }}";
						break;
				}
				$("#fn_dialog_confirm_msg").html(cmsg);
				$("#fn_dialog_confirm_msg").dialog({
					resizable: false,
					height:140,
					width: 320,
					modal: true,
					title: cfg_package_name,
					buttons:buttonText
				});
				return false;
			});
		});
	</script>
@stop