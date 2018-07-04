<!-- BEGIN: PAGE TITLE -->
<h2>
	{{ trans('myaccount/viewProfile.shop') }}
    @if(count($d_arr['shop_product_list']) > 0)
        <a href='{{ $d_arr['shop_url'] }}'><small class="text-primary">{{ trans('common.see_more') }}</small></a>
    @endif
</h2>
<!-- END: PAGE TITLE -->

@if(count($d_arr['shop_product_list']) > 0)
	<!-- BEGIN: SHOP PRODUCT LIST -->
    <ul class="list-inline shp-lst clearfix">
        @foreach($d_arr['shop_product_list'] AS $prd)
            <?php
                $ProductService = new ProductService;
                //$p_img_arr = $ProductService->populateProductDefaultThumbImages($prd->id);
                $p_img_arr = $prod_obj->getProductImage($prd->id);
                $p_thumb_img = $ProductService->getProductDefaultThumbImage($prd->id, 'small', $p_img_arr);
                $view_url = $ProductService->getProductViewURL($prd->id, $prd);
            ?>
            <li>
            	{{ CUtil::showFeaturedProductIcon($prd->id, $prd) }}
                <a href="{{ $view_url }}">
                    <img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{{ $prd->product_name }}}" alt="{{ $prd->product_name  }}" />
                </a>
            </li>
        @endforeach
    </ul>
    <!-- END: SHOP PRODUCT LIST -->
@else
	<!-- BEGIN: INFO BLOCK -->
    <div class="note note-info margin-0">{{ trans('myaccount/viewProfile.no_products_found') }}</div>
    <!-- END: INFO BLOCK -->
@endif
