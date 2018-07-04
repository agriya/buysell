@extends('base')
@section('content')
    <!-- BEGIN: PAGE TITLE -->
    @if(Input::get('user_id') == '')
    	<div class="clsMyAccountSubTitle "><h1>{{Lang::get('collection.collections')}} <small> - {{Lang::get('collection.collection_desc')}}</small></h3></div>
    @else
    	<div class="clsMyAccountSubTitle "><h1>{{Lang::get('collection.your_collections')}} <small> - {{Lang::get('collection.collection_desc')}}</small></h3></div>
    @endif
    <!-- END: PAGE TITLE -->

	<div class="row bs-colleclist">
		<div class="col-md-3 blog-sidebar">
        	<div class="well">
                <!-- BEGIN: SEARCH BLOCK -->
                {{-- Form::open(array('url'=>$current_url, 'id'=>'collectionFrm', 'method'=>'post','class' => 'form-horizontal')) --}}
                {{ Form::model($inputs, ['url'=>$current_url, 'id'=>'collectionFrm', 'method'=>'post','class' => '']) }}
                    <h4>{{Lang::get('common.search')}}</h4>
                    <div id="selAttributesList" class="well-border">
                        <div class="clearfix margin-bottom-15">
                            <div class="form-group clearfix">
                                {{Form::text('collection_name', Null, array('onblur' => "if(this.value=='') this.value='".Lang::get('collection.default_search_collection_name')."'", 'onclick' => "if(this.value=='".Lang::get('collection.default_search_collection_name')."') this.value=''", 'class' => 'col-md-12 form-control mb10', 'id' => 'collection_name'))}}
                            </div>

                            <div class="form-group">
                                {{Form::text('collection_by', Null, array('onblur' => "if(this.value=='') this.value='".Lang::get('collection.default_search_by_member')."'", 'onclick' => "if(this.value=='".Lang::get('collection.default_search_by_member')."') this.value=''", 'class' => 'col-md-12 form-control mb10', 'id' => 'collection_by'))}}
                            </div>
                        </div>

                        <div class="sidebar-btn">
                        	<button type="submit"  id="search_item" name="seach_items" class="btn purple-plum"><i class="fa fa-search"></i> {{Lang::get('common.search')}}</button>
                            <button type="button" onclick="return clearForm(this.form);" id="search_item" name="seach_items" class="btn default"><i class="fa fa-undo"></i> {{Lang::get('common.reset')}}</button>
                        </div>
                    </div>
                {{Form::close()}}
                <!-- END: SEARCH BLOCK -->

                <!-- BEGIN: CURATOR TOOLS -->
                <h4>{{Lang::get('collection.curator_tools')}}</h4>
                <ul class="list-unstyled no-margin">
                    <li><a title="Create a list" href="{{URL::action('MyCollectionsController@getAdd')}}">{{Lang::get('collection.add_collection')}}</a></li>
                    <li><a title="All collections list" href="{{URL::action('CollectionsController@getIndex')}}">{{Lang::get('collection.all_collections_list')}}</a></li>
                    @if(CUtil::isMember())
                        <?php $logged_user_id = BasicCUtil::getLoggedUserId(); ?>
                        <li><a title="Your collections list" href="{{URL::action('CollectionsController@getIndex', array('user_id'=>$logged_user_id))}}">{{Lang::get('collection.your_collections_list')}}</a></li>
                    @endif
                </ul>
                <!-- END: CURATOR TOOLS -->

                <!-- BEGIN: TODAYS TRENDING -->
                <!--<div class="well">
                    <h3>Todays Trending Tags</h3>
                    <ul class="list-unstyled list-inline myacc-tag mar0">
                        <li><a href="{{URL::action('CollectionsController@getIndex', array('collection_name'=>'collection'))}}">collection</a></li>
                    </ul>
                </div>-->
                <!-- END: TODAYS TRENDING -->
            </div>

            <!-- BEGIN: SIDE BANNER GOOGLE ADDS -->
            {{ getAdvertisement('side-banner') }}
            <!-- END: SIDE BANNER GOOGLE ADDS -->
		</div>

	    <div class="col-md-9">
            <!-- BEGIN: INFO BLOCK -->
            @if(Session::has('error_message') && Session::get('error_message') != '')
                <div class="note note-danger">{{ Session::get('error_message') }}</div>
                <?php Session::forget('error_message'); ?>
            @endif

            @if(Session::has('success_message') && Session::get('success_message') != '')
                <div class="note note-success">{{ Session::get('success_message') }}</div>
                <?php Session::forget('success_message'); ?>
            @endif
            <!-- END: INFO BLOCK -->

            <div class="well">
                <div class="tab-menu margin-bottom-20">
                    <a href="" class="btn purple-plum"><i class="fa fa-list margin-right-5"></i> Menu</a>
                    <ul class="nav nav-tabs">
                        <?php
                            $sort_by_arr = CUtil::populateCollectionsOrderbyArray();
                            $orderby_field = 'id';
                            if (Input::has('orderby_field'))
                                $orderby_field = Input::get('orderby_field');
                        ?>

                        @foreach($sort_by_arr as $sortKey => $sort)
                            <li @if($orderby_field == $sort['innervalue']) class="active" @endif><a href="{{ $sort['href'] }}">{{ $sort['innertext'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                @if(count($collections) <= 0)
                	<!-- BEGIN: INFO BLOCK -->
                    <div class="note note-info margin-0">
                       {{ Lang::get('collection.list_empty') }}
                    </div>
                    <!-- END: INFO BLOCK -->
                @else
                    @if(count($collections) > 0)
                        <!-- BEGIN: COLLECTIONS LIST -->
                        <div class="table-responsive" id="selViewAllCollections">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach($collections as $collection)
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

                                            $product_count = $collectionservice->getCollectionProductCounts($collection->id);
                                            $show_no_of_product = true;
                                            if($product_count > 2)
                                            {
                                            	$show_no_of_product = true;
												$collection_products = $collectionservice->getCollectionProductIds($collection->id, 1);
											}
											else
											{
												$show_no_of_product = false;
												$collection_products = $collectionservice->getCollectionProductIds($collection->id, 2);
											}

                                            $collection_view_url = URL::action('CollectionsController@getViewCollection',$collection->collection_slug);
                                        ?>
                                        <tr>
                                            <td>
                                                <a class="pull-left imguserborsm-56X56 margin-right-10" title="{{$user_details['display_name']}}" href="{{$user_details['profile_url']}}"><img title="{{$user_details['display_name']}}" alt="{{$user_details['display_name']}}" src="{{$user_image['image_url']}}" {{$user_image['image_attr']}}></a>
                                                <div class="pull-left coll-username">
                                                    <h3 class="title-one no-margin">
                                                        <a title="agtest" class="text-ellipsis" href="{{$collection_view_url}}">{{$collection->collection_name}}</a>
                                                    </h3>
                                                    <p class="no-margin">
                                                        <span class="text-muted">{{Lang::get('collection.by')}} </span>
                                                        <a href="{{$user_details['profile_url']}}" title="Online"><span class="text-success">{{$user_details['display_name']}}</span></a>
                                                        <div class="margin-top-5">{{ CUtil::showFeaturedSellersIcon($collection->user_id) }}</div>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled">
                                                    <li class="text-muted">{{ CUtil::FMTDate($collection->created_at, 'Y-m-d H:i:s', ''); }}</li>
                                                    <li>{{$collection->total_comments}} <small class="text-muted">{{Lang::get('collection.comments')}}</small></li>
                                                    <li>{{$collection->total_views}} <small class="text-muted">{{Lang::choice('collection.views', trim($collection->total_views))}}</small></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled list-inline myacc-coll pull-right margin-0 featured-icon">
                                                    @if($collection_products && !empty($collection_products))
                                                        @foreach($collection_products as $product_id)
                                                            <?php
                                                                $product = Products::initialize($product_id);
                                                                $product_det = $product->getProductDetails();

                                                                $p_img_arr = $product->getProductImage($product_id);
                                                                $p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'small', $p_img_arr);
                                                                $view_url = $productService->getProductViewURL($product_id, $product_det);
                                                            ?>
                                                            <li>
                                                            	{{ CUtil::showFeaturedProductIcon($product_id, $product_det) }}
                                                                <div href="{{$view_url}}">
                                                                    <a href="{{$view_url}}" class="imgsize-75X75"><img title="{{{ $p_thumb_img['title']  }}}" alt="{{{ $p_thumb_img['title']  }}}" src="{{$p_thumb_img['image_url']}}" {{$p_thumb_img['image_attr']}}></a>
                                                                </div>
                                                            </li>
                                                        @endforeach

                                                        @if($show_no_of_product && $product_count > 2)
                                                        	<li>
                                                                <div href="{{$collection_view_url}}">
                                                                    <a href="{{$collection_view_url}}" class="imgsize-75X75 total-prod">
																		<strong>{{$product_count}}</strong>
																		<span class="text-muted">{{ Lang::get('collection.products') }}</span>
																	</a>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- END: COLLECTIONS LIST -->

                        @if(count($collections) > 0)
                        	<div class="text-center">
                            	{{ $collections->appends(array('collection_name' => Input::get('collection_name'), 'srchproduct_submit' => Input::get('srchproduct_submit')))->links() }}
                            </div>
                        @endif

                        <div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
                            <span class="ui-icon ui-icon-alert"></span>
                            <span id="dialog-product-confirm-content" class="show"></span>
                        </div>
                    @else
                        <div class="note note-info">
                           {{ Lang::get('collection.list_empty') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		$('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('collection.show_search_filters') }} <i class="fa fa-caret-down ml5"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('collection.hide_search_filters') }} <i class="fa fa-caret-up ml5"></i>');
	        }
	        return false;
	    });

	    /*function doAction(collection_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('collection.confirm_delete') }}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('collection.collections_list') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#collection_action').val(selected_action);
						$('#collection_id').val(collection_id);
						document.getElementById("collectionsActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}*/
		function clearForm(oForm)
		{
			var elements = oForm.elements;
			oForm.reset();
			for(i=0; i<elements.length; i++)
			{
				field_type = elements[i].type.toLowerCase();
				switch(field_type)
				{
					case "text":
					case "textarea":
					case "hidden":
						elements[i].value = "";
						break;
					case "checkbox":
						if (elements[i].checked) {          elements[i].checked = false;      }
						  break;
					case "select-one":
					case "select-multi":
						elements[i].selectedIndex = -1;
					  break;
				}
			}
			oForm.submit();
			//$('#collectionFrm').submit();
			//document.collectionFrm.submit();
		}

		$(".fn_changeStatus").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	</script>
@stop