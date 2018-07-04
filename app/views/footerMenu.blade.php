<!-- BEGIN: FOOTER -->
<footer class="footer">
    <div class="container">
    	<div class="clearfix">
            @if(Config::get("generalConfig.is_multi_lang_support"))
                <?php
                    $default_language = CUtil::getLanguageToDisplay();
                    $language_list = CUtil::fetchAllowedLanguagesList();
                ?>

                <div class="national-symbol">
                    <div class="btn-group dropup pull-center">
                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
                            <span>{{$default_language["code"]}}</span> <a href="javascript:void(0);"><img src="{{ $default_language["flag_src"] }}" alt="{{ $default_language["code"] }}" /> <i class="fa fa-chevron-down"></i></a>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <form method="" action="" name="" id="" class="no-margin">
                                 @if(!empty($language_list))
                                     @foreach($language_list as $code => $flag_src)
                                        <li>
                                            <a href="javascript:void(0);" onClick="updateLanguage('{{$code}}');">
                                                <i class="fa fa-angle-right"></i> <span>{{ $code }}</span> <img src="{{ $flag_src }}" alt="{{ $code }}">
                                            </a>
                                        </li>
                                     @endforeach
                                 @endif
                            </form>
                        </ul>
                    </div>
                </div>
            @endif

            @if(Config::get("generalConfig.currency_is_multi_currency_support"))
                <?php
                    $default_currency = CUtil::getCurrencyToDisplay();
                    $currency_list = CUtil::fetchAllowedCurrenciesList();
                    //$image = CUtil::getFlagIcon($default_currency["currency_code"]);
                ?>

                <div class="national-symbol">
                    <div class="btn-group dropup pull-center">
                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
                            <span>{{$default_currency["currency_symbol"]}}</span>
                            <a href="javascript:void(0);">{{$default_currency["currency_code"]}} <i class="fa fa-chevron-down"></i></a>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <form method="" action="" name="" id="" class="no-margin">
                                <?php //$currency_list = CUtil::fetchAllowedCurrenciesList(); //echo "<pre>";print_r($currency_list);echo "</pre>";exit; ?>
                                 @if(!empty($currency_list))
                                     @foreach($currency_list as $currency)
                                        <li><a href="javascript:void(0);" onClick="updateCurrency('{{$currency}}');"><i class="fa fa-angle-right"></i> {{$currency}}</a></li>
                                     @endforeach
                                 @endif
                            </form>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <ul class="list-inline margin-bottom-10">
            <?php $footer_links = CUtil::getStaticPageFooterLinks(); ?>

            @if(count($footer_links) > 0)
                @foreach($footer_links as $key => $val)
                    <li>
                        @if($val['page_type'] == 'external')
                            <a target="_blank" href="{{ $val['external_link'] }}">{{ $val['page_name_ucfirst'] }}</a>
                        @else
                            <a href="{{ $val['page_link'] }}">{{ $val['page_name_ucfirst'] }}</a>
                        @endif
                    </li>
                @endforeach
            @endif
        </ul>

        <p class="site-info"><small>|</small> &copy; {{ Config::get('generalConfig.copyright') }} <a class="text-muted" href="{{ Request::root() }}">{{ Config::get('generalConfig.site_name') }}</a> {{ Config::get('version.version') }}. {{ Lang::get('common.all_rights_reserved')}}. {{ Lang::get('common.powered_by')}} <span><a class="text-bold" href="http://www.agriya.com" target="_blank">Agriya</a></span></p>

        <!-- BEGIN: BOTTOM BANNER GOOGLE ADDS -->
        {{ getAdvertisement('bottom-banner') }}
        <!-- END: BOTTOM BANNER GOOGLE ADDS -->
    </div>
</footer>
<!-- END: FOOTER -->