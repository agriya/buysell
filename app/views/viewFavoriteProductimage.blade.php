<!-- USER DETAILS STARTS -->
<div class="white-bg">
	<div class="container favimg-blk pos-relative">
		<div class="row">
			<div class="col-md-12">
				<a href="{{ $user_details['profile_url'] }}" class="pull-left imgusersm-54X54 margin-right-10">
				<img src="{{ $user_image['image_url'] }}" alt="{{ $user_details['display_name'] }}" /></a>
				<span class="pull-left margin-top-20">{{ trans('favorite.curated_by') }} <a href="{{ $user_details['profile_url'] }}">{{ $user_details['display_name'] }}</a></span>
			</div>
		</div>
	</div>
</div>
<!-- USER DETAILS ENDS -->