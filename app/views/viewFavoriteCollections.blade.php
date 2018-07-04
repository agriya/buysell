@if(count($favorite_details) > 0)
    <!-- COLLECTIONS LIST STARTS -->
    	<div class="well">
            <div class="table-responsive bs-colleclist" id="selViewAllCollections">
                <table class="table table-hover">
                    <tbody>
                        @foreach($favorite_details as $collection)
                            <?php
                                $user_image = array();
                                $user_details = array();
                                if(!isset($user_image_details[$collection->user_id])) {
                                    $user_image_details[$collection->user_id] = CUtil::getUserPersonalImage($collection->user_id, 'small');
                                }
                                if(!isset($user_details[$collection->user_id])){
                                    $user_details[$collection->user_id] = CUtil::getUserDetails($collection->user_id);
                                }
                                $user_image = isset($user_image_details[$collection->user_id])?$user_image_details[$collection->user_id]:array('image_url'=>URL::asset("images/no_image").'/usernoimage-50x50.jpg');
                                $user_details = isset($user_details[$collection->user_id])?$user_details[$collection->user_id]:array('profile_url' =>'#', 'display_name' => 'anonymous');

                                $collection_products = $collectionservice->getCollectionProductIds($collection->id, 2);
                                $collection_view_url = URL::action('CollectionsController@getViewCollection',$collection->collection_slug);
                            ?>
                            <tr>
                                <td>
                                    <a class="pull-left imguserborsm-56X56 margin-right-10" title="asdsad" href="{{$user_details['profile_url']}}">
                                        <img title="asdsad" alt="asdsad" src="{{$user_image['image_url']}}">
                                    </a>
                                    <div class="pull-left coll-username">
                                        <h3 class="title-one no-margin"><a title="agtest" class="text-ellipsis" href="{{$collection_view_url}}">
										<strong>{{$collection->collection_name}}</strong></a></h3>
                                        <p class="no-margin">
                                            <span class="text-muted">{{Lang::get('collection.by')}} </span>
                                            <a href="{{$user_details['profile_url']}}" title="Online"><span class="text-success">{{$user_details['display_name']}}</span></a>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li class="text-muted">{{ CUtil::FMTDate($collection->created_at, 'Y-m-d H:i:s', '') }}</li>
                                        <li>{{$collection->total_comments}} <small class="text-muted">{{ Lang::get('collection.comments') }}</small></li>
                                        <li>{{$collection->total_views}} <small class="text-muted">{{Lang::choice('collection.views',$collection->total_views)}}</small></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled list-inline myacc-coll margin-0 pull-right">
                                        @if($collection_products && !empty($collection_products))
                                            @foreach($collection_products as $product_id)
                                                <?php
                                                    $product = Products::initialize($product_id);
                                                    $product_det = $product->getProductDetails();

                                                    $p_img_arr = $product->getProductImage($product_id);
                                                    $p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
                                                    $view_url = $productService->getProductViewURL($product_id, $product_det);
                                                ?>
                                                <li>
                                                    <div href="{{$view_url}}">
                                                        <a href="{{$view_url}}" class="imgsize-75X75">
                                                        <img width="75" title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" src="{{$p_thumb_img['image_url']}}"></a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    	</div>
    <!-- COLLECTIONS LIST END -->

    @if(count($favorite_details) > 0)
        {{ $favorite_details->appends(array('favorites' => Input::get('favorites')))->links() }}
    @endif

	<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
		<span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-product-confirm-content" class="show"></span>
	</div>
@else
	<div class="alert alert-info">
	   {{ Lang::get('collection.list_empty') }}
	</div>
@endif