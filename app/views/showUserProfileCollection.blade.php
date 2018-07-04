<div class="well clearfix">
	<!-- BEGIN: PAGE TITLE -->
    <h2>
    	{{ trans('myaccount/viewProfile.treasury_lists') }}
        @if(count($d_arr['collections']) > 0)
            <a href="{{ $d_arr['collections_url'] }}"><small class="text-primary">{{ trans('common.see_more') }}</small></a>
        @endif
    </h2>
    <!-- END: PAGE TITLE -->

    @if(isset($d_arr['collections']) && $d_arr['collections']!='' && count($d_arr['collections']) > 0)
    	<!-- BEGIN: USER PROFILE COLLECTION -->
        @foreach($d_arr['collections'] as $collection)
            <div class="margin-bottom-20">
                <h3><a href="{{ Url::to('collections/view-collection/'.$collection->collection_slug) }}">{{$collection->collection_name}}</a></h3>
                @if(isset($collection->product_ids) && $collection->product_ids!='' && count($collection->product_ids) > 0)
	                <ul class="list-inline lst-items clearfix">
	                    @foreach($collection->product_ids as $product_id)
	                        <?php
	                            $ProductService = new ProductService;
	                            $product = Products::initialize($product_id);
	                            $product->setIncludeDeleted(true);
								$product->setIncludeBlockedUserProducts(true);

	                            $product_det = $product->getProductDetails();
	                            //echo '<pre>';print_r($product_det);echo '</pre>';die;
	                            $p_img_arr = $product->getProductImage($product_id);
	                            //echo '<pre>';print_r($p_img_arr);echo '</pre>';die;
	                            $p_thumb_img = $ProductService->getProductDefaultThumbImage($product_id, 'small', $p_img_arr);
	                            $prod_view_url = $ProductService->getProductViewURL($product_id, $product_det);
	                        ?>
	                        <li>
	                        	{{ CUtil::showFeaturedProductIcon($product_id, $product_det) }}
	                            <a href="{{ $prod_view_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{$product_det['product_name']}}" alt="{{$product_det['product_name']}}"  /></a>
	                        </li>
	                    @endforeach
                        
	                    <li><a href="{{ Url::to('collections/view-collection/'.$collection->collection_slug) }}" class="total-prod"><strong>{{ $collection->total_views }} <span class="text-muted">{{ Lang::choice('common.view_choice', $collection->total_views) }}</span></strong></a></li>
	                </ul>
	            @endif
            </div>
        @endforeach
        <!-- END: USER PROFILE COLLECTION -->
    @else
    	<!-- BEGIN: INFO BLOCK -->
        <div class="note note-info margin-0">{{ trans('myaccount/viewProfile.no_products_found') }}</div>
        <!-- END: INFO BLOCK -->
    @endif
</div>