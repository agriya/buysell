@extends('base')
@section('content')
	<?php $staticPageService = new StaticPageService();
		$content = $staticPageService->getSellPageStaticContent();
		if(!empty($content))
			$content = array_map(array($staticPageService, 'replaceSiteName'), $content);
	?>
	<div id="main">
		<!-- BEGIN: PAGE TITLE -->
		<h1>
			@if(isset($content['page_title']) && $content['page_title']!='')
				{{$content['page_title']}} @else {{Lang::get('static.learn_how_to_sell', array('site_name' => Config::get('generalConfig.site_name'))) }}
			@endif
		</h1>
		<!-- END: PAGE TITLE -->

		<div class="row sell-list">
        	<!-- BEGIN: STATIC PAGE -->
			<div class="col-md-9">
            	<div class="well">
                	<h2>{{Lang::get('static.what_can_you_sell')}}</h2>
					@if(isset($content['what_can_you_sell']) && $content['what_can_you_sell']!='')
						{{$content['what_can_you_sell']}}
					@endif
				</div>

                <div class="well">
	                <h2>{{Lang::get('static.how_does_it_work')}}</h2>
					@if(isset($content['how_doest_it_work']) && $content['how_doest_it_work']!='')
						{{$content['how_doest_it_work']}}
					@endif
				</div>

				@if(isset($content['additional_title']) && $content['additional_title']!='' || $content['additional_content'] && $content['additional_content']!='')
					<div class="well">
						@if(isset($content['additional_title']) && $content['additional_title']!='')
							<h2>{{$content['additional_title']}}</h2>
						@endif
						@if(isset($content['additional_content']) && $content['additional_content']!='')
							{{$content['additional_content']}}
						@endif
					</div>
				@endif
			</div>
			<!-- END: STATIC PAGE -->

			<!-- BEGIN: SELLON BUTTON -->
            <div class="col-md-3 text-center">
            	<div class="well">
                	<a class="btn btn-sm purple-plum" title="Sell on BuySell" href="{{Url::to('product/add')}}">
						<i class="fa fa-shopping-cart margin-right-5"></i> {{Lang::get('static.sell_on_site', array('site_name' => Config::get('generalConfig.site_name'))) }}
					</a>
				</div>

                <!-- BEGIN: SIDE BANNER GOOGLE ADDS -->
				{{ getAdvertisement('side-banner') }}
				<!-- END: SIDE BANNER GOOGLE ADDS -->

			</div>
			<!-- END: SELLON BUTTON -->
		</div>
   </div>
@stop