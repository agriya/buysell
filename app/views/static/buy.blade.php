@extends('base')
@section('content')
	<div id="main">
    	<div>
        	<h1 class="mb20">{{Lang::get('static.shop_on_site',array('site_name' => Config::get('generalConfig.site_name')))}}
			<span> {{Lang::get('static.every_item_tells_story') }}</span></h1>
            <div class="well explore-buysell margin-bottom-50">
                <ul class="list-unstyled list-inline clearfix margin-0 row">
                    <li class="col-md-4" onclick="javascript:location.href='{{URL::action('CollectionsController@getIndex')}}'">
						<div class="explore-inner">
							<div class="icon-img">
								<p><a title="{{Lang::get('static.explore_collections') }}" href="{{URL::action('CollectionsController@getIndex')}}">{{Lang::get('static.explore_collections') }}</a></p>
								<i class="fa fa-camera"></i>
								<i class="fa fa-headphones"></i>
								<i class="fa fa-suitcase"></i>
							</div>
							<div class="mix-details">
								<div class="icon-img">
									<p><a title="{{Lang::get('static.explore_collections') }}" href="{{URL::action('CollectionsController@getIndex')}}">{{Lang::get('static.explore_collections') }}</a></p>
									<i class="fa fa-camera"></i>
									<i class="fa fa-headphones"></i>
									<i class="fa fa-suitcase"></i>
								</div>
							</div>
						</div>
                    </li>
                    <li class="col-md-4" onclick="javascript:location.href='{{Url::to('product')}}'">
						<div class="explore-inner">
							<div class="icon-img">
								<p><a title="{{Lang::get('static.explore_categories') }}" href="{{ Url::to('product') }}">{{Lang::get('static.explore_categories') }}</a></p>
								<i class="fa fa-gift"></i>
								<i class="fa fa-paint-brush"></i>
								<i class="fa fa-coffee"></i>
							</div>
							<div class="mix-details">
								<div class="icon-img">
									<p><a title="{{Lang::get('static.explore_categories') }}" href="{{ Url::to('product') }}">{{Lang::get('static.explore_categories') }}</a></p>
									<i class="fa fa-gift"></i>
									<i class="fa fa-paint-brush"></i>
									<i class="fa fa-trophy"></i>
								</div>
							</div>
						</div>
                    </li>
                    <li class="col-md-4" onclick="javascript:location.href='{{Url::to('shop')}}'">
						<div class="explore-inner">
							<div class="icon-img">
								<p><a title="{{Lang::get('static.explore_shops') }}" href="{{Url::to('shop')}}">{{Lang::get('static.explore_shops') }}</a></p>
								<i class="fa fa-envelope"></i>
								<i class="fa fa-gamepad"></i>
								<i class="fa fa-book"></i>
							</div>
							<div class="mix-details">
								<div class="icon-img">
									<p><a title="{{Lang::get('static.explore_shops') }}" href="{{Url::to('shop')}}">{{Lang::get('static.explore_shops') }}</a></p>
									<i class="fa fa-envelope"></i>
									<i class="fa fa-gamepad"></i>
									<i class="fa fa-book"></i>
								</div>
							</div>
						</div>
                    </li>
            	</ul>
            </div>

            <h1>{{Lang::get('static.more_ways_to_shop')}}</h1>
			{{ Form::open(array('url' => url::action('ProductController@showList'), 'method' => 'get', 'class' => 'form-horizontal',  'id' => 'productSearchfrm', 'name' => 'productSearchfrm')) }}
            	<div class="buyitem-search well" id="selAttributesList">
                	<h3 class="title-two margin-bottom-20">{{Lang::get('static.advanced_search') }}</h3>
                    <div class="row">
                    	<div class="col-md-6">
							<?php
								$productService = new ProductService();
								$root_category_id = Products::getRootCategoryId();
								$cat_list = $productService->populateProductCategoryList($root_category_id);
								if(count($cat_list) > 0)
							{?>
							<div class="form-group row">
								<div class="col-md-10">
									<select name="cat_search[]" multiple="multiple" tabindex=-1 size="10" class="form-control select2me chosen-select">
										<option value="">-- Select --</option>
										<?php
										foreach($cat_list as $key => $val)
										{?>
										<option value="<?php echo $val['id'];?>"><?php echo $val['category_name'];?></option>
										<?php
										}?>
									</select>
								</div>
							</div>
							<?php } ?>
							<div class="form-group row">
                            	<div class="col-md-10">
                            		{{ Form::text('tag_search', Input::get('tag_search'), array('class' => 'form-control', 'placeholder' => Lang::get('static.keyword').'...')); }}
								</div>
							</div>
						</div>

                        <div class="col-md-6">
                            <div class="form-group">
                            	<div class="col-md-10">
                            		{{ Form::text('shop_search', Input::get('shop_search'), array('class' => 'form-control col-md-12', 'placeholder' => Lang::get('product.shop_search_title'))); }}
								</div>
							</div>

                            <div class="attr-prcrng" id="selviewAttribute_pricerange">
                                <div class="form-group">
                                    <label class="control-label col-md-3">{{Lang::get('static.price_range')}}</label>
                                    <div class="col-md-8 row margin-0 padding-left-0">
										<div class="col-md-5 col-sm-5 padding-left-0">
											<span class="input-group">
												<label class="input-group-addon">{{ Config::get('generalConfig.site_default_currency') }}</label>
												{{ Form::text('price_range_start', Input::get('price_range_start'), array('class' => 'form-control', 'id' => 'price_range_start')); }}
											</span>
										</div>
										<div class="col-md-1 col-sm-1 margin-top-8 padding-left-0">{{Lang::get('common.to')}}</div>
                                        <div class="col-md-5 col-sm-5 padding-left-0">
											<span class="input-group">
												<label class="input-group-addon">{{ Config::get('generalConfig.site_default_currency') }}</label>
												{{ Form::text('price_range_end', Input::get('price_range_end'), array('class' => 'form-control', 'id' => 'price_range_end')); }}
											</span>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
                    <div class="clearfix margin-top-10">
                    	<input type="submit" value="{{Lang::get('common.search')}}" tabindex="1015" class="btn purple-plum" id="search_item" name="seach_items">
                        <input type="button" onclick="return clearForm(this.form);" value="{{Lang::get('common.reset')}}" tabindex="1020" class="btn default" id="search_item" name="seach_items">
					</div>
				</div>
			{{Form::close()}}
		</div>
   </div>
