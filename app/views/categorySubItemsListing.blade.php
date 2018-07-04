@extends('base')
@section('content')
    <div class="category-new">
    	@if(count($products) > 0)
    		<ul class="list-unstyled row">
	        	@foreach($products as $key => $prod)
		        	<?php
						$p_img_arr = $prod_obj->getProductImage($prod['id']);
						$p_thumb_img = $product_service->getProductDefaultThumbImage($prod['id'], 'large', $p_img_arr);
						$cat_bread_arr = $product_service->getProductCategoryArr($prod['product_category_id'], false, true, 'browse');
						$cat_link = isset($cat_bread_arr['cat_link']) ? $cat_bread_arr['cat_link'] : '#';
						$cat_name = Products::getCategoryName($prod['product_category_id']);
					?>
		            <li>
		                <div class="category-inner">
		                    <figure>
		                        <a href="{{ $cat_link }}">
									<strong class="arrow-icon"><span></span></strong>
		                        	<img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} alt="{{ $cat_name }}" />
								</a>
		                    </figure>
		                    <h3><a href="{{ $cat_link }}">{{ $cat_name }}</a></h3>
		                </div>
		            </li>
	            @endforeach
            </ul>
        @else
			<div class="alert alert-info no-margin">{{Lang::get('product.no_subcategory_found')}}</div>
        @endif
    </div>
@stop