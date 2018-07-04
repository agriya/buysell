<div class="col-md-3 blog-sidebar">
	<div class="well">
    <!-- BEGIN: CATEGORIES BLOCK -->
    	<div class="well-border">
            @if($subcat)
                <h4 class="no-top-space">{{ Lang::get('product.sub_categories_title') }}</h2>
            @else
                <h4 class="no-top-space">{{ Lang::get('product.categories_title') }}</h2>
            @endif

            @if(count($cat_list) > 0)
                <ul class="list-unstyled no-margin">
                    @foreach($cat_list AS $cat)
                        <li><a href="{{$cat['cat_link']}}" title="{{{ $cat['category_name'] }}} ({{ $cat['product_count'] }})">{{{ $cat['category_name'] }}} <strong class="text-muted">{{ $cat['product_count'] }}</strong></a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="alert alert-info no-margin">{{ Lang::get('product.category_not_found') }}</p>
            @endif
        </div>
    <!-- END: CATEGORIES -->

	@if(!isset($is_index_page))
    {{ Form::open(array('url' => Request::url(), 'method' => 'get', 'class' => 'form-horizontal',  'id' => 'productSearchfrm', 'name' => 'productSearchfrm')) }}
        <!-- BEGIN: REFINE SEARCH -->
            @if(count($cat_list) > 0)
                <div class="well-border">
                    <h4>{{ Lang::get('product.item_type') }}</h4>
                    <ul class="list-unstyled no-margin refine-catgry">
                        @foreach($cat_list as $key => $val)
                            <li>
                                <div class="checkbox">
                                    <?php
                                        $cat_search = array();
                                        if(Input::get('cat_search') != "")
                                        {
                                            $cat_search = Input::get('cat_search');
                                        }
                                    ?>
                                    {{ Form::checkbox('cat_search[]', $val['id'], (in_array($val['id'], $cat_search))? true : false, array("id" => $val['id'])) }}
                                    {{ Form::label($val['id'], $val['category_name']) }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- BEGIN: ATTRIBUTE FILTER -->
            @if(!empty($category_attributes))
            	<div class="well-border">
                    <h4>{{ trans('viewProduct.attributes_of_the_product') }}</h4>
                    @foreach($category_attributes as $attributes)
						<p class="margin-top-10">{{ $attributes['attribute_label'] }}</p>
						<?php
							$attr_id = $attributes['attribute_id'];
							$elem_name = 'textattribute_'.$attr_id;
							$elem_value = $attributes['default_value'];
							$input_type = $attributes['attribute_question_type'];
						 ?>
						@if($input_type == 'text' || $input_type == 'textarea')
							{{ Form::text($elem_name, Input::get($elem_name), array('class' => 'form-control valid')) }}
						@else
							@if(@count($attributes['attribute_options']) > 0)
								<ul class="list-unstyled no-margin refine-catgry">
									@foreach($attributes['attribute_options'] as $key => $val)
										 <li>
												 <div class="checkbox">
												<?php $checked='';?>
												@if(!empty($selected_attr))
													<?php
													if(in_array($attr_id.'_'.$key,$selected_attr))
													{
														 $checked = ' checked="checked" ';
													}?>
												@endif
												<?php
													$id_name = 'attribmopt_'.strtolower(str_replace(' ', '_', $val));
												?>
												<input type="checkbox" id="{{ $id_name }}" name="attributes[]" value="{{ $attr_id.'_'.$key }}" {{ $checked }}>
												{{ Form::label($id_name, $val, array('class' => '')) }}
											</div>
										</li>
									@endforeach
								</ul>
							@endif
						@endif
                    @endforeach
                </div>
            @endif
        	<!-- END: ATTRIBUTE FILTER -->

            <div class="well-border clearfix">
                <h4>{{ Lang::get('product.keyword_search_title') }}</h4>
                <div class="margin-bottom-10">
                    {{ Form::text('tag_search', Input::get('tag_search'), array('class' => 'form-control')); }}
                </div>
                {{ Form::text('shop_search', Input::get('shop_search'), array('class' => 'form-control col-md-12', 'placeholder' => Lang::get('product.shop_search_title'))); }}
            </div>

            <div class="well-border">
	        <!-- BEGIN: PRICE RANGE -->
				<?php $default_currency = CUtil::getCurrencyToDisplay(); ?>
                <h4>{{ Lang::get('product.price_range_title') }}</h4>
                <div class="form-group">
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        {{ Form::label('price_range_start', $default_currency['currency_code'], array('class' => 'control-label')) }}
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4 padding-right-0">{{ Form::text('price_range_start', Input::get('price_range_start'), array('class' => 'form-control', 'id' => 'price_range_start', 'placeholder' => Lang::get('common.min') )); }}</div>
                    <div class="col-md-2 col-sm-1 col-xs-2 text-right">
                        {{ Form::label('price_range_end', Lang::get('common.to'), array('class' => 'control-label')) }}
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4 padding-left-0">{{ Form::text('price_range_end', Input::get('price_range_end'), array('class' => 'form-control', 'id' => 'price_range_end', 'placeholder' => Lang::get('common.max') )); }}</div>
                </div>
			<!-- END: PRICE RANGE -->

            	<div class="sidebar-btn">
                    <button type="submit" name="search_products" value="search_products" class="btn purple-plum"><i class="fa fa-search"></i> {{ Lang::get('common.search') }}</button>
                    <button type="reset" name="reset_products" value="reset_products" class="btn default" onclick="javascript:window.location = '{{ BasicCUtil::getCurrentUrl() }}';"><i class="fa fa-undo"></i> {{ Lang::get('common.reset') }}</button>
                </div>
            </div>
     <!-- END: REFINE SEARCH -->
    {{ Form::close() }}
	@endif

	<!-- BEGIN: WAYS TO SHOP -->
    	<h4>{{Lang::get('product.ways_to_shop')}}</h4>
		<ul class="ways-shop list-unstyled no-margin clearfix">
			<li>
                <i class="fa fa-th"></i>
				<div>
					<a title="Collections" href="{{URL::action('CollectionsController@getIndex')}}">{{ Lang::get('collection.collections') }}</a>
					<p class="text-muted"><small>{{Lang::get('product.curated_lists')}}</small></p>
				</div>
			</li>
			<li>
                <i class="fa fa-shopping-cart"></i>
                <div>
                    <a title="Shops" href="{{Url::to('shop')}}">{{Lang::get('product.shops')}}</a>
                    <p class="text-muted"><small>{{Lang::get('product.explore_shops')}}</small></p>
                </div>
			</li>
			<li>
                <i class="fa fa-cubes"></i>
				<div>
					<a title="Items" href="{{URL::action('ProductController@showList')}}">{{Lang::get('product.products')}}</a>
					<p class="text-muted"><small>{{Lang::get('product.just_listed')}}</small></p>
				</div>
			</li>
		</ul>
    <!-- END: WAYS TO SHOP -->
    </div>
</div>