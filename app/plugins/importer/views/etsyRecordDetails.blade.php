<!-- BEGIN: ETSY RECORD IMPORT DETAILS -->
<div class="dl-horizontal-new dl-horizontal">
	<dl class="import-dl">
		<dt>{{ Lang::get('importer::importer.title') }}</dt>
		<dd><span>{{$record->title}}</span></dd>

		<dt>{{Lang::get('importer::importer.description')}}</dt>
		<dd><span>{{$record->description}}</span></dd>

		<dt>{{Lang::get('importer::importer.summary')}}</dt>
		<dd><span>{{$record->summary}}</span></dd>

		<dt>{{Lang::get('importer::importer.price')}}</dt>
		<dd><span>{{$record->price}}</span></dd>

		<dt>{{Lang::get('importer::importer.quantity')}}</dt>
		<dd><span>{{$record->stock_available}}</span></dd>

		<dt>{{Lang::get('importer::importer.tags')}}</dt>
		<dd><span>{{$record->tags}}</span></dd>
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
<!-- END: ETSY RECORD IMPORT DETAILS -->