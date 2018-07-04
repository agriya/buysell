<!-- BEGIN: TOP MENU -->
<div class="top-menu">
    <ul class="nav navbar-nav pull-right admin-topmenu">
    	<?php $user_id = BasicCUtil::getLoggedUserId(); $user_details = CUtil::getUserDetails($user_id); ?>
        <li><span>{{trans('admin/accountmenu.welcome')}} <strong>{{ $user_details['user_name'] }}</strong></span></li>
        <li><a href="{{ URL::to('/') }}">{{trans('admin/accountmenu.member_area')}}</a></li>
        <li>
            <a href="{{ URL::to('users/logout') }}">{{trans('admin/accountmenu.logout')}}</a>
        </li>
        @if(Config::get("generalConfig.is_multi_lang_support"))
	        <li>
	            <?php
	                $default_language = CUtil::getLanguageToDisplay();
	                $language_list = CUtil::fetchAllowedLanguagesList();
	            ?>
	            <div class="national-symbol">
	                <div class="btn-group dropdown pull-center">
	                    <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button">
	                        <span>{{$default_language["code"]}}</span> <a href="javascript:void(0);">
                            <img src="{{ $default_language["flag_src"] }}" alt="{{ $default_language["code"] }}" /> <i class="fa fa-chevron-down font11"></i></a>
	                    </button>
	                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
	                        <form method="" action="" name="" id="" class="no-margin">
	                             @if(!empty($language_list))
	                                 @foreach($language_list as $code => $flag_src)
	                                    <li>
	                                        <a href="javascript:void(0);" onClick="updateLanguage('{{$code}}');">
	                                            <i class="fa fa-angle-right"></i> <span>{{ $code }}</span> <img src="{{ $flag_src }}" alt="{{ $code }}" />
	                                        </a>
	                                    </li>
	                                 @endforeach
	                             @endif
	                        </form>
	                    </ul>
	                </div>
	            </div>
	        </li>
        @endif
    </ul>
</div>
<!-- END: TOP MENU -->