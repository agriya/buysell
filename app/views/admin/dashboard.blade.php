@extends('admin')
@section('content')
    <!--<a href="{{url('admin/group/add')}}" class="btn btn-xs btn-primary pull-right mt10"><i class="fa fa-plus"></i> {{trans('admin/manageGroups.create_group')}}</a>
    <h1 class="page-title">{{trans('admin/manageGroups.group_list')}}</h1>-->
     <!--- ERROR INFO STARTS --->
    @if (Session::has('error') && Session::get('error') != "")
        <div class="note note-danger">{{ Session::get('error') }}</div>
    @endif
    <!--- ERROR INFO END --->

    <!--- ERROR INFO STARTS --->
    @if (Session::has('success') && Session::get('success') != "")
        <div class="note note-success">{{ Session::get('success') }}</div>
    @endif
    <!--- ERROR INFO END --->
  <?php
  	$today = date('Y-m-d');
  	$day = date('w');
  	$this_week_start_date = date('Y-m-d', strtotime('-'.$day.' days'));
  	$this_month_start_date = date('Y-m-d', strtotime('first day of this month'));
  ?>

    <!--- DASHBOARD BLOCK STARTS --->
    <h3 class="page-title">{{Lang::get('admin/dashboard.dashboard')}}</h3>
    <div class="row custom-dashboard">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue-madison">
                <div class="visual"> <i class="fa fa-users"></i></div>
                <div class="details">
                    <div class="number"> {{isset($member_stats_details['total_members'])?$member_stats_details['total_members']:0}} </div>
                    <div class="desc"> {{Lang::get('admin/dashboard.total_members')}} </div>
                </div>
                <a href="{{Url::action('AdminUserController@index')}}" class="more"> {{Lang::get('admin/dashboard.members')}} <i class="m-icon-swapright m-icon-white"></i> </a>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat green-turquoise">
                <div class="visual"><i class="fa fa-check-square-o"></i></div>
                <div class="details">
                    <div class="number">{{isset($member_stats_details['activated_users'])?$member_stats_details['activated_users']:0}}</div>
                    <div class="desc">{{Lang::get('admin/dashboard.activated')}}</div>
                </div>
                <a href="{{Url::to('admin/users?status=active')}}" class="more">{{Lang::get('admin/dashboard.members')}} <i class="m-icon-swapright m-icon-white"></i> </a>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat grey-cascade">
                <div class="visual"> <i class="fa fa-calendar"></i> </div>
                <div class="details">
                    <div class="number"> {{isset($member_stats_details['joined_today'])?$member_stats_details['joined_today']:0}} </div>
                    <div class="desc"> {{Lang::get('admin/dashboard.joined_today')}} </div>
                </div>
                <a href="{{Url::to('admin/users?from_date='.$today)}}" class="more"> {{Lang::get('admin/dashboard.members')}} <i class="m-icon-swapright m-icon-white"></i> </a>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat purple-plum">
                <div class="visual"><i class="fa fa-calendar-o"><span>W</span></i> </div>
                <div class="details">
                    <div class="number">{{isset($member_stats_details['joined_thisweek'])?$member_stats_details['joined_thisweek']:0}}</div>
                    <div class="desc">{{Lang::get('admin/dashboard.this_week')}}</div>
                </div>
                <a href="{{Url::to('admin/users?from_date='.$this_week_start_date.'&to_date='.$today)}}" class="more"> {{Lang::get('admin/dashboard.members')}} <i class="m-icon-swapright m-icon-white"></i> </a>
              </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue-hoki">
                <div class="visual"> <i class="fa fa-calendar-o"><span>M</span></i> </div>
                <div class="details">
                    <div class="number"> {{isset($member_stats_details['joined_thismonth'])?$member_stats_details['joined_thismonth']:0}} </div>
                    <div class="desc"> {{Lang::get('admin/dashboard.this_month')}} </div>
                </div>
                <a href="{{Url::to('admin/users?from_date='.$this_month_start_date.'&to_date='.$today)}}" class="more"> {{Lang::get('admin/dashboard.members')}} <i class="m-icon-swapright m-icon-white"></i> </a>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-md-6 col-sm-6">
            <div class="portlet box red-sunglo">
                  <!--- title starts --->
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-globe"></i> {{Lang::get('admin/dashboard.marketplace_stats')}} </div>
                </div>
                <!--- title end --->
                <div class="portlet-body">
                    <div class="tiles custom-tiles">
                        <div class="tile">
                            <div class="tile-body font-blue-madison"> <i class="fa fa-shopping-cart"></i> </div>
                            <div class="tile-object">
                                <div class="name"><a class="text-muted" href="{{ Url::to('admin/product/list') }}">{{Lang::get('admin/dashboard.total_items')}}</a></div>
                                <div class="number"><a href="{{ Url::to('admin/product/list') }}">{{isset($product_stats_details['total_products'])?$product_stats_details['total_products']:0}}</a></div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile-body font-green"> <i class="fa fa-check-square"></i> </div>
                            <div class="tile-object">
                                <div class="name">
                                	<a class="text-muted" href="{{ Url::to('admin/product/list?search_product_status=ToActivate') }}">{{Lang::get('admin/dashboard.to_be_activated')}}</a>
                                	<!--Items to be activated-->
                                </div>
                                <div class="number"><a href="{{ Url::to('admin/product/list?search_product_status=ToActivate') }}">{{isset($product_stats_details['inactive_products'])?$product_stats_details['inactive_products']:0}}</a></div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile-body font-blue-hoki"> <i class="fa fa-calendar"></i> </div>
                            <div class="tile-object">
                                <div class="name">
                                	<a class="text-muted" href="{{ Url::to('admin/product/list?search_product_from_date='.$today) }}">{{Lang::get('admin/dashboard.added_today')}}</a>
                                    <!--Items added today-->
                                </div>
                                <div class="number"><a href="{{ Url::to('admin/product/list?search_product_from_date='.$today) }}">{{isset($product_stats_details['added_today'])?$product_stats_details['added_today']:0}}</a></div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile-body font-green-meadow"> <i class="fa fa-calendar-o"><span>W</span></i> </div>
                            <div class="tile-object">
                                <div class="name">
                                    <a class="text-muted" href="{{ Url::to('admin/product/list?search_product_from_date='.$this_week_start_date.'&search_product_to_date='.$today) }}">{{Lang::get('admin/dashboard.this_week')}}</a>
                                </div>
                                <div class="number">
                                    <a href="{{ Url::to('admin/product/list?search_product_from_date='.$this_week_start_date.'&search_product_to_date='.$today) }}">{{isset($product_stats_details['added_thisweek'])?$product_stats_details['added_thisweek']:0}}</a>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile-body font-purple-studio"> <i class="fa fa-calendar-o"><span>M</span></i> </div>
                            <div class="tile-object">
                                <div class="name">
                                    <a class="text-muted" href="{{ Url::to('admin/product/list?search_product_from_date='.$this_month_start_date.'&search_product_to_date='.$today) }}">{{Lang::get('admin/dashboard.this_month')}}</a>
                                </div>
                                <div class="number">
                                    <a href="{{ Url::to('admin/product/list?search_product_from_date='.$this_month_start_date.'&search_product_to_date='.$today) }}">{{isset($product_stats_details['added_thismonth'])?$product_stats_details['added_thismonth']:0}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-6">
            <div class="portlet box green-haze">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-money"></i> {{Lang::get('admin/dashboard.site_earnings')}}</div>
                </div>
                <div class="portlet-body">
                    <div class="tiles custom-tiles">
                        <div class="tile">
                            <div class="tile-body font-blue-steel"> <i class="fa fa-dollar"></i> </div>
                            <div class="tile-object">
                                <div class="name">
                                	<a class="text-muted" href="{{ Url::to('admin/site-wallet/index') }}">{{Lang::get('admin/dashboard.total')}}</a>
                                	<!--Total site earnings-->
                                </div>
                                <div class="number">
                                	<a class="text-bold" href="{{ Url::to('admin/site-wallet/index') }}">{{ Config::get('generalConfig.site_default_currency') }} {{isset($account_balance_arr['amount'])?$account_balance_arr['amount']:0}}</a>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile-body font-grey-cascade"> <i class="fa fa-calendar"></i> </div>
                            <div class="tile-object">
                                <div class="name"><a class="text-muted" href="{{ Url::action('AdminPurchasesController@getIndex').'?from_date='.date('Y-m-d').'&to_date='.date('Y-m-d').'&order_status=payment_completed' }}">{{Lang::get('admin/dashboard.today_sales')}}</a></div>
                                <div class="number">
                                	<a href="{{ Url::action('AdminPurchasesController@getIndex').'?from_date='.date('Y-m-d').'&to_date='.date('Y-m-d').'&order_status=payment_completed' }}">{{ Config::get('generalConfig.site_default_currency') }} {{isset($site_earning_details['todays_earning'])?$site_earning_details['todays_earning']:0}}</a>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile-body font-red-sunglo"><i class="fa fa-credit-card"></i></div>
                            <div class="tile-object">
                                <div class="name">
                                	<a class="text-muted" href="{{ Url::action('AdminPurchasesController@getIndex').'?order_status=not_paid'}}">{{Lang::get('admin/dashboard.pending')}}</a>
                                	<!--Pending withdrawals-->
                                </div>
                                <div class="number">
                                    <a href="{{ Url::action('AdminPurchasesController@getIndex').'?order_status=not_paid'}}">{{ Config::get('generalConfig.site_default_currency') }} {{isset($site_earning_details['pending_payment'])?$site_earning_details['pending_payment']:0}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
    <!--- DASHBOARD BLOCK END --->

    <!--<div id="sellatestNews">
	  <div class="portlet box blue-madison">
	    <div class="portlet-title">
	      <div class="caption"><i class="fa fa-rss-square"></i> Latest News</div>
	      <div class="pull-right"> <a class="btn default purple-stripe btn-xs" href="#">More... <i class="fa fa-arrow-right"></i></a> </div>
	    </div>
	    <div class="portlet-body">
	      <div id="selReportBlock"></div>
	      <div class="note note-info" id="selMsgAlert"> No records found </div>
	    </div>
	  </div>
	</div>-->
@stop