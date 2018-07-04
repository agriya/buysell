<!-- CATEGORY STARTS -->
    <div class="white-bg">
        <div class="container catg-list">
        	@if(isset($category_list) && count($category_list) > 0)
	            <div class="row">
	                <div class="col-md-3 col-sm-4 col-xs-12">
	                	<?php
							$cat_bread = $product_service->getProductCategoryArr($cat_id, false, false, 'browse', true);
							if(isset($active_cat_details)) {
								$active_seo_cat_name = $active_cat_details->seo_category_name;
								$active_cat_name = $active_cat_details->category_name;
								if(isset($cat_bread[$active_seo_cat_name]))
									unset($cat_bread[$active_seo_cat_name]);
							}
							$final_arr = array();
							$needed_url = '';
							foreach($cat_bread as $cat_seo => $cat_link)
							{
								$final_arr[$cat_seo] = "<a href=".$cat_link['cat_link'].">".$cat_link['category_name']."</a>";
								$needed_url = $cat_link['cat_link'];
							}
							if($needed_url=='')
								$needed_url = Url::to('browse/');
							$cat_bread = $final_arr;
						?>
						@if(count($cat_bread > 0))
	                    	<h3>{{ implode(' &raquo; ', $cat_bread) }}</h3>
	                    @endif
	                    <h1>
							{{{ $active_cat_name or '' }}}
						</h1>
	                </div>
	                <div class="col-md-9 col-sm-8 col-xs-12">
	                    <ul class="list-inline category-nav innercateg-nav">
	                    	@foreach($category_list as $key => $cat)
	                    		<?php $cat_active = ($cat->id == $cat_id) ? 'active' : ''; ?>
		                        <li class="{{ $cat_active }}"><a href="{{ $needed_url.'/'.$cat->seo_category_name }}" title="{{ $cat->category_name }}">{{ $cat->category_name }}</a></li>
	                        @endforeach
	                    </ul>
	                </div>
	            </div>
            @endif
        </div>
    </div>
<!-- CATEGORY END -->