@stop

@section('script_content')
	<script type="text/javascript">
		//$(document).ready(function() {
			$(".select2me").select2({
				placeholder: "{{Lang::get('static.select_category')}}"
			});
		//});
		function clearForm(oForm)
        {
            var elements = oForm.elements;
            oForm.reset();
            $('#cat_search').val();
            $('#cat_search').attr("checked", 'false');
            $('#cat_search').click();
            //$('#cat_search').removeAttr('checked');
            /*
            deselectCheckBox(document.reportedListForm.name, document.reportedListForm.check_all.name);

            var checkboxelement = 'cat_search';
            var check_frm = eval('document.'+oForm+'.'+checkboxelement);
            //if(check_frm.checked){
                check_frm.checked=false;
            }*/


            for(i=0; i<elements.length; i++)
            {
                field_type = elements[i].type.toLowerCase();
                //alert(field_type);

                switch(field_type)
                {

                    case "text":
                    case "textarea":
                    //case "hidden":
                        elements[i].value = "";			  break;
                    case "checkbox":
                    case "checkbox":
                        if (elements[i].checked) {          elements[i].checked = false;      }
                          break;
                    case "select-one":
                    case "select-multi":
                    case "select-multiple":
                       //$('#cat_search').prop('selectedIndex',0);
                       $(".select2me").select2("val", "{{Lang::get('static.select_category')}}");
                      break;
                }
            }
            //document.selSearchForm.submit();
        }
    </script>
@stop