@extends('admin')
@section('content')
	@if(Input::get('i') == 'invoice')
        <a href="{{ Url::to('admin/unpaid-invoice-list/index') }}" class="btn default purple-stripe btn-xs pull-right mt5"><i class="fa fa-arrow-left"></i> {{trans('admin/unpaidInvoiceList.unpaid_invoices_list')}}</a>
   	@else
        <a href="{{ Url::to('admin/users') }}" class="btn default purple-stripe btn-xs pull-right mt5">
            <i class="fa fa-arrow-left"></i> {{trans('admin/manageMembers.memberlist_page_title')}}
        </a>
   	@endif

    {{--<h1 class="page-title">{{ Lang::get('admin/manageMembers.viewmember_user_profile_details') }}</h1>--}}

    <!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{ trans('admin/manageMembers.profile_details') }}</h1>
    <!-- END: PAGE TITLE -->

	@if(count($user_details) <= 0)
    	<!-- BEGIN: INFO BLOCK -->
        <div class="note note-info">
           {{ Lang::get('myInvoice.invalid_id') }}
        </div>
        <!-- END: INFO BLOCK -->
    @else
        @if(count($user_details) > 0)
        	<div class="adm-usrdet">
            	<div class="row">
					<?php
                        $name = BasicCUtil::getUserGroupName($user_details->group_id);
                        $user_account_balance = CUtil::getUserAccountBalance($user_details->user_id);
                        $total_credit_amount = DB::table('common_invoice')->where('user_id',$user_details->user_id)
                                                ->where('reference_type','=','Credits')
                                                ->where('status', '=', 'Unpaid')
                                                ->SUM('amount');
                        $user_image = CUtil::getUserPersonalImage($user_details->user_id, "large", false);
                    ?>

                    <!-- BEGIN: USER DETAILS -->
                    <div class="col-md-8 col-sm-8 col-xs-12 mb20">
                        <div class="admin-dl clearfix">
                        	<div class="well-new">
                                <h3>{{ trans('admin/manageMembers.viewmember_user_details_title') }}</h3>

                                <ul class="list-unstyled profile-image">
                                    <li>
                                        <a class="profile-img text-center" href="javascript:void(0);">
                                            <img src="{{ $user_image['image_url'] }}" {{ $user_image['image_attr'] }} alt="{{ $user_details->user_name }}" title="{{ $user_details->user_name }}" />
                                        </a>
                                    </li>
                                </ul>

                                <div class="dl-horizontal invoice-list mb20 clearfix">
                                    <dl>
                                        <dt>{{ trans('admin/manageMembers.viewmember_first_name') }}</dt> 
                                        <dd><span>{{ $user_details->first_name }}</span></dd>
                                    </dl>
                                    
                                    <dl>
                                        <dt>{{ trans('admin/manageMembers.viewmember_last_name') }}</dt>
                                        <dd><span>{{ $user_details->last_name }}</span></dd>
                                    </dl>
                                    
                                    <dl>
                                        <dt>{{ trans('admin/manageMembers.viewmember_user_name') }}</dt> 
                                        <dd><span>{{ $user_details->user_name }}</span></dd>
                                    </dl>
                                    
                                    <dl>
                                        <dt>{{ trans('admin/manageMembers.viewmember_email') }}</dt> 
                                        <dd><span title="{{ $user_details->email }}">{{ $user_details->email }}</span></dd>
                                    </dl>
                                    
                                    <dl>
                                        <dt>{{ trans('admin/manageMembers.viewmember_group_name') }}</dt> 
                                        <dd><span>{{ $name }}</span></dd>
                                    </dl>
                                    
                                    @if($user_details->is_banned == 1)
										<dl>
                                            <dt>{{ trans('admin/manageMembers.viewmember_status') }}</dt> 
                                            <dd><span class="label label-danger">{{ trans('admin/manageMembers.viewmember_blocked') }}</span></dd>
                                        </dl>
									@elseif($user_details->is_banned == 0 && $user_details->activated == 1)
										<dl>
                                            <dt>{{ trans('admin/manageMembers.viewmember_status') }}</dt> 
                                            <dd><span class="label label-success">{{ trans('admin/manageMembers.viewmember_active') }}</span></dd>
                                        </dl>
									@elseif($user_details->activated == 0)
										<dl>
                                            <dt>{{ trans('admin/manageMembers.viewmember_status') }}</dt> 
                                            <dd><span class="label label-default">{{ trans('admin/manageMembers.viewmember_inactive') }}</span></dd>
                                        </dl>
									@endif
                                    <!-- <dl><dt>Account Balance</dt> <dd><span>{{ $user_account_balance['currency'] }} {{ $user_account_balance['amount'] }}</span></dd></dl> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END: USER DETAILS -->

                    <?php
                        $shop_obj = Products::initializeShops();
                        $shop_obj->setIncludeBlockedUserShop(true);
                        $shop_details = $shop_obj->getShopDetails($user_details->id);
                        $owner_name = CUtil::getUserDetails($user_details->id);
                    ?>

                    <!-- BEGIN: SELLER DETAILS -->
                    @if(isset($user_details->is_shop_owner) && $user_details->is_shop_owner == 'Yes')
                        @if(isset($user_details->is_allowed_to_add_product) && $user_details->is_allowed_to_add_product == 'Yes')
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="well-new">
                                    <div class="clearfix">
                                        <h3 class="pull-left">{{ Lang::get('common.seller_details')}} {{ CUtil::showFeaturedSellersIcon($user_details->user_id, $shop_details) }}</h3>
                                    </div>
                                    <div>
                                        <dl>
                                            <dt>{{ Lang::get('admin/manageMembers.shop_owner')}}</dt>
                                            <dd><a href="{{ $shop_details['shop_url'] }}" class="text-primary">{{ $owner_name['display_name'] }}</a></dd>
                                        </dl>
                                        <dl>
                                            <dt>{{ Lang::get('admin/manageMembers.shop_name')}}</dt>
                                            <dd><a href="{{ $shop_details['shop_url'] }}" class="text-primary">{{ $shop_details['shop_name'] }}</a></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    <!-- END: SELLER DETAILS -->
                </div>
            </div>

            <!-- BEGIN: ACCOUNT BALANCE -->
            @if(isset($d_arr['main_currency_arr']) && count($d_arr['main_currency_arr']) > 0)
                <div class="row userdet-accbal">
                    @foreach($d_arr['main_currency_arr'] as $curr => $acbal)
                        @if($curr == Config::get('generalConfig.site_default_currency'))
                            @if(round($acbal['amount']) == 0)
                                <div class="alert alert-danger">
                                    <span>{{ Lang::get('common.account_balance')}} <small class="text-muted">({{ trans('myaccount/form.my-withdrawals.available_to_withdraw') }}) : </small></span>
                                    <strong class="text-danger">NIL</strong>
                                </div>
                            @else
                                <!-- <span @if($curr == 'INR')class="clsWebRupe" @endif>{{ $acbal['currency_symbol']}} </span> -->
                                <?php
                                    $bal_amount = $acbal['amount'] - $total_credit_amount;
                                    $amount = ($bal_amount > 0)?$bal_amount: 0 ;
                                 ?>
                                <div class="alert alert-success">
                                    <span>{{ Lang::get('common.account_balance')}} <small class="text-muted">({{ trans('myaccount/form.my-withdrawals.available_to_withdraw') }}) : </small></span>
                                    <strong class="text-success"><small>{{ $curr}}</small><span>{{ CUtil::formatAmount($amount) }} </span></strong>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            @endif
            <!-- END: ACCOUNT BALANCE -->
        @endif

        <!-- BEGIN: INVOICE DETAILS -->
        @if(CUtil::getAuthUser()->id != $user_details->id)
            <div class="tabbable-custom tabbable-customnew mt20">
                <ul class="nav nav-tabs">
                    <li class="@if($status == 'Paid' || $status =='')active @endif"><a href="{{ URL::to('admin/users/user-details').'/'.$user_id.'?status=Paid' }}">{{ trans('common.paid') }}</a></li>
                    <li class="@if($status == 'Unpaid') active @endif"><a href="{{ URL::to('admin/users/user-details').'/'.$user_id.'?status=Unpaid' }}">{{ trans('common.unpaid') }}</a></li>
                </ul>
            </div>

            <div class="portlet box blue-hoki">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i> {{ Lang::get('admin/manageMembers.viewmember_invoice_details') }}
                    </div>
                </div>

                <div class="portlet-body">
                    @if(count($invoice_details) <= 0)
                        <div class="note note-info mar0">
                           {{ Lang::get('admin/unpaidInvoiceList.no_result') }}
                        </div>
                    @else
                        <div class="table-scrollable">
                           <table class="table table-striped table-hover table-bordered">
                               <thead>
                                    <tr>
                                        <th>{{ Lang::get('myInvoice.common_invoice_id') }}</th>
                                        <th>{{ Lang::get('myInvoice.date_added') }}</th>
                                        <th>{{ Lang::get('myInvoice.user_notes') }}</th>
                                        <th>{{ Lang::get('myInvoice.paid_date') }}</th>
                                        <th>{{ Lang::get('myInvoice.amount') }}</th>
                                        <th>{{ Lang::get('myInvoice.status') }}</th>
                                        <th>{{ Lang::get('myInvoice.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($invoice_details) > 0)
                                        @foreach($invoice_details as $invoice)
                                            <tr>
                                               <td>{{ $invoice->common_invoice_id }}</td>

                                               @if($invoice->reference_type == 'Products')
                                                    <td class="text-muted">{{ CUtil::FMTDate($invoice->date_created, 'Y-m-d H:i:s', '')}}</td>
                                               @else
                                                    @if($invoice->date_added != '0000-00-00 00:00:00')
                                                        <td class="text-muted">{{ CUtil::FMTDate($invoice->date_added, 'Y-m-d H:i:s', '')}}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                               @endif

                                               @if($invoice->reference_type == 'Products')
                                                    <td>{{ Lang::get('myInvoice.created_for_product_purchase')}}</td>
                                               @else
                                                    <td>{{ $invoice->user_notes }}</td>
                                               @endif

                                               @if($invoice->reference_type == 'Products')
                                                    <td class="text-muted">{{ CUtil::FMTDate($invoice->date_created, 'Y-m-d H:i:s', '')}}</td>
                                               @else
                                                    @if($invoice->status == 'Paid' && $invoice->date_paid != '0000-00-00 00:00:00')
                                                        <td class="text-muted">{{ CUtil::FMTDate($invoice->date_paid, 'Y-m-d H:i:s', '')}}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                               @endif

                                               @if($invoice->reference_type == 'Products')
                                                    <td><strong>{{ $invoice->total_amount }}</strong></td>
                                               @else
                                                    <td><strong>{{ $invoice->amount }}</strong></td>
                                               @endif

                                               <td>
                                                    <?php
                                                        $lbl_class = "";
                                                        if(strtolower ($invoice->status) == "unpaid")
                                                            $lbl_class = "label-danger";
                                                        else
                                                            $lbl_class = "label-success";
                                                    ?>
                                                    <span class="label {{ $lbl_class }}">{{ $invoice->status }}</span>
                                                    <!--{{ $invoice->status }}-->
                                               </td>

                                               <td class="action-btn">
                                                   @if($invoice->reference_type == 'Products')
                                                        <a href="{{ URL::action('AdminPurchasesController@getOrderDetails', $invoice->id).'?s='.$status.'&m=member&i=invoice' }}" class="btn btn-xs btn-info" title="View Order Details"><i class="fa fa-eye"></i></a>
                                                   </td>
                                                   @else
                                                       <a href="{{ URL::action('AdminUnpaidInvoiceListController@getInvoiceDetails', $invoice->common_invoice_id).'?s='.$status.'&m=member'}}" class="btn btn-xs btn-info" title="View Invoice Details"><i class="fa fa-eye"></i></a>
                                                   @endif
                                               </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7"><p class="alert alert-info">{{ Lang::get('myInvoice.no_result') }}</p></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if(count($invoice_details) > 0)
                            <div class="clearfix">{{ $invoice_details->appends( array('status' => Input::get('status')))->links() }}</div>
                        @endif
                    @endif
                </div>
            </div>
        @endif
        <!-- END: INVOICE DETAILS -->
    @endif
@stop