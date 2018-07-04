<!-- BEGIN: GENERAL IMPORT DETAILS -->
<div class="dl-horizontal-new dl-horizontal">
	<dl class="import-dl">
		<dt>{{ Lang::get('importer::importer.title') }}</dt>
		<dd><span>{{$record->title}}</span></dd>

		<dt>{{ Lang::get('importer::importer.url_slug') }}</dt>
		<dd><span>{{$record->url_slug}}</span></dd>

		<dt>{{Lang::get('importer::importer.description')}}</dt>
		<dd><span>{{$record->description}}</span></dd>

		<dt>{{Lang::get('importer::importer.summary')}}</dt>
		<dd><span>{{$record->summary}}</span></dd>

		<dt>{{Lang::get('importer::importer.price')}}</dt>
		<dd><span>{{$record->price}}</span></dd>

		<dt>{{Lang::get('importer::importer.category')}}</dt>
		<dd><span>{{$record->category}}</span></dd>

		@if($record->is_downloadable == 'Yes')
			<?php $lbl_class = "label-success";?>
		@else
			<?php $lbl_class = "label-danger"; ?>
		@endif
		<dt>{{Lang::get('importer::importer.is_downloadable')}}</dt>
		<dd><span class="label {{$lbl_class}} pull-left">{{$record->is_downloadable}}</span></dd>

		<dt>{{Lang::get('importer::importer.tags')}}</dt>
		<dd><span>{{$record->tags}}</span></dd>

		@if($record->demo_url!='')
			<dt>{{Lang::get('importer::importer.demo_url')}}</dt>
			<dd><span><a target="_blank" href="{{$record->demo_url}}">{{$record->demo_url}}</a></span></dd>
		@endif

		<dt>{{Lang::get('importer::importer.stock_available')}}</dt>
		<dd><span>{{$record->stock_available}}</span></dd>

		<dt>{{Lang::get('importer::importer.shipping_template')}}</dt>
		<dd><span>{{$record->shipping_template}}</span></dd>

		@if($record->thumb_image!='')
			<dt>{{Lang::get('importer::importer.thumb_image')}}</dt>
			<dd>
				@if($record->image_attached == 'Yes')
					<span>{{Lang::get('importer::importer.image_take_from_zip_file')}}</span>
				@else
					<span class="imgsize-75X75"><img alt="{{Lang::get('importer::importer.thumb_image')}}" src="{{$record->thumb_image}}"></span>
				@endif

			</dd>
		@endif

		@if($record->default_image!='')
			<dt>{{Lang::get('importer::importer.default_image')}}</dt>
			<dd>
				@if($record->image_attached == 'Yes')
					<span>{{Lang::get('importer::importer.image_take_from_zip_file')}}</span>
				@else
					<span class="imgsize-75X75"><img alt="{{Lang::get('importer::importer.default_image')}}" src="{{$record->default_image}}"></span>
				@endif
			</dd>
		@endif

		<dt>{{Lang::get('importer::importer.images')}}</dt>
		<dd>
			@if($record->image_attached == 'Yes')
				<span>{{Lang::get('importer::importer.image_take_from_zip_file')}}</span>
			@else
				@if(isset($record->images_arr))
					@foreach($record->images_arr as $image)
						<span class="imgsize-75X75"><img alt="{{Lang::get('importer::importer.images')}}" src="{{$image}}"></span>
					@endforeach
				@endif
			@endif
		</dd>
	</dl>
</div>
<!-- END: GENERAL IMPORT DETAILS -->