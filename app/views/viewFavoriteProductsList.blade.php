<!--  BEGIN: NEW FAVORITES PRODUCT LIST -->
<div class="toppicks-list fav-prolist">
	<ul class="list-unstyled row">
		@if(count($favorite_details))
            @foreach($favorite_details  AS $key => $list)
                <?php
                    $user_code = BasicCutil::setUserCode($user_id);
                    $list_fav_prod_ids = $favoriteservice->favoriteProductIdsByList($list['user_id'], $list['list_id'], 4);
                    //$list_items_cnt = count($list_fav_prod_ids);
					$list_fav_prod_ids_counts = $favoriteservice->favoriteProductIdsByList($list['user_id'], $list['list_id']); 
                    $list_items_cnt = count($list_fav_prod_ids_counts);//$favoriteservice->totalFavorites($user_id);
                    $list_count_msg = ($list_items_cnt > 0) ? $list_items_cnt : trans('common.empty');
                    $list_items_url = Url::to('favorite-list/'.$user_code.'?list_id='.$list['list_id']);
                    $list_name = $favoriteservice->getListName($list['list_id']);
                ?>
                <li class="col-md-4 col-sm-4 col-xs-4  margin-bottom-10">
                    <div class="tooltip-hover">
                        <div class="recfav-item">
                            <div class="toppick-item clearfix">
                                <div class="arrow-icon"><span><strong>{{ $list_count_msg }}</strong></span></div>
                                <ul class="list-unstyled clearfix">
                                    <?php $no_img = 4; ?>
                                    @if(count($list_fav_prod_ids) > 0)
                                        @foreach($list_fav_prod_ids AS $prd)
                                            <?php
                                                $fav_products = Products::initialize();
                                                $fav_products->setProductId($prd);
                                                $fav_products->setIncludeDeleted(true);
                                                $fav_products->setIncludeBlockedUserProducts(true);
                                                $product_details = $fav_products->getProductDetails();
                                                $valid_prod = false;
                                                if(count($product_details) > 0) {
                                                    $valid_prod = true;
                                                    $no_img -= 1;
                                                    $p_img_arr = $fav_products->getProductImage($prd);
                                                    $p_thumb_img = $productService->getProductDefaultThumbImage($prd, 'thumb', $p_img_arr);
                                                    $view_url = $productService->getProductViewURL($prd, $product_details);
                                                }
                                            ?>
                                            @if($valid_prod)
                                                <li>
                                                    {{ CUtil::showFeaturedProductIcon($prd, $product_details) }}
                                                    <a href="{{ $view_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{{ $product_details['product_name']  }}}" alt="{{{ $product_details['product_name']  }}}" /></a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                    @while($no_img > 0)
                                        <li><a href="javascript:void(0);"><img src="{{ URL::asset('images/no_image/prodnoimage-215x170.jpg') }}" alt="image"></a></li>
                                        @if($no_img = $no_img - 1)@endif
                                    @endwhile
                                </ul>
                            </div>
                            <figcaption>
                                <h3 class="margin-0"><a href="{{ $list_items_url }}">{{{ $list_name }}}</a></h3>
                                <a href="{{ $list_items_url }}" class="pull-right margin-top-5"><i class="fa fa-circle-thin"><sup class="fa fa-chevron-right"></sup></i></a>
                                <p>{{ $list_items_cnt }} {{ Lang::choice('common.item_choice', $list_items_cnt) }}</p>
                            </figcaption>
                        </div>
                    </div>
                </li>
            @endforeach
		@endif

		@if(CUtil::isMember())
			@if($logged_user_id == $user_id)
				<li class="col-md-4 col-sm-4 col-xs-4  margin-bottom-10">
					<div class="tooltip-hover">
						<div class="recfav-item">
							<div class="toppick-item creat-list-new">
								<div id="new_list_opener" class="creat-listopen"><i class="fa fa-plus fonts18 text-muted"></i><div>{{ trans('favorite.create_new_list') }}</div></div>
								<div id="new_list_create" style="display:none;" class="pad10">
									<div class="text-right">
										<a href="javascript:void(0);" id="new_list_close" title="{{ trans('common.close') }}"><i class="fa fa-times text-muted"></i></a>
									</div>
									<div class="margin-top-50">
										<input type="text" name="list_name" id="list_name" maxlength="{{ config::get('generalConfig.favorite_list_name_char_limit')}}"  class="form-control list_name"  />
										<button type="button" class="btn blue clsAddList btn-block margin-top-10">{{ trans('common.add') }}</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			@endif
		@endif
	</ul>
</div>
<!--  END: NEW FAVORITES PRODUCT LIST -->