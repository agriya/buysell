<!-- BEGIN: SHOP LIST -->
<div class="white-bg">
    <div class="container shop-info">
        <div class="row">
        	@if(isset($d_arr['shop_details']) && count($d_arr['shop_details']) > 0)
	            <div class="col-md-7 col-sm-7 col-xs-7">
	                <div class="media">
	                    <?php
	                        ///echo "<pre>";print_r($d_arr);echo "</pre>";exit;
	                        $user_image_details = CUtil::getUserPersonalImage($d_arr['shop_details']['user_id'], 'thumb');
	                    ?>
	                    <a href="{{ $d_arr['shop_details']['shop_url'] }}" class="pull-left bs-imgrounded">
	                        <img class="img-circle" src="{{$user_image_details['image_url']}}" alt="image" height="75" width="75" />
	                    </a>
	                    <div class="media-body">
	                        <h2><a href="{{ $d_arr['shop_details']['shop_url']}}" title="{{{ $d_arr['shop_details']['shop_name'] }}}">{{{ $d_arr['shop_details']['shop_name'] }}}</a>
	                    	{{ CUtil::showFeaturedSellersIcon($d_arr['shop_details']['user_id'], $d_arr['shop_details']) }}</h2>
	                        <div class="fav-btn">
	                            @if(CUtil::isMember())
	                                <?php $isFavoriteShop = $shopFavoritesService->isFavoriteShop($d_arr['shop_details']['id'], $logged_user_id);  ?>
	                                @if($isFavoriteShop)
	                                    <a href="javascript:void(0);" data-shopid="{{$d_arr['shop_details']['id']}}" data-userid="{{$d_arr['shop_details']['user_id']}}" class="btn btn-default js-addto-favorite-shop"><i class="fa fa-heart text-pink"></i> {{trans('viewProduct.unfavorite_shop')}}</a>
	                                @else
	                                    <a href="javascript:void(0);" data-shopid="{{$d_arr['shop_details']['id']}}" data-userid="{{$d_arr['shop_details']['user_id']}}" class="btn btn-default js-addto-favorite-shop"><i class="fa fa-heart text-muted"></i> {{trans('viewProduct.favorite_shop')}}</a>
	                                @endif
	                            @else
	                                <?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
	                                <a href="{{ $login_url }}" class="fn_signuppop btn btn-default"><i class="fa fa-heart text-muted"></i> {{trans('viewProduct.favorite_shop')}}</a>
	                            @endif
	                        </div>
	                    </div>
	                </div>
	            </div>
			@endif

            <div class="col-md-5 col-sm-5 col-xs-5">
                @if(isset($d_arr['shop_item_details']) && count($d_arr['shop_item_details']) > 0)
                    <?php
                    	$prod_obj->setFilterProductStatus('Ok');
						$prod_obj->setFilterProductExpiry(true);
                        $total_shop_items = $prod_obj->getTotalProducts($p_details['product_user_id']);
                    ?>
                    <ul class="viewitem-list list-unstyled pull-right featured-icon">
                        @foreach($d_arr['shop_item_details'] AS $prd)
							<?php
                                $p_img_arr = $prod_obj->getProductImage($prd->id);
                                $p_thumb_img = $service_obj->getProductDefaultThumbImage($prd->id, 'small', $p_img_arr);
                                $view_url = $service_obj->getProductViewURLNew($prd->id, $prd);
                            ?>
                            <li>
                            	{{ CUtil::showFeaturedProductIcon($prd->id, $prd) }}
                                <a href="{{ $view_url }}"><img src="{{ $p_thumb_img['image_url'] }}" {{ $p_thumb_img['image_attr'] }} title="{{{ nl2br($prd->product_name)  }}}" alt="{{{ nl2br($prd->product_name)  }}}" /></a>
                            </li>
                        @endforeach

                        @if($total_shop_items > 0)
                            <li><a href="{{ $d_arr['shop_details']['shop_url'] }}" class="img-lastchild"><span><strong>{{ $total_shop_items}}</strong>{{trans('product.items')}}</span></a></li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- END: SHOP LIST -->

<script language="javascript" type="text/javascript">
	@if ($logged_user_id)
		var user_id = {{$logged_user_id}};
	@else
		var user_id = 0;
	@endif
	var favorite_product_url = "{{URL::action('FavoritesController@postToggleFavorite')}}";
</script>