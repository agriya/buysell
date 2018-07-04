<!-- BEGIN: RECENTLY FEATURED -->
@if(isset($d_arr['featured_products']) && count($d_arr['featured_products']) > 0)
    <div class="toppicks-list fav-prolist">
        <ul class="list-unstyled inner-favlist row">
            <li class="margin-bottom-10 col-md-12">
                <div class="recfav-item">
                    <div class="toppick-item clearfix">
                        <ul class="list-unstyled clearfix">
                            <?php $remain_featured_prod_cnt = 0; ?>
                            @if(isset($d_arr['featured_products']) && count($d_arr['featured_products']) > 0)
                                <?php $remain_featured_prod_cnt = 6 - count($d_arr['featured_products']); ?>
                                @foreach($d_arr['featured_products'] as $key => $val)
                                    <li><a href="{{ $d_arr['featured_products'][$key]['p_view_url'] }}">
                                        <img src="{{ $d_arr['featured_products'][$key]['p_thumb_img']['image_url'] }}" {{ $d_arr['featured_products'][$key]['p_thumb_img']['image_attr'] }}  alt="{{ $d_arr['featured_products'][$key]['p_product_details']['product_name'] }}" />
                                    </a></li>
                                @endforeach
                            @endif

                            @while($remain_featured_prod_cnt > 0)
                                <li>
                                    <a href="javascript:void(0);">
                                        <img src="{{ URL::asset('images/no_image/prodnoimage-215x170.jpg') }}" alt="image" />
                                    </a>
                                </li>
                                @if($remain_featured_prod_cnt = $remain_featured_prod_cnt - 1)@endif
                            @endwhile
                        </ul>
                    </div>
                    <figcaption>
                        <h3 class="margin-0"><a href="{{ $d_arr['featured_products_url'] }}">{{ Lang::get('featuredproducts::featuredproducts.recently_featured_items') }}</a></h3>
                        <a href="{{ $d_arr['featured_products_url'] }}" class="pull-right margin-top-5"><i class="fa fa-circle-thin"><sup class="fa fa-chevron-right"></sup></i></a>
                        <p>{{ $d_arr['featured_products_total'] }} {{ Lang::choice('common.item_choice', $d_arr['featured_products_total']) }}</p>
                    </figcaption>
                </div>
            </li>
        </ul>
    </div>
@endif
<!-- END: RECENTLY FEATURED -->