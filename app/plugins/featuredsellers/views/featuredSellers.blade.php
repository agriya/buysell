<!-- BEGIN: RECENTLY FEATURED -->
@if(isset($d_arr['featured_sellers']) && count($d_arr['featured_sellers']) > 0)
    <div class="toppicks-list fav-prolist">
        <ul class="list-unstyled inner-favlist row">
            <li class="margin-bottom-10 col-md-12">
                <div class="recfav-item">
                    <div class="toppick-item clearfix">
                        <ul class="list-unstyled clearfix">
                            <?php $remain_featured_seller_cnt = 0; ?>
                            @if(isset($d_arr['featured_sellers']) && count($d_arr['featured_sellers']) > 0)
                                <?php $remain_featured_seller_cnt = 6 - count($d_arr['featured_sellers']); ?>
                                @foreach($d_arr['featured_sellers'] as $key => $val)
                                    <li><a href="{{ $d_arr['featured_sellers'][$key]['seller_shop_url'] }}">
                                        <img src="{{ $d_arr['featured_sellers'][$key]['seller_image']['image_url'] }}" {{ $d_arr['featured_sellers'][$key]['seller_image']['image_attr'] }}  alt="{{ $d_arr['featured_sellers'][$key]['seller_name'] }}" />
                                    </a></li>
                                @endforeach
                            @endif

                            @while($remain_featured_seller_cnt > 0)
                                <li>
                                    <a href="javascript:void(0);">
                                        <img src="{{ URL::asset('images/no_image/usernoimage-90x90.jpg') }}" alt="image" />
                                    </a>
                                </li>
                                @if($remain_featured_seller_cnt = $remain_featured_seller_cnt - 1)@endif
                            @endwhile
                        </ul>
                    </div>
                    <figcaption>
                        <h3 class="margin-0"><a href="{{ $d_arr['featured_sellers_url'] }}">{{ Lang::get('featuredsellers::featuredsellers.featured_sellers') }}</a></h3>
                        <a href="{{ $d_arr['featured_sellers_url'] }}" class="pull-right margin-top-5"><i class="fa fa-circle-thin"><sup class="fa fa-chevron-right"></sup></i></a>
                        <p>{{ $d_arr['featured_sellers_total'] }} {{ Lang::choice('featuredsellers::featuredsellers.featured_sellers_choice', $d_arr['featured_sellers_total']) }}</p>
                    </figcaption>
                </div>
            </li>
        </ul>
    </div>
@endif
<!-- END: RECENTLY FEATURED -->