<div class="container recfav-list">
    <h1 class="margin-bottom-20">{{ ucwords(trans('featuredsellers::featuredsellers.featured_sellers')) }}</h1>
    @if(count($featured_sellers) > 0)
        <?php $shop_details = array(); ?>
        <div id="js-product-view">
            <ul class="row list-unstyled prolist-max768">
                @foreach($featured_sellers as $key => $seller)
                    <li class="col-md-3 col-sm-3 feat-prod">
                        <div class="bs-prodet product-gridview">
                            <?php
                                $seller_id = $seller->user_id;
                                $seller_shop_url = URL::to('shop/'.$seller->url_slug);
                                $seller_image = CUtil::getUserPersonalImage($seller_id, "large"); 
								if (isset($seller_image['image_url'])) {
								 	$seller_image['image_url'] = str_replace('-50x50', '-140x140', $seller_image['image_url']);
									$seller_image['image_url'] = str_replace('_S', '_L', $seller_image['image_url']);
								}
                                $seller_user_code = BasicCUtil::setUserCode($seller_id);
                                $seller_profile_url = CUtil::userProfileUrl($seller_user_code);
                                $display_name = '';
                                if(isset($seller->first_name) && isset($seller->last_name)) {
                                    $display_name = ucfirst($seller->first_name).' '.ucfirst($seller->last_name);
                                }
                            ?>
                            <p class="prodet-img">
                                <a href="{{ $seller_shop_url }}">
                                    <img src="{{ $seller_image['image_url'] }}" {{ $seller_image['image_attr'] }} alt="{{ $seller->shop_name }}" />
                                </a>
                            </p>
                            <div class="prodet-info">
                                <h3><a href="{{$seller_shop_url}}" title="{{{ $display_name }}}">{{{ $display_name }}}</a></h3>
                                <a href='{{$seller_shop_url}}' class="bs-title">{{ $seller->shop_name }}</a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="alert alert-info">{{ Lang::get('featuredsellers::featuredsellers.featured_sellers_not_found') }}</p>
    @endif
</div>