<!-- BEGIN: SHOP LIST -->
<div class="white-bg">
    <div class="container shop-info">
        <div class="row">
        	@if(isset($d_arr['shop_details']) && count($d_arr['shop_details']) > 0)
				<div class="col-md-6 col-sm-6 col-xs-6">
					<div class="media margin-bottom-10">
						<?php
							$user_image_details = CUtil::getUserPersonalImage($d_arr['shop_details']['user_id'], 'thumb');
						?>
						<a href="{{ $d_arr['shop_details']['shop_url'] }}" class="pull-left bs-imgrounded">
							<img class="img-circle" src="{{$user_image_details['image_url']}}" alt="image"/>
						</a>
						<div class="media-body">
							<h2><a href="{{ $d_arr['shop_details']['shop_url']}}" title="{{{ $d_arr['shop_details']['shop_name'] }}}">{{{ $d_arr['shop_details']['shop_name'] }}}</a></h2>
						</div>
					</div>
				</div>
			@endif
            
            <!-- BEGIN: APPLICABLE PRODUCT DEALS -->   
            @if(isset($deal_details->deal_status) && $deal_details->deal_status == "active")
	            <div class="col-md-6 col-sm-6 col-xs-6">
                    @if((isset($d_arr['product_details']) && COUNT($d_arr['product_details']) > 0 ) || ($deal_details->applicable_for == 'all_items' && isset($shopDetails) && COUNT($shopDetails) > 0))               
                        <div class="pull-right">     
                            <div class="title-three">{{ Lang::get('deals::deals.this_deal_applicable_lbl') }}</div>      
                            <ul class="viewitem-list list-unstyled pull-right featured-icon margin-0">
                                @if($deal_details->applicable_for == 'single_item')
                                    @foreach($d_arr['product_details'] as $productKey => $product)
                                        <?php
                                            $products = Products::initialize();
                                            $p_img_arr = $products->getProductImage($product['id']);
                                            $p_thumb_img = $list_prod_serviceobj->getProductDefaultThumbImage($product['id'], 'small', $p_img_arr);
                                            $prd_view_url = $list_prod_serviceobj->getProductViewURL($product['id'], $product);
                                        ?>  
                                        <li><a href="{{ $prd_view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a></li>
                                    @endforeach
                                    
                                @elseif($deal_details->applicable_for == 'selected_items')
                                
                                    @foreach($d_arr['product_details'] as $productKey => $product)
                                        <?php
                                            $products = Products::initialize();
                                            $p_img_arr = $products->getProductImage($product['id']);
                                            $p_thumb_img = $list_prod_serviceobj->getProductDefaultThumbImage($product['id'], 'small', $p_img_arr);
                                            $prd_view_url = $list_prod_serviceobj->getProductViewURL($product['id'], $product);
                                        ?>  
                                        <li><a href="{{ $prd_view_url }}"><img id="item_thumb_image_id" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}} title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" /></a></li>
                                    @endforeach
                                    
                                    @if($d_arr['product_total_count'] > 3)
                                        <li>
                                            <a href="{{ Url::to('deals/deal-items', array('deal_id' => $deal_details->deal_id)) }}" title="{{ Lang::get('deals::deals.this_deal_applicable_lbl')." ".$d_arr['product_total_count'].' '. Lang::get('deals::deals.deal_item_count_lbl') }}" class="img-lastchild">
                                                <span><strong>{{ $d_arr['product_total_count'] }}</strong> {{ Lang::get('deals::deals.deal_item_count_lbl') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @elseif($deal_details->applicable_for == 'all_items')
                                @if(isset($shopDetails) && COUNT($shopDetails) > 0)
                                    <div class="show note note-info margin-0">
                                        <span title="{{ Lang::get('deals::deals.this_deal_applicable_lbl') ." ". Lang::get('deals::deals.deal_applicable_all_items_of_shop_lbl')  }}">
                                            {{ Lang::get('deals::deals.deal_applicable_all_items_of_shop_lbl') }}: 
                                            <a href="{{ $shop_url}}" title="{{{ $shopDetails->shop_name  }}}">{{{ $shopDetails->shop_name  }}}</a>
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            @endif
            <!-- END: APPLICABLE PRODUCT DEALS -->  
        </div>
    </div>
</div>
<!-- END: SHOP LIST -->

<script language="javascript" type="text/javascript">
	var user_id = {{$logged_user_id}};
	var favorite_product_url = "{{URL::action('FavoritesController@postToggleFavorite')}}";
</script